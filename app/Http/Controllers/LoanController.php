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
        if (!Auth::user()->isAdmin() && $loan->firm_id !== Auth::user()->firm_id) {
            abort(403);
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

        return [
            'firms'      => $firms,
            'properties' => $propQuery->get(),
            'customers'  => $custQuery->get(),
        ];
    }

    private function generateEmiSchedule(Loan $loan): void
    {
        $loan->emiSchedules()->delete();

        $schedules = [];
        $date = Carbon::parse($loan->loan_start_date)->startOfMonth();

        for ($i = 0; $i < $loan->total_emi_months; $i++) {
            $emiDate = $date->copy()->addMonths($i);
            $schedules[] = [
                'firm_id'        => $loan->firm_id,
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
        $query = Loan::with(['firm', 'property', 'customer']);

        if (!Auth::user()->isAdmin()) {
            $query->where('firm_id', Auth::user()->firm_id);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('bank_name', 'like', "%{$s}%")
                  ->orWhere('loan_type', 'like', "%{$s}%")
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
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
        $firmId = $request->firm_id ?? Auth::user()->firm_id;

        $loan = Loan::create([
            'firm_id'         => $firmId,
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

    public function show(Loan $loan)
    {
        $this->authorise($loan);
        $loan->load(['firm', 'property', 'customer', 'emiSchedules.firm']);

        $today = now()->toDateString();
        foreach ($loan->emiSchedules as $emi) {
            if ($emi->emi_status === 'Pending' && $emi->emi_date < $today) {
                $emi->update(['emi_status' => 'Overdue']);
            }
        }
        $loan->refresh()->load('emiSchedules.firm');

        return view('admin.loans.show', compact('loan'));
    }

    public function edit(Loan $loan)
    {
        $this->authorise($loan);
        return view('admin.loans.edit', array_merge(
            ['loan' => $loan],
            $this->dropdowns($loan->firm_id)
        ));
    }

    public function update(LoanRequest $request, Loan $loan)
    {
        $this->authorise($loan);

        $firmId = $request->firm_id ?? $loan->firm_id ?? Auth::user()->firm_id;

        $loan->update([
            'firm_id'         => $firmId,
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

        if ($request->boolean('regenerate_emi')) {
            $this->generateEmiSchedule($loan);
            $loan->update(['paid_amount' => 0, 'pending_amount' => $request->loan_amount]);
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

    public function emiSchedule(Loan $loan)
    {
        $this->authorise($loan);
        $loan->load(['firm', 'property', 'customer', 'emiSchedules.firm']);

        $today = now()->toDateString();
        foreach ($loan->emiSchedules as $emi) {
            if ($emi->emi_status === 'Pending' && $emi->emi_date < $today) {
                $emi->update(['emi_status' => 'Overdue']);
            }
        }
        $loan->refresh()->load('emiSchedules.firm');

        return view('admin.loans.emi-schedule', compact('loan'));
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
