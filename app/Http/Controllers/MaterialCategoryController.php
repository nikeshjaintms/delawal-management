<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaterialCategoryRequest;

use App\Models\MaterialCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = MaterialCategory::where('firm_id', Auth::user()->firm_id);
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('category_name', 'like', '%'.$request->search.'%')
                  ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }
        $categories = $query->latest()->paginate(15);
        return view('admin.material-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.material-categories.create');
    }

    public function store(MaterialCategoryRequest $request)
    {
        
        MaterialCategory::create([
            'firm_id'       => Auth::user()->firm_id,
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'status'        => $request->status,
        ]);
        return redirect()->route('material-categories.index')->with('success', 'Material category added successfully.');
    }

    public function show(MaterialCategory $materialCategory)
    {
        if ($materialCategory->firm_id != Auth::user()->firm_id) abort(403);
        return view('admin.material-categories.show', compact('materialCategory'));
    }

    public function edit(MaterialCategory $materialCategory)
    {
        if ($materialCategory->firm_id != Auth::user()->firm_id) abort(403);
        return view('admin.material-categories.edit', compact('materialCategory'));
    }

    public function update(MaterialCategoryRequest $request, MaterialCategory $materialCategory)
    {
        if ($materialCategory->firm_id != Auth::user()->firm_id) abort(403);
        
        $materialCategory->update([
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'status'        => $request->status,
        ]);
        return redirect()->route('material-categories.index')->with('success', 'Material category updated successfully.');
    }

    public function destroy(MaterialCategory $materialCategory)
    {
        if ($materialCategory->firm_id != Auth::user()->firm_id) abort(403);
        $materialCategory->delete();
        return redirect()->route('material-categories.index')->with('success', 'Material category deleted successfully.');
    }
}
