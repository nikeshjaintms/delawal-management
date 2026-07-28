<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockOutwardRequest;
use App\Models\StockOutward;
use App\Models\StockInward;
use App\Models\Material;
use App\Models\Property;
use App\Models\Firm;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOutwardController extends Controller
{
    private function dropdowns()
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        if ($isAdmin) {
            $materials  = Material::where('status', 'active')->orderBy('material_name')->get();
            $properties = Property::orderBy('property_name')->get();
        } else {
            $firmId     = $user ? $user->firm_id : session('firm_id');
            $materials  = Material::where('firm_id', $firmId)->where('status', 'active')->orderBy('material_name')->get();
            $properties = Property::where('firm_id', $firmId)->orderBy('property_name')->get();
        }
        return compact('materials', 'properties');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $query = StockOutward::with(['material.materialCategory', 'property']);

        if (!$isAdmin) {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        }

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('used_for', 'like', "%{$s}%")
                  ->orWhere('outward_number', 'like', "%{$s}%")
                  ->orWhere('stock_inward_number', 'like', "%{$s}%")
                  ->orWhereHas('material', fn($m) => $m->where('material_name', 'like', "%{$s}%"))
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"));
            });
        }
        if ($request->filter_material) $query->where('material_id', $request->filter_material);
        if ($request->filter_property) $query->where('property_id', $request->filter_property);
        if ($request->filter_date)     $query->where('outward_date', $request->filter_date);

        // Group by outward_number / ID to show representative transactions
        $subQuery = StockOutward::select(DB::raw('MIN(id) as id'))
            ->groupBy(DB::raw('COALESCE(outward_number, CAST(id AS CHAR))'));
        $query->whereIn('id', $subQuery);

        $outwards   = $query->orderBy('outward_date', 'desc')->paginate(15)->withQueryString();

        if ($isAdmin) {
            $materials  = Material::where('status', 'active')->get();
            $properties = Property::get();
        } else {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $materials  = Material::where('firm_id', $firmId)->where('status', 'active')->get();
            $properties = Property::where('firm_id', $firmId)->get();
        }

        return view('admin.stock-outwards.index', compact('outwards', 'materials', 'properties'));
    }

    public function create(Request $request)
    {
        $siNumber = $request->input('stock_inward_number');
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');

        // Fetch available inward transaction numbers
        $inwardNumbersQuery = StockInward::whereNotNull('inward_number')
            ->select('inward_number')
            ->distinct();
        if ($firmId && (!$user || !$user->isAdmin())) {
            $inwardNumbersQuery->where('firm_id', $firmId);
        }
        $inwardNumbers = $inwardNumbersQuery->orderBy('inward_number', 'desc')->pluck('inward_number');

        $propertiesQuery = Property::orderBy('property_name');
        if ($firmId && (!$user || !$user->isAdmin())) {
            $propertiesQuery->where('firm_id', $firmId);
        }
        $properties = $propertiesQuery->get();

        $selectedInward = null;
        $pendingItems = [];

        if ($siNumber) {
            $selectedInward = StockInward::where('inward_number', $siNumber)->first();
            if ($selectedInward) {
                if ($user && !$user->isAdmin() && $selectedInward->firm_id != $firmId) {
                    abort(403);
                }

                $inwards = StockInward::where('inward_number', $siNumber)->get();
                foreach ($inwards as $inw) {
                    $dispatched = StockOutward::where('stock_inward_number', $siNumber)
                        ->where('material_id', $inw->material_id)
                        ->sum('quantity');

                    $qtyReceivedOk = max(0, $inw->quantity - $inw->qty_damaged);
                    $pendingQty = max(0, $qtyReceivedOk - $dispatched);

                    if ($pendingQty > 0) {
                        $pendingItems[] = [
                            'material_id'   => $inw->material_id,
                            'material_name' => $inw->material->material_name ?? 'Unknown',
                            'unit'          => $inw->material->unit ?? 'Units',
                            'qty_received'  => $qtyReceivedOk,
                            'qty_dispatched'=> $dispatched,
                            'qty_pending'   => $pendingQty,
                            'available_stock'=> $inw->material->current_stock ?? 0,
                        ];
                    }
                }
            }
        }

        $dropdowns = $this->dropdowns();

        return view('admin.stock-outwards.create', array_merge(
            $dropdowns,
            compact('inwardNumbers', 'properties', 'selectedInward', 'pendingItems')
        ));
    }

    public function getPendingOutwardItems($inwardNumber)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $inwards = StockInward::where('inward_number', $inwardNumber)
            ->when($firmId && (!$user || !$user->isAdmin()), fn($q) => $q->where('firm_id', $firmId))
            ->with(['material', 'purchaseOrder'])
            ->get();

        if ($inwards->isEmpty()) {
            return response()->json(['error' => 'Inward transaction not found.'], 404);
        }

        $items = [];
        $first = $inwards->first();

        foreach ($inwards as $inw) {
            $dispatched = StockOutward::where('stock_inward_number', $inwardNumber)
                ->where('material_id', $inw->material_id)
                ->sum('quantity');

            $qtyReceivedOk = max(0, $inw->quantity - $inw->qty_damaged);
            $pendingQty = max(0, $qtyReceivedOk - $dispatched);

            $items[] = [
                'material_id'   => $inw->material_id,
                'material_name' => $inw->material->material_name ?? 'Unknown',
                'unit'          => $inw->material->unit ?? 'Units',
                'qty_received'  => $qtyReceivedOk,
                'qty_dispatched'=> $dispatched,
                'qty_pending'   => $pendingQty,
                'available_stock'=> $inw->material->current_stock ?? 0,
            ];
        }

        $poNumber = $first->purchaseOrder ? $first->purchaseOrder->po_number : 'Manual';

        return response()->json([
            'firm_id'       => $first->firm_id,
            'supplier_name' => $first->supplier_name ?? '—',
            'warehouse'     => $first->warehouse ?? 'Main Warehouse',
            'inward_date'   => $first->inward_date->format('Y-m-d'),
            'po_number'     => $poNumber,
            'items'         => $items
        ]);
    }

    public function store(StockOutwardRequest $request)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if ($request->has('items') && $request->filled('stock_inward_number')) {
            $siNumber = $request->stock_inward_number;
            $inwGroup = StockInward::where('inward_number', $siNumber)->first();
            if (!$inwGroup) {
                return back()->with('error', 'Reference Inward not found.');
            }

            if ($user && !$user->isAdmin() && $inwGroup->firm_id != $firmId) {
                abort(403);
            }

            // Validate quantities first
            foreach ($request->items as $itemData) {
                $materialId = $itemData['material_id'];
                $qtyDisp = (float) $itemData['qty_dispatch'];

                if ($qtyDisp <= 0) continue;

                $material = Material::find($materialId);
                if (!$material || $material->current_stock < $qtyDisp) {
                    $avail = $material ? $material->current_stock : 0;
                    return back()->withInput()->with('error', "Insufficient stock for " . ($material ? $material->material_name : 'Item') . ". Available: {$avail}");
                }

                // Verify against SI pending
                $inwItem = StockInward::where('inward_number', $siNumber)->where('material_id', $materialId)->first();
                $dispatched = StockOutward::where('stock_inward_number', $siNumber)
                    ->where('material_id', $materialId)
                    ->sum('quantity');

                $qtyReceivedOk = max(0, ($inwItem ? $inwItem->quantity : 0) - ($inwItem ? $inwItem->qty_damaged : 0));
                $pendingQty = max(0, $qtyReceivedOk - $dispatched);

                if ($qtyDisp > $pendingQty) {
                    return back()->withInput()->with('error', "Dispatch quantity exceeds remaining quantity in Stock Inward for " . ($material ? $material->material_name : 'Item'));
                }
            }

            // Generate Outward Number
            $year = date('Y', strtotime($request->outward_date));
            $count = StockOutward::whereYear('outward_date', $year)->whereNotNull('outward_number')->distinct('outward_number')->count('outward_number') + 1;
            $outwardNumber = 'SO-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            while (StockOutward::where('outward_number', $outwardNumber)->exists()) {
                $count++;
                $outwardNumber = 'SO-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }

            DB::beginTransaction();
            try {
                foreach ($request->items as $itemData) {
                    $materialId = $itemData['material_id'];
                    $qtyDisp = (float) $itemData['qty_dispatch'];

                    if ($qtyDisp <= 0) continue;

                    $out = StockOutward::create([
                        'firm_id'             => $inwGroup->firm_id,
                        'outward_number'      => $outwardNumber,
                        'stock_inward_number' => $siNumber,
                        'material_id'         => $materialId,
                        'property_id'         => $request->property_id,
                        'outward_date'        => $request->outward_date,
                        'quantity'            => $qtyDisp,
                        'vehicle_no'          => $request->vehicle_no,
                        'driver_name'         => $request->driver_name,
                        'lr_no'               => $request->lr_no,
                        'transport_name'      => $request->transport_name,
                        'used_for'            => 'Dispatched to Site via Gate Pass ' . $outwardNumber,
                        'remarks'             => $request->remarks,
                    ]);

                    $material = Material::find($materialId);
                    if ($material) {
                        $material->decrement('current_stock', $qtyDisp);

                        StockMovement::create([
                            'firm_id'         => $inwGroup->firm_id,
                            'material_id'     => $materialId,
                            'reference_type'  => 'Stock Outward (GP)',
                            'reference_id'    => $out->id,
                            'qty_out'         => $qtyDisp,
                            'balance_stock'   => $material->current_stock,
                            'balance_damaged' => $material->damaged_stock,
                            'remarks'         => 'Dispatched to Site via ' . $outwardNumber,
                            'created_by'      => Auth::id(),
                        ]);
                    }
                }

                DB::commit();
                return redirect()->route('stock-outwards.index')->with('success', 'Stock outward recorded. Outward Number: ' . $outwardNumber);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Error saving stock outward: ' . $e->getMessage());
            }
        } else {
            // Manual Outward
            $material = Material::where('firm_id', Auth::user()->firm_id)->findOrFail($request->material_id);
            $qty      = (float) $request->quantity;

            if ($material->current_stock < $qty) {
                return back()->withInput()->withErrors([
                    'quantity' => 'Insufficient stock. Available: '.$material->current_stock.' '.$material->unit
                ]);
            }

            // Generate Outward Number
            $year = date('Y', strtotime($request->outward_date));
            $count = StockOutward::whereYear('outward_date', $year)->whereNotNull('outward_number')->distinct('outward_number')->count('outward_number') + 1;
            $outwardNumber = 'SO-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            while (StockOutward::where('outward_number', $outwardNumber)->exists()) {
                $count++;
                $outwardNumber = 'SO-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }

            DB::beginTransaction();
            try {
                $out = StockOutward::create([
                    'firm_id'        => Auth::user()->firm_id,
                    'outward_number' => $outwardNumber,
                    'material_id'    => $request->material_id,
                    'property_id'    => $request->property_id ?: null,
                    'outward_date'   => $request->outward_date,
                    'quantity'       => $qty,
                    'used_for'       => $request->used_for,
                    'remarks'        => $request->remarks,
                ]);

                $material->decrement('current_stock', $qty);

                StockMovement::create([
                    'firm_id'         => Auth::user()->firm_id,
                    'material_id'     => $request->material_id,
                    'reference_type'  => 'Stock Outward (Manual)',
                    'reference_id'    => $out->id,
                    'qty_out'         => $qty,
                    'balance_stock'   => $material->current_stock,
                    'balance_damaged' => $material->damaged_stock,
                    'remarks'         => 'Manual Stock Outward',
                    'created_by'      => Auth::id(),
                ]);

                DB::commit();
                return redirect()->route('stock-outwards.index')->with('success', 'Manual stock outward recorded.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
            }
        }
    }

    public function show(StockOutward $stockOutward)
    {
        $user = Auth::user();
        if ($stockOutward->firm_id != $user->firm_id) abort(403);

        if ($stockOutward->outward_number) {
            $outwards = StockOutward::where('outward_number', $stockOutward->outward_number)->with(['material.materialCategory', 'property'])->get();
            $outwardGroup = $outwards->first();
            return view('admin.stock-outwards.show', compact('outwardGroup', 'outwards'));
        } else {
            $stockOutward->load(['material.materialCategory', 'property']);
            return view('admin.stock-outwards.show', compact('stockOutward'));
        }
    }

    public function print(StockOutward $stockOutward)
    {
        $user = Auth::user();
        if ($stockOutward->firm_id != $user->firm_id) abort(403);

        if ($stockOutward->outward_number) {
            $outwards = StockOutward::where('outward_number', $stockOutward->outward_number)->with(['material.materialCategory', 'property'])->get();
            $outwardGroup = $outwards->first();
            return view('admin.stock-outwards.show', compact('outwardGroup', 'outwards'))->with('printMode', true);
        } else {
            $stockOutward->load(['material.materialCategory', 'property']);
            return view('admin.stock-outwards.show', compact('stockOutward'))->with('printMode', true);
        }
    }

    public function edit(StockOutward $stockOutward)
    {
        if ($stockOutward->firm_id != Auth::user()->firm_id) abort(403);
        if ($stockOutward->stock_inward_number) {
            return redirect()->route('stock-outwards.index')->with('error', 'Cannot edit stock outward dispatched against a Stock Inward.');
        }

        return view('admin.stock-outwards.edit', array_merge(
            ['stockOutward' => $stockOutward], $this->dropdowns()
        ));
    }

    public function update(StockOutwardRequest $request, StockOutward $stockOutward)
    {
        if ($stockOutward->firm_id != Auth::user()->firm_id) abort(403);
        if ($stockOutward->stock_inward_number) {
            return back()->with('error', 'Cannot edit reference stock outward.');
        }

        $oldQty  = (float) $stockOutward->quantity;
        $newQty  = (float) $request->quantity;
        $material = Material::where('firm_id', Auth::user()->firm_id)->findOrFail($stockOutward->material_id);

        $availableAfter = $material->current_stock + $oldQty - $newQty;
        if ($availableAfter < 0) {
            return back()->withInput()->withErrors([
                'quantity' => 'Insufficient stock. Max allowed: '.($material->current_stock + $oldQty).' '.$material->unit
            ]);
        }

        $material->increment('current_stock', $oldQty);
        $material->decrement('current_stock', $newQty);

        $stockOutward->update([
            'material_id'  => $request->material_id,
            'property_id'  => $request->property_id ?: null,
            'outward_date' => $request->outward_date,
            'quantity'     => $newQty,
            'used_for'     => $request->used_for,
            'remarks'      => $request->remarks,
        ]);

        return redirect()->route('stock-outwards.index')->with('success', 'Stock outward updated.');
    }

    public function destroy(StockOutward $stockOutward)
    {
        if ($stockOutward->firm_id != Auth::user()->firm_id) abort(403);

        DB::beginTransaction();
        try {
            if ($stockOutward->outward_number) {
                $outwards = StockOutward::where('outward_number', $stockOutward->outward_number)->get();
                foreach ($outwards as $out) {
                    $material = Material::find($out->material_id);
                    if ($material) {
                        $material->increment('current_stock', (float) $out->quantity);
                    }
                    $out->delete();
                }
            } else {
                $material = Material::find($stockOutward->material_id);
                if ($material) {
                    $material->increment('current_stock', (float) $stockOutward->quantity);
                }
                $stockOutward->delete();
            }
            DB::commit();
            return redirect()->route('stock-outwards.index')->with('success', 'Stock outward deleted and stock reversed.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting stock outward: ' . $e->getMessage());
        }
    }
}
