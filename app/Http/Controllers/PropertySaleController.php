<?php

namespace App\Http\Controllers;

use App\Models\PropertySale;
use App\Models\Property;
use App\Models\Customer;
use App\Models\Broker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertySaleController extends Controller
{
    private function getDropdownData()
    {
        $firmId = Auth::user()->firm_id;

        $properties = Property::where('firm_id', $firmId)
            ->orderBy('property_name')
            ->get();

        $customers = Customer::where('firm_id', $firmId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $brokers = Broker::where('firm_id', $firmId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return compact('properties', 'customers', 'brokers');
    }

    private function updatePropertyStatus(PropertySale $sale)
    {
        $property = Property::find($sale->property_id);
        if (!$property) return;

        $statusMap = [
            'booked'    => 'booked',
            'sold'      => 'sold',
            'cancelled' => 'available',
        ];

        if (isset($statusMap[$sale->sale_status])) {
            $property->update(['status' => $statusMap[$sale->sale_status]]);
        }
    }

    public function index(Request $request)
    {
        $query = PropertySale::with(['property', 'customer', 'broker'])
            ->where('firm_id', Auth::user()->firm_id);

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('property', function ($p) use ($search) {
                    $p->where('property_name', 'like', "%{$search}%")
                      ->orWhere('property_code', 'like', "%{$search}%");
                })
                ->orWhereHas('customer', function ($c) use ($search) {
                    $c->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('broker', function ($b) use ($search) {
                    $b->where('name', 'like', "%{$search}%");
                })
                ->orWhere('payment_status', 'like', "%{$search}%")
                ->orWhere('sale_status', 'like', "%{$search}%");
            });
        }

        $propertySales = $query->latest()->paginate(10);

        return view('admin.property-sales.index', compact('propertySales'));
    }

    public function create()
    {
        return view('admin.property-sales.create', $this->getDropdownData());
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id'      => 'required',
            'customer_id'      => 'required',
            'broker_id'        => 'nullable',
            'sale_date'        => 'nullable|date',
            'sale_amount'      => 'nullable|numeric',
            'booking_amount'   => 'nullable|numeric',
            'remaining_amount' => 'nullable|numeric',
            'payment_status'   => 'required',
            'sale_status'      => 'required',
            'agreement_file'   => 'nullable|file',
            'note'             => 'nullable',
        ]);

        $agreementPath = null;
        if ($request->hasFile('agreement_file')) {
            $agreementPath = $request->file('agreement_file')->store('property-agreements', 'public');
        }

        $sale = PropertySale::create([
            'firm_id'          => Auth::user()->firm_id,
            'property_id'      => $request->property_id,
            'customer_id'      => $request->customer_id,
            'broker_id'        => $request->broker_id ?: null,
            'sale_date'        => $request->sale_date,
            'sale_amount'      => $request->sale_amount,
            'booking_amount'   => $request->booking_amount,
            'remaining_amount' => $request->remaining_amount,
            'payment_status'   => $request->payment_status,
            'sale_status'      => $request->sale_status,
            'agreement_file'   => $agreementPath,
            'note'             => $request->note,
        ]);

        $this->updatePropertyStatus($sale);

        return redirect()->route('property-sales.index')->with('success', 'Property sale added successfully.');
    }

    public function show(PropertySale $propertySale)
    {
        if ($propertySale->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        $propertySale->load(['property.propertyType', 'customer', 'broker']);

        return view('admin.property-sales.show', compact('propertySale'));
    }

    public function edit(PropertySale $propertySale)
    {
        if ($propertySale->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        return view('admin.property-sales.edit', array_merge(
            ['propertySale' => $propertySale],
            $this->getDropdownData()
        ));
    }

    public function update(Request $request, PropertySale $propertySale)
    {
        if ($propertySale->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        $request->validate([
            'property_id'      => 'required',
            'customer_id'      => 'required',
            'broker_id'        => 'nullable',
            'sale_date'        => 'nullable|date',
            'sale_amount'      => 'nullable|numeric',
            'booking_amount'   => 'nullable|numeric',
            'remaining_amount' => 'nullable|numeric',
            'payment_status'   => 'required',
            'sale_status'      => 'required',
            'agreement_file'   => 'nullable|file',
            'note'             => 'nullable',
        ]);

        $agreementPath = $propertySale->agreement_file;
        if ($request->hasFile('agreement_file')) {
            if ($propertySale->agreement_file) {
                Storage::disk('public')->delete($propertySale->agreement_file);
            }
            $agreementPath = $request->file('agreement_file')->store('property-agreements', 'public');
        }

        $propertySale->update([
            'property_id'      => $request->property_id,
            'customer_id'      => $request->customer_id,
            'broker_id'        => $request->broker_id ?: null,
            'sale_date'        => $request->sale_date,
            'sale_amount'      => $request->sale_amount,
            'booking_amount'   => $request->booking_amount,
            'remaining_amount' => $request->remaining_amount,
            'payment_status'   => $request->payment_status,
            'sale_status'      => $request->sale_status,
            'agreement_file'   => $agreementPath,
            'note'             => $request->note,
        ]);

        $this->updatePropertyStatus($propertySale);

        return redirect()->route('property-sales.index')->with('success', 'Property sale updated successfully.');
    }

    public function destroy(PropertySale $propertySale)
    {
        if ($propertySale->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        if ($propertySale->agreement_file) {
            Storage::disk('public')->delete($propertySale->agreement_file);
        }

        // Restore property to available when sale is deleted
        $property = Property::find($propertySale->property_id);
        if ($property) {
            $property->update(['status' => 'available']);
        }

        $propertySale->delete();

        return redirect()->route('property-sales.index')->with('success', 'Property sale deleted successfully.');
    }
}
