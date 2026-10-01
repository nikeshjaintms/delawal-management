<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Property;
use App\Models\PropertyMaster;
use App\Models\Project;
use App\Models\PropertySale;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\RentalPayment;
use App\Models\Expense;
use App\Models\Loan;
use App\Models\Material;
use App\Models\StockInward;
use App\Models\StockOutward;
use App\Models\Firm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        Property::syncAllStatuses();

        // ══════════════════════════════════════════════════════════
        //  FIRM SESSION DASHBOARD
        // ══════════════════════════════════════════════════════════
        if (session('login_type') === 'firm' && session('firm_id')) {
            return $this->firmDashboard($request);
        }

        // ══════════════════════════════════════════════════════════
        //  ADMIN DASHBOARD
        // ══════════════════════════════════════════════════════════
        return $this->adminDashboard($request);
    }

    // ─────────────────────────────────────────────────────────────
    //  Admin Dashboard
    // ─────────────────────────────────────────────────────────────
    private function adminDashboard(Request $request)
    {
        $propertyMasterId = $request->filled('property_master_id') ? (int)$request->property_master_id : null;
        $projectId        = $request->filled('project_id') ? (int)$request->project_id : null;
        $filterType       = $request->get('filter_type');

        if ($propertyMasterId && !$projectId) {
            $filterType = 'property';
        } elseif ($projectId && !$propertyMasterId) {
            $filterType = 'project';
        } elseif (!$propertyMasterId && !$projectId) {
            $filterType = 'all';
        }

        // Dropdown lists
        $propertyMastersList = PropertyMaster::withCount('plots')->orderBy('property_name')->get();
        $projectsList        = Project::withCount('properties')->orderBy('project_name')->get();

        $selectedPropertyMaster = $propertyMasterId ? PropertyMaster::with(['seller', 'broker'])->find($propertyMasterId) : null;
        $selectedProject        = $projectId ? Project::with(['propertyMaster'])->find($projectId) : null;

        $totalFirms    = Firm::count();
        $activeFirms   = Firm::where('status', 'active')->count();
        $inactiveFirms = Firm::where('status', 'inactive')->count();
        $totalUsers    = User::count();
        $activeUsers   = User::where('status', 'active')->count();
        $totalCustomers = Customer::count();
        $totalProjects  = Project::count();
        $activeProjects = Project::where('status', 'active')->count();

        // ── Case 1: FILTER BY PROPERTY MASTER ────────────────────
        if ($filterType === 'property' && $selectedPropertyMaster) {
            $filteredPlots = Property::where('property_master_id', $selectedPropertyMaster->id)
                ->whereNull('project_id')
                ->get();
            $filteredPlots = Property::naturalSort($filteredPlots);

            $plotIds = $filteredPlots->pluck('id')->toArray();
            $totalProperties = $filteredPlots->count() > 0 ? $filteredPlots->count() : (int)($selectedPropertyMaster->total_units_count ?? 1);
            $availableProperties = $filteredPlots->where('status', 'available')->count();
            $bookedProperties    = $filteredPlots->where('status', 'booked')->count();
            $soldProperties      = $filteredPlots->where('status', 'sold')->count();
            $rentedProperties    = $filteredPlots->where('status', 'rented')->count();
            $totalBookings       = $bookedProperties;
            $portfolioVal        = (float)$filteredPlots->sum('price');
            if ($portfolioVal <= 0) {
                $portfolioVal = (float)($selectedPropertyMaster->purchase_price ?? 0);
            }

            // Sales linked to this property's plots
            $salesList = PropertySale::with(['customer', 'broker', 'properties.propertyMaster', 'property.propertyMaster'])
                ->where(function ($q) use ($plotIds, $selectedPropertyMaster) {
                    if (!empty($plotIds)) {
                        $q->whereIn('property_id', $plotIds)
                          ->orWhereHas('properties', fn($sub) => $sub->whereIn('properties.id', $plotIds));
                    }
                })
                ->where('sale_status', '!=', 'cancelled')
                ->get();

            $totalSalesRevenue      = (float)$salesList->sum('sale_amount');
            $totalSalesPurchaseCost = (float)$salesList->sum(fn($s) => $s->total_purchase_cost);
            if ($totalSalesPurchaseCost <= 0 && $soldProperties > 0 && (float)($selectedPropertyMaster->purchase_price ?? 0) > 0) {
                $totalUnits = max(1, $totalProperties);
                $totalSalesPurchaseCost = round(((float)$selectedPropertyMaster->purchase_price / $totalUnits) * $soldProperties, 2);
            }
            $totalSalesGrossProfit  = $totalSalesRevenue - $totalSalesPurchaseCost;
            $totalSalesCommission   = (float)$salesList->sum('broker_commission_amount');
            $totalSalesProfit       = (float)$salesList->sum(fn($s) => $s->net_profit);
            if ($salesList->isEmpty() && $totalSalesRevenue > 0) {
                $totalSalesProfit = $totalSalesGrossProfit - $totalSalesCommission;
            }
            $salesProfitMargin      = $totalSalesRevenue > 0 ? round(($totalSalesProfit / $totalSalesRevenue) * 100, 1) : 0.0;
            $totalSoldUnitsCount    = (int)$salesList->sum(fn($s) => $s->properties->count() ?: 1);
            if ($totalSoldUnitsCount === 0 && $soldProperties > 0) {
                $totalSoldUnitsCount = $soldProperties;
            }

            // Expenses for this PropertyMaster
            $totalExpenses = (float)$selectedPropertyMaster->total_expenses;

            // Cashflow & Receipts for this Property
            $paymentReceived  = !empty($plotIds) ? (Payment::whereIn('property_id', $plotIds)->sum('payment_amount') ?: 0) : 0;
            $bookingReceived  = !empty($plotIds) ? (Booking::where(function($q) use ($plotIds) {
                $q->whereIn('property_id', $plotIds)
                  ->orWhereHas('properties', fn($sub) => $sub->whereIn('properties.id', $plotIds));
            })->where('status', '!=', 'cancelled')->sum('booking_amount') ?: 0) : 0;
            $saleInitialPaid  = $salesList->sum('booking_amount') ?: 0;
            $rentalReceived   = !empty($plotIds) ? (RentalPayment::whereIn('property_id', $plotIds)->sum('paid_amount') ?: 0) : 0;
            $totalReceivedAmt = $paymentReceived + $bookingReceived + $saleInitialPaid + $rentalReceived;
            $netProfit        = $totalReceivedAmt - $totalExpenses;

            $salePending      = $salesList->sum('remaining_amount') ?: 0;
            $bookingPending   = !empty($plotIds) ? (Booking::where(function($q) use ($plotIds) {
                $q->whereIn('property_id', $plotIds)
                  ->orWhereHas('properties', fn($sub) => $sub->whereIn('properties.id', $plotIds));
            })->where('status', '!=', 'cancelled')->sum('remaining_amount') ?: 0) : 0;
            $totalPendingAmt  = $salePending + $bookingPending;

            $recentCustomers  = Customer::latest()->limit(5)->get();
            $recentPayments   = !empty($plotIds) ? Payment::with(['customer', 'property'])->whereIn('property_id', $plotIds)->latest()->limit(5)->get() : collect();

        // ── Case 2: FILTER BY PROJECT ────────────────────────────
        } elseif ($filterType === 'project' && $selectedProject) {
            $filteredPlots = Property::where('project_id', $selectedProject->id)
                ->whereNull('property_master_id')
                ->get();
            $filteredPlots = Property::naturalSort($filteredPlots);

            $plotIds = $filteredPlots->pluck('id')->toArray();
            $totalProperties     = $filteredPlots->count();
            $availableProperties = $filteredPlots->where('status', 'available')->count();
            $bookedProperties    = $filteredPlots->where('status', 'booked')->count();
            $soldProperties      = $filteredPlots->where('status', 'sold')->count();
            $rentedProperties    = $filteredPlots->where('status', 'rented')->count();
            $totalBookings       = $bookedProperties;
            $portfolioVal        = (float)$filteredPlots->sum('price');

            // Sales linked to this project's plots
            $salesList = PropertySale::with(['customer', 'broker', 'properties.propertyMaster', 'property.propertyMaster'])
                ->where(function ($q) use ($plotIds) {
                    if (!empty($plotIds)) {
                        $q->whereIn('property_id', $plotIds)
                          ->orWhereHas('properties', fn($sub) => $sub->whereIn('properties.id', $plotIds));
                    }
                })
                ->where('sale_status', '!=', 'cancelled')
                ->get();

            $totalSalesRevenue      = (float)$salesList->sum('sale_amount');
            $totalSalesPurchaseCost = (float)$salesList->sum(fn($s) => $s->total_purchase_cost);
            $totalSalesGrossProfit  = $totalSalesRevenue - $totalSalesPurchaseCost;
            $totalSalesCommission   = (float)$salesList->sum('broker_commission_amount');
            $totalSalesProfit       = (float)$salesList->sum(fn($s) => $s->net_profit);
            $salesProfitMargin      = $totalSalesRevenue > 0 ? round(($totalSalesProfit / $totalSalesRevenue) * 100, 1) : 0.0;
            $totalSoldUnitsCount    = (int)$salesList->sum(fn($s) => $s->properties->count() ?: 1);

            // Expenses for this Project
            $totalExpenses = (float)$selectedProject->total_expenses;

            // Cashflow & Receipts for this Project
            $paymentReceived  = !empty($plotIds) ? (Payment::whereIn('property_id', $plotIds)->sum('payment_amount') ?: 0) : 0;
            $bookingReceived  = !empty($plotIds) ? (Booking::where(function($q) use ($plotIds) {
                $q->whereIn('property_id', $plotIds)
                  ->orWhereHas('properties', fn($sub) => $sub->whereIn('properties.id', $plotIds));
            })->where('status', '!=', 'cancelled')->sum('booking_amount') ?: 0) : 0;
            $saleInitialPaid  = $salesList->sum('booking_amount') ?: 0;
            $rentalReceived   = !empty($plotIds) ? (RentalPayment::whereIn('property_id', $plotIds)->sum('paid_amount') ?: 0) : 0;
            $totalReceivedAmt = $paymentReceived + $bookingReceived + $saleInitialPaid + $rentalReceived;
            $netProfit        = $totalReceivedAmt - $totalExpenses;

            $salePending      = $salesList->sum('remaining_amount') ?: 0;
            $bookingPending   = !empty($plotIds) ? (Booking::where(function($q) use ($plotIds) {
                $q->whereIn('property_id', $plotIds)
                  ->orWhereHas('properties', fn($sub) => $sub->whereIn('properties.id', $plotIds));
            })->where('status', '!=', 'cancelled')->sum('remaining_amount') ?: 0) : 0;
            $totalPendingAmt  = $salePending + $bookingPending;

            $recentCustomers  = Customer::latest()->limit(5)->get();
            $recentPayments   = !empty($plotIds) ? Payment::with(['customer', 'property'])->whereIn('property_id', $plotIds)->latest()->limit(5)->get() : collect();

        // ── Case 3: ALL OVERVIEW (GLOBAL) ────────────────────────
        } else {
            $filteredPlots = null;
            $filterType = 'all';

            $totalProperties     = Property::where(function($q) {
                $q->whereNotNull('project_id')->orWhereNotNull('property_master_id');
            })->count();
            $availableProperties = Property::where(function($q) {
                $q->whereNotNull('project_id')->orWhereNotNull('property_master_id');
            })->where('status', 'available')->count();
            $bookedProperties    = Property::where(function($q) {
                $q->whereNotNull('project_id')->orWhereNotNull('property_master_id');
            })->where('status', 'booked')->count();
            $soldProperties      = Property::where(function($q) {
                $q->whereNotNull('project_id')->orWhereNotNull('property_master_id');
            })->where('status', 'sold')->count();
            $rentedProperties    = Property::where(function($q) {
                $q->whereNotNull('project_id')->orWhereNotNull('property_master_id');
            })->where('status', 'rented')->count();
            $totalBookings       = $bookedProperties;
            $portfolioVal        = (float)Property::sum('price');

            // Comprehensive revenue calculation from all modules
            $paymentReceived     = Payment::sum('payment_amount') ?: 0;
            $bookingReceived     = Booking::sum('booking_amount') ?: 0;
            $saleInitialPaid     = PropertySale::sum('booking_amount') ?: 0;
            $rentalReceived      = RentalPayment::sum('paid_amount') ?: 0;
            $totalReceivedAmt    = $paymentReceived + $bookingReceived + $saleInitialPaid + $rentalReceived;

            $totalExpenses       = Expense::sum('amount') ?: 0;
            $netProfit           = $totalReceivedAmt - $totalExpenses;

            $salePending         = PropertySale::sum('remaining_amount') ?: 0;
            $bookingPending      = Booking::sum('remaining_amount') ?: 0;
            $totalPendingAmt     = $salePending + $bookingPending;

            $recentCustomers     = Customer::latest()->limit(5)->get();
            $recentPayments      = Payment::with(['customer', 'property'])->latest()->limit(5)->get();

            // Property Sales & Profit Analysis (Purchase Cost vs Selling Price)
            $salesList              = PropertySale::with(['properties.propertyMaster', 'property.propertyMaster'])
                ->where('sale_status', '!=', 'cancelled')
                ->get();
            $totalSalesRevenue      = (float)$salesList->sum('sale_amount');
            $totalSalesPurchaseCost = (float)$salesList->sum(fn($s) => $s->total_purchase_cost);
            $totalSalesGrossProfit  = $totalSalesRevenue - $totalSalesPurchaseCost;
            $totalSalesCommission   = (float)$salesList->sum('broker_commission_amount');
            $totalSalesProfit       = (float)$salesList->sum(fn($s) => $s->net_profit);
            $salesProfitMargin      = $totalSalesRevenue > 0 ? round(($totalSalesProfit / $totalSalesRevenue) * 100, 1) : 0.0;
            $totalSoldUnitsCount    = (int)$salesList->sum(fn($s) => $s->properties->count() ?: 1);
        }

        return view('admin.dashboard', compact(
            'propertyMastersList', 'projectsList', 'filterType', 'selectedPropertyMaster', 'selectedProject', 'filteredPlots',
            'totalFirms', 'activeFirms', 'inactiveFirms', 'totalUsers', 'activeUsers',
            'totalCustomers', 'totalProperties', 'availableProperties', 'bookedProperties',
            'soldProperties', 'rentedProperties', 'totalBookings', 'portfolioVal', 'totalReceivedAmt',
            'totalExpenses', 'netProfit', 'totalPendingAmt', 'recentCustomers',
            'recentPayments', 'totalProjects', 'activeProjects',
            'totalSalesRevenue', 'totalSalesPurchaseCost', 'totalSalesGrossProfit',
            'totalSalesCommission', 'totalSalesProfit', 'salesProfitMargin', 'totalSoldUnitsCount'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    //  Firm Dashboard  (session-based, scoped to firm_id)
    // ─────────────────────────────────────────────────────────────
    private function firmDashboard(Request $request)
    {
        $firmId           = session('firm_id');
        $propertyMasterId = $request->filled('property_master_id') ? (int)$request->property_master_id : null;
        $projectId        = $request->filled('project_id') ? (int)$request->project_id : null;
        $filterType       = $request->get('filter_type');

        if ($propertyMasterId && !$projectId) {
            $filterType = 'property';
        } elseif ($projectId && !$propertyMasterId) {
            $filterType = 'project';
        } elseif (!$propertyMasterId && !$projectId) {
            $filterType = 'all';
        }

        // Dropdown lists scoped to firm
        $propertyMastersList = PropertyMaster::where('firm_id', $firmId)->withCount('plots')->orderBy('property_name')->get();
        $projectsList        = Project::where('firm_id', $firmId)->withCount('properties')->orderBy('project_name')->get();

        $selectedPropertyMaster = $propertyMasterId ? PropertyMaster::where('firm_id', $firmId)->with(['seller', 'broker'])->find($propertyMasterId) : null;
        $selectedProject        = $projectId ? Project::where('firm_id', $firmId)->with(['propertyMaster'])->find($projectId) : null;

        // ── Customers ──────────────────────────────────────────────
        $totalCustomers    = Customer::where('firm_id', $firmId)->count();
        $newCustomersMonth = Customer::where('firm_id', $firmId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at',  now()->year)
            ->count();

        $totalProjects  = Project::where('firm_id', $firmId)->count();
        $activeProjects = Project::where('firm_id', $firmId)->where('status', 'active')->count();

        // ── Loans ──────────────────────────────────────────────────
        $totalLoans      = Loan::where('firm_id', $firmId)->count();
        $totalLoanAmount = Loan::where('firm_id', $firmId)->sum('loan_amount') ?: 0;
        $pendingLoanAmt  = Loan::where('firm_id', $firmId)
            ->whereIn('loan_status', ['Active', 'Pending', 'Under Process'])
            ->sum('pending_amount') ?: 0;

        // ── Inventory / Low Stock ──────────────────────────────────
        $materials     = Material::where('firm_id', $firmId)->where('status', 'active')->get();
        $lowStockCount = 0;
        $outStockCount = 0;

        foreach ($materials as $mat) {
            $totalIn  = StockInward::where('material_id',  $mat->id)->sum('quantity');
            $totalOut = StockOutward::where('material_id', $mat->id)->sum('quantity');
            $current  = (float)$mat->opening_stock + (float)$totalIn - (float)$totalOut;

            if ($current <= 0) {
                $outStockCount++;
            } elseif ($mat->minimum_stock > 0 && $current <= $mat->minimum_stock) {
                $lowStockCount++;
            }
        }
        $totalMaterials = $materials->count();

        // ── Overdue Rent ───────────────────────────────────────────
        $overdueRentCount = Rental::where('firm_id', $firmId)
            ->where('rental_status', 'active')
            ->where('payment_status', 'pending')
            ->count();

        $activeRentals = Rental::where('firm_id', $firmId)
            ->where('rental_status', 'active')->count();

        $monthlyRentIncome = RentalPayment::whereHas('rental', fn($q) => $q->where('firm_id', $firmId))
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date',  now()->year)
            ->sum('paid_amount') ?: 0;

        $totalRentalIncome = RentalPayment::whereHas('rental', fn($q) => $q->where('firm_id', $firmId))
            ->sum('paid_amount') ?: 0;

        // ── Case 1: FILTER BY PROPERTY MASTER (Firm) ─────────────
        if ($filterType === 'property' && $selectedPropertyMaster) {
            $filteredPlots = Property::where('firm_id', $firmId)
                ->where('property_master_id', $selectedPropertyMaster->id)
                ->whereNull('project_id')
                ->get();
            $filteredPlots = Property::naturalSort($filteredPlots);

            $plotIds = $filteredPlots->pluck('id')->toArray();
            $totalProperties     = $filteredPlots->count() > 0 ? $filteredPlots->count() : (int)($selectedPropertyMaster->total_units_count ?? 1);
            $availableProperties = $filteredPlots->where('status', 'available')->count();
            $bookedProperties    = $filteredPlots->where('status', 'booked')->count();
            $soldProperties      = $filteredPlots->where('status', 'sold')->count();
            $rentedProperties    = $filteredPlots->where('status', 'rented')->count();
            $totalBookings       = $bookedProperties;
            $portfolioVal        = (float)$filteredPlots->sum('price');
            if ($portfolioVal <= 0) {
                $portfolioVal = (float)($selectedPropertyMaster->purchase_price ?? 0);
            }

            $salesList = PropertySale::where('firm_id', $firmId)
                ->with(['customer', 'broker', 'properties.propertyMaster', 'property.propertyMaster'])
                ->where(function ($q) use ($plotIds) {
                    if (!empty($plotIds)) {
                        $q->whereIn('property_id', $plotIds)
                          ->orWhereHas('properties', fn($sub) => $sub->whereIn('properties.id', $plotIds));
                    }
                })
                ->where('sale_status', '!=', 'cancelled')
                ->get();

            $totalSalesRevenue      = (float)$salesList->sum('sale_amount');
            $totalSalesPurchaseCost = (float)$salesList->sum(fn($s) => $s->total_purchase_cost);
            if ($totalSalesPurchaseCost <= 0 && $soldProperties > 0 && (float)($selectedPropertyMaster->purchase_price ?? 0) > 0) {
                $totalUnits = max(1, $totalProperties);
                $totalSalesPurchaseCost = round(((float)$selectedPropertyMaster->purchase_price / $totalUnits) * $soldProperties, 2);
            }
            $totalSalesGrossProfit  = $totalSalesRevenue - $totalSalesPurchaseCost;
            $totalSalesCommission   = (float)$salesList->sum('broker_commission_amount');
            $totalSalesProfit       = (float)$salesList->sum(fn($s) => $s->net_profit);
            $salesProfitMargin      = $totalSalesRevenue > 0 ? round(($totalSalesProfit / $totalSalesRevenue) * 100, 1) : 0.0;
            $totalSoldUnitsCount    = (int)$salesList->sum(fn($s) => $s->properties->count() ?: 1);
            $totalSalesAmt          = $totalSalesRevenue;

            $totalExpenses = (float)$selectedPropertyMaster->total_expenses;

            $firmPaymentReceived = !empty($plotIds) ? (Payment::where('firm_id', $firmId)->whereIn('property_id', $plotIds)->sum('payment_amount') ?: 0) : 0;
            $firmBookingReceived = !empty($plotIds) ? (Booking::where('firm_id', $firmId)->where(function($q) use ($plotIds) {
                $q->whereIn('property_id', $plotIds)->orWhereHas('properties', fn($sub) => $sub->whereIn('properties.id', $plotIds));
            })->where('status', '!=', 'cancelled')->sum('booking_amount') ?: 0) : 0;
            $firmSaleInitialPaid = $salesList->sum('booking_amount') ?: 0;
            $firmRentalReceived  = !empty($plotIds) ? (RentalPayment::whereIn('property_id', $plotIds)->sum('paid_amount') ?: 0) : 0;
            $totalReceivedAmt    = $firmPaymentReceived + $firmBookingReceived + $firmSaleInitialPaid + $firmRentalReceived;

            $totalPendingAmt     = ($salesList->sum('remaining_amount') ?: 0)
                                 + (!empty($plotIds) ? (Booking::where('firm_id', $firmId)->where(function($q) use ($plotIds) {
                                     $q->whereIn('property_id', $plotIds)->orWhereHas('properties', fn($sub) => $sub->whereIn('properties.id', $plotIds));
                                 })->where('status', '!=', 'cancelled')->sum('remaining_amount') ?: 0) : 0);

            $recentCustomers = Customer::where('firm_id', $firmId)->latest()->limit(5)->get();
            $recentPayments  = !empty($plotIds) ? Payment::with(['customer', 'property'])->where('firm_id', $firmId)->whereIn('property_id', $plotIds)->latest()->limit(5)->get() : collect();

        // ── Case 2: FILTER BY PROJECT (Firm) ─────────────────────
        } elseif ($filterType === 'project' && $selectedProject) {
            $filteredPlots = Property::where('firm_id', $firmId)
                ->where('project_id', $selectedProject->id)
                ->whereNull('property_master_id')
                ->get();
            $filteredPlots = Property::naturalSort($filteredPlots);

            $plotIds = $filteredPlots->pluck('id')->toArray();
            $totalProperties     = $filteredPlots->count();
            $availableProperties = $filteredPlots->where('status', 'available')->count();
            $bookedProperties    = $filteredPlots->where('status', 'booked')->count();
            $soldProperties      = $filteredPlots->where('status', 'sold')->count();
            $rentedProperties    = $filteredPlots->where('status', 'rented')->count();
            $totalBookings       = $bookedProperties;
            $portfolioVal        = (float)$filteredPlots->sum('price');

            $salesList = PropertySale::where('firm_id', $firmId)
                ->with(['customer', 'broker', 'properties.propertyMaster', 'property.propertyMaster'])
                ->where(function ($q) use ($plotIds) {
                    if (!empty($plotIds)) {
                        $q->whereIn('property_id', $plotIds)
                          ->orWhereHas('properties', fn($sub) => $sub->whereIn('properties.id', $plotIds));
                    }
                })
                ->where('sale_status', '!=', 'cancelled')
                ->get();

            $totalSalesRevenue      = (float)$salesList->sum('sale_amount');
            $totalSalesPurchaseCost = (float)$salesList->sum(fn($s) => $s->total_purchase_cost);
            $totalSalesGrossProfit  = $totalSalesRevenue - $totalSalesPurchaseCost;
            $totalSalesCommission   = (float)$salesList->sum('broker_commission_amount');
            $totalSalesProfit       = (float)$salesList->sum(fn($s) => $s->net_profit);
            $salesProfitMargin      = $totalSalesRevenue > 0 ? round(($totalSalesProfit / $totalSalesRevenue) * 100, 1) : 0.0;
            $totalSoldUnitsCount    = (int)$salesList->sum(fn($s) => $s->properties->count() ?: 1);
            $totalSalesAmt          = $totalSalesRevenue;

            $totalExpenses = (float)$selectedProject->total_expenses;

            $firmPaymentReceived = !empty($plotIds) ? (Payment::where('firm_id', $firmId)->whereIn('property_id', $plotIds)->sum('payment_amount') ?: 0) : 0;
            $firmBookingReceived = !empty($plotIds) ? (Booking::where('firm_id', $firmId)->where(function($q) use ($plotIds) {
                $q->whereIn('property_id', $plotIds)->orWhereHas('properties', fn($sub) => $sub->whereIn('properties.id', $plotIds));
            })->where('status', '!=', 'cancelled')->sum('booking_amount') ?: 0) : 0;
            $firmSaleInitialPaid = $salesList->sum('booking_amount') ?: 0;
            $firmRentalReceived  = !empty($plotIds) ? (RentalPayment::whereIn('property_id', $plotIds)->sum('paid_amount') ?: 0) : 0;
            $totalReceivedAmt    = $firmPaymentReceived + $firmBookingReceived + $firmSaleInitialPaid + $firmRentalReceived;

            $totalPendingAmt     = ($salesList->sum('remaining_amount') ?: 0)
                                 + (!empty($plotIds) ? (Booking::where('firm_id', $firmId)->where(function($q) use ($plotIds) {
                                     $q->whereIn('property_id', $plotIds)->orWhereHas('properties', fn($sub) => $sub->whereIn('properties.id', $plotIds));
                                 })->where('status', '!=', 'cancelled')->sum('remaining_amount') ?: 0) : 0);

            $recentCustomers = Customer::where('firm_id', $firmId)->latest()->limit(5)->get();
            $recentPayments  = !empty($plotIds) ? Payment::with(['customer', 'property'])->where('firm_id', $firmId)->whereIn('property_id', $plotIds)->latest()->limit(5)->get() : collect();

        // ── Case 3: ALL OVERVIEW (Firm) ──────────────────────────
        } else {
            $filteredPlots = null;
            $filterType = 'all';

            $totalProperties     = Property::where('firm_id', $firmId)->where(function($q) {
                $q->whereNotNull('project_id')->orWhereNotNull('property_master_id');
            })->count();
            $availableProperties = Property::where('firm_id', $firmId)->where(function($q) {
                $q->whereNotNull('project_id')->orWhereNotNull('property_master_id');
            })->where('status', 'available')->count();
            $soldProperties      = Property::where('firm_id', $firmId)->where(function($q) {
                $q->whereNotNull('project_id')->orWhereNotNull('property_master_id');
            })->where('status', 'sold')->count();
            $bookedProperties    = Property::where('firm_id', $firmId)->where(function($q) {
                $q->whereNotNull('project_id')->orWhereNotNull('property_master_id');
            })->where('status', 'booked')->count();
            $rentedProperties    = Property::where('firm_id', $firmId)->where(function($q) {
                $q->whereNotNull('project_id')->orWhereNotNull('property_master_id');
            })->where('status', 'rented')->count();
            $portfolioVal        = Property::where('firm_id', $firmId)->sum('price') ?: 0;

            $totalBookings = $bookedProperties;
            $totalSalesAmt = PropertySale::where('firm_id', $firmId)->sum('grand_total') ?: 0;

            $firmPaymentReceived = Payment::where('firm_id', $firmId)->sum('payment_amount') ?: 0;
            $firmBookingReceived = Booking::where('firm_id', $firmId)->sum('booking_amount') ?: 0;
            $firmSaleInitialPaid = PropertySale::where('firm_id', $firmId)->sum('booking_amount') ?: 0;
            $firmRentalReceived  = RentalPayment::whereHas('rental', fn($q) => $q->where('firm_id', $firmId))->sum('paid_amount') ?: 0;
            $totalReceivedAmt    = $firmPaymentReceived + $firmBookingReceived + $firmSaleInitialPaid + $firmRentalReceived;

            $totalPendingAmt     = (PropertySale::where('firm_id', $firmId)->sum('remaining_amount') ?: 0)
                                 + (Booking::where('firm_id', $firmId)->sum('remaining_amount') ?: 0);

            $totalExpenses = Expense::where('firm_id', $firmId)->sum('amount') ?: 0;

            $recentCustomers = Customer::where('firm_id', $firmId)->latest()->limit(5)->get();
            $recentPayments  = Payment::with(['customer', 'property'])
                ->where('firm_id', $firmId)->latest()->limit(5)->get();

            $firmSalesList          = PropertySale::with(['properties.propertyMaster', 'property.propertyMaster'])
                ->where('firm_id', $firmId)
                ->where('sale_status', '!=', 'cancelled')
                ->get();
            $totalSalesRevenue      = (float)$firmSalesList->sum('sale_amount');
            $totalSalesPurchaseCost = (float)$firmSalesList->sum(fn($s) => $s->total_purchase_cost);
            $totalSalesGrossProfit  = $totalSalesRevenue - $totalSalesPurchaseCost;
            $totalSalesCommission   = (float)$firmSalesList->sum('broker_commission_amount');
            $totalSalesProfit       = (float)$firmSalesList->sum(fn($s) => $s->net_profit);
            $salesProfitMargin      = $totalSalesRevenue > 0 ? round(($totalSalesProfit / $totalSalesRevenue) * 100, 1) : 0.0;
            $totalSoldUnitsCount    = (int)$firmSalesList->sum(fn($s) => $s->properties->count() ?: 1);
        }

        return view('admin.firm-dashboard', compact(
            'propertyMastersList', 'projectsList', 'filterType', 'selectedPropertyMaster', 'selectedProject', 'filteredPlots',
            'totalCustomers', 'newCustomersMonth',
            'totalProperties', 'availableProperties', 'soldProperties',
            'bookedProperties', 'rentedProperties', 'portfolioVal',
            'totalBookings', 'totalSalesAmt',
            'totalReceivedAmt', 'totalPendingAmt',
            'activeRentals', 'monthlyRentIncome', 'totalRentalIncome', 'overdueRentCount',
            'totalExpenses',
            'totalLoans', 'totalLoanAmount', 'pendingLoanAmt',
            'totalMaterials', 'lowStockCount', 'outStockCount',
            'recentCustomers', 'recentPayments', 'totalProjects', 'activeProjects',
            'totalSalesRevenue', 'totalSalesPurchaseCost', 'totalSalesGrossProfit',
            'totalSalesCommission', 'totalSalesProfit', 'salesProfitMargin', 'totalSoldUnitsCount'
        ));
    }
}
