<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $query = Project::with('firm');

        if ($isAdmin) {
            if ($request->filled('firm_id')) {
                $query->where('firm_id', $request->firm_id);
            }
        } else {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
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

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(ProjectRequest $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : (auth()->user() ? auth()->user()->firm_id : session('firm_id'));

        $imagePath = null;
        if ($request->hasFile('project_image')) {
            $imagePath = $request->file('project_image')->store('projects/images', 'public');
        }

        Project::create([
            'firm_id'       => $firmId,
            'project_name'  => $request->project_name,
            'project_code'  => $request->project_code,
            'project_type'  => $request->project_type,
            'address'       => $request->address,
            'city'          => $request->city,
            'state'         => $request->state,
            'country'       => $request->country,
            'pincode'       => $request->pincode,
            'description'   => $request->description,
            'status'        => $request->status,
            'project_image' => $imagePath,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project added successfully.');
    }

    public function show(Project $project)
    {
        $this->authorise($project);
        $project->load('properties');

        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorise($project);

        return view('admin.projects.edit', compact('project'));
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorise($project);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : $project->firm_id;

        $imagePath = $project->project_image;
        if ($request->hasFile('project_image')) {
            if ($project->project_image) {
                Storage::disk('public')->delete($project->project_image);
            }
            $imagePath = $request->file('project_image')->store('projects/images', 'public');
        }

        $project->update([
            'firm_id'       => $firmId,
            'project_name'  => $request->project_name,
            'project_code'  => $request->project_code,
            'project_type'  => $request->project_type,
            'address'       => $request->address,
            'city'          => $request->city,
            'state'         => $request->state,
            'country'       => $request->country,
            'pincode'       => $request->pincode,
            'description'   => $request->description,
            'status'        => $request->status,
            'project_image' => $imagePath,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $this->authorise($project);

        if ($project->project_image) {
            Storage::disk('public')->delete($project->project_image);
        }

        $project->delete();

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
