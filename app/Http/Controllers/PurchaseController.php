<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use App\Models\Vendor;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    const PAYMENT_MODES = ['Cash', 'Bank Transfer', 'UPI', 'Cheque', 'Other'];

    private function authorise(Purchase $purchase): void
    {
        $user = Auth::user();
        if ($user && !$user->isAdmin()) {
            $userFirmId = $user->firm_id;
            if ($purchase->firm_id != $userFirmId && !$purchase->firms->contains($userFirmId)) {
                abort(403);
            }
        }
    }

    private function dropdowns($selectedFirmId = null): array
    {
        $user   = Auth::user();
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
        $query = Purchase::with(['firms', 'firm', 'vendor']);

        if (!Auth::user()->isAdmin()) {
            $query->forFirms([Auth::user()->firm_id]);
        } elseif ($request->filled('firm_ids') || $request->filled('firm_id')) {
            $firmIds = $request->input('firm_ids', (array)$request->firm_id);
            $query->forFirms($firmIds);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('item_name', 'like', "%{$s}%")
                  ->orWhere('reference_no', 'like', "%{$s}%")
                  ->orWhere('payment_status', 'like', "%{$s}%")
                  ->orWhereHas('vendor', fn($v) => $v->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('firms', fn($f) => $f->where('firm_name', 'like', "%{$s}%"))
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('payment_status', $request->filter_status);
        }

        $totalAmount = (clone $query)->sum('purchase_amount');
        $purchases   = $query->orderBy('purchase_date', 'desc')->paginate(15)->withQueryString();
        $firms       = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.purchases.index', compact('purchases', 'firms', 'totalAmount'));
    }

    public function create()
    {
        return view('admin.purchases.create', $this->dropdowns());
    }

    public function store(PurchaseRequest $request)
    {
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? Auth::user()->firm_id));
        $primaryFirmId = reset($firmIds) ?: Auth::user()->firm_id;

        $purchase = Purchase::create([
            'firm_id'         => $primaryFirmId,
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

        $purchase->syncFirms($firmIds);

        return redirect()->route('purchases.index')->with('success', 'Purchase record added successfully.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['firms', 'firm', 'vendor']);
        $this->authorise($purchase);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $purchase->load(['firms', 'firm']);
        $this->authorise($purchase);
        return view('admin.purchases.edit', array_merge(['purchase' => $purchase], $this->dropdowns($purchase->firm_id)));
    }

    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        $purchase->load(['firms', 'firm']);
        $this->authorise($purchase);

        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $purchase->firm_id ?? Auth::user()->firm_id));
        $primaryFirmId = reset($firmIds) ?: $purchase->firm_id;

        $purchase->update([
            'firm_id'         => $primaryFirmId,
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

        $purchase->syncFirms($firmIds);

        return redirect()->route('purchases.index')->with('success', 'Purchase record updated successfully.');
    }

    public function destroy(Purchase $purchase)
    {
        $this->authorise($purchase);
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Purchase record deleted successfully.');
    }
}
