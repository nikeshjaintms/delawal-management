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

        $query = Contractor::with(['project', 'firm']);

        if (!$isAdmin) {
            $query->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
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
                  ->orWhereHas('project', function ($pq) use ($s) {
                      $pq->where('project_name', 'like', "%{$s}%");
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
            $projects = Project::with(['propertyMaster', 'firm', 'firms'])->orderBy('project_name')->get();
            $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        } else {
            $projects = Project::forFirms([$firmId])->with(['propertyMaster', 'firm', 'firms'])->orderBy('project_name')->get();
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

        $contractor = Contractor::create([
            'firm_id'         => $primaryFirmId,
            'project_id'      => $request->project_id,
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

        \App\Models\AuditLog::log(
            'Contractor Management',
            'Create',
            "Created new contractor '{$contractor->contractor_name}' for Project ID {$contractor->project_id}"
        );

        return redirect()->route('contractors.index')
            ->with('success', "Contractor '{$contractor->contractor_name}' added successfully.");
    }

    public function show(Contractor $contractor)
    {
        $this->authorise($contractor);
        $contractor->load(['project.propertyMaster', 'firm', 'creator', 'updater']);

        return view('admin.contractors.show', compact('contractor'));
    }

    public function edit(Contractor $contractor)
    {
        $this->authorise($contractor);
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if ($isAdmin) {
            $projects = Project::with(['propertyMaster', 'firm', 'firms'])->orderBy('project_name')->get();
            $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        } else {
            $projects = Project::forFirms([$contractor->firm_id])->with(['propertyMaster', 'firm', 'firms'])->orderBy('project_name')->get();
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

        $contractor->update([
            'firm_id'         => $primaryFirmId,
            'project_id'      => $request->project_id,
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

        \App\Models\AuditLog::log(
            'Contractor Management',
            'Update',
            "Updated contractor '{$contractor->contractor_name}' (ID: {$contractor->id})"
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

        $query = Contractor::where('project_id', $projectId)->where('status', 'active');
        if (!$isAdmin && $firmId) {
            $query->where('firm_id', $firmId);
        }

        $contractors = $query->orderBy('contractor_name')->get(['id', 'contractor_name', 'mobile', 'project_id']);
        return response()->json($contractors);
    }
}
