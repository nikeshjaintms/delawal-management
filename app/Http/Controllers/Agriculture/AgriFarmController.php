<?php

namespace App\Http\Controllers\Agriculture;

use App\Http\Controllers\Controller;
use App\Models\AgriFarm;
use App\Models\Property;
use App\Models\Project;
use App\Models\Firm;
use App\Models\Customer;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgriFarmController extends Controller
{
    private function authorise(AgriFarm $farm): void
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            if ($farm->firm_id != $firmId && !$farm->firms->contains($firmId)) {
                abort(403);
            }
        }
    }

    private function dropdowns($selectedFirmId = null): array
    {
        $user = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $propQuery = Property::with(['project.propertyMaster', 'firm'])->orderBy('property_name');
        $projQuery = Project::with(['propertyMaster', 'firm'])->where('status', 'active')->orderBy('project_name');
        $sellersQuery = Seller::where('status', 'active')->orderBy('name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $propQuery->where('firm_id', $firmId);
            $projQuery->where('firm_id', $firmId);
            $sellersQuery->where('firm_id', $firmId);
        }

        return [
            'firms'      => $firms,
            'properties' => $propQuery->get(),
            'projects'   => $projQuery->get(),
            'sellers'    => $sellersQuery->get(),
            'areaUnits'  => ['Acre', 'Bigha', 'Guntha', 'Hectare', 'Sq. Yard'],
            'farmTypes'  => ['Owned', 'Leased', 'Contract Farming', 'Shared / Partnership', 'Other'],
            'statuses'   => ['Active', 'Under Preparation', 'Harvested', 'Fallow', 'Inactive'],
        ];
    }

    public function index(Request $request)
    {
        $query = AgriFarm::with(['property', 'project', 'firm', 'firms'])
            ->withCount(['labours', 'expenses', 'incomes']);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            $query->forFirms([$firmId]);
        } elseif ($request->filled('firm_id')) {
            $query->forFirms([$request->firm_id]);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('farm_name', 'like', "%{$s}%")
                  ->orWhere('village', 'like', "%{$s}%")
                  ->orWhere('taluka', 'like', "%{$s}%")
                  ->orWhere('district', 'like', "%{$s}%")
                  ->orWhere('survey_no', 'like', "%{$s}%")
                  ->orWhere('owner_seller_name', 'like', "%{$s}%")
                  ->orWhere('crop_activity', 'like', "%{$s}%")
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                  ->orWhereHas('project', fn($pr) => $pr->where('project_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_type')) {
            $query->where('farm_type', $request->filter_type);
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        if ($request->filled('filter_project')) {
            $query->where('project_id', $request->filter_project);
        }

        $farms = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $totalArea = (clone $query)->sum('land_area');
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $projects = Project::where('status', 'active')->orderBy('project_name')->get();

        return view('admin.agriculture.farms.index', compact('farms', 'totalArea', 'firms', 'projects'));
    }

    public function create()
    {
        return view('admin.agriculture.farms.create', $this->dropdowns());
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $firmId;

        $request->validate([
            'farm_name'         => 'required|string|max:255',
            'property_id'       => 'nullable|exists:properties,id',
            'project_id'        => 'nullable|exists:projects,id',
            'owner_seller_name' => 'nullable|string|max:255',
            'village'           => 'nullable|string|max:150',
            'taluka'            => 'nullable|string|max:150',
            'district'          => 'nullable|string|max:150',
            'survey_no'         => 'nullable|string|max:100',
            'land_area'         => 'required|numeric|min:0',
            'area_unit'         => 'required|string|max:50',
            'farm_type'         => 'required|string|max:100',
            'crop_activity'     => 'nullable|string|max:255',
            'start_date'        => 'nullable|date',
            'status'            => 'required|string|max:50',
            'notes'             => 'nullable|string',
        ]);

        $farm = AgriFarm::create([
            'firm_id'           => $primaryFirmId,
            'property_id'       => $request->property_id ?: null,
            'project_id'        => $request->project_id ?: null,
            'farm_name'         => $request->farm_name,
            'owner_seller_name' => $request->owner_seller_name,
            'village'           => $request->village,
            'taluka'            => $request->taluka,
            'district'          => $request->district,
            'survey_no'         => $request->survey_no,
            'land_area'         => $request->land_area,
            'area_unit'         => $request->area_unit,
            'farm_type'         => $request->farm_type,
            'crop_activity'     => $request->crop_activity,
            'start_date'        => $request->start_date,
            'status'            => $request->status,
            'notes'             => $request->notes,
            'created_by'        => Auth::id(),
        ]);

        $farm->syncFirms($firmIds);

        return redirect()->route('agriculture.farms.show', $farm->id)
            ->with('success', 'Agriculture farm "' . $farm->farm_name . '" created successfully.');
    }

    public function show(AgriFarm $farm)
    {
        $farm->load([
            'property.propertyMaster',
            'project.propertyMaster',
            'firm',
            'firms',
            'labours',
            'expenses.paymentMode',
            'expenses.labour',
            'incomes.customer',
            'incomes.paymentMode',
            'labourPayments.labour'
        ]);

        $this->authorise($farm);

        $totalIncome   = (float) $farm->incomes->sum('total_amount');
        $incomeReceived= (float) $farm->incomes->sum('payment_received');
        $incomePending = (float) $farm->incomes->sum('pending_amount');

        $totalExpense  = (float) $farm->expenses->sum('amount');
        $labourExpense = (float) $farm->expenses->where('category', 'Labour')->sum('amount');
        $netProfit     = round($totalIncome - $totalExpense, 2);

        return view('admin.agriculture.farms.show', compact(
            'farm',
            'totalIncome',
            'incomeReceived',
            'incomePending',
            'totalExpense',
            'labourExpense',
            'netProfit'
        ));
    }

    public function edit(AgriFarm $farm)
    {
        $farm->load(['firms', 'firm']);
        $this->authorise($farm);

        return view('admin.agriculture.farms.edit', array_merge(
            ['farm' => $farm],
            $this->dropdowns($farm->firm_id)
        ));
    }

    public function update(Request $request, AgriFarm $farm)
    {
        $this->authorise($farm);

        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $farm->firm_id ?? $firmId));
        $primaryFirmId = reset($firmIds) ?: $farm->firm_id;

        $request->validate([
            'farm_name'         => 'required|string|max:255',
            'property_id'       => 'nullable|exists:properties,id',
            'project_id'        => 'nullable|exists:projects,id',
            'owner_seller_name' => 'nullable|string|max:255',
            'village'           => 'nullable|string|max:150',
            'taluka'            => 'nullable|string|max:150',
            'district'          => 'nullable|string|max:150',
            'survey_no'         => 'nullable|string|max:100',
            'land_area'         => 'required|numeric|min:0',
            'area_unit'         => 'required|string|max:50',
            'farm_type'         => 'required|string|max:100',
            'crop_activity'     => 'nullable|string|max:255',
            'start_date'        => 'nullable|date',
            'status'            => 'required|string|max:50',
            'notes'             => 'nullable|string',
        ]);

        $farm->update([
            'firm_id'           => $primaryFirmId,
            'property_id'       => $request->property_id ?: null,
            'project_id'        => $request->project_id ?: null,
            'farm_name'         => $request->farm_name,
            'owner_seller_name' => $request->owner_seller_name,
            'village'           => $request->village,
            'taluka'            => $request->taluka,
            'district'          => $request->district,
            'survey_no'         => $request->survey_no,
            'land_area'         => $request->land_area,
            'area_unit'         => $request->area_unit,
            'farm_type'         => $request->farm_type,
            'crop_activity'     => $request->crop_activity,
            'start_date'        => $request->start_date,
            'status'            => $request->status,
            'notes'             => $request->notes,
        ]);

        $farm->syncFirms($firmIds);

        return redirect()->route('agriculture.farms.show', $farm->id)
            ->with('success', 'Agriculture farm details updated successfully.');
    }

    public function destroy(AgriFarm $farm)
    {
        $this->authorise($farm);
        $name = $farm->farm_name;
        $farm->delete();

        return redirect()->route('agriculture.farms.index')
            ->with('success', 'Farm "' . $name . '" deleted successfully.');
    }

    /**
     * AJAX auto-fetch property details
     */
    public function ajaxPropertyDetails(Property $property)
    {
        $property->load(['project.propertyMaster', 'propertyMaster', 'firm']);
        
        $sellerName = null;
        if ($property->propertyMaster && $property->propertyMaster->seller_name) {
            $sellerName = $property->propertyMaster->seller_name;
        }

        return response()->json([
            'property_name'  => $property->property_name,
            'property_code'  => $property->property_code,
            'project_id'     => $property->project_id,
            'project_name'   => $property->project?->project_name ?? $property->propertyMaster?->property_name,
            'location'       => $property->location ?: $property->address,
            'city'           => $property->city,
            'size'           => $property->size,
            'size_unit'      => $property->size_unit,
            'survey_no'      => $property->propertyMaster?->survey_number ?? $property->unit_no,
            'owner_seller'   => $sellerName,
            'firm_id'        => $property->firm_id,
        ]);
    }

    /**
     * AJAX auto-fetch project details
     */
    public function ajaxProjectDetails(Project $project)
    {
        $project->load(['propertyMaster', 'firm']);

        return response()->json([
            'project_name' => $project->project_name,
            'location'     => $project->location ?: $project->propertyMaster?->location,
            'city'         => $project->city,
            'firm_id'      => $project->firm_id,
            'survey_no'    => $project->propertyMaster?->survey_number,
        ]);
    }
}
