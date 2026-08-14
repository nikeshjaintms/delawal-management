<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Models\PropertyMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $query = Project::with(['firm', 'propertyMaster'])->withCount('properties');

        if ($isAdmin) {
            if ($request->filled('firm_id')) {
                $query->where('firm_id', $request->firm_id);
            }
        } else {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('project_name', 'like', "%{$s}%")
                  ->orWhere('project_code', 'like', "%{$s}%")
                  ->orWhere('project_type', 'like', "%{$s}%")
                  ->orWhere('city',         'like', "%{$s}%")
                  ->orWhere('status',       'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->latest()->paginate(15)->withQueryString();
        
        $propertyMaster = null;
        if ($request->filled('property_id')) {
            $propertyMaster = PropertyMaster::find($request->property_id);
        }

        return view('admin.projects.index', compact('projects', 'propertyMaster'));
    }

    public function create(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        if ($isAdmin) {
            $properties = PropertyMaster::orderBy('property_name')->get();
        } else {
            $properties = PropertyMaster::where('firm_id', $firmId)->orderBy('property_name')->get();
        }

        $selectedPropertyId = $request->get('property_id');

        return view('admin.projects.create', compact('properties', 'selectedPropertyId'));
    }

    public function store(ProjectRequest $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : (auth()->user() ? auth()->user()->firm_id : session('firm_id'));

        // Ensure property belongs to firm if specified
        if ($request->property_id) {
            $prop = PropertyMaster::find($request->property_id);
            if ($prop) {
                $firmId = $prop->firm_id;
            }
        }

        $projectCode = $request->project_code;
        if (empty($projectCode)) {
            $latest = Project::latest('id')->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $projectCode = 'PRJ-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        }

        $imagePath = null;
        if ($request->hasFile('project_image')) {
            $imagePath = $request->file('project_image')->store('projects/images', 'public');
        }

        $project = Project::create([
            'firm_id'       => $firmId,
            'property_id'   => $request->property_id,
            'project_name'  => $request->project_name,
            'project_code'  => $projectCode,
            'project_type'  => $request->project_type,
            'address'       => $request->address,
            'city'          => $request->city,
            'state'         => $request->state,
            'country'       => $request->country,
            'pincode'       => $request->pincode,
            'description'   => $request->description,
            'status'        => $request->status,
            'project_image' => $imagePath,
            'created_by'    => auth()->id(),
            'updated_by'    => auth()->id(),
        ]);

        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $this->authorise($project);
        $project->load(['propertyMaster', 'firm', 'properties.propertyType']);

        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorise($project);
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $project->firm_id;

        if ($isAdmin) {
            $properties = PropertyMaster::orderBy('property_name')->get();
        } else {
            $properties = PropertyMaster::where('firm_id', $firmId)->orderBy('property_name')->get();
        }

        return view('admin.projects.edit', compact('project', 'properties'));
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorise($project);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : $project->firm_id;

        if ($request->property_id) {
            $prop = PropertyMaster::find($request->property_id);
            if ($prop) {
                $firmId = $prop->firm_id;
            }
        }

        $projectCode = $request->project_code;
        if (empty($projectCode)) {
            $projectCode = $project->project_code;
        }

        $imagePath = $project->project_image;
        if ($request->hasFile('project_image')) {
            if ($project->project_image) {
                Storage::disk('public')->delete($project->project_image);
            }
            $imagePath = $request->file('project_image')->store('projects/images', 'public');
        }

        $project->update([
            'firm_id'       => $firmId,
            'property_id'   => $request->property_id,
            'project_name'  => $request->project_name,
            'project_code'  => $projectCode,
            'project_type'  => $request->project_type,
            'address'       => $request->address,
            'city'          => $request->city,
            'state'         => $request->state,
            'country'       => $request->country,
            'pincode'       => $request->pincode,
            'description'   => $request->description,
            'status'        => $request->status,
            'project_image' => $imagePath,
            'updated_by'    => auth()->id(),
        ]);

        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $this->authorise($project);

        if ($project->properties()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete Project because it has associated bulk records.');
        }

        if ($project->project_image) {
            Storage::disk('public')->delete($project->project_image);
        }

        $propertyId = $project->property_id;
        $project->delete();

        if ($propertyId) {
            return redirect()->route('property-masters.show', $propertyId)
                ->with('success', 'Project deleted successfully.');
        }

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    private function authorise(Project $project): void
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if (!$isAdmin) {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            if ($project->firm_id != $firmId) {
                abort(403);
            }
        }
    }
}
