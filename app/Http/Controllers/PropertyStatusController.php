<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyStatusRequest;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PropertyStatusController extends Controller
{
    private function firmProperties($firmId = null)
    {
        if (!$firmId) {
            $isAdmin = auth()->user() && auth()->user()->isAdmin();
            $firmId = $isAdmin ? null : (auth()->user() ? auth()->user()->firm_id : session('firm_id'));
        }

        if ($firmId) {
            return Property::where('firm_id', $firmId);
        }

        return Property::query();
    }

    /* ── INDEX ─────────────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();

        if ($isAdmin) {
            $propertyTypes = PropertyType::orderBy('name')->get();
            $properties    = Property::orderBy('property_name')->get();
            $statuses      = PropertyStatus::statuses();
            $query         = PropertyStatus::with(['property.propertyType', 'firm']);
            
            if ($request->filled('firm_id')) {
                $query->where('firm_id', $request->firm_id);
            }
        } else {
            $firmId        = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            $propertyTypes = PropertyType::where('firm_id', $firmId)->orderBy('name')->get();
            $properties    = $this->firmProperties($firmId)->orderBy('property_name')->get();
            $statuses      = PropertyStatus::statuses();
            $query         = PropertyStatus::with(['property.propertyType', 'firm'])
                                ->where('firm_id', $firmId);
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('property_type_id')) {
            $query->whereHas('property', fn($q) => $q->where('property_type_id', $request->property_type_id));
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('remarks', 'like', "%{$s}%")
                  ->orWhereHas('property', fn($p) =>
                        $p->where('property_name', 'like', "%{$s}%")
                          ->orWhere('unit_no', 'like', "%{$s}%")
                          ->orWhere('property_code', 'like', "%{$s}%")
                  );
            });
        }

        $records = $query->latest('status_date')->latest()->paginate(15)->withQueryString();

        return view('admin.property-availability.index',
            compact('records', 'properties', 'propertyTypes', 'statuses'));
    }

    /* ── CREATE ─────────────────────────────────────────────────────── */
    public function create()
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        if ($isAdmin) {
            $properties = Property::with('propertyType')->orderBy('property_name')->get();
        } else {
            $properties = $this->firmProperties($firmId)->with('propertyType')->orderBy('property_name')->get();
        }
        $statuses = PropertyStatus::statuses();

        return view('admin.property-availability.create', compact('properties', 'statuses'));
    }

    /* ── STORE ──────────────────────────────────────────────────────── */
    public function store(PropertyStatusRequest $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : (auth()->user() ? auth()->user()->firm_id : session('firm_id'));

        // Authorise
        $property = Property::findOrFail($request->property_id);
        if ($property->firm_id != $firmId) abort(403);

        // Create status record
        $record = PropertyStatus::create([
            'firm_id'     => $firmId,
            'property_id' => $request->property_id,
            'status'      => $request->status,
            'status_date' => $request->status_date,
            'remarks'     => $request->remarks,
            'updated_by'  => auth()->id(),
        ]);

        // Also sync the status on the Property master
        $property->update(['status' => $request->status]);

        \App\Models\AuditLog::log(
            'Property Availability',
            'Create',
            "Status set to '{$request->status}' for property '{$property->property_name}'"
        );

        return redirect()->route('property-availability.index')
            ->with('success', "Status updated to '{$record->status_label}' for {$property->property_name}.");
    }

    /* ── SHOW ───────────────────────────────────────────────────────── */
    public function show(PropertyStatus $propertyAvailability)
    {
        $this->authorise($propertyAvailability);
        $propertyAvailability->load(['property.propertyType', 'updatedBy']);

        return view('admin.property-availability.show', ['record' => $propertyAvailability]);
    }

    /* ── EDIT ───────────────────────────────────────────────────────── */
    public function edit(PropertyStatus $propertyAvailability)
    {
        $this->authorise($propertyAvailability);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if ($isAdmin) {
            $properties = Property::with('propertyType')->orderBy('property_name')->get();
        } else {
            $properties = $this->firmProperties($propertyAvailability->firm_id)->with('propertyType')->orderBy('property_name')->get();
        }
        $statuses = PropertyStatus::statuses();

        return view('admin.property-availability.edit',
            ['record' => $propertyAvailability, 'properties' => $properties, 'statuses' => $statuses]);
    }

    /* ── UPDATE ─────────────────────────────────────────────────────── */
    public function update(PropertyStatusRequest $request, PropertyStatus $propertyAvailability)
    {
        $this->authorise($propertyAvailability);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : $propertyAvailability->firm_id;

        $property = Property::findOrFail($request->property_id);
        if ($property->firm_id != $firmId) abort(403);

        $propertyAvailability->update([
            'firm_id'     => $firmId,
            'property_id' => $request->property_id,
            'status'      => $request->status,
            'status_date' => $request->status_date,
            'remarks'     => $request->remarks,
            'updated_by'  => auth()->id(),
        ]);

        // Sync latest status to Property master
        $latestRecord = PropertyStatus::where('property_id', $request->property_id)
            ->latest('status_date')->latest()->first();
        if ($latestRecord) {
            $property->update(['status' => $latestRecord->status]);
        }

        \App\Models\AuditLog::log(
            'Property Availability',
            'Update',
            "Status record ID {$propertyAvailability->id} updated for '{$property->property_name}'"
        );

        return redirect()->route('property-availability.index')
            ->with('success', 'Property status record updated successfully.');
    }

    /* ── DESTROY ────────────────────────────────────────────────────── */
    public function destroy(PropertyStatus $propertyAvailability)
    {
        $this->authorise($propertyAvailability);

        $propName = $propertyAvailability->property->property_name ?? 'Unknown';
        $propId   = $propertyAvailability->property_id;
        $propertyAvailability->delete();

        // Sync latest remaining status to Property master
        $latest = PropertyStatus::where('property_id', $propId)->latest('status_date')->latest()->first();
        Property::where('id', $propId)->update(['status' => $latest?->status ?? 'available']);

        \App\Models\AuditLog::log(
            'Property Availability',
            'Delete',
            "Status record deleted for property '{$propName}'"
        );

        return redirect()->route('property-availability.index')
            ->with('success', 'Status record deleted successfully.');
    }

    private function authorise(PropertyStatus $record): void
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if (!$isAdmin) {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            if ($record->firm_id != $firmId) abort(403);
        }
    }
}
