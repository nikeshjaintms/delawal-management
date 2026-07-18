<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentModeRequest;

use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentModeController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentMode::where('firm_id', Auth::user()->firm_id);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%');
            });
        }

        $paymentModes = $query->latest()->paginate(10);

        return view('admin.payment-modes.index', compact('paymentModes'));
    }

    public function create()
    {
        return view('admin.payment-modes.create');
    }

    public function store(PaymentModeRequest $request)
    {
        

        PaymentMode::create([
            'firm_id'     => Auth::user()->firm_id,
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
        ]);

        return redirect()->route('payment-modes.index')->with('success', 'Payment mode added successfully.');
    }

    public function show(PaymentMode $paymentMode)
    {
        if ($paymentMode->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        return view('admin.payment-modes.show', compact('paymentMode'));
    }

    public function edit(PaymentMode $paymentMode)
    {
        if ($paymentMode->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        return view('admin.payment-modes.edit', compact('paymentMode'));
    }

    public function update(PaymentModeRequest $request, PaymentMode $paymentMode)
    {
        if ($paymentMode->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        

        $paymentMode->update([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
        ]);

        return redirect()->route('payment-modes.index')->with('success', 'Payment mode updated successfully.');
    }

    public function destroy(PaymentMode $paymentMode)
    {
        if ($paymentMode->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        $paymentMode->delete();

        return redirect()->route('payment-modes.index')->with('success', 'Payment mode deleted successfully.');
    }
}
