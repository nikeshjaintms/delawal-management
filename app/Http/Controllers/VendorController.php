<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorRequest;
use App\Models\Vendor;
use App\Models\Project;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::with(['firm', 'project']);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();

        if (!$isAdmin) {
            $query->where('firm_id', $user ? $user->firm_id : session('firm_id'));
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('project_id')) {
            if ($request->project_id === 'general') {
                $query->whereNull('project_id');
            } else {
                $query->where('project_id', $request->project_id);
            }
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q
                    ->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('mobile', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('city', 'like', '%' . $request->search . '%')
                    ->orWhere('gst_no', 'like', '%' . $request->search . '%')
                    ->orWhereHas('project', function($pq) use ($request) {
                        $pq->where('project_name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $vendors = $query->latest()->paginate(10)->withQueryString();
        $projects = Project::where('status', 'active')->orderBy('project_name')->get();

        return view('admin.vendors.index', compact('vendors', 'projects'));
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->orderBy('project_name')->get();
        return view('admin.vendors.create', compact('projects'));
    }

    public function store(VendorRequest $request)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if ($request->filled('firm_ids') && is_array($request->firm_ids)) {
            $firmId = $request->firm_ids[0] ?? $firmId;
        } elseif ($request->filled('firm_id')) {
            $firmId = $request->firm_id;
        }

        if (empty($firmId)) {
            $defaultFirm = Firm::where('status', 'active')->first();
            $firmId = $defaultFirm ? $defaultFirm->id : 1;
        }

        $vendor = Vendor::create([
            'firm_id' => $firmId,
            'project_id' => $request->project_id ?: null,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'gst_no' => $request->gst_no ? strtoupper($request->gst_no) : null,
            'address' => $request->address,
            'city' => $request->city,
            'payment_terms' => $request->payment_terms,
            'status' => $request->status,
        ]);

        if ($request->filled('firm_ids') && is_array($request->firm_ids)) {
            $vendor->syncFirms($request->firm_ids);
        }

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vendor added successfully.',
                'vendor_id' => $vendor->id,
                'display_text' => $vendor->name . ($vendor->mobile ? " ({$vendor->mobile})" : ''),
            ]);
        }

        return redirect()->route('vendors.index')->with('success', 'Vendor added successfully.');
    }

    public function quickStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'firm_id' => 'nullable|integer',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if ($request->filled('firm_id')) {
            $firmId = $request->firm_id;
        }

        if (empty($firmId)) {
            $defaultFirm = Firm::where('status', 'active')->first();
            $firmId = $defaultFirm ? $defaultFirm->id : 1;
        }

        $vendor = Vendor::create([
            'firm_id' => $firmId,
            'project_id' => $request->project_id ?: null,
            'name' => $request->name,
            'mobile' => $request->mobile ?: null,
            'email' => $request->email ?: null,
            'city' => $request->city ?: null,
            'address' => $request->address ?: null,
            'status' => 'active',
        ]);

        if (method_exists($vendor, 'syncFirms')) {
            $vendor->syncFirms([$firmId]);
        }

        $displayText = $vendor->name . ($vendor->mobile ? " ({$vendor->mobile})" : '');

        return response()->json([
            'success' => true,
            'message' => "Vendor '{$vendor->name}' created successfully.",
            'vendor_id' => $vendor->id,
            'name' => $vendor->name,
            'display_text' => $displayText,
        ]);
    }

    public function show(Vendor $vendor)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $vendor->firm_id != $firmId) {
            abort(403);
        }

        $vendor->load(['firm', 'project', 'purchaseOrders', 'expenses']);

        return view('admin.vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $vendor->firm_id != $firmId) {
            abort(403);
        }

        $projects = Project::where('status', 'active')->orderBy('project_name')->get();

        return view('admin.vendors.edit', compact('vendor', 'projects'));
    }

    public function update(VendorRequest $request, Vendor $vendor)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $vendor->firm_id != $firmId) {
            abort(403);
        }

        $targetFirmId = $vendor->firm_id;
        if ($request->filled('firm_ids') && is_array($request->firm_ids)) {
            $targetFirmId = $request->firm_ids[0] ?? $targetFirmId;
        } elseif ($request->filled('firm_id')) {
            $targetFirmId = $request->firm_id;
        }

        $vendor->update([
            'firm_id' => $targetFirmId,
            'project_id' => $request->project_id ?: null,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'gst_no' => $request->gst_no ? strtoupper($request->gst_no) : null,
            'address' => $request->address,
            'city' => $request->city,
            'payment_terms' => $request->payment_terms,
            'status' => $request->status,
        ]);

        if ($request->filled('firm_ids') && is_array($request->firm_ids)) {
            $vendor->syncFirms($request->firm_ids);
        }

        return redirect()->route('vendors.index')->with('success', 'Vendor updated successfully.');
    }

    public function destroy(Vendor $vendor)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $vendor->firm_id != $firmId) {
            abort(403);
        }

        $vendor->delete();

        return redirect()->route('vendors.index')->with('success', 'Vendor deleted successfully.');
    }

    public function exportPdf(Request $request)
    {
        $query = Vendor::with(['firm', 'project']);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();

        if (!$isAdmin) {
            $query->where('firm_id', $user ? $user->firm_id : session('firm_id'));
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('project_id')) {
            if ($request->project_id === 'general') {
                $query->whereNull('project_id');
            } else {
                $query->where('project_id', $request->project_id);
            }
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q
                    ->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('mobile', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('city', 'like', '%' . $request->search . '%')
                    ->orWhere('gst_no', 'like', '%' . $request->search . '%');
            });
        }

        $vendors = $query->latest()->get();
        $totalVendors = $vendors->count();
        $activeVendors = $vendors->where('status', 'active')->count();

        return view('admin.vendors.pdf', compact('vendors', 'totalVendors', 'activeVendors'));
    }

    public function downloadPdf(Vendor $vendor)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $vendor->firm_id != $firmId) {
            abort(403);
        }

        $vendor->load(['firm', 'project', 'purchaseOrders', 'expenses']);

        return view('admin.vendors.show-pdf', compact('vendor'));
    }
}

