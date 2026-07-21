<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockInwardRequest;

use App\Models\StockInward;
use App\Models\Material;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockInwardController extends Controller
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
        $query = StockInward::with(['material.materialCategory', 'property']);

        if (!$isAdmin) {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        }

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('supplier_name', 'like', "%{$s}%")
                  ->orWhere('bill_no', 'like', "%{$s}%")
                  ->orWhereHas('material', fn($m) => $m->where('material_name', 'like', "%{$s}%"))
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"));
            });
        }
        if ($request->filter_material)  $query->where('material_id', $request->filter_material);
        if ($request->filter_property)  $query->where('property_id', $request->filter_property);
        if ($request->filter_date)      $query->where('inward_date', $request->filter_date);

        $inwards    = $query->orderBy('inward_date', 'desc')->paginate(15);

        if ($isAdmin) {
            $materials  = Material::where('status', 'active')->get();
            $properties = Property::get();
        } else {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $materials  = Material::where('firm_id', $firmId)->where('status', 'active')->get();
            $properties = Property::where('firm_id', $firmId)->get();
        }

        return view('admin.stock-inwards.index', compact('inwards', 'materials', 'properties'));
    }

    public function create()
    {
        return view('admin.stock-inwards.create', $this->dropdowns());
    }

    public function store(StockInwardRequest $request)
    {
        $user = Auth::user();
        $firmId = $request->firm_id ?? ($user ? $user->firm_id : session('firm_id'));
        $qty   = (float) $request->quantity;
        $rate  = (float) ($request->rate ?? 0);
        $total = $qty * $rate;

        $inward = StockInward::create([
            'firm_id'       => $firmId,
            'material_id'   => $request->material_id,
            'property_id'   => $request->property_id ?: null,
            'inward_date'   => $request->inward_date,
            'quantity'      => $qty,
            'rate'          => $rate ?: null,
            'total_amount'  => $total ?: null,
            'supplier_name' => $request->supplier_name,
            'bill_no'       => $request->bill_no,
            'remarks'       => $request->remarks,
        ]);

        // Increase material current_stock
        $material = Material::find($request->material_id);
        if ($material) {
            $material->increment('current_stock', $qty);
        }

        return redirect()->route('stock-inwards.index')->with('success', 'Stock inward recorded. Material stock updated.');
    }

    public function show(StockInward $stockInward)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $stockInward->firm_id != $firmId) abort(403);
        $stockInward->load(['material.materialCategory', 'property']);
        return view('admin.stock-inwards.show', compact('stockInward'));
    }

    public function edit(StockInward $stockInward)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $stockInward->firm_id != $firmId) abort(403);
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

        $oldQty = (float) $stockInward->quantity;
        $newQty = (float) $request->quantity;
        $rate   = (float) ($request->rate ?? 0);

        // Reverse old, add new to current_stock
        $material = Material::find($stockInward->material_id);
        if ($material) {
            $material->decrement('current_stock', $oldQty);
            $material->increment('current_stock', $newQty);
        }

        $stockInward->update([
            'material_id'   => $request->material_id,
            'property_id'   => $request->property_id ?: null,
            'inward_date'   => $request->inward_date,
            'quantity'      => $newQty,
            'rate'          => $rate ?: null,
            'total_amount'  => $newQty * $rate ?: null,
            'supplier_name' => $request->supplier_name,
            'bill_no'       => $request->bill_no,
            'remarks'       => $request->remarks,
        ]);

        return redirect()->route('stock-inwards.index')->with('success', 'Stock inward updated. Material stock recalculated.');
    }

    public function destroy(StockInward $stockInward)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $stockInward->firm_id != $firmId) abort(403);

        // Reverse the stock
        $material = Material::find($stockInward->material_id);
        if ($material) {
            $material->decrement('current_stock', (float) $stockInward->quantity);
        }

        $stockInward->delete();
        return redirect()->route('stock-inwards.index')->with('success', 'Stock inward deleted. Material stock reversed.');
    }
}
