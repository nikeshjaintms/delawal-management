<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceiptRequest;

use App\Models\Receipt;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    private function generateReceiptNo(): string
    {
        $firmId = Auth::user()->firm_id;
        $count  = Receipt::where('firm_id', $firmId)->count() + 1;
        return 'RCT-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    private function dropdowns(): array
    {
        return [
            'paymentModes' => PaymentMode::where('firm_id', Auth::user()->firm_id)
                ->where('status', 'active')->orderBy('name')->get(),
        ];
    }

    public function index(Request $request)
    {
        $query = Receipt::with('paymentMode')->where('firm_id', Auth::user()->firm_id);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('receipt_no', 'like', "%{$s}%")
                  ->orWhere('received_from', 'like', "%{$s}%")
                  ->orWhere('reference_no', 'like', "%{$s}%");
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        $totalAmount = (clone $query)->sum('amount');
        $receipts    = $query->orderBy('receipt_date', 'desc')->paginate(15);

        return view('admin.receipts.index', compact('receipts', 'totalAmount'));
    }

    public function create()
    {
        $nextReceiptNo = $this->generateReceiptNo();
        return view('admin.receipts.create', array_merge(['nextReceiptNo' => $nextReceiptNo], $this->dropdowns()));
    }

    public function store(ReceiptRequest $request)
    {
        

        Receipt::create([
            'firm_id'         => Auth::user()->firm_id,
            'receipt_no'      => $request->receipt_no ?: $this->generateReceiptNo(),
            'receipt_date'    => $request->receipt_date,
            'received_from'   => $request->received_from,
            'amount'          => $request->amount,
            'payment_mode_id' => $request->payment_mode_id ?: null,
            'reference_no'    => $request->reference_no,
            'remarks'         => $request->remarks,
            'status'          => $request->status,
        ]);

        return redirect()->route('receipts.index')->with('success', 'Receipt created successfully.');
    }

    public function show(Receipt $receipt)
    {
        if ($receipt->firm_id != Auth::user()->firm_id) abort(403);
        $receipt->load('paymentMode');
        return view('admin.receipts.show', compact('receipt'));
    }

    public function edit(Receipt $receipt)
    {
        if ($receipt->firm_id != Auth::user()->firm_id) abort(403);
        return view('admin.receipts.edit', array_merge(['receipt' => $receipt], $this->dropdowns()));
    }

    public function update(ReceiptRequest $request, Receipt $receipt)
    {
        if ($receipt->firm_id != Auth::user()->firm_id) abort(403);

        

        $receipt->update([
            'receipt_date'    => $request->receipt_date,
            'received_from'   => $request->received_from,
            'amount'          => $request->amount,
            'payment_mode_id' => $request->payment_mode_id ?: null,
            'reference_no'    => $request->reference_no,
            'remarks'         => $request->remarks,
            'status'          => $request->status,
        ]);

        return redirect()->route('receipts.index')->with('success', 'Receipt updated successfully.');
    }

    public function destroy(Receipt $receipt)
    {
        if ($receipt->firm_id != Auth::user()->firm_id) abort(403);
        $receipt->delete();
        return redirect()->route('receipts.index')->with('success', 'Receipt deleted successfully.');
    }
}
