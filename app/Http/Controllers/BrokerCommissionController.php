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
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $commission->firm_id != $firmId) {
            abort(403);
        }
    }

    private function dropdowns($selectedFirmId = null): array
    {
        $user   = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $brokerQuery = Broker::with(['project', 'firms'])->orderBy('name');
        if ($firmId && !$isAdmin) {
            $brokerQuery->where(function($q) use ($firmId) {
                $q->where('firm_id', $firmId)
                  ->orWhereHas('firms', function($fq) use ($firmId) {
                      $fq->where('firms.id', $firmId);
                  });
            });
        }
        $brokers = $brokerQuery->get();
        if ($brokers->isEmpty()) {
            $brokers = Broker::with(['project', 'firms'])->orderBy('name')->get();
        }

        $projectQuery = \App\Models\Project::with('propertyMaster')->orderBy('project_name');
        if ($firmId && !$isAdmin) {
            $projectQuery->where('firm_id', $firmId);
        }
        $projects = $projectQuery->get();
        if ($projects->isEmpty()) {
            $projects = \App\Models\Project::with('propertyMaster')->orderBy('project_name')->get();
        }

        $propertyMasterQuery = \App\Models\PropertyMaster::orderBy('property_name');
        if ($firmId && !$isAdmin) {
            $propertyMasterQuery->where('firm_id', $firmId);
        }
        $propertyMasters = $propertyMasterQuery->get();

        $propertyQuery = Property::with(['propertyMaster', 'project.propertyMaster'])->orderBy('property_name');
        if ($firmId && !$isAdmin) {
            $propertyQuery->where('firm_id', $firmId);
        }
        $properties = $propertyQuery->get();

        $customerQuery = Customer::orderBy('name');
        if ($firmId && !$isAdmin) {
            $customerQuery->where('firm_id', $firmId);
        }
        $customers = $customerQuery->get();

        $bookingQuery = Booking::with(['property', 'customer'])->latest();
        if ($firmId && !$isAdmin) {
            $bookingQuery->where('firm_id', $firmId);
        }
        $bookings = $bookingQuery->get();

        return [
            'firms'           => $firms,
            'brokers'         => $brokers,
            'projects'        => $projects,
            'propertyMasters' => $propertyMasters,
            'properties'      => $properties,
            'customers'       => $customers,
            'bookings'        => $bookings,
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $kpiQuery = BrokerCommission::query();
        if (!$isAdmin) {
            $kpiQuery->where('firm_id', $firmId);
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
        $query = BrokerCommission::with(['firm', 'broker', 'property.propertyMaster', 'property.project.propertyMaster', 'customer', 'booking']);

        if (!$isAdmin) {
            $query->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('broker', function ($bq) use ($search) {
                    $bq->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('property', function ($pq) use ($search) {
                    $pq->where('property_name', 'like', '%' . $search . '%')
                      ->orWhereHas('propertyMaster', function($pmq) use ($search) {
                          $pmq->where('property_name', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('project.propertyMaster', function($pmq) use ($search) {
                          $pmq->where('property_name', 'like', '%' . $search . '%');
                      });
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
            $pmId = $request->filter_property;
            $query->where(function($q) use ($pmId) {
                $q->whereHas('property', function($pq) use ($pmId) {
                    $pq->where('property_master_id', $pmId)
                      ->orWhereHas('project', function($prjQ) use ($pmId) {
                          $prjQ->where('property_id', $pmId);
                      });
                })->orWhere('property_id', $pmId);
            });
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

        $dropdownsData   = $this->dropdowns($request->firm_id);
        $firms           = $dropdownsData['firms'];
        $brokers         = $dropdownsData['brokers'];
        $propertyMasters = $dropdownsData['propertyMasters'];
        $properties      = $dropdownsData['properties'];

        return view('admin.broker-commissions.index', compact(
            'commissions', 'firms', 'brokers', 'propertyMasters', 'properties',
            'totalCommission', 'paidCommission', 'pendingCommission', 'thisMonthCommission'
        ));
    }

    public function create()
    {
        return view('admin.broker-commissions.create', $this->dropdowns());
    }

    public function store(BrokerCommissionRequest $request)
    {
        $user = Auth::user();
        $firmId = null;

        if ($request->filled('firm_ids') && is_array($request->firm_ids)) {
            $firmId = $request->firm_ids[0] ?? null;
        } elseif ($request->filled('firm_id')) {
            $firmId = $request->firm_id;
        }

        if (empty($firmId)) {
            $firmId = $user ? $user->firm_id : session('firm_id');
        }

        if (empty($firmId) && $request->filled('property_id')) {
            $prop = Property::find($request->property_id);
            $firmId = $prop ? $prop->firm_id : null;
        }

        if (empty($firmId)) {
            $defaultFirm = \App\Models\Firm::where('status', 'active')->first();
            $firmId = $defaultFirm ? $defaultFirm->id : 1;
        }

        $commission = BrokerCommission::create([
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
            'status'            => $request->status ?? 'active',
            'created_by'        => Auth::id(),
        ]);

        if ($request->filled('firm_ids') && is_array($request->firm_ids)) {
            $commission->syncFirms($request->firm_ids);
        }

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

        $user = Auth::user();
        $firmId = null;

        if ($request->filled('firm_ids') && is_array($request->firm_ids)) {
            $firmId = $request->firm_ids[0] ?? null;
        } elseif ($request->filled('firm_id')) {
            $firmId = $request->firm_id;
        }

        if (empty($firmId)) {
            $firmId = $commission->firm_id ?? ($user ? $user->firm_id : session('firm_id'));
        }

        if (empty($firmId)) {
            $defaultFirm = \App\Models\Firm::where('status', 'active')->first();
            $firmId = $defaultFirm ? $defaultFirm->id : 1;
        }

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
            'status'            => $request->status ?? 'active',
        ]);

        if ($request->filled('firm_ids') && is_array($request->firm_ids)) {
            $commission->syncFirms($request->firm_ids);
        }

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
