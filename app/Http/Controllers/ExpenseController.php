<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use App\Models\Property;
use App\Models\ExpenseCategory;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    const PAYMENT_MODES     = ['Cash', 'Bank Transfer', 'UPI', 'Cheque', 'Other'];
    const APPROVAL_STATUSES = ['Pending', 'Approved', 'Rejected'];

    private function authorise(Expense $expense): void
    {
        $user = Auth::user();
        if ($user && !$user->isAdmin()) {
            $userFirmId = $user->firm_id;
            if ($expense->firm_id != $userFirmId && !$expense->firms->contains($userFirmId)) {
                abort(403);
            }
        }
    }

    private function dropdowns($selectedFirmId = null)
    {
        $user   = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $propQuery = Property::orderBy('property_name');
        $catQuery  = ExpenseCategory::where('status', 'active')->orderBy('name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $propQuery->where('firm_id', $firmId);
            $catQuery->where('firm_id', $firmId);
        }

        return [
            'firms'      => $firms,
            'properties' => $propQuery->get(),
            'categories' => $catQuery->get(),
        ];
    }

    public function index(Request $request)
    {
        $query = Expense::with(['firms', 'firm', 'property', 'expenseCategory']);

        if (!Auth::user()->isAdmin()) {
            $query->forFirms([Auth::user()->firm_id]);
        } elseif ($request->filled('firm_ids') || $request->filled('firm_id')) {
            $firmIds = $request->input('firm_ids', (array)$request->firm_id);
            $query->forFirms($firmIds);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('expense_title', 'like', "%{$s}%")
                  ->orWhere('expense_category', 'like', "%{$s}%")
                  ->orWhere('paid_to', 'like', "%{$s}%")
                  ->orWhere('bill_no', 'like', "%{$s}%")
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                  ->orWhereHas('firms', fn($f) => $f->where('firm_name', 'like', "%{$s}%"))
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"));
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

        $totalAmount = (clone $query)->sum('amount');
        $expenses    = $query->orderBy('expense_date', 'desc')->paginate(15)->withQueryString();

        $firmsData  = $this->dropdowns($request->firm_id);
        $firms      = $firmsData['firms'];
        $properties = $firmsData['properties'];
        $categories = $firmsData['categories'];

        return view('admin.expenses.index', compact(
            'expenses', 'firms', 'properties', 'categories', 'totalAmount'
        ));
    }

    public function create()
    {
        return view('admin.expenses.create', $this->dropdowns());
    }

    public function store(ExpenseRequest $request)
    {
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? Auth::user()->firm_id));
        $primaryFirmId = reset($firmIds) ?: Auth::user()->firm_id;

        $categoryName = null;
        if ($request->expense_category_id) {
            $cat = ExpenseCategory::find($request->expense_category_id);
            $categoryName = $cat?->name;
        }

        $billFilePath = null;
        if ($request->hasFile('bill_file')) {
            $billFilePath = $request->file('bill_file')
                ->store('expenses/bills', 'public');
        }

        $expense = Expense::create([
            'firm_id'             => $primaryFirmId,
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

        $expense->syncFirms($firmIds);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense added successfully.');
    }

    public function show(Expense $expense)
    {
        $expense->load(['firms', 'firm', 'property.propertyType', 'expenseCategory']);
        $this->authorise($expense);
        return view('admin.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $expense->load(['firms', 'firm']);
        $this->authorise($expense);
        return view('admin.expenses.edit', array_merge(
            ['expense' => $expense],
            $this->dropdowns($expense->firm_id)
        ));
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        $expense->load(['firms', 'firm']);
        $this->authorise($expense);

        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $expense->firm_id ?? Auth::user()->firm_id));
        $primaryFirmId = reset($firmIds) ?: $expense->firm_id;

        $categoryName = $expense->expense_category;
        if ($request->expense_category_id) {
            $cat = ExpenseCategory::find($request->expense_category_id);
            $categoryName = $cat?->name;
        } elseif (!$request->expense_category_id) {
            $categoryName = null;
        }

        $billFilePath = $expense->bill_file;
        if ($request->hasFile('bill_file')) {
            if ($expense->bill_file) {
                Storage::disk('public')->delete($expense->bill_file);
            }
            $billFilePath = $request->file('bill_file')
                ->store('expenses/bills', 'public');
        }

        $expense->update([
            'firm_id'             => $primaryFirmId,
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

        $expense->syncFirms($firmIds);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorise($expense);

        if ($expense->bill_file) {
            Storage::disk('public')->delete($expense->bill_file);
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
