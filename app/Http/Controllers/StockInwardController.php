<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockInwardRequest;
use App\Models\StockInward;
use App\Models\Material;
use App\Models\Project;
use App\Models\Firm;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockInwardController extends Controller
{
    private function dropdowns()
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        if ($isAdmin) {
            $materials = Material::where('status', 'active')->orderBy('material_name')->get();
            $projects  = Project::with('propertyMaster')->orderBy('project_name')->get();
        } else {
            $firmId    = $user ? $user->firm_id : session('firm_id');
            $materials = Material::where('firm_id', $firmId)->where('status', 'active')->orderBy('material_name')->get();
            $projects  = Project::where('firm_id', $firmId)->with('propertyMaster')->orderBy('project_name')->get();
        }
        return compact('materials', 'projects');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $query = StockInward::with(['material.materialCategory', 'project.propertyMaster', 'property', 'purchaseOrder']);

        if (!$isAdmin) {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        }

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('supplier_name', 'like', "%{$s}%")
                  ->orWhere('bill_no', 'like', "%{$s}%")
                  ->orWhere('inward_number', 'like', "%{$s}%")
                  ->orWhereHas('material', fn($m) => $m->where('material_name', 'like', "%{$s}%"))
                  ->orWhereHas('project', fn($p) => $p->where('project_name', 'like', "%{$s}%"));
            });
        }
        if ($request->filter_material)  $query->where('material_id', $request->filter_material);
        if ($request->filter_project)   $query->where('project_id', $request->filter_project);
        if ($request->filter_date)      $query->where('inward_date', $request->filter_date);

        // Group by inward_number / ID to show representative transactions
        $subQuery = StockInward::select(DB::raw('MIN(id) as id'))
            ->groupBy(DB::raw('COALESCE(inward_number, CAST(id AS CHAR))'));
        $query->whereIn('id', $subQuery);

        $inwards = $query->orderBy('inward_date', 'desc')->paginate(15)->withQueryString();

        if ($isAdmin) {
            $materials = Material::where('status', 'active')->get();
            $projects  = Project::with('propertyMaster')->orderBy('project_name')->get();
        } else {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $materials = Material::where('firm_id', $firmId)->where('status', 'active')->get();
            $projects  = Project::where('firm_id', $firmId)->with('propertyMaster')->orderBy('project_name')->get();
        }

        return view('admin.stock-inwards.index', compact('inwards', 'materials', 'projects'));
    }

    public function create(Request $request)
    {
        $poId = $request->input('purchase_order_id');
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $poQuery = PurchaseOrder::whereNotIn('status', ['Completed', 'Cancelled', 'Received']);
        if ($firmId && (!$user || !$user->isAdmin())) {
            $poQuery->where('firm_id', $firmId);
        }
        $purchaseOrders = $poQuery->orderBy('po_number', 'desc')->get();

        $selectedPo = null;
        $pendingItems = [];

        if ($poId) {
            $selectedPo = PurchaseOrder::with(['vendor', 'firm', 'items.material'])->find($poId);
            if ($selectedPo) {
                $isAdmin = $user && $user->isAdmin();
                if (!$isAdmin && $selectedPo->firm_id != $firmId) {
                    abort(403);
                }
                foreach ($selectedPo->items as $poItem) {
                    $receivedQty = StockInward::where('purchase_order_id', $poId)
                        ->where('material_id', $poItem->material_id)
                        ->sum('quantity');

                    $pendingQty = max(0, $poItem->qty - $receivedQty);

                    $pendingItems[] = [
                        'material_id'   => $poItem->material_id,
                        'material_name' => $poItem->material->material_name ?? 'Unknown',
                        'unit'          => $poItem->material->unit ?? 'Units',
                        'rate'          => $poItem->rate,
                        'discount_pct'  => $poItem->discount_pct,
                        'gst_pct'       => $poItem->gst_pct,
                        'qty_ordered'   => $poItem->qty,
                        'qty_received'  => $receivedQty,
                        'qty_pending'   => $pendingQty,
                    ];
                }
            }
        }

        $dropdowns = $this->dropdowns();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $selectedProjectId = $request->input('project_id');

        return view('admin.stock-inwards.create', array_merge(
            $dropdowns,
            compact('purchaseOrders', 'selectedPo', 'pendingItems', 'firms', 'selectedProjectId')
        ));
    }

    public function getPendingItems(PurchaseOrder $purchaseOrder)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $isAdmin = $user && $user->isAdmin();
        if (!$isAdmin && $purchaseOrder->firm_id != $firmId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $pendingItems = [];
        foreach ($purchaseOrder->items as $poItem) {
            $receivedQty = StockInward::where('purchase_order_id', $purchaseOrder->id)
                ->where('material_id', $poItem->material_id)
                ->sum('quantity');

            $pendingQty = max(0, $poItem->qty - $receivedQty);

            $pendingItems[] = [
                'material_id'   => $poItem->material_id,
                'material_name' => $poItem->material->material_name ?? 'Unknown',
                'unit'          => $poItem->material->unit ?? 'Units',
                'rate'          => $poItem->rate,
                'discount_pct'  => $poItem->discount_pct,
                'gst_pct'       => $poItem->gst_pct,
                'qty_ordered'   => $poItem->qty,
                'qty_received'  => $receivedQty,
                'qty_pending'   => $pendingQty,
            ];
        }

        $vendor = $purchaseOrder->vendor;
        $firm = $purchaseOrder->firm;
        $isInterstate = false;
        if ($vendor && $firm) {
            $vendorState = strtolower(trim($vendor->state ?? ''));
            $firmState = strtolower(trim($firm->state ?? ''));
            if (!empty($vendorState) && !empty($firmState) && $vendorState !== $firmState) {
                $isInterstate = true;
            }
        }

        return response()->json([
            'firm_id'       => $purchaseOrder->firm_id,
            'project_id'    => $purchaseOrder->project_id,
            'vendor_id'     => $purchaseOrder->vendor_id,
            'vendor_name'   => $purchaseOrder->vendor->name ?? '',
            'po_date'       => $purchaseOrder->po_date->format('Y-m-d'),
            'is_interstate' => $isInterstate,
            'items'         => $pendingItems
        ]);
    }

    public function print(StockInward $stockInward)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $stockInward->firm_id != $firmId) abort(403);

        if ($stockInward->inward_number) {
            $inwards = StockInward::where('inward_number', $stockInward->inward_number)->with(['material.materialCategory', 'project.propertyMaster'])->get();
            $inwardGroup = $inwards->first();
            return view('admin.stock-inwards.show', compact('inwardGroup', 'inwards'))->with('printMode', true);
        } else {
            $stockInward->load(['material.materialCategory', 'project.propertyMaster']);
            return view('admin.stock-inwards.show', compact('stockInward'))->with('printMode', true);
        }
    }

    public function store(StockInwardRequest $request)
    {
        $user = Auth::user();
        $firmId = $request->firm_id ?? ($user ? $user->firm_id : session('firm_id'));

        if ($request->filled('purchase_order_id') && $request->has('items')) {
            $po = PurchaseOrder::find($request->purchase_order_id);
            if (!$po) {
                return back()->with('error', 'Purchase Order not found.');
            }

            // Generate IMIR Number
            $count = StockInward::whereNotNull('inward_number')->where('inward_number', 'like', 'IMIR-%')->distinct('inward_number')->count('inward_number') + 1;
            $inwardNumber = 'IMIR-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            while (StockInward::where('inward_number', $inwardNumber)->exists()) {
                $count++;
                $inwardNumber = 'IMIR-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }

            DB::beginTransaction();
            try {
                foreach ($request->items as $itemData) {
                    $qtyRec = (float) $itemData['qty_received'];
                    $qtyDmg = (float) ($itemData['qty_damaged'] ?? 0);
                    $rate = (float) $itemData['rate'];
                    $discountPct = (float) ($itemData['discount_pct'] ?? 0);
                    $gstPct = (float) ($itemData['gst_pct'] ?? 0);

                    if ($qtyRec <= 0) continue;

                    $sub = $qtyRec * $rate;
                    $disc = $sub * ($discountPct / 100);
                    $taxable = $sub - $disc;
                    $gst = $taxable * ($gstPct / 100);
                    $total = $taxable + $gst;

                    $inward = StockInward::create([
                        'firm_id'           => $po->firm_id,
                        'project_id'        => $po->project_id ?? $request->project_id,
                        'inward_number'     => $inwardNumber,
                        'purchase_order_id' => $po->id,
                        'material_id'       => $itemData['material_id'],
                        'property_id'       => null,
                        'inward_date'       => $request->inward_date,
                        'quantity'          => $qtyRec,
                        'qty_ordered'       => (float) $itemData['qty_ordered'],
                        'qty_damaged'       => $qtyDmg,
                        'rate'              => $rate,
                        'gst_pct'           => $gstPct,
                        'gst_amount'        => $gst,
                        'discount_pct'      => $discountPct,
                        'discount_amount'   => $disc,
                        'total_amount'      => $total,
                        'supplier_name'     => $po->vendor->name ?? '',
                        'bill_no'           => $request->bill_no,
                        'challan_no'        => $request->challan_no,
                        'vehicle_no'        => $request->vehicle_no,
                        'warehouse'         => $request->warehouse,
                        'remarks'           => $request->remarks,
                    ]);

                    $material = Material::find($itemData['material_id']);
                    if ($material) {
                        $acceptedQty = max(0, $qtyRec - $qtyDmg);
                        $material->increment('current_stock', $acceptedQty);
                        if ($qtyDmg > 0) {
                            $material->increment('damaged_stock', $qtyDmg);
                        }

                        StockMovement::create([
                            'firm_id'         => $po->firm_id,
                            'material_id'     => $itemData['material_id'],
                            'reference_type'  => 'Stock Inward (PO)',
                            'reference_id'    => $inward->id,
                            'qty_in'          => $qtyRec,
                            'qty_damaged'     => $qtyDmg,
                            'balance_stock'   => $material->current_stock,
                            'balance_damaged' => $material->damaged_stock,
                            'remarks'         => 'Received against PO ' . $po->po_number,
                            'created_by'      => Auth::id(),
                        ]);
                    }
                }

                // Update PO Status
                $allCompleted = true;
                $anyReceived = false;
                foreach ($po->items as $poItem) {
                    $receivedQty = StockInward::where('purchase_order_id', $po->id)
                        ->where('material_id', $poItem->material_id)
                        ->sum('quantity');

                    if ($receivedQty > 0) $anyReceived = true;
                    if ($receivedQty < $poItem->qty) $allCompleted = false;
                }

                if ($allCompleted) {
                    $po->update(['status' => 'Completed']);
                } elseif ($anyReceived) {
                    $po->update(['status' => 'Partially Received']);
                }

                DB::commit();
                return redirect()->route('stock-inwards.index')->with('success', 'Stock inward recorded. Inward Number: ' . $inwardNumber);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Error saving stock inward: ' . $e->getMessage());
            }
        } else {
            // Manual Inward
            $count = StockInward::whereNotNull('inward_number')->where('inward_number', 'like', 'IMIR-%')->distinct('inward_number')->count('inward_number') + 1;
            $inwardNumber = 'IMIR-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            while (StockInward::where('inward_number', $inwardNumber)->exists()) {
                $count++;
                $inwardNumber = 'IMIR-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }

            $qty   = (float) $request->quantity;
            $rate  = (float) ($request->rate ?? 0);
            $total = $qty * $rate;

            DB::beginTransaction();
            try {
                $inward = StockInward::create([
                    'firm_id'       => $firmId,
                    'project_id'    => $request->project_id ?: null,
                    'inward_number' => $inwardNumber,
                    'material_id'   => $request->material_id,
                    'property_id'   => null,
                    'inward_date'   => $request->inward_date,
                    'quantity'      => $qty,
                    'qty_ordered'   => $qty,
                    'rate'          => $rate ?: null,
                    'total_amount'  => $total ?: null,
                    'supplier_name' => $request->supplier_name,
                    'bill_no'       => $request->bill_no,
                    'challan_no'    => $request->challan_no,
                    'remarks'       => $request->remarks,
                ]);

                $material = Material::find($request->material_id);
                if ($material) {
                    $material->increment('current_stock', $qty);

                    StockMovement::create([
                        'firm_id'         => $firmId,
                        'material_id'     => $request->material_id,
                        'reference_type'  => 'Stock Inward (Manual)',
                        'reference_id'    => $inward->id,
                        'qty_in'          => $qty,
                        'balance_stock'   => $material->current_stock,
                        'balance_damaged' => $material->damaged_stock,
                        'remarks'         => 'Manual Stock Inward',
                        'created_by'      => Auth::id(),
                    ]);
                }

                DB::commit();
                return redirect()->route('stock-inwards.index')->with('success', 'Manual stock inward recorded.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
            }
        }
    }

    public function show(StockInward $stockInward)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $stockInward->firm_id != $firmId) abort(403);

        if ($stockInward->inward_number) {
            $inwards = StockInward::where('inward_number', $stockInward->inward_number)->with(['material.materialCategory', 'project.propertyMaster', 'property'])->get();
            $inwardGroup = $inwards->first();
            return view('admin.stock-inwards.show', compact('inwardGroup', 'inwards'));
        } else {
            $stockInward->load(['material.materialCategory', 'project.propertyMaster', 'property']);
            return view('admin.stock-inwards.show', compact('stockInward'));
        }
    }

    public function edit(StockInward $stockInward)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $stockInward->firm_id != $firmId) abort(403);
        
        if ($stockInward->purchase_order_id) {
            return redirect()->route('stock-inwards.index')->with('error', 'Cannot edit stock inward received against a Purchase Order.');
        }

        return view('admin.stock-inwards.edit', array_merge(
            ['stockInward' => $stockInward], $this->dropdowns()
        ));
    }

    public function update(StockInwardRequest $request, StockInward $stockInward)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $stockInward->firm_id != $firmId) abort(403);
        if ($stockInward->purchase_order_id) {
            return back()->with('error', 'Cannot edit PO stock inward.');
        }

        $oldQty = (float) $stockInward->quantity;
        $newQty = (float) $request->quantity;
        $rate   = (float) ($request->rate ?? 0);

        $material = Material::find($stockInward->material_id);
        if ($material) {
            $material->decrement('current_stock', $oldQty);
            $material->increment('current_stock', $newQty);
        }

        $stockInward->update([
            'material_id'   => $request->material_id,
            'project_id'    => $request->project_id ?: null,
            'inward_date'   => $request->inward_date,
            'quantity'      => $newQty,
            'rate'          => $rate ?: null,
            'total_amount'  => ($newQty * $rate) ?: null,
            'supplier_name' => $request->supplier_name,
            'bill_no'       => $request->bill_no,
            'remarks'       => $request->remarks,
        ]);

        return redirect()->route('stock-inwards.index')->with('success', 'Stock inward updated.');
    }

    public function destroy(StockInward $stockInward)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $stockInward->firm_id != $firmId) abort(403);

        DB::beginTransaction();
        try {
            if ($stockInward->inward_number) {
                $inwards = StockInward::where('inward_number', $stockInward->inward_number)->get();
                foreach ($inwards as $inw) {
                    $material = Material::find($inw->material_id);
                    if ($material) {
                        $acceptedQty = max(0, $inw->quantity - $inw->qty_damaged);
                        $material->decrement('current_stock', $acceptedQty);
                        if ($inw->qty_damaged > 0) {
                            $material->decrement('damaged_stock', $inw->qty_damaged);
                        }
                    }
                    $inw->delete();
                }
            } else {
                $material = Material::find($stockInward->material_id);
                if ($material) {
                    $material->decrement('current_stock', $stockInward->quantity);
                }
                $stockInward->delete();
            }
            DB::commit();
            return redirect()->route('stock-inwards.index')->with('success', 'Stock inward deleted and stock reversed.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting stock inward: ' . $e->getMessage());
        }
    }
}
