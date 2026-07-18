<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;

use App\Models\Expense;
use App\Models\Property;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    const PAYMENT_MODES    = ['Cash', 'Bank Transfer', 'UPI', 'Cheque', 'Other'];
    const APPROVAL_STATUSES = ['Pending', 'Approved', 'Rejected'];

    // ---------------------------------------------------------------
    // Shared dropdown data
    // ---------------------------------------------------------------
    private function dropdowns()
    {
        $firmId = Auth::user()->firm_id;

        $properties = Property::where('firm_id', $firmId)
            ->orderBy('property_name')->get();

        $categories = ExpenseCategory::where('firm_id', $firmId)
            ->where('status', 'active')
            ->orderBy('name')->get();

        return compact('properties', 'categories');
    }

    // ---------------------------------------------------------------
    // INDEX
    // ---------------------------------------------------------------
    public function index(Request $request)
    {
        $firmId = Auth::user()->firm_id;

        $query = Expense::with(['property', 'expenseCategory'])
            ->where('firm_id', $firmId);

        // --- Filters ---
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('expense_title', 'like', "%{$s}%")
                  ->orWhere('expense_category', 'like', "%{$s}%")
                  ->orWhere('paid_to', 'like', "%{$s}%")
                  ->orWhere('bill_no', 'like', "%{$s}%")
                  ->orWhereHas('property', fn($p) =>
                      $p->where('property_name', 'like', "%{$s}%")
                  );
            });
        }

        if ($request->filled('filter_property')) {
            $query->where('property_id', $request->filter_property);
        }

        if ($request->filled('filter_category')) {
            $query->where('expense_category_id', $request->filter_category);
        }

        if ($request->filled('filter_mode')) {
            $query->where('payment_mode', $request->filter_mode);
        }

        if ($request->filled('filter_status')) {
            $query->where('approval_status', $request->filter_status);
        }

        if ($request->filled('filter_date')) {
            $query->where('expense_date', $request->filter_date);
        }

        // Total for current filter (before pagination)
        $totalAmount = (clone $query)->sum('amount');

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(15);

        $properties = Property::where('firm_id', $firmId)->orderBy('property_name')->get();
        $categories = ExpenseCategory::where('firm_id', $firmId)->where('status', 'active')->orderBy('name')->get();

        return view('admin.expenses.index', compact(
            'expenses', 'properties', 'categories', 'totalAmount'
        ));
    }

    // ---------------------------------------------------------------
    // CREATE / STORE
    // ---------------------------------------------------------------
    public function create()
    {
        return view('admin.expenses.create', $this->dropdowns());
    }

    public function store(ExpenseRequest $request)
    {
        

        // Resolve category name from ID
        $categoryName = null;
        if ($request->expense_category_id) {
            $cat = ExpenseCategory::find($request->expense_category_id);
            $categoryName = $cat?->name;
        }

        // Handle bill file upload
        $billFilePath = null;
        if ($request->hasFile('bill_file')) {
            $billFilePath = $request->file('bill_file')
                ->store('expenses/bills', 'public');
        }

        Expense::create([
            'firm_id'             => Auth::user()->firm_id,
            'property_id'         => $request->property_id ?: null,
            'expense_date'        => $request->expense_date,
            'expense_category_id' => $request->expense_category_id ?: null,
            'expense_category'    => $categoryName,
            'expense_title'       => $request->expense_title,
            'amount'              => $request->amount,
            'payment_mode'        => $request->payment_mode,
            'paid_to'             => $request->paid_to,
            'bill_no'             => $request->bill_no,
            'bill_file'           => $billFilePath,
            'approval_status'     => $request->approval_status,
            'remarks'             => $request->remarks,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense added successfully.');
    }

    // ---------------------------------------------------------------
    // SHOW
    // ---------------------------------------------------------------
    public function show(Expense $expense)
    {
        $this->authorise($expense);
        $expense->load(['property.propertyType', 'expenseCategory']);
        return view('admin.expenses.show', compact('expense'));
    }

    // ---------------------------------------------------------------
    // EDIT / UPDATE
    // ---------------------------------------------------------------
    public function edit(Expense $expense)
    {
        $this->authorise($expense);
        return view('admin.expenses.edit', array_merge(
            ['expense' => $expense],
            $this->dropdowns()
        ));
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        $this->authorise($expense);

        

        $categoryName = $expense->expense_category;
        if ($request->expense_category_id) {
            $cat = ExpenseCategory::find($request->expense_category_id);
            $categoryName = $cat?->name;
        } elseif (!$request->expense_category_id) {
            $categoryName = null;
        }

        // Handle bill file upload — replace if new file provided
        $billFilePath = $expense->bill_file;
        if ($request->hasFile('bill_file')) {
            // Delete old file
            if ($expense->bill_file) {
                Storage::disk('public')->delete($expense->bill_file);
            }
            $billFilePath = $request->file('bill_file')
                ->store('expenses/bills', 'public');
        }

        $expense->update([
            'property_id'         => $request->property_id ?: null,
            'expense_date'        => $request->expense_date,
            'expense_category_id' => $request->expense_category_id ?: null,
            'expense_category'    => $categoryName,
            'expense_title'       => $request->expense_title,
            'amount'              => $request->amount,
            'payment_mode'        => $request->payment_mode,
            'paid_to'             => $request->paid_to,
            'bill_no'             => $request->bill_no,
            'bill_file'           => $billFilePath,
            'approval_status'     => $request->approval_status,
            'remarks'             => $request->remarks,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    // ---------------------------------------------------------------
    // DESTROY
    // ---------------------------------------------------------------
    public function destroy(Expense $expense)
    {
        $this->authorise($expense);

        // Delete attached bill file
        if ($expense->bill_file) {
            Storage::disk('public')->delete($expense->bill_file);
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    // ---------------------------------------------------------------
    // Helper
    // ---------------------------------------------------------------
    private function authorise(Expense $expense): void
    {
        if ($expense->firm_id !== Auth::user()->firm_id) {
            abort(403);
        }
    }
}
