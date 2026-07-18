<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;

use App\Models\Purchase;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    const PAYMENT_MODES = ['Cash', 'Bank Transfer', 'UPI', 'Cheque', 'Other'];

    private function dropdowns(): array
    {
        return [
            'vendors' => Vendor::where('firm_id', Auth::user()->firm_id)
                ->where('status', 'active')->orderBy('name')->get(),
        ];
    }

    public function index(Request $request)
    {
        $query = Purchase::with('vendor')->where('firm_id', Auth::user()->firm_id);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('item_name', 'like', "%{$s}%")
                  ->orWhere('reference_no', 'like', "%{$s}%")
                  ->orWhere('payment_status', 'like', "%{$s}%")
                  ->orWhereHas('vendor', fn($v) => $v->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('payment_status', $request->filter_status);
        }

        $totalAmount = (clone $query)->sum('purchase_amount');
        $purchases   = $query->orderBy('purchase_date', 'desc')->paginate(15);

        return view('admin.purchases.index', compact('purchases', 'totalAmount'));
    }

    public function create()
    {
        return view('admin.purchases.create', $this->dropdowns());
    }

    public function store(PurchaseRequest $request)
    {
        

        Purchase::create([
            'firm_id'         => Auth::user()->firm_id,
            'vendor_id'       => $request->vendor_id ?: null,
            'item_name'       => $request->item_name,
            'purchase_date'   => $request->purchase_date,
            'purchase_amount' => $request->purchase_amount,
            'quantity'        => $request->quantity ?? 1,
            'payment_mode'    => $request->payment_mode,
            'payment_status'  => $request->payment_status,
            'reference_no'    => $request->reference_no,
            'remarks'         => $request->remarks,
            'status'          => $request->status,
        ]);

        return redirect()->route('purchases.index')->with('success', 'Purchase record added successfully.');
    }

    public function show(Purchase $purchase)
    {
        if ($purchase->firm_id != Auth::user()->firm_id) abort(403);
        $purchase->load('vendor');
        return view('admin.purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        if ($purchase->firm_id != Auth::user()->firm_id) abort(403);
        return view('admin.purchases.edit', array_merge(['purchase' => $purchase], $this->dropdowns()));
    }

    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        if ($purchase->firm_id != Auth::user()->firm_id) abort(403);

        

        $purchase->update([
            'vendor_id'       => $request->vendor_id ?: null,
            'item_name'       => $request->item_name,
            'purchase_date'   => $request->purchase_date,
            'purchase_amount' => $request->purchase_amount,
            'quantity'        => $request->quantity ?? 1,
            'payment_mode'    => $request->payment_mode,
            'payment_status'  => $request->payment_status,
            'reference_no'    => $request->reference_no,
            'remarks'         => $request->remarks,
            'status'          => $request->status,
        ]);

        return redirect()->route('purchases.index')->with('success', 'Purchase record updated successfully.');
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->firm_id != Auth::user()->firm_id) abort(403);
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Purchase record deleted successfully.');
    }
}
