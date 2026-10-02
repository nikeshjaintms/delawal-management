<?php

namespace App\Http\Controllers;

use App\Models\PropertySale;
use App\Models\Project;
use App\Models\Property;
use App\Models\Customer;
use App\Models\Broker;
use App\Models\Firm;
use App\Models\Payment;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertySaleController extends Controller
{
    private function getDropdownData($selectedFirmId = null, $currentPropertySale = null)
    {
        $user = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $projectsQuery        = Project::with('propertyMaster')->orderBy('project_name');
        $propertiesQuery      = Property::with(['project.propertyMaster', 'propertyMaster'])->orderBy('property_name');
        $propertyMastersQuery = \App\Models\PropertyMaster::with(['plots', 'projects'])->orderBy('property_name');
        $customersQuery       = Customer::where('status', 'active')->orderBy('name');
        $sellersQuery         = \App\Models\Seller::where('status', 'active')->orderBy('name');
        $brokersQuery         = Broker::where('status', 'active')->orderBy('name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $projectsQuery->where('firm_id', $firmId);
            $propertiesQuery->where('firm_id', $firmId);
            $propertyMastersQuery->where('firm_id', $firmId);
            $customersQuery->where('firm_id', $firmId);
            $sellersQuery->where('firm_id', $firmId);
            $brokersQuery->where('firm_id', $firmId);
        }

        if ($currentPropertySale) {
            $currentSalePropertyIds = $currentPropertySale->all_properties->pluck('id')->toArray();
            $propertiesQuery->where(function ($q) use ($currentSalePropertyIds, $currentPropertySale) {
                $q->where(function ($sq) use ($currentPropertySale) {
                    $sq->where('status', '!=', 'sold')
                       ->whereDoesntHave('sales', function ($ssq) use ($currentPropertySale) {
                           $ssq->where('sale_status', '!=', 'cancelled')
                               ->where('property_sales.id', '!=', $currentPropertySale->id);
                       })
                       ->whereDoesntHave('salesList', function ($ssq) use ($currentPropertySale) {
                           $ssq->where('sale_status', '!=', 'cancelled')
                               ->where('property_sales.id', '!=', $currentPropertySale->id);
                       });
                })->orWhereIn('id', $currentSalePropertyIds);
            });

            $currentMasterId = $currentPropertySale->property?->property_master_id;
            $propertyMastersQuery->where(function ($q) use ($currentMasterId) {
                $q->where('status', '!=', 'sold')
                  ->orWhere('id', $currentMasterId);
            });
        } else {
            // In create / new sale: completely exclude sold properties and any property linked to active sale
            $propertiesQuery->where('status', '!=', 'sold')
                            ->whereDoesntHave('sales', function ($sq) {
                                $sq->where('sale_status', '!=', 'cancelled');
                            })
                            ->whereDoesntHave('salesList', function ($sq) {
                                $sq->where('sale_status', '!=', 'cancelled');
                            });
            $propertyMastersQuery->where('status', '!=', 'sold');
        }

        $projects        = $projectsQuery->get();
        $properties      = Property::naturalSort($propertiesQuery->get());
        $propertyMasters = $propertyMastersQuery->get();
        $customers       = $customersQuery->get();
        $sellers         = $sellersQuery->get();
        $brokers         = $brokersQuery->get();

        return compact('firms', 'projects', 'properties', 'propertyMasters', 'customers', 'sellers', 'brokers');
    }

    private function updatePropertyStatus(PropertySale $sale, array $allPropertyIds = [])
    {
        $propertyIds = !empty($allPropertyIds) ? $allPropertyIds : [$sale->property_id];
        $propertyIds = array_values(array_filter($propertyIds));
        $properties  = Property::whereIn('id', $propertyIds)->get();
        if ($properties->isEmpty()) return;

        $statusMap = [
            'booked'    => 'booked',
            'sold'      => 'sold',
            'cancelled' => 'available',
        ];

        if (isset($statusMap[$sale->sale_status])) {
            $targetStatus = $statusMap[$sale->sale_status];
            Property::whereIn('id', $propertyIds)->update(['status' => $targetStatus]);

            // If any of the properties is an entire PropertyMaster, cascade to its plots
            foreach ($properties as $property) {
                if ($property->property_master_id) {
                    $pm = \App\Models\PropertyMaster::find($property->property_master_id);
                    if ($pm) {
                        if ($property->unit_no === null && empty($property->project_id) && $pm->all_projects->isEmpty()) {
                            $pmStatus = ($targetStatus === 'available') ? 'active' : $targetStatus;
                            $pm->update(['status' => $pmStatus]);
                            $pm->plots()->update(['status' => $targetStatus]);
                        } else {
                            // Check if all plots in this PropertyMaster are now sold
                            $totalPlots = $pm->plots()->count();
                            if ($totalPlots > 0) {
                                $unsoldPlots = $pm->plots()->where('status', '!=', 'sold')->count();
                                if ($unsoldPlots === 0) {
                                    $pm->update(['status' => 'sold']);
                                } elseif ($pm->status === 'sold') {
                                    $pm->update(['status' => 'active']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function index(Request $request)
    {
        $query = PropertySale::with([
            'firm',
            'property.propertyMaster',
            'properties.propertyMaster',
            'customer',
            'broker',
            'payments'
        ]);

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
                $q->whereHas('property', function ($p) use ($search) {
                    $p->where('property_name', 'like', "%{$search}%")
                      ->orWhere('property_code', 'like', "%{$search}%");
                })
                ->orWhereHas('properties', function ($p) use ($search) {
                    $p->where('property_name', 'like', "%{$search}%")
                      ->orWhere('property_code', 'like', "%{$search}%");
                })
                ->orWhereHas('customer', function ($c) use ($search) {
                    $c->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('broker', function ($b) use ($search) {
                    $b->where('name', 'like', "%{$search}%");
                })
                ->orWhere('broker_name', 'like', "%{$search}%")
                ->orWhereHas('firm', function ($f) use ($search) {
                    $f->where('firm_name', 'like', "%{$search}%");
                })
                ->orWhere('payment_status', 'like', "%{$search}%")
                ->orWhere('sale_status', 'like', "%{$search}%");
            });
        }

        // Summary KPI calculations across all matching sales
        $summarySales = (clone $query)->get();
        $totalSalesRevenue      = (float)$summarySales->sum('sale_amount');
        $totalSalesPurchaseCost = (float)$summarySales->sum(fn($s) => $s->total_cost_basis);
        $totalSalesProfit       = (float)$summarySales->sum(fn($s) => $s->net_profit);
        $salesProfitMargin      = $totalSalesRevenue > 0 ? round(($totalSalesProfit / $totalSalesRevenue) * 100, 1) : 0.0;
        $totalSalesCount        = $summarySales->count();
        $totalPaidAmount        = (float)$summarySales->sum('booking_amount');
        $totalDueAmount         = (float)$summarySales->sum('remaining_amount');

        $propertySales = $query->latest()->paginate(10)->withQueryString();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $paymentModes = PaymentMode::where('status', 'active')->orderBy('name')->get();

        return view('admin.property-sales.index', compact(
            'propertySales', 'firms', 'paymentModes',
            'totalSalesRevenue', 'totalSalesPurchaseCost', 'totalSalesProfit',
            'salesProfitMargin', 'totalSalesCount', 'totalPaidAmount', 'totalDueAmount'
        ));
    }

    public function create()
    {
        return view('admin.property-sales.create', $this->getDropdownData());
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$request->filled('firm_id')) {
            $firmIds = $request->input('firm_ids', []);
            $firmId  = (is_array($firmIds) && !empty($firmIds))
                ? $firmIds[0]
                : ($user ? $user->firm_id : session('firm_id'));
            $request->merge(['firm_id' => $firmId]);
        }

        $submittedPropIds = (array) ($request->property_ids ?: ($request->property_id ? [$request->property_id] : []));
        $submittedPropIds = array_values(array_unique(array_filter($submittedPropIds)));
        if (!empty($submittedPropIds)) {
            $request->merge(['property_id' => $submittedPropIds[0]]);
        }

        // Handle Entire Property sale scope
        if ($request->input('sale_scope') === 'entire' && $request->filled('property_master_id')) {
            $pm = \App\Models\PropertyMaster::find($request->property_master_id);
            if ($pm) {
                $entireProp = Property::firstOrCreate(
                    ['property_master_id' => $pm->id, 'unit_no' => null],
                    [
                        'firm_id'          => $pm->firm_id,
                        'property_name'    => $pm->property_name . ' (Entire Property)',
                        'property_code'    => $pm->property_code ? $pm->property_code . '-ENTIRE' : 'PROP-' . $pm->id . '-ENTIRE',
                        'location'         => $pm->location,
                        'city'             => $pm->city,
                        'address'          => $pm->address,
                        'size'             => $pm->total_area,
                        'size_unit'        => $pm->area_unit ?: 'sq.ft',
                        'price'            => $pm->purchase_price,
                        'purchase_rate'    => $pm->purchase_rate,
                        'purchase_date'    => $pm->purchase_date,
                        'status'           => 'available',
                        'description'      => 'Entire Property Master: ' . $pm->property_name,
                    ]
                );
                $request->merge(['property_id' => $entireProp->id]);
                $submittedPropIds = [$entireProp->id];
            }
        }

        $request->validate([
            'firm_id'          => 'required|exists:firms,id',
            'property_id'      => 'required|exists:properties,id',
            'customer_id'      => 'required|exists:customers,id',
            'seller_id'        => 'nullable|exists:sellers,id',
            'seller_name'      => 'nullable|string|max:255',
            'broker_id'        => 'nullable|exists:brokers,id',
            'broker_name'      => 'nullable|string|max:255',
            'broker_commission_type' => 'nullable|in:percentage,fixed',
            'broker_commission_rate' => 'nullable|numeric|min:0',
            'broker_commission_amount' => 'nullable|numeric|min:0',
            'broker_commission_paid' => 'nullable|numeric|min:0',
            'broker_commission_due'  => 'nullable|numeric|min:0',
            'broker_commission_payment_mode' => 'nullable|string|max:100',
            'broker_commission_payment_date' => 'nullable|date',
            'broker_commission_status' => 'nullable|string|max:50',
            'broker_notes'     => 'nullable|string|max:2000',
            'purchase_date'    => 'nullable|date',
            'purchase_cost'    => 'nullable|numeric|min:0',
            'property_expenses'=> 'nullable|numeric|min:0',
            'total_area'       => 'nullable|numeric|min:0',
            'area_unit'        => 'nullable|string|max:50',
            'purchase_rate'    => 'nullable|numeric|min:0',
            'sell_rate'        => 'nullable|numeric|min:0',
            'sale_date'        => 'nullable|date',
            'sale_amount'      => 'nullable|numeric',
            'booking_amount'   => 'nullable|numeric',
            'remaining_amount' => 'nullable|numeric',
            'payment_status'   => 'required',
            'sale_status'      => 'required',
            'agreement_file'   => 'nullable|file',
            'note'             => 'nullable',
        ]);

        $agreementPath = null;
        if ($request->hasFile('agreement_file')) {
            $agreementPath = $request->file('agreement_file')->store('property-agreements', 'public');
        }

        $saleAmount      = (float)($request->sale_amount ?? 0);
        $bookingAmount   = (float)($request->booking_amount ?? 0);
        $remainingAmount = max(0, round($saleAmount - $bookingAmount, 2));

        $paymentStatus = $request->payment_status ?: 'pending';
        if ($saleAmount > 0) {
            if ($bookingAmount >= $saleAmount) {
                $paymentStatus = 'paid';
            } elseif ($bookingAmount > 0) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'pending';
            }
        }

        // Seller data
        $sellerId = $request->seller_id ?: null;
        $sellerName = $request->filled('seller_name') ? $request->seller_name : ($sellerId ? (\App\Models\Seller::find($sellerId)?->name) : null);

        // Broker commission calculation
        $brokerId = $request->broker_id ?: null;
        $brokerName = $request->filled('broker_name') ? $request->broker_name : ($brokerId ? (\App\Models\Broker::find($brokerId)?->name) : null);
        $brokerCommType = $request->broker_commission_type ?: 'percentage';
        $brokerCommRate = $request->filled('broker_commission_rate') ? floatval($request->broker_commission_rate) : null;
        $brokerCommAmount = $request->filled('broker_commission_amount') ? floatval($request->broker_commission_amount) : 0.00;
        if (($brokerId || $brokerName) && $brokerCommType === 'percentage' && $brokerCommRate && $brokerCommAmount == 0 && $saleAmount > 0) {
            $brokerCommAmount = round(($saleAmount * $brokerCommRate) / 100, 2);
        }
        $brokerCommPaid = $request->filled('broker_commission_paid') ? floatval($request->broker_commission_paid) : 0.00;
        $brokerCommDue = max(0.00, $brokerCommAmount - $brokerCommPaid);
        $brokerCommStatus = $request->broker_commission_status;
        if (empty($brokerCommStatus)) {
            if ($brokerCommAmount > 0) {
                if ($brokerCommPaid >= $brokerCommAmount) {
                    $brokerCommStatus = 'paid';
                } elseif ($brokerCommPaid > 0) {
                    $brokerCommStatus = 'partial';
                } else {
                    $brokerCommStatus = 'unpaid';
                }
            } else {
                $brokerCommStatus = 'unpaid';
            }
        }

        $sale = PropertySale::create([
            'firm_id'                        => $request->firm_id,
            'property_id'                    => $request->property_id,
            'customer_id'                    => $request->customer_id,
            'seller_id'                      => $sellerId,
            'seller_name'                    => $sellerName,
            'broker_id'                      => $brokerId,
            'broker_name'                    => $brokerName,
            'purchase_date'                  => $request->purchase_date,
            'purchase_cost'                  => $request->filled('purchase_cost') ? (float)$request->purchase_cost : null,
            'property_expenses'              => $request->filled('property_expenses') ? (float)$request->property_expenses : null,
            'total_area'                     => $request->filled('total_area') ? (float)$request->total_area : null,
            'area_unit'                      => $request->area_unit ?: 'Sq.Ft',
            'purchase_rate'                  => $request->filled('purchase_rate') ? (float)$request->purchase_rate : null,
            'sell_rate'                      => $request->filled('sell_rate') ? (float)$request->sell_rate : null,
            'broker_commission_type'         => $brokerCommType,
            'broker_commission_rate'         => $brokerCommRate,
            'broker_commission_amount'       => $brokerCommAmount,
            'broker_commission_paid'         => $brokerCommPaid,
            'broker_commission_due'          => $brokerCommDue,
            'broker_commission_payment_mode' => $request->broker_commission_payment_mode,
            'broker_commission_payment_date' => $request->broker_commission_payment_date,
            'broker_commission_status'       => $brokerCommStatus,
            'broker_notes'                   => $request->broker_notes,
            'sale_date'                      => $request->sale_date,
            'sale_amount'                    => $saleAmount,
            'booking_amount'                 => $bookingAmount,
            'remaining_amount'               => $remainingAmount,
            'payment_status'                 => $paymentStatus,
            'sale_status'                    => $request->sale_status,
            'agreement_file'                 => $agreementPath,
            'note'                           => $request->note,
        ]);

        // Sync multiple properties in pivot table
        if (!empty($submittedPropIds)) {
            $sale->properties()->sync($submittedPropIds);
        }

        $this->updatePropertyStatus($sale, $submittedPropIds);

        // Record initial booking payment if booking_amount > 0
        if ($bookingAmount > 0) {
            Payment::create([
                'firm_id'          => $sale->firm_id,
                'property_sale_id' => $sale->id,
                'customer_id'      => $sale->customer_id,
                'property_id'      => $sale->property_id,
                'total_amount'     => $saleAmount,
                'paid_amount'      => $bookingAmount,
                'pending_amount'   => $remainingAmount,
                'payment_amount'   => $bookingAmount,
                'payment_mode'     => $request->payment_mode ?: 'Cash',
                'transaction_ref'  => $request->transaction_ref ?: null,
                'payment_date'     => $request->sale_date ?: now()->toDateString(),
                'status'           => $paymentStatus,
                'remarks'          => 'Initial booking / down payment',
            ]);
        }

        return redirect()->route('property-sales.index')->with('success', 'Sales agreement added successfully.');
    }

    public function show(PropertySale $propertySale)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $propertySale->firm_id != $firmId) {
            abort(403);
        }

        $propertySale->load([
            'firm',
            'property.propertyType',
            'property.propertyMaster',
            'properties.propertyType',
            'properties.propertyMaster',
            'customer',
            'broker',
            'payments',
        ]);

        $paymentModes = PaymentMode::where('status', 'active')->orderBy('name')->get();

        return view('admin.property-sales.show', compact('propertySale', 'paymentModes'));
    }

    public function edit(PropertySale $propertySale)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $propertySale->firm_id != $firmId) {
            abort(403);
        }

        $propertySale->load(['properties', 'property']);

        return view('admin.property-sales.edit', array_merge(
            ['propertySale' => $propertySale],
            $this->getDropdownData($propertySale->firm_id, $propertySale)
        ));
    }

    public function update(Request $request, PropertySale $propertySale)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $propertySale->firm_id != $firmId) {
            abort(403);
        }

        if (!$request->filled('firm_id')) {
            $firmIds = $request->input('firm_ids', []);
            $fId = (is_array($firmIds) && !empty($firmIds)) ? $firmIds[0] : $propertySale->firm_id;
            $request->merge(['firm_id' => $fId]);
        }

        $submittedPropIds = (array) ($request->property_ids ?: ($request->property_id ? [$request->property_id] : []));
        $submittedPropIds = array_values(array_unique(array_filter($submittedPropIds)));
        if (!empty($submittedPropIds)) {
            $request->merge(['property_id' => $submittedPropIds[0]]);
        }

        // Handle Entire Property sale scope
        if ($request->input('sale_scope') === 'entire' && $request->filled('property_master_id')) {
            $pm = \App\Models\PropertyMaster::find($request->property_master_id);
            if ($pm) {
                $entireProp = Property::firstOrCreate(
                    ['property_master_id' => $pm->id, 'unit_no' => null],
                    [
                        'firm_id'          => $pm->firm_id,
                        'property_name'    => $pm->property_name . ' (Entire Property)',
                        'property_code'    => $pm->property_code ? $pm->property_code . '-ENTIRE' : 'PROP-' . $pm->id . '-ENTIRE',
                        'location'         => $pm->location,
                        'city'             => $pm->city,
                        'address'          => $pm->address,
                        'size'             => $pm->total_area,
                        'size_unit'        => $pm->area_unit ?: 'sq.ft',
                        'price'            => $pm->purchase_price,
                        'purchase_rate'    => $pm->purchase_rate,
                        'purchase_date'    => $pm->purchase_date,
                        'status'           => 'available',
                        'description'      => 'Entire Property Master: ' . $pm->property_name,
                    ]
                );
                $request->merge(['property_id' => $entireProp->id]);
                $submittedPropIds = [$entireProp->id];
            }
        }

        $request->validate([
            'firm_id'          => 'required|exists:firms,id',
            'property_id'      => 'required|exists:properties,id',
            'customer_id'      => 'required|exists:customers,id',
            'broker_id'        => 'nullable|exists:brokers,id',
            'broker_name'      => 'nullable|string|max:255',
            'broker_commission_type' => 'nullable|in:percentage,fixed',
            'broker_commission_rate' => 'nullable|numeric|min:0',
            'broker_commission_amount' => 'nullable|numeric|min:0',
            'broker_commission_paid' => 'nullable|numeric|min:0',
            'broker_commission_due'  => 'nullable|numeric|min:0',
            'broker_commission_payment_mode' => 'nullable|string|max:100',
            'broker_commission_payment_date' => 'nullable|date',
            'broker_commission_status' => 'nullable|string|max:50',
            'broker_notes'     => 'nullable|string|max:2000',
            'purchase_date'    => 'nullable|date',
            'purchase_cost'    => 'nullable|numeric|min:0',
            'property_expenses'=> 'nullable|numeric|min:0',
            'total_area'       => 'nullable|numeric|min:0',
            'area_unit'        => 'nullable|string|max:50',
            'purchase_rate'    => 'nullable|numeric|min:0',
            'sell_rate'        => 'nullable|numeric|min:0',
            'sale_date'        => 'nullable|date',
            'sale_amount'      => 'nullable|numeric',
            'booking_amount'   => 'nullable|numeric',
            'remaining_amount' => 'nullable|numeric',
            'payment_status'   => 'required',
            'sale_status'      => 'required',
            'agreement_file'   => 'nullable|file',
            'note'             => 'nullable',
        ]);

        $agreementPath = $propertySale->agreement_file;
        if ($request->hasFile('agreement_file')) {
            if ($propertySale->agreement_file) {
                Storage::disk('public')->delete($propertySale->agreement_file);
            }
            $agreementPath = $request->file('agreement_file')->store('property-agreements', 'public');
        }

        $saleAmount      = (float)($request->sale_amount !== null ? $request->sale_amount : ($propertySale->sale_amount ?? 0));
        
        // If payments table has records, preserve the accurate sum of payments
        $paymentsSum = (float)$propertySale->payments()->sum('payment_amount');
        if ($paymentsSum > 0 && !$request->filled('booking_amount')) {
            $bookingAmount = $paymentsSum;
        } else {
            $bookingAmount = (float)($request->booking_amount !== null ? $request->booking_amount : ($propertySale->booking_amount ?? 0));
        }
        
        $remainingAmount = max(0, round($saleAmount - $bookingAmount, 2));

        $paymentStatus = $request->payment_status ?: ($propertySale->payment_status ?? 'pending');
        if ($saleAmount > 0) {
            if ($bookingAmount >= $saleAmount) {
                $paymentStatus = 'paid';
            } elseif ($bookingAmount > 0) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'pending';
            }
        }

        // Seller data
        $sellerId = $request->filled('seller_id') ? $request->seller_id : ($request->has('seller_id') ? null : $propertySale->seller_id);
        $sellerName = $request->filled('seller_name') ? $request->seller_name : ($sellerId ? ($propertySale->seller_id == $sellerId ? ($propertySale->seller_name ?: \App\Models\Seller::find($sellerId)?->name) : (\App\Models\Seller::find($sellerId)?->name ?? null)) : null);

        // Broker commission calculation
        $brokerId = $request->filled('broker_id') ? $request->broker_id : ($request->has('broker_id') ? null : $propertySale->broker_id);
        $brokerName = $request->filled('broker_name') ? $request->broker_name : ($brokerId ? ($propertySale->broker_id == $brokerId ? ($propertySale->broker_name ?: \App\Models\Broker::find($brokerId)?->name) : (\App\Models\Broker::find($brokerId)?->name ?? null)) : null);
        $brokerCommType = $request->filled('broker_commission_type') ? $request->broker_commission_type : ($propertySale->broker_commission_type ?? 'percentage');
        $brokerCommRate = $request->filled('broker_commission_rate') ? floatval($request->broker_commission_rate) : ($request->has('broker_commission_rate') ? null : $propertySale->broker_commission_rate);
        $brokerCommAmount = $request->filled('broker_commission_amount') ? floatval($request->broker_commission_amount) : ($propertySale->broker_commission_amount ?? 0.00);
        if (($brokerId || $brokerName) && $brokerCommType === 'percentage' && $brokerCommRate && ($brokerCommAmount == 0 || $request->filled('broker_commission_rate')) && $saleAmount > 0) {
            $brokerCommAmount = round(($saleAmount * $brokerCommRate) / 100, 2);
        }
        $brokerCommPaid = $request->filled('broker_commission_paid') ? floatval($request->broker_commission_paid) : ($propertySale->broker_commission_paid ?? 0.00);
        $brokerCommDue = max(0.00, $brokerCommAmount - $brokerCommPaid);
        $brokerCommStatus = $request->broker_commission_status;
        if (empty($brokerCommStatus)) {
            if ($brokerCommAmount > 0) {
                if ($brokerCommPaid >= $brokerCommAmount) {
                    $brokerCommStatus = 'paid';
                } elseif ($brokerCommPaid > 0) {
                    $brokerCommStatus = 'partial';
                } else {
                    $brokerCommStatus = 'unpaid';
                }
            } else {
                $brokerCommStatus = 'unpaid';
            }
        }

        // Collect old properties to release removed ones
        $oldPropIds = $propertySale->all_properties->pluck('id')->toArray();

        $propertySale->update([
            'firm_id'                        => $request->firm_id,
            'property_id'                    => $request->property_id,
            'customer_id'                    => $request->customer_id,
            'seller_id'                      => $sellerId,
            'seller_name'                    => $sellerName,
            'broker_id'                      => $brokerId,
            'broker_name'                    => $brokerName,
            'purchase_date'                  => $request->purchase_date,
            'purchase_cost'                  => $request->filled('purchase_cost') ? (float)$request->purchase_cost : null,
            'property_expenses'              => $request->filled('property_expenses') ? (float)$request->property_expenses : null,
            'total_area'                     => $request->filled('total_area') ? (float)$request->total_area : null,
            'area_unit'                      => $request->area_unit ?: ($propertySale->area_unit ?: 'Sq.Ft'),
            'purchase_rate'                  => $request->filled('purchase_rate') ? (float)$request->purchase_rate : null,
            'sell_rate'                      => $request->filled('sell_rate') ? (float)$request->sell_rate : null,
            'broker_commission_type'         => $brokerCommType,
            'broker_commission_rate'         => $brokerCommRate,
            'broker_commission_amount'       => $brokerCommAmount,
            'broker_commission_paid'         => $brokerCommPaid,
            'broker_commission_due'          => $brokerCommDue,
            'broker_commission_payment_mode' => $request->broker_commission_payment_mode ?: $propertySale->broker_commission_payment_mode,
            'broker_commission_payment_date' => $request->has('broker_commission_payment_date') ? $request->broker_commission_payment_date : $propertySale->broker_commission_payment_date,
            'broker_commission_status'       => $brokerCommStatus,
            'broker_notes'                   => $request->broker_notes ?: $propertySale->broker_notes,
            'sale_date'                      => $request->sale_date,
            'sale_amount'                    => $saleAmount,
            'booking_amount'                 => $bookingAmount,
            'remaining_amount'               => $remainingAmount,
            'payment_status'                 => $paymentStatus,
            'sale_status'                    => $request->sale_status,
            'agreement_file'                 => $agreementPath,
            'note'                           => $request->note,
        ]);

        if (!empty($submittedPropIds)) {
            $propertySale->properties()->sync($submittedPropIds);
        }

        // Release removed properties
        $removedPropIds = array_diff($oldPropIds, $submittedPropIds);
        if (!empty($removedPropIds)) {
            Property::whereIn('id', $removedPropIds)->update(['status' => 'available']);
        }

        $this->updatePropertyStatus($propertySale, $submittedPropIds);

        return redirect()->route('property-sales.index')->with('success', 'Sales agreement updated successfully.');
    }

    public function storePayment(Request $request, PropertySale $propertySale)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $propertySale->firm_id != $firmId) {
            abort(403);
        }

        $validated = $request->validate([
            'payment_amount'  => 'required|numeric|min:0.01',
            'payment_date'    => 'required|date',
            'payment_mode'    => 'required|string|max:100',
            'transaction_ref' => 'nullable|string|max:150',
            'remarks'         => 'nullable|string|max:1000',
        ]);

        $saleTotal       = (float)($propertySale->sale_amount ?? 0);
        $totalPaidSoFar  = (float)Payment::where('property_sale_id', $propertySale->id)->sum('payment_amount');
        
        // If this is the first payment entry in the payments table, but propertySale already had an initial booking_amount,
        // create a backfilled record for that initial booking_amount so history is accurate.
        if ($totalPaidSoFar == 0 && (float)$propertySale->booking_amount > 0) {
            Payment::create([
                'firm_id'          => $propertySale->firm_id,
                'property_sale_id' => $propertySale->id,
                'customer_id'      => $propertySale->customer_id,
                'property_id'      => $propertySale->property_id,
                'total_amount'     => $saleTotal,
                'paid_amount'      => (float)$propertySale->booking_amount,
                'pending_amount'   => max(0.00, round($saleTotal - (float)$propertySale->booking_amount, 2)),
                'payment_amount'   => (float)$propertySale->booking_amount,
                'payment_mode'     => 'Cash / Initial Payment',
                'transaction_ref'  => null,
                'payment_date'     => $propertySale->sale_date ?: now()->toDateString(),
                'status'           => 'paid',
                'remarks'          => 'Initial booking payment / Down payment',
            ]);
            $totalPaidSoFar = (float)$propertySale->booking_amount;
        }

        $newPaymentAmt   = (float)$validated['payment_amount'];
        $newTotalPaid    = round($totalPaidSoFar + $newPaymentAmt, 2);
        $pendingAfter    = max(0.00, round($saleTotal - $newTotalPaid, 2));

        if ($saleTotal > 0 && $newTotalPaid >= $saleTotal) {
            $status = 'paid';
        } elseif ($newTotalPaid > 0) {
            $status = 'partial';
        } else {
            $status = 'pending';
        }

        Payment::create([
            'firm_id'          => $propertySale->firm_id,
            'property_sale_id' => $propertySale->id,
            'customer_id'      => $propertySale->customer_id,
            'property_id'      => $propertySale->property_id,
            'total_amount'     => $saleTotal,
            'paid_amount'      => $newTotalPaid,
            'pending_amount'   => $pendingAfter,
            'payment_amount'   => $newPaymentAmt,
            'payment_mode'     => $validated['payment_mode'],
            'transaction_ref'  => $validated['transaction_ref'] ?? null,
            'payment_date'     => $validated['payment_date'],
            'status'           => $status,
            'remarks'          => $validated['remarks'] ?? null,
        ]);

        $propertySale->update([
            'booking_amount'   => $newTotalPaid,
            'remaining_amount' => $pendingAfter,
            'payment_status'   => $status,
        ]);

        return redirect()->back()->with('success', 'Installment payment of ₹' . number_format($newPaymentAmt, 2) . ' recorded successfully.');
    }

    public function updatePayment(Request $request, PropertySale $propertySale, Payment $payment)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $propertySale->firm_id != $firmId) {
            abort(403);
        }

        if ($payment->property_sale_id != $propertySale->id) {
            abort(404);
        }

        $validated = $request->validate([
            'payment_amount'  => 'required|numeric|min:0.01',
            'payment_date'    => 'required|date',
            'payment_mode'    => 'required|string|max:100',
            'transaction_ref' => 'nullable|string|max:150',
            'remarks'         => 'nullable|string|max:1000',
        ]);

        $payment->update([
            'payment_amount'  => (float)$validated['payment_amount'],
            'payment_date'    => $validated['payment_date'],
            'payment_mode'    => $validated['payment_mode'],
            'transaction_ref' => $validated['transaction_ref'] ?? null,
            'remarks'         => $validated['remarks'] ?? null,
        ]);

        $propertySale->recalculatePaymentStatus();

        return redirect()->back()->with('success', 'Payment installment updated and balances recalculated successfully.');
    }

    public function destroyPayment(PropertySale $propertySale, Payment $payment)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $propertySale->firm_id != $firmId) {
            abort(403);
        }

        if ($payment->property_sale_id != $propertySale->id) {
            abort(404);
        }

        $payment->delete();
        $propertySale->recalculatePaymentStatus();

        return redirect()->back()->with('success', 'Payment record deleted and balances updated successfully.');
    }

    public function destroy(PropertySale $propertySale)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $propertySale->firm_id != $firmId) {
            abort(403);
        }

        if ($propertySale->agreement_file) {
            Storage::disk('public')->delete($propertySale->agreement_file);
        }

        // Restore all associated properties to available
        $properties = $propertySale->all_properties;
        foreach ($properties as $prop) {
            $prop->update(['status' => 'available']);
            if ($prop->property_master_id && $prop->unit_no === null) {
                $pm = \App\Models\PropertyMaster::find($prop->property_master_id);
                if ($pm) {
                    $pm->update(['status' => 'active']);
                    $pm->plots()->update(['status' => 'available']);
                }
            }
        }

        $propertySale->properties()->detach();
        $propertySale->delete();

        return redirect()->route('property-sales.index')->with('success', 'Property sale deleted successfully.');
    }

    public function exportPdf(Request $request)
    {
        $query = PropertySale::with(['firm', 'property.project', 'property.propertyMaster', 'properties', 'customer', 'broker', 'payments']);

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
                $q->whereHas('property', function ($p) use ($search) {
                    $p->where('property_name', 'like', "%{$search}%")
                      ->orWhere('property_code', 'like', "%{$search}%");
                })
                ->orWhereHas('properties', function ($p) use ($search) {
                    $p->where('property_name', 'like', "%{$search}%")
                      ->orWhere('property_code', 'like', "%{$search}%");
                })
                ->orWhereHas('customer', function ($c) use ($search) {
                    $c->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('broker', function ($b) use ($search) {
                    $b->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('firm', function ($f) use ($search) {
                    $f->where('firm_name', 'like', "%{$search}%");
                })
                ->orWhere('payment_status', 'like', "%{$search}%")
                ->orWhere('sale_status', 'like', "%{$search}%");
            });
        }

        $propertySales = $query->latest()->get();
        $totalSalesCount = $propertySales->count();
        $totalSaleAmount = $propertySales->sum('sale_amount');
        $totalBookingAmount = $propertySales->sum('booking_amount');
        $totalRemainingAmount = $propertySales->sum('remaining_amount');

        return view('admin.property-sales.pdf', compact(
            'propertySales', 'totalSalesCount', 'totalSaleAmount', 'totalBookingAmount', 'totalRemainingAmount'
        ));
    }

    public function downloadPdf(PropertySale $propertySale)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $propertySale->firm_id != $firmId) {
            abort(403);
        }

        $propertySale->load([
            'firm',
            'property.propertyType',
            'property.project',
            'property.propertyMaster',
            'properties.propertyType',
            'customer',
            'broker',
            'payments',
        ]);

        $sale = $propertySale;
        return view('admin.property-sales.show-pdf', compact('propertySale', 'sale'));
    }
}
