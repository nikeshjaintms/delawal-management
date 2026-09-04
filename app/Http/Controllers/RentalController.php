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

        if ($request->has('collect')) {
            $query->where('rental_status', 'active');
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

        $propQuery = Property::with(['project.propertyMaster'])->orderBy('property_name');
        if (!$isAdmin && $firmId) {
            $propQuery->where('firm_id', $firmId);
        }
        $properties = $propQuery->get();

        $projQuery = \App\Models\Project::with('propertyMaster')->orderBy('project_name');
        if (!$isAdmin && $firmId) {
            $projQuery->where('firm_id', $firmId);
        }
        $projects = $projQuery->get();

        $tenantQuery = \App\Models\Tenant::with('firm')->where('status', 'active')->orderBy('name');
        if (!$isAdmin && $firmId) {
            $tenantQuery->where('firm_id', $firmId);
        }
        $tenants = $tenantQuery->get();

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.rentals.create', compact('properties', 'projects', 'tenants', 'firms'));
    }

    public function store(RentalRequest $request)
    {
        $user = Auth::user();
        $firmId = $request->firm_id;
        if (!$firmId && $request->has('firm_ids')) {
            $firmIds = (array) $request->firm_ids;
            $firmId = $firmIds[0] ?? null;
        }
        if (!$firmId) {
            $firmId = $user ? $user->firm_id : session('firm_id');
        }

        $data = [
            'firm_id'            => $firmId,
            'property_id'        => $request->property_id,
            'tenant_id'          => $request->tenant_id,
            'agreement_no'       => $request->agreement_no,
            'tenant_name'        => $request->tenant_name,
            'tenant_mobile'      => $request->tenant_mobile,
            'tenant_email'       => $request->tenant_email,
            'rent_amount'        => $request->rent_amount,
            'security_deposit'   => $request->security_deposit,
            'maintenance_amount' => $request->maintenance_amount,
            'rent_start_date'    => $request->rent_start_date,
            'rent_end_date'      => $request->rent_end_date,
            'handover_date'      => $request->handover_date,
            'rent_due_date'      => $request->rent_due_date,
            'lock_in_period'     => $request->lock_in_period,
            'notice_period'      => $request->notice_period,
            'meter_reading'      => $request->meter_reading,
            'escalation_percent' => $request->escalation_percent,
            'payment_status'     => $request->payment_status,
            'rental_status'      => $request->rental_status,
            'remarks'            => $request->remarks,
        ];

        if ($request->hasFile('agreement_document')) {
            $data['agreement_document'] = $request->file('agreement_document')->store('rental_documents', 'public');
        }

        $rental = Rental::create($data);

        if ($request->has('firm_ids') && is_array($request->firm_ids) && !empty($request->firm_ids)) {
            $rental->syncFirms($request->firm_ids);
        } elseif ($firmId) {
            $rental->syncFirms([$firmId]);
        }

        $this->updatePropertyStatus($rental);

        return redirect()->route('rentals.index')->with('success', 'Rental agreement added successfully.');
    }

    public function show(Rental $rental)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $rental->firm_id != $firmId) {
            abort(403);
        }

        $rental->load(['firm', 'property.propertyType', 'property.project.propertyMaster', 'tenant']);

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

        $propQuery = Property::with(['project.propertyMaster'])->orderBy('property_name');
        if (!$isAdmin) {
            $propQuery->where('firm_id', $rental->firm_id ?: $firmId);
        }
        $properties = $propQuery->get();

        $projQuery = \App\Models\Project::with('propertyMaster')->orderBy('project_name');
        if (!$isAdmin && ($rental->firm_id ?: $firmId)) {
            $projQuery->where('firm_id', $rental->firm_id ?: $firmId);
        }
        $projects = $projQuery->get();

        $selectedProjectId = $rental->property ? $rental->property->project_id : null;

        $tenantQuery = \App\Models\Tenant::with('firm')->where('status', 'active')->orderBy('name');
        if (!$isAdmin && ($rental->firm_id ?: $firmId)) {
            $tenantQuery->where('firm_id', $rental->firm_id ?: $firmId);
        }
        $tenants = $tenantQuery->get();

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.rentals.edit', compact('rental', 'properties', 'projects', 'tenants', 'firms', 'selectedProjectId'));
    }

    public function update(RentalRequest $request, Rental $rental)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $rental->firm_id != $firmId) {
            abort(403);
        }

        $newFirmId = $request->firm_id;
        if (!$newFirmId && $request->has('firm_ids')) {
            $firmIds = (array) $request->firm_ids;
            $newFirmId = $firmIds[0] ?? null;
        }
        if (!$newFirmId) {
            $newFirmId = $rental->firm_id;
        }

        $data = [
            'firm_id'            => $newFirmId,
            'property_id'        => $request->property_id,
            'tenant_id'          => $request->tenant_id,
            'agreement_no'       => $request->agreement_no,
            'tenant_name'        => $request->tenant_name,
            'tenant_mobile'      => $request->tenant_mobile,
            'tenant_email'       => $request->tenant_email,
            'rent_amount'        => $request->rent_amount,
            'security_deposit'   => $request->security_deposit,
            'maintenance_amount' => $request->maintenance_amount,
            'rent_start_date'    => $request->rent_start_date,
            'rent_end_date'      => $request->rent_end_date,
            'handover_date'      => $request->handover_date,
            'rent_due_date'      => $request->rent_due_date,
            'lock_in_period'     => $request->lock_in_period,
            'notice_period'      => $request->notice_period,
            'meter_reading'      => $request->meter_reading,
            'escalation_percent' => $request->escalation_percent,
            'payment_status'     => $request->payment_status,
            'rental_status'      => $request->rental_status,
            'remarks'            => $request->remarks,
        ];

        if ($request->hasFile('agreement_document')) {
            if ($rental->agreement_document && \Illuminate\Support\Facades\Storage::disk('public')->exists($rental->agreement_document)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($rental->agreement_document);
            }
            $data['agreement_document'] = $request->file('agreement_document')->store('rental_documents', 'public');
        }

        $rental->update($data);

        if ($request->has('firm_ids') && is_array($request->firm_ids) && !empty($request->firm_ids)) {
            $rental->syncFirms($request->firm_ids);
        } elseif ($newFirmId) {
            $rental->syncFirms([$newFirmId]);
        }

        $this->updatePropertyStatus($rental);

        return redirect()->route('rentals.index')->with('success', 'Rental agreement updated successfully.');
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
