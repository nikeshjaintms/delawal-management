<?php

namespace App\Http\Controllers\Agriculture;

use App\Http\Controllers\Controller;
use App\Models\AgriIncome;
use App\Models\AgriFarm;
use App\Models\Project;
use App\Models\Property;
use App\Models\Customer;
use App\Models\PaymentMode;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AgriIncomeController extends Controller
{
    private function authorise(AgriIncome $income): void
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            if ($income->firm_id != $firmId && !$income->firms->contains($firmId)) {
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
        $custQuery = Customer::where('status', 'active')->orderBy('name');
        $pmQuery = PaymentMode::where('status', 'active')->orderBy('name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $farmQuery->where('firm_id', $firmId);
            $projQuery->where('firm_id', $firmId);
            $propQuery->where('firm_id', $firmId);
            $custQuery->where('firm_id', $firmId);
            $pmQuery->whereHas('firms', function($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            });
        }

        return [
            'firms'        => $firms,
            'farms'        => $farmQuery->get(),
            'projects'     => $projQuery->get(),
            'properties'   => $propQuery->get(),
            'customers'    => $custQuery->get(),
            'paymentModes' => $pmQuery->get(),
            'incomeTypes'  => AgriIncome::INCOME_TYPES,
            'units'        => AgriIncome::UNITS,
        ];
    }

    public function index(Request $request)
    {
        $query = AgriIncome::with(['farm', 'project', 'property', 'customer', 'paymentMode', 'firm', 'firms']);

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
                $q->where('crop_product', 'like', "%{$s}%")
                  ->orWhere('buyer_name', 'like', "%{$s}%")
                  ->orWhere('invoice_no', 'like', "%{$s}%")
                  ->orWhere('income_type', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%")
                  ->orWhereHas('farm', fn($f) => $f->where('farm_name', 'like', "%{$s}%"))
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_type')) {
            $query->where('income_type', $request->filter_type);
        }

        if ($request->filled('filter_farm')) {
            $query->where('farm_id', $request->filter_farm);
        }

        if ($request->filled('filter_status')) {
            $query->where('payment_status', $request->filter_status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('income_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('income_date', '<=', $request->to_date);
        }

        $incomes = $query->orderBy('income_date', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $totalIncomeAmount = (float) (clone $query)->sum('total_amount');
        $totalReceived     = (float) (clone $query)->sum('payment_received');
        $totalPending      = (float) (clone $query)->sum('pending_amount');

        $dropdownData = $this->dropdowns();

        return view('admin.agriculture.incomes.index', array_merge(
            $dropdownData,
            compact('incomes', 'totalIncomeAmount', 'totalReceived', 'totalPending')
        ));
    }

    public function create()
    {
        return view('admin.agriculture.incomes.create', $this->dropdowns());
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $firmId;

        $request->validate([
            'income_date'      => 'required|date',
            'income_type'      => 'required|string|max:100',
            'farm_id'          => 'nullable|exists:agri_farms,id',
            'project_id'       => 'nullable|exists:projects,id',
            'property_id'      => 'nullable|exists:properties,id',
            'customer_id'      => 'nullable|exists:customers,id',
            'buyer_name'       => 'nullable|string|max:255',
            'crop_product'     => 'required|string|max:255',
            'quantity'         => 'required|numeric|min:0.01',
            'unit'             => 'required|string|max:50',
            'rate'             => 'required|numeric|min:0.01',
            'payment_received' => 'nullable|numeric|min:0',
            'payment_mode_id'  => 'nullable|exists:payment_modes,id',
            'payment_method'   => 'nullable|string|max:100',
            'payment_status'   => 'required|string|max:50',
            'invoice_no'       => 'nullable|string|max:100',
            'attachment'       => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'notes'            => 'nullable|string',
        ]);

        $quantity = (float) $request->quantity;
        $rate = (float) $request->rate;
        $totalAmount = round($quantity * $rate, 2);
        $paymentReceived = $request->filled('payment_received') ? (float)$request->payment_received : $totalAmount;
        $pendingAmount = max(0, round($totalAmount - $paymentReceived, 2));

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('agri_incomes', 'public');
        }

        $paymentMethod = $request->payment_method;
        if (!$paymentMethod && $request->filled('payment_mode_id')) {
            $paymentMethod = PaymentMode::find($request->payment_mode_id)?->name;
        }

        $income = AgriIncome::create([
            'firm_id'          => $primaryFirmId,
            'farm_id'          => $request->farm_id ?: null,
            'project_id'       => $request->project_id ?: null,
            'property_id'      => $request->property_id ?: null,
            'income_date'      => $request->income_date,
            'income_type'      => $request->income_type,
            'customer_id'      => $request->customer_id ?: null,
            'buyer_name'       => $request->buyer_name,
            'crop_product'     => $request->crop_product,
            'quantity'         => $quantity,
            'unit'             => $request->unit,
            'rate'             => $rate,
            'total_amount'     => $totalAmount,
            'payment_received' => $paymentReceived,
            'pending_amount'   => $pendingAmount,
            'payment_mode_id'  => $request->payment_mode_id ?: null,
            'payment_method'   => $paymentMethod ?: 'Cash',
            'payment_status'   => $request->payment_status,
            'invoice_no'       => $request->invoice_no,
            'attachment'       => $attachmentPath,
            'notes'            => $request->notes,
            'created_by'       => Auth::id(),
        ]);

        $income->syncFirms($firmIds);

        return redirect()->route('agriculture.incomes.index')
            ->with('success', 'Agriculture income of ₹' . number_format($income->total_amount, 2) . ' recorded successfully.');
    }

    public function show(AgriIncome $income)
    {
        $income->load(['farm', 'project', 'property', 'customer', 'paymentMode', 'firm', 'firms', 'creator']);
        $this->authorise($income);

        return view('admin.agriculture.incomes.show', compact('income'));
    }

    public function edit(AgriIncome $income)
    {
        $income->load(['firms', 'firm']);
        $this->authorise($income);

        return view('admin.agriculture.incomes.edit', array_merge(
            ['income' => $income],
            $this->dropdowns($income->firm_id)
        ));
    }

    public function update(Request $request, AgriIncome $income)
    {
        $this->authorise($income);

        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $income->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $income->firm_id;

        $request->validate([
            'income_date'      => 'required|date',
            'income_type'      => 'required|string|max:100',
            'farm_id'          => 'nullable|exists:agri_farms,id',
            'project_id'       => 'nullable|exists:projects,id',
            'property_id'      => 'nullable|exists:properties,id',
            'customer_id'      => 'nullable|exists:customers,id',
            'buyer_name'       => 'nullable|string|max:255',
            'crop_product'     => 'required|string|max:255',
            'quantity'         => 'required|numeric|min:0.01',
            'unit'             => 'required|string|max:50',
            'rate'             => 'required|numeric|min:0.01',
            'payment_received' => 'nullable|numeric|min:0',
            'payment_mode_id'  => 'nullable|exists:payment_modes,id',
            'payment_method'   => 'nullable|string|max:100',
            'payment_status'   => 'required|string|max:50',
            'invoice_no'       => 'nullable|string|max:100',
            'attachment'       => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'notes'            => 'nullable|string',
        ]);

        $quantity = (float) $request->quantity;
        $rate = (float) $request->rate;
        $totalAmount = round($quantity * $rate, 2);
        $paymentReceived = $request->filled('payment_received') ? (float)$request->payment_received : $totalAmount;
        $pendingAmount = max(0, round($totalAmount - $paymentReceived, 2));

        $attachmentPath = $income->attachment;
        if ($request->hasFile('attachment')) {
            if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {
                Storage::disk('public')->delete($attachmentPath);
            }
            $attachmentPath = $request->file('attachment')->store('agri_incomes', 'public');
        }

        $paymentMethod = $request->payment_method;
        if (!$paymentMethod && $request->filled('payment_mode_id')) {
            $paymentMethod = PaymentMode::find($request->payment_mode_id)?->name;
        }

        $income->update([
            'firm_id'          => $primaryFirmId,
            'farm_id'          => $request->farm_id ?: null,
            'project_id'       => $request->project_id ?: null,
            'property_id'      => $request->property_id ?: null,
            'income_date'      => $request->income_date,
            'income_type'      => $request->income_type,
            'customer_id'      => $request->customer_id ?: null,
            'buyer_name'       => $request->buyer_name,
            'crop_product'     => $request->crop_product,
            'quantity'         => $quantity,
            'unit'             => $request->unit,
            'rate'             => $rate,
            'total_amount'     => $totalAmount,
            'payment_received' => $paymentReceived,
            'pending_amount'   => $pendingAmount,
            'payment_mode_id'  => $request->payment_mode_id ?: null,
            'payment_method'   => $paymentMethod ?: 'Cash',
            'payment_status'   => $request->payment_status,
            'invoice_no'       => $request->invoice_no,
            'attachment'       => $attachmentPath,
            'notes'            => $request->notes,
        ]);

        $income->syncFirms($firmIds);

        return redirect()->route('agriculture.incomes.index')
            ->with('success', 'Agriculture income updated successfully.');
    }

    public function destroy(AgriIncome $income)
    {
        $this->authorise($income);

        if ($income->attachment && Storage::disk('public')->exists($income->attachment)) {
            Storage::disk('public')->delete($income->attachment);
        }

        $income->delete();

        return redirect()->route('agriculture.incomes.index')
            ->with('success', 'Agriculture income record deleted successfully.');
    }
}
