<?php

namespace App\Http\Controllers\Agriculture;

use App\Http\Controllers\Controller;
use App\Models\AgriLabour;
use App\Models\AgriFarm;
use App\Models\Firm;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgriLabourController extends Controller
{
    const LABOUR_TYPES = ['Fixed Labour', 'Normal Labour', 'Advance Labour'];

    private function authorise(AgriLabour $labour): void
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            if ($labour->firm_id != $firmId && !$labour->firms->contains($firmId)) {
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
        $pmQuery = PaymentMode::where('status', 'active')->orderBy('name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $farmQuery->where('firm_id', $firmId);
            $pmQuery->whereHas('firms', function($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            });
        }

        return [
            'firms'        => $firms,
            'farms'        => $farmQuery->get(),
            'paymentModes' => $pmQuery->get(),
            'labourTypes'  => self::LABOUR_TYPES,
        ];
    }

    public function index(Request $request)
    {
        $query = AgriLabour::with(['farm', 'firm', 'firms']);

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
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('mobile_number', 'like', "%{$s}%")
                  ->orWhere('field_crop', 'like', "%{$s}%")
                  ->orWhereHas('farm', fn($f) => $f->where('farm_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_type')) {
            $query->where('labour_type', $request->filter_type);
        }

        if ($request->filled('filter_farm')) {
            $query->where('farm_id', $request->filter_farm);
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        $labours = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $totalAdvance = (float) (clone $query)->sum('total_advance');
        $totalPaid    = (float) (clone $query)->sum('total_paid');
        $totalPending = (float) (clone $query)->sum('pending_amount');

        $farms = AgriFarm::where('status', 'Active')->orderBy('farm_name')->get();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.agriculture.labours.index', compact(
            'labours',
            'totalAdvance',
            'totalPaid',
            'totalPending',
            'farms',
            'firms'
        ));
    }

    public function create()
    {
        return view('admin.agriculture.labours.create', $this->dropdowns());
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $firmId;

        $request->validate([
            'name'           => 'required|string|max:255',
            'mobile_number'  => 'nullable|string|max:50',
            'labour_type'    => 'required|string|max:100',
            'farm_id'        => 'nullable|exists:agri_farms,id',
            'field_crop'     => 'nullable|string|max:255',
            'joining_date'   => 'nullable|date',
            'daily_wage'     => 'nullable|numeric|min:0',
            'fixed_salary'   => 'nullable|numeric|min:0',
            'initial_advance'=> 'nullable|numeric|min:0',
            'status'         => 'required|string|max:50',
            'notes'          => 'nullable|string',
        ]);

        $initialAdvance = $request->filled('initial_advance') ? (float)$request->initial_advance : 0;
        $dailyWage = $request->filled('daily_wage') ? (float)$request->daily_wage : 0;
        $fixedSalary = $request->filled('fixed_salary') ? (float)$request->fixed_salary : 0;

        $labour = AgriLabour::create([
            'firm_id'         => $primaryFirmId,
            'farm_id'         => $request->farm_id ?: null,
            'name'            => $request->name,
            'mobile_number'   => $request->mobile_number,
            'labour_type'     => $request->labour_type,
            'field_crop'      => $request->field_crop,
            'joining_date'    => $request->joining_date,
            'daily_wage'      => $dailyWage,
            'fixed_salary'    => $fixedSalary,
            'total_earned'    => 0,
            'total_advance'   => $initialAdvance,
            'total_paid'      => 0,
            'advance_balance' => $initialAdvance,
            'pending_amount'  => 0,
            'status'          => $request->status,
            'notes'           => $request->notes,
            'created_by'      => Auth::id(),
        ]);

        $labour->syncFirms($firmIds);

        // If initial advance was given, create an advance payment log & sync to AgriExpense
        if ($initialAdvance > 0) {
            $payment = \App\Models\AgriLabourPayment::create([
                'firm_id'         => $primaryFirmId,
                'farm_id'         => $request->farm_id ?: null,
                'labour_id'       => $labour->id,
                'payment_type'    => 'Advance Given',
                'payment_date'    => $request->joining_date ?: now()->toDateString(),
                'amount'          => $initialAdvance,
                'payment_mode'    => 'Cash',
                'payment_status'  => 'Paid',
                'sync_to_expense' => true,
                'notes'           => 'Initial advance given during labour registration',
                'created_by'      => Auth::id(),
            ]);

            // Sync to AgriExpense
            \App\Models\AgriExpense::create([
                'firm_id'           => $primaryFirmId,
                'farm_id'           => $request->farm_id ?: null,
                'expense_date'      => $request->joining_date ?: now()->toDateString(),
                'category'          => 'Labour',
                'expense_type'      => 'Labour Payment',
                'labour_id'         => $labour->id,
                'labour_payment_id' => $payment->id,
                'amount'            => $initialAdvance,
                'payment_method'    => 'Cash',
                'payment_status'    => 'Paid',
                'description'       => 'Labour Advance: ' . $labour->name,
                'notes'             => 'Initial advance recorded automatically from Labour module',
                'created_by'        => Auth::id(),
            ]);
        }

        return redirect()->route('agriculture.labours.show', $labour->id)
            ->with('success', 'Labour "' . $labour->name . '" registered successfully.');
    }

    public function show(AgriLabour $labour)
    {
        $labour->load(['farm', 'firm', 'firms', 'payments.paymentMode', 'payments.creator']);
        $this->authorise($labour);

        $dropdowns = $this->dropdowns($labour->firm_id);
        $paymentModes = $dropdowns['paymentModes'];

        return view('admin.agriculture.labours.show', compact('labour', 'paymentModes'));
    }

    public function edit(AgriLabour $labour)
    {
        $labour->load(['firms', 'firm']);
        $this->authorise($labour);

        return view('admin.agriculture.labours.edit', array_merge(
            ['labour' => $labour],
            $this->dropdowns($labour->firm_id)
        ));
    }

    public function update(Request $request, AgriLabour $labour)
    {
        $this->authorise($labour);

        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $labour->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $labour->firm_id;

        $request->validate([
            'name'          => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:50',
            'labour_type'   => 'required|string|max:100',
            'farm_id'       => 'nullable|exists:agri_farms,id',
            'field_crop'    => 'nullable|string|max:255',
            'joining_date'  => 'nullable|date',
            'daily_wage'    => 'nullable|numeric|min:0',
            'fixed_salary'  => 'nullable|numeric|min:0',
            'status'        => 'required|string|max:50',
            'notes'         => 'nullable|string',
        ]);

        $labour->update([
            'firm_id'       => $primaryFirmId,
            'farm_id'       => $request->farm_id ?: null,
            'name'          => $request->name,
            'mobile_number' => $request->mobile_number,
            'labour_type'   => $request->labour_type,
            'field_crop'    => $request->field_crop,
            'joining_date'  => $request->joining_date,
            'daily_wage'    => (float) $request->daily_wage,
            'fixed_salary'  => (float) $request->fixed_salary,
            'status'        => $request->status,
            'notes'         => $request->notes,
        ]);

        $labour->syncFirms($firmIds);
        $labour->recalculateBalances();

        return redirect()->route('agriculture.labours.show', $labour->id)
            ->with('success', 'Labour record updated successfully.');
    }

    public function destroy(AgriLabour $labour)
    {
        $this->authorise($labour);
        $name = $labour->name;
        $labour->delete();

        return redirect()->route('agriculture.labours.index')
            ->with('success', 'Labour "' . $name . '" deleted successfully.');
    }

    /**
     * AJAX auto-fetch labour details for payment / expense forms
     */
    public function ajaxLabourDetails(AgriLabour $labour)
    {
        $labour->load(['farm', 'firm']);

        return response()->json([
            'id'              => $labour->id,
            'name'            => $labour->name,
            'mobile_number'   => $labour->mobile_number,
            'labour_type'     => $labour->labour_type,
            'farm_id'         => $labour->farm_id,
            'farm_name'       => $labour->farm?->farm_name,
            'field_crop'      => $labour->field_crop,
            'daily_wage'      => $labour->daily_wage,
            'fixed_salary'    => $labour->fixed_salary,
            'total_advance'   => $labour->total_advance,
            'advance_balance' => $labour->advance_balance,
            'pending_amount'  => $labour->pending_amount,
            'total_paid'      => $labour->total_paid,
        ]);
    }
}
