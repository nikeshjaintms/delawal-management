<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyRequest;

use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    // ----------------------------------------------------------------
    // INDEX
    // ----------------------------------------------------------------
    public function index(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $query = Property::with(['propertyType', 'firm']);

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
                $q->where('property_name', 'like', "%{$s}%")
                  ->orWhere('property_code', 'like', "%{$s}%")
                  ->orWhere('location',      'like', "%{$s}%")
                  ->orWhere('city',          'like', "%{$s}%")
                  ->orWhere('status',        'like', "%{$s}%");
            });
        }

        $properties = $query->latest()->paginate(15);

        return view('admin.properties.index', compact('properties'));
    }

    // ----------------------------------------------------------------
    // CREATE
    // ----------------------------------------------------------------
    public function create()
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        if ($isAdmin) {
            $propertyTypes = PropertyType::orderBy('name')->get();
        } else {
            $propertyTypes = PropertyType::where('firm_id', $firmId)->orderBy('name')->get();
        }

        return view('admin.properties.create', compact('propertyTypes'));
    }

    // ----------------------------------------------------------------
    // STORE
    // ----------------------------------------------------------------
    public function store(PropertyRequest $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : (auth()->user() ? auth()->user()->firm_id : session('firm_id'));

        $mainImagePath  = null;
        $documentPath   = null;

        if ($request->hasFile('main_image')) {
            $mainImagePath = $request->file('main_image')
                ->store('properties/images', 'public');
        }

        if ($request->hasFile('document_file')) {
            $documentPath = $request->file('document_file')
                ->store('properties/documents', 'public');
        }

        Property::create([
            'firm_id'          => $firmId,
            'property_type_id' => $request->property_type_id ?: null,
            'property_name'    => $request->property_name,
            'property_code'    => $request->property_code,
            'status'           => $request->status,
            'location'         => $request->location,
            'city'             => $request->city,
            'address'          => $request->address,
            'size'             => $request->size,
            'size_unit'        => $request->size_unit,
            'price'            => $request->price ?: null,
            'unit_no'          => $request->unit_no,
            'floor_no'         => $request->floor_no,
            'facing'           => $request->facing,
            'description'      => $request->description,
            'main_image'       => $mainImagePath,
            'document_file'    => $documentPath,
        ]);

        return redirect()->route('properties.index')
            ->with('success', 'Property added successfully.');
    }

    // ----------------------------------------------------------------
    // SHOW
    // ----------------------------------------------------------------
    public function show(Property $property)
    {
        $this->authorise($property);
        $property->load(['propertyType', 'documents' => fn($q) => $q->latest()]);

        return view('admin.properties.show', compact('property'));
    }

    // ----------------------------------------------------------------
    // EDIT
    // ----------------------------------------------------------------
    public function edit(Property $property)
    {
        $this->authorise($property);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if ($isAdmin) {
            $propertyTypes = PropertyType::orderBy('name')->get();
        } else {
            $propertyTypes = PropertyType::where('firm_id', $property->firm_id)->orderBy('name')->get();
        }

        return view('admin.properties.edit', compact('property', 'propertyTypes'));
    }

    // ----------------------------------------------------------------
    // UPDATE
    // ----------------------------------------------------------------
    public function update(PropertyRequest $request, Property $property)
    {
        $this->authorise($property);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : $property->firm_id;

        $mainImagePath = $property->main_image;
        $documentPath  = $property->document_file;

        if ($request->hasFile('main_image')) {
            if ($property->main_image) {
                Storage::disk('public')->delete($property->main_image);
            }
            $mainImagePath = $request->file('main_image')
                ->store('properties/images', 'public');
        }

        if ($request->hasFile('document_file')) {
            if ($property->document_file) {
                Storage::disk('public')->delete($property->document_file);
            }
            $documentPath = $request->file('document_file')
                ->store('properties/documents', 'public');
        }

        $property->update([
            'firm_id'          => $firmId,
            'property_type_id' => $request->property_type_id ?: null,
            'property_name'    => $request->property_name,
            'property_code'    => $request->property_code,
            'status'           => $request->status,
            'location'         => $request->location,
            'city'             => $request->city,
            'address'          => $request->address,
            'size'             => $request->size,
            'size_unit'        => $request->size_unit,
            'price'            => $request->price ?: null,
            'unit_no'          => $request->unit_no,
            'floor_no'         => $request->floor_no,
            'facing'           => $request->facing,
            'description'      => $request->description,
            'main_image'       => $mainImagePath,
            'document_file'    => $documentPath,
        ]);

        return redirect()->route('properties.index')
            ->with('success', 'Property updated successfully.');
    }

    // ----------------------------------------------------------------
    // DESTROY
    // ----------------------------------------------------------------
    public function destroy(Property $property)
    {
        $this->authorise($property);

        if ($property->main_image) {
            Storage::disk('public')->delete($property->main_image);
        }
        if ($property->document_file) {
            Storage::disk('public')->delete($property->document_file);
        }

        $property->delete();

        return redirect()->route('properties.index')
            ->with('success', 'Property deleted successfully.');
    }

    // ----------------------------------------------------------------
    // Helper
    // ----------------------------------------------------------------
    private function authorise(Property $property): void
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if (!$isAdmin) {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            if ($property->firm_id != $firmId) {
                abort(403);
            }
        }
    }
}
