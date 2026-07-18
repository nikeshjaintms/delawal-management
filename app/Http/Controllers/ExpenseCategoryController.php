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
        $query = ExpenseCategory::where('firm_id', Auth::user()->firm_id);

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
        return view('admin.expense-categories.create');
    }

    public function store(ExpenseCategoryRequest $request)
    {
        

        ExpenseCategory::create([
            'firm_id'     => Auth::user()->firm_id,
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
        ]);

        return redirect()->route('expense-categories.index')->with('success', 'Expense category added successfully.');
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        return view('admin.expense-categories.show', compact('expenseCategory'));
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        return view('admin.expense-categories.edit', compact('expenseCategory'));
    }

    public function update(ExpenseCategoryRequest $request, ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        

        $expenseCategory->update([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
        ]);

        return redirect()->route('expense-categories.index')->with('success', 'Expense category updated successfully.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')->with('success', 'Expense category deleted successfully.');
    }
}
