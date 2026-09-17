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
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            if ($expense->firm_id != $firmId && !$expense->firms->contains($firmId)) {
                abort(403);
            }
        }
    }

    private function dropdowns($selectedFirmId = null)
    {
        $user   = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $propQuery   = Property::with(['project.propertyMaster'])->orderBy('property_name');
        $catQuery    = ExpenseCategory::where('status', 'active')->orderBy('name');
        $projQuery   = \App\Models\Project::with('propertyMaster')->orderBy('project_name');
        $vendorQuery = \App\Models\Vendor::where('status', 'active')->orderBy('name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $propQuery->where('firm_id', $firmId);
            $projQuery->where('firm_id', $firmId);
            $vendorQuery->where(function($q) use ($firmId) {
                $q->where('firm_id', $firmId)
                  ->orWhereHas('firms', fn($f) => $f->where('firms.id', $firmId))
                  ->orWhereNull('firm_id');
            });
            $catQuery->whereHas('firms', function($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            });
        }

        return [
            'firms'      => $firms,
            'projects'   => $projQuery->get(),
            'properties' => $propQuery->get(),
            'categories' => $catQuery->get(),
            'vendors'    => $vendorQuery->get(),
        ];
    }

    public function index(Request $request)
    {
        $query = Expense::with(['firms', 'firm', 'project', 'property.propertyType', 'property.project', 'expenseCategory', 'vendor', 'purchaseOrder.vendor']);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            $query->forFirms([$firmId]);
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
                  ->orWhereHas('vendor', fn($v) => $v->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('project', fn($pr) => $pr->where('project_name', 'like', "%{$s}%"))
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                  ->orWhereHas('firms', fn($f) => $f->where('firm_name', 'like', "%{$s}%"))
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"))
                  ->orWhereHas('purchaseOrder', fn($po) => $po->where('po_number', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_project')) {
            $query->where('project_id', $request->filter_project);
        }

        if ($request->filled('filter_property')) {
            $query->where('property_id', $request->filter_property);
        }

        if ($request->filled('filter_category')) {
            $query->where('expense_category_id', $request->filter_category);
        }

        if ($request->filled('filter_vendor')) {
            $query->where('vendor_id', $request->filter_vendor);
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

        $totalAmount         = (clone $query)->sum('amount');
        $poExpensesTotal     = (clone $query)->whereNotNull('purchase_order_id')->sum('amount');
        $directExpensesTotal = (clone $query)->whereNull('purchase_order_id')->sum('amount');
        $approvedAmount      = (clone $query)->where('approval_status', 'Approved')->sum('amount');
        $pendingAmount       = (clone $query)->where('approval_status', 'Pending')->sum('amount');

        $expenses    = $query->orderBy('expense_date', 'desc')->paginate(15)->withQueryString();

        $firmsData  = $this->dropdowns($request->firm_id);
        $firms      = $firmsData['firms'];
        $projects   = $firmsData['projects'];
        $properties = $firmsData['properties'];
        $categories = $firmsData['categories'];
        $vendors    = $firmsData['vendors'];

        $selectedProject = $request->filled('filter_project') ? \App\Models\Project::find($request->filter_project) : null;

        return view('admin.expenses.index', compact(
            'expenses', 'firms', 'projects', 'properties', 'categories', 'vendors', 'totalAmount',
            'poExpensesTotal', 'directExpensesTotal', 'approvedAmount', 'pendingAmount', 'selectedProject'
        ));
    }

    public function create(Request $request)
    {
        $data = $this->dropdowns();
        $data['selectedProjectId'] = $request->input('project_id');
        return view('admin.expenses.create', $data);
    }

    public function store(ExpenseRequest $request)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $firmId;

        $categoryName = trim((string)$request->input('expense_category', ''));
        $categoryId = $request->input('expense_category_id');

        if (!empty($categoryName) && (empty($categoryId) || $categoryId === '__new__')) {
            $cat = ExpenseCategory::where('name', $categoryName)->first();
            if (!$cat) {
                $cat = ExpenseCategory::create([
                    'firm_id'     => $primaryFirmId ?: 1,
                    'name'        => $categoryName,
                    'status'      => 'active',
                    'description' => 'Created via Expense Entry',
                ]);
            }
            if (!empty($firmIds)) {
                $cat->firms()->syncWithoutDetaching($firmIds);
            } elseif ($primaryFirmId) {
                $cat->firms()->syncWithoutDetaching([$primaryFirmId]);
            }
            $categoryId = $cat->id;
            $categoryName = $cat->name;
        } elseif (!empty($categoryId) && $categoryId !== '__new__') {
            $cat = ExpenseCategory::find($categoryId);
            if ($cat) {
                $categoryName = $cat->name;
                if (!empty($firmIds)) {
                    $cat->firms()->syncWithoutDetaching($firmIds);
                } elseif ($primaryFirmId) {
                    $cat->firms()->syncWithoutDetaching([$primaryFirmId]);
                }
            }
        } else {
            $categoryId = null;
            $categoryName = null;
        }

        $billFilePath = null;
        if ($request->hasFile('bill_file')) {
            $billFilePath = $request->file('bill_file')
                ->store('expenses/bills', 'public');
        }

        $projectId = $request->project_id ?: null;
        if (!$projectId && $request->property_id) {
            $prop = Property::find($request->property_id);
            $projectId = $prop?->project_id ?: null;
        }

        $vendorId = $request->input('vendor_id');
        $paidTo   = $request->input('paid_to');

        if ($vendorId && $vendorId !== '__custom__') {
            $vendor = \App\Models\Vendor::find($vendorId);
            if ($vendor) {
                if (empty($paidTo)) {
                    $paidTo = $vendor->name;
                }
            } else {
                $vendorId = null;
            }
        } else {
            $vendorId = null;
        }

        $expense = Expense::create([
            'firm_id'             => $primaryFirmId,
            'project_id'          => $projectId,
            'property_id'         => $request->property_id ?: null,
            'vendor_id'           => $vendorId,
            'expense_date'        => $request->expense_date,
            'expense_category_id' => $categoryId ?: null,
            'expense_category'    => $categoryName,
            'expense_title'       => $request->expense_title,
            'amount'              => $request->amount,
            'payment_mode'        => $request->payment_mode,
            'paid_to'             => $paidTo,
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
        $expense->load(['firms', 'firm', 'project.propertyMaster', 'property.propertyType', 'property.project.propertyMaster', 'expenseCategory', 'vendor']);
        $this->authorise($expense);
        return view('admin.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $expense->load(['firms', 'firm', 'project', 'property.project', 'vendor']);
        $this->authorise($expense);
        return view('admin.expenses.edit', array_merge(
            [
                'expense' => $expense,
                'selectedProjectId' => $expense->project_id ?? $expense->property?->project_id,
            ],
            $this->dropdowns($expense->firm_id)
        ));
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        $expense->load(['firms', 'firm']);
        $this->authorise($expense);

        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $expense->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $expense->firm_id;

        $categoryName = trim((string)$request->input('expense_category', ''));
        $categoryId = $request->input('expense_category_id');

        if (!empty($categoryName) && (empty($categoryId) || $categoryId === '__new__')) {
            $cat = ExpenseCategory::where('name', $categoryName)->first();
            if (!$cat) {
                $cat = ExpenseCategory::create([
                    'firm_id'     => $primaryFirmId ?: 1,
                    'name'        => $categoryName,
                    'status'      => 'active',
                    'description' => 'Created via Expense Entry',
                ]);
            }
            if (!empty($firmIds)) {
                $cat->firms()->syncWithoutDetaching($firmIds);
            } elseif ($primaryFirmId) {
                $cat->firms()->syncWithoutDetaching([$primaryFirmId]);
            }
            $categoryId = $cat->id;
            $categoryName = $cat->name;
        } elseif (!empty($categoryId) && $categoryId !== '__new__') {
            $cat = ExpenseCategory::find($categoryId);
            if ($cat) {
                $categoryName = $cat->name;
                if (!empty($firmIds)) {
                    $cat->firms()->syncWithoutDetaching($firmIds);
                } elseif ($primaryFirmId) {
                    $cat->firms()->syncWithoutDetaching([$primaryFirmId]);
                }
            }
        } else {
            $categoryId = null;
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

        $projectId = $request->project_id ?: null;
        if (!$projectId && $request->property_id) {
            $prop = Property::find($request->property_id);
            $projectId = $prop?->project_id ?: null;
        }

        $vendorId = $request->input('vendor_id');
        $paidTo   = $request->input('paid_to');

        if ($vendorId && $vendorId !== '__custom__') {
            $vendor = \App\Models\Vendor::find($vendorId);
            if ($vendor) {
                if (empty($paidTo)) {
                    $paidTo = $vendor->name;
                }
            } else {
                $vendorId = null;
            }
        } else {
            $vendorId = null;
        }

        $expense->update([
            'firm_id'             => $primaryFirmId,
            'project_id'          => $projectId,
            'property_id'         => $request->property_id ?: null,
            'vendor_id'           => $vendorId,
            'expense_date'        => $request->expense_date,
            'expense_category_id' => $categoryId ?: null,
            'expense_category'    => $categoryName,
            'expense_title'       => $request->expense_title,
            'amount'              => $request->amount,
            'payment_mode'        => $request->payment_mode,
            'paid_to'             => $paidTo,
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

    public function exportPdf(Request $request)
    {
        $query = Expense::with(['firms', 'firm', 'project', 'property.propertyType', 'property.project', 'expenseCategory']);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            $query->forFirms([$firmId]);
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
                  ->orWhereHas('project', fn($pr) => $pr->where('project_name', 'like', "%{$s}%"))
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                  ->orWhereHas('firms', fn($f) => $f->where('firm_name', 'like', "%{$s}%"))
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_project')) {
            $query->where('project_id', $request->filter_project);
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

        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $totalExpensesCount = $expenses->count();
        $totalExpenseAmount = $expenses->sum('amount');
        $approvedExpenseAmount = $expenses->where('approval_status', 'Approved')->sum('amount');
        $pendingExpenseAmount = $expenses->where('approval_status', 'Pending')->sum('amount');

        return view('admin.expenses.pdf', compact(
            'expenses', 'totalExpensesCount', 'totalExpenseAmount', 'approvedExpenseAmount', 'pendingExpenseAmount'
        ));
    }

    public function downloadPdf(Expense $expense)
    {
        $this->authorise($expense);
        $expense->load(['firms', 'firm', 'project.propertyMaster', 'property.propertyType', 'property.project.propertyMaster', 'expenseCategory', 'vendor']);

        return view('admin.expenses.show-pdf', compact('expense'));
    }
}
