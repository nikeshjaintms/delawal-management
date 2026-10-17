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

    public function destroy(ExpenseCategory $expenseCategory, Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $categoryName = $expenseCategory->name;
        $categoryId = $expenseCategory->id;

        $expenseCategory->load('firms');
        $expenseCategory->firms()->detach();
        $expenseCategory->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Category '{$categoryName}' deleted successfully.",
                'id' => $categoryId,
                'name' => $categoryName
            ]);
        }

        return redirect()->route('expense-categories.index')->with('success', 'Expense category deleted successfully.');
    }

    public function quickStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array) ($request->firm_id ?? $firmId));

        $name = trim($request->name);
        $category = ExpenseCategory::where('name', $name)->first();
        if (!$category) {
            $category = ExpenseCategory::create([
                'name' => $name,
                'description' => $request->description ?: 'Created via Quick Add',
                'status' => 'active',
                'firm_id' => reset($firmIds) ?: 1,
            ]);
        }

        if (!empty($firmIds)) {
            $category->firms()->syncWithoutDetaching($firmIds);
        } elseif ($firmId) {
            $category->firms()->syncWithoutDetaching([$firmId]);
        }

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
            ],
            'message' => 'Expense category added successfully.'
        ]);
    }

    /**
     * Quick delete/remove an expense category via AJAX
     */
    public function quickDestroy(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');

        $category = null;
        if ($id) {
            $category = ExpenseCategory::find($id);
        } elseif ($name) {
            $category = ExpenseCategory::where('name', trim($name))->first();
        }

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Expense category not found.'
            ], 404);
        }

        $categoryName = $category->name;
        $categoryId = $category->id;

        // Check how many expenses use this category
        $expensesCount = \App\Models\Expense::where('expense_category_id', $categoryId)
            ->orWhere('expense_category', $categoryName)
            ->count();

        // Detach firms pivot
        $category->firms()->detach();

        // Nullify foreign key on expenses if any
        if ($expensesCount > 0) {
            \App\Models\Expense::where('expense_category_id', $categoryId)
                ->update(['expense_category_id' => null]);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => "Category '{$categoryName}' removed successfully.",
            'id' => $categoryId,
            'name' => $categoryName,
            'affected_expenses' => $expensesCount
        ]);
    }

    /**
     * Quick fetch list of all active expense categories with usage count
     */
    public function quickList(Request $request)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $categories = ExpenseCategory::where('status', 'active')
            ->when($firmId && (!$user || !$user->isAdmin()), function ($q) use ($firmId) {
                $q->where(function ($sub) use ($firmId) {
                    $sub->whereHas('firms', fn($f) => $f->where('firms.id', $firmId))
                        ->orWhereDoesntHave('firms');
                });
            })
            ->orderBy('name')
            ->get();

        $list = $categories->map(function ($cat) {
            $count = \App\Models\Expense::where('expense_category_id', $cat->id)
                ->orWhere('expense_category', $cat->name)
                ->count();
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'description' => $cat->description,
                'usage_count' => $count,
            ];
        });

        return response()->json([
            'success' => true,
            'categories' => $list
        ]);
    }
}
