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
    const PROPERTY_TYPES = ['Plot', 'Flat', 'House', 'Commercial', 'Land', 'Other'];
    const AREA_UNITS = ['Sq.Ft', 'Sq.Yd', 'Sq.Mtr', 'Acre'];

    private function authorise(Purchase $purchase): void
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            if ($purchase->firm_id != $firmId && !$purchase->firms->contains($firmId)) {
                abort(403);
            }
        }
    }

    private function dropdowns($selectedFirmId = null): array
    {
        $user   = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return [
            'firms'         => $firms,
            'propertyTypes' => self::PROPERTY_TYPES,
            'areaUnits'     => self::AREA_UNITS,
        ];
    }

    public function index(Request $request)
    {
        $query = Purchase::with(['firms', 'firm']);

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
                $q->where('property_name', 'like', "%{$s}%")
                  ->orWhere('property_code', 'like', "%{$s}%")
                  ->orWhere('location', 'like', "%{$s}%")
                  ->orWhere('survey_no', 'like', "%{$s}%")
                  ->orWhere('tp_no', 'like', "%{$s}%")
                  ->orWhere('fp_no', 'like', "%{$s}%")
                  ->orWhere('item_name', 'like', "%{$s}%")
                  ->orWhere('address', 'like', "%{$s}%")
                  ->orWhereHas('firms', fn($f) => $f->where('firm_name', 'like', "%{$s}%"))
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        $purchases     = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $firms         = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $propertyTypes = self::PROPERTY_TYPES;

        return view('admin.purchases.index', compact('purchases', 'firms', 'propertyTypes'));
    }

    public function create()
    {
        return view('admin.purchases.create', $this->dropdowns());
    }

    public function store(PurchaseRequest $request)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $firmId;

        $propertyName = $request->property_name ?: $request->item_name;

        $purchase = Purchase::create([
            'firm_id'         => $primaryFirmId,
            'vendor_id'       => $request->vendor_id ?: null,
            'property_name'   => $propertyName,
            'property_type'   => $request->property_type,
            'property_code'   => $request->property_code,
            'location'        => $request->location,
            'address'         => $request->address,
            'survey_no'       => $request->survey_no,
            'tp_no'           => $request->tp_no,
            'fp_no'           => $request->fp_no,
            'area'            => $request->area,
            'area_unit'       => $request->area_unit ?: 'Sq.Ft',
            'item_name'       => $propertyName,
            'purchase_date'   => $request->purchase_date ?: date('Y-m-d'),
            'purchase_amount' => $request->purchase_amount ?: 0,
            'quantity'        => 1,
            'payment_mode'    => $request->payment_mode,
            'payment_status'  => $request->payment_status ?: 'unpaid',
            'reference_no'    => $request->reference_no,
            'remarks'         => $request->remarks,
            'status'          => $request->status ?: 'active',
        ]);

        $purchase->syncFirms($firmIds);

        return redirect()->route('purchases.index')->with('success', "Property Buy record '{$propertyName}' added successfully.");
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['firms', 'firm']);
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

        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $purchase->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $purchase->firm_id;

        $propertyName = $request->property_name ?: ($request->item_name ?: $purchase->property_name);

        $purchase->update([
            'firm_id'         => $primaryFirmId,
            'vendor_id'       => $request->vendor_id ?: $purchase->vendor_id,
            'property_name'   => $propertyName,
            'property_type'   => $request->property_type,
            'property_code'   => $request->property_code,
            'location'        => $request->location,
            'address'         => $request->address,
            'survey_no'       => $request->survey_no,
            'tp_no'           => $request->tp_no,
            'fp_no'           => $request->fp_no,
            'area'            => $request->area,
            'area_unit'       => $request->area_unit ?: ($purchase->area_unit ?? 'Sq.Ft'),
            'item_name'       => $propertyName,
            'purchase_date'   => $request->purchase_date ?: $purchase->purchase_date,
            'purchase_amount' => $request->purchase_amount !== null ? $request->purchase_amount : $purchase->purchase_amount,
            'quantity'        => 1,
            'payment_mode'    => $request->payment_mode ?: $purchase->payment_mode,
            'payment_status'  => $request->payment_status ?: ($purchase->payment_status ?? 'unpaid'),
            'reference_no'    => $request->reference_no ?: $purchase->reference_no,
            'remarks'         => $request->remarks,
            'status'          => $request->status ?: ($purchase->status ?? 'active'),
        ]);

        $purchase->syncFirms($firmIds);

        return redirect()->route('purchases.index')->with('success', "Property Buy record '{$propertyName}' updated successfully.");
    }

    public function destroy(Purchase $purchase)
    {
        $this->authorise($purchase);
        $name = $purchase->display_name;
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', "Property Buy record '{$name}' deleted successfully.");
    }
}
