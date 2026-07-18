<?php

namespace App\Http\Controllers;

use App\Http\Requests\TenantRequest;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::where('firm_id', Auth::user()->firm_id);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('mobile', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('city', 'like', '%' . $request->search . '%')
                    ->orWhere('identity_number', 'like', '%' . $request->search . '%');
            });
        }

        $tenants = $query->latest()->paginate(10);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(TenantRequest $request)
    {
        

        $documentPath = null;
        if ($request->hasFile('document_file')) {
            $documentPath = $request->file('document_file')->store('tenant-documents', 'public');
        }

        Tenant::create([
            'firm_id'         => Auth::user()->firm_id,
            'name'            => $request->name,
            'mobile'          => $request->mobile,
            'email'           => $request->email,
            'address'         => $request->address,
            'city'            => $request->city,
            'identity_type'   => $request->identity_type,
            'identity_number' => $request->identity_number,
            'document_file'   => $documentPath,
            'status'          => $request->status,
        ]);

        return redirect()->route('tenants.index')->with('success', 'Tenant added successfully.');
    }

    public function show(Tenant $tenant)
    {
        if ($tenant->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        return view('admin.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        if ($tenant->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(TenantRequest $request, Tenant $tenant)
    {
        if ($tenant->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        

        $documentPath = $tenant->document_file;
        if ($request->hasFile('document_file')) {
            // Delete old file if exists
            if ($tenant->document_file) {
                Storage::disk('public')->delete($tenant->document_file);
            }
            $documentPath = $request->file('document_file')->store('tenant-documents', 'public');
        }

        $tenant->update([
            'name'            => $request->name,
            'mobile'          => $request->mobile,
            'email'           => $request->email,
            'address'         => $request->address,
            'city'            => $request->city,
            'identity_type'   => $request->identity_type,
            'identity_number' => $request->identity_number,
            'document_file'   => $documentPath,
            'status'          => $request->status,
        ]);

        return redirect()->route('tenants.index')->with('success', 'Tenant updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        if ($tenant->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        if ($tenant->document_file) {
            Storage::disk('public')->delete($tenant->document_file);
        }

        $tenant->delete();

        return redirect()->route('tenants.index')->with('success', 'Tenant deleted successfully.');
    }
}
