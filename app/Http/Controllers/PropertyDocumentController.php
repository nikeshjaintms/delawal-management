<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyDocumentRequest;

use App\Models\Property;
use App\Models\PropertyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyDocumentController extends Controller
{
    /* ── INDEX ─────────────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $firmId = Auth::user()->firm_id;

        $properties = Property::where('firm_id', $firmId)->orderBy('property_name')->get();

        $query = PropertyDocument::with('property')
            ->whereHas('property', fn($q) => $q->where('firm_id', $firmId));

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
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
                  ->orWhere('document_type', 'like', "%{$s}%");
            });
        }

        $documents   = $query->latest()->paginate(15)->withQueryString();
        $documentTypes = PropertyDocument::documentTypes();

        return view('admin.property-documents.index',
            compact('documents', 'properties', 'documentTypes'));
    }

    /* ── CREATE ─────────────────────────────────────────────────────── */
    public function create()
    {
        $properties    = Property::where('firm_id', Auth::user()->firm_id)
                            ->orderBy('property_name')->get();
        $documentTypes = PropertyDocument::documentTypes();

        return view('admin.property-documents.create',
            compact('properties', 'documentTypes'));
    }

    /* ── STORE ──────────────────────────────────────────────────────── */
    public function store(PropertyDocumentRequest $request)
    {
        

        // Authorise: property must belong to logged-in firm
        $property = Property::findOrFail($request->property_id);
        if ($property->firm_id !== Auth::user()->firm_id) {
            abort(403);
        }

        $filePath = $request->file('document_file')
            ->store('property-documents', 'public');

        $doc = PropertyDocument::create([
            'property_id'     => $request->property_id,
            'document_type'   => $request->document_type,
            'document_title'  => $request->document_title,
            'document_file'   => $filePath,
            'document_number' => $request->document_number,
            'expiry_date'     => $request->expiry_date ?: null,
            'remarks'         => $request->remarks,
            'status'          => $request->status,
            'created_by'      => Auth::id(),
        ]);

        \App\Models\AuditLog::log(
            'Property Documents',
            'Create',
            "Added document '{$doc->document_title}' for property ID {$doc->property_id}"
        );

        return redirect()->route('property-documents.index')
            ->with('success', 'Property document added successfully!');
    }

    /* ── SHOW ───────────────────────────────────────────────────────── */
    public function show(PropertyDocument $propertyDocument)
    {
        $this->authorise($propertyDocument);
        $propertyDocument->load('property', 'creator');

        return view('admin.property-documents.show',
            ['doc' => $propertyDocument]);
    }

    /* ── EDIT ───────────────────────────────────────────────────────── */
    public function edit(PropertyDocument $propertyDocument)
    {
        $this->authorise($propertyDocument);

        $properties    = Property::where('firm_id', Auth::user()->firm_id)
                            ->orderBy('property_name')->get();
        $documentTypes = PropertyDocument::documentTypes();

        return view('admin.property-documents.edit',
            ['doc' => $propertyDocument, 'properties' => $properties, 'documentTypes' => $documentTypes]);
    }

    /* ── UPDATE ─────────────────────────────────────────────────────── */
    public function update(PropertyDocumentRequest $request, PropertyDocument $propertyDocument)
    {
        $this->authorise($propertyDocument);

        

        $filePath = $propertyDocument->document_file;

        if ($request->hasFile('document_file')) {
            Storage::disk('public')->delete($filePath);
            $filePath = $request->file('document_file')
                ->store('property-documents', 'public');
        }

        $propertyDocument->update([
            'property_id'     => $request->property_id,
            'document_type'   => $request->document_type,
            'document_title'  => $request->document_title,
            'document_file'   => $filePath,
            'document_number' => $request->document_number,
            'expiry_date'     => $request->expiry_date ?: null,
            'remarks'         => $request->remarks,
            'status'          => $request->status,
        ]);

        \App\Models\AuditLog::log(
            'Property Documents',
            'Update',
            "Updated document '{$propertyDocument->document_title}' (ID {$propertyDocument->id})"
        );

        return redirect()->route('property-documents.index')
            ->with('success', 'Property document updated successfully!');
    }

    /* ── DESTROY ────────────────────────────────────────────────────── */
    public function destroy(PropertyDocument $propertyDocument)
    {
        $this->authorise($propertyDocument);

        $title = $propertyDocument->document_title;
        Storage::disk('public')->delete($propertyDocument->document_file);
        $propertyDocument->delete();

        \App\Models\AuditLog::log(
            'Property Documents',
            'Delete',
            "Deleted document '{$title}'"
        );

        return redirect()->route('property-documents.index')
            ->with('success', 'Document deleted successfully!');
    }

    /* ── Helper ─────────────────────────────────────────────────────── */
    private function authorise(PropertyDocument $doc): void
    {
        if ($doc->property->firm_id !== Auth::user()->firm_id) {
            abort(403);
        }
    }
}
