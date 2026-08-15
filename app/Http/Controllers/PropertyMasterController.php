<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyMasterRequest;
use App\Models\PropertyMaster;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyMasterController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $query = PropertyMaster::with('firm')->withCount('projects');

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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $propertyMasters = $query->latest()->paginate(15)->withQueryString();

        return view('admin.property-masters.index', compact('propertyMasters'));
    }

    public function create()
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        if ($isAdmin) {
            $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        } else {
            $firms = Firm::where('id', $firmId)->get();
        }

        return view('admin.property-masters.create', compact('firms'));
    }

    public function store(PropertyMasterRequest $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : (auth()->user() ? auth()->user()->firm_id : session('firm_id'));

        $propertyCode = $request->property_code;
        if (empty($propertyCode)) {
            $latest = PropertyMaster::latest('id')->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $propertyCode = 'PROP-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        }

        $mainImagePath = null;
        $documentPath  = null;

        if ($request->hasFile('main_image')) {
            $mainImagePath = $request->file('main_image')->store('property-masters/images', 'public');
        }
        if ($request->hasFile('document_file')) {
            $documentPath = $request->file('document_file')->store('property-masters/documents', 'public');
        }

        $propertyMaster = PropertyMaster::create([
            'firm_id'       => $firmId,
            'property_name' => $request->property_name,
            'property_code' => $propertyCode,
            'location'      => $request->location,
            'address'       => $request->address,
            'city'          => $request->city,
            'state'         => $request->state,
            'country'       => $request->country,
            'pincode'       => $request->pincode,
            'description'   => $request->description,
            'status'        => $request->status,
            'main_image'    => $mainImagePath,
            'document_file' => $documentPath,
            'created_by'    => auth()->id(),
            'updated_by'    => auth()->id(),
        ]);

        return redirect()->route('property-masters.show', $propertyMaster->id)
            ->with('success', 'Property Master created successfully.');
    }

    public function show(PropertyMaster $propertyMaster)
    {
        $this->authorise($propertyMaster);
        $propertyMaster->load(['firm', 'projects.bulks']);

        return view('admin.property-masters.show', compact('propertyMaster'));
    }

    public function edit(PropertyMaster $propertyMaster)
    {
        $this->authorise($propertyMaster);
        $isAdmin = auth()->user() && auth()->user()->isAdmin();

        if ($isAdmin) {
            $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        } else {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            $firms = Firm::where('id', $firmId)->get();
        }

        return view('admin.property-masters.edit', compact('propertyMaster', 'firms'));
    }

    public function update(PropertyMasterRequest $request, PropertyMaster $propertyMaster)
    {
        $this->authorise($propertyMaster);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : $propertyMaster->firm_id;

        $mainImagePath = $propertyMaster->main_image;
        $documentPath  = $propertyMaster->document_file;

        if ($request->hasFile('main_image')) {
            if ($propertyMaster->main_image) {
                Storage::disk('public')->delete($propertyMaster->main_image);
            }
            $mainImagePath = $request->file('main_image')->store('property-masters/images', 'public');
        }

        if ($request->hasFile('document_file')) {
            if ($propertyMaster->document_file) {
                Storage::disk('public')->delete($propertyMaster->document_file);
            }
            $documentPath = $request->file('document_file')->store('property-masters/documents', 'public');
        }

        $propertyMaster->update([
            'firm_id'       => $firmId,
            'property_name' => $request->property_name,
            'property_code' => $request->property_code ?: $propertyMaster->property_code,
            'location'      => $request->location,
            'address'       => $request->address,
            'city'          => $request->city,
            'state'         => $request->state,
            'country'       => $request->country,
            'pincode'       => $request->pincode,
            'description'   => $request->description,
            'status'        => $request->status,
            'main_image'    => $mainImagePath,
            'document_file' => $documentPath,
            'updated_by'    => auth()->id(),
        ]);

        return redirect()->route('property-masters.show', $propertyMaster->id)
            ->with('success', 'Property Master updated successfully.');
    }

    public function destroy(PropertyMaster $propertyMaster)
    {
        $this->authorise($propertyMaster);

        if ($propertyMaster->main_image) {
            Storage::disk('public')->delete($propertyMaster->main_image);
        }
        if ($propertyMaster->document_file) {
            Storage::disk('public')->delete($propertyMaster->document_file);
        }

        $propertyMaster->delete();

        return redirect()->route('property-masters.index')
            ->with('success', 'Property Master deleted successfully.');
    }

    private function authorise(PropertyMaster $propertyMaster)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if (!$isAdmin) {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            if ($propertyMaster->firm_id != $firmId) {
                abort(403, 'Unauthorized access to Property Master.');
            }
        }
    }
}
