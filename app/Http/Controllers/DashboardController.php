<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Property;
use App\Models\PropertySale;
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
        $totalProperties     = Property::count();
        $availableProperties = Property::where('status', 'available')->count();
        $soldProperties      = Property::where('status', 'sold')->count();
        $rentedProperties    = Property::where('status', 'rented')->count();
        $totalBookings       = PropertySale::count();
        $totalReceivedAmt    = Payment::sum('payment_amount') ?: 0;
        $totalExpenses       = Expense::sum('amount') ?: 0;
        $netProfit           = $totalReceivedAmt - $totalExpenses;
        $totalPendingAmt     = PropertySale::sum('remaining_amount') ?: 0;
        $recentCustomers     = Customer::latest()->limit(5)->get();
        $recentPayments      = Payment::with(['customer', 'property'])->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalFirms', 'activeFirms', 'inactiveFirms', 'totalUsers', 'activeUsers',
            'totalCustomers', 'totalProperties', 'availableProperties', 'soldProperties',
            'rentedProperties', 'totalBookings', 'totalReceivedAmt', 'totalExpenses', 'netProfit',
            'totalPendingAmt', 'recentCustomers', 'recentPayments'
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

        // ── Properties ─────────────────────────────────────────────
        $totalProperties     = Property::where('firm_id', $firmId)->count();
        $availableProperties = Property::where('firm_id', $firmId)->where('status', 'available')->count();
        $soldProperties      = Property::where('firm_id', $firmId)->where('status', 'sold')->count();
        $bookedProperties    = Property::where('firm_id', $firmId)->where('status', 'booked')->count();
        $rentedProperties    = Property::where('firm_id', $firmId)->where('status', 'rented')->count();
        $portfolioVal        = Property::where('firm_id', $firmId)->sum('price') ?: 0;

        // ── Sales / Bookings ───────────────────────────────────────
        $totalBookings = PropertySale::where('firm_id', $firmId)->count();
        $totalSalesAmt = PropertySale::where('firm_id', $firmId)->sum('grand_total') ?: 0;

        // ── Payments ───────────────────────────────────────────────
        $totalReceivedAmt = Payment::where('firm_id', $firmId)->sum('payment_amount') ?: 0;
        $totalPendingAmt  = PropertySale::where('firm_id', $firmId)->sum('remaining_amount') ?: 0;

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
            'recentCustomers', 'recentPayments'
        ));
    }
}
