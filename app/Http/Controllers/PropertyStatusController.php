<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyStatusRequest;
use App\Models\Property;
use App\Models\PropertyMaster;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PropertyStatusController extends Controller
{
    private function firmPropertyMasters($firmId = null)
    {
        if (!$firmId) {
            $isAdmin = auth()->user() && auth()->user()->isAdmin();
            $firmId = $isAdmin ? null : (auth()->user() ? auth()->user()->firm_id : session('firm_id'));
        }

        if ($firmId) {
            return PropertyMaster::where('firm_id', $firmId);
        }

        return PropertyMaster::query();
    }

    /* ── INDEX ─────────────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();

        if ($isAdmin) {
            $propertyMasters = PropertyMaster::orderBy('property_name')->get();
            $statuses        = PropertyStatus::statuses();
            $query           = PropertyStatus::with(['propertyMaster.firm', 'property.propertyType', 'firm']);
            
            if ($request->filled('firm_id')) {
                $query->where('firm_id', $request->firm_id);
            }
        } else {
            $firmId          = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            $propertyMasters = $this->firmPropertyMasters($firmId)->orderBy('property_name')->get();
            $statuses        = PropertyStatus::statuses();
            $query           = PropertyStatus::with(['propertyMaster.firm', 'property.propertyType', 'firm'])
                                ->where('firm_id', $firmId);
        }

        if ($request->filled('property_master_id')) {
            $query->where('property_master_id', $request->property_master_id);
        }
        if ($request->filled('property_id')) {
            $query->where(function($q) use ($request) {
                $q->where('property_master_id', $request->property_id)
                  ->orWhere('property_id', $request->property_id);
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('remarks', 'like', "%{$s}%")
                  ->orWhereHas('propertyMaster', fn($pm) =>
                        $pm->where('property_name', 'like', "%{$s}%")
                           ->orWhere('property_code', 'like', "%{$s}%")
                           ->orWhere('location', 'like', "%{$s}%")
                  )
                  ->orWhereHas('property', fn($p) =>
                        $p->where('property_name', 'like', "%{$s}%")
                           ->orWhere('unit_no', 'like', "%{$s}%")
                           ->orWhere('property_code', 'like', "%{$s}%")
                  );
            });
        }

        $records = $query->latest('status_date')->latest()->paginate(15)->withQueryString();

        return view('admin.property-availability.index',
            compact('records', 'propertyMasters', 'statuses'));
    }

    /* ── CREATE ─────────────────────────────────────────────────────── */
    public function create()
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        if ($isAdmin) {
            $propertyMasters = PropertyMaster::with(['firm', 'firms'])->orderBy('property_name')->get();
        } else {
            $propertyMasters = $this->firmPropertyMasters($firmId)->with(['firm', 'firms'])->orderBy('property_name')->get();
        }
        $statuses = PropertyStatus::statuses();

        return view('admin.property-availability.create', compact('propertyMasters', 'statuses'));
    }

    /* ── STORE ──────────────────────────────────────────────────────── */
    public function store(PropertyStatusRequest $request)
    {
        $masterId = $request->property_master_id ?: $request->property_id;
        $propertyMaster = PropertyMaster::find($masterId);

        if ($propertyMaster) {
            $isAdmin = auth()->user() && auth()->user()->isAdmin();
            if (!$isAdmin) {
                $userFirmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
                if ($propertyMaster->firm_id && $propertyMaster->firm_id != $userFirmId) {
                    abort(403);
                }
            }

            $firmId = $propertyMaster->firm_id ?: ($request->firm_id ?: (auth()->user() ? auth()->user()->firm_id : 1));

            $record = PropertyStatus::create([
                'firm_id'            => $firmId,
                'property_master_id' => $propertyMaster->id,
                'property_id'        => null,
                'status'             => $request->status,
                'status_date'        => $request->status_date,
                'remarks'            => $request->remarks,
                'updated_by'         => auth()->id(),
            ]);

            // Sync status on PropertyMaster
            $propertyMaster->update(['status' => $request->status]);

            \App\Models\AuditLog::log(
                'Property Availability',
                'Create',
                "Status set to '{$request->status}' for Land Property '{$propertyMaster->property_name}'"
            );

            return redirect()->route('property-availability.index')
                ->with('success', "Status updated to '{$record->status_label}' for Land Property {$propertyMaster->property_name}.");
        }

        // Fallback for Property
        $property = Property::findOrFail($request->property_id);
        $firmId = $property->firm_id ?: 1;

        $record = PropertyStatus::create([
            'firm_id'            => $firmId,
            'property_master_id' => null,
            'property_id'        => $property->id,
            'status'             => $request->status,
            'status_date'        => $request->status_date,
            'remarks'            => $request->remarks,
            'updated_by'         => auth()->id(),
        ]);

        $property->update(['status' => $request->status]);

        return redirect()->route('property-availability.index')
            ->with('success', "Status updated to '{$record->status_label}' for {$property->property_name}.");
    }

    /* ── SHOW ───────────────────────────────────────────────────────── */
    public function show(PropertyStatus $propertyAvailability)
    {
        $this->authorise($propertyAvailability);
        $propertyAvailability->load(['propertyMaster.firm', 'property.propertyType', 'updatedBy']);

        return view('admin.property-availability.show', ['record' => $propertyAvailability]);
    }

    /* ── EDIT ───────────────────────────────────────────────────────── */
    public function edit(PropertyStatus $propertyAvailability)
    {
        $this->authorise($propertyAvailability);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if ($isAdmin) {
            $propertyMasters = PropertyMaster::with(['firm', 'firms'])->orderBy('property_name')->get();
        } else {
            $propertyMasters = $this->firmPropertyMasters($propertyAvailability->firm_id)->with(['firm', 'firms'])->orderBy('property_name')->get();
        }
        $statuses = PropertyStatus::statuses();

        return view('admin.property-availability.edit',
            ['record' => $propertyAvailability, 'propertyMasters' => $propertyMasters, 'statuses' => $statuses]);
    }

    /* ── UPDATE ─────────────────────────────────────────────────────── */
    public function update(PropertyStatusRequest $request, PropertyStatus $propertyAvailability)
    {
        $this->authorise($propertyAvailability);

        $masterId = $request->property_master_id ?: $request->property_id;
        $propertyMaster = PropertyMaster::find($masterId);

        if ($propertyMaster) {
            $propertyAvailability->update([
                'property_master_id' => $propertyMaster->id,
                'property_id'        => null,
                'status'             => $request->status,
                'status_date'        => $request->status_date,
                'remarks'            => $request->remarks,
                'updated_by'         => auth()->id(),
            ]);

            $propertyMaster->update(['status' => $request->status]);

            return redirect()->route('property-availability.index')
                ->with('success', "Status record updated for Land Property {$propertyMaster->property_name}.");
        }

        $property = Property::findOrFail($request->property_id);
        $propertyAvailability->update([
            'property_master_id' => null,
            'property_id'        => $property->id,
            'status'             => $request->status,
            'status_date'        => $request->status_date,
            'remarks'            => $request->remarks,
            'updated_by'         => auth()->id(),
        ]);

        $property->update(['status' => $request->status]);

        return redirect()->route('property-availability.index')
            ->with('success', "Status record updated for {$property->property_name}.");
    }

    /* ── DESTROY ─────────────────────────────────────────────────────── */
    public function destroy(PropertyStatus $propertyAvailability)
    {
        $this->authorise($propertyAvailability);
        $name = $propertyAvailability->target_name;
        $propertyAvailability->delete();

        return redirect()->route('property-availability.index')
            ->with('success', "Status record for '{$name}' removed.");
    }

    private function authorise(PropertyStatus $record): void
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if ($isAdmin) return;

        $userFirmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
        if ($record->firm_id && $record->firm_id != $userFirmId) {
            abort(403, 'Unauthorized access to this status record.');
        }
    }
}
