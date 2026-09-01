<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrokerRequest;

use App\Models\Broker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrokerController extends Controller
{
    public function index(Request $request)
    {
        $query = Broker::with(['firm', 'firms', 'project.propertyMaster']);
        
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            $query->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('mobile', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('city', 'like', '%' . $request->search . '%')
                    ->orWhereHas('project', function($pq) use ($request) {
                        $pq->where('project_name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $brokers = $query->latest()->paginate(10)->withQueryString();

        $projectQuery = \App\Models\Project::with('propertyMaster')->where('status', 'active')->orderBy('project_name');
        $firms = \App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get();
        if (!$isAdmin && $firmId) {
            $projectQuery->where('firm_id', $firmId);
        }
        $projects = $projectQuery->get();

        return view('admin.brokers.index', compact('brokers', 'projects', 'firms'));
    }

    public function create()
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $projectQuery = \App\Models\Project::with('propertyMaster')->where('status', 'active')->orderBy('project_name');
        if (!$isAdmin && $firmId) {
            $projectQuery->where('firm_id', $firmId);
        }
        $projects = $projectQuery->get();

        return view('admin.brokers.create', compact('projects'));
    }

    public function store(BrokerRequest $request)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if ($request->filled('firm_ids') && is_array($request->firm_ids)) {
            $firmId = $request->firm_ids[0] ?? $firmId;
        } elseif ($request->filled('firm_id')) {
            $firmId = $request->firm_id;
        }

        if (empty($firmId)) {
            $defaultFirm = \App\Models\Firm::where('status', 'active')->first();
            $firmId = $defaultFirm ? $defaultFirm->id : 1;
        }

        $broker = Broker::create([
            'firm_id'               => $firmId,
            'project_id'            => $request->project_id ?: null,
            'name'                  => $request->name,
            'mobile'                => $request->mobile,
            'email'                 => $request->email,
            'address'               => $request->address,
            'city'                  => $request->city,
            'commission_percentage' => $request->commission_percentage,
            'status'                => $request->status,
        ]);

        if ($request->filled('firm_ids') && is_array($request->firm_ids)) {
            $broker->syncFirms($request->firm_ids);
        }

        return redirect()->route('brokers.index')->with('success', 'Broker added successfully.');
    }

    public function show(Broker $broker)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $broker->firm_id != $firmId) {
            abort(403);
        }

        $broker->load(['firm', 'firms', 'project.propertyMaster']);

        return view('admin.brokers.show', compact('broker'));
    }

    public function edit(Broker $broker)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $broker->firm_id != $firmId) {
            abort(403);
        }

        $projectQuery = \App\Models\Project::with('propertyMaster')->where('status', 'active')->orderBy('project_name');
        if (!$isAdmin && $firmId) {
            $projectQuery->where('firm_id', $firmId);
        }
        $projects = $projectQuery->get();

        return view('admin.brokers.edit', compact('broker', 'projects'));
    }

    public function update(BrokerRequest $request, Broker $broker)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $broker->firm_id != $firmId) {
            abort(403);
        }

        $targetFirmId = $broker->firm_id;
        if ($request->filled('firm_ids') && is_array($request->firm_ids)) {
            $targetFirmId = $request->firm_ids[0] ?? $targetFirmId;
        } elseif ($request->filled('firm_id')) {
            $targetFirmId = $request->firm_id;
        }

        $broker->update([
            'firm_id'               => $targetFirmId,
            'project_id'            => $request->project_id ?: null,
            'name'                  => $request->name,
            'mobile'                => $request->mobile,
            'email'                 => $request->email,
            'address'               => $request->address,
            'city'                  => $request->city,
            'commission_percentage' => $request->commission_percentage,
            'status'                => $request->status,
        ]);

        if ($request->filled('firm_ids') && is_array($request->firm_ids)) {
            $broker->syncFirms($request->firm_ids);
        }

        return redirect()->route('brokers.index')->with('success', 'Broker updated successfully.');
    }

    public function destroy(Broker $broker)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $broker->firm_id != $firmId) {
            abort(403);
        }

        $broker->delete();

        return redirect()->route('brokers.index')->with('success', 'Broker deleted successfully.');
    }
}
