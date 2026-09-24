<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Property;
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
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        Property::syncAllStatuses();

        // ══════════════════════════════════════════════════════════
        //  FIRM SESSION DASHBOARD
        // ══════════════════════════════════════════════════════════
        if (session('login_type') === 'firm' && session('firm_id')) {
            return $this->firmDashboard();
        }

        // ══════════════════════════════════════════════════════════
        //  ADMIN DASHBOARD
        // ══════════════════════════════════════════════════════════
        return $this->adminDashboard();
    }

    // ─────────────────────────────────────────────────────────────
    //  Admin Dashboard
    // ─────────────────────────────────────────────────────────────
    private function adminDashboard()
    {
        $totalFirms          = \App\Models\Firm::count();
        $activeFirms         = \App\Models\Firm::where('status', 'active')->count();
        $inactiveFirms       = \App\Models\Firm::where('status', 'inactive')->count();
        $totalUsers          = \App\Models\User::count();
        $activeUsers         = \App\Models\User::where('status', 'active')->count();
        $totalCustomers      = Customer::count();
        $totalProperties     = Property::whereNotNull('project_id')->whereNull('property_master_id')->count();
        $availableProperties = Property::whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'available')->count();
        $bookedProperties    = Property::whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'booked')->count();
        $soldProperties      = Property::whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'sold')->count();
        $rentedProperties    = Property::whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'rented')->count();
        $totalBookings       = Property::whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'booked')->count();
        
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
        $totalProjects       = \App\Models\Project::count();
        $activeProjects      = \App\Models\Project::where('status', 'active')->count();

        // Property Sales & Profit Analysis (Purchase Cost vs Selling Price)
        $salesList              = PropertySale::with(['properties.propertyMaster', 'property.propertyMaster'])
            ->where('sale_status', '!=', 'cancelled')
            ->get();
        $totalSalesRevenue      = (float)$salesList->sum('sale_amount');
        $totalSalesPurchaseCost = (float)$salesList->sum(fn($s) => $s->total_purchase_cost);
        $totalSalesGrossProfit  = $totalSalesRevenue - $totalSalesPurchaseCost;
        $totalSalesCommission   = (float)$salesList->sum('broker_commission');
        $totalSalesProfit       = (float)$salesList->sum(fn($s) => $s->net_profit);
        $salesProfitMargin      = $totalSalesRevenue > 0 ? round(($totalSalesProfit / $totalSalesRevenue) * 100, 1) : 0.0;
        $totalSoldUnitsCount    = (int)$salesList->sum(fn($s) => $s->properties->count() ?: 1);

        return view('admin.dashboard', compact(
            'totalFirms', 'activeFirms', 'inactiveFirms', 'totalUsers', 'activeUsers',
            'totalCustomers', 'totalProperties', 'availableProperties', 'bookedProperties',
            'soldProperties', 'rentedProperties', 'totalBookings', 'totalReceivedAmt',
            'totalExpenses', 'netProfit', 'totalPendingAmt', 'recentCustomers',
            'recentPayments', 'totalProjects', 'activeProjects',
            'totalSalesRevenue', 'totalSalesPurchaseCost', 'totalSalesGrossProfit',
            'totalSalesCommission', 'totalSalesProfit', 'salesProfitMargin', 'totalSoldUnitsCount'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    //  Firm Dashboard  (session-based, no Auth guard needed)
    // ─────────────────────────────────────────────────────────────
    private function firmDashboard()
    {
        $firmId = session('firm_id');

        // ── Customers ──────────────────────────────────────────────
        $totalCustomers    = Customer::where('firm_id', $firmId)->count();
        $newCustomersMonth = Customer::where('firm_id', $firmId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at',  now()->year)
            ->count();

        // ── Properties (Project Plots Only) ────────────────────────
        $totalProperties     = Property::where('firm_id', $firmId)->whereNotNull('project_id')->whereNull('property_master_id')->count();
        $availableProperties = Property::where('firm_id', $firmId)->whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'available')->count();
        $soldProperties      = Property::where('firm_id', $firmId)->whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'sold')->count();
        $bookedProperties    = Property::where('firm_id', $firmId)->whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'booked')->count();
        $rentedProperties    = Property::where('firm_id', $firmId)->whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'rented')->count();
        $portfolioVal        = Property::where('firm_id', $firmId)->whereNotNull('project_id')->whereNull('property_master_id')->sum('price') ?: 0;

        // ── Sales / Bookings ───────────────────────────────────────
        $totalBookings = Property::where('firm_id', $firmId)->whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'booked')->count();
        $totalSalesAmt = PropertySale::where('firm_id', $firmId)->sum('grand_total') ?: 0;

        // ── Payments & Revenue ─────────────────────────────────────
        $firmPaymentReceived = Payment::where('firm_id', $firmId)->sum('payment_amount') ?: 0;
        $firmBookingReceived = Booking::where('firm_id', $firmId)->sum('booking_amount') ?: 0;
        $firmSaleInitialPaid = PropertySale::where('firm_id', $firmId)->sum('booking_amount') ?: 0;
        $firmRentalReceived  = RentalPayment::whereHas('rental', fn($q) => $q->where('firm_id', $firmId))->sum('paid_amount') ?: 0;
        $totalReceivedAmt    = $firmPaymentReceived + $firmBookingReceived + $firmSaleInitialPaid + $firmRentalReceived;

        $totalPendingAmt     = (PropertySale::where('firm_id', $firmId)->sum('remaining_amount') ?: 0)
                             + (Booking::where('firm_id', $firmId)->sum('remaining_amount') ?: 0);

        // ── Rental Income ──────────────────────────────────────────
        $activeRentals = Rental::where('firm_id', $firmId)
            ->where('rental_status', 'active')->count();

        $monthlyRentIncome = RentalPayment::whereHas('rental', fn($q) => $q->where('firm_id', $firmId))
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date',  now()->year)
            ->sum('paid_amount') ?: 0;

        $totalRentalIncome = RentalPayment::whereHas('rental', fn($q) => $q->where('firm_id', $firmId))
            ->sum('paid_amount') ?: 0;

        // ── Expenses ───────────────────────────────────────────────
        $totalExpenses = Expense::where('firm_id', $firmId)->sum('amount') ?: 0;

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

        // ── Recent Records ─────────────────────────────────────────
        $recentCustomers = Customer::where('firm_id', $firmId)->latest()->limit(5)->get();
        $recentPayments  = Payment::with(['customer', 'property'])
            ->where('firm_id', $firmId)->latest()->limit(5)->get();

        $totalProjects  = \App\Models\Project::where('firm_id', $firmId)->count();
        $activeProjects = \App\Models\Project::where('firm_id', $firmId)->where('status', 'active')->count();

        // ── Property Sales & Profit Analysis (Firm Scoped) ───────────
        $firmSalesList          = PropertySale::with(['properties.propertyMaster', 'property.propertyMaster'])
            ->where('firm_id', $firmId)
            ->where('sale_status', '!=', 'cancelled')
            ->get();
        $totalSalesRevenue      = (float)$firmSalesList->sum('sale_amount');
        $totalSalesPurchaseCost = (float)$firmSalesList->sum(fn($s) => $s->total_purchase_cost);
        $totalSalesGrossProfit  = $totalSalesRevenue - $totalSalesPurchaseCost;
        $totalSalesCommission   = (float)$firmSalesList->sum('broker_commission');
        $totalSalesProfit       = (float)$firmSalesList->sum(fn($s) => $s->net_profit);
        $salesProfitMargin      = $totalSalesRevenue > 0 ? round(($totalSalesProfit / $totalSalesRevenue) * 100, 1) : 0.0;
        $totalSoldUnitsCount    = (int)$firmSalesList->sum(fn($s) => $s->properties->count() ?: 1);

        return view('admin.firm-dashboard', compact(
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
