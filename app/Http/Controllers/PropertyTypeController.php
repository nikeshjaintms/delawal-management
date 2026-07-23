<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyTypeRequest;

use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = PropertyType::with('firms')->whereHas('firms', function($q) {
            $q->where('firms.id', Auth::user()->firm_id);
        });

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%');
            });
        }

        $propertyTypes = $query->latest()->paginate(10);

        return view('admin.property-types.index', compact('propertyTypes'));
    }

    public function create()
    {
        $firms = \App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get();
        return view('admin.property-types.create', compact('firms'));
    }

    public function store(PropertyTypeRequest $request)
    {
        $propertyType = PropertyType::create([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
        ]);
        $propertyType->firms()->attach($request->firm_ids);

        return redirect()->route('property-types.index')->with('success', 'Property type added successfully.');
    }

    public function show(PropertyType $propertyType)
    {
        $propertyType->load('firms');
        if (!$propertyType->firms->contains(Auth::user()->firm_id)) {
            abort(403);
        }

        return view('admin.property-types.show', compact('propertyType'));
    }

    public function edit(PropertyType $propertyType)
    {
        $propertyType->load('firms');
        if (!$propertyType->firms->contains(Auth::user()->firm_id)) {
            abort(403);
        }

        $firms = \App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get();
        return view('admin.property-types.edit', compact('propertyType', 'firms'));
    }

    public function update(PropertyTypeRequest $request, PropertyType $propertyType)
    {
        $propertyType->load('firms');
        if (!$propertyType->firms->contains(Auth::user()->firm_id)) {
            abort(403);
        }

        $propertyType->update([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
        ]);
        $propertyType->firms()->sync($request->firm_ids);

        return redirect()->route('property-types.index')->with('success', 'Property type updated successfully.');
    }

    public function destroy(PropertyType $propertyType)
    {
        $propertyType->load('firms');
        if (!$propertyType->firms->contains(Auth::user()->firm_id)) {
            abort(403);
        }

        $propertyType->delete();

        return redirect()->route('property-types.index')->with('success', 'Property type deleted successfully.');
    }
}
