<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use App\Models\Firm;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    private function authorise(PurchaseOrder $po): void
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        if (!$isAdmin) {
            $firmId = $user ? $user->firm_id : session('firm_id');
            if ($po->firm_id != $firmId && !$po->firms->contains($firmId)) {
                abort(403);
            }
        }
    }

    private function dropdowns($selectedFirmId = null): array
    {
        $user   = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $vendorQuery = Vendor::where('status', 'active')->orderBy('name');
        $materialQuery = Material::where('status', 'active')->orderBy('material_name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $vendorQuery->where('firm_id', $firmId);
            $materialQuery->where('firm_id', $firmId);
        }

        return [
            'firms'     => $firms,
            'vendors'   => $vendorQuery->get(),
            'materials' => $materialQuery->get(),
        ];
    }

    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['firm', 'vendor', 'creator']);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();

        if (!$isAdmin) {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $query->forFirms([$firmId]);
        } elseif ($request->filled('firm_id')) {
            $query->forFirms([$request->firm_id]);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('po_number', 'like', "%{$s}%")
                  ->orWhere('status', 'like', "%{$s}%")
                  ->orWhereHas('vendor', fn($v) => $v->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('po_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('po_date', '<=', $request->end_date);
        }

        $totalAmount = (clone $query)->sum('grand_total');
        $purchaseOrders = $query->orderBy('po_date', 'desc')->paginate(15)->withQueryString();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.purchase-orders.index', compact('purchaseOrders', 'firms', 'totalAmount'));
    }

    public function create()
    {
        return view('admin.purchase-orders.create', $this->dropdowns());
    }

    public function store(Request $request)
    {
        $request->validate([
            'firm_id'       => 'required|exists:firms,id',
            'vendor_id'     => 'required|exists:vendors,id',
            'po_date'       => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:po_date',
            'status'        => 'required|in:Draft,Pending,Approved,Ordered,Received,Cancelled',
            'items'         => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.qty'         => 'required|numeric|min:0.01',
            'items.*.rate'        => 'required|numeric|min:0.00',
            'items.*.discount_pct'=> 'nullable|numeric|min:0|max:100',
            'items.*.gst_pct'     => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Auto generate PO number
            $year = date('Y', strtotime($request->po_date));
            $count = PurchaseOrder::whereYear('po_date', $year)->count() + 1;
            $poNumber = 'PO-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            // Double check uniqueness
            while (PurchaseOrder::where('po_number', $poNumber)->exists()) {
                $count++;
                $poNumber = 'PO-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            $po = PurchaseOrder::create([
                'firm_id'         => $request->firm_id,
                'po_number'       => $poNumber,
                'vendor_id'       => $request->vendor_id,
                'po_date'         => $request->po_date,
                'delivery_date'   => $request->delivery_date,
                'status'          => $request->status,
                'sub_total'       => 0,
                'discount_amount' => 0,
                'taxable_amount'  => 0,
                'cgst_amount'     => 0,
                'sgst_amount'     => 0,
                'igst_amount'     => 0,
                'grand_total'     => 0,
                'remarks'         => $request->remarks,
                'created_by'      => Auth::id(),
            ]);

            $subTotal = 0;
            $totalDiscount = 0;
            $totalTaxable = 0;
            $totalCgst = 0;
            $totalSgst = 0;
            $totalIgst = 0;
            $grandTotal = 0;

            // Load vendor state for tax calculation
            $vendor = Vendor::find($request->vendor_id);
            $firm = Firm::find($po->firm_id);
            $isInterstate = false;
            if ($vendor && $firm) {
                // If states are different, it is Interstate (IGST), otherwise Intrastate (CGST + SGST)
                $vendorState = strtolower(trim($vendor->state ?? ''));
                $firmState = strtolower(trim($firm->state ?? ''));
                if (!empty($vendorState) && !empty($firmState) && $vendorState !== $firmState) {
                    $isInterstate = true;
                }
            }

            foreach ($request->items as $itemData) {
                $qty = (float)$itemData['qty'];
                $rate = (float)$itemData['rate'];
                $discPct = (float)($itemData['discount_pct'] ?? 0);
                $gstPct = (float)($itemData['gst_pct'] ?? 0);

                $rowSub = $qty * $rate;
                $rowDisc = $rowSub * ($discPct / 100);
                $rowTaxable = $rowSub - $rowDisc;
                $rowGst = $rowTaxable * ($gstPct / 100);
                $rowTotal = $rowTaxable + $rowGst;

                $subTotal += $rowSub;
                $totalDiscount += $rowDisc;
                $totalTaxable += $rowTaxable;

                $cgst = 0;
                $sgst = 0;
                $igst = 0;

                if ($isInterstate) {
                    $igst = $rowGst;
                    $totalIgst += $igst;
                } else {
                    $cgst = $rowGst / 2;
                    $sgst = $rowGst / 2;
                    $totalCgst += $cgst;
                    $totalSgst += $sgst;
                }

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'material_id'       => $itemData['material_id'],
                    'qty'               => $qty,
                    'rate'              => $rate,
                    'discount_pct'      => $discPct,
                    'discount_amount'   => $rowDisc,
                    'taxable_amount'    => $rowTaxable,
                    'gst_pct'           => $gstPct,
                    'gst_amount'        => $rowGst,
                    'line_total'        => $rowTotal,
                ]);
            }

            $grandTotal = $totalTaxable + $totalCgst + $totalSgst + $totalIgst;

            $po->update([
                'sub_total'       => $subTotal,
                'discount_amount' => $totalDiscount,
                'taxable_amount'  => $totalTaxable,
                'cgst_amount'     => $totalCgst,
                'sgst_amount'     => $totalSgst,
                'igst_amount'     => $totalIgst,
                'grand_total'     => $grandTotal,
            ]);

            DB::commit();
            return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating Purchase Order: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['firm', 'vendor', 'creator', 'items.material']);
        $this->authorise($purchaseOrder);
        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['items']);
        $this->authorise($purchaseOrder);
        return view('admin.purchase-orders.edit', array_merge(['purchaseOrder' => $purchaseOrder], $this->dropdowns($purchaseOrder->firm_id)));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorise($purchaseOrder);

        $request->validate([
            'firm_id'       => 'required|exists:firms,id',
            'vendor_id'     => 'required|exists:vendors,id',
            'po_date'       => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:po_date',
            'status'        => 'required|in:Draft,Pending,Approved,Ordered,Received,Cancelled',
            'items'         => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.qty'         => 'required|numeric|min:0.01',
            'items.*.rate'        => 'required|numeric|min:0.00',
            'items.*.discount_pct'=> 'nullable|numeric|min:0|max:100',
            'items.*.gst_pct'     => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $purchaseOrder->update([
                'firm_id'       => $request->firm_id,
                'vendor_id'     => $request->vendor_id,
                'po_date'       => $request->po_date,
                'delivery_date' => $request->delivery_date,
                'status'        => $request->status,
                'remarks'       => $request->remarks,
            ]);

            // Clear old items
            $purchaseOrder->items()->delete();

            $subTotal = 0;
            $totalDiscount = 0;
            $totalTaxable = 0;
            $totalCgst = 0;
            $totalSgst = 0;
            $totalIgst = 0;
            $grandTotal = 0;

            // Load vendor state for tax calculation
            $vendor = Vendor::find($request->vendor_id);
            $firm = Firm::find($purchaseOrder->firm_id);
            $isInterstate = false;
            if ($vendor && $firm) {
                $vendorState = strtolower(trim($vendor->state ?? ''));
                $firmState = strtolower(trim($firm->state ?? ''));
                if (!empty($vendorState) && !empty($firmState) && $vendorState !== $firmState) {
                    $isInterstate = true;
                }
            }

            foreach ($request->items as $itemData) {
                $qty = (float)$itemData['qty'];
                $rate = (float)$itemData['rate'];
                $discPct = (float)($itemData['discount_pct'] ?? 0);
                $gstPct = (float)($itemData['gst_pct'] ?? 0);

                $rowSub = $qty * $rate;
                $rowDisc = $rowSub * ($discPct / 100);
                $rowTaxable = $rowSub - $rowDisc;
                $rowGst = $rowTaxable * ($gstPct / 100);
                $rowTotal = $rowTaxable + $rowGst;

                $subTotal += $rowSub;
                $totalDiscount += $rowDisc;
                $totalTaxable += $rowTaxable;

                $cgst = 0;
                $sgst = 0;
                $igst = 0;

                if ($isInterstate) {
                    $igst = $rowGst;
                    $totalIgst += $igst;
                } else {
                    $cgst = $rowGst / 2;
                    $sgst = $rowGst / 2;
                    $totalCgst += $cgst;
                    $totalSgst += $sgst;
                }

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'material_id'       => $itemData['material_id'],
                    'qty'               => $qty,
                    'rate'              => $rate,
                    'discount_pct'      => $discPct,
                    'discount_amount'   => $rowDisc,
                    'taxable_amount'    => $rowTaxable,
                    'gst_pct'           => $gstPct,
                    'gst_amount'        => $rowGst,
                    'line_total'        => $rowTotal,
                ]);
            }

            $grandTotal = $totalTaxable + $totalCgst + $totalSgst + $totalIgst;

            $purchaseOrder->update([
                'sub_total'       => $subTotal,
                'discount_amount' => $totalDiscount,
                'taxable_amount'  => $totalTaxable,
                'cgst_amount'     => $totalCgst,
                'sgst_amount'     => $totalSgst,
                'igst_amount'     => $totalIgst,
                'grand_total'     => $grandTotal,
            ]);

            DB::commit();
            return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating Purchase Order: ' . $e->getMessage());
        }
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->authorise($purchaseOrder);
        DB::beginTransaction();
        try {
            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();
            DB::commit();
            return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting Purchase Order: ' . $e->getMessage());
        }
    }

    public function print(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['firm', 'vendor', 'creator', 'items.material']);
        $this->authorise($purchaseOrder);
        return view('admin.purchase-orders.pdf', compact('purchaseOrder'))->with('printMode', true);
    }

    public function downloadPdf(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['firm', 'vendor', 'creator', 'items.material']);
        $this->authorise($purchaseOrder);
        return view('admin.purchase-orders.pdf', compact('purchaseOrder'))->with('printMode', true);
    }

    public function exportPdf(Request $request)
    {
        $query = PurchaseOrder::with(['firm', 'vendor']);
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        if (!$isAdmin) {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $query->forFirms([$firmId]);
        }
        $purchaseOrders = $query->orderBy('po_date', 'desc')->get();
        return view('admin.purchase-orders.pdf', compact('purchaseOrders'))->with('listMode', true)->with('printMode', true);
    }

    public function exportExcel(Request $request)
    {
        $query = PurchaseOrder::with(['firm', 'vendor']);
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        if (!$isAdmin) {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $query->forFirms([$firmId]);
        }
        $purchaseOrders = $query->orderBy('po_date', 'desc')->get();

        $filename = 'purchase-orders-' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['PO Number', 'Firm', 'Supplier', 'PO Date', 'Delivery Date', 'Status', 'Grand Total'];

        $callback = function() use($purchaseOrders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($purchaseOrders as $po) {
                fputcsv($file, [
                    $po->po_number,
                    $po->firm->firm_name ?? '',
                    $po->vendor->name ?? '',
                    $po->po_date->format('Y-m-d'),
                    $po->delivery_date ? $po->delivery_date->format('Y-m-d') : '',
                    $po->status,
                    $po->grand_total
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
