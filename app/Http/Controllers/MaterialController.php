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
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $query = Material::with('materialCategory');

        if (!$isAdmin) {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        }

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

        if ($isAdmin) {
            $categories = MaterialCategory::where('status', 'active')->get();
        } else {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $categories = MaterialCategory::where('firm_id', $firmId)->where('status', 'active')->get();
        }

        return view('admin.materials.index', compact('materials', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        if ($isAdmin) {
            $categories = MaterialCategory::where('status', 'active')->get();
        } else {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $categories = MaterialCategory::where('firm_id', $firmId)->where('status', 'active')->get();
        }
        return view('admin.materials.create', compact('categories'));
    }

    public function store(MaterialRequest $request)
    {
        $user = Auth::user();
        $firmId = $request->firm_id ?? ($user ? $user->firm_id : session('firm_id'));
        $opening = (float) ($request->opening_stock ?? 0);

        Material::create([
            'firm_id'              => $firmId,
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
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $material->firm_id != $firmId) abort(403);
        $material->load('materialCategory');
        return view('admin.materials.show', compact('material'));
    }

    public function edit(Material $material)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $material->firm_id != $firmId) abort(403);

        if ($isAdmin) {
            $categories = MaterialCategory::where('status', 'active')->get();
        } else {
            $categories = MaterialCategory::where('firm_id', $firmId)->where('status', 'active')->get();
        }
        return view('admin.materials.edit', compact('material', 'categories'));
    }

    public function update(MaterialRequest $request, Material $material)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $material->firm_id != $firmId) abort(403);

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
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $material->firm_id != $firmId) abort(403);
        $material->delete();
        return redirect()->route('materials.index')->with('success', 'Material deleted successfully.');
    }
}
