<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrokerRequest;

use App\Models\Broker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrokerController extends Controller
{
    public function index(Request $request)
    {
        $query = Broker::query();
        
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
                    ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        $brokers = $query->latest()->paginate(10)->withQueryString();

        return view('admin.brokers.index', compact('brokers'));
    }

    public function create()
    {
        return view('admin.brokers.create');
    }

    public function store(BrokerRequest $request)
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');

        Broker::create([
            'firm_id'               => $request->firm_id ?? $firmId,
            'name'                  => $request->name,
            'mobile'                => $request->mobile,
            'email'                 => $request->email,
            'address'               => $request->address,
            'city'                  => $request->city,
            'commission_percentage' => $request->commission_percentage,
            'status'                => $request->status,
        ]);

        return redirect()->route('brokers.index')->with('success', 'Broker added successfully.');
    }

    public function show(Broker $broker)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $broker->firm_id != $firmId) {
            abort(403);
        }

        return view('admin.brokers.show', compact('broker'));
    }

    public function edit(Broker $broker)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $broker->firm_id != $firmId) {
            abort(403);
        }

        return view('admin.brokers.edit', compact('broker'));
    }

    public function update(BrokerRequest $request, Broker $broker)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $broker->firm_id != $firmId) {
            abort(403);
        }

        $broker->update([
            'firm_id'               => $request->firm_id ?? $broker->firm_id,
            'name'                  => $request->name,
            'mobile'                => $request->mobile,
            'email'                 => $request->email,
            'address'               => $request->address,
            'city'                  => $request->city,
            'commission_percentage' => $request->commission_percentage,
            'status'                => $request->status,
        ]);

        return redirect()->route('brokers.index')->with('success', 'Broker updated successfully.');
    }

    public function destroy(Broker $broker)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $broker->firm_id != $firmId) {
            abort(403);
        }

        $broker->delete();

        return redirect()->route('brokers.index')->with('success', 'Broker deleted successfully.');
    }
}
