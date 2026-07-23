<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditNoteRequest;
use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\PropertySale;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditNoteController extends Controller
{
    const STATUSES = ['Pending', 'Approved', 'Rejected'];

    private function authorise(CreditNote $note): void
    {
        if (!Auth::user()->isAdmin() && $note->firm_id !== Auth::user()->firm_id) abort(403);
    }

    private function dropdowns($selectedFirmId = null): array
    {
        $user = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $custQuery = Customer::where('status', 'active')->orderBy('name');
        $saleQuery = PropertySale::with('property')->orderBy('sale_date', 'desc');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $custQuery->where('firm_id', $firmId);
            $saleQuery->where('firm_id', $firmId);
        }

        return [
            'firms'         => $firms,
            'customers'     => $custQuery->get(),
            'propertySales' => $saleQuery->get(),
        ];
    }

    public function index(Request $request)
    {
        $query = CreditNote::with(['firm', 'customer']);

        if (!Auth::user()->isAdmin()) {
            $query->where('firm_id', Auth::user()->firm_id);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('credit_note_no', 'like', "%{$s}%")
                  ->orWhere('related_invoice_no', 'like', "%{$s}%")
                  ->orWhere('reason', 'like', "%{$s}%")
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('filter_customer')) $query->where('customer_id', $request->filter_customer);
        if ($request->filled('filter_status'))   $query->where('status', $request->filter_status);
        if ($request->filled('from_date'))        $query->whereDate('credit_note_date', '>=', $request->from_date);
        if ($request->filled('to_date'))          $query->whereDate('credit_note_date', '<=', $request->to_date);

        $totalCredit  = (clone $query)->sum('credit_amount');
        $creditNotes  = $query->orderBy('credit_note_date', 'desc')->paginate(15)->withQueryString();

        $custQuery = Customer::orderBy('name');
        if (!Auth::user()->isAdmin()) {
            $custQuery->where('firm_id', Auth::user()->firm_id);
        }
        $customers = $custQuery->get();
        $firms     = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.credit-notes.index', compact('creditNotes', 'customers', 'firms', 'totalCredit'));
    }

    public function create()
    {
        return view('admin.credit-notes.create', $this->dropdowns());
    }

    public function store(CreditNoteRequest $request)
    {
        $firmId = $request->firm_id ?? Auth::user()->firm_id;

        $cgst   = (float) ($request->cgst_amount ?? 0);
        $sgst   = (float) ($request->sgst_amount ?? 0);
        $igst   = (float) ($request->igst_amount ?? 0);
        $totGst = $cgst + $sgst + $igst;
        $credit = (float) $request->taxable_amount + $totGst;

        CreditNote::create([
            'firm_id'            => $firmId,
            'credit_note_no'     => $request->credit_note_no,
            'credit_note_date'   => $request->credit_note_date,
            'customer_id'        => $request->customer_id   ?: null,
            'property_sale_id'   => $request->property_sale_id ?: null,
            'related_invoice_no' => $request->related_invoice_no,
            'reason'             => $request->reason,
            'taxable_amount'     => $request->taxable_amount,
            'cgst_rate'          => $request->cgst_rate,
            'cgst_amount'        => $cgst,
            'sgst_rate'          => $request->sgst_rate,
            'sgst_amount'        => $sgst,
            'igst_rate'          => $request->igst_rate,
            'igst_amount'        => $igst,
            'total_gst'          => $totGst,
            'credit_amount'      => $credit,
            'status'             => $request->status,
            'notes'              => $request->notes,
        ]);

        return redirect()->route('credit-notes.index')
            ->with('success', 'Credit note added successfully.');
    }

    public function show(CreditNote $creditNote)
    {
        $this->authorise($creditNote);
        $creditNote->load(['firm', 'customer', 'propertySale.property']);
        return view('admin.credit-notes.show', compact('creditNote'));
    }

    public function edit(CreditNote $creditNote)
    {
        $this->authorise($creditNote);
        return view('admin.credit-notes.edit', array_merge(
            ['creditNote' => $creditNote],
            $this->dropdowns($creditNote->firm_id)
        ));
    }

    public function update(CreditNoteRequest $request, CreditNote $creditNote)
    {
        $this->authorise($creditNote);

        $firmId = $request->firm_id ?? $creditNote->firm_id;

        $cgst   = (float) ($request->cgst_amount ?? 0);
        $sgst   = (float) ($request->sgst_amount ?? 0);
        $igst   = (float) ($request->igst_amount ?? 0);
        $totGst = $cgst + $sgst + $igst;
        $credit = (float) $request->taxable_amount + $totGst;

        $creditNote->update([
            'firm_id'            => $firmId,
            'credit_note_no'     => $request->credit_note_no,
            'credit_note_date'   => $request->credit_note_date,
            'customer_id'        => $request->customer_id   ?: null,
            'property_sale_id'   => $request->property_sale_id ?: null,
            'related_invoice_no' => $request->related_invoice_no,
            'reason'             => $request->reason,
            'taxable_amount'     => $request->taxable_amount,
            'cgst_rate'          => $request->cgst_rate,
            'cgst_amount'        => $cgst,
            'sgst_rate'          => $request->sgst_rate,
            'sgst_amount'        => $sgst,
            'igst_rate'          => $request->igst_rate,
            'igst_amount'        => $igst,
            'total_gst'          => $totGst,
            'credit_amount'      => $credit,
            'status'             => $request->status,
            'notes'              => $request->notes,
        ]);

        return redirect()->route('credit-notes.index')
            ->with('success', 'Credit note updated successfully.');
    }

    public function destroy(CreditNote $creditNote)
    {
        $this->authorise($creditNote);
        $creditNote->delete();

        return redirect()->route('credit-notes.index')
            ->with('success', 'Credit note deleted successfully.');
    }
}
