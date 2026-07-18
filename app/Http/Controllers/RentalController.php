<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentalRequest;

use App\Models\Rental;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalController extends Controller
{
    private function updatePropertyStatus(Rental $rental)
    {
        $property = Property::find($rental->property_id);
        if (!$property) return;

        if ($rental->rental_status === 'active') {
            $property->update(['status' => 'rented']);
        } elseif (in_array($rental->rental_status, ['completed', 'cancelled'])) {
            $property->update(['status' => 'available']);
        }
    }

    public function index(Request $request)
    {
        $query = Rental::with('property')
            ->where('firm_id', Auth::user()->firm_id);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('tenant_name', 'like', '%' . $request->search . '%')
                  ->orWhere('tenant_mobile', 'like', '%' . $request->search . '%')
                  ->orWhere('payment_status', 'like', '%' . $request->search . '%')
                  ->orWhere('rental_status', 'like', '%' . $request->search . '%')
                  ->orWhereHas('property', fn($p) =>
                      $p->where('property_name', 'like', '%' . $request->search . '%')
                        ->orWhere('property_code', 'like', '%' . $request->search . '%')
                  );
            });
        }

        $rentals = $query->latest()->paginate(10);

        return view('admin.rentals.index', compact('rentals'));
    }

    public function create()
    {
        $properties = Property::where('firm_id', Auth::user()->firm_id)
            ->orderBy('property_name')
            ->get();

        return view('admin.rentals.create', compact('properties'));
    }

    public function store(RentalRequest $request)
    {
        

        $rental = Rental::create([
            'firm_id'          => Auth::user()->firm_id,
            'property_id'      => $request->property_id,
            'tenant_name'      => $request->tenant_name,
            'tenant_mobile'    => $request->tenant_mobile,
            'tenant_email'     => $request->tenant_email,
            'rent_amount'      => $request->rent_amount,
            'security_deposit' => $request->security_deposit,
            'rent_start_date'  => $request->rent_start_date,
            'rent_end_date'    => $request->rent_end_date,
            'rent_due_date'    => $request->rent_due_date,
            'payment_status'   => $request->payment_status,
            'rental_status'    => $request->rental_status,
            'remarks'          => $request->remarks,
        ]);

        $this->updatePropertyStatus($rental);

        return redirect()->route('rentals.index')->with('success', 'Rental record added successfully.');
    }

    public function show(Rental $rental)
    {
        if ($rental->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        $rental->load('property.propertyType');

        return view('admin.rentals.show', compact('rental'));
    }

    public function edit(Rental $rental)
    {
        if ($rental->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        $properties = Property::where('firm_id', Auth::user()->firm_id)
            ->orderBy('property_name')
            ->get();

        return view('admin.rentals.edit', compact('rental', 'properties'));
    }

    public function update(RentalRequest $request, Rental $rental)
    {
        if ($rental->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        

        $rental->update([
            'property_id'      => $request->property_id,
            'tenant_name'      => $request->tenant_name,
            'tenant_mobile'    => $request->tenant_mobile,
            'tenant_email'     => $request->tenant_email,
            'rent_amount'      => $request->rent_amount,
            'security_deposit' => $request->security_deposit,
            'rent_start_date'  => $request->rent_start_date,
            'rent_end_date'    => $request->rent_end_date,
            'rent_due_date'    => $request->rent_due_date,
            'payment_status'   => $request->payment_status,
            'rental_status'    => $request->rental_status,
            'remarks'          => $request->remarks,
        ]);

        $this->updatePropertyStatus($rental);

        return redirect()->route('rentals.index')->with('success', 'Rental record updated successfully.');
    }

    public function destroy(Rental $rental)
    {
        if ($rental->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        // Restore property to available on delete
        $property = Property::find($rental->property_id);
        if ($property && $property->status === 'rented') {
            $property->update(['status' => 'available']);
        }

        $rental->delete();

        return redirect()->route('rentals.index')->with('success', 'Rental record deleted successfully.');
    }
}
