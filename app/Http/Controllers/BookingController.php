<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Project;
use App\Models\Property;
use App\Models\Customer;
use App\Models\Broker;
use App\Models\Firm;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    private function dropdowns($selectedFirmId = null): array
    {
        $user = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $projQuery = Project::with(['propertyMasters', 'properties'])->orderBy('project_name');
        $propQuery = Property::with(['project.propertyMaster', 'propertyMaster'])->orderBy('property_name');
        $pmQueryM  = \App\Models\PropertyMaster::with(['plots', 'projects'])->orderBy('property_name');
        $custQuery = Customer::where('status', 'active')->orderBy('name');
        $brokQuery = Broker::where('status', 'active')->orderBy('name');
        $pmQuery   = PaymentMode::where('status', 'active')->orderBy('name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $projQuery->where('firm_id', $firmId);
            $propQuery->where('firm_id', $firmId);
            $pmQueryM->where('firm_id', $firmId);
            $custQuery->where('firm_id', $firmId);
            $brokQuery->where('firm_id', $firmId);
            $pmQuery->whereHas('firms', function($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            });
        }

        $paymentModes = $pmQuery->get();
        if ($paymentModes->isEmpty()) {
            $paymentModes = PaymentMode::where('status', 'active')->orderBy('name')->get();
        }

        $projects = $projQuery->get();
        $allPropertyMasters = $pmQueryM->get();

        // Standalone property masters are those that do NOT belong to any project
        $standalonePropertyMasters = $allPropertyMasters->filter(function ($pm) {
            return $pm->all_projects->isEmpty();
        })->values();

        $properties = Property::naturalSort($propQuery->get());

        return [
            'firms'                     => $firms,
            'projects'                  => $projects,
            'properties'                => $properties,
            'propertyMasters'           => $allPropertyMasters,
            'standalonePropertyMasters' => $standalonePropertyMasters,
            'customers'                 => $custQuery->get(),
            'brokers'                   => $brokQuery->get(),
            'paymentModes'              => $paymentModes,
        ];
    }

    private function updatePropertyStatus(Booking $booking, array $allPropertyIds = [], array|int|null $oldPropertyIds = []): void
    {
        $oldPropIds = is_array($oldPropertyIds) ? $oldPropertyIds : ($oldPropertyIds ? [$oldPropertyIds] : []);
        $propertyIds = !empty($allPropertyIds) ? $allPropertyIds : ($booking->property_id ? [$booking->property_id] : []);
        $propertyIds = array_values(array_filter($propertyIds));

        // If any properties were removed from this booking, revert them to available
        $removedPropIds = array_diff($oldPropIds, $propertyIds);
        if (!empty($removedPropIds)) {
            $removedProps = Property::whereIn('id', $removedPropIds)->get();
            Property::whereIn('id', $removedPropIds)->update(['status' => 'available']);
            foreach ($removedProps as $oldProp) {
                if ($oldProp->property_master_id && $oldProp->unit_no === null && empty($oldProp->project_id)) {
                    $oldPm = \App\Models\PropertyMaster::find($oldProp->property_master_id);
                    if ($oldPm && $oldPm->all_projects->isEmpty()) {
                        $oldPm->update(['status' => 'active']);
                        $oldPm->plots()->update(['status' => 'available']);
                    }
                }
            }
        }

        if (empty($propertyIds)) return;

        $properties = Property::whereIn('id', $propertyIds)->get();
        if ($properties->isEmpty()) return;

        if ($booking->status === 'cancelled') {
            Property::whereIn('id', $propertyIds)->update(['status' => 'available']);
            foreach ($properties as $property) {
                if ($property->property_master_id && $property->unit_no === null && empty($property->project_id)) {
                    $pm = \App\Models\PropertyMaster::find($property->property_master_id);
                    if ($pm && $pm->all_projects->isEmpty()) {
                        $pm->update(['status' => 'active']);
                        $pm->plots()->update(['status' => 'available']);
                    }
                }
            }
        } else {
            // For any active booking (pending, confirmed, booked, etc.)
            Property::whereIn('id', $propertyIds)->update(['status' => 'booked']);
            foreach ($properties as $property) {
                if ($property->property_master_id && $property->unit_no === null && empty($property->project_id)) {
                    $pm = \App\Models\PropertyMaster::find($property->property_master_id);
                    if ($pm && $pm->all_projects->isEmpty()) {
                        $pm->update(['status' => 'booked']);
                        $pm->plots()->update(['status' => 'booked']);
                    }
                }
            }
        }
    }

    public function index(Request $request)
    {
        $query = Booking::with([
            'firm',
            'property.project',
            'property.propertyMaster',
            'properties.project',
            'properties.propertyMaster',
            'customer',
            'broker',
            'paymentMode'
        ]);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();

        if (!$isAdmin) {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('status', 'like', "%{$s}%")
                  ->orWhere('booking_type', 'like', "%{$s}%")
                  ->orWhere('payment_status', 'like', "%{$s}%")
                  ->orWhere('payment_mode', 'like', "%{$s}%")
                  ->orWhere('transaction_ref', 'like', "%{$s}%")
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                  ->orWhereHas('properties', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        if ($request->filled('filter_booking_type')) {
            $query->where('booking_type', $request->filter_booking_type);
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();
        $firms    = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.bookings.index', compact('bookings', 'firms'));
    }

    public function create()
    {
        Property::syncAllStatuses();
        return view('admin.bookings.create', $this->dropdowns());
    }

    public function store(BookingRequest $request)
    {
        $user = Auth::user();
        $firmId = $request->firm_id ?? ($user ? $user->firm_id : session('firm_id'));

        $paymentModeName = $request->payment_mode;
        if ($request->filled('payment_mode_id')) {
            $pm = PaymentMode::find($request->payment_mode_id);
            if ($pm) {
                $paymentModeName = $pm->name;
            }
        }

        $submittedPropIds = (array) ($request->property_ids ?: ($request->property_id ? [$request->property_id] : []));
        $submittedPropIds = array_values(array_filter($submittedPropIds));
        $primaryPropId = !empty($submittedPropIds) ? $submittedPropIds[0] : $request->property_id;

        $booking = Booking::create([
            'firm_id'          => $firmId,
            'property_id'      => $primaryPropId,
            'customer_id'      => $request->customer_id,
            'broker_id'        => $request->broker_id ?: null,
            'booking_type'     => $request->booking_type ?: 'booking',
            'booking_date'     => $request->booking_date,
            'total_amount'     => $request->total_amount,
            'discount_type'    => $request->discount_type ?: 'percentage',
            'discount_value'   => $request->discount_value ?: 0,
            'discount_amount'  => $request->discount_amount ?: 0,
            'final_amount'     => $request->final_amount,
            'booking_amount'   => $request->booking_amount,
            'remaining_amount' => $request->remaining_amount,
            'payment_mode_id'  => $request->payment_mode_id ?: null,
            'payment_mode'     => $paymentModeName,
            'transaction_ref'  => $request->transaction_ref,
            'agreement_date'   => $request->agreement_date,
            'status'           => $request->status,
            'payment_status'   => $request->payment_status,
            'remarks'          => $request->remarks,
        ]);

        if (!empty($submittedPropIds)) {
            $booking->properties()->sync($submittedPropIds);
        }

        $this->updatePropertyStatus($booking, $submittedPropIds);

        // Save broker commission if broker selected and value entered
        if ($booking->broker_id && $request->filled('commission_value')) {
            \App\Models\BrokerCommission::create([
                'firm_id'           => $firmId,
                'broker_id'         => $booking->broker_id,
                'property_id'       => $booking->property_id,
                'customer_id'       => $booking->customer_id,
                'booking_id'        => $booking->id,
                'commission_type'   => $request->commission_type ?: 'percentage',
                'commission_value'  => $request->commission_value,
                'commission_amount' => $request->commission_amount ?: 0,
                'payment_status'    => 'pending',
                'status'            => 'active',
                'created_by'        => Auth::id() ?: session('user_id'),
            ]);
        }

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully.');
    }

    public function show(Booking $booking)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');
        if (!$isAdmin && $booking->firm_id != $firmId) abort(403);
        $booking->load(['firm', 'property.propertyType', 'property.project', 'property.propertyMaster', 'properties.propertyType', 'properties.project', 'properties.propertyMaster', 'customer', 'broker', 'paymentMode']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');
        if (!$isAdmin && $booking->firm_id != $firmId) abort(403);
        $booking->load(['properties']);
        $commission = \App\Models\BrokerCommission::where('booking_id', $booking->id)->first();
        return view('admin.bookings.edit', array_merge([
            'booking' => $booking,
            'commission' => $commission
        ], $this->dropdowns($booking->firm_id)));
    }

    public function update(BookingRequest $request, Booking $booking)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');
        if (!$isAdmin && $booking->firm_id != $firmId) abort(403);

        $firmId = $request->firm_id ?? $booking->firm_id;

        $paymentModeName = $request->payment_mode;
        if ($request->filled('payment_mode_id')) {
            $pm = PaymentMode::find($request->payment_mode_id);
            if ($pm) {
                $paymentModeName = $pm->name;
            }
        }

        $submittedPropIds = (array) ($request->property_ids ?: ($request->property_id ? [$request->property_id] : []));
        $submittedPropIds = array_values(array_filter($submittedPropIds));
        $primaryPropId = !empty($submittedPropIds) ? $submittedPropIds[0] : $request->property_id;

        $oldPropIds = $booking->properties()->pluck('properties.id')->toArray();
        if (empty($oldPropIds) && $booking->property_id) {
            $oldPropIds = [$booking->property_id];
        }

        $booking->update([
            'firm_id'          => $firmId,
            'property_id'      => $primaryPropId,
            'customer_id'      => $request->customer_id,
            'broker_id'        => $request->broker_id ?: null,
            'booking_type'     => $request->booking_type ?: 'booking',
            'booking_date'     => $request->booking_date,
            'total_amount'     => $request->total_amount,
            'discount_type'    => $request->discount_type ?: 'percentage',
            'discount_value'   => $request->discount_value ?: 0,
            'discount_amount'  => $request->discount_amount ?: 0,
            'final_amount'     => $request->final_amount,
            'booking_amount'   => $request->booking_amount,
            'remaining_amount' => $request->remaining_amount,
            'payment_mode_id'  => $request->payment_mode_id ?: null,
            'payment_mode'     => $paymentModeName,
            'transaction_ref'  => $request->transaction_ref,
            'agreement_date'   => $request->agreement_date,
            'status'           => $request->status,
            'payment_status'   => $request->payment_status,
            'remarks'          => $request->remarks,
        ]);

        if (!empty($submittedPropIds)) {
            $booking->properties()->sync($submittedPropIds);
        }

        $this->updatePropertyStatus($booking, $submittedPropIds, $oldPropIds);

        // Save or update broker commission
        if ($booking->broker_id && $request->filled('commission_value')) {
            \App\Models\BrokerCommission::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'firm_id'           => $firmId,
                    'broker_id'         => $booking->broker_id,
                    'property_id'       => $booking->property_id,
                    'customer_id'       => $booking->customer_id,
                    'commission_type'   => $request->commission_type ?: 'percentage',
                    'commission_value'  => $request->commission_value,
                    'commission_amount' => $request->commission_amount ?: 0,
                    'payment_status'    => 'pending',
                    'status'            => 'active',
                    'created_by'        => Auth::id() ?: session('user_id'),
                ]
            );
        }

        return redirect()->route('bookings.index')->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');
        if (!$isAdmin && $booking->firm_id != $firmId) abort(403);

        $propIds = $booking->properties()->pluck('properties.id')->toArray();
        if (empty($propIds) && $booking->property_id) {
            $propIds = [$booking->property_id];
        }

        if (!empty($propIds)) {
            $properties = Property::whereIn('id', $propIds)->get();
            Property::whereIn('id', $propIds)->update(['status' => 'available']);
            foreach ($properties as $property) {
                if ($property->property_master_id && $property->unit_no === null) {
                    $pm = \App\Models\PropertyMaster::find($property->property_master_id);
                    if ($pm) {
                        $pm->update(['status' => 'active']);
                        $pm->plots()->update(['status' => 'available']);
                    }
                }
            }
        }

        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully.');
    }

    public function exportPdf(Request $request)
    {
        $query = Booking::with(['firm', 'property.project', 'property.propertyMaster', 'properties.project', 'properties.propertyMaster', 'customer', 'broker', 'paymentMode']);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();

        if (!$isAdmin) {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('status', 'like', "%{$s}%")
                  ->orWhere('payment_status', 'like', "%{$s}%")
                  ->orWhere('payment_mode', 'like', "%{$s}%")
                  ->orWhere('transaction_ref', 'like', "%{$s}%")
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                  ->orWhereHas('properties', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        $bookings = $query->latest()->get();
        $totalCount = $bookings->count();
        $totalFinalAmount = $bookings->sum('final_amount');
        $totalBookingAmount = $bookings->sum('booking_amount');
        $totalRemainingAmount = $bookings->sum('remaining_amount');

        return view('admin.bookings.pdf', compact(
            'bookings', 'totalCount', 'totalFinalAmount', 'totalBookingAmount', 'totalRemainingAmount'
        ));
    }

    public function downloadPdf(Booking $booking)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');
        if (!$isAdmin && $booking->firm_id != $firmId) abort(403);

        $booking->load([
            'firm',
            'property.propertyType',
            'property.project',
            'property.propertyMaster',
            'properties.propertyType',
            'properties.project',
            'properties.propertyMaster',
            'customer',
            'broker',
            'paymentMode'
        ]);

        $commission = \App\Models\BrokerCommission::where('booking_id', $booking->id)->first();

        return view('admin.bookings.show-pdf', compact('booking', 'commission'));
    }
}

