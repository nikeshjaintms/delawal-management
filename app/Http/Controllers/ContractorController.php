<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractorRequest;
use App\Models\Contractor;
use App\Models\Firm;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractorController extends Controller
{
    private function authorise(Contractor $contractor): void
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $contractor->firm_id != $firmId) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $query = Contractor::with(['project', 'projects', 'properties', 'firm', 'firms']);

        if (!$isAdmin) {
            $query->forFirms([$firmId]);
        } elseif ($request->filled('firm_id')) {
            $query->forFirms([$request->firm_id]);
        }

        if ($request->filled('project_id')) {
            $pId = $request->project_id;
            $query->where(function ($q) use ($pId) {
                $q->where('project_id', $pId)
                  ->orWhereHas('projects', function ($pq) use ($pId) {
                      $pq->where('projects.id', $pId);
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('contractor_name', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('aadhar_no', 'like', "%{$s}%")
                  ->orWhere('pan_no', 'like', "%{$s}%")
                  ->orWhere('bank_name', 'like', "%{$s}%")
                  ->orWhere('account_number', 'like', "%{$s}%")
                  ->orWhereHas('projects', function ($pq) use ($s) {
                      $pq->where('project_name', 'like', "%{$s}%");
                  })
                  ->orWhereHas('project', function ($pq) use ($s) {
                      $pq->where('project_name', 'like', "%{$s}%");
                  })
                  ->orWhereHas('properties', function ($pq) use ($s) {
                      $pq->where('property_name', 'like', "%{$s}%")
                         ->orWhere('property_code', 'like', "%{$s}%")
                         ->orWhere('unit_no', 'like', "%{$s}%");
                  });
            });
        }

        $contractors = $query->latest()->paginate(15)->withQueryString();

        $projectsQuery = Project::orderBy('project_name');
        if (!$isAdmin) {
            $projectsQuery->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $projectsQuery->where('firm_id', $request->firm_id);
        }
        $projects = $projectsQuery->get();

        $firms = $isAdmin ? Firm::where('status', 'active')->orderBy('firm_name')->get() : collect();

        return view('admin.contractors.index', compact('contractors', 'projects', 'firms'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if ($isAdmin) {
            $projects = Project::with(['properties', 'propertyMaster', 'firm', 'firms'])->orderBy('project_name')->get();
            $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        } else {
            $projects = Project::forFirms([$firmId])->with(['properties', 'propertyMaster', 'firm', 'firms'])->orderBy('project_name')->get();
            $firms = collect();
        }

        $selectedProjectId = $request->project_id;

        return view('admin.contractors.create', compact('projects', 'firms', 'selectedProjectId'));
    }

    public function store(ContractorRequest $request)
    {
        $user = Auth::user();
        $firmIds = $request->firm_ids;
        if (empty($firmIds)) {
            $firmIds = (array)($user ? $user->firm_id : session('firm_id'));
        }
        $primaryFirmId = reset($firmIds);

        $projectIds = (array) $request->project_ids;
        if (empty($projectIds) && $request->filled('project_id')) {
            $projectIds = [(int)$request->project_id];
        }
        $primaryProjectId = reset($projectIds) ?: null;

        $contractor = Contractor::create([
            'firm_id'         => $primaryFirmId,
            'project_id'      => $primaryProjectId,
            'contractor_name' => $request->contractor_name,
            'mobile'          => $request->mobile,
            'aadhar_no'       => $request->aadhar_no,
            'pan_no'          => $request->pan_no,
            'bank_name'       => $request->bank_name,
            'account_number'  => $request->account_number,
            'ifsc_code'       => $request->ifsc_code,
            'branch_name'     => $request->branch_name,
            'address'         => $request->address,
            'status'          => $request->status,
            'created_by'      => Auth::id(),
            'updated_by'      => Auth::id(),
        ]);

        $contractor->syncFirms($firmIds);
        $contractor->syncProjects($projectIds);

        $propertyIds = (array) $request->property_ids;
        $contractor->syncProperties($propertyIds);

        \App\Models\AuditLog::log(
            'Contractor Management',
            'Create',
            "Created new contractor '{$contractor->contractor_name}' with " . count($projectIds) . " assigned project(s) and " . count($propertyIds) . " assigned plot(s)/unit(s)"
        );

        return redirect()->route('contractors.index')
            ->with('success', "Contractor '{$contractor->contractor_name}' added successfully.");
    }

    public function show(Contractor $contractor)
    {
        $this->authorise($contractor);
        $contractor->load(['project.propertyMaster', 'projects.propertyMaster', 'properties.project', 'firm', 'firms', 'creator', 'updater']);

        return view('admin.contractors.show', compact('contractor'));
    }

    public function edit(Contractor $contractor)
    {
        $this->authorise($contractor);
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $contractor->load(['projects', 'properties', 'firms']);

        if ($isAdmin) {
            $projects = Project::with(['properties', 'propertyMaster', 'firm', 'firms'])->orderBy('project_name')->get();
            $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        } else {
            $projects = Project::forFirms([$contractor->firm_id])->with(['properties', 'propertyMaster', 'firm', 'firms'])->orderBy('project_name')->get();
            $firms = collect();
        }

        return view('admin.contractors.edit', compact('contractor', 'projects', 'firms'));
    }

    public function update(ContractorRequest $request, Contractor $contractor)
    {
        $this->authorise($contractor);
        $user = Auth::user();

        $firmIds = $request->firm_ids;
        if (empty($firmIds)) {
            $firmIds = (array)($user ? $user->firm_id : session('firm_id'));
        }
        $primaryFirmId = reset($firmIds);

        $projectIds = (array) $request->project_ids;
        if (empty($projectIds) && $request->filled('project_id')) {
            $projectIds = [(int)$request->project_id];
        }
        $primaryProjectId = reset($projectIds) ?: null;

        $contractor->update([
            'firm_id'         => $primaryFirmId,
            'project_id'      => $primaryProjectId,
            'contractor_name' => $request->contractor_name,
            'mobile'          => $request->mobile,
            'aadhar_no'       => $request->aadhar_no,
            'pan_no'          => $request->pan_no,
            'bank_name'       => $request->bank_name,
            'account_number'  => $request->account_number,
            'ifsc_code'       => $request->ifsc_code,
            'branch_name'     => $request->branch_name,
            'address'         => $request->address,
            'status'          => $request->status,
            'updated_by'      => Auth::id(),
        ]);

        $contractor->syncFirms($firmIds);
        $contractor->syncProjects($projectIds);

        $propertyIds = (array) $request->property_ids;
        $contractor->syncProperties($propertyIds);

        \App\Models\AuditLog::log(
            'Contractor Management',
            'Update',
            "Updated contractor '{$contractor->contractor_name}' (ID: {$contractor->id}) with " . count($projectIds) . " assigned project(s) and " . count($propertyIds) . " assigned plot(s)/unit(s)"
        );

        return redirect()->route('contractors.index')
            ->with('success', "Contractor '{$contractor->contractor_name}' updated successfully.");
    }

    public function destroy(Contractor $contractor)
    {
        $this->authorise($contractor);
        $name = $contractor->contractor_name;
        $contractor->delete();

        \App\Models\AuditLog::log(
            'Contractor Management',
            'Delete',
            "Deleted contractor '{$name}'"
        );

        return redirect()->route('contractors.index')
            ->with('success', "Contractor '{$name}' deleted successfully.");
    }

    public function getByProject($projectId)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $query = Contractor::where('status', 'active')
            ->where(function ($q) use ($projectId) {
                $q->where('project_id', $projectId)
                  ->orWhereHas('projects', function ($pq) use ($projectId) {
                      $pq->where('projects.id', $projectId);
                  });
            });

        if (!$isAdmin && $firmId) {
            $query->forFirms([$firmId]);
        }

        $contractors = $query->orderBy('contractor_name')->get(['id', 'contractor_name', 'mobile', 'project_id']);
        return response()->json($contractors);
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $query = Contractor::with(['project.propertyMaster', 'projects.propertyMaster', 'properties', 'firm', 'firms']);

        if (!$isAdmin) {
            $query->forFirms([$firmId]);
        } elseif ($request->filled('firm_id')) {
            $query->forFirms([$request->firm_id]);
        }

        if ($request->filled('project_id')) {
            $pId = $request->project_id;
            $query->where(function ($q) use ($pId) {
                $q->where('project_id', $pId)
                  ->orWhereHas('projects', function ($pq) use ($pId) {
                      $pq->where('projects.id', $pId);
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('contractor_name', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('aadhar_no', 'like', "%{$s}%")
                  ->orWhere('pan_no', 'like', "%{$s}%")
                  ->orWhere('bank_name', 'like', "%{$s}%")
                  ->orWhere('account_number', 'like', "%{$s}%")
                  ->orWhereHas('projects', function ($pq) use ($s) {
                      $pq->where('project_name', 'like', "%{$s}%");
                  })
                  ->orWhereHas('project', function ($pq) use ($s) {
                      $pq->where('project_name', 'like', "%{$s}%");
                  })
                  ->orWhereHas('properties', function ($pq) use ($s) {
                      $pq->where('property_name', 'like', "%{$s}%")
                         ->orWhere('property_code', 'like', "%{$s}%")
                         ->orWhere('unit_no', 'like', "%{$s}%");
                  });
            });
        }

        $contractors = $query->latest()->get();
        $totalContractors = $contractors->count();
        $activeContractors = $contractors->where('status', 'active')->count();

        return view('admin.contractors.pdf', compact('contractors', 'totalContractors', 'activeContractors'));
    }

    public function downloadPdf(Contractor $contractor)
    {
        $this->authorise($contractor);
        $contractor->load(['project.propertyMaster', 'projects.propertyMaster', 'properties.project', 'firm', 'firms', 'creator', 'updater']);

        return view('admin.contractors.show-pdf', compact('contractor'));
    }
}
