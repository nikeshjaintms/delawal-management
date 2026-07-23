<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentalPaymentRequest;
use App\Models\Rental;
use App\Models\RentalPayment;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalPaymentController extends Controller
{
    private function firmCheck(Rental $rental)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $rental->firm_id != $firmId) {
            abort(403);
        }
    }

    public function index(Rental $rental)
    {
        $this->firmCheck($rental);

        $rental->load(['property', 'firm']);

        $payments = RentalPayment::with(['firm', 'property.propertyType'])
            ->where('rental_id', $rental->id)
            ->orderByDesc('payment_year')
            ->orderByDesc('payment_month')
            ->orderByDesc('id')
            ->paginate(12);

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.rental-payments.index', compact('rental', 'payments', 'firms'));
    }

    public function create(Rental $rental)
    {
        $this->firmCheck($rental);

        $rental->load(['property', 'firm']);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $propQuery = Property::with('propertyType')->orderBy('property_name');
        if (!$isAdmin) {
            $propQuery->where('firm_id', $firmId);
        }
        $properties = $propQuery->get();

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.rental-payments.create', compact('rental', 'firms', 'properties'));
    }

    public function store(RentalPaymentRequest $request, Rental $rental)
    {
        $this->firmCheck($rental);

        $rentAmt  = (float) $request->rent_amount;
        $paidAmt  = (float) $request->paid_amount;
        $pending  = max(0, $rentAmt - $paidAmt);

        if ($paidAmt <= 0) {
            $status = 'pending';
        } elseif ($paidAmt >= $rentAmt) {
            $status = 'paid';
        } else {
            $status = 'partial';
        }

        $firmId = $request->firm_id ?? ($rental->firm_id ?: Auth::user()->firm_id);

        RentalPayment::create([
            'firm_id'        => $firmId,
            'rental_id'      => $rental->id,
            'property_id'    => $request->property_id,
            'payment_month'  => $request->payment_month,
            'payment_year'   => $request->payment_year,
            'rent_amount'    => $rentAmt,
            'paid_amount'    => $paidAmt,
            'pending_amount' => $pending,
            'payment_date'   => $request->payment_date,
            'payment_mode'   => $request->payment_mode,
            'payment_status' => $status,
            'remarks'        => $request->remarks,
        ]);

        $rental->update(['payment_status' => $status]);

        return redirect()
            ->route('rental-payments.index', $rental->id)
            ->with('success', 'Payment recorded successfully.');
    }

    public function edit(Rental $rental, RentalPayment $rentalPayment)
    {
        $this->firmCheck($rental);
        if ($rentalPayment->rental_id !== $rental->id) {
            abort(403);
        }

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $propQuery = Property::with('propertyType')->orderBy('property_name');
        if (!$isAdmin) {
            $propQuery->where('firm_id', $firmId);
        }
        $properties = $propQuery->get();

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.rental-payments.edit', compact('rental', 'rentalPayment', 'firms', 'properties'));
    }

    public function update(RentalPaymentRequest $request, Rental $rental, RentalPayment $rentalPayment)
    {
        $this->firmCheck($rental);
        if ($rentalPayment->rental_id !== $rental->id) {
            abort(403);
        }

        $rentAmt  = (float) $request->rent_amount;
        $paidAmt  = (float) $request->paid_amount;
        $pending  = max(0, $rentAmt - $paidAmt);

        if ($paidAmt <= 0) {
            $status = 'pending';
        } elseif ($paidAmt >= $rentAmt) {
            $status = 'paid';
        } else {
            $status = 'partial';
        }

        $rentalPayment->update([
            'property_id'    => $request->property_id,
            'payment_month'  => $request->payment_month,
            'payment_year'   => $request->payment_year,
            'rent_amount'    => $rentAmt,
            'paid_amount'    => $paidAmt,
            'pending_amount' => $pending,
            'payment_date'   => $request->payment_date,
            'payment_mode'   => $request->payment_mode,
            'payment_status' => $status,
            'remarks'        => $request->remarks,
        ]);

        $rental->update(['payment_status' => $status]);

        return redirect()
            ->route('rental-payments.index', $rental->id)
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Rental $rental, RentalPayment $rentalPayment)
    {
        $this->firmCheck($rental);

        if ($rentalPayment->rental_id !== $rental->id) {
            abort(403);
        }

        $rentalPayment->delete();

        $latest = RentalPayment::where('rental_id', $rental->id)->latest('id')->first();
        $rental->update([
            'payment_status' => $latest ? $latest->payment_status : 'pending',
        ]);

        return redirect()
            ->route('rental-payments.index', $rental->id)
            ->with('success', 'Payment record deleted successfully.');
    }
}
