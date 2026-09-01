<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('firm');

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();

        if (!$isAdmin) {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('mobile', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('city', 'like', '%' . $request->search . '%')
                    ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', '%' . $request->search . '%'));
            });
        }

        $customers = $query->latest()->paginate(10)->withQueryString();
        $firms = \App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.customers.index', compact('customers', 'firms'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(CustomerRequest $request)
    {
        $user = Auth::user();
        $firmId = $request->firm_id;
        if (!$firmId && $request->has('firm_ids') && is_array($request->firm_ids) && count($request->firm_ids) > 0) {
            $firmId = (int) $request->firm_ids[0];
        }
        if (!$firmId) {
            $firmId = $user ? $user->firm_id : session('firm_id');
        }

        $customer = Customer::create([
            'firm_id'       => $firmId,
            'name'          => $request->name,
            'mobile'        => $request->mobile,
            'email'         => $request->email,
            'address'       => $request->address,
            'city'          => $request->city,
            'customer_type' => $request->customer_type,
            'status'        => $request->status,
        ]);

        if ($request->has('firm_ids') && is_array($request->firm_ids) && method_exists($customer, 'firms')) {
            $customer->firms()->sync($request->firm_ids);
        }

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    public function show(Customer $customer)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $customer->firm_id != $firmId) {
            abort(403);
        }

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $customer->firm_id != $firmId) {
            abort(403);
        }

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $customer->firm_id != $firmId) {
            abort(403);
        }

        $updateFirmId = $request->firm_id;
        if (!$updateFirmId && $request->has('firm_ids') && is_array($request->firm_ids) && count($request->firm_ids) > 0) {
            $updateFirmId = (int) $request->firm_ids[0];
        }
        if (!$updateFirmId) {
            $updateFirmId = $customer->firm_id;
        }

        $customer->update([
            'firm_id'       => $updateFirmId,
            'name'          => $request->name,
            'mobile'        => $request->mobile,
            'email'         => $request->email,
            'address'       => $request->address,
            'city'          => $request->city,
            'customer_type' => $request->customer_type,
            'status'        => $request->status,
        ]);

        if ($request->has('firm_ids') && is_array($request->firm_ids) && method_exists($customer, 'firms')) {
            $customer->firms()->sync($request->firm_ids);
        }

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $customer->firm_id != $firmId) {
            abort(403);
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}