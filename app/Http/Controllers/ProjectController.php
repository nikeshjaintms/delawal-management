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
        $query = Project::with(['firm', 'propertyMasters', 'propertyMaster'])->withCount('properties');

        if ($isAdmin) {
            if ($request->filled('firm_id')) {
                $query->where('firm_id', $request->firm_id);
            }
        } else {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        }

        if ($request->filled('property_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('property_id', $request->property_id)
                  ->orWhereHas('propertyMasters', function ($sub) use ($request) {
                      $sub->where('property_masters.id', $request->property_id);
                  });
            });
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
            'plots' => function ($q) {
                $q->whereNull('project_id')->where('status', 'available');
            }
        ]);

        if ($isAdmin) {
            $properties = $query->orderBy('property_name')->get();
        } else {
            $properties = $query->where('firm_id', $firmId)->orderBy('property_name')->get();
        }

        $selectedPropertyIds = (array) ($request->get('property_ids') ?: ($request->get('property_id') ? [$request->get('property_id')] : []));

        return view('admin.projects.create', compact('properties', 'selectedPropertyIds'));
    }

    public function store(ProjectRequest $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : (auth()->user() ? auth()->user()->firm_id : session('firm_id'));

        $propertyMasterIds = (array) ($request->property_ids ?: ($request->property_id ? [$request->property_id] : []));
        $propertyMasterIds = array_values(array_filter($propertyMasterIds));

        // Ensure property belongs to firm if specified
        if (!empty($propertyMasterIds)) {
            $prop = PropertyMaster::find($propertyMasterIds[0]);
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

        return DB::transaction(function () use ($request, $firmId, $projectCode, $imagePath, $propertyMasterIds) {
            $primaryPropertyId = !empty($propertyMasterIds) ? $propertyMasterIds[0] : null;

            $project = Project::create([
                'firm_id'       => $firmId,
                'property_id'   => $primaryPropertyId,
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

            // Sync multiple Property Masters to pivot table
            if (!empty($propertyMasterIds)) {
                $project->syncPropertyMasters($propertyMasterIds);
            }

            // Assign selected plots to this project
            if ($request->filled('selected_plot_ids') && is_array($request->selected_plot_ids)) {
                Property::whereIn('id', $request->selected_plot_ids)
                    ->where('firm_id', $firmId)
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
            'propertyMasters',
            'propertyMaster',
            'firm',
            'properties.propertyType',
            'properties.propertyMaster',
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
            'plots' => function ($q) use ($project) {
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

        $project->load(['properties', 'propertyMasters']);

        $selectedPropertyIds = $project->propertyMasters->pluck('id')->toArray();
        if (empty($selectedPropertyIds) && $project->property_id) {
            $selectedPropertyIds = [$project->property_id];
        }

        return view('admin.projects.edit', compact('project', 'properties', 'selectedPropertyIds'));
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorise($project);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : $project->firm_id;

        $propertyMasterIds = (array) ($request->property_ids ?: ($request->property_id ? [$request->property_id] : []));
        $propertyMasterIds = array_values(array_filter($propertyMasterIds));

        if (!empty($propertyMasterIds)) {
            $prop = PropertyMaster::find($propertyMasterIds[0]);
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

        return DB::transaction(function () use ($request, $project, $firmId, $projectCode, $imagePath, $propertyMasterIds) {
            $primaryPropertyId = !empty($propertyMasterIds) ? $propertyMasterIds[0] : null;

            $project->update([
                'firm_id'       => $firmId,
                'property_id'   => $primaryPropertyId,
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

            // Sync multiple property masters
            $project->syncPropertyMasters($propertyMasterIds);

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
                        ->update(['project_id' => $project->id]);
                }
            } else {
                // If nothing was checked, unassign all available plots
                Property::where('project_id', $project->id)
                    ->where('status', 'available')
                    ->update(['project_id' => null]);
            }

            return redirect()->route('projects.show', $project->id)
                ->with('success', 'Project and plot assignments updated successfully.');
        });
    }

    /**
     * AJAX endpoint: fetch plots from multiple Property Masters
     */
    public function getPropertiesAndPlots(Request $request)
    {
        $propertyIds = $request->get('property_ids');
        if (is_string($propertyIds)) {
            $propertyIds = explode(',', $propertyIds);
        }
        $propertyIds = array_values(array_filter((array) $propertyIds));

        $projectId = $request->get('project_id');

        if (empty($propertyIds)) {
            return response()->json([
                'success'    => true,
                'properties' => [],
            ]);
        }

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        $query = PropertyMaster::whereIn('id', $propertyIds);
        if (!$isAdmin) {
            $query->where('firm_id', $firmId);
        }

        $propertyMasters = $query->with(['plots' => function ($q) use ($projectId) {
            $q->where(function ($sub) use ($projectId) {
                $sub->whereNull('project_id');
                if ($projectId) {
                    $sub->orWhere('project_id', $projectId);
                }
            })->orderByRaw('CAST(COALESCE(NULLIF(unit_no, ""), id) AS UNSIGNED) ASC, id ASC');
        }])->get();

        $result = [];
        foreach ($propertyMasters as $pm) {
            $result[] = [
                'id'            => $pm->id,
                'property_name' => $pm->property_name,
                'property_code' => $pm->property_code,
                'location'      => $pm->location,
                'city'          => $pm->city,
                'address'       => $pm->address,
                'state'         => $pm->state,
                'country'       => $pm->country,
                'pincode'       => $pm->pincode,
                'full_address'  => $pm->full_address,
                'purchase_rate' => $pm->purchase_rate,
                'plots'         => $pm->plots->map(function ($plot) {
                    return [
                        'id'            => $plot->id,
                        'property_name' => $plot->property_name,
                        'property_code' => $plot->property_code,
                        'unit_no'       => $plot->unit_no,
                        'size'          => $plot->size,
                        'size_unit'     => $plot->size_unit,
                        'facing'        => $plot->facing,
                        'purchase_rate' => $plot->purchase_rate,
                        'price'         => $plot->price,
                        'status'        => $plot->status,
                        'project_id'    => $plot->project_id,
                    ];
                }),
            ];
        }

        return response()->json([
            'success'    => true,
            'properties' => $result,
        ]);
    }

    public function destroy(Project $project)
    {
        $this->authorise($project);

        // Check if any plots in this project are already booked or sold
        $bookedOrSold = $project->properties()->whereIn('status', ['booked', 'sold'])->count();
        if ($bookedOrSold > 0) {
            return redirect()->back()
                ->with('error', "Cannot delete Project because {$bookedOrSold} plot(s) are already booked or sold.");
        }

        if ($project->project_image) {
            Storage::disk('public')->delete($project->project_image);
        }

        $propertyId = $project->property_id;

        DB::transaction(function () use ($project) {
            // Unassign master plots back to available inventory
            $project->properties()
                ->whereNotNull('property_master_id')
                ->update(['project_id' => null]);

            // Delete any standalone plots created solely for this project
            $project->properties()->whereNull('property_master_id')->delete();

            // Detach pivot table
            $project->propertyMasters()->detach();

            // Delete contractors attached to this project
            $project->contractors()->delete();

            $project->delete();
        });

        if ($propertyId) {
            return redirect()->route('property-masters.show', $propertyId)
                ->with('success', 'Project deleted successfully and unbooked plots returned to available inventory.');
        }

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    public function exportPdf(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $query = Project::with(['firm', 'propertyMasters', 'propertyMaster', 'properties'])->withCount('properties');

        if ($isAdmin) {
            if ($request->filled('firm_id')) {
                $query->where('firm_id', $request->firm_id);
            }
        } else {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        }

        if ($request->filled('property_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('property_id', $request->property_id)
                  ->orWhereHas('propertyMasters', function ($sub) use ($request) {
                      $sub->where('property_masters.id', $request->property_id);
                  });
            });
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

        $projects = $query->latest()->get();
        $totalProjects = $projects->count();
        $totalPlots = $projects->sum('properties_count');
        $totalValue = $projects->sum(function ($p) {
            return $p->properties->sum('price');
        });

        return view('admin.projects.pdf', compact('projects', 'totalProjects', 'totalPlots', 'totalValue'));
    }

    public function downloadPdf(Project $project)
    {
        $this->authorise($project);
        $project->load([
            'propertyMasters',
            'propertyMaster',
            'firm',
            'properties.propertyType',
            'properties.propertyMaster',
            'contractors',
        ]);

        $totalPlots = $project->properties->count();
        $availablePlots = $project->properties->where('status', 'available')->count();
        $bookedPlots = $project->properties->where('status', 'booked')->count();
        $soldPlots = $project->properties->where('status', 'sold')->count();
        $totalValue = $project->properties->sum('price');
        $totalArea = $project->properties->sum('size');

        return view('admin.projects.show-pdf', compact(
            'project', 'totalPlots', 'availablePlots', 'bookedPlots', 'soldPlots', 'totalValue', 'totalArea'
        ));
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
