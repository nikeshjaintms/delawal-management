<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Property;
use App\Models\PropertySale;
use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Super Admin Dashboard Calculations
        $totalFirms = \App\Models\Firm::count();
        $activeFirms = \App\Models\Firm::where('status', 'active')->count();
        $inactiveFirms = \App\Models\Firm::where('status', 'inactive')->count();
        $totalUsers = \App\Models\User::count();
        $activeUsers = \App\Models\User::where('status', 'active')->count();
        $totalCustomers = Customer::count();
        $totalProperties = Property::whereNotNull('project_id')->whereNull('property_master_id')->count();
        $availableProperties = Property::whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'available')->count();
        $bookedProperties = Property::whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'booked')->count();
        $soldProperties = Property::whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'sold')->count();
        $rentedProperties = Property::whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'rented')->count();
        $totalBookings = Property::whereNotNull('project_id')->whereNull('property_master_id')->where('status', 'booked')->count();
        $totalReceivedAmt = Payment::sum('payment_amount') ?: 0;
        $totalExpenses = Expense::sum('amount') ?: 0;
        $netProfit = $totalReceivedAmt - $totalExpenses;
        
        // Missing Sections Data
        $totalPendingAmt = PropertySale::sum('remaining_amount') ?: 0;
        $recentCustomers = Customer::latest()->limit(5)->get();
        $recentPayments = Payment::with(['customer', 'property'])->latest()->limit(5)->get();
        $totalProjects = \App\Models\Project::count();
        $activeProjects = \App\Models\Project::where('status', 'active')->count();

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
}
