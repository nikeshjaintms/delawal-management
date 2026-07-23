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
        $query = ExpenseCategory::with('firms')->whereHas('firms', function($q) {
            $q->where('firms.id', Auth::user()->firm_id);
        });

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
        $expenseCategory->load('firms');
        if (!$expenseCategory->firms->contains(Auth::user()->firm_id)) {
            abort(403);
        }

        return view('admin.expense-categories.show', compact('expenseCategory'));
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->load('firms');
        if (!$expenseCategory->firms->contains(Auth::user()->firm_id)) {
            abort(403);
        }

        $firms = \App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get();
        return view('admin.expense-categories.edit', compact('expenseCategory', 'firms'));
    }

    public function update(ExpenseCategoryRequest $request, ExpenseCategory $expenseCategory)
    {
        $expenseCategory->load('firms');
        if (!$expenseCategory->firms->contains(Auth::user()->firm_id)) {
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
        $expenseCategory->load('firms');
        if (!$expenseCategory->firms->contains(Auth::user()->firm_id)) {
            abort(403);
        }

        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')->with('success', 'Expense category deleted successfully.');
    }
}
