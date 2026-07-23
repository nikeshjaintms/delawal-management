<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanRequest;
use App\Models\Loan;
use App\Models\LoanEmiSchedule;
use App\Models\Property;
use App\Models\Customer;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoanController extends Controller
{
    const LOAN_TYPES    = ['Home Loan', 'Personal Loan', 'Business Loan', 'Mortgage', 'Car Loan', 'Other'];
    const LOAN_STATUSES = ['Active', 'Completed', 'Closed', 'Cancelled'];
    const PAY_MODES     = ['Cash', 'Bank Transfer', 'UPI', 'Cheque', 'Other'];

    private function authorise(Loan $loan): void
    {
        $user = Auth::user();
        if ($user && !$user->isAdmin()) {
            $userFirmId = $user->firm_id;
            if ($loan->firm_id != $userFirmId && !$loan->firms->contains($userFirmId)) {
                abort(403);
            }
        }
    }

    private function dropdowns($selectedFirmId = null): array
    {
        $user   = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $propQuery = Property::orderBy('property_name');
        $custQuery = Customer::where('status', 'active')->orderBy('name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $propQuery->where('firm_id', $firmId);
            $custQuery->where('firm_id', $firmId);
        }

        $pmQuery = \App\Models\PaymentMode::where('status', 'active')->orderBy('name');
        if ($firmId && (!$user || !$user->isAdmin())) {
            $pmQuery->whereHas('firms', function($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            });
        }

        return [
            'firms'        => $firms,
            'properties'   => $propQuery->get(),
            'customers'    => $custQuery->get(),
            'paymentModes' => $pmQuery->get(),
        ];
    }

    private function generateEmiSchedule(Loan $loan, array $firmIds = []): void
    {
        $loan->emiSchedules()->delete();

        $schedules = [];
        $date = Carbon::parse($loan->loan_start_date)->startOfMonth();
        $primaryFirmId = reset($firmIds) ?: $loan->firm_id;

        for ($i = 0; $i < $loan->total_emi_months; $i++) {
            $emiDate = $date->copy()->addMonths($i);
            $schedules[] = [
                'firm_id'        => $primaryFirmId,
                'loan_id'        => $loan->id,
                'emi_month'      => (int) $emiDate->format('n'),
                'emi_year'       => (int) $emiDate->format('Y'),
                'emi_date'       => $emiDate->toDateString(),
                'emi_amount'     => $loan->emi_amount,
                'paid_amount'    => 0,
                'pending_amount' => $loan->emi_amount,
                'payment_date'   => null,
                'payment_mode'   => null,
                'emi_status'     => 'Pending',
                'remarks'        => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        LoanEmiSchedule::insert($schedules);

        $inserted = LoanEmiSchedule::where('loan_id', $loan->id)->get();
        foreach ($inserted as $scheduleItem) {
            $scheduleItem->syncFirms(!empty($firmIds) ? $firmIds : [$primaryFirmId]);
        }
    }

    private function recalculateLoan(Loan $loan): void
    {
        $loan->refresh();
        $schedules = $loan->emiSchedules;

        $totalPaid = $schedules->sum('paid_amount');
        $pending   = $loan->loan_amount - $totalPaid;

        $allPaid = $schedules->every(fn($e) => $e->emi_status === 'Paid');
        $status  = $allPaid ? 'Completed' : $loan->loan_status;

        $loan->update([
            'paid_amount'    => $totalPaid,
            'pending_amount' => max(0, $pending),
            'loan_status'    => $status,
        ]);
    }

    public function index(Request $request)
    {
        $query = Loan::with(['firms', 'firm', 'property', 'customer', 'paymentMode']);

        if (!Auth::user()->isAdmin()) {
            $query->forFirms([Auth::user()->firm_id]);
        } elseif ($request->filled('firm_ids') || $request->filled('firm_id')) {
            $firmIds = $request->input('firm_ids', (array)$request->firm_id);
            $query->forFirms($firmIds);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('bank_name', 'like', "%{$s}%")
                  ->orWhere('loan_type', 'like', "%{$s}%")
                  ->orWhere('person_name', 'like', "%{$s}%")
                  ->orWhere('relationship', 'like', "%{$s}%")
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                  ->orWhereHas('firms', fn($f) => $f->where('firm_name', 'like', "%{$s}%"))
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('filter_status')) {
            $query->where('loan_status', $request->filter_status);
        }
        if ($request->filled('filter_property')) {
            $query->where('property_id', $request->filter_property);
        }
        if ($request->filled('filter_customer')) {
            $query->where('customer_id', $request->filter_customer);
        }
        if ($request->filled('filter_loan_type')) {
            $query->where('loan_type', $request->filter_loan_type);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('loan_start_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('loan_start_date', '<=', $request->to_date);
        }

        $loans      = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $totalLoan  = (clone $query)->sum('loan_amount');
        $totalPaid  = (clone $query)->sum('paid_amount');
        $firms      = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.loans.index', array_merge(
            $this->dropdowns($request->firm_id),
            compact('loans', 'firms', 'totalLoan', 'totalPaid')
        ));
    }

    public function create()
    {
        return view('admin.loans.create', $this->dropdowns());
    }

    public function store(LoanRequest $request)
    {
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? Auth::user()->firm_id));
        $primaryFirmId = reset($firmIds) ?: Auth::user()->firm_id;

        $loan = Loan::create([
            'firm_id'         => $primaryFirmId,
            'bank_name'       => $request->loan_type === 'Business Loan' ? $request->bank_name : null,
            'loan_type'       => $request->loan_type,
            'property_id'     => $request->property_id ?: null,
            'customer_id'     => $request->customer_id ?: null,
            'loan_amount'     => $request->loan_amount,
            'interest_rate'   => $request->loan_type === 'Business Loan' ? $request->interest_rate : null,
            'emi_amount'      => $request->loan_type === 'Business Loan' ? $request->emi_amount : null,
            'loan_start_date' => $request->loan_start_date,
            'loan_end_date'   => $request->loan_type === 'Business Loan' ? $request->loan_end_date : null,
            'total_emi_months'=> $request->loan_type === 'Business Loan' ? $request->total_emi_months : null,
            'paid_amount'     => 0,
            'pending_amount'  => $request->loan_amount,
            'loan_status'     => $request->loan_status,
            'remarks'         => $request->remarks,
            'person_name'     => $request->loan_type === 'Personal Loan' ? $request->person_name : null,
            'mobile_number'   => $request->loan_type === 'Personal Loan' ? $request->mobile_number : null,
            'relationship'    => $request->loan_type === 'Personal Loan' ? $request->relationship : null,
            'payment_mode_id' => $request->loan_type === 'Personal Loan' ? $request->payment_mode_id : null,
        ]);

        $loan->syncFirms($firmIds);
        
        if ($loan->loan_type === 'Business Loan') {
            $this->generateEmiSchedule($loan, $firmIds);
            return redirect()->route('loans.show', $loan->id)
                ->with('success', 'Business Loan added and EMI schedule generated successfully.');
        }

        return redirect()->route('loans.show', $loan->id)
            ->with('success', 'Personal Loan added successfully.');
    }

    public function show(Loan $loan)
    {
        $loan->load(['firms', 'firm', 'property', 'customer', 'paymentMode', 'emiSchedules.firms', 'emiSchedules.firm']);
        $this->authorise($loan);

        $today = now()->toDateString();
        foreach ($loan->emiSchedules as $emi) {
            if ($emi->emi_status === 'Pending' && $emi->emi_date < $today) {
                $emi->update(['emi_status' => 'Overdue']);
            }
        }
        $loan->refresh()->load(['firms', 'firm', 'emiSchedules.firms', 'emiSchedules.firm']);

        return view('admin.loans.show', compact('loan'));
    }

    public function edit(Loan $loan)
    {
        $loan->load(['firms', 'firm']);
        $this->authorise($loan);
        return view('admin.loans.edit', array_merge(
            ['loan' => $loan],
            $this->dropdowns($loan->firm_id)
        ));
    }

    public function update(LoanRequest $request, Loan $loan)
    {
        $loan->load(['firms', 'firm']);
        $this->authorise($loan);

        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $loan->firm_id ?? Auth::user()->firm_id));
        $primaryFirmId = reset($firmIds) ?: $loan->firm_id;

        $loan->update([
            'firm_id'         => $primaryFirmId,
            'bank_name'       => $request->loan_type === 'Business Loan' ? $request->bank_name : null,
            'loan_type'       => $request->loan_type,
            'property_id'     => $request->property_id ?: null,
            'customer_id'     => $request->customer_id ?: null,
            'loan_amount'     => $request->loan_amount,
            'interest_rate'   => $request->loan_type === 'Business Loan' ? $request->interest_rate : null,
            'emi_amount'      => $request->loan_type === 'Business Loan' ? $request->emi_amount : null,
            'loan_start_date' => $request->loan_start_date,
            'loan_end_date'   => $request->loan_type === 'Business Loan' ? $request->loan_end_date : null,
            'total_emi_months'=> $request->loan_type === 'Business Loan' ? $request->total_emi_months : null,
            'loan_status'     => $request->loan_status,
            'remarks'         => $request->remarks,
            'person_name'     => $request->loan_type === 'Personal Loan' ? $request->person_name : null,
            'mobile_number'   => $request->loan_type === 'Personal Loan' ? $request->mobile_number : null,
            'relationship'    => $request->loan_type === 'Personal Loan' ? $request->relationship : null,
            'payment_mode_id' => $request->loan_type === 'Personal Loan' ? $request->payment_mode_id : null,
        ]);

        $loan->syncFirms($firmIds);

        if ($loan->loan_type === 'Business Loan') {
            if ($request->boolean('regenerate_emi')) {
                $this->generateEmiSchedule($loan, $firmIds);
                $loan->update(['paid_amount' => 0, 'pending_amount' => $request->loan_amount]);
            }
        } else {
            // Delete any existing EMI schedules for Personal Loan
            $loan->emiSchedules()->delete();
        }

        return redirect()->route('loans.show', $loan->id)
            ->with('success', 'Loan updated successfully.');
    }

    public function destroy(Loan $loan)
    {
        $this->authorise($loan);
        $loan->emiSchedules()->delete();
        $loan->delete();

        return redirect()->route('loans.index')
            ->with('success', 'Loan deleted successfully.');
    }

    public function emiScheduleIndex(Request $request)
    {
        $query = Loan::with(['firm', 'property', 'customer', 'emiSchedules'])
            ->where('loan_type', 'Business Loan');

        if (!Auth::user()->isAdmin()) {
            $query->forFirms([Auth::user()->firm_id]);
        } elseif ($request->filled('firm_id')) {
            $query->forFirms([$request->firm_id]);
        }

        if ($request->filled('filter_status')) {
            $query->where('loan_status', $request->filter_status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('bank_name', 'like', "%{$s}%")
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"));
            });
        }

        // Auto-update overdue EMIs
        $today = now()->toDateString();
        $loans = $query->orderBy('loan_start_date', 'desc')->get();
        foreach ($loans as $loan) {
            foreach ($loan->emiSchedules as $emi) {
                if ($emi->emi_status === 'Pending' && $emi->emi_date < $today) {
                    $emi->update(['emi_status' => 'Overdue']);
                }
            }
        }
        // Reload with fresh emi data after possible updates
        $loans->each->load('emiSchedules');

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.loans.emi-schedule-index', compact('loans', 'firms'));
    }

    public function emiSchedule(Loan $loan)
    {
        $loan->load(['firms', 'firm', 'property', 'customer', 'emiSchedules.firms', 'emiSchedules.firm']);
        $this->authorise($loan);

        if ($loan->loan_type === 'Personal Loan') {
            return redirect()->route('loans.show', $loan->id)->with('error', 'EMI Schedule is not applicable for Personal Loans.');
        }

        $today = now()->toDateString();
        foreach ($loan->emiSchedules as $emi) {
            if ($emi->emi_status === 'Pending' && $emi->emi_date < $today) {
                $emi->update(['emi_status' => 'Overdue']);
            }
        }
        $loan->refresh()->load(['firms', 'firm', 'emiSchedules.firms', 'emiSchedules.firm']);

        $user = Auth::user();
        $firmId = $loan->firm_id ?? ($user ? $user->firm_id : session('firm_id'));
        $pmQuery = \App\Models\PaymentMode::where('status', 'active')->orderBy('name');
        if ($firmId && (!$user || !$user->isAdmin())) {
            $pmQuery->whereHas('firms', function($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            });
        }
        $paymentModes = $pmQuery->get();

        return view('admin.loans.emi-schedule', compact('loan', 'paymentModes'));
    }

    public function emiPay(Request $request, Loan $loan, LoanEmiSchedule $emi)
    {
        $this->authorise($loan);

        $paid    = (float) $request->paid_amount;
        $pending = round($emi->emi_amount - $paid, 2);

        $status = 'Partial';
        if ($paid >= $emi->emi_amount) {
            $status  = 'Paid';
            $pending = 0;
        }

        $emi->update([
            'paid_amount'    => $paid,
            'pending_amount' => $pending,
            'payment_date'   => $request->payment_date,
            'payment_mode'   => $request->payment_mode,
            'emi_status'     => $status,
            'remarks'        => $request->remarks,
        ]);

        $this->recalculateLoan($loan);

        return redirect()->route('loans.emi-schedule', $loan->id)
            ->with('success', 'EMI payment recorded successfully.');
    }
}
