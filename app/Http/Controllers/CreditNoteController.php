<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditNoteRequest;

use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\PropertySale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditNoteController extends Controller
{
    const STATUSES = ['Pending', 'Approved', 'Rejected'];

    private function authorise(CreditNote $note): void
    {
        if ($note->firm_id !== Auth::user()->firm_id) abort(403);
    }

    private function dropdowns(): array
    {
        $firmId = Auth::user()->firm_id;
        return [
            'customers' => Customer::where('firm_id', $firmId)
                ->where('status', 'active')->orderBy('name')->get(),
            'propertySales' => PropertySale::with('property')
                ->where('firm_id', $firmId)->orderBy('sale_date', 'desc')->get(),
        ];
    }

    // ----------------------------------------------------------------
    // INDEX
    // ----------------------------------------------------------------
    public function index(Request $request)
    {
        $firmId = Auth::user()->firm_id;
        $query  = CreditNote::with('customer')->where('firm_id', $firmId);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('credit_note_no', 'like', "%{$s}%")
                  ->orWhere('related_invoice_no', 'like', "%{$s}%")
                  ->orWhere('reason', 'like', "%{$s}%")
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('filter_customer')) $query->where('customer_id', $request->filter_customer);
        if ($request->filled('filter_status'))   $query->where('status', $request->filter_status);
        if ($request->filled('from_date'))        $query->whereDate('credit_note_date', '>=', $request->from_date);
        if ($request->filled('to_date'))          $query->whereDate('credit_note_date', '<=', $request->to_date);

        $totalCredit  = (clone $query)->sum('credit_amount');
        $creditNotes  = $query->orderBy('credit_note_date', 'desc')->paginate(15);

        $customers = Customer::where('firm_id', $firmId)->orderBy('name')->get();

        return view('admin.credit-notes.index', compact('creditNotes', 'customers', 'totalCredit'));
    }

    // ----------------------------------------------------------------
    // CREATE / STORE
    // ----------------------------------------------------------------
    public function create()
    {
        return view('admin.credit-notes.create', $this->dropdowns());
    }

    public function store(CreditNoteRequest $request)
    {
        

        $cgst   = (float) ($request->cgst_amount ?? 0);
        $sgst   = (float) ($request->sgst_amount ?? 0);
        $igst   = (float) ($request->igst_amount ?? 0);
        $totGst = $cgst + $sgst + $igst;
        $credit = (float) $request->taxable_amount + $totGst;

        CreditNote::create([
            'firm_id'            => Auth::user()->firm_id,
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

    // ----------------------------------------------------------------
    // SHOW
    // ----------------------------------------------------------------
    public function show(CreditNote $creditNote)
    {
        $this->authorise($creditNote);
        $creditNote->load(['customer', 'propertySale.property']);
        return view('admin.credit-notes.show', compact('creditNote'));
    }

    // ----------------------------------------------------------------
    // EDIT / UPDATE
    // ----------------------------------------------------------------
    public function edit(CreditNote $creditNote)
    {
        $this->authorise($creditNote);
        return view('admin.credit-notes.edit', array_merge(
            ['creditNote' => $creditNote],
            $this->dropdowns()
        ));
    }

    public function update(CreditNoteRequest $request, CreditNote $creditNote)
    {
        $this->authorise($creditNote);

        

        $cgst   = (float) ($request->cgst_amount ?? 0);
        $sgst   = (float) ($request->sgst_amount ?? 0);
        $igst   = (float) ($request->igst_amount ?? 0);
        $totGst = $cgst + $sgst + $igst;
        $credit = (float) $request->taxable_amount + $totGst;

        $creditNote->update([
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

    // ----------------------------------------------------------------
    // DESTROY
    // ----------------------------------------------------------------
    public function destroy(CreditNote $creditNote)
    {
        $this->authorise($creditNote);
        $creditNote->delete();
        return redirect()->route('credit-notes.index')
            ->with('success', 'Credit note deleted successfully.');
    }
}
