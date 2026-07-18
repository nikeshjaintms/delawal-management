<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Customer;
use App\Models\Broker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    private function dropdowns(): array
    {
        $firmId = Auth::user()->firm_id;
        return [
            'properties' => Property::where('firm_id', $firmId)->orderBy('property_name')->get(),
            'customers'  => Customer::where('firm_id', $firmId)->where('status', 'active')->orderBy('name')->get(),
            'brokers'    => Broker::where('firm_id', $firmId)->where('status', 'active')->orderBy('name')->get(),
        ];
    }

    private function updatePropertyStatus(Booking $booking): void
    {
        $property = Property::find($booking->property_id);
        if (!$property) return;
        if ($booking->status === 'confirmed') {
            $property->update(['status' => 'booked']);
        } elseif ($booking->status === 'cancelled') {
            $property->update(['status' => 'available']);
        }
    }

    public function index(Request $request)
    {
        $query = Booking::with(['property', 'customer', 'broker'])
            ->where('firm_id', Auth::user()->firm_id);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('status', 'like', "%{$s}%")
                  ->orWhere('payment_status', 'like', "%{$s}%")
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        $bookings = $query->latest()->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function create()
    {
        return view('admin.bookings.create', $this->dropdowns());
    }

    public function store(BookingRequest $request)
    {
        $booking = Booking::create([
            'firm_id'        => Auth::user()->firm_id,
            'property_id'    => $request->property_id,
            'customer_id'    => $request->customer_id,
            'broker_id'      => $request->broker_id ?: null,
            'booking_date'   => $request->booking_date,
            'booking_amount' => $request->booking_amount,
            'agreement_date' => $request->agreement_date,
            'status'         => $request->status,
            'payment_status' => $request->payment_status,
            'remarks'        => $request->remarks,
        ]);

        $this->updatePropertyStatus($booking);

        // Save broker commission if broker selected and value entered
        if ($booking->broker_id && $request->filled('commission_value')) {
            \App\Models\BrokerCommission::create([
                'firm_id'           => Auth::user()->firm_id,
                'broker_id'         => $booking->broker_id,
                'property_id'       => $booking->property_id,
                'customer_id'       => $booking->customer_id,
                'booking_id'        => $booking->id,
                'commission_type'   => $request->commission_type ?: 'percentage',
                'commission_value'  => $request->commission_value,
                'commission_amount' => $request->commission_amount ?: 0,
                'payment_status'    => 'pending',
                'status'            => 'active',
                'created_by'        => Auth::user()->id,
            ]);
        }

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully.');
    }

    public function show(Booking $booking)
    {
        if ($booking->firm_id != Auth::user()->firm_id) abort(403);
        $booking->load(['property.propertyType', 'customer', 'broker']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        if ($booking->firm_id != Auth::user()->firm_id) abort(403);
        $commission = \App\Models\BrokerCommission::where('booking_id', $booking->id)->first();
        return view('admin.bookings.edit', array_merge([
            'booking' => $booking,
            'commission' => $commission
        ], $this->dropdowns()));
    }

    public function update(BookingRequest $request, Booking $booking)
    {
        if ($booking->firm_id != Auth::user()->firm_id) abort(403);

        $booking->update([
            'property_id'    => $request->property_id,
            'customer_id'    => $request->customer_id,
            'broker_id'      => $request->broker_id ?: null,
            'booking_date'   => $request->booking_date,
            'booking_amount' => $request->booking_amount,
            'agreement_date' => $request->agreement_date,
            'status'         => $request->status,
            'payment_status' => $request->payment_status,
            'remarks'        => $request->remarks,
        ]);

        $this->updatePropertyStatus($booking);

        // Save or update broker commission
        if ($booking->broker_id && $request->filled('commission_value')) {
            \App\Models\BrokerCommission::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'firm_id'           => Auth::user()->firm_id,
                    'broker_id'         => $booking->broker_id,
                    'property_id'       => $booking->property_id,
                    'customer_id'       => $booking->customer_id,
                    'commission_type'   => $request->commission_type ?: 'percentage',
                    'commission_value'  => $request->commission_value,
                    'commission_amount' => $request->commission_amount ?: 0,
                    'status'            => 'active',
                ]
            );
        } else {
            // Delete commission if broker is removed
            \App\Models\BrokerCommission::where('booking_id', $booking->id)->delete();
        }

        return redirect()->route('bookings.index')->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        if ($booking->firm_id != Auth::user()->firm_id) abort(403);

        $property = Property::find($booking->property_id);
        if ($property && $property->status === 'booked') {
            $property->update(['status' => 'available']);
        }

        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully.');
    }
}
