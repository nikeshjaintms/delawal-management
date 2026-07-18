<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockOutwardRequest;

use App\Models\StockOutward;
use App\Models\Material;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockOutwardController extends Controller
{
    private function dropdowns()
    {
        $firmId     = Auth::user()->firm_id;
        $materials  = Material::where('firm_id', $firmId)->where('status', 'active')->orderBy('material_name')->get();
        $properties = Property::where('firm_id', $firmId)->orderBy('property_name')->get();
        return compact('materials', 'properties');
    }

    public function index(Request $request)
    {
        $query = StockOutward::with(['material.materialCategory', 'property'])
            ->where('firm_id', Auth::user()->firm_id);

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('used_for', 'like', "%{$s}%")
                  ->orWhereHas('material', fn($m) => $m->where('material_name', 'like', "%{$s}%"))
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"));
            });
        }
        if ($request->filter_material) $query->where('material_id', $request->filter_material);
        if ($request->filter_property) $query->where('property_id', $request->filter_property);
        if ($request->filter_date)     $query->where('outward_date', $request->filter_date);

        $outwards   = $query->orderBy('outward_date', 'desc')->paginate(15);
        $materials  = Material::where('firm_id', Auth::user()->firm_id)->where('status', 'active')->get();
        $properties = Property::where('firm_id', Auth::user()->firm_id)->get();

        return view('admin.stock-outwards.index', compact('outwards', 'materials', 'properties'));
    }

    public function create()
    {
        return view('admin.stock-outwards.create', $this->dropdowns());
    }

    public function store(StockOutwardRequest $request)
    {
        

        $material = Material::where('firm_id', Auth::user()->firm_id)->findOrFail($request->material_id);
        $qty      = (float) $request->quantity;

        if ($material->current_stock < $qty) {
            return back()->withInput()->withErrors([
                'quantity' => 'Insufficient stock. Available: '.$material->current_stock.' '.$material->unit
            ]);
        }

        StockOutward::create([
            'firm_id'      => Auth::user()->firm_id,
            'material_id'  => $request->material_id,
            'property_id'  => $request->property_id ?: null,
            'outward_date' => $request->outward_date,
            'quantity'     => $qty,
            'used_for'     => $request->used_for,
            'remarks'      => $request->remarks,
        ]);

        $material->decrement('current_stock', $qty);

        return redirect()->route('stock-outwards.index')->with('success', 'Stock outward recorded. Material stock decreased.');
    }

    public function show(StockOutward $stockOutward)
    {
        if ($stockOutward->firm_id != Auth::user()->firm_id) abort(403);
        $stockOutward->load(['material.materialCategory', 'property']);
        return view('admin.stock-outwards.show', compact('stockOutward'));
    }

    public function edit(StockOutward $stockOutward)
    {
        if ($stockOutward->firm_id != Auth::user()->firm_id) abort(403);
        return view('admin.stock-outwards.edit', array_merge(
            ['stockOutward' => $stockOutward], $this->dropdowns()
        ));
    }

    public function update(StockOutwardRequest $request, StockOutward $stockOutward)
    {
        if ($stockOutward->firm_id != Auth::user()->firm_id) abort(403);

        

        $oldQty  = (float) $stockOutward->quantity;
        $newQty  = (float) $request->quantity;
        $material = Material::where('firm_id', Auth::user()->firm_id)->findOrFail($stockOutward->material_id);

        // Available = current_stock + old (already deducted) - new
        $availableAfter = $material->current_stock + $oldQty - $newQty;
        if ($availableAfter < 0) {
            return back()->withInput()->withErrors([
                'quantity' => 'Insufficient stock. Max allowed: '.($material->current_stock + $oldQty).' '.$material->unit
            ]);
        }

        // Recalculate stock
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

        return redirect()->route('stock-outwards.index')->with('success', 'Stock outward updated. Material stock recalculated.');
    }

    public function destroy(StockOutward $stockOutward)
    {
        if ($stockOutward->firm_id != Auth::user()->firm_id) abort(403);

        // Reverse the stock deduction
        $material = Material::find($stockOutward->material_id);
        if ($material && $material->firm_id == Auth::user()->firm_id) {
            $material->increment('current_stock', (float) $stockOutward->quantity);
        }

        $stockOutward->delete();
        return redirect()->route('stock-outwards.index')->with('success', 'Stock outward deleted. Material stock reversed.');
    }
}
