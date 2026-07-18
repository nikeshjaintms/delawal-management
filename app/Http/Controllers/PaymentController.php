<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;

use App\Models\Payment;
use App\Models\PropertySale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Return booking info as JSON for AJAX auto-fill.
     */
    public function getBookingInfo($id)
    {
        $sale = PropertySale::with(['customer', 'property'])
            ->where('firm_id', Auth::user()->firm_id)
            ->findOrFail($id);

        // Total paid so far from all payment entries
        $totalPaidSoFar = Payment::where('property_sale_id', $sale->id)->sum('payment_amount');

        return response()->json([
            'customer_name'  => $sale->customer->name ?? '',
            'customer_mobile'=> $sale->customer->mobile ?? '',
            'property_name'  => $sale->property->property_name ?? '',
            'property_code'  => $sale->property->property_code ?? '',
            'unit_no'        => $sale->property->unit_no ?? '',
            'total_amount'   => $sale->sale_amount ?? 0,
            'paid_amount'    => $totalPaidSoFar,
            'pending_amount' => max(0, ($sale->sale_amount ?? 0) - $totalPaidSoFar),
        ]);
    }

    public function index(Request $request)
    {
        $query = Payment::with(['propertySale', 'customer', 'property'])
            ->where('firm_id', Auth::user()->firm_id);

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('property', fn($p) =>
                        $p->where('property_name', 'like', "%{$search}%")
                          ->orWhere('property_code', 'like', "%{$search}%")
                  )
                  ->orWhere('payment_mode', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('transaction_ref', 'like', "%{$search}%");
            });
        }

        $payments = $query->latest()->paginate(10);

        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        $bookings = PropertySale::with(['customer', 'property'])
            ->where('firm_id', Auth::user()->firm_id)
            ->whereIn('sale_status', ['booked', 'sold'])
            ->latest()
            ->get();

        return view('admin.payments.create', compact('bookings'));
    }

    public function store(PaymentRequest $request)
    {
        

        $sale = PropertySale::where('firm_id', Auth::user()->firm_id)
            ->findOrFail($request->property_sale_id);

        // Calculate totals
        $totalPaidSoFar  = Payment::where('property_sale_id', $sale->id)->sum('payment_amount');
        $newPaid         = $totalPaidSoFar + $request->payment_amount;
        $saleTotal       = $sale->sale_amount ?? 0;
        $pendingAfter    = max(0, $saleTotal - $newPaid);

        // Determine status
        if ($saleTotal > 0 && $newPaid >= $saleTotal) {
            $paymentStatus = 'paid';
        } elseif ($newPaid > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'pending';
        }

        // Save payment entry
        Payment::create([
            'firm_id'          => Auth::user()->firm_id,
            'property_sale_id' => $sale->id,
            'customer_id'      => $sale->customer_id,
            'property_id'      => $sale->property_id,
            'total_amount'     => $saleTotal,
            'paid_amount'      => $newPaid,
            'pending_amount'   => $pendingAfter,
            'payment_amount'   => $request->payment_amount,
            'payment_mode'     => $request->payment_mode,
            'transaction_ref'  => $request->transaction_ref,
            'payment_date'     => $request->payment_date,
            'status'           => $paymentStatus,
            'remarks'          => $request->remarks,
        ]);

        // Update property sale paid/remaining/status
        $sale->update([
            'booking_amount'   => $newPaid,
            'remaining_amount' => $pendingAfter,
            'payment_status'   => $paymentStatus,
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        if ($payment->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        $payment->load(['propertySale.property', 'propertySale.customer', 'customer', 'property']);

        return view('admin.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        if ($payment->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        $bookings = PropertySale::with(['customer', 'property'])
            ->where('firm_id', Auth::user()->firm_id)
            ->whereIn('sale_status', ['booked', 'sold'])
            ->latest()
            ->get();

        return view('admin.payments.edit', compact('payment', 'bookings'));
    }

    public function update(PaymentRequest $request, Payment $payment)
    {
        if ($payment->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        

        $sale = PropertySale::where('firm_id', Auth::user()->firm_id)
            ->findOrFail($request->property_sale_id);

        // Recalculate: sum all OTHER payments for this booking + this new amount
        $totalPaidSoFar = Payment::where('property_sale_id', $sale->id)
            ->where('id', '!=', $payment->id)
            ->sum('payment_amount');

        $newPaid      = $totalPaidSoFar + $request->payment_amount;
        $saleTotal    = $sale->sale_amount ?? 0;
        $pendingAfter = max(0, $saleTotal - $newPaid);

        if ($saleTotal > 0 && $newPaid >= $saleTotal) {
            $paymentStatus = 'paid';
        } elseif ($newPaid > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'pending';
        }

        $payment->update([
            'property_sale_id' => $sale->id,
            'customer_id'      => $sale->customer_id,
            'property_id'      => $sale->property_id,
            'total_amount'     => $saleTotal,
            'paid_amount'      => $newPaid,
            'pending_amount'   => $pendingAfter,
            'payment_amount'   => $request->payment_amount,
            'payment_mode'     => $request->payment_mode,
            'transaction_ref'  => $request->transaction_ref,
            'payment_date'     => $request->payment_date,
            'status'           => $paymentStatus,
            'remarks'          => $request->remarks,
        ]);

        $sale->update([
            'booking_amount'   => $newPaid,
            'remaining_amount' => $pendingAfter,
            'payment_status'   => $paymentStatus,
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->firm_id != Auth::user()->firm_id) {
            abort(403);
        }

        $sale            = PropertySale::find($payment->property_sale_id);
        $removedAmount   = $payment->payment_amount;

        $payment->delete();

        if ($sale) {
            // Recalculate from remaining entries
            $totalPaidNow  = Payment::where('property_sale_id', $sale->id)->sum('payment_amount');
            $saleTotal     = $sale->sale_amount ?? 0;
            $pendingNow    = max(0, $saleTotal - $totalPaidNow);

            if ($saleTotal > 0 && $totalPaidNow >= $saleTotal) {
                $status = 'paid';
            } elseif ($totalPaidNow > 0) {
                $status = 'partial';
            } else {
                $status = 'pending';
            }

            $sale->update([
                'booking_amount'   => $totalPaidNow,
                'remaining_amount' => $pendingNow,
                'payment_status'   => $status,
            ]);
        }

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }
}
