<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceiptRequest;
use App\Models\Receipt;
use App\Models\PaymentMode;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    private function authorise(Receipt $receipt): void
    {
        $user = Auth::user();
        if ($user && !$user->isAdmin()) {
            $userFirmId = $user->firm_id;
            if ($receipt->firm_id != $userFirmId && !$receipt->firms->contains($userFirmId)) {
                abort(403);
            }
        }
    }

    private function generateReceiptNo($firmId = null): string
    {
        $fId   = $firmId ?? Auth::user()->firm_id;
        $count = Receipt::where('firm_id', $fId)->count() + 1;
        return 'RCT-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    private function dropdowns($selectedFirmId = null): array
    {
        $user   = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $pmQuery = PaymentMode::where('status', 'active')->orderBy('name');
        if ($firmId && (!$user || !$user->isAdmin())) {
            $pmQuery->whereHas('firms', function($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            });
        }

        return [
            'firms'        => $firms,
            'paymentModes' => $pmQuery->get(),
        ];
    }

    public function index(Request $request)
    {
        $query = Receipt::with(['firms', 'firm', 'paymentMode']);

        if (!Auth::user()->isAdmin()) {
            $query->forFirms([Auth::user()->firm_id]);
        } elseif ($request->filled('firm_ids') || $request->filled('firm_id')) {
            $firmIds = $request->input('firm_ids', (array)$request->firm_id);
            $query->forFirms($firmIds);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('receipt_no', 'like', "%{$s}%")
                  ->orWhere('received_from', 'like', "%{$s}%")
                  ->orWhere('reference_no', 'like', "%{$s}%")
                  ->orWhereHas('firms', fn($f) => $f->where('firm_name', 'like', "%{$s}%"))
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        $totalAmount = (clone $query)->sum('amount');
        $receipts    = $query->orderBy('receipt_date', 'desc')->paginate(15)->withQueryString();
        $firms       = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.receipts.index', compact('receipts', 'firms', 'totalAmount'));
    }

    public function create()
    {
        $nextReceiptNo = $this->generateReceiptNo();
        return view('admin.receipts.create', array_merge(['nextReceiptNo' => $nextReceiptNo], $this->dropdowns()));
    }

    public function store(ReceiptRequest $request)
    {
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? Auth::user()->firm_id));
        $primaryFirmId = reset($firmIds) ?: Auth::user()->firm_id;

        $receipt = Receipt::create([
            'firm_id'         => $primaryFirmId,
            'receipt_no'      => $request->receipt_no ?: $this->generateReceiptNo($primaryFirmId),
            'receipt_date'    => $request->receipt_date,
            'received_from'   => $request->received_from,
            'amount'          => $request->amount,
            'payment_mode_id' => $request->payment_mode_id ?: null,
            'reference_no'    => $request->reference_no,
            'remarks'         => $request->remarks,
            'status'          => $request->status,
        ]);

        $receipt->syncFirms($firmIds);

        return redirect()->route('receipts.index')->with('success', 'Receipt created successfully.');
    }

    public function show(Receipt $receipt)
    {
        $receipt->load(['firms', 'firm', 'paymentMode']);
        $this->authorise($receipt);
        return view('admin.receipts.show', compact('receipt'));
    }

    public function edit(Receipt $receipt)
    {
        $receipt->load(['firms', 'firm']);
        $this->authorise($receipt);
        return view('admin.receipts.edit', array_merge(['receipt' => $receipt], $this->dropdowns($receipt->firm_id)));
    }

    public function update(ReceiptRequest $request, Receipt $receipt)
    {
        $receipt->load(['firms', 'firm']);
        $this->authorise($receipt);

        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $receipt->firm_id ?? Auth::user()->firm_id));
        $primaryFirmId = reset($firmIds) ?: $receipt->firm_id;

        $receipt->update([
            'firm_id'         => $primaryFirmId,
            'receipt_date'    => $request->receipt_date,
            'received_from'   => $request->received_from,
            'amount'          => $request->amount,
            'payment_mode_id' => $request->payment_mode_id ?: null,
            'reference_no'    => $request->reference_no,
            'remarks'         => $request->remarks,
            'status'          => $request->status,
        ]);

        $receipt->syncFirms($firmIds);

        return redirect()->route('receipts.index')->with('success', 'Receipt updated successfully.');
    }

    public function destroy(Receipt $receipt)
    {
        $this->authorise($receipt);
        $receipt->delete();
        return redirect()->route('receipts.index')->with('success', 'Receipt deleted successfully.');
    }
}
