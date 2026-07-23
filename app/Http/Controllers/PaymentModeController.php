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
        $query = PaymentMode::with('firms')->whereHas('firms', function($q) {
            $q->where('firms.id', Auth::user()->firm_id);
        });

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
        $firms = \App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get();
        return view('admin.payment-modes.create', compact('firms'));
    }

    public function store(PaymentModeRequest $request)
    {
        $paymentMode = PaymentMode::create([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
        ]);
        $paymentMode->firms()->attach($request->firm_ids);

        return redirect()->route('payment-modes.index')->with('success', 'Payment mode added successfully.');
    }

    public function show(PaymentMode $paymentMode)
    {
        $paymentMode->load('firms');
        if (!$paymentMode->firms->contains(Auth::user()->firm_id)) {
            abort(403);
        }

        return view('admin.payment-modes.show', compact('paymentMode'));
    }

    public function edit(PaymentMode $paymentMode)
    {
        $paymentMode->load('firms');
        if (!$paymentMode->firms->contains(Auth::user()->firm_id)) {
            abort(403);
        }

        $firms = \App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get();
        return view('admin.payment-modes.edit', compact('paymentMode', 'firms'));
    }

    public function update(PaymentModeRequest $request, PaymentMode $paymentMode)
    {
        $paymentMode->load('firms');
        if (!$paymentMode->firms->contains(Auth::user()->firm_id)) {
            abort(403);
        }

        $paymentMode->update([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
        ]);
        $paymentMode->firms()->sync($request->firm_ids);

        return redirect()->route('payment-modes.index')->with('success', 'Payment mode updated successfully.');
    }

    public function destroy(PaymentMode $paymentMode)
    {
        $paymentMode->load('firms');
        if (!$paymentMode->firms->contains(Auth::user()->firm_id)) {
            abort(403);
        }

        $paymentMode->delete();

        return redirect()->route('payment-modes.index')->with('success', 'Payment mode deleted successfully.');
    }
}
