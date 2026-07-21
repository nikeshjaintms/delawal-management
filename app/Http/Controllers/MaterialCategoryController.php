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
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $query = MaterialCategory::query();

        if (!$isAdmin) {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        }

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
        $user = Auth::user();
        $firmId = $request->firm_id ?? ($user ? $user->firm_id : session('firm_id'));

        MaterialCategory::create([
            'firm_id'       => $firmId,
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'status'        => $request->status,
        ]);
        return redirect()->route('material-categories.index')->with('success', 'Material category added successfully.');
    }

    public function show(MaterialCategory $materialCategory)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $materialCategory->firm_id != $firmId) abort(403);
        return view('admin.material-categories.show', compact('materialCategory'));
    }

    public function edit(MaterialCategory $materialCategory)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $materialCategory->firm_id != $firmId) abort(403);
        return view('admin.material-categories.edit', compact('materialCategory'));
    }

    public function update(MaterialCategoryRequest $request, MaterialCategory $materialCategory)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $materialCategory->firm_id != $firmId) abort(403);
        
        $materialCategory->update([
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'status'        => $request->status,
        ]);
        return redirect()->route('material-categories.index')->with('success', 'Material category updated successfully.');
    }

    public function destroy(MaterialCategory $materialCategory)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $materialCategory->firm_id != $firmId) abort(403);
        $materialCategory->delete();
        return redirect()->route('material-categories.index')->with('success', 'Material category deleted successfully.');
    }
}
