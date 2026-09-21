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
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            if ($loan->firm_id != $firmId && !$loan->firms->contains($firmId)) {
                abort(403);
            }
        }
    }

    private function dropdowns($selectedFirmId = null): array
    {
        $user   = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $propQuery = Property::with(['project.propertyMaster'])->orderBy('property_name');
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

        if ($loan->has_emi && $schedules->count() > 0) {
            $totalPaid = $schedules->sum('paid_amount');
            $pending   = $loan->loan_amount - $totalPaid;

            if ($pending <= 0) {
                foreach ($schedules as $e) {
                    if (in_array($e->emi_status, ['Pending', 'Partial', 'Overdue'])) {
                        $e->update([
                            'emi_status'     => 'Paid',
                            'pending_amount' => 0.00,
                        ]);
                    }
                }
                $status = 'Completed';
            } else {
                $allPaid = $schedules->every(fn($e) => $e->emi_status === 'Paid');
                $status  = $allPaid ? 'Completed' : $loan->loan_status;
            }

            $loan->update([
                'paid_amount'    => $totalPaid,
                'pending_amount' => max(0, $pending),
                'loan_status'    => $status,
            ]);
        } else {
            $paymentsCount = $loan->payments()->count();
            if ($paymentsCount > 0) {
                $totalPaid = (float)$loan->payments()->sum('amount');
            } else {
                $totalPaid = (float)$loan->paid_amount;
            }
            $pending = round(max(0, (float)$loan->loan_amount - $totalPaid), 2);
            $status = ($pending <= 0) ? 'Completed' : ($loan->loan_status === 'Completed' ? 'Active' : $loan->loan_status);
            $loan->update([
                'paid_amount'    => $totalPaid,
                'pending_amount' => $pending,
                'loan_status'    => $status,
            ]);
        }
    }

    public function index(Request $request)
    {
        $query = Loan::with(['firms', 'firm', 'property', 'customer', 'paymentMode']);

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
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $firmId;

        $hasEmi = $request->boolean('has_emi');
        $paidAmount = $request->filled('paid_amount') ? (float)$request->paid_amount : 0;
        $loanAmount = (float)$request->loan_amount;
        $pendingAmount = max(0, $loanAmount - $paidAmount);
        $status = ($pendingAmount <= 0) ? 'Completed' : $request->loan_status;

        $loan = Loan::create([
            'firm_id'         => $primaryFirmId,
            'bank_name'       => $request->loan_type === 'Business Loan' ? $request->bank_name : null,
            'loan_type'       => $request->loan_type,
            'has_emi'         => $hasEmi,
            'property_id'     => $request->property_id ?: null,
            'customer_id'     => $request->customer_id ?: null,
            'loan_amount'     => $loanAmount,
            'interest_rate'   => $hasEmi ? $request->interest_rate : null,
            'emi_amount'      => $hasEmi ? $request->emi_amount : null,
            'loan_start_date' => $request->loan_start_date,
            'loan_end_date'   => $hasEmi ? $request->loan_end_date : null,
            'total_emi_months'=> $hasEmi ? $request->total_emi_months : null,
            'paid_amount'     => $paidAmount,
            'pending_amount'  => $pendingAmount,
            'loan_status'     => $status,
            'remarks'         => $request->remarks,
            'person_name'     => $request->loan_type === 'Personal Loan' ? $request->person_name : null,
            'mobile_number'   => $request->loan_type === 'Personal Loan' ? $request->mobile_number : null,
            'relationship'    => $request->loan_type === 'Personal Loan' ? $request->relationship : null,
            'payment_mode_id' => $request->payment_mode_id ?: null,
        ]);

        $loan->syncFirms($firmIds);
        
        if (!$hasEmi && $paidAmount > 0) {
            \App\Models\LoanPayment::create([
                'loan_id'         => $loan->id,
                'firm_id'         => $primaryFirmId,
                'payment_mode_id' => $request->payment_mode_id ?: null,
                'amount'          => $paidAmount,
                'payment_date'    => $request->loan_start_date ?: now()->toDateString(),
                'payment_mode'    => $loan->paymentMode?->name ?? 'Direct Payment',
                'reference_no'    => null,
                'remarks'         => 'Initial payment recorded during loan creation',
                'created_by'      => Auth::id(),
            ]);
        }

        if ($hasEmi && $loan->total_emi_months > 0 && $loan->emi_amount > 0) {
            $this->generateEmiSchedule($loan, $firmIds);
            return redirect()->route('loans.show', $loan->id)
                ->with('success', $loan->loan_type . ' added and EMI schedule generated successfully.');
        }

        return redirect()->route('loans.show', $loan->id)
            ->with('success', $loan->loan_type . ' added successfully.');
    }

    public function show(Loan $loan)
    {
        $loan->load(['firms', 'firm', 'property', 'customer', 'paymentMode', 'emiSchedules.firms', 'emiSchedules.firm', 'payments.paymentMode', 'payments.creator']);
        $this->authorise($loan);

        if ($loan->has_emi && $loan->emiSchedules->count() > 0) {
            $today = now()->toDateString();
            foreach ($loan->emiSchedules as $emi) {
                if ($emi->emi_status === 'Pending' && $emi->emi_date < $today) {
                    $emi->update(['emi_status' => 'Overdue']);
                }
            }
            $loan->refresh()->load(['firms', 'firm', 'emiSchedules.firms', 'emiSchedules.firm', 'payments.paymentMode', 'payments.creator']);
        }

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $loan->firm_id ?? ($user ? $user->firm_id : session('firm_id'));
        $pmQuery = \App\Models\PaymentMode::where('status', 'active')->orderBy('name');
        if ($firmId && (!$user || !$isAdmin)) {
            $pmQuery->whereHas('firms', function($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            });
        }
        $paymentModes = $pmQuery->get();

        return view('admin.loans.show', compact('loan', 'paymentModes'));
    }

    public function recordPayment(Request $request, Loan $loan)
    {
        $this->authorise($loan);

        $request->validate([
            'paid_amount'     => 'required|numeric|min:0.01',
            'payment_date'    => 'required|date',
            'payment_mode'    => 'nullable|string|max:100',
            'payment_mode_id' => 'nullable|exists:payment_modes,id',
            'reference_no'    => 'nullable|string|max:100',
            'remarks'         => 'nullable|string|max:500',
        ]);

        $amount = (float) $request->paid_amount;
        $paymentModeName = $request->payment_mode;
        if (!$paymentModeName && $request->filled('payment_mode_id')) {
            $paymentModeName = \App\Models\PaymentMode::find($request->payment_mode_id)?->name;
        }

        \App\Models\LoanPayment::create([
            'loan_id'         => $loan->id,
            'firm_id'         => $loan->firm_id,
            'payment_mode_id' => $request->payment_mode_id ?: null,
            'amount'          => $amount,
            'payment_date'    => $request->payment_date,
            'payment_mode'    => $paymentModeName,
            'reference_no'    => $request->reference_no,
            'remarks'         => $request->remarks,
            'created_by'      => Auth::id(),
        ]);

        $this->recalculateLoan($loan);
        $loan->refresh();

        return redirect()->back()
            ->with('success', 'Payment of ₹' . number_format($amount, 2) . ' recorded successfully. Pending balance: ₹' . number_format($loan->pending_amount, 2));
    }

    public function destroyPayment(Loan $loan, \App\Models\LoanPayment $payment)
    {
        $this->authorise($loan);

        if ($payment->loan_id != $loan->id) {
            abort(404);
        }

        $payment->delete();
        $this->recalculateLoan($loan);

        return redirect()->back()
            ->with('success', 'Payment record deleted successfully.');
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

        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $loan->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $loan->firm_id;

        $hasEmi = $request->boolean('has_emi');
        $loanAmount = (float)$request->loan_amount;

        $paidAmount = $request->filled('paid_amount') ? (float)$request->paid_amount : $loan->paid_amount;
        $pendingAmount = max(0, $loanAmount - $paidAmount);
        $status = ($pendingAmount <= 0) ? 'Completed' : $request->loan_status;

        $loan->update([
            'firm_id'         => $primaryFirmId,
            'bank_name'       => $request->loan_type === 'Business Loan' ? $request->bank_name : null,
            'loan_type'       => $request->loan_type,
            'has_emi'         => $hasEmi,
            'property_id'     => $request->property_id ?: null,
            'customer_id'     => $request->customer_id ?: null,
            'loan_amount'     => $loanAmount,
            'interest_rate'   => $hasEmi ? $request->interest_rate : null,
            'emi_amount'      => $hasEmi ? $request->emi_amount : null,
            'loan_start_date' => $request->loan_start_date,
            'loan_end_date'   => $hasEmi ? $request->loan_end_date : null,
            'total_emi_months'=> $hasEmi ? $request->total_emi_months : null,
            'loan_status'     => $status,
            'remarks'         => $request->remarks,
            'person_name'     => $request->loan_type === 'Personal Loan' ? $request->person_name : null,
            'mobile_number'   => $request->loan_type === 'Personal Loan' ? $request->mobile_number : null,
            'relationship'    => $request->loan_type === 'Personal Loan' ? $request->relationship : null,
            'payment_mode_id' => $request->payment_mode_id ?: null,
        ]);

        $loan->syncFirms($firmIds);

        if (!$hasEmi) {
            $loan->emiSchedules()->delete();
            $loan->update([
                'paid_amount'    => $paidAmount,
                'pending_amount' => $pendingAmount,
            ]);
        } else {
            if ($request->boolean('regenerate_emi') || ($loan->emiSchedules()->count() == 0 && $loan->total_emi_months > 0 && $loan->emi_amount > 0)) {
                $this->generateEmiSchedule($loan, $firmIds);
                $loan->update(['paid_amount' => 0, 'pending_amount' => $loanAmount]);
            }
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
            ->where(function($q) {
                $q->where('has_emi', true)
                  ->orWhereHas('emiSchedules');
            });

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            $query->forFirms([$firmId]);
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
                  ->orWhere('person_name', 'like', "%{$s}%")
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

        $today = now()->toDateString();
        foreach ($loan->emiSchedules as $emi) {
            if ($emi->emi_status === 'Pending' && $emi->emi_date < $today) {
                $emi->update(['emi_status' => 'Overdue']);
            }
        }
        $loan->refresh()->load(['firms', 'firm', 'emiSchedules.firms', 'emiSchedules.firm']);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $loan->firm_id ?? ($user ? $user->firm_id : session('firm_id'));
        $pmQuery = \App\Models\PaymentMode::where('status', 'active')->orderBy('name');
        if ($firmId && (!$user || !$isAdmin)) {
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
