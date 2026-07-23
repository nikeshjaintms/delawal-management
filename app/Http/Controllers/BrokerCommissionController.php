<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrokerCommissionRequest;
use App\Models\BrokerCommission;
use App\Models\Broker;
use App\Models\Property;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BrokerCommissionController extends Controller
{
    private function authorise(BrokerCommission $commission): void
    {
        if (!Auth::user()->isAdmin() && $commission->firm_id != Auth::user()->firm_id) {
            abort(403);
        }
    }

    private function dropdowns($selectedFirmId = null): array
    {
        $user   = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $brokerQuery   = Broker::where('status', 'active')->orderBy('name');
        $propertyQuery = Property::orderBy('property_name');
        $customerQuery = Customer::where('status', 'active')->orderBy('name');
        $bookingQuery  = Booking::with(['property', 'customer'])->latest();

        if ($firmId && (!$user || !$user->isAdmin())) {
            $brokerQuery->where('firm_id', $firmId);
            $propertyQuery->where('firm_id', $firmId);
            $customerQuery->where('firm_id', $firmId);
            $bookingQuery->where('firm_id', $firmId);
        }

        return [
            'firms'      => $firms,
            'brokers'    => $brokerQuery->get(),
            'properties' => $propertyQuery->get(),
            'customers'  => $customerQuery->get(),
            'bookings'   => $bookingQuery->get(),
        ];
    }

    public function index(Request $request)
    {
        $kpiQuery = BrokerCommission::query();
        if (!Auth::user()->isAdmin()) {
            $kpiQuery->where('firm_id', Auth::user()->firm_id);
        } elseif ($request->filled('firm_id')) {
            $kpiQuery->where('firm_id', $request->firm_id);
        }

        // KPI calculations
        $totalCommission   = (clone $kpiQuery)->sum('commission_amount');
        $paidCommission    = (clone $kpiQuery)->where('payment_status', 'paid')->sum('commission_amount');
        $pendingCommission = (clone $kpiQuery)->where('payment_status', 'pending')->sum('commission_amount');

        $thisMonthCommission = (clone $kpiQuery)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('commission_amount');

        // Query with filters
        $query = BrokerCommission::with(['firm', 'broker', 'property', 'customer', 'booking']);

        if (!Auth::user()->isAdmin()) {
            $query->where('firm_id', Auth::user()->firm_id);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('broker', function ($bq) use ($search) {
                    $bq->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('property', function ($pq) use ($search) {
                    $pq->where('property_name', 'like', '%' . $search . '%');
                })->orWhereHas('customer', function ($cq) use ($search) {
                    $cq->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('firm', function ($fq) use ($search) {
                    $fq->where('firm_name', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->filled('filter_broker')) {
            $query->where('broker_id', $request->filter_broker);
        }

        if ($request->filled('filter_property')) {
            $query->where('property_id', $request->filter_property);
        }

        if ($request->filled('filter_payment_status')) {
            $query->where('payment_status', $request->filter_payment_status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        $commissions = $query->latest()->paginate(10)->withQueryString();

        $dropdownsData = $this->dropdowns($request->firm_id);
        $firms      = $dropdownsData['firms'];
        $brokers    = $dropdownsData['brokers'];
        $properties = $dropdownsData['properties'];

        return view('admin.broker-commissions.index', compact(
            'commissions', 'firms', 'brokers', 'properties',
            'totalCommission', 'paidCommission', 'pendingCommission', 'thisMonthCommission'
        ));
    }

    public function create()
    {
        return view('admin.broker-commissions.create', $this->dropdowns());
    }

    public function store(BrokerCommissionRequest $request)
    {
        $firmId = $request->firm_id ?? Auth::user()->firm_id;

        BrokerCommission::create([
            'firm_id'           => $firmId,
            'broker_id'         => $request->broker_id,
            'property_id'       => $request->property_id,
            'customer_id'       => $request->customer_id,
            'booking_id'        => $request->booking_id,
            'commission_type'   => $request->commission_type,
            'commission_value'  => $request->commission_value,
            'commission_amount' => $request->commission_amount,
            'payment_status'    => $request->payment_status,
            'payment_date'      => $request->payment_date,
            'remarks'           => $request->remarks,
            'status'            => $request->status,
            'created_by'        => Auth::id(),
        ]);

        return redirect()->route('broker-commissions.index')->with('success', 'Broker commission added successfully.');
    }

    public function show($id)
    {
        $commission = BrokerCommission::with(['firm', 'broker', 'property', 'customer', 'booking', 'creator'])
            ->findOrFail($id);

        $this->authorise($commission);

        return view('admin.broker-commissions.show', compact('commission'));
    }

    public function edit($id)
    {
        $commission = BrokerCommission::findOrFail($id);
        $this->authorise($commission);

        return view('admin.broker-commissions.edit', array_merge(
            ['commission' => $commission],
            $this->dropdowns($commission->firm_id)
        ));
    }

    public function update(BrokerCommissionRequest $request, $id)
    {
        $commission = BrokerCommission::findOrFail($id);
        $this->authorise($commission);

        $firmId = $request->firm_id ?? $commission->firm_id ?? Auth::user()->firm_id;

        $commission->update([
            'firm_id'           => $firmId,
            'broker_id'         => $request->broker_id,
            'property_id'       => $request->property_id,
            'customer_id'       => $request->customer_id,
            'booking_id'        => $request->booking_id,
            'commission_type'   => $request->commission_type,
            'commission_value'  => $request->commission_value,
            'commission_amount' => $request->commission_amount,
            'payment_status'    => $request->payment_status,
            'payment_date'      => $request->payment_date,
            'remarks'           => $request->remarks,
            'status'            => $request->status,
        ]);

        return redirect()->route('broker-commissions.index')->with('success', 'Broker commission updated successfully.');
    }

    public function destroy($id)
    {
        $commission = BrokerCommission::findOrFail($id);
        $this->authorise($commission);

        $commission->delete();

        return redirect()->route('broker-commissions.index')->with('success', 'Broker commission deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $commission = BrokerCommission::findOrFail($id);
        $this->authorise($commission);

        $commission->update([
            'status' => $commission->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('broker-commissions.index')->with('success', 'Commission status updated successfully.');
    }

    private function getFilteredData(Request $request)
    {
        $query = BrokerCommission::with(['firm', 'broker', 'property', 'customer', 'booking']);

        if (!Auth::user()->isAdmin()) {
            $query->where('firm_id', Auth::user()->firm_id);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('broker', function ($bq) use ($search) {
                    $bq->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('property', function ($pq) use ($search) {
                    $pq->where('property_name', 'like', '%' . $search . '%');
                })->orWhereHas('customer', function ($cq) use ($search) {
                    $cq->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('firm', function ($fq) use ($search) {
                    $fq->where('firm_name', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->filled('filter_broker')) {
            $query->where('broker_id', $request->filter_broker);
        }

        if ($request->filled('filter_property')) {
            $query->where('property_id', $request->filter_property);
        }

        if ($request->filled('filter_payment_status')) {
            $query->where('payment_status', $request->filter_payment_status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        return $query->latest()->get();
    }

    public function exportExcel(Request $request)
    {
        $commissions = $this->getFilteredData($request);
        $filename = 'broker-commissions-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($commissions) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, ['Delawala Properties & Management - Broker Commissions Report']);
            fputcsv($handle, ['Generated on', date('d M Y, h:i A')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'No', 'Firm', 'Broker Name', 'Property', 'Customer', 'Commission Type',
                'Commission Value', 'Calculated Amount (₹)', 'Payment Status', 'Payment Date', 'Status'
            ]);

            foreach ($commissions as $key => $c) {
                fputcsv($handle, [
                    $key + 1,
                    $c->firm->firm_name ?? '-',
                    $c->broker->name ?? '-',
                    $c->property->property_name ?? '-',
                    $c->customer->name ?? '-',
                    ucfirst($c->commission_type),
                    $c->commission_value,
                    $c->commission_amount,
                    ucfirst($c->payment_status),
                    $c->payment_date ? Carbon::parse($c->payment_date)->format('d M Y') : '-',
                    ucfirst($c->status)
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $commissions = $this->getFilteredData($request);
        $totalCommission = $commissions->sum('commission_amount');
        $paidCommission = $commissions->where('payment_status', 'paid')->sum('commission_amount');
        $pendingCommission = $commissions->where('payment_status', 'pending')->sum('commission_amount');

        return view('admin.broker-commissions.pdf', compact('commissions', 'totalCommission', 'paidCommission', 'pendingCommission'));
    }
}
