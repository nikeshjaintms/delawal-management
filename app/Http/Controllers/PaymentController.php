<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;

use App\Models\Payment;
use App\Models\PropertySale;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Return booking info as JSON for AJAX auto-fill.
     */
    public function getBookingInfo($id)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $query = PropertySale::with(['customer', 'property.project', 'properties.project', 'firm']);
        if (!$isAdmin && $firmId) {
            $query->where('firm_id', $firmId);
        }
        $sale = $query->findOrFail($id);

        $prop = $sale->property ?: $sale->properties->first();
        $propNames = $sale->properties->isNotEmpty()
            ? $sale->properties->pluck('property_name')->join(', ')
            : ($prop?->property_name ?? '—');
        $unitNos = $sale->properties->isNotEmpty()
            ? $sale->properties->pluck('unit_no')->filter()->join(', ')
            : ($prop?->unit_no ?? '');
        $project = $prop?->project ?: $sale->properties->first()?->project;
        if (!$project) {
            $pmId = $prop?->property_master_id ?: $sale->properties->first()?->property_master_id;
            if ($pmId) {
                $project = \App\Models\Project::where('property_id', $pmId)->first();
            }
        }

        // Total paid so far from all payment entries or initial booking_amount
        $paymentsSum = (float) Payment::where('property_sale_id', $sale->id)->sum('payment_amount');
        $totalPaidSoFar = max($paymentsSum, (float) ($sale->booking_amount ?? 0));

        return response()->json([
            'id'             => $sale->id,
            'customer_id'    => $sale->customer_id,
            'customer_name'  => $sale->customer->name ?? '',
            'customer_mobile'=> $sale->customer->mobile ?? '',
            'project_id'     => $project?->id ?? '',
            'project_name'   => $project?->project_name ?? '',
            'property_id'    => $prop?->id ?? '',
            'property_name'  => $propNames,
            'property_code'  => $prop?->property_code ?? '',
            'unit_no'        => $unitNos,
            'total_amount'   => (float) ($sale->sale_amount ?? 0),
            'paid_amount'    => $totalPaidSoFar,
            'pending_amount' => max(0, (float) ($sale->sale_amount ?? 0) - $totalPaidSoFar),
        ]);
    }

    public function index(Request $request)
    {
        $query = Payment::with(['firm', 'propertySale', 'customer', 'property']);

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin) {
            $query->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('property', fn($p) =>
                        $p->where('property_name', 'like', "%{$search}%")
                          ->orWhere('property_code', 'like', "%{$search}%")
                  )
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$search}%"))
                  ->orWhere('payment_mode', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('transaction_ref', 'like', "%{$search}%");
            });
        }

        $payments = $query->latest()->paginate(10)->withQueryString();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.payments.index', compact('payments', 'firms'));
    }

    public function create()
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $bookingsQuery = PropertySale::with(['customer', 'property.project', 'properties.project', 'firm'])
            ->whereIn('sale_status', ['booked', 'sold']);

        $projectsQuery = \App\Models\Project::where('status', 'active')->orderBy('project_name');
        $propertiesQuery = \App\Models\Property::with('project')->orderBy('property_name');

        if (!$isAdmin && $firmId) {
            $bookingsQuery->where('firm_id', $firmId);
            $projectsQuery->where('firm_id', $firmId);
            $propertiesQuery->where('firm_id', $firmId);
        }

        $bookings   = $bookingsQuery->latest()->get();
        $projects   = $projectsQuery->get();
        $properties = $propertiesQuery->get();
        $firms      = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.payments.create', compact('bookings', 'projects', 'properties', 'firms'));
    }

    public function store(PaymentRequest $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $saleQuery = PropertySale::query();
        if (!$isAdmin && $firmId) {
            $saleQuery->where('firm_id', $firmId);
        }
        $sale = $saleQuery->findOrFail($request->property_sale_id);

        $resolvedFirmId = $request->firm_id ?? ($request->firm_ids[0] ?? ($sale->firm_id ?? (session('firm_id') ?? ($user?->firm_id))));

        // Calculate totals
        $totalPaidSoFar  = Payment::where('property_sale_id', $sale->id)->sum('payment_amount');
        $newPaid         = $totalPaidSoFar + (float) $request->payment_amount;
        $saleTotal       = (float) ($sale->sale_amount ?? 0);
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
        $payment = Payment::create([
            'firm_id'          => $resolvedFirmId,
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

        if ($request->has('firm_ids') && is_array($request->firm_ids) && !empty($request->firm_ids)) {
            $payment->syncFirms($request->firm_ids);
        } elseif ($resolvedFirmId) {
            $payment->syncFirms([$resolvedFirmId]);
        }

        // Update property sale paid/remaining/status
        $sale->update([
            'booking_amount'   => $newPaid,
            'remaining_amount' => $pendingAfter,
            'payment_status'   => $paymentStatus,
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment collection recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $payment->firm_id != $firmId) {
            abort(403);
        }

        $payment->load(['firm', 'propertySale.property', 'propertySale.customer', 'customer', 'property']);

        return view('admin.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $payment->firm_id != $firmId) {
            abort(403);
        }

        $bookingsQuery = PropertySale::with(['customer', 'property', 'firm'])
            ->whereIn('sale_status', ['booked', 'sold']);

        if (!$isAdmin) {
            $bookingsQuery->where('firm_id', $firmId);
        }

        $bookings = $bookingsQuery->latest()->get();
        $firms    = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.payments.edit', compact('payment', 'bookings', 'firms'));
    }

    public function update(PaymentRequest $request, Payment $payment)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $firmId && $payment->firm_id != $firmId) {
            abort(403);
        }

        $saleQuery = PropertySale::query();
        if (!$isAdmin && $firmId) {
            $saleQuery->where('firm_id', $firmId);
        }
        $sale = $saleQuery->findOrFail($request->property_sale_id);

        $resolvedFirmId = $request->firm_id ?? ($request->firm_ids[0] ?? ($sale->firm_id ?? $payment->firm_id));

        // Recalculate: sum all OTHER payments for this booking + this new amount
        $totalPaidSoFar = Payment::where('property_sale_id', $sale->id)
            ->where('id', '!=', $payment->id)
            ->sum('payment_amount');

        $newPaid      = $totalPaidSoFar + (float) $request->payment_amount;
        $saleTotal    = (float) ($sale->sale_amount ?? 0);
        $pendingAfter = max(0, $saleTotal - $newPaid);

        if ($saleTotal > 0 && $newPaid >= $saleTotal) {
            $paymentStatus = 'paid';
        } elseif ($newPaid > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'pending';
        }

        $payment->update([
            'firm_id'          => $resolvedFirmId,
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

        if ($request->has('firm_ids') && is_array($request->firm_ids) && !empty($request->firm_ids)) {
            $payment->syncFirms($request->firm_ids);
        } elseif ($resolvedFirmId) {
            $payment->syncFirms([$resolvedFirmId]);
        }

        $sale->update([
            'booking_amount'   => $newPaid,
            'remaining_amount' => $pendingAfter,
            'payment_status'   => $paymentStatus,
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment collection updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $payment->firm_id != $firmId) {
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

        return redirect()->route('payments.index')->with('success', 'Payment collection deleted successfully.');
    }
}
