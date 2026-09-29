<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyStatusRequest;
use App\Models\Firm;
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
        // 1. Auto-sync statuses across properties based on active sales, bookings, and rentals
        try {
            Property::syncAllStatuses();
        } catch (\Exception $e) {
            // Silently continue if sync fails
        }

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $userFirmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
        $activeFirmId = $isAdmin ? ($request->filled('firm_id') ? $request->firm_id : null) : $userFirmId;

        // Base Query for Properties
        $query = Property::with([
            'firm',
            'propertyMaster',
            'project',
            'propertyType',
            'sales.customer',
            'bookings.customer',
            'rentals.tenant'
        ]);

        if ($activeFirmId) {
            $query->where('firm_id', $activeFirmId);
        }

        // Available firms & property masters for filter dropdowns
        if ($isAdmin) {
            $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
            $propertyMasters = PropertyMaster::orderBy('property_name')->get();
        } else {
            $firms = Firm::where('id', $userFirmId)->get();
            $propertyMasters = PropertyMaster::where('firm_id', $userFirmId)->orderBy('property_name')->get();
        }

        // Calculate Global KPI Summary Counters (scoped to firm)
        $kpiQuery = Property::query();
        if ($activeFirmId) {
            $kpiQuery->where('firm_id', $activeFirmId);
        }
        $kpiCounts = (clone $kpiQuery)
            ->select('status', DB::raw('count(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->toArray();

        $totalUnitsCount = array_sum($kpiCounts);
        $availableCount  = $kpiCounts['available'] ?? 0;
        $bookedCount     = $kpiCounts['booked'] ?? 0;
        $soldCount       = $kpiCounts['sold'] ?? 0;
        $rentedCount     = $kpiCounts['rented'] ?? 0;
        $reservedCount   = ($kpiCounts['reserved'] ?? 0) + ($kpiCounts['under_maintenance'] ?? 0);

        // Apply Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply Property Master / Project Filter
        if ($request->filled('property_master_id')) {
            $query->where('property_master_id', $request->property_master_id);
        }
        if ($request->filled('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }

        // Apply Search Filter
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('property_name', 'like', "%{$s}%")
                  ->orWhere('property_code', 'like', "%{$s}%")
                  ->orWhere('unit_no', 'like', "%{$s}%")
                  ->orWhere('location', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%")
                  ->orWhere('status', 'like', "%{$s}%")
                  ->orWhereHas('propertyMaster', function($pm) use ($s) {
                      $pm->where('property_name', 'like', "%{$s}%")
                         ->orWhere('property_code', 'like', "%{$s}%");
                  })
                  ->orWhereHas('project', function($pj) use ($s) {
                      $pj->where('project_name', 'like', "%{$s}%");
                  })
                  ->orWhereHas('firm', function($f) use ($s) {
                      $f->where('firm_name', 'like', "%{$s}%");
                  });
            });
        }

        $properties = $query->orderByRaw("FIELD(status, 'available', 'booked', 'rented', 'reserved', 'under_maintenance', 'sold')")
                            ->orderBy('property_name')
                            ->paginate(20)
                            ->withQueryString();

        $statuses = PropertyStatus::statuses();

        return view('admin.property-availability.index', compact(
            'properties',
            'firms',
            'propertyMasters',
            'statuses',
            'totalUnitsCount',
            'availableCount',
            'bookedCount',
            'soldCount',
            'rentedCount',
            'reservedCount'
        ));
    }

    /* ── CREATE ─────────────────────────────────────────────────────── */
    public function create()
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        if ($isAdmin) {
            $properties = Property::with(['firm', 'propertyMaster'])->orderBy('property_name')->get();
            $propertyMasters = PropertyMaster::with(['firm'])->orderBy('property_name')->get();
        } else {
            $properties = Property::where('firm_id', $firmId)->with(['firm', 'propertyMaster'])->orderBy('property_name')->get();
            $propertyMasters = PropertyMaster::where('firm_id', $firmId)->with(['firm'])->orderBy('property_name')->get();
        }
        $statuses = PropertyStatus::statuses();

        return view('admin.property-availability.create', compact('properties', 'propertyMasters', 'statuses'));
    }

    /* ── STORE / QUICK UPDATE STATUS ─────────────────────────────────── */
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required',
            'status'      => 'required|in:available,booked,sold,rented,reserved,under_maintenance',
            'status_date' => 'nullable|date',
            'remarks'     => 'nullable|string|max:500',
        ]);

        $property = Property::find($request->property_id);

        if ($property) {
            $this->authoriseProperty($property);

            $oldStatus = $property->status;
            $property->update(['status' => $request->status]);

            // Create audit log history in property_statuses
            PropertyStatus::create([
                'firm_id'            => $property->firm_id ?: 1,
                'property_master_id' => $property->property_master_id,
                'property_id'        => $property->id,
                'status'             => $request->status,
                'status_date'        => $request->status_date ?: now(),
                'remarks'            => $request->remarks ?: "Status changed from {$oldStatus} to {$request->status}",
                'updated_by'         => auth()->id(),
            ]);

            \App\Models\AuditLog::log(
                'Property Availability',
                'Update Status',
                "Status of '{$property->property_name}' updated from '{$oldStatus}' to '{$request->status}'"
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Status for {$property->property_name} updated to " . ucfirst($request->status),
                    'status' => $request->status,
                ]);
            }

            return redirect()->route('property-availability.index')
                ->with('success', "Status for '{$property->property_name}' updated to " . ucfirst($request->status) . " successfully.");
        }

        // Standalone Property Master
        $propertyMaster = PropertyMaster::findOrFail($request->property_id);
        $oldStatus = $propertyMaster->status;
        $propertyMaster->update(['status' => $request->status]);

        PropertyStatus::create([
            'firm_id'            => $propertyMaster->firm_id ?: 1,
            'property_master_id' => $propertyMaster->id,
            'property_id'        => null,
            'status'             => $request->status,
            'status_date'        => $request->status_date ?: now(),
            'remarks'            => $request->remarks ?: "Status changed from {$oldStatus} to {$request->status}",
            'updated_by'         => auth()->id(),
        ]);

        return redirect()->route('property-availability.index')
            ->with('success', "Status for Land Property '{$propertyMaster->property_name}' updated to " . ucfirst($request->status) . " successfully.");
    }

    /* ── SHOW ───────────────────────────────────────────────────────── */
    public function show($id)
    {
        $property = Property::with([
            'firm',
            'propertyMaster',
            'project',
            'propertyType',
            'sales.customer',
            'bookings.customer',
            'rentals.tenant'
        ])->find($id);

        if ($property) {
            $this->authoriseProperty($property);
            $history = PropertyStatus::where('property_id', $property->id)->with('updatedBy')->latest('status_date')->latest()->get();
            return view('admin.property-availability.show', compact('property', 'history'));
        }

        $propertyMaster = PropertyMaster::with(['firm', 'plots'])->findOrFail($id);
        $history = PropertyStatus::where('property_master_id', $propertyMaster->id)->with('updatedBy')->latest('status_date')->latest()->get();
        return view('admin.property-availability.show', [
            'property' => null,
            'propertyMaster' => $propertyMaster,
            'history' => $history
        ]);
    }

    /* ── EDIT ───────────────────────────────────────────────────────── */
    public function edit($id)
    {
        $property = Property::with(['firm', 'propertyMaster'])->find($id);
        if ($property) {
            $this->authoriseProperty($property);
            $statuses = PropertyStatus::statuses();
            return view('admin.property-availability.edit', compact('property', 'statuses'));
        }

        $propertyMaster = PropertyMaster::findOrFail($id);
        $statuses = PropertyStatus::statuses();
        return view('admin.property-availability.edit', compact('propertyMaster', 'statuses'));
    }

    /* ── UPDATE ─────────────────────────────────────────────────────── */
    public function update(Request $request, $id)
    {
        return $this->store($request->merge(['property_id' => $id]));
    }

    /* ── DESTROY ─────────────────────────────────────────────────────── */
    public function destroy($id)
    {
        // Revert status to available
        $property = Property::find($id);
        if ($property) {
            $this->authoriseProperty($property);
            $property->update(['status' => 'available']);
            return redirect()->route('property-availability.index')
                ->with('success', "Property status reset to 'Available'.");
        }

        $propertyStatus = PropertyStatus::find($id);
        if ($propertyStatus) {
            $propertyStatus->delete();
            return redirect()->route('property-availability.index')
                ->with('success', "Status log entry removed.");
        }

        return redirect()->route('property-availability.index');
    }

    private function authoriseProperty(Property $property): void
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if ($isAdmin) return;

        $userFirmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
        if ($property->firm_id && $property->firm_id != $userFirmId) {
            abort(403, 'Unauthorized access to this property.');
        }
    }
}
