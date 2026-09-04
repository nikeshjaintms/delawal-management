<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyDocumentRequest;
use App\Models\Property;
use App\Models\PropertyMaster;
use App\Models\PropertyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyDocumentController extends Controller
{
    private function firmPropertyMasters($firmId = null)
    {
        if (!$firmId) {
            $isAdmin = auth()->user() && auth()->user()->isAdmin();
            $firmId = $isAdmin ? null : (auth()->user() ? auth()->user()->firm_id : session('firm_id'));
        }

        if ($firmId) {
            return PropertyMaster::where('firm_id', $firmId);
        }

        return PropertyMaster::query();
    }

    /* ── INDEX ─────────────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();

        if ($isAdmin) {
            $propertyMasters = PropertyMaster::orderBy('property_name')->get();
            $query = PropertyDocument::with(['propertyMaster.firm', 'property', 'firm']);
            if ($request->filled('firm_id')) {
                $query->where('firm_id', $request->firm_id);
            }
        } else {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            $propertyMasters = $this->firmPropertyMasters($firmId)->orderBy('property_name')->get();
            $query = PropertyDocument::with(['propertyMaster.firm', 'property', 'firm'])
                ->where('firm_id', $firmId);
        }

        if ($request->filled('property_master_id')) {
            $query->where('property_master_id', $request->property_master_id);
        }
        if ($request->filled('property_id')) {
            $query->where(function($q) use ($request) {
                $q->where('property_master_id', $request->property_id)
                  ->orWhere('property_id', $request->property_id);
            });
        }
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('document_title', 'like', "%{$s}%")
                  ->orWhere('document_number', 'like', "%{$s}%")
                  ->orWhere('document_type', 'like', "%{$s}%")
                  ->orWhereHas('propertyMaster', fn($pm) =>
                        $pm->where('property_name', 'like', "%{$s}%")
                           ->orWhere('property_code', 'like', "%{$s}%")
                  );
            });
        }

        $documents     = $query->latest()->paginate(15)->withQueryString();
        $documentTypes = PropertyDocument::documentTypes();

        return view('admin.property-documents.index',
            compact('documents', 'propertyMasters', 'documentTypes'));
    }

    /* ── CREATE ─────────────────────────────────────────────────────── */
    public function create()
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        if ($isAdmin) {
            $propertyMasters = PropertyMaster::with(['firm', 'firms'])->orderBy('property_name')->get();
        } else {
            $propertyMasters = $this->firmPropertyMasters($firmId)->with(['firm', 'firms'])->orderBy('property_name')->get();
        }
        $documentTypes = PropertyDocument::documentTypes();

        return view('admin.property-documents.create',
            compact('propertyMasters', 'documentTypes'));
    }

    /* ── STORE ──────────────────────────────────────────────────────── */
    public function store(PropertyDocumentRequest $request)
    {
        $masterId = $request->property_master_id ?: $request->property_id;
        $propertyMaster = PropertyMaster::find($masterId);

        $filePath = null;
        if ($request->hasFile('document_file')) {
            $filePath = $request->file('document_file')->store('property-documents', 'public');
        }

        if ($propertyMaster) {
            $isAdmin = auth()->user() && auth()->user()->isAdmin();
            if (!$isAdmin) {
                $userFirmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
                if ($propertyMaster->firm_id && $propertyMaster->firm_id != $userFirmId) {
                    abort(403);
                }
            }

            $firmId = $propertyMaster->firm_id ?: ($request->firm_id ?: (auth()->user() ? auth()->user()->firm_id : 1));

            $doc = PropertyDocument::create([
                'firm_id'            => $firmId,
                'property_master_id' => $propertyMaster->id,
                'property_id'        => null,
                'document_type'      => $request->document_type,
                'document_title'     => $request->document_title,
                'document_file'      => $filePath,
                'document_number'    => $request->document_number,
                'expiry_date'        => $request->expiry_date ?: null,
                'remarks'            => $request->remarks,
                'status'             => $request->status,
                'created_by'         => auth()->id(),
            ]);

            \App\Models\AuditLog::log(
                'Property Documents',
                'Create',
                "Added document '{$doc->document_title}' for Land Property '{$propertyMaster->property_name}'"
            );

            return redirect()->route('property-documents.index')
                ->with('success', 'Land property document added successfully!');
        }

        // Fallback for Property
        $property = Property::findOrFail($request->property_id);
        $firmId = $property->firm_id ?: 1;

        $doc = PropertyDocument::create([
            'firm_id'            => $firmId,
            'property_master_id' => null,
            'property_id'        => $property->id,
            'document_type'      => $request->document_type,
            'document_title'     => $request->document_title,
            'document_file'      => $filePath,
            'document_number'    => $request->document_number,
            'expiry_date'        => $request->expiry_date ?: null,
            'remarks'            => $request->remarks,
            'status'             => $request->status,
            'created_by'         => auth()->id(),
        ]);

        return redirect()->route('property-documents.index')
            ->with('success', 'Property document added successfully!');
    }

    /* ── SHOW ───────────────────────────────────────────────────────── */
    public function show(PropertyDocument $propertyDocument)
    {
        $this->authorise($propertyDocument);
        $propertyDocument->load('propertyMaster.firm', 'property', 'creator');

        return view('admin.property-documents.show',
            ['doc' => $propertyDocument]);
    }

    /* ── EDIT ───────────────────────────────────────────────────────── */
    public function edit(PropertyDocument $propertyDocument)
    {
        $this->authorise($propertyDocument);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if ($isAdmin) {
            $propertyMasters = PropertyMaster::with(['firm', 'firms'])->orderBy('property_name')->get();
        } else {
            $propertyMasters = $this->firmPropertyMasters($propertyDocument->firm_id)->with(['firm', 'firms'])->orderBy('property_name')->get();
        }
        $documentTypes = PropertyDocument::documentTypes();

        return view('admin.property-documents.edit',
            ['doc' => $propertyDocument, 'propertyMasters' => $propertyMasters, 'documentTypes' => $documentTypes]);
    }

    /* ── UPDATE ─────────────────────────────────────────────────────── */
    public function update(PropertyDocumentRequest $request, PropertyDocument $propertyDocument)
    {
        $this->authorise($propertyDocument);

        $masterId = $request->property_master_id ?: $request->property_id;
        $propertyMaster = PropertyMaster::find($masterId);

        $data = [
            'document_type'   => $request->document_type,
            'document_title'  => $request->document_title,
            'document_number' => $request->document_number,
            'expiry_date'     => $request->expiry_date ?: null,
            'remarks'         => $request->remarks,
            'status'          => $request->status,
        ];

        if ($propertyMaster) {
            $data['property_master_id'] = $propertyMaster->id;
            $data['property_id'] = null;
        } elseif ($request->filled('property_id')) {
            $data['property_master_id'] = null;
            $data['property_id'] = $request->property_id;
        }

        if ($request->hasFile('document_file')) {
            if ($propertyDocument->document_file && Storage::disk('public')->exists($propertyDocument->document_file)) {
                Storage::disk('public')->delete($propertyDocument->document_file);
            }
            $data['document_file'] = $request->file('document_file')->store('property-documents', 'public');
        }

        $propertyDocument->update($data);

        return redirect()->route('property-documents.index')
            ->with('success', 'Document updated successfully!');
    }

    /* ── DESTROY ─────────────────────────────────────────────────────── */
    public function destroy(PropertyDocument $propertyDocument)
    {
        $this->authorise($propertyDocument);

        if ($propertyDocument->document_file && Storage::disk('public')->exists($propertyDocument->document_file)) {
            Storage::disk('public')->delete($propertyDocument->document_file);
        }

        $propertyDocument->delete();

        return redirect()->route('property-documents.index')
            ->with('success', 'Property document deleted successfully!');
    }

    /* ── DOWNLOAD ────────────────────────────────────────────────────── */
    public function download(PropertyDocument $propertyDocument)
    {
        $this->authorise($propertyDocument);

        if (!$propertyDocument->document_file || !Storage::disk('public')->exists($propertyDocument->document_file)) {
            return back()->with('error', 'Document file not found.');
        }

        $filename = $propertyDocument->document_title . '.' . pathinfo($propertyDocument->document_file, PATHINFO_EXTENSION);
        return Storage::disk('public')->download($propertyDocument->document_file, $filename);
    }

    private function authorise(PropertyDocument $record): void
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if ($isAdmin) return;

        $userFirmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
        if ($record->firm_id && $record->firm_id != $userFirmId) {
            abort(403, 'Unauthorized access to this document.');
        }
    }
}
