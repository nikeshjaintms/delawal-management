<?php

namespace App\Http\Controllers;

use App\Http\Requests\LedgerRequest;

use App\Models\Ledger;
use App\Models\Property;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Broker;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LedgerController extends Controller
{
    const TRANSACTION_TYPES = [
        'Sale', 'Payment Received', 'Expense', 'Purchase',
        'Rent Received', 'Loan EMI', 'Other',
    ];

    private function authContext(): array
    {
        $user = Auth::user();
        $isFirm = session('login_type') === 'firm' && session('firm_id');
        $isAdmin = !$isFirm && $user && $user->isAdmin();
        $firmId = $isFirm ? (int) session('firm_id') : ($user && $user->firm_id ? (int) $user->firm_id : null);

        return [$isAdmin, $firmId];
    }

    // ----------------------------------------------------------------
    // Shared dropdown data
    // ----------------------------------------------------------------
    private function dropdowns(): array
    {
        [$isAdmin, $firmId] = $this->authContext();

        $propQuery = Property::with(['project.propertyMaster'])->orderBy('property_name');
        $custQuery = Customer::where('status', 'active')->orderBy('name');
        $vendQuery = Vendor::where('status', 'active')->orderBy('name');
        $brokQuery = Broker::where('status', 'active')->orderBy('name');
        $pmQuery   = PaymentMode::where('status', 'active')->orderBy('name');

        if (!$isAdmin && $firmId) {
            $propQuery->where('firm_id', $firmId);
            $custQuery->where('firm_id', $firmId);
            $vendQuery->where('firm_id', $firmId);
            $brokQuery->where('firm_id', $firmId);
            $pmQuery->whereHas('firms', function ($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            });
        }

        return [
            'properties'   => $propQuery->get(),
            'customers'    => $custQuery->get(),
            'vendors'      => $vendQuery->get(),
            'brokers'      => $brokQuery->get(),
            'paymentModes' => $pmQuery->get(),
        ];
    }

    private function authorise(Ledger $ledger): void
    {
        [$isAdmin, $firmId] = $this->authContext();
        if (!$isAdmin && (int) $ledger->firm_id !== (int) $firmId) {
            abort(403);
        }
    }

    // ----------------------------------------------------------------
    // INDEX
    // ----------------------------------------------------------------
    public function index(Request $request)
    {
        [$isAdmin, $firmId] = $this->authContext();

        $query = Ledger::with(['property', 'customer', 'vendor', 'broker', 'firm']);
        if (!$isAdmin && $firmId) {
            $query->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('transaction_title', 'like', "%{$s}%")
                  ->orWhere('reference_no',    'like', "%{$s}%")
                  ->orWhere('remarks',         'like', "%{$s}%")
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('vendor',   fn($v) => $v->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('from_date'))        $query->where('ledger_date', '>=', $request->from_date);
        if ($request->filled('to_date'))          $query->where('ledger_date', '<=', $request->to_date);
        if ($request->filled('filter_property'))  $query->where('property_id', $request->filter_property);
        if ($request->filled('filter_customer'))  $query->where('customer_id', $request->filter_customer);
        if ($request->filled('filter_vendor'))    $query->where('vendor_id',   $request->filter_vendor);
        if ($request->filled('filter_broker'))    $query->where('broker_id',   $request->filter_broker);
        if ($request->filled('filter_type'))      $query->where('transaction_type', $request->filter_type);
        if ($request->filled('filter_mode'))      $query->where('payment_mode',     $request->filter_mode);

        // Totals before pagination
        $totalDebit  = (clone $query)->sum('debit_amount');
        $totalCredit = (clone $query)->sum('credit_amount');
        $balance     = $totalCredit - $totalDebit;

        $ledgers = $query->orderBy('ledger_date', 'desc')->orderBy('id', 'desc')->paginate(15);

        $dd = $this->dropdowns();

        return view('admin.ledgers.index', array_merge(
            compact('ledgers', 'totalDebit', 'totalCredit', 'balance'),
            $dd
        ));
    }

    // ----------------------------------------------------------------
    // CREATE / STORE
    // ----------------------------------------------------------------
    public function create()
    {
        return view('admin.ledgers.create', $this->dropdowns());
    }

    public function store(LedgerRequest $request)
    {
        [$isAdmin, $firmId] = $this->authContext();
        $resolvedFirmId = $request->input('firm_id') ?: ($firmId ?: \App\Models\Firm::value('id'));

        Ledger::create([
            'firm_id'           => $resolvedFirmId,
            'ledger_date'       => $request->ledger_date,
            'property_id'       => $request->property_id ?: null,
            'customer_id'       => $request->customer_id ?: null,
            'vendor_id'         => $request->vendor_id   ?: null,
            'broker_id'         => $request->broker_id   ?: null,
            'transaction_type'  => $request->transaction_type,
            'transaction_title' => $request->transaction_title,
            'debit_amount'      => $request->debit_amount  ?? 0,
            'credit_amount'     => $request->credit_amount ?? 0,
            'payment_mode'      => $request->payment_mode,
            'reference_no'      => $request->reference_no,
            'remarks'           => $request->remarks,
        ]);

        return redirect()->route('ledgers.index')
            ->with('success', 'Ledger entry added successfully.');
    }

    // ----------------------------------------------------------------
    // SHOW
    // ----------------------------------------------------------------
    public function show(Ledger $ledger)
    {
        $this->authorise($ledger);
        $ledger->load(['property', 'customer', 'vendor', 'broker']);
        return view('admin.ledgers.show', compact('ledger'));
    }

    // ----------------------------------------------------------------
    // EDIT / UPDATE
    // ----------------------------------------------------------------
    public function edit(Ledger $ledger)
    {
        $this->authorise($ledger);
        return view('admin.ledgers.edit', array_merge(
            ['ledger' => $ledger],
            $this->dropdowns()
        ));
    }

    public function update(LedgerRequest $request, Ledger $ledger)
    {
        $this->authorise($ledger);

        

        $ledger->update([
            'ledger_date'       => $request->ledger_date,
            'property_id'       => $request->property_id ?: null,
            'customer_id'       => $request->customer_id ?: null,
            'vendor_id'         => $request->vendor_id   ?: null,
            'broker_id'         => $request->broker_id   ?: null,
            'transaction_type'  => $request->transaction_type,
            'transaction_title' => $request->transaction_title,
            'debit_amount'      => $request->debit_amount  ?? 0,
            'credit_amount'     => $request->credit_amount ?? 0,
            'payment_mode'      => $request->payment_mode,
            'reference_no'      => $request->reference_no,
            'remarks'           => $request->remarks,
        ]);

        return redirect()->route('ledgers.index')
            ->with('success', 'Ledger entry updated successfully.');
    }

    // ----------------------------------------------------------------
    // DESTROY
    // ----------------------------------------------------------------
    public function destroy(Ledger $ledger)
    {
        $this->authorise($ledger);
        $ledger->delete();
        return redirect()->route('ledgers.index')
            ->with('success', 'Ledger entry deleted successfully.');
    }
}
