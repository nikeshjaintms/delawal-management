<?php

namespace App\Http\Controllers\Agriculture;

use App\Http\Controllers\Controller;
use App\Models\AgriLabourPayment;
use App\Models\AgriLabour;
use App\Models\AgriFarm;
use App\Models\AgriExpense;
use App\Models\Firm;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgriLabourPaymentController extends Controller
{
    private function authorise(AgriLabourPayment $payment): void
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            if ($payment->firm_id != $firmId && !$payment->firms->contains($firmId)) {
                abort(403);
            }
        }
    }

    private function dropdowns($selectedFirmId = null): array
    {
        $user = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $labourQuery = AgriLabour::where('status', 'Active')->with('farm')->orderBy('name');
        $farmQuery = AgriFarm::where('status', 'Active')->orderBy('farm_name');
        $pmQuery = PaymentMode::where('status', 'active')->orderBy('name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $labourQuery->where('firm_id', $firmId);
            $farmQuery->where('firm_id', $firmId);
            $pmQuery->whereHas('firms', function($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            });
        }

        return [
            'firms'        => $firms,
            'labours'      => $labourQuery->get(),
            'farms'        => $farmQuery->get(),
            'paymentModes' => $pmQuery->get(),
            'paymentTypes' => ['Daily Wage', 'Salary', 'Advance Given', 'Advance Deduction', 'Bonus'],
        ];
    }

    public function index(Request $request)
    {
        $query = AgriLabourPayment::with(['labour.farm', 'farm', 'paymentMode', 'firm', 'creator']);

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
                $q->where('reference_no', 'like', "%{$s}%")
                  ->orWhere('payment_type', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%")
                  ->orWhereHas('labour', fn($l) => $l->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('farm', fn($f) => $f->where('farm_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_type')) {
            $query->where('payment_type', $request->filter_type);
        }

        if ($request->filled('filter_labour')) {
            $query->where('labour_id', $request->filter_labour);
        }

        if ($request->filled('filter_farm')) {
            $query->where('farm_id', $request->filter_farm);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        $payments = $query->orderBy('payment_date', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $totalAmountPaid = (float) (clone $query)->whereIn('payment_type', ['Daily Wage', 'Salary', 'Advance Given', 'Bonus'])->sum('amount');
        $totalAdvanceDeducted = (float) (clone $query)->sum('advance_deducted');

        $dropdownData = $this->dropdowns();

        return view('admin.agriculture.labour-payments.index', array_merge(
            $dropdownData,
            compact('payments', 'totalAmountPaid', 'totalAdvanceDeducted')
        ));
    }

    public function create(Request $request)
    {
        $dropdowns = $this->dropdowns();
        $selectedLabour = null;
        if ($request->filled('labour_id')) {
            $selectedLabour = AgriLabour::find($request->labour_id);
        }

        return view('admin.agriculture.labour-payments.create', array_merge($dropdowns, compact('selectedLabour')));
    }

    public function store(Request $request)
    {
        $request->validate([
            'labour_id'        => 'required|exists:agri_labours,id',
            'payment_type'     => 'required|string|max:50',
            'payment_date'     => 'required|date',
            'working_days'     => 'nullable|numeric|min:0',
            'daily_wage_rate'  => 'nullable|numeric|min:0',
            'gross_amount'     => 'nullable|numeric|min:0',
            'advance_deducted' => 'nullable|numeric|min:0',
            'amount'           => 'required|numeric|min:0',
            'payment_mode_id'  => 'nullable|exists:payment_modes,id',
            'payment_mode'     => 'nullable|string|max:100',
            'reference_no'     => 'nullable|string|max:100',
            'payment_status'   => 'required|string|max:50',
            'sync_to_expense'  => 'nullable|boolean',
            'notes'            => 'nullable|string',
        ]);

        $labour = AgriLabour::findOrFail($request->labour_id);
        $user = Auth::user();
        $firmId = $labour->firm_id ?: ($user ? $user->firm_id : session('firm_id'));
        $farmId = $request->farm_id ?: $labour->farm_id;

        $paymentModeName = $request->payment_mode;
        if (!$paymentModeName && $request->filled('payment_mode_id')) {
            $paymentModeName = PaymentMode::find($request->payment_mode_id)?->name;
        }

        $syncToExpense = $request->has('sync_to_expense') ? (bool)$request->sync_to_expense : true;
        $amount = (float) $request->amount;
        $grossAmount = $request->filled('gross_amount') ? (float)$request->gross_amount : $amount;
        $advanceDeducted = $request->filled('advance_deducted') ? (float)$request->advance_deducted : 0;

        $payment = AgriLabourPayment::create([
            'firm_id'          => $firmId,
            'farm_id'          => $farmId,
            'labour_id'        => $labour->id,
            'payment_type'     => $request->payment_type,
            'payment_date'     => $request->payment_date,
            'working_days'     => (float)$request->working_days,
            'daily_wage_rate'  => (float)$request->daily_wage_rate,
            'gross_amount'     => $grossAmount,
            'advance_deducted' => $advanceDeducted,
            'amount'           => $amount,
            'payment_mode_id'  => $request->payment_mode_id ?: null,
            'payment_mode'     => $paymentModeName ?: 'Cash',
            'reference_no'     => $request->reference_no,
            'payment_status'   => $request->payment_status,
            'sync_to_expense'  => $syncToExpense,
            'notes'            => $request->notes,
            'created_by'       => Auth::id(),
        ]);

        $payment->syncFirms([$firmId]);

        // Auto-generate AgriExpense if sync_to_expense is enabled and actual money was paid
        if ($syncToExpense && $amount > 0 && in_array($request->payment_type, ['Daily Wage', 'Salary', 'Advance Given', 'Bonus'])) {
            $expDesc = $request->payment_type . ' Payment: ' . $labour->name;
            if ($request->payment_type === 'Daily Wage' && (float)$request->working_days > 0) {
                $expDesc .= ' (' . (float)$request->working_days . ' days @ ₹' . number_format((float)$request->daily_wage_rate, 2) . ')';
            }

            AgriExpense::create([
                'firm_id'           => $firmId,
                'farm_id'           => $farmId,
                'expense_date'      => $request->payment_date,
                'category'          => 'Labour',
                'expense_type'      => 'Labour Payment',
                'labour_id'         => $labour->id,
                'labour_payment_id' => $payment->id,
                'amount'            => $amount,
                'payment_mode_id'   => $request->payment_mode_id ?: null,
                'payment_method'    => $paymentModeName ?: 'Cash',
                'payment_status'    => $request->payment_status,
                'description'       => $expDesc,
                'notes'             => $request->notes ?: 'Auto-synced from Labour Payment',
                'created_by'        => Auth::id(),
            ]);
        }

        // Recalculate labour balance
        $labour->recalculateBalances();

        return redirect()->route('agriculture.labours.show', $labour->id)
            ->with('success', 'Labour payment of ₹' . number_format($amount, 2) . ' recorded and balances updated successfully.');
    }

    public function destroy(AgriLabourPayment $payment)
    {
        $this->authorise($payment);
        $labour = $payment->labour;

        // Delete auto-linked expense if exists
        AgriExpense::where('labour_payment_id', $payment->id)->delete();

        $payment->delete();

        if ($labour) {
            $labour->recalculateBalances();
        }

        return redirect()->back()
            ->with('success', 'Labour payment record deleted successfully.');
    }
}
