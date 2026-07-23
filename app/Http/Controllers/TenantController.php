<?php

namespace App\Http\Controllers;

use App\Http\Requests\TenantRequest;
use App\Models\Tenant;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with('firm');

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();

        if (!$isAdmin) {
            $query->where('firm_id', $user ? $user->firm_id : session('firm_id'));
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('identity_number', 'like', "%{$search}%")
                    ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$search}%"));
            });
        }

        $tenants = $query->latest()->paginate(10)->withQueryString();
        $firms   = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.tenants.index', compact('tenants', 'firms'));
    }

    public function create()
    {
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        return view('admin.tenants.create', compact('firms'));
    }

    public function store(TenantRequest $request)
    {
        $user = Auth::user();
        $firmId = $request->firm_id ?? ($user ? $user->firm_id : session('firm_id'));

        $documentPath = null;
        if ($request->hasFile('document_file')) {
            $documentPath = $request->file('document_file')->store('tenant-documents', 'public');
        }

        Tenant::create([
            'firm_id'         => $firmId,
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
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $tenant->firm_id != $firmId) {
            abort(403);
        }

        return view('admin.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $tenant->firm_id != $firmId) {
            abort(403);
        }

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.tenants.edit', compact('tenant', 'firms'));
    }

    public function update(TenantRequest $request, Tenant $tenant)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $tenant->firm_id != $firmId) {
            abort(403);
        }

        $documentPath = $tenant->document_file;
        if ($request->hasFile('document_file')) {
            if ($tenant->document_file) {
                Storage::disk('public')->delete($tenant->document_file);
            }
            $documentPath = $request->file('document_file')->store('tenant-documents', 'public');
        }

        $tenant->update([
            'firm_id'         => $request->firm_id ?? $tenant->firm_id,
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
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $tenant->firm_id != $firmId) {
            abort(403);
        }

        if ($tenant->document_file) {
            Storage::disk('public')->delete($tenant->document_file);
        }

        $tenant->delete();

        return redirect()->route('tenants.index')->with('success', 'Tenant deleted successfully.');
    }
}
