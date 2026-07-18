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
        $totalProperties = Property::count();
        $availableProperties = Property::where('status', 'available')->count();
        $soldProperties = Property::where('status', 'sold')->count();
        $rentedProperties = Property::where('status', 'rented')->count();
        $totalBookings = PropertySale::count();
        $totalReceivedAmt = Payment::sum('payment_amount') ?: 0;
        $totalExpenses = Expense::sum('amount') ?: 0;
        $netProfit = $totalReceivedAmt - $totalExpenses;
        
        // Missing Sections Data
        $totalPendingAmt = PropertySale::sum('remaining_amount') ?: 0;
        $recentCustomers = Customer::latest()->limit(5)->get();
        $recentPayments = Payment::with(['customer', 'property'])->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalFirms', 'activeFirms', 'inactiveFirms', 'totalUsers', 'activeUsers',
            'totalCustomers', 'totalProperties', 'availableProperties', 'soldProperties',
            'rentedProperties', 'totalBookings', 'totalReceivedAmt', 'totalExpenses', 'netProfit',
            'totalPendingAmt', 'recentCustomers', 'recentPayments'
        ));
    }
}
