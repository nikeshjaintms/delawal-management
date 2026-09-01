<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $query = PropertyMaster::with([
            'acquisitionBatches.plots' => function ($q) {
                $q->whereNull('project_id')->where('status', 'available');
            }
        ]);

        if ($isAdmin) {
            $properties = $query->orderBy('property_name')->get();
        } else {
            $properties = $query->where('firm_id', $firmId)->orderBy('property_name')->get();
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

        return DB::transaction(function () use ($request, $firmId, $projectCode, $imagePath) {
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

            // Assign selected plots from acquisition batches to this project
            if ($request->filled('selected_plot_ids') && is_array($request->selected_plot_ids)) {
                Property::whereIn('id', $request->selected_plot_ids)
                    ->where('firm_id', $firmId)
                    ->where('property_master_id', $request->property_id)
                    ->update(['project_id' => $project->id]);
            }

            return redirect()->route('projects.show', $project->id)
                ->with('success', 'Project created successfully with selected plots.');
        });
    }

    public function show(Project $project)
    {
        $this->authorise($project);
        $project->load([
            'propertyMaster.acquisitionBatches',
            'firm',
            'properties.propertyType',
            'properties.acquisitionBatch',
            'contractors',
        ]);

        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorise($project);
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $project->firm_id;

        $query = PropertyMaster::with([
            'acquisitionBatches.plots' => function ($q) use ($project) {
                // Include available unassigned plots PLUS plots already assigned to this project
                $q->where(function ($sub) use ($project) {
                    $sub->whereNull('project_id')
                        ->orWhere('project_id', $project->id);
                });
            }
        ]);

        if ($isAdmin) {
            $properties = $query->orderBy('property_name')->get();
        } else {
            $properties = $query->where('firm_id', $firmId)->orderBy('property_name')->get();
        }

        $project->load('properties');

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

        return DB::transaction(function () use ($request, $project, $firmId, $projectCode, $imagePath) {
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

            if ($request->has('selected_plot_ids')) {
                $selectedIds = (array) $request->selected_plot_ids;

                // Unassign any available plots that were removed from the project
                Property::where('project_id', $project->id)
                    ->whereNotIn('id', $selectedIds)
                    ->where('status', 'available')
                    ->update(['project_id' => null]);

                // Assign newly selected plots
                if (!empty($selectedIds)) {
                    Property::whereIn('id', $selectedIds)
                        ->where('property_master_id', $project->property_id)
                        ->update(['project_id' => $project->id]);
                }
            }

            return redirect()->route('projects.show', $project->id)
                ->with('success', 'Project and plot assignments updated successfully.');
        });
    }

    public function getBatchesAndPlots(PropertyMaster $propertyMaster, Request $request)
    {
        $this->authoriseProperty($propertyMaster);

        $projectId = $request->get('project_id');

        $batches = $propertyMaster->acquisitionBatches()
            ->orderBy('id', 'asc')
            ->with(['plots' => function ($q) use ($projectId) {
                $q->where(function ($sub) use ($projectId) {
                    $sub->whereNull('project_id');
                    if ($projectId) {
                        $sub->orWhere('project_id', $projectId);
                    }
                })->orderByRaw('CAST(COALESCE(NULLIF(unit_no, ""), id) AS UNSIGNED) ASC, id ASC');
            }])
            ->get();

        return response()->json([
            'success'         => true,
            'property_master' => [
                'id'            => $propertyMaster->id,
                'property_name' => $propertyMaster->property_name,
                'property_code' => $propertyMaster->property_code,
                'city'          => $propertyMaster->city,
                'location'      => $propertyMaster->location,
                'address'       => $propertyMaster->address,
                'state'         => $propertyMaster->state,
                'country'       => $propertyMaster->country,
                'pincode'       => $propertyMaster->pincode,
                'full_address'  => $propertyMaster->full_address,
            ],
            'batches'         => $batches,
        ]);
    }

    private function authoriseProperty(PropertyMaster $propertyMaster): void
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if (!$isAdmin) {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            if ($propertyMaster->firm_id != $firmId) {
                abort(403);
            }
        }
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
