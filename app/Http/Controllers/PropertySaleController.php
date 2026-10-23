<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use App\Models\BrokerCommission;
use App\Models\Customer;
use App\Models\Firm;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertySale;
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

        $projectsQuery = Project::with('propertyMaster')->orderBy('project_name');
        $propertiesQuery = Property::with(['project.propertyMaster', 'propertyMaster'])->orderBy('property_name');
        $propertyMastersQuery = \App\Models\PropertyMaster::with(['plots', 'projects'])->orderBy('property_name');
        $customersQuery = Customer::where('status', 'active')->orderBy('name');
        $sellersQuery = \App\Models\Seller::where('status', 'active')->orderBy('name');
        $brokersQuery = Broker::where('status', 'active')->orderBy('name');

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
                    $sq
                        ->where('status', '!=', 'sold')
                        ->whereDoesntHave('sales', function ($ssq) use ($currentPropertySale) {
                            $ssq
                                ->where('sale_status', '!=', 'cancelled')
                                ->where('property_sales.id', '!=', $currentPropertySale->id);
                        })
                        ->whereDoesntHave('salesList', function ($ssq) use ($currentPropertySale) {
                            $ssq
                                ->where('sale_status', '!=', 'cancelled')
                                ->where('property_sales.id', '!=', $currentPropertySale->id);
                        });
                })->orWhereIn('id', $currentSalePropertyIds);
            });

            $currentMasterId = $currentPropertySale->property?->property_master_id;
            $propertyMastersQuery->where(function ($q) use ($currentMasterId) {
                $q
                    ->where('status', '!=', 'sold')
                    ->orWhere('id', $currentMasterId);
            });
        } else {
            // In create / new sale: completely exclude sold properties and any property linked to active sale
            $propertiesQuery
                ->where('status', '!=', 'sold')
                ->whereDoesntHave('sales', function ($sq) {
                    $sq->where('sale_status', '!=', 'cancelled');
                })
                ->whereDoesntHave('salesList', function ($sq) {
                    $sq->where('sale_status', '!=', 'cancelled');
                });
            $propertyMastersQuery->where('status', '!=', 'sold');
        }

        $projects = $projectsQuery->get();
        $properties = Property::naturalSort($propertiesQuery->get());
        $propertyMasters = $propertyMastersQuery->get();
        $customers = $customersQuery->get();
        $sellers = $sellersQuery->get();
        $brokers = $brokersQuery->get();

        return compact('firms', 'projects', 'properties', 'propertyMasters', 'customers', 'sellers', 'brokers');
    }

    private function updatePropertyStatus(PropertySale $sale, array $allPropertyIds = [])
    {
        $propertyIds = !empty($allPropertyIds) ? $allPropertyIds : [$sale->property_id];
        $propertyIds = array_values(array_filter($propertyIds));
        $properties = Property::whereIn('id', $propertyIds)->get();
        if ($properties->isEmpty())
            return;

        $statusMap = [
            'booked' => 'booked',
            'sold' => 'sold',
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

    /**
     * Resolve existing registered broker or register a new broker if unregistered.
     */
    private function resolveOrCreateBroker(?int $brokerId, ?string $brokerName, ?string $brokerMobile, int $firmId, ?float $commissionPercentage = null, ?int $projectId = null): ?Broker
    {
        $brokerName = trim((string) $brokerName);
        $brokerMobile = trim((string) $brokerMobile);
        $cleanMobile = null;
        if ($brokerMobile !== '') {
            $digits = preg_replace('/[^0-9]/', '', $brokerMobile);
            if (strlen($digits) === 10) {
                $cleanMobile = $digits;
            } elseif (strlen($digits) > 10) {
                $cleanMobile = substr($digits, -10);
            }
        }

        // 1. If explicit broker_id provided
        if ($brokerId) {
            $broker = Broker::find($brokerId);
            if ($broker) {
                $updates = [];
                if ($cleanMobile && empty($broker->mobile)) {
                    $updates['mobile'] = $cleanMobile;
                }
                if ($commissionPercentage && empty($broker->commission_percentage)) {
                    $updates['commission_percentage'] = $commissionPercentage;
                }
                if (!empty($updates)) {
                    $broker->update($updates);
                }
                $broker->syncFirms([$firmId]);
                return $broker;
            }
        }

        // 2. If no broker_id, but broker_name is given (Unregistered Broker)
        if (empty($brokerName)) {
            return null;
        }

        // Check if matching broker already exists by mobile or name
        $existing = null;
        if ($cleanMobile) {
            $existing = Broker::where('mobile', $cleanMobile)->first();
        }
        if (!$existing) {
            $existing = Broker::whereRaw('LOWER(name) = ?', [strtolower($brokerName)])
                ->where(function ($q) use ($firmId) {
                    $q->where('firm_id', $firmId)
                      ->orWhereHas('firms', fn($fq) => $fq->where('firms.id', $firmId));
                })->first();
        }

        if ($existing) {
            $updates = [];
            if ($cleanMobile && empty($existing->mobile)) {
                $updates['mobile'] = $cleanMobile;
            }
            if ($commissionPercentage && empty($existing->commission_percentage)) {
                $updates['commission_percentage'] = $commissionPercentage;
            }
            if (!empty($updates)) {
                $existing->update($updates);
            }
            $existing->syncFirms([$firmId]);
            return $existing;
        }

        // 3. Register the new broker into the 'brokers' table
        $newBroker = Broker::create([
            'firm_id'               => $firmId,
            'project_id'            => $projectId,
            'name'                  => $brokerName,
            'mobile'                => $cleanMobile ?: null,
            'commission_percentage' => $commissionPercentage,
            'status'                => 'active',
        ]);

        $newBroker->syncFirms([$firmId]);

        return $newBroker;
    }

    /**
     * Create or sync BrokerCommission record linked to this PropertySale.
     */
    private function syncBrokerCommission(PropertySale $sale): ?BrokerCommission
    {
        // If no broker is set and no commission amount was entered, remove any stale commission
        if (!$sale->broker_id && (float)($sale->broker_commission_amount ?? 0) <= 0) {
            BrokerCommission::where('property_sale_id', $sale->id)->delete();
            return null;
        }

        $brokerId = $sale->broker_id;
        if (!$brokerId && $sale->broker_name) {
            $b = $this->resolveOrCreateBroker(null, $sale->broker_name, $sale->broker_mobile, (int)$sale->firm_id);
            if ($b) {
                $brokerId = $b->id;
                $sale->updateQuietly(['broker_id' => $brokerId]);
            }
        }

        if (!$brokerId) {
            return null;
        }

        // Map payment status from PropertySale ('unpaid', 'partial', 'paid') to BrokerCommission ('pending', 'partial', 'paid')
        $commPaymentStatus = match (strtolower((string) $sale->broker_commission_status)) {
            'paid'    => 'paid',
            'partial' => 'partial',
            default   => 'pending',
        };

        $commType = in_array($sale->broker_commission_type, ['percentage', 'fixed']) ? $sale->broker_commission_type : 'percentage';
        $commValue = ($commType === 'percentage') ? (float)($sale->broker_commission_rate ?? 0) : (float)($sale->broker_commission_amount ?? 0);
        $commAmount = (float)($sale->broker_commission_amount ?? 0);

        if ($commAmount <= 0 && $commType === 'percentage' && $commValue > 0 && (float)$sale->sale_amount > 0) {
            $commAmount = round(((float)$sale->sale_amount * $commValue) / 100, 2);
        }

        $paymentDate = $sale->broker_commission_payment_date 
            ? $sale->broker_commission_payment_date->format('Y-m-d') 
            : ($sale->sale_date ? $sale->sale_date->format('Y-m-d') : now()->toDateString());

        $remarks = $sale->broker_notes 
            ?: ('Property Sale: ' . ($sale->property_names ?: 'Property #' . $sale->property_id));

        $commission = BrokerCommission::updateOrCreate(
            ['property_sale_id' => $sale->id],
            [
                'firm_id'           => $sale->firm_id,
                'broker_id'         => $brokerId,
                'property_id'       => $sale->property_id,
                'customer_id'       => $sale->customer_id,
                'booking_id'        => null,
                'property_sale_id'  => $sale->id,
                'commission_type'   => $commType,
                'commission_value'  => $commValue,
                'commission_amount' => $commAmount,
                'payment_status'    => $commPaymentStatus,
                'payment_date'      => $paymentDate,
                'remarks'           => $remarks,
                'status'            => 'active',
                'created_by'        => Auth::id() ?: session('user_id'),
            ]
        );

        if ($sale->firm_id) {
            $commission->syncFirms([$sale->firm_id]);
        }

        return $commission;
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
                $q
                    ->whereHas('property', function ($p) use ($search) {
                        $p
                            ->where('property_name', 'like', "%{$search}%")
                            ->orWhere('property_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('properties', function ($p) use ($search) {
                        $p
                            ->where('property_name', 'like', "%{$search}%")
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
        $totalSalesRevenue = (float) $summarySales->sum('sale_amount');
        $totalSalesPurchaseCost = (float) $summarySales->sum(fn($s) => $s->total_cost_basis);
        $totalSalesProfit = (float) $summarySales->sum(fn($s) => $s->net_profit);
        $salesProfitMargin = $totalSalesRevenue > 0 ? round(($totalSalesProfit / $totalSalesRevenue) * 100, 1) : 0.0;
        $totalSalesCount = $summarySales->count();
        $totalPaidAmount = (float) $summarySales->sum('booking_amount');
        $totalDueAmount = (float) $summarySales->sum('remaining_amount');

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
            $firmId = (is_array($firmIds) && !empty($firmIds))
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
                        'firm_id' => $pm->firm_id,
                        'property_name' => $pm->property_name . ' (Entire Property)',
                        'property_code' => $pm->property_code ? $pm->property_code . '-ENTIRE' : 'PROP-' . $pm->id . '-ENTIRE',
                        'location' => $pm->location,
                        'city' => $pm->city,
                        'address' => $pm->address,
                        'size' => $pm->total_area,
                        'size_unit' => $pm->area_unit ?: 'sq.ft',
                        'price' => $pm->purchase_price,
                        'purchase_rate' => $pm->purchase_rate,
                        'purchase_date' => $pm->purchase_date,
                        'status' => 'available',
                        'description' => 'Entire Property Master: ' . $pm->property_name,
                    ]
                );
                $request->merge(['property_id' => $entireProp->id]);
                $submittedPropIds = [$entireProp->id];
            }
        }

        $request->validate([
            'firm_id' => 'required|exists:firms,id',
            'property_id' => 'required|exists:properties,id',
            'customer_id' => 'required|exists:customers,id',
            'seller_id' => 'nullable|exists:sellers,id',
            'seller_name' => 'nullable|string|max:255',
            'broker_id' => 'nullable|exists:brokers,id',
            'broker_name' => 'nullable|string|max:255',
            'broker_mobile' => 'nullable|string|max:25',
            'broker_commission_type' => 'nullable|in:percentage,fixed',
            'broker_commission_rate' => 'nullable|numeric|min:0',
            'broker_commission_amount' => 'nullable|numeric|min:0',
            'broker_commission_paid' => 'nullable|numeric|min:0',
            'broker_commission_due' => 'nullable|numeric|min:0',
            'broker_commission_payment_mode' => 'nullable|string|max:100',
            'broker_commission_payment_date' => 'nullable|date',
            'broker_commission_status' => 'nullable|string|max:50',
            'broker_notes' => 'nullable|string|max:2000',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'property_expenses' => 'nullable|numeric|min:0',
            'total_area' => 'nullable|numeric|min:0',
            'area_unit' => 'nullable|string|max:50',
            'purchase_rate' => 'nullable|numeric|min:0',
            'sell_rate' => 'nullable|numeric|min:0',
            'sale_date' => 'nullable|date',
            'sale_amount' => 'nullable|numeric',
            'booking_amount' => 'nullable|numeric',
            'remaining_amount' => 'nullable|numeric',
            'payment_status' => 'required',
            'sale_status' => 'required',
            'agreement_file' => 'nullable|file',
            'note' => 'nullable',
        ]);

        $agreementPath = null;
        if ($request->hasFile('agreement_file')) {
            $agreementPath = $request->file('agreement_file')->store('property-agreements', 'public');
        }

        $saleAmount = (float) ($request->sale_amount ?? 0);
        $bookingAmount = (float) ($request->booking_amount ?? 0);
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

        // Broker commission calculation & unregistered broker resolution
        $brokerCommType = $request->broker_commission_type ?: 'percentage';
        $brokerCommRate = $request->filled('broker_commission_rate') ? floatval($request->broker_commission_rate) : null;
        $brokerCommAmount = $request->filled('broker_commission_amount') ? floatval($request->broker_commission_amount) : 0.0;
        if ($brokerCommType === 'percentage' && $brokerCommRate && $brokerCommAmount == 0 && $saleAmount > 0) {
            $brokerCommAmount = round(($saleAmount * $brokerCommRate) / 100, 2);
        }
        $brokerCommPaid = $request->filled('broker_commission_paid') ? floatval($request->broker_commission_paid) : 0.0;
        $brokerCommDue = max(0.0, $brokerCommAmount - $brokerCommPaid);
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

        // Unregistered / Registered Broker resolution
        $firstProp = Property::find($request->property_id);
        $resolvedBroker = $this->resolveOrCreateBroker(
            $request->filled('broker_id') ? (int) $request->broker_id : null,
            $request->broker_name,
            $request->broker_mobile,
            (int) $request->firm_id,
            $brokerCommRate,
            $firstProp?->project_id
        );
        $brokerId = $resolvedBroker?->id;
        $brokerName = $request->filled('broker_name') ? $request->broker_name : ($resolvedBroker?->name ?? null);
        $brokerMobile = $request->filled('broker_mobile') ? $request->broker_mobile : ($resolvedBroker?->mobile ?? null);

        $sale = PropertySale::create([
            'firm_id' => $request->firm_id,
            'property_id' => $request->property_id,
            'customer_id' => $request->customer_id,
            'seller_id' => $sellerId,
            'seller_name' => $sellerName,
            'broker_id' => $brokerId,
            'broker_name' => $brokerName,
            'broker_mobile' => $brokerMobile,
            'purchase_date' => $request->purchase_date,
            'purchase_cost' => $request->filled('purchase_cost') ? (float) $request->purchase_cost : null,
            'property_expenses' => $request->filled('property_expenses') ? (float) $request->property_expenses : null,
            'total_area' => $request->filled('total_area') ? (float) $request->total_area : null,
            'area_unit' => $request->area_unit ?: 'Sq.Ft',
            'purchase_rate' => $request->filled('purchase_rate') ? (float) $request->purchase_rate : null,
            'sell_rate' => $request->filled('sell_rate') ? (float) $request->sell_rate : null,
            'broker_commission_type' => $brokerCommType,
            'broker_commission_rate' => $brokerCommRate,
            'broker_commission_amount' => $brokerCommAmount,
            'broker_commission_paid' => $brokerCommPaid,
            'broker_commission_due' => $brokerCommDue,
            'broker_commission_payment_mode' => $request->broker_commission_payment_mode,
            'broker_commission_payment_date' => $request->broker_commission_payment_date,
            'broker_commission_status' => $brokerCommStatus,
            'broker_notes' => $request->broker_notes,
            'sale_date' => $request->sale_date,
            'sale_amount' => $saleAmount,
            'booking_amount' => $bookingAmount,
            'remaining_amount' => $remainingAmount,
            'payment_status' => $paymentStatus,
            'sale_status' => $request->sale_status,
            'agreement_file' => $agreementPath,
            'note' => $request->note,
        ]);

        // Sync multiple properties in pivot table
        if (!empty($submittedPropIds)) {
            $sale->properties()->sync($submittedPropIds);
        }

        $this->updatePropertyStatus($sale, $submittedPropIds);

        // Sync to BrokerCommission table
        $this->syncBrokerCommission($sale);

        // Record initial booking payment if booking_amount > 0
        if ($bookingAmount > 0) {
            Payment::create([
                'firm_id' => $sale->firm_id,
                'property_sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'property_id' => $sale->property_id,
                'total_amount' => $saleAmount,
                'paid_amount' => $bookingAmount,
                'pending_amount' => $remainingAmount,
                'payment_amount' => $bookingAmount,
                'payment_mode' => $request->payment_mode ?: 'Cash',
                'transaction_ref' => $request->transaction_ref ?: null,
                'payment_date' => $request->sale_date ?: now()->toDateString(),
                'status' => $paymentStatus,
                'remarks' => 'Initial booking / down payment',
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
            'brokerCommission',
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
                        'firm_id' => $pm->firm_id,
                        'property_name' => $pm->property_name . ' (Entire Property)',
                        'property_code' => $pm->property_code ? $pm->property_code . '-ENTIRE' : 'PROP-' . $pm->id . '-ENTIRE',
                        'location' => $pm->location,
                        'city' => $pm->city,
                        'address' => $pm->address,
                        'size' => $pm->total_area,
                        'size_unit' => $pm->area_unit ?: 'sq.ft',
                        'price' => $pm->purchase_price,
                        'purchase_rate' => $pm->purchase_rate,
                        'purchase_date' => $pm->purchase_date,
                        'status' => 'available',
                        'description' => 'Entire Property Master: ' . $pm->property_name,
                    ]
                );
                $request->merge(['property_id' => $entireProp->id]);
                $submittedPropIds = [$entireProp->id];
            }
        }

        $request->validate([
            'firm_id' => 'required|exists:firms,id',
            'property_id' => 'required|exists:properties,id',
            'customer_id' => 'required|exists:customers,id',
            'broker_id' => 'nullable|exists:brokers,id',
            'broker_name' => 'nullable|string|max:255',
            'broker_mobile' => 'nullable|string|max:25',
            'broker_commission_type' => 'nullable|in:percentage,fixed',
            'broker_commission_rate' => 'nullable|numeric|min:0',
            'broker_commission_amount' => 'nullable|numeric|min:0',
            'broker_commission_paid' => 'nullable|numeric|min:0',
            'broker_commission_due' => 'nullable|numeric|min:0',
            'broker_commission_payment_mode' => 'nullable|string|max:100',
            'broker_commission_payment_date' => 'nullable|date',
            'broker_commission_status' => 'nullable|string|max:50',
            'broker_notes' => 'nullable|string|max:2000',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'property_expenses' => 'nullable|numeric|min:0',
            'total_area' => 'nullable|numeric|min:0',
            'area_unit' => 'nullable|string|max:50',
            'purchase_rate' => 'nullable|numeric|min:0',
            'sell_rate' => 'nullable|numeric|min:0',
            'sale_date' => 'nullable|date',
            'sale_amount' => 'nullable|numeric',
            'booking_amount' => 'nullable|numeric',
            'remaining_amount' => 'nullable|numeric',
            'payment_status' => 'required',
            'sale_status' => 'required',
            'agreement_file' => 'nullable|file',
            'note' => 'nullable',
        ]);

        $agreementPath = $propertySale->agreement_file;
        if ($request->hasFile('agreement_file')) {
            if ($propertySale->agreement_file) {
                Storage::disk('public')->delete($propertySale->agreement_file);
            }
            $agreementPath = $request->file('agreement_file')->store('property-agreements', 'public');
        }

        $saleAmount = (float) ($request->sale_amount !== null ? $request->sale_amount : ($propertySale->sale_amount ?? 0));

        // If payments table has records, preserve the accurate sum of payments
        $paymentsSum = (float) $propertySale->payments()->sum('payment_amount');
        if ($paymentsSum > 0 && !$request->filled('booking_amount')) {
            $bookingAmount = $paymentsSum;
        } else {
            $bookingAmount = (float) ($request->booking_amount !== null ? $request->booking_amount : ($propertySale->booking_amount ?? 0));
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

        // Broker commission calculation & unregistered broker resolution
        $brokerCommType = $request->filled('broker_commission_type') ? $request->broker_commission_type : ($propertySale->broker_commission_type ?? 'percentage');
        $brokerCommRate = $request->filled('broker_commission_rate') ? floatval($request->broker_commission_rate) : ($request->has('broker_commission_rate') ? null : $propertySale->broker_commission_rate);
        $brokerCommAmount = $request->filled('broker_commission_amount') ? floatval($request->broker_commission_amount) : ($propertySale->broker_commission_amount ?? 0.0);
        if ($brokerCommType === 'percentage' && $brokerCommRate && ($brokerCommAmount == 0 || $request->filled('broker_commission_rate')) && $saleAmount > 0) {
            $brokerCommAmount = round(($saleAmount * $brokerCommRate) / 100, 2);
        }
        $brokerCommPaid = $request->filled('broker_commission_paid') ? floatval($request->broker_commission_paid) : ($propertySale->broker_commission_paid ?? 0.0);
        $brokerCommDue = max(0.0, $brokerCommAmount - $brokerCommPaid);
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

        // Unregistered / Registered Broker resolution
        $inputBrokerId = $request->filled('broker_id') ? (int) $request->broker_id : ($request->has('broker_id') ? null : $propertySale->broker_id);
        $inputBrokerName = $request->filled('broker_name') ? $request->broker_name : ($request->has('broker_name') ? null : $propertySale->broker_name);
        $inputBrokerMobile = $request->filled('broker_mobile') ? $request->broker_mobile : ($request->has('broker_mobile') ? null : $propertySale->broker_mobile);

        $firstProp = Property::find($request->property_id);
        $resolvedBroker = $this->resolveOrCreateBroker(
            $inputBrokerId,
            $inputBrokerName,
            $inputBrokerMobile,
            (int) $request->firm_id,
            $brokerCommRate,
            $firstProp?->project_id
        );
        $brokerId = $resolvedBroker?->id;
        $brokerName = $inputBrokerName ?: ($resolvedBroker?->name ?? null);
        $brokerMobile = $inputBrokerMobile ?: ($resolvedBroker?->mobile ?? null);

        // Collect old properties to release removed ones
        $oldPropIds = $propertySale->all_properties->pluck('id')->toArray();

        $propertySale->update([
            'firm_id' => $request->firm_id,
            'property_id' => $request->property_id,
            'customer_id' => $request->customer_id,
            'seller_id' => $sellerId,
            'seller_name' => $sellerName,
            'broker_id' => $brokerId,
            'broker_name' => $brokerName,
            'broker_mobile' => $brokerMobile,
            'purchase_date' => $request->purchase_date,
            'purchase_cost' => $request->filled('purchase_cost') ? (float) $request->purchase_cost : null,
            'property_expenses' => $request->filled('property_expenses') ? (float) $request->property_expenses : null,
            'total_area' => $request->filled('total_area') ? (float) $request->total_area : null,
            'area_unit' => $request->area_unit ?: ($propertySale->area_unit ?: 'Sq.Ft'),
            'purchase_rate' => $request->filled('purchase_rate') ? (float) $request->purchase_rate : null,
            'sell_rate' => $request->filled('sell_rate') ? (float) $request->sell_rate : null,
            'broker_commission_type' => $brokerCommType,
            'broker_commission_rate' => $brokerCommRate,
            'broker_commission_amount' => $brokerCommAmount,
            'broker_commission_paid' => $brokerCommPaid,
            'broker_commission_due' => $brokerCommDue,
            'broker_commission_payment_mode' => $request->broker_commission_payment_mode ?: $propertySale->broker_commission_payment_mode,
            'broker_commission_payment_date' => $request->has('broker_commission_payment_date') ? $request->broker_commission_payment_date : $propertySale->broker_commission_payment_date,
            'broker_commission_status' => $brokerCommStatus,
            'broker_notes' => $request->broker_notes ?: $propertySale->broker_notes,
            'sale_date' => $request->sale_date,
            'sale_amount' => $saleAmount,
            'booking_amount' => $bookingAmount,
            'remaining_amount' => $remainingAmount,
            'payment_status' => $paymentStatus,
            'sale_status' => $request->sale_status,
            'agreement_file' => $agreementPath,
            'note' => $request->note,
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

        // Sync to BrokerCommission table
        $this->syncBrokerCommission($propertySale);

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
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string|max:100',
            'transaction_ref' => 'nullable|string|max:150',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $saleTotal = (float) ($propertySale->sale_amount ?? 0);
        $totalPaidSoFar = (float) Payment::where('property_sale_id', $propertySale->id)->sum('payment_amount');

        // If this is the first payment entry in the payments table, but propertySale already had an initial booking_amount,
        // create a backfilled record for that initial booking_amount so history is accurate.
        if ($totalPaidSoFar == 0 && (float) $propertySale->booking_amount > 0) {
            Payment::create([
                'firm_id' => $propertySale->firm_id,
                'property_sale_id' => $propertySale->id,
                'customer_id' => $propertySale->customer_id,
                'property_id' => $propertySale->property_id,
                'total_amount' => $saleTotal,
                'paid_amount' => (float) $propertySale->booking_amount,
                'pending_amount' => max(0.0, round($saleTotal - (float) $propertySale->booking_amount, 2)),
                'payment_amount' => (float) $propertySale->booking_amount,
                'payment_mode' => 'Cash / Initial Payment',
                'transaction_ref' => null,
                'payment_date' => $propertySale->sale_date ?: now()->toDateString(),
                'status' => 'paid',
                'remarks' => 'Initial booking payment / Down payment',
            ]);
            $totalPaidSoFar = (float) $propertySale->booking_amount;
        }

        $newPaymentAmt = (float) $validated['payment_amount'];
        $newTotalPaid = round($totalPaidSoFar + $newPaymentAmt, 2);
        $pendingAfter = max(0.0, round($saleTotal - $newTotalPaid, 2));

        if ($saleTotal > 0 && $newTotalPaid >= $saleTotal) {
            $status = 'paid';
        } elseif ($newTotalPaid > 0) {
            $status = 'partial';
        } else {
            $status = 'pending';
        }

        Payment::create([
            'firm_id' => $propertySale->firm_id,
            'property_sale_id' => $propertySale->id,
            'customer_id' => $propertySale->customer_id,
            'property_id' => $propertySale->property_id,
            'total_amount' => $saleTotal,
            'paid_amount' => $newTotalPaid,
            'pending_amount' => $pendingAfter,
            'payment_amount' => $newPaymentAmt,
            'payment_mode' => $validated['payment_mode'],
            'transaction_ref' => $validated['transaction_ref'] ?? null,
            'payment_date' => $validated['payment_date'],
            'status' => $status,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        $propertySale->update([
            'booking_amount' => $newTotalPaid,
            'remaining_amount' => $pendingAfter,
            'payment_status' => $status,
        ]);

        return redirect()->back()->with('success', 'Installment payment of ₹' . number_format($newPaymentAmt, 2) . ' recorded successfully.');
    }

    public function storeBrokerPayment(Request $request, PropertySale $propertySale)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $propertySale->firm_id != $firmId) {
            abort(403);
        }

        $commTotal = (float) ($propertySale->broker_commission_amount ?? 0);
        $currentPaid = (float) ($propertySale->broker_commission_paid ?? 0);
        $currentDue = max(0.0, round($commTotal - $currentPaid, 2));

        $validated = $request->validate([
            'broker_payment_amount' => 'required|numeric|min:0.01',
            'broker_payment_date'   => 'required|date',
            'broker_payment_mode'   => 'required|string|max:100',
            'broker_payment_notes'  => 'nullable|string|max:1000',
        ]);

        $payAmount = (float) $validated['broker_payment_amount'];
        $newPaid = round($currentPaid + $payAmount, 2);
        $newDue = max(0.0, round($commTotal - $newPaid, 2));

        $status = 'unpaid';
        if ($commTotal > 0) {
            if ($newPaid >= $commTotal) {
                $status = 'paid';
            } elseif ($newPaid > 0) {
                $status = 'partial';
            }
        }

        $newNotes = $propertySale->broker_notes;
        $dateStr = \Carbon\Carbon::parse($validated['broker_payment_date'])->format('d M Y');
        $entryNote = "[{$dateStr}] Paid ₹" . number_format($payAmount, 2) . " via " . $validated['broker_payment_mode'] . (!empty($validated['broker_payment_notes']) ? " ({$validated['broker_payment_notes']})" : "");
        $newNotes = $newNotes ? ($newNotes . "\n" . $entryNote) : $entryNote;

        $propertySale->update([
            'broker_commission_paid'         => $newPaid,
            'broker_commission_due'          => $newDue,
            'broker_commission_status'        => $status,
            'broker_commission_payment_mode' => $validated['broker_payment_mode'],
            'broker_commission_payment_date' => $validated['broker_payment_date'],
            'broker_notes'                   => $newNotes,
        ]);

        // Sync to BrokerCommission table
        $this->syncBrokerCommission($propertySale);

        return redirect()->back()->with('success', 'Broker payment of ₹' . number_format($payAmount, 2) . ' recorded successfully. Remaining Due: ₹' . number_format($newDue, 2));
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
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string|max:100',
            'transaction_ref' => 'nullable|string|max:150',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $payment->update([
            'payment_amount' => (float) $validated['payment_amount'],
            'payment_date' => $validated['payment_date'],
            'payment_mode' => $validated['payment_mode'],
            'transaction_ref' => $validated['transaction_ref'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
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

        BrokerCommission::where('property_sale_id', $propertySale->id)->delete();
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
                $q
                    ->whereHas('property', function ($p) use ($search) {
                        $p
                            ->where('property_name', 'like', "%{$search}%")
                            ->orWhere('property_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('properties', function ($p) use ($search) {
                        $p
                            ->where('property_name', 'like', "%{$search}%")
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
