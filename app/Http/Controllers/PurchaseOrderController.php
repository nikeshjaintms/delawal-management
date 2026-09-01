<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use App\Models\Firm;
use App\Models\Project;
use App\Models\Contractor;
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

        $vendorQuery     = Vendor::where('status', 'active')->orderBy('name');
        $materialQuery   = Material::where('status', 'active')->orderBy('material_name');
        $projectQuery    = Project::with('propertyMaster')->orderBy('project_name');
        $contractorQuery = Contractor::with('project')->where('status', 'active')->orderBy('contractor_name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $vendorQuery->where('firm_id', $firmId);
            $materialQuery->where('firm_id', $firmId);
            $projectQuery->where('firm_id', $firmId);
            $contractorQuery->where('firm_id', $firmId);
        }

        return [
            'firms'       => $firms,
            'vendors'     => $vendorQuery->get(),
            'materials'   => $materialQuery->get(),
            'projects'    => $projectQuery->get(),
            'contractors' => $contractorQuery->get(),
        ];
    }

    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['firm', 'vendor', 'contractor', 'creator', 'project.propertyMaster']);

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
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"))
                  ->orWhereHas('project', fn($p) => $p->where('project_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }
        if ($request->filled('filter_project')) {
            $query->where('project_id', $request->filter_project);
        }
        if ($request->filled('filter_contractor')) {
            $query->where('contractor_id', $request->filter_contractor);
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
        if ($isAdmin) {
            $projects = Project::with('propertyMaster')->orderBy('project_name')->get();
            $contractors = Contractor::with('project')->where('status', 'active')->orderBy('contractor_name')->get();
        } else {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $projects = Project::where('firm_id', $firmId)->with('propertyMaster')->orderBy('project_name')->get();
            $contractors = Contractor::where('firm_id', $firmId)->with('project')->where('status', 'active')->orderBy('contractor_name')->get();
        }

        return view('admin.purchase-orders.index', compact('purchaseOrders', 'firms', 'projects', 'contractors', 'totalAmount'));
    }

    public function create()
    {
        return view('admin.purchase-orders.create', $this->dropdowns());
    }

    public function store(Request $request)
    {
        $request->validate([
            'firm_id'       => 'required|exists:firms,id',
            'project_id'    => 'nullable|exists:projects,id',
            'contractor_id' => 'nullable|exists:contractors,id',
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

            $projectId = $request->project_id;
            if (!$projectId && $request->contractor_id) {
                $con = Contractor::find($request->contractor_id);
                if ($con && $con->project_id) {
                    $projectId = $con->project_id;
                }
            }

            $po = PurchaseOrder::create([
                'firm_id'         => $request->firm_id,
                'project_id'      => $projectId ?: null,
                'contractor_id'   => $request->contractor_id ?: null,
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

                if ($isInterstate) {
                    $totalIgst += $rowGst;
                } else {
                    $totalCgst += ($rowGst / 2);
                    $totalSgst += ($rowGst / 2);
                }
                $grandTotal += $rowTotal;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'material_id'       => $itemData['material_id'],
                    'qty'               => $qty,
                    'rate'              => $rate,
                    'discount_pct'      => $discPct,
                    'gst_pct'           => $gstPct,
                    'total_amount'      => $rowTotal,
                ]);
            }

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
            return back()->withInput()->with('error', 'Failed to create Purchase Order: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->authorise($purchaseOrder);
        $purchaseOrder->load(['firm', 'vendor', 'contractor', 'creator', 'project.propertyMaster', 'items.material']);
        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    public function print(PurchaseOrder $purchaseOrder)
    {
        $this->authorise($purchaseOrder);
        $purchaseOrder->load(['firm', 'vendor', 'contractor', 'creator', 'project.propertyMaster', 'items.material']);
        return view('admin.purchase-orders.show', compact('purchaseOrder'))->with('printMode', true);
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->authorise($purchaseOrder);
        $purchaseOrder->load(['items.material']);
        $dropdowns = $this->dropdowns($purchaseOrder->firm_id);
        return view('admin.purchase-orders.edit', array_merge(['purchaseOrder' => $purchaseOrder], $dropdowns));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorise($purchaseOrder);

        $request->validate([
            'firm_id'       => 'required|exists:firms,id',
            'project_id'    => 'nullable|exists:projects,id',
            'contractor_id' => 'nullable|exists:contractors,id',
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
            $projectId = $request->project_id;
            if (!$projectId && $request->contractor_id) {
                $con = Contractor::find($request->contractor_id);
                if ($con && $con->project_id) {
                    $projectId = $con->project_id;
                }
            }

            $purchaseOrder->update([
                'firm_id'       => $request->firm_id,
                'project_id'    => $projectId ?: null,
                'contractor_id' => $request->contractor_id ?: null,
                'vendor_id'     => $request->vendor_id,
                'po_date'       => $request->po_date,
                'delivery_date' => $request->delivery_date,
                'status'        => $request->status,
                'remarks'       => $request->remarks,
            ]);

            // Sync items
            $purchaseOrder->items()->delete();

            $subTotal = 0;
            $totalDiscount = 0;
            $totalTaxable = 0;
            $totalCgst = 0;
            $totalSgst = 0;
            $totalIgst = 0;
            $grandTotal = 0;

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

                if ($isInterstate) {
                    $totalIgst += $rowGst;
                } else {
                    $totalCgst += ($rowGst / 2);
                    $totalSgst += ($rowGst / 2);
                }
                $grandTotal += $rowTotal;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'material_id'       => $itemData['material_id'],
                    'qty'               => $qty,
                    'rate'              => $rate,
                    'discount_pct'      => $discPct,
                    'gst_pct'           => $gstPct,
                    'total_amount'      => $rowTotal,
                ]);
            }

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
            return back()->withInput()->with('error', 'Failed to update Purchase Order: ' . $e->getMessage());
        }
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->authorise($purchaseOrder);
        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order deleted successfully.');
    }

    public function downloadPdf(PurchaseOrder $purchaseOrder)
    {
        $this->authorise($purchaseOrder);
        $purchaseOrder->load(['firm', 'vendor', 'contractor', 'creator', 'project.propertyMaster', 'items.material']);
        return view('admin.purchase-orders.show', compact('purchaseOrder'))->with('printMode', true);
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $query = PurchaseOrder::with(['firm', 'vendor', 'contractor', 'creator', 'project']);

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
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"))
                  ->orWhereHas('project', fn($p) => $p->where('project_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }
        if ($request->filled('filter_project')) {
            $query->where('project_id', $request->filter_project);
        }
        if ($request->filled('filter_contractor')) {
            $query->where('contractor_id', $request->filter_contractor);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('po_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('po_date', '<=', $request->end_date);
        }

        $purchaseOrders = $query->orderBy('po_date', 'desc')->get();
        $filename = 'purchase-orders-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($purchaseOrders) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['Delawala Properties & Management - Purchase Orders Report']);
            fputcsv($handle, ['Generated on', date('d M Y, h:i A')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'PO Number', 'Firm', 'Project', 'Contractor', 'Supplier / Vendor',
                'PO Date', 'Delivery Date', 'Status', 'Taxable Amount (Rs)', 'GST Amount (Rs)', 'Grand Total (Rs)'
            ]);

            foreach ($purchaseOrders as $po) {
                fputcsv($handle, [
                    $po->po_number,
                    $po->firm->firm_name ?? '-',
                    $po->project->project_name ?? '-',
                    $po->contractor->contractor_name ?? '-',
                    $po->vendor->name ?? '-',
                    $po->po_date ? $po->po_date->format('d M Y') : '-',
                    $po->delivery_date ? $po->delivery_date->format('d M Y') : '-',
                    $po->status,
                    $po->taxable_amount,
                    ($po->cgst_amount + $po->sgst_amount + $po->igst_amount),
                    $po->grand_total,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        return $this->exportExcel($request);
    }
}
