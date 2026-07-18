<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaterialRequest;

use App\Models\Material;
use App\Models\MaterialCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::with('materialCategory')
            ->where('firm_id', Auth::user()->firm_id);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('material_name', 'like', '%'.$request->search.'%')
                  ->orWhere('unit', 'like', '%'.$request->search.'%')
                  ->orWhereHas('materialCategory', fn($c) =>
                      $c->where('category_name', 'like', '%'.$request->search.'%')
                  );
            });
        }

        if ($request->filter_category) {
            $query->where('material_category_id', $request->filter_category);
        }

        $materials   = $query->latest()->paginate(15);
        $categories  = MaterialCategory::where('firm_id', Auth::user()->firm_id)->where('status', 'active')->get();

        return view('admin.materials.index', compact('materials', 'categories'));
    }

    public function create()
    {
        $categories = MaterialCategory::where('firm_id', Auth::user()->firm_id)->where('status', 'active')->get();
        return view('admin.materials.create', compact('categories'));
    }

    public function store(MaterialRequest $request)
    {
        

        $opening = (float) ($request->opening_stock ?? 0);

        Material::create([
            'firm_id'              => Auth::user()->firm_id,
            'material_category_id' => $request->material_category_id ?: null,
            'material_name'        => $request->material_name,
            'unit'                 => $request->unit,
            'opening_stock'        => $opening,
            'current_stock'        => $opening,
            'minimum_stock'        => $request->minimum_stock ?? 0,
            'status'               => $request->status,
        ]);

        return redirect()->route('materials.index')->with('success', 'Material added successfully.');
    }

    public function show(Material $material)
    {
        if ($material->firm_id != Auth::user()->firm_id) abort(403);
        $material->load('materialCategory');
        return view('admin.materials.show', compact('material'));
    }

    public function edit(Material $material)
    {
        if ($material->firm_id != Auth::user()->firm_id) abort(403);
        $categories = MaterialCategory::where('firm_id', Auth::user()->firm_id)->where('status', 'active')->get();
        return view('admin.materials.edit', compact('material', 'categories'));
    }

    public function update(MaterialRequest $request, Material $material)
    {
        if ($material->firm_id != Auth::user()->firm_id) abort(403);

        

        $material->update([
            'material_category_id' => $request->material_category_id ?: null,
            'material_name'        => $request->material_name,
            'unit'                 => $request->unit,
            'opening_stock'        => $request->opening_stock ?? 0,
            'minimum_stock'        => $request->minimum_stock ?? 0,
            'status'               => $request->status,
        ]);

        return redirect()->route('materials.index')->with('success', 'Material updated successfully.');
    }

    public function destroy(Material $material)
    {
        if ($material->firm_id != Auth::user()->firm_id) abort(403);
        $material->delete();
        return redirect()->route('materials.index')->with('success', 'Material deleted successfully.');
    }
}
