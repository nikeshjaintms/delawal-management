<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\BrokerCommission;
use App\Models\Contractor;
use App\Models\ContractorPayment;
use App\Models\Firm;
use App\Models\PaymentMode;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyMaster;
use App\Models\PropertyMasterPayment;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    const PAYMENT_MODES = Expense::PAYMENT_MODES;
    const APPROVAL_STATUSES = Expense::APPROVAL_STATUSES;

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
        $user = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $propQuery = Property::with(['project.propertyMaster'])->orderBy('property_name');
        $catQuery = ExpenseCategory::where('status', 'active')->orderBy('name');
        $projQuery = Project::with('propertyMaster')->orderBy('project_name');
        $vendorQuery = Vendor::where('status', 'active')->orderBy('name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $propQuery->where('firm_id', $firmId);
            $projQuery->where('firm_id', $firmId);
            $vendorQuery->where(function ($q) use ($firmId) {
                $q
                    ->where('firm_id', $firmId)
                    ->orWhereHas('firms', fn($f) => $f->where('firms.id', $firmId))
                    ->orWhereNull('firm_id');
            });
            $catQuery->whereHas('firms', function ($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            });
        }

        $paymentModes = PaymentMode::where('status', 'active')
            ->when($firmId, function ($q) use ($firmId) {
                $q->where(function ($sub) use ($firmId) {
                    $sub
                        ->whereHas('firms', fn($f) => $f->where('firms.id', $firmId))
                        ->orWhereDoesntHave('firms');
                });
            })
            ->orderBy('name')
            ->get();

        $paymentModes = PaymentMode::where('status', 'active')
            ->when($firmId, function ($q) use ($firmId) {
                $q->where(function ($sub) use ($firmId) {
                    $sub
                        ->whereHas('firms', fn($f) => $f->where('firms.id', $firmId))
                        ->orWhereDoesntHave('firms');
                });
            })
            ->orderBy('name')
            ->get();

        if ($paymentModes->isEmpty()) {
            $paymentModes = PaymentMode::where('status', 'active')->orderBy('name')->get();
        }

        $rentalsQuery = \App\Models\Rental::with(['property', 'tenant', 'firm'])
            ->orderByDesc('id');
        $tenantsQuery = \App\Models\Tenant::with('firm')->where('status', 'active')->orderBy('name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $rentalsQuery->where('firm_id', $firmId);
            $tenantsQuery->where('firm_id', $firmId);
        }

        return [
            'firms' => $firms,
            'projects' => $projQuery->get(),
            'properties' => $propQuery->get(),
            'categories' => $catQuery->get(),
            'vendors' => $vendorQuery->get(),
            'paymentModes' => $paymentModes,
            'rentals' => $rentalsQuery->get(),
            'tenants' => $tenantsQuery->get(),
            'rentalCategories' => Expense::RENTAL_CATEGORIES,
            'recoveryStatuses' => Expense::RECOVERY_STATUSES,
        ];
    }

    public function propertyExpenses(Request $request)
    {
        $request->merge(['type' => 'Property']);
        return $this->index($request);
    }

    public function generalExpenses(Request $request)
    {
        $request->merge(['type' => 'General']);
        return $this->index($request);
    }

    public function rentalExpenses(Request $request)
    {
        $request->merge(['type' => 'Rental']);
        return $this->index($request);
    }

    public function personalExpenses(Request $request)
    {
        $request->merge(['type' => 'Personal']);
        return $this->index($request);
    }

    public function index(Request $request)
    {
        $query = Expense::with([
            'firms', 'firm', 'project', 'properties.propertyType',
            'property.propertyType', 'property.project', 'expenseCategory',
            'vendor', 'purchaseOrder.vendor', 'rental.property', 'tenant'
        ]);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            $query->forFirms([$firmId]);
        } elseif ($request->filled('firm_ids') || $request->filled('firm_id')) {
            $firmIds = $request->input('firm_ids', (array) $request->firm_id);
            $query->forFirms($firmIds);
        }

        $activeType = $request->input('type', $request->input('expense_type', $request->input('filter_type')));
        if ($activeType) {
            if ($activeType === 'Property') {
                $query->where(function ($q) {
                    $q->where('expense_type', 'Property')
                      ->orWhereNotNull('property_id')
                      ->orWhereHas('properties');
                });
            } elseif ($activeType === 'Project') {
                $query->where(function ($q) {
                    $q->where('expense_type', 'Project')
                      ->orWhereNotNull('project_id')
                      ->orWhereNotNull('purchase_order_id');
                });
            } elseif ($activeType === 'General') {
                $query->where(function ($q) {
                    $q->whereIn('expense_type', ['General', 'Office'])
                      ->orWhere(function ($sub) {
                          $sub->whereNull('expense_type')
                              ->whereNull('project_id')
                              ->whereNull('property_id');
                      });
                });
            } elseif ($activeType === 'Rental') {
                $query->where(function ($q) {
                    $q->where('expense_type', 'Rental')
                      ->orWhereNotNull('rental_id')
                      ->orWhereNotNull('tenant_id')
                      ->orWhere('expense_category', 'like', '%Rental%')
                      ->orWhere('expense_category', 'like', '%Rent%')
                      ->orWhere('expense_category', 'like', '%Tenant%');
                });
            } elseif ($activeType === 'Personal') {
                $query->where(function ($q) {
                    $q->where('expense_type', 'Personal')
                      ->orWhere('expense_category', 'like', '%Personal%')
                      ->orWhere('expense_category', 'like', '%Drawing%');
                });
            } else {
                $query->where('expense_type', $activeType);
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q
                    ->where('expense_title', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")
                    ->orWhere('expense_category', 'like', "%{$s}%")
                    ->orWhere('expense_subcategory', 'like', "%{$s}%")
                    ->orWhere('expense_type', 'like', "%{$s}%")
                    ->orWhere('paid_to', 'like', "%{$s}%")
                    ->orWhere('bill_no', 'like', "%{$s}%")
                    ->orWhere('reference_no', 'like', "%{$s}%")
                    ->orWhere('payment_account', 'like', "%{$s}%")
                    ->orWhere('notes', 'like', "%{$s}%")
                    ->orWhereHas('vendor', fn($v) => $v->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('tenant', fn($t) => $t->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"))
                    ->orWhereHas('rental', fn($r) => $r->where('agreement_no', 'like', "%{$s}%")->orWhere('tenant_name', 'like', "%{$s}%"))
                    ->orWhereHas('project', fn($pr) => $pr->where('project_name', 'like', "%{$s}%"))
                    ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                    ->orWhereHas('properties', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                    ->orWhereHas('firms', fn($f) => $f->where('firm_name', 'like', "%{$s}%"))
                    ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"))
                    ->orWhereHas('purchaseOrder', fn($po) => $po->where('po_number', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_project')) {
            $query->where('project_id', $request->filter_project);
        }

        if ($request->filled('filter_property')) {
            $propId = $request->filter_property;
            $query->where(function ($q) use ($propId) {
                $q->where('property_id', $propId)
                  ->orWhereHas('properties', fn($p) => $p->where('properties.id', $propId));
            });
        }

        if ($request->filled('filter_rental')) {
            $query->where('rental_id', $request->filter_rental);
        }

        if ($request->filled('filter_tenant')) {
            $query->where('tenant_id', $request->filter_tenant);
        }

        if ($request->filled('filter_recovery_status')) {
            $query->where('recovery_status', $request->filter_recovery_status);
        }

        if ($request->filled('filter_recoverable')) {
            $query->where('is_tenant_recoverable', (bool) $request->filter_recoverable);
        }

        if ($request->filled('filter_category')) {
            $cat = $request->filter_category;
            if (is_numeric($cat)) {
                $query->where('expense_category_id', $cat);
            } else {
                $query->where(function($q) use ($cat) {
                    $q->where('expense_category', $cat)
                      ->orWhere('expense_subcategory', $cat);
                });
            }
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

        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $totalAmount = (clone $query)->sum('amount');
        $poExpensesTotal = (clone $query)->whereNotNull('purchase_order_id')->sum('amount');
        $directExpensesTotal = (clone $query)->whereNull('purchase_order_id')->sum('amount');
        $approvedAmount = (clone $query)->where('approval_status', 'Approved')->sum('amount');
        $pendingAmount = (clone $query)->where('approval_status', 'Pending')->sum('amount');

        // Rental-specific KPIs
        $recoverableTotal = (clone $query)->where('is_tenant_recoverable', true)->sum('recovery_amount');
        $recoveredTotal = (clone $query)->where('is_tenant_recoverable', true)->where('recovery_status', 'Recovered')->sum('recovery_amount');
        $pendingRecoveryTotal = (clone $query)->where('is_tenant_recoverable', true)->where('recovery_status', 'Pending')->sum('recovery_amount');

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(15)->withQueryString();

        $dropdownData = $this->dropdowns($request->firm_id);
        $firms = $dropdownData['firms'];
        $projects = $dropdownData['projects'];
        $properties = $dropdownData['properties'];
        $categories = $dropdownData['categories'];
        $vendors = $dropdownData['vendors'];
        $paymentModes = $dropdownData['paymentModes'];
        $rentals = $dropdownData['rentals'];
        $tenants = $dropdownData['tenants'];
        $rentalCategories = $dropdownData['rentalCategories'];
        $recoveryStatuses = $dropdownData['recoveryStatuses'];

        $selectedProject = $request->filled('filter_project') ? Project::find($request->filter_project) : null;
        $selectedRental = $request->filled('filter_rental') ? \App\Models\Rental::with(['property', 'tenant'])->find($request->filter_rental) : null;
        $selectedTenant = $request->filled('filter_tenant') ? \App\Models\Tenant::find($request->filter_tenant) : null;

        return view('admin.expenses.index', compact(
            'expenses', 'firms', 'projects', 'properties', 'categories', 'vendors', 'paymentModes',
            'rentals', 'tenants', 'rentalCategories', 'recoveryStatuses',
            'totalAmount', 'poExpensesTotal', 'directExpensesTotal',
            'approvedAmount', 'pendingAmount', 'recoverableTotal', 'recoveredTotal', 'pendingRecoveryTotal',
            'selectedProject', 'selectedRental', 'selectedTenant', 'activeType'
        ));
    }

    public function create(Request $request)
    {
        $data = $this->dropdowns();
        $data['selectedType'] = $request->input('type', $request->input('expense_type', 'Property'));
        $data['selectedProjectId'] = $request->input('project_id');
        $data['selectedPropertyId'] = $request->input('property_id');
        $data['selectedRentalId'] = $request->input('rental_id');
        $data['selectedTenantId'] = $request->input('tenant_id');
        $data['selectedPropertyIds'] = (array) ($request->input('property_ids') ?: ($request->input('property_id') ? [$request->input('property_id')] : []));
        
        // If rental_id is given directly, pre-fetch rental data
        if ($data['selectedRentalId']) {
            $rental = \App\Models\Rental::with(['property', 'tenant', 'firm'])->find($data['selectedRentalId']);
            if ($rental) {
                $data['selectedRental'] = $rental;
                $data['selectedPropertyId'] = $rental->property_id;
                $data['selectedTenantId'] = $rental->tenant_id;
                if (!in_array($rental->property_id, $data['selectedPropertyIds'])) {
                    $data['selectedPropertyIds'][] = $rental->property_id;
                }
            }
        }

        return view('admin.expenses.create', $data);
    }

    public function store(ExpenseRequest $request)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array) ($request->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $firmId;

        $categoryName = trim((string) $request->input('expense_category', ''));
        $categoryId = $request->input('expense_category_id');

        if (!empty($categoryName) && (empty($categoryId) || $categoryId === '__new__')) {
            $cat = ExpenseCategory::where('name', $categoryName)->first();
            if (!$cat) {
                $cat = ExpenseCategory::create([
                    'firm_id' => $primaryFirmId ?: 1,
                    'name' => $categoryName,
                    'status' => 'active',
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
            $categoryName = !empty($categoryName) ? $categoryName : null;
        }

        $billFilePath = null;
        if ($request->hasFile('bill_file')) {
            $billFilePath = $request->file('bill_file')->store('expenses/bills', 'public');
        }

        $rentalId = $request->input('rental_id') ?: null;
        $tenantId = $request->input('tenant_id') ?: null;

        $propertyIds = $request->input('property_ids', (array) ($request->property_id ? [$request->property_id] : []));
        $propertyIds = array_filter((array) $propertyIds);
        $primaryPropertyId = reset($propertyIds) ?: null;

        // If rental is selected, link and ensure property/tenant/firm are synced
        if ($rentalId) {
            $rental = \App\Models\Rental::find($rentalId);
            if ($rental) {
                if (!$primaryPropertyId && $rental->property_id) {
                    $primaryPropertyId = $rental->property_id;
                    $propertyIds[] = $rental->property_id;
                }
                if (!$tenantId && $rental->tenant_id) {
                    $tenantId = $rental->tenant_id;
                }
                if (!$primaryFirmId && $rental->firm_id) {
                    $primaryFirmId = $rental->firm_id;
                    $firmIds[] = $rental->firm_id;
                }
            }
        }

        $projectId = $request->project_id ?: null;
        if (!$projectId && $primaryPropertyId) {
            $prop = Property::find($primaryPropertyId);
            $projectId = $prop?->project_id ?: null;
        }

        $vendorId = $request->input('vendor_id');
        $paidTo = $request->input('paid_to');

        if ($vendorId && $vendorId !== '__custom__') {
            $vendor = Vendor::find($vendorId);
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

        $expenseTitle = $request->input('expense_title');
        if (empty($expenseTitle)) {
            if ($request->filled('description')) {
                $expenseTitle = Str::limit($request->input('description'), 60);
            } elseif (!empty($categoryName)) {
                $expenseTitle = $categoryName . ' Expense';
            } elseif ($request->filled('expense_type')) {
                $expenseTitle = $request->input('expense_type') . ' Expense';
            } else {
                $expenseTitle = 'Expense - ' . ($request->expense_date ?? date('Y-m-d'));
            }
        }

        $isRecoverable = $request->boolean('is_tenant_recoverable');
        $recoveryAmount = $isRecoverable ? (float) ($request->input('recovery_amount') ?: $request->input('amount')) : 0.00;
        $recoveryStatus = $isRecoverable ? ($request->input('recovery_status') ?: 'Pending') : 'Not Applicable';

        $expense = Expense::create([
            'firm_id' => $primaryFirmId,
            'project_id' => $projectId,
            'property_id' => $primaryPropertyId,
            'rental_id' => $rentalId,
            'tenant_id' => $tenantId,
            'vendor_id' => $vendorId,
            'expense_date' => $request->expense_date,
            'expense_category_id' => $categoryId ?: null,
            'expense_category' => $categoryName,
            'expense_subcategory' => $request->expense_subcategory ?: null,
            'expense_type' => $request->expense_type ?: null,
            'expense_title' => $expenseTitle,
            'description' => $request->description ?: null,
            'amount' => $request->amount,
            'payment_mode' => $request->payment_mode ?: null,
            'reference_no' => $request->reference_no ?: null,
            'payment_account' => $request->payment_account ?: null,
            'paid_to' => $paidTo ?: null,
            'bill_no' => $request->bill_no ?: null,
            'bill_file' => $billFilePath,
            'is_tenant_recoverable' => $isRecoverable,
            'recovery_amount' => $recoveryAmount,
            'recovery_status' => $recoveryStatus,
            'approval_status' => $request->approval_status ?? 'Pending',
            'remarks' => $request->remarks ?: null,
            'notes' => $request->notes ?: null,
        ]);

        if (!empty($propertyIds)) {
            $expense->syncProperties($propertyIds);
        }

        if (!empty($firmIds)) {
            $expense->syncFirms($firmIds);
        }

        $expType = $expense->expense_type ?: $request->expense_type;
        if ($expType === 'General' || $expType === 'Office') {
            return redirect()->route('expenses.general')->with('success', 'General expense added successfully.');
        } elseif ($expType === 'Rental') {
            return redirect()->route('expenses.rental')->with('success', 'Rental expense added successfully.');
        } elseif ($expType === 'Personal') {
            return redirect()->route('expenses.personal')->with('success', 'Personal expense added successfully.');
        } elseif ($expType === 'Property') {
            return redirect()->route('expenses.property')->with('success', 'Property expense added successfully.');
        } else {
            return redirect()
                ->route('expenses.project-wise', $projectId ? ['project_id' => $projectId] : [])
                ->with('success', 'Project expense added successfully.');
        }
    }

    public function show(Expense $expense)
    {
        $expense->load([
            'firms', 'firm', 'project.propertyMaster',
            'properties.propertyType', 'properties.project.propertyMaster',
            'property.propertyType', 'property.project.propertyMaster',
            'rental.tenant', 'rental.property', 'tenant',
            'expenseCategory', 'vendor'
        ]);
        $this->authorise($expense);
        return view('admin.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $expense->load(['firms', 'firm', 'project', 'properties', 'property.project', 'vendor', 'rental.tenant', 'tenant']);
        $this->authorise($expense);
        $selectedType = $expense->expense_type ?: ($expense->rental_id ? 'Rental' : ($expense->project_id || $expense->property_id ? 'Property' : 'General'));
        return view('admin.expenses.edit', array_merge(
            [
                'expense' => $expense,
                'selectedType' => $selectedType,
                'selectedProjectId' => $expense->project_id ?? $expense->property?->project_id ?? $expense->properties->first()?->project_id,
                'selectedPropertyId' => $expense->property_id,
                'selectedRentalId' => $expense->rental_id,
                'selectedTenantId' => $expense->tenant_id,
                'selectedPropertyIds' => $expense->properties->pluck('id')->toArray(),
            ],
            $this->dropdowns($expense->firm_id)
        ));
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        $expense->load(['firms', 'firm', 'properties']);
        $this->authorise($expense);

        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array) ($request->firm_id ?? $expense->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $expense->firm_id;

        $categoryName = trim((string) $request->input('expense_category', ''));
        $categoryId = $request->input('expense_category_id');

        if (!empty($categoryName) && (empty($categoryId) || $categoryId === '__new__')) {
            $cat = ExpenseCategory::where('name', $categoryName)->first();
            if (!$cat) {
                $cat = ExpenseCategory::create([
                    'firm_id' => $primaryFirmId ?: 1,
                    'name' => $categoryName,
                    'status' => 'active',
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
            $categoryName = !empty($categoryName) ? $categoryName : null;
        }

        $billFilePath = $expense->bill_file;
        if ($request->hasFile('bill_file')) {
            if ($expense->bill_file) {
                Storage::disk('public')->delete($expense->bill_file);
            }
            $billFilePath = $request->file('bill_file')->store('expenses/bills', 'public');
        }

        $rentalId = $request->input('rental_id') ?: null;
        $tenantId = $request->input('tenant_id') ?: null;

        $propertyIds = $request->input('property_ids', (array) ($request->property_id ? [$request->property_id] : []));
        $propertyIds = array_filter((array) $propertyIds);
        $primaryPropertyId = reset($propertyIds) ?: null;

        if ($rentalId) {
            $rental = \App\Models\Rental::find($rentalId);
            if ($rental) {
                if (!$primaryPropertyId && $rental->property_id) {
                    $primaryPropertyId = $rental->property_id;
                    $propertyIds[] = $rental->property_id;
                }
                if (!$tenantId && $rental->tenant_id) {
                    $tenantId = $rental->tenant_id;
                }
                if (!$primaryFirmId && $rental->firm_id) {
                    $primaryFirmId = $rental->firm_id;
                    $firmIds[] = $rental->firm_id;
                }
            }
        }

        $projectId = $request->project_id ?: null;
        if (!$projectId && $primaryPropertyId) {
            $prop = Property::find($primaryPropertyId);
            $projectId = $prop?->project_id ?: null;
        }

        $vendorId = $request->input('vendor_id');
        $paidTo = $request->input('paid_to');

        if ($vendorId && $vendorId !== '__custom__') {
            $vendor = Vendor::find($vendorId);
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

        $expenseTitle = $request->input('expense_title');
        if (empty($expenseTitle)) {
            if ($request->filled('description')) {
                $expenseTitle = Str::limit($request->input('description'), 60);
            } elseif (!empty($categoryName)) {
                $expenseTitle = $categoryName . ' Expense';
            } elseif ($request->filled('expense_type')) {
                $expenseTitle = $request->input('expense_type') . ' Expense';
            } else {
                $expenseTitle = 'Expense - ' . ($request->expense_date ?? date('Y-m-d'));
            }
        }

        $isRecoverable = $request->boolean('is_tenant_recoverable');
        $recoveryAmount = $isRecoverable ? (float) ($request->input('recovery_amount') ?: $request->input('amount')) : 0.00;
        $recoveryStatus = $isRecoverable ? ($request->input('recovery_status') ?: 'Pending') : 'Not Applicable';

        $expense->update([
            'firm_id' => $primaryFirmId,
            'project_id' => $projectId,
            'property_id' => $primaryPropertyId,
            'rental_id' => $rentalId,
            'tenant_id' => $tenantId,
            'vendor_id' => $vendorId,
            'expense_date' => $request->expense_date,
            'expense_category_id' => $categoryId ?: null,
            'expense_category' => $categoryName,
            'expense_subcategory' => $request->expense_subcategory ?: null,
            'expense_type' => $request->expense_type ?: null,
            'expense_title' => $expenseTitle,
            'description' => $request->description ?: null,
            'amount' => $request->amount,
            'payment_mode' => $request->payment_mode ?: null,
            'reference_no' => $request->reference_no ?: null,
            'payment_account' => $request->payment_account ?: null,
            'paid_to' => $paidTo ?: null,
            'bill_no' => $request->bill_no ?: null,
            'bill_file' => $billFilePath,
            'is_tenant_recoverable' => $isRecoverable,
            'recovery_amount' => $recoveryAmount,
            'recovery_status' => $recoveryStatus,
            'approval_status' => $request->approval_status ?? 'Pending',
            'remarks' => $request->remarks ?: null,
            'notes' => $request->notes ?: null,
        ]);

        $expense->syncProperties($propertyIds);

        if (!empty($firmIds)) {
            $expense->syncFirms($firmIds);
        }

        $expType = $expense->expense_type ?: $request->expense_type;
        if ($expType === 'General' || $expType === 'Office') {
            return redirect()->route('expenses.general')->with('success', 'General expense updated successfully.');
        } elseif ($expType === 'Rental') {
            return redirect()->route('expenses.rental')->with('success', 'Rental expense updated successfully.');
        } elseif ($expType === 'Personal') {
            return redirect()->route('expenses.personal')->with('success', 'Personal expense updated successfully.');
        } elseif ($expType === 'Property') {
            return redirect()->route('expenses.property')->with('success', 'Property expense updated successfully.');
        } else {
            return redirect()
                ->route('expenses.project-wise', $projectId ? ['project_id' => $projectId] : [])
                ->with('success', 'Project expense updated successfully.');
        }
    }

    public function destroy(Expense $expense)
    {
        $this->authorise($expense);

        if ($expense->bill_file) {
            Storage::disk('public')->delete($expense->bill_file);
        }

        $expense->delete();

        return redirect()
            ->back()
            ->with('success', 'Expense deleted successfully.');
    }

    public function exportPdf(Request $request)
    {
        $query = Expense::with(['firms', 'firm', 'project', 'property.propertyType', 'property.project', 'expenseCategory', 'vendor']);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            $query->forFirms([$firmId]);
        } elseif ($request->filled('firm_ids') || $request->filled('firm_id')) {
            $firmIds = $request->input('firm_ids', (array) $request->firm_id);
            $query->forFirms($firmIds);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q
                    ->where('expense_title', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")
                    ->orWhere('expense_category', 'like', "%{$s}%")
                    ->orWhere('expense_type', 'like', "%{$s}%")
                    ->orWhere('paid_to', 'like', "%{$s}%")
                    ->orWhere('bill_no', 'like', "%{$s}%")
                    ->orWhere('reference_no', 'like', "%{$s}%")
                    ->orWhere('payment_account', 'like', "%{$s}%")
                    ->orWhereHas('project', fn($pr) => $pr->where('project_name', 'like', "%{$s}%"))
                    ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                    ->orWhereHas('firms', fn($f) => $f->where('firm_name', 'like', "%{$s}%"))
                    ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_type')) {
            $query->where('expense_type', $request->filter_type);
        }
        if ($request->filled('filter_project')) {
            $query->where('project_id', $request->filter_project);
        }
        if ($request->filled('filter_property')) {
            $query->where('property_id', $request->filter_property);
        }
        if ($request->filled('filter_category')) {
            $cat = $request->filter_category;
            if (is_numeric($cat)) {
                $query->where('expense_category_id', $cat);
            } else {
                $query->where('expense_category', $cat);
            }
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

    public function projectWise(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        // Base project query
        $projectsQuery = Project::with(['firm', 'propertyMaster'])
            ->where(function ($q) {
                $q->where('status', 'active')
                  ->orWhereRaw('LOWER(status) = ?', ['active'])
                  ->orWhereNull('status');
            });

        if (!$isAdmin && $firmId) {
            $projectsQuery->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $projectsQuery->where('firm_id', $request->firm_id);
        }

        if ($request->filled('project_id')) {
            $projectsQuery->where('id', $request->project_id);
        }

        $allProjects = $projectsQuery->orderBy('project_name')->get();

        // Base expense query for filtering (strictly Property / Project expenses)
        $expensesQuery = Expense::with([
            'firms', 'firm', 'project', 'properties.propertyType', 'properties.project', 'property.propertyType',
            'property.project', 'expenseCategory', 'vendor', 'purchaseOrder.vendor'
        ])->where(function ($q) {
            $q->whereIn('expense_type', ['Property', 'Project'])
              ->orWhereNotNull('project_id')
              ->orWhereNotNull('property_id')
              ->orWhereNotNull('purchase_order_id');
        });

        if (!$isAdmin && $firmId) {
            $expensesQuery->forFirms([$firmId]);
        } elseif ($request->filled('firm_id')) {
            $expensesQuery->forFirms((array) $request->firm_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $expensesQuery->where(function ($q) use ($s) {
                $q->where('expense_title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('expense_category', 'like', "%{$s}%")
                  ->orWhere('paid_to', 'like', "%{$s}%")
                  ->orWhere('bill_no', 'like', "%{$s}%")
                  ->orWhere('reference_no', 'like', "%{$s}%")
                  ->orWhere('payment_account', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%")
                  ->orWhereHas('vendor', fn($v) => $v->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('properties', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                  ->orWhereHas('purchaseOrder', fn($po) => $po->where('po_number', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('date_from')) {
            $expensesQuery->where('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $expensesQuery->where('expense_date', '<=', $request->date_to);
        }
        if ($request->filled('filter_category')) {
            $cat = $request->filter_category;
            if (is_numeric($cat)) {
                $expensesQuery->where('expense_category_id', $cat);
            } else {
                $expensesQuery->where('expense_category', $cat);
            }
        }
        if ($request->filled('filter_status')) {
            $expensesQuery->where('approval_status', $request->filter_status);
        }

        $allFilteredExpenses = $expensesQuery->orderBy('expense_date', 'desc')->get();

        // Base contractor payments query for filtering
        $contractorPaymentsQuery = ContractorPayment::with([
            'contractor.projects', 'contractor.properties', 'project', 'property', 'firm', 'paymentMode'
        ]);

        if (!$isAdmin && $firmId) {
            $contractorPaymentsQuery->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $contractorPaymentsQuery->where('firm_id', $request->firm_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $contractorPaymentsQuery->where(function ($q) use ($s) {
                $q->where('remarks', 'like', "%{$s}%")
                  ->orWhere('reference_no', 'like', "%{$s}%")
                  ->orWhere('bill_no', 'like', "%{$s}%")
                  ->orWhere('payment_mode', 'like', "%{$s}%")
                  ->orWhere('bank_name', 'like', "%{$s}%")
                  ->orWhere('payment_type', 'like', "%{$s}%")
                  ->orWhereHas('contractor', fn($c) => $c->where('contractor_name', 'like', "%{$s}%")->orWhere('work_type', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('date_from')) {
            $contractorPaymentsQuery->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $contractorPaymentsQuery->where('payment_date', '<=', $request->date_to);
        }

        $allFilteredContractorPayments = $contractorPaymentsQuery->orderBy('payment_date', 'desc')->get();

        // Base broker commissions query for filtering
        $brokerCommissionsQuery = BrokerCommission::with([
            'broker', 'property.project', 'booking.property.project', 'firm', 'customer'
        ]);

        if (!$isAdmin && $firmId) {
            $brokerCommissionsQuery->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $brokerCommissionsQuery->where('firm_id', $request->firm_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $brokerCommissionsQuery->where(function ($q) use ($s) {
                $q->where('remarks', 'like', "%{$s}%")
                  ->orWhere('payment_status', 'like', "%{$s}%")
                  ->orWhere('commission_type', 'like', "%{$s}%")
                  ->orWhereHas('broker', fn($b) => $b->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"))
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('date_from')) {
            $brokerCommissionsQuery->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $brokerCommissionsQuery->where('payment_date', '<=', $request->date_to);
        }

        $allFilteredBrokerCommissions = $brokerCommissionsQuery->orderBy('payment_date', 'desc')->get();

        // Base Land / Property Master Payments query for filtering
        $landPaymentsQuery = PropertyMasterPayment::with([
            'propertyMaster.projects', 'propertyMaster.seller', 'firm', 'paymentMode'
        ]);

        if (!$isAdmin && $firmId) {
            $landPaymentsQuery->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $landPaymentsQuery->where('firm_id', $request->firm_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $landPaymentsQuery->where(function ($q) use ($s) {
                $q->where('remarks', 'like', "%{$s}%")
                  ->orWhere('reference_no', 'like', "%{$s}%")
                  ->orWhere('bank_name', 'like', "%{$s}%")
                  ->orWhere('payment_mode', 'like', "%{$s}%")
                  ->orWhereHas('propertyMaster', fn($pm) => $pm->where('property_name', 'like', "%{$s}%")->orWhere('seller_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('date_from')) {
            $landPaymentsQuery->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $landPaymentsQuery->where('payment_date', '<=', $request->date_to);
        }

        $allFilteredLandPayments = $landPaymentsQuery->orderBy('payment_date', 'desc')->get();

        // Group expenses & payments per project
        $projectsData = [];
        $grandTotal = 0;
        $grandPoTotal = 0;
        $grandDirectTotal = 0;
        $grandContractorTotal = 0;
        $grandBrokerTotal = 0;
        $grandLandTotal = 0;
        $grandApprovedTotal = 0;
        $grandPendingTotal = 0;
        $totalProjectsWithExpenses = 0;

        foreach ($allProjects as $project) {
            $pExpenses = $allFilteredExpenses->filter(function ($exp) use ($project) {
                return $exp->project_id == $project->id
                    || ($exp->property && $exp->property->project_id == $project->id)
                    || ($exp->relationLoaded('properties') && $exp->properties->contains('project_id', $project->id))
                    || ($exp->purchaseOrder && $exp->purchaseOrder->project_id == $project->id);
            });

            $pContractorPayments = $allFilteredContractorPayments->filter(function ($cp) use ($project) {
                return $cp->project_id == $project->id
                    || ($cp->property && $cp->property->project_id == $project->id)
                    || ($cp->contractor && $cp->contractor->project_id == $project->id)
                    || ($cp->contractor && $cp->contractor->relationLoaded('projects') && $cp->contractor->projects->contains('id', $project->id));
            });

            $pBrokerCommissions = $allFilteredBrokerCommissions->filter(function ($bc) use ($project) {
                return ($bc->property && $bc->property->project_id == $project->id)
                    || ($bc->booking && $bc->booking->property && $bc->booking->property->project_id == $project->id);
            });

            $pLandPayments = $allFilteredLandPayments->filter(function ($lp) use ($project) {
                if (!$lp->propertyMaster) return false;
                return $project->property_id == $lp->property_master_id
                    || ($lp->propertyMaster->relationLoaded('projects') && $lp->propertyMaster->projects->contains('id', $project->id))
                    || ($project->relationLoaded('propertyMasters') && $project->propertyMasters->contains('id', $lp->property_master_id));
            });

            $poCost = (float) $pExpenses->whereNotNull('purchase_order_id')->sum('amount');
            $directCost = (float) $pExpenses->whereNull('purchase_order_id')->sum('amount');
            $contractorCost = (float) $pContractorPayments->sum('amount');
            $brokerCost = (float) $pBrokerCommissions->sum('commission_amount');
            $landCost = (float) $pLandPayments->sum('amount');
            $totalCost = $poCost + $directCost + $contractorCost + $brokerCost + $landCost;

            $approvedCost = (float) $pExpenses->where('approval_status', 'Approved')->sum('amount') 
                          + $contractorCost 
                          + (float) $pBrokerCommissions->where('payment_status', 'paid')->sum('commission_amount') 
                          + $landCost;
            $pendingCost = (float) $pExpenses->where('approval_status', 'Pending')->sum('amount') 
                         + (float) $pBrokerCommissions->where('payment_status', '!=', 'paid')->sum('commission_amount');

            // Category breakdown
            $catBreakdown = [];
            foreach ($pExpenses as $pe) {
                $cName = $pe->expense_category ?: ($pe->expenseCategory->name ?? 'General');
                if (!isset($catBreakdown[$cName])) {
                    $catBreakdown[$cName] = ['name' => $cName, 'total' => 0, 'count' => 0];
                }
                $catBreakdown[$cName]['total'] += (float) $pe->amount;
                $catBreakdown[$cName]['count']++;
            }
            if ($contractorCost > 0) {
                $catBreakdown['Contractor Payments'] = [
                    'name' => 'Contractor Payments',
                    'total' => $contractorCost,
                    'count' => $pContractorPayments->count()
                ];
            }
            if ($brokerCost > 0) {
                $catBreakdown['Broker Commission'] = [
                    'name' => 'Broker Commission',
                    'total' => $brokerCost,
                    'count' => $pBrokerCommissions->count()
                ];
            }
            if ($landCost > 0) {
                $catBreakdown['Land & Acquisition Payment'] = [
                    'name' => 'Land & Acquisition Payment',
                    'total' => $landCost,
                    'count' => $pLandPayments->count()
                ];
            }
            uasort($catBreakdown, fn($a, $b) => $b['total'] <=> $a['total']);

            if ($pExpenses->isNotEmpty() || $pContractorPayments->isNotEmpty() || $pBrokerCommissions->isNotEmpty() || $pLandPayments->isNotEmpty()) {
                $totalProjectsWithExpenses++;
            }

            $grandTotal += $totalCost;
            $grandPoTotal += $poCost;
            $grandDirectTotal += $directCost;
            $grandContractorTotal += $contractorCost;
            $grandBrokerTotal += $brokerCost;
            $grandLandTotal += $landCost;
            $grandApprovedTotal += $approvedCost;
            $grandPendingTotal += $pendingCost;

            $projectsData[] = [
                'project' => $project,
                'expenses' => $pExpenses,
                'contractorPayments' => $pContractorPayments,
                'brokerCommissions' => $pBrokerCommissions,
                'landPayments' => $pLandPayments,
                'totalCost' => $totalCost,
                'poCost' => $poCost,
                'directCost' => $directCost,
                'contractorCost' => $contractorCost,
                'brokerCost' => $brokerCost,
                'landCost' => $landCost,
                'approvedCost' => $approvedCost,
                'pendingCost' => $pendingCost,
                'categories' => $catBreakdown,
                'expensesCount' => $pExpenses->count(),
                'contractorsCount' => $pContractorPayments->count(),
                'brokersCount' => $pBrokerCommissions->count(),
                'landCount' => $pLandPayments->count(),
            ];
        }

        // Unallocated / General Expenses & Payments (not tied to any project)
        $unallocatedExpenses = $allFilteredExpenses->filter(function ($exp) {
            $hasProjInProperties = $exp->relationLoaded('properties') && $exp->properties->some(fn($p) => !empty($p->project_id));
            return empty($exp->project_id) && (!$exp->property || empty($exp->property->project_id)) && !$hasProjInProperties;
        });

        $unallocatedContractorPayments = $allFilteredContractorPayments->filter(function ($cp) {
            $hasProjInContractor = $cp->contractor && ($cp->contractor->project_id || ($cp->contractor->relationLoaded('projects') && $cp->contractor->projects->isNotEmpty()));
            $hasProjInProperty = $cp->property && !empty($cp->property->project_id);
            return empty($cp->project_id) && !$hasProjInProperty && !$hasProjInContractor;
        });

        $unallocatedBrokerCommissions = $allFilteredBrokerCommissions->filter(function ($bc) {
            $hasProjInProp = $bc->property && !empty($bc->property->project_id);
            $hasProjInBooking = $bc->booking && $bc->booking->property && !empty($bc->booking->property->project_id);
            return !$hasProjInProp && !$hasProjInBooking;
        });

        $unallocatedLandPayments = $allFilteredLandPayments->filter(function ($lp) {
            if (!$lp->propertyMaster) return true;
            return empty($lp->propertyMaster->projects) || $lp->propertyMaster->projects->isEmpty();
        });

        $unallocatedDirectTotal = $unallocatedExpenses->whereNull('purchase_order_id')->sum('amount');
        $unallocatedPoTotal = $unallocatedExpenses->whereNotNull('purchase_order_id')->sum('amount');
        $unallocatedContractorTotal = (float) $unallocatedContractorPayments->sum('amount');
        $unallocatedBrokerTotal = (float) $unallocatedBrokerCommissions->sum('commission_amount');
        $unallocatedLandTotal = (float) $unallocatedLandPayments->sum('amount');

        $unallocatedTotal = $unallocatedExpenses->sum('amount') + $unallocatedContractorTotal + $unallocatedBrokerTotal + $unallocatedLandTotal;
        $unallocatedApproved = $unallocatedExpenses->where('approval_status', 'Approved')->sum('amount') 
                             + $unallocatedContractorTotal 
                             + (float) $unallocatedBrokerCommissions->where('payment_status', 'paid')->sum('commission_amount') 
                             + $unallocatedLandTotal;
        $unallocatedPending = $unallocatedExpenses->where('approval_status', 'Pending')->sum('amount') 
                            + (float) $unallocatedBrokerCommissions->where('payment_status', '!=', 'paid')->sum('commission_amount');

        $unallocatedCategories = [];
        foreach ($unallocatedExpenses as $ue) {
            $cName = $ue->expense_category ?: ($ue->expenseCategory->name ?? 'General');
            if (!isset($unallocatedCategories[$cName])) {
                $unallocatedCategories[$cName] = ['name' => $cName, 'total' => 0, 'count' => 0];
            }
            $unallocatedCategories[$cName]['total'] += (float) $ue->amount;
            $unallocatedCategories[$cName]['count']++;
        }
        if ($unallocatedContractorTotal > 0) {
            $unallocatedCategories['Contractor Payments'] = [
                'name' => 'Contractor Payments',
                'total' => $unallocatedContractorTotal,
                'count' => $unallocatedContractorPayments->count()
            ];
        }
        if ($unallocatedBrokerTotal > 0) {
            $unallocatedCategories['Broker Commission'] = [
                'name' => 'Broker Commission',
                'total' => $unallocatedBrokerTotal,
                'count' => $unallocatedBrokerCommissions->count()
            ];
        }
        if ($unallocatedLandTotal > 0) {
            $unallocatedCategories['Land & Acquisition Payment'] = [
                'name' => 'Land & Acquisition Payment',
                'total' => $unallocatedLandTotal,
                'count' => $unallocatedLandPayments->count()
            ];
        }
        uasort($unallocatedCategories, fn($a, $b) => $b['total'] <=> $a['total']);

        $grandTotal += $unallocatedTotal;
        $grandPoTotal += $unallocatedPoTotal;
        $grandDirectTotal += $unallocatedDirectTotal;
        $grandContractorTotal += $unallocatedContractorTotal;
        $grandBrokerTotal += $unallocatedBrokerTotal;
        $grandLandTotal += $unallocatedLandTotal;
        $grandApprovedTotal += $unallocatedApproved;
        $grandPendingTotal += $unallocatedPending;

        $dropdownData = $this->dropdowns($request->firm_id);
        $firms = $dropdownData['firms'];
        $categories = $dropdownData['categories'];
        $projectsList = Project::orderBy('project_name')->get();

        return view('admin.expenses.project-wise', compact(
            'projectsData', 'unallocatedExpenses', 'unallocatedContractorPayments', 'unallocatedBrokerCommissions', 'unallocatedLandPayments',
            'unallocatedTotal', 'unallocatedPoTotal', 'unallocatedDirectTotal', 'unallocatedContractorTotal', 'unallocatedBrokerTotal', 'unallocatedLandTotal',
            'unallocatedApproved', 'unallocatedPending', 'unallocatedCategories',
            'grandTotal', 'grandPoTotal', 'grandDirectTotal', 'grandContractorTotal', 'grandBrokerTotal', 'grandLandTotal',
            'grandApprovedTotal', 'grandPendingTotal',
            'totalProjectsWithExpenses', 'allProjects', 'firms', 'categories', 'projectsList'
        ));
    }

    public function exportProjectWisePdf(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $projectsQuery = Project::with(['firm', 'propertyMaster'])
            ->where(function ($q) {
                $q->where('status', 'active')
                  ->orWhereRaw('LOWER(status) = ?', ['active'])
                  ->orWhereNull('status');
            });

        if (!$isAdmin && $firmId) {
            $projectsQuery->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $projectsQuery->where('firm_id', $request->firm_id);
        }

        if ($request->filled('project_id')) {
            $projectsQuery->where('id', $request->project_id);
        }

        $allProjects = $projectsQuery->orderBy('project_name')->get();

        $expensesQuery = Expense::with([
            'firms', 'firm', 'project', 'properties.propertyType', 'properties.project', 'property.propertyType',
            'property.project', 'expenseCategory', 'vendor', 'purchaseOrder.vendor'
        ]);

        if (!$isAdmin && $firmId) {
            $expensesQuery->forFirms([$firmId]);
        } elseif ($request->filled('firm_id')) {
            $expensesQuery->forFirms((array) $request->firm_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $expensesQuery->where(function ($q) use ($s) {
                $q->where('expense_title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('expense_category', 'like', "%{$s}%")
                  ->orWhere('paid_to', 'like', "%{$s}%")
                  ->orWhere('bill_no', 'like', "%{$s}%")
                  ->orWhere('reference_no', 'like', "%{$s}%");
            });
        }

        if ($request->filled('date_from')) {
            $expensesQuery->where('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $expensesQuery->where('expense_date', '<=', $request->date_to);
        }
        if ($request->filled('filter_category')) {
            $cat = $request->filter_category;
            if (is_numeric($cat)) {
                $expensesQuery->where('expense_category_id', $cat);
            } else {
                $expensesQuery->where('expense_category', $cat);
            }
        }
        if ($request->filled('filter_status')) {
            $expensesQuery->where('approval_status', $request->filter_status);
        }

        $allFilteredExpenses = $expensesQuery->orderBy('expense_date', 'desc')->get();

        $contractorPaymentsQuery = ContractorPayment::with([
            'contractor.projects', 'contractor.properties', 'project', 'property', 'firm', 'paymentMode'
        ]);

        if (!$isAdmin && $firmId) {
            $contractorPaymentsQuery->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $contractorPaymentsQuery->where('firm_id', $request->firm_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $contractorPaymentsQuery->where(function ($q) use ($s) {
                $q->where('remarks', 'like', "%{$s}%")
                  ->orWhere('reference_no', 'like', "%{$s}%")
                  ->orWhere('bill_no', 'like', "%{$s}%")
                  ->orWhere('payment_mode', 'like', "%{$s}%")
                  ->orWhere('bank_name', 'like', "%{$s}%")
                  ->orWhere('payment_type', 'like', "%{$s}%")
                  ->orWhereHas('contractor', fn($c) => $c->where('contractor_name', 'like', "%{$s}%")->orWhere('work_type', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('date_from')) {
            $contractorPaymentsQuery->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $contractorPaymentsQuery->where('payment_date', '<=', $request->date_to);
        }

        $allFilteredContractorPayments = $contractorPaymentsQuery->orderBy('payment_date', 'desc')->get();

        $brokerCommissionsQuery = BrokerCommission::with([
            'broker', 'property.project', 'booking.property.project', 'firm', 'customer'
        ]);

        if (!$isAdmin && $firmId) {
            $brokerCommissionsQuery->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $brokerCommissionsQuery->where('firm_id', $request->firm_id);
        }

        if ($request->filled('date_from')) {
            $brokerCommissionsQuery->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $brokerCommissionsQuery->where('payment_date', '<=', $request->date_to);
        }

        $allFilteredBrokerCommissions = $brokerCommissionsQuery->orderBy('payment_date', 'desc')->get();

        $landPaymentsQuery = PropertyMasterPayment::with([
            'propertyMaster.projects', 'propertyMaster.seller', 'firm', 'paymentMode'
        ]);

        if (!$isAdmin && $firmId) {
            $landPaymentsQuery->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $landPaymentsQuery->where('firm_id', $request->firm_id);
        }

        if ($request->filled('date_from')) {
            $landPaymentsQuery->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $landPaymentsQuery->where('payment_date', '<=', $request->date_to);
        }

        $allFilteredLandPayments = $landPaymentsQuery->orderBy('payment_date', 'desc')->get();

        $projectsData = [];
        $grandTotal = 0;
        $grandPoTotal = 0;
        $grandDirectTotal = 0;
        $grandContractorTotal = 0;
        $grandBrokerTotal = 0;
        $grandLandTotal = 0;
        $grandApprovedTotal = 0;
        $grandPendingTotal = 0;

        foreach ($allProjects as $project) {
            $pExpenses = $allFilteredExpenses->filter(function ($exp) use ($project) {
                return $exp->project_id == $project->id
                    || ($exp->property && $exp->property->project_id == $project->id)
                    || ($exp->relationLoaded('properties') && $exp->properties->contains('project_id', $project->id));
            });

            $pContractorPayments = $allFilteredContractorPayments->filter(function ($cp) use ($project) {
                return $cp->project_id == $project->id
                    || ($cp->property && $cp->property->project_id == $project->id)
                    || ($cp->contractor && $cp->contractor->project_id == $project->id)
                    || ($cp->contractor && $cp->contractor->relationLoaded('projects') && $cp->contractor->projects->contains('id', $project->id));
            });

            $pBrokerCommissions = $allFilteredBrokerCommissions->filter(function ($bc) use ($project) {
                return ($bc->property && $bc->property->project_id == $project->id)
                    || ($bc->booking && $bc->booking->property && $bc->booking->property->project_id == $project->id);
            });

            $pLandPayments = $allFilteredLandPayments->filter(function ($lp) use ($project) {
                if (!$lp->propertyMaster) return false;
                return $project->property_id == $lp->property_master_id
                    || ($lp->propertyMaster->relationLoaded('projects') && $lp->propertyMaster->projects->contains('id', $project->id))
                    || ($project->relationLoaded('propertyMasters') && $project->propertyMasters->contains('id', $lp->property_master_id));
            });

            $poCost = (float) $pExpenses->whereNotNull('purchase_order_id')->sum('amount');
            $directCost = (float) $pExpenses->whereNull('purchase_order_id')->sum('amount');
            $contractorCost = (float) $pContractorPayments->sum('amount');
            $brokerCost = (float) $pBrokerCommissions->sum('commission_amount');
            $landCost = (float) $pLandPayments->sum('amount');
            $totalCost = $poCost + $directCost + $contractorCost + $brokerCost + $landCost;

            $approvedCost = (float) $pExpenses->where('approval_status', 'Approved')->sum('amount') 
                          + $contractorCost 
                          + (float) $pBrokerCommissions->where('payment_status', 'paid')->sum('commission_amount') 
                          + $landCost;
            $pendingCost = (float) $pExpenses->where('approval_status', 'Pending')->sum('amount') 
                         + (float) $pBrokerCommissions->where('payment_status', '!=', 'paid')->sum('commission_amount');

            $grandTotal += $totalCost;
            $grandPoTotal += $poCost;
            $grandDirectTotal += $directCost;
            $grandContractorTotal += $contractorCost;
            $grandBrokerTotal += $brokerCost;
            $grandLandTotal += $landCost;
            $grandApprovedTotal += $approvedCost;
            $grandPendingTotal += $pendingCost;

            $projectsData[] = [
                'project' => $project,
                'expenses' => $pExpenses,
                'contractorPayments' => $pContractorPayments,
                'brokerCommissions' => $pBrokerCommissions,
                'landPayments' => $pLandPayments,
                'totalCost' => $totalCost,
                'poCost' => $poCost,
                'directCost' => $directCost,
                'contractorCost' => $contractorCost,
                'brokerCost' => $brokerCost,
                'landCost' => $landCost,
                'approvedCost' => $approvedCost,
                'pendingCost' => $pendingCost,
                'expensesCount' => $pExpenses->count(),
                'contractorsCount' => $pContractorPayments->count(),
                'brokersCount' => $pBrokerCommissions->count(),
                'landCount' => $pLandPayments->count(),
            ];
        }

        $unallocatedExpenses = $allFilteredExpenses->filter(function ($exp) {
            $hasProjInProperties = $exp->relationLoaded('properties') && $exp->properties->some(fn($p) => !empty($p->project_id));
            return empty($exp->project_id) && (!$exp->property || empty($exp->property->project_id)) && !$hasProjInProperties;
        });

        $unallocatedContractorPayments = $allFilteredContractorPayments->filter(function ($cp) {
            $hasProjInContractor = $cp->contractor && ($cp->contractor->project_id || ($cp->contractor->relationLoaded('projects') && $cp->contractor->projects->isNotEmpty()));
            $hasProjInProperty = $cp->property && !empty($cp->property->project_id);
            return empty($cp->project_id) && !$hasProjInProperty && !$hasProjInContractor;
        });

        $unallocatedBrokerCommissions = $allFilteredBrokerCommissions->filter(function ($bc) {
            $hasProjInProp = $bc->property && !empty($bc->property->project_id);
            $hasProjInBooking = $bc->booking && $bc->booking->property && !empty($bc->booking->property->project_id);
            return !$hasProjInProp && !$hasProjInBooking;
        });

        $unallocatedLandPayments = $allFilteredLandPayments->filter(function ($lp) {
            if (!$lp->propertyMaster) return true;
            return empty($lp->propertyMaster->projects) || $lp->propertyMaster->projects->isEmpty();
        });

        $unallocatedContractorTotal = (float) $unallocatedContractorPayments->sum('amount');
        $unallocatedBrokerTotal = (float) $unallocatedBrokerCommissions->sum('commission_amount');
        $unallocatedLandTotal = (float) $unallocatedLandPayments->sum('amount');
        $unallocatedTotal = $unallocatedExpenses->sum('amount') + $unallocatedContractorTotal + $unallocatedBrokerTotal + $unallocatedLandTotal;

        $grandTotal += $unallocatedTotal;
        $grandContractorTotal += $unallocatedContractorTotal;
        $grandBrokerTotal += $unallocatedBrokerTotal;
        $grandLandTotal += $unallocatedLandTotal;

        return view('admin.expenses.project-wise-pdf', compact(
            'projectsData', 'unallocatedExpenses', 'unallocatedContractorPayments', 'unallocatedBrokerCommissions', 'unallocatedLandPayments', 'unallocatedTotal',
            'grandTotal', 'grandPoTotal', 'grandDirectTotal', 'grandContractorTotal', 'grandBrokerTotal', 'grandLandTotal', 'grandApprovedTotal', 'grandPendingTotal'
        ));
    }

    /**
     * AJAX endpoint to fetch rental, tenant, and agreement info for a property
     */
    public function getRentalPropertyInfo(Request $request, $propertyId)
    {
        $property = Property::with(['project', 'firm', 'firms'])->find($propertyId);
        if (!$property) {
            return response()->json(['success' => false, 'message' => 'Property not found.'], 404);
        }

        $rentals = \App\Models\Rental::with(['tenant', 'firm'])
            ->where('property_id', $propertyId)
            ->orderByRaw("CASE WHEN rental_status = 'active' THEN 1 ELSE 2 END")
            ->orderByDesc('id')
            ->get();

        $activeRental = $rentals->where('rental_status', 'active')->first() ?? $rentals->first();

        $firmIds = $property->firms->pluck('id')->toArray();
        if ($property->firm_id && !in_array($property->firm_id, $firmIds)) {
            $firmIds[] = $property->firm_id;
        }

        return response()->json([
            'success' => true,
            'property' => [
                'id' => $property->id,
                'name' => $property->property_name,
                'property_code' => $property->property_code,
                'project_id' => $property->project_id,
                'project_name' => $property->project?->project_name,
                'firm_id' => $property->firm_id,
                'firm_ids' => $firmIds,
                'firm_name' => $property->firm?->firm_name,
            ],
            'active_rental' => $activeRental ? [
                'id' => $activeRental->id,
                'agreement_no' => $activeRental->agreement_no,
                'tenant_id' => $activeRental->tenant_id,
                'tenant_name' => $activeRental->tenant_name ?? $activeRental->tenant?->name,
                'tenant_phone' => $activeRental->tenant_mobile ?? $activeRental->tenant?->phone,
                'firm_id' => $activeRental->firm_id,
                'rent_amount' => $activeRental->rent_amount,
                'security_deposit' => $activeRental->security_deposit,
                'rental_status' => $activeRental->rental_status,
                'start_date' => $activeRental->start_date ? (is_string($activeRental->start_date) ? $activeRental->start_date : $activeRental->start_date->format('Y-m-d')) : null,
                'end_date' => $activeRental->end_date ? (is_string($activeRental->end_date) ? $activeRental->end_date : $activeRental->end_date->format('Y-m-d')) : null,
            ] : null,
            'rentals' => $rentals->map(function ($r) {
                return [
                    'id' => $r->id,
                    'agreement_no' => $r->agreement_no,
                    'tenant_id' => $r->tenant_id,
                    'tenant_name' => $r->tenant_name ?? $r->tenant?->name,
                    'tenant_phone' => $r->tenant_mobile ?? $r->tenant?->phone,
                    'firm_id' => $r->firm_id,
                    'rent_amount' => $r->rent_amount,
                    'security_deposit' => $r->security_deposit,
                    'rental_status' => $r->rental_status,
                    'start_date' => $r->start_date ? (is_string($r->start_date) ? $r->start_date : $r->start_date->format('Y-m-d')) : null,
                    'end_date' => $r->end_date ? (is_string($r->end_date) ? $r->end_date : $r->end_date->format('Y-m-d')) : null,
                ];
            }),
        ]);
    }
}
