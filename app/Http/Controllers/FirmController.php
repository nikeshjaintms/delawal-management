<?php

namespace App\Http\Controllers;

use App\Http\Requests\FirmRequest;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FirmController extends Controller
{
    public function index(Request $request)
    {
        $query = Firm::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('firm_name', 'like', "%{$s}%")
                  ->orWhere('owner_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('gst_no', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $firms = $query->latest()->paginate(10)->withQueryString();

        return view('admin.firm-management.firms.index', compact('firms'));
    }

    public function create()
    {
        return view('admin.firm-management.firms.create');
    }

    public function store(FirmRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('firm_logo')) {
            $validated['firm_logo'] = $request->file('firm_logo')
                ->store('firm-logos', 'public');
        }

        if (!empty($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        Firm::create($validated);

        return redirect()->route('firm-master.index')
            ->with('success', 'Firm created successfully!');
    }

    public function show(Firm $firmMaster)
    {
        return view('admin.firm-management.firms.show', ['firm' => $firmMaster]);
    }

    public function edit(Firm $firmMaster)
    {
        return view('admin.firm-management.firms.edit', ['firm' => $firmMaster]);
    }

    public function update(FirmRequest $request, Firm $firmMaster)
    {
        $validated = $request->validated();
        if ($request->hasFile('firm_logo')) {
            // Delete old logo
            if ($firmMaster->firm_logo) {
                Storage::disk('public')->delete($firmMaster->firm_logo);
            }
            $validated['firm_logo'] = $request->file('firm_logo')
                ->store('firm-logos', 'public');
        }

        if (!empty($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $firmMaster->update($validated);

        return redirect()->route('firm-master.index')
            ->with('success', 'Firm updated successfully!');
    }

    public function destroy(Firm $firmMaster)
    {
        if ($firmMaster->firm_logo) {
            Storage::disk('public')->delete($firmMaster->firm_logo);
        }

        $firmMaster->delete();

        return redirect()->route('firm-master.index')
            ->with('success', 'Firm deleted successfully!');
    }
}
