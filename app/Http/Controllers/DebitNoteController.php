<?php

namespace App\Http\Controllers;

use App\Http\Requests\DebitNoteRequest;
use App\Models\DebitNote;
use App\Models\Vendor;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebitNoteController extends Controller
{
    const STATUSES = ['Pending', 'Approved', 'Rejected'];

    private function authorise(DebitNote $note): void
    {
        if (!Auth::user()->isAdmin() && $note->firm_id !== Auth::user()->firm_id) abort(403);
    }

    private function dropdowns($selectedFirmId = null): array
    {
        $user = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $vendorQuery = Vendor::where('status', 'active')->orderBy('name');
        if ($firmId && (!$user || !$user->isAdmin())) {
            $vendorQuery->where('firm_id', $firmId);
        }

        return [
            'firms'   => $firms,
            'vendors' => $vendorQuery->get(),
        ];
    }

    public function index(Request $request)
    {
        $query = DebitNote::with(['firm', 'vendor']);

        if (!Auth::user()->isAdmin()) {
            $query->where('firm_id', Auth::user()->firm_id);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('debit_note_no', 'like', "%{$s}%")
                  ->orWhere('related_bill_no', 'like', "%{$s}%")
                  ->orWhere('reason', 'like', "%{$s}%")
                  ->orWhereHas('vendor', fn($v) => $v->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('filter_vendor'))  $query->where('vendor_id', $request->filter_vendor);
        if ($request->filled('filter_status'))  $query->where('status', $request->filter_status);
        if ($request->filled('from_date'))       $query->whereDate('debit_note_date', '>=', $request->from_date);
        if ($request->filled('to_date'))         $query->whereDate('debit_note_date', '<=', $request->to_date);

        $totalDebit  = (clone $query)->sum('debit_amount');
        $debitNotes  = $query->orderBy('debit_note_date', 'desc')->paginate(15)->withQueryString();

        $vendorQuery = Vendor::orderBy('name');
        if (!Auth::user()->isAdmin()) {
            $vendorQuery->where('firm_id', Auth::user()->firm_id);
        }
        $vendors = $vendorQuery->get();
        $firms   = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.debit-notes.index', compact('debitNotes', 'vendors', 'firms', 'totalDebit'));
    }

    public function create()
    {
        return view('admin.debit-notes.create', $this->dropdowns());
    }

    public function store(DebitNoteRequest $request)
    {
        $firmId = $request->firm_id ?? Auth::user()->firm_id;

        $cgst   = (float) ($request->cgst_amount ?? 0);
        $sgst   = (float) ($request->sgst_amount ?? 0);
        $igst   = (float) ($request->igst_amount ?? 0);
        $totGst = $cgst + $sgst + $igst;
        $debit  = (float) $request->taxable_amount + $totGst;

        DebitNote::create([
            'firm_id'          => $firmId,
            'debit_note_no'    => $request->debit_note_no,
            'debit_note_date'  => $request->debit_note_date,
            'vendor_id'        => $request->vendor_id ?: null,
            'related_bill_no'  => $request->related_bill_no,
            'reason'           => $request->reason,
            'taxable_amount'   => $request->taxable_amount,
            'cgst_rate'        => $request->cgst_rate,
            'cgst_amount'      => $cgst,
            'sgst_rate'        => $request->sgst_rate,
            'sgst_amount'      => $sgst,
            'igst_rate'        => $request->igst_rate,
            'igst_amount'      => $igst,
            'total_gst'        => $totGst,
            'debit_amount'     => $debit,
            'status'           => $request->status,
            'notes'            => $request->notes,
        ]);

        return redirect()->route('debit-notes.index')
            ->with('success', 'Debit note added successfully.');
    }

    public function show(DebitNote $debitNote)
    {
        $this->authorise($debitNote);
        $debitNote->load(['firm', 'vendor']);
        return view('admin.debit-notes.show', compact('debitNote'));
    }

    public function edit(DebitNote $debitNote)
    {
        $this->authorise($debitNote);
        return view('admin.debit-notes.edit', array_merge(
            ['debitNote' => $debitNote],
            $this->dropdowns($debitNote->firm_id)
        ));
    }

    public function update(DebitNoteRequest $request, DebitNote $debitNote)
    {
        $this->authorise($debitNote);

        $firmId = $request->firm_id ?? $debitNote->firm_id;

        $cgst   = (float) ($request->cgst_amount ?? 0);
        $sgst   = (float) ($request->sgst_amount ?? 0);
        $igst   = (float) ($request->igst_amount ?? 0);
        $totGst = $cgst + $sgst + $igst;
        $debit  = (float) $request->taxable_amount + $totGst;

        $debitNote->update([
            'firm_id'          => $firmId,
            'debit_note_no'    => $request->debit_note_no,
            'debit_note_date'  => $request->debit_note_date,
            'vendor_id'        => $request->vendor_id ?: null,
            'related_bill_no'  => $request->related_bill_no,
            'reason'           => $request->reason,
            'taxable_amount'   => $request->taxable_amount,
            'cgst_rate'        => $request->cgst_rate,
            'cgst_amount'      => $cgst,
            'sgst_rate'        => $request->sgst_rate,
            'sgst_amount'      => $sgst,
            'igst_rate'        => $request->igst_rate,
            'igst_amount'      => $igst,
            'total_gst'        => $totGst,
            'debit_amount'     => $debit,
            'status'           => $request->status,
            'notes'            => $request->notes,
        ]);

        return redirect()->route('debit-notes.index')
            ->with('success', 'Debit note updated successfully.');
    }

    public function destroy(DebitNote $debitNote)
    {
        $this->authorise($debitNote);
        $debitNote->delete();

        return redirect()->route('debit-notes.index')
            ->with('success', 'Debit note deleted successfully.');
    }
}
