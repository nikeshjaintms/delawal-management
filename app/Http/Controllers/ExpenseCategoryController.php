<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseCategoryRequest;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $query = ExpenseCategory::with('firms');
        
        if (!$isAdmin) {
            $query->whereHas('firms', function($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            });
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%');
            });
        }

        $expenseCategories = $query->latest()->paginate(10);

        return view('admin.expense-categories.index', compact('expenseCategories'));
    }

    public function create()
    {
        $firms = \App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get();
        return view('admin.expense-categories.create', compact('firms'));
    }

    public function store(ExpenseCategoryRequest $request)
    {
        $expenseCategory = ExpenseCategory::create([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
        ]);
        $expenseCategory->firms()->attach($request->firm_ids);

        return redirect()->route('expense-categories.index')->with('success', 'Expense category added successfully.');
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $expenseCategory->load('firms');
        if (!$isAdmin && !$expenseCategory->firms->contains($firmId)) {
            abort(403);
        }

        return view('admin.expense-categories.show', compact('expenseCategory'));
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $expenseCategory->load('firms');
        if (!$isAdmin && !$expenseCategory->firms->contains($firmId)) {
            abort(403);
        }

        $firms = \App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get();
        return view('admin.expense-categories.edit', compact('expenseCategory', 'firms'));
    }

    public function update(ExpenseCategoryRequest $request, ExpenseCategory $expenseCategory)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $expenseCategory->load('firms');
        if (!$isAdmin && !$expenseCategory->firms->contains($firmId)) {
            abort(403);
        }

        $expenseCategory->update([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
        ]);
        $expenseCategory->firms()->sync($request->firm_ids);

        return redirect()->route('expense-categories.index')->with('success', 'Expense category updated successfully.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $expenseCategory->load('firms');
        if (!$isAdmin && !$expenseCategory->firms->contains($firmId)) {
            abort(403);
        }

        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')->with('success', 'Expense category deleted successfully.');
    }
}
