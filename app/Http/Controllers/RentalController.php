<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentalRequest;
use App\Models\Rental;
use App\Models\Property;
use App\Models\Firm;
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
        $query = Rental::with(['firm', 'property']);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();

        if (!$isAdmin) {
            $query->where('firm_id', $user ? $user->firm_id : session('firm_id'));
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tenant_name', 'like', "%{$search}%")
                  ->orWhere('tenant_mobile', 'like', "%{$search}%")
                  ->orWhere('payment_status', 'like', "%{$search}%")
                  ->orWhere('rental_status', 'like', "%{$search}%")
                  ->orWhereHas('property', fn($p) =>
                      $p->where('property_name', 'like', "%{$search}%")
                        ->orWhere('property_code', 'like', "%{$search}%")
                  )
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$search}%"));
            });
        }

        $rentals = $query->latest()->paginate(10)->withQueryString();
        $firms   = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.rentals.index', compact('rentals', 'firms'));
    }

    public function create()
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $propQuery = Property::orderBy('property_name');
        if (!$isAdmin) {
            $propQuery->where('firm_id', $firmId);
        }
        $properties = $propQuery->get();

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.rentals.create', compact('properties', 'firms'));
    }

    public function store(RentalRequest $request)
    {
        $user = Auth::user();
        $firmId = $request->firm_id ?? ($user ? $user->firm_id : session('firm_id'));

        $rental = Rental::create([
            'firm_id'          => $firmId,
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
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $rental->firm_id != $firmId) {
            abort(403);
        }

        $rental->load(['firm', 'property.propertyType']);

        return view('admin.rentals.show', compact('rental'));
    }

    public function edit(Rental $rental)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $rental->firm_id != $firmId) {
            abort(403);
        }

        $propQuery = Property::orderBy('property_name');
        if (!$isAdmin) {
            $propQuery->where('firm_id', $rental->firm_id ?: $firmId);
        }
        $properties = $propQuery->get();

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.rentals.edit', compact('rental', 'properties', 'firms'));
    }

    public function update(RentalRequest $request, Rental $rental)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $rental->firm_id != $firmId) {
            abort(403);
        }

        $rental->update([
            'firm_id'          => $request->firm_id ?? $rental->firm_id,
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
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $rental->firm_id != $firmId) {
            abort(403);
        }

        $property = Property::find($rental->property_id);
        if ($property && $property->status === 'rented') {
            $property->update(['status' => 'available']);
        }

        $rental->delete();

        return redirect()->route('rentals.index')->with('success', 'Rental record deleted successfully.');
    }
}
