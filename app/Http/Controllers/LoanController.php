<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanRequest;

use App\Models\Loan;
use App\Models\LoanEmiSchedule;
use App\Models\Property;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoanController extends Controller
{
    const LOAN_TYPES    = ['Home Loan', 'Personal Loan', 'Business Loan', 'Mortgage', 'Car Loan', 'Other'];
    const LOAN_STATUSES = ['Active', 'Completed', 'Closed', 'Cancelled'];
    const PAY_MODES     = ['Cash', 'Bank Transfer', 'UPI', 'Cheque', 'Other'];

    // ----------------------------------------------------------------
    // Shared dropdown data
    // ----------------------------------------------------------------
    private function dropdowns(): array
    {
        $firmId = Auth::user()->firm_id;
        return [
            'properties' => Property::where('firm_id', $firmId)->orderBy('property_name')->get(),
            'customers'  => Customer::where('firm_id', $firmId)->where('status', 'active')->orderBy('name')->get(),
        ];
    }

    private function authorise(Loan $loan): void
    {
        if ($loan->firm_id !== Auth::user()->firm_id) abort(403);
    }

    // ----------------------------------------------------------------
    // Generate month-wise EMI schedule
    // ----------------------------------------------------------------
    private function generateEmiSchedule(Loan $loan): void
    {
        // Remove any existing schedule first
        $loan->emiSchedules()->delete();

        $schedules = [];
        $date = Carbon::parse($loan->loan_start_date)->startOfMonth();

        for ($i = 0; $i < $loan->total_emi_months; $i++) {
            $emiDate = $date->copy()->addMonths($i);
            $schedules[] = [
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
    }

    // ----------------------------------------------------------------
    // Recalculate loan paid/pending from EMI data
    // ----------------------------------------------------------------
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

    // ----------------------------------------------------------------
    // INDEX
    // ----------------------------------------------------------------
    public function index(Request $request)
    {
        $firmId = Auth::user()->firm_id;
        $query  = Loan::with(['property', 'customer'])->where('firm_id', $firmId);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('bank_name', 'like', "%{$s}%")
                  ->orWhere('loan_type', 'like', "%{$s}%")
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"));
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

        $loans      = $query->orderBy('created_at', 'desc')->paginate(15);
        $totalLoan  = (clone $query)->sum('loan_amount');
        $totalPaid  = (clone $query)->sum('paid_amount');

        return view('admin.loans.index', array_merge(
            $this->dropdowns(),
            compact('loans', 'totalLoan', 'totalPaid')
        ));
    }

    // ----------------------------------------------------------------
    // CREATE / STORE
    // ----------------------------------------------------------------
    public function create()
    {
        return view('admin.loans.create', $this->dropdowns());
    }

    public function store(LoanRequest $request)
    {
        

        $loan = Loan::create([
            'firm_id'         => Auth::user()->firm_id,
            'bank_name'       => $request->bank_name,
            'loan_type'       => $request->loan_type,
            'property_id'     => $request->property_id ?: null,
            'customer_id'     => $request->customer_id ?: null,
            'loan_amount'     => $request->loan_amount,
            'interest_rate'   => $request->interest_rate,
            'emi_amount'      => $request->emi_amount,
            'loan_start_date' => $request->loan_start_date,
            'loan_end_date'   => $request->loan_end_date,
            'total_emi_months'=> $request->total_emi_months,
            'paid_amount'     => 0,
            'pending_amount'  => $request->loan_amount,
            'loan_status'     => $request->loan_status,
            'remarks'         => $request->remarks,
        ]);

        $this->generateEmiSchedule($loan);

        return redirect()->route('loans.show', $loan->id)
            ->with('success', 'Loan added and EMI schedule generated successfully.');
    }

    // ----------------------------------------------------------------
    // SHOW
    // ----------------------------------------------------------------
    public function show(Loan $loan)
    {
        $this->authorise($loan);
        $loan->load(['property', 'customer', 'emiSchedules']);

        // Auto-update overdue EMIs
        $today = now()->toDateString();
        foreach ($loan->emiSchedules as $emi) {
            if ($emi->emi_status === 'Pending' && $emi->emi_date < $today) {
                $emi->update(['emi_status' => 'Overdue']);
            }
        }
        $loan->refresh()->load('emiSchedules');

        return view('admin.loans.show', compact('loan'));
    }

    // ----------------------------------------------------------------
    // EDIT / UPDATE
    // ----------------------------------------------------------------
    public function edit(Loan $loan)
    {
        $this->authorise($loan);
        return view('admin.loans.edit', array_merge(
            ['loan' => $loan],
            $this->dropdowns()
        ));
    }

    public function update(LoanRequest $request, Loan $loan)
    {
        $this->authorise($loan);

        

        $loan->update([
            'bank_name'       => $request->bank_name,
            'loan_type'       => $request->loan_type,
            'property_id'     => $request->property_id ?: null,
            'customer_id'     => $request->customer_id ?: null,
            'loan_amount'     => $request->loan_amount,
            'interest_rate'   => $request->interest_rate,
            'emi_amount'      => $request->emi_amount,
            'loan_start_date' => $request->loan_start_date,
            'loan_end_date'   => $request->loan_end_date,
            'total_emi_months'=> $request->total_emi_months,
            'loan_status'     => $request->loan_status,
            'remarks'         => $request->remarks,
        ]);

        // Regenerate EMI schedule if key financial fields changed
        if ($request->boolean('regenerate_emi')) {
            $this->generateEmiSchedule($loan);
            $loan->update(['paid_amount' => 0, 'pending_amount' => $request->loan_amount]);
        }

        return redirect()->route('loans.show', $loan->id)
            ->with('success', 'Loan updated successfully.');
    }

    // ----------------------------------------------------------------
    // DESTROY
    // ----------------------------------------------------------------
    public function destroy(Loan $loan)
    {
        $this->authorise($loan);
        $loan->emiSchedules()->delete();
        $loan->delete();

        return redirect()->route('loans.index')
            ->with('success', 'Loan deleted successfully.');
    }

    // ----------------------------------------------------------------
    // EMI SCHEDULE page (list + payment form per EMI)
    // ----------------------------------------------------------------
    public function emiSchedule(Loan $loan)
    {
        $this->authorise($loan);
        $loan->load(['property', 'customer', 'emiSchedules']);

        // Auto-update overdue
        $today = now()->toDateString();
        foreach ($loan->emiSchedules as $emi) {
            if ($emi->emi_status === 'Pending' && $emi->emi_date < $today) {
                $emi->update(['emi_status' => 'Overdue']);
            }
        }
        $loan->refresh()->load('emiSchedules');

        return view('admin.loans.emi-schedule', compact('loan'));
    }

    // ----------------------------------------------------------------
    // EMI PAYMENT (POST)
    // ----------------------------------------------------------------
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
