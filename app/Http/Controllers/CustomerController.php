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
        $query = Customer::query();

        if (!Auth::user()->isAdmin()) {
            $query->where('firm_id', Auth::user()->firm_id);
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

        $customers = $query->latest()->paginate(10)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(CustomerRequest $request)
    {
        Customer::create([
            'firm_id' => $request->firm_id ?? Auth::user()->firm_id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'customer_type' => $request->customer_type,
            'status' => $request->status,
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    public function show(Customer $customer)
    {
        if (!Auth::user()->isAdmin() && $customer->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        if (!Auth::user()->isAdmin() && $customer->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        if (!Auth::user()->isAdmin() && $customer->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        $customer->update([
            'firm_id' => $request->firm_id ?? $customer->firm_id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'customer_type' => $request->customer_type,
            'status' => $request->status,
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        if (!Auth::user()->isAdmin() && $customer->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}