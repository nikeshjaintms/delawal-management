<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorRequest;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query();
        
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();

        if (!$isAdmin) {
            $query->where('firm_id', $user ? $user->firm_id : session('firm_id'));
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('mobile', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('city', 'like', '%' . $request->search . '%')
                    ->orWhere('gst_no', 'like', '%' . $request->search . '%');
            });
        }

        $vendors = $query->latest()->paginate(10)->withQueryString();

        return view('admin.vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('admin.vendors.create');
    }

    public function store(VendorRequest $request)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');

        Vendor::create([
            'firm_id'       => $request->firm_id ?? $firmId,
            'name'          => $request->name,
            'mobile'        => $request->mobile,
            'email'         => $request->email,
            'gst_no'        => $request->gst_no,
            'address'       => $request->address,
            'city'          => $request->city,
            'payment_terms' => $request->payment_terms,
            'status'        => $request->status,
        ]);

        return redirect()->route('vendors.index')->with('success', 'Vendor added successfully.');
    }

    public function show(Vendor $vendor)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $vendor->firm_id != $firmId) {
            abort(403);
        }

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

        return view('admin.vendors.edit', compact('vendor'));
    }

    public function update(VendorRequest $request, Vendor $vendor)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $vendor->firm_id != $firmId) {
            abort(403);
        }

        $vendor->update([
            'firm_id'       => $request->firm_id ?? $vendor->firm_id,
            'name'          => $request->name,
            'mobile'        => $request->mobile,
            'email'         => $request->email,
            'gst_no'        => $request->gst_no,
            'address'       => $request->address,
            'city'          => $request->city,
            'payment_terms' => $request->payment_terms,
            'status'        => $request->status,
        ]);

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
}
