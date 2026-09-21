<?php

namespace App\Http\Controllers\Agriculture;

use App\Http\Controllers\Controller;
use App\Models\AgriExpense;
use App\Models\AgriFarm;
use App\Models\AgriLabour;
use App\Models\Project;
use App\Models\Property;
use App\Models\Vendor;
use App\Models\Contractor;
use App\Models\PaymentMode;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AgriExpenseController extends Controller
{
    private function authorise(AgriExpense $expense): void
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

    private function dropdowns($selectedFirmId = null): array
    {
        $user = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $farmQuery = AgriFarm::where('status', 'Active')->orderBy('farm_name');
        $projQuery = Project::where('status', 'active')->orderBy('project_name');
        $propQuery = Property::with(['project.propertyMaster', 'firm'])->orderBy('property_name');
        $vendorQuery = Vendor::where('status', 'active')->orderBy('name');
        $contractorQuery = Contractor::where('status', 'active')->orderBy('contractor_name');
        $labourQuery = AgriLabour::where('status', 'Active')->orderBy('name');
        $pmQuery = PaymentMode::where('status', 'active')->orderBy('name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $farmQuery->where('firm_id', $firmId);
            $projQuery->where('firm_id', $firmId);
            $propQuery->where('firm_id', $firmId);
            $vendorQuery->where('firm_id', $firmId);
            $contractorQuery->where('firm_id', $firmId);
            $labourQuery->where('firm_id', $firmId);
            $pmQuery->whereHas('firms', function($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            });
        }

        return [
            'firms'        => $firms,
            'farms'        => $farmQuery->get(),
            'projects'     => $projQuery->get(),
            'properties'   => $propQuery->get(),
            'vendors'      => $vendorQuery->get(),
            'contractors'  => $contractorQuery->get(),
            'labours'      => $labourQuery->get(),
            'paymentModes' => $pmQuery->get(),
            'categories'   => AgriExpense::CATEGORIES,
            'expenseTypes' => AgriExpense::EXPENSE_TYPES,
        ];
    }

    public function index(Request $request)
    {
        $query = AgriExpense::with(['farm', 'project', 'property', 'vendor', 'contractor', 'labour', 'paymentMode', 'firm', 'firms']);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            $query->forFirms([$firmId]);
        } elseif ($request->filled('firm_id')) {
            $query->forFirms([$request->firm_id]);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('category', 'like', "%{$s}%")
                  ->orWhere('bill_no', 'like', "%{$s}%")
                  ->orWhere('invoice_no', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%")
                  ->orWhereHas('farm', fn($f) => $f->where('farm_name', 'like', "%{$s}%"))
                  ->orWhereHas('vendor', fn($v) => $v->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('contractor', fn($c) => $c->where('contractor_name', 'like', "%{$s}%"))
                  ->orWhereHas('labour', fn($l) => $l->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_category')) {
            $query->where('category', $request->filter_category);
        }

        if ($request->filled('filter_farm')) {
            $query->where('farm_id', $request->filter_farm);
        }

        if ($request->filled('filter_status')) {
            $query->where('payment_status', $request->filter_status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('expense_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $totalAmount = (float) (clone $query)->sum('amount');

        $dropdownData = $this->dropdowns();

        return view('admin.agriculture.expenses.index', array_merge(
            $dropdownData,
            compact('expenses', 'totalAmount')
        ));
    }

    public function create()
    {
        return view('admin.agriculture.expenses.create', $this->dropdowns());
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $firmId;

        $request->validate([
            'expense_date'    => 'required|date',
            'category'        => 'required|string|max:100',
            'expense_type'    => 'required|string|max:100',
            'farm_id'         => 'nullable|exists:agri_farms,id',
            'project_id'      => 'nullable|exists:projects,id',
            'property_id'     => 'nullable|exists:properties,id',
            'vendor_id'       => 'nullable|exists:vendors,id',
            'contractor_id'   => 'nullable|exists:contractors,id',
            'labour_id'       => 'nullable|exists:agri_labours,id',
            'amount'          => 'required|numeric|min:0.01',
            'payment_mode_id' => 'nullable|exists:payment_modes,id',
            'payment_method'  => 'nullable|string|max:100',
            'payment_status'  => 'required|string|max:50',
            'bill_no'         => 'nullable|string|max:100',
            'invoice_no'      => 'nullable|string|max:100',
            'attachment'      => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'description'     => 'nullable|string|max:500',
            'notes'           => 'nullable|string',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('agri_expenses', 'public');
        }

        $paymentMethod = $request->payment_method;
        if (!$paymentMethod && $request->filled('payment_mode_id')) {
            $paymentMethod = PaymentMode::find($request->payment_mode_id)?->name;
        }

        $expense = AgriExpense::create([
            'firm_id'         => $primaryFirmId,
            'farm_id'         => $request->farm_id ?: null,
            'project_id'      => $request->project_id ?: null,
            'property_id'     => $request->property_id ?: null,
            'expense_date'    => $request->expense_date,
            'category'        => $request->category,
            'expense_type'    => $request->expense_type,
            'vendor_id'       => $request->vendor_id ?: null,
            'contractor_id'   => $request->contractor_id ?: null,
            'labour_id'       => $request->labour_id ?: null,
            'amount'          => (float)$request->amount,
            'payment_mode_id' => $request->payment_mode_id ?: null,
            'payment_method'  => $paymentMethod ?: 'Cash',
            'payment_status'  => $request->payment_status,
            'bill_no'         => $request->bill_no,
            'invoice_no'      => $request->invoice_no,
            'attachment'      => $attachmentPath,
            'description'     => $request->description,
            'notes'           => $request->notes,
            'created_by'      => Auth::id(),
        ]);

        $expense->syncFirms($firmIds);

        return redirect()->route('agriculture.expenses.index')
            ->with('success', 'Agriculture expense of ₹' . number_format($expense->amount, 2) . ' recorded successfully.');
    }

    public function show(AgriExpense $expense)
    {
        $expense->load(['farm', 'project', 'property', 'vendor', 'contractor', 'labour', 'paymentMode', 'firm', 'firms', 'creator']);
        $this->authorise($expense);

        return view('admin.agriculture.expenses.show', compact('expense'));
    }

    public function edit(AgriExpense $expense)
    {
        $expense->load(['firms', 'firm']);
        $this->authorise($expense);

        return view('admin.agriculture.expenses.edit', array_merge(
            ['expense' => $expense],
            $this->dropdowns($expense->firm_id)
        ));
    }

    public function update(Request $request, AgriExpense $expense)
    {
        $this->authorise($expense);

        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $expense->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $expense->firm_id;

        $request->validate([
            'expense_date'    => 'required|date',
            'category'        => 'required|string|max:100',
            'expense_type'    => 'required|string|max:100',
            'farm_id'         => 'nullable|exists:agri_farms,id',
            'project_id'      => 'nullable|exists:projects,id',
            'property_id'     => 'nullable|exists:properties,id',
            'vendor_id'       => 'nullable|exists:vendors,id',
            'contractor_id'   => 'nullable|exists:contractors,id',
            'labour_id'       => 'nullable|exists:agri_labours,id',
            'amount'          => 'required|numeric|min:0.01',
            'payment_mode_id' => 'nullable|exists:payment_modes,id',
            'payment_method'  => 'nullable|string|max:100',
            'payment_status'  => 'required|string|max:50',
            'bill_no'         => 'nullable|string|max:100',
            'invoice_no'      => 'nullable|string|max:100',
            'attachment'      => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'description'     => 'nullable|string|max:500',
            'notes'           => 'nullable|string',
        ]);

        $attachmentPath = $expense->attachment;
        if ($request->hasFile('attachment')) {
            if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {
                Storage::disk('public')->delete($attachmentPath);
            }
            $attachmentPath = $request->file('attachment')->store('agri_expenses', 'public');
        }

        $paymentMethod = $request->payment_method;
        if (!$paymentMethod && $request->filled('payment_mode_id')) {
            $paymentMethod = PaymentMode::find($request->payment_mode_id)?->name;
        }

        $expense->update([
            'firm_id'         => $primaryFirmId,
            'farm_id'         => $request->farm_id ?: null,
            'project_id'      => $request->project_id ?: null,
            'property_id'     => $request->property_id ?: null,
            'expense_date'    => $request->expense_date,
            'category'        => $request->category,
            'expense_type'    => $request->expense_type,
            'vendor_id'       => $request->vendor_id ?: null,
            'contractor_id'   => $request->contractor_id ?: null,
            'labour_id'       => $request->labour_id ?: null,
            'amount'          => (float)$request->amount,
            'payment_mode_id' => $request->payment_mode_id ?: null,
            'payment_method'  => $paymentMethod ?: 'Cash',
            'payment_status'  => $request->payment_status,
            'bill_no'         => $request->bill_no,
            'invoice_no'      => $request->invoice_no,
            'attachment'      => $attachmentPath,
            'description'     => $request->description,
            'notes'           => $request->notes,
        ]);

        $expense->syncFirms($firmIds);

        return redirect()->route('agriculture.expenses.index')
            ->with('success', 'Agriculture expense updated successfully.');
    }

    public function destroy(AgriExpense $expense)
    {
        $this->authorise($expense);

        if ($expense->attachment && Storage::disk('public')->exists($expense->attachment)) {
            Storage::disk('public')->delete($expense->attachment);
        }

        $expense->delete();

        return redirect()->route('agriculture.expenses.index')
            ->with('success', 'Agriculture expense deleted successfully.');
    }
}
