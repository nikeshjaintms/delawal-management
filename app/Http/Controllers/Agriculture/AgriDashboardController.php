<?php

namespace App\Http\Controllers\Agriculture;

use App\Http\Controllers\Controller;
use App\Models\AgriFarm;
use App\Models\AgriLabour;
use App\Models\AgriExpense;
use App\Models\AgriIncome;
use App\Models\Firm;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgriDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        // Base queries with firm scope
        $farmQuery    = AgriFarm::with(['property', 'project', 'firm']);
        $labourQuery  = AgriLabour::with(['farm', 'firm']);
        $expenseQuery = AgriExpense::with(['farm', 'labour', 'vendor', 'contractor', 'firm']);
        $incomeQuery  = AgriIncome::with(['farm', 'customer', 'firm']);

        if (!$isAdmin) {
            $farmQuery->forFirms([$firmId]);
            $labourQuery->forFirms([$firmId]);
            $expenseQuery->forFirms([$firmId]);
            $incomeQuery->forFirms([$firmId]);
        } elseif ($request->filled('firm_id')) {
            $farmQuery->forFirms([$request->firm_id]);
            $labourQuery->forFirms([$request->firm_id]);
            $expenseQuery->forFirms([$request->firm_id]);
            $incomeQuery->forFirms([$request->firm_id]);
        }

        // Filters
        if ($request->filled('farm_id')) {
            $expenseQuery->where('farm_id', $request->farm_id);
            $incomeQuery->where('farm_id', $request->farm_id);
            $labourQuery->where('farm_id', $request->farm_id);
        }

        if ($request->filled('project_id')) {
            $farmQuery->where('project_id', $request->project_id);
            $expenseQuery->where('project_id', $request->project_id);
            $incomeQuery->where('project_id', $request->project_id);
        }

        if ($request->filled('expense_category')) {
            $expenseQuery->where('category', $request->expense_category);
        }

        if ($request->filled('labour_type')) {
            $labourQuery->where('labour_type', $request->labour_type);
        }

        if ($request->filled('from_date')) {
            $expenseQuery->whereDate('expense_date', '>=', $request->from_date);
            $incomeQuery->whereDate('income_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $expenseQuery->whereDate('expense_date', '<=', $request->to_date);
            $incomeQuery->whereDate('income_date', '<=', $request->to_date);
        }

        // Summary Calculations
        $totalFarms         = (clone $farmQuery)->count();
        $totalLandArea      = (clone $farmQuery)->sum('land_area');
        $activeFarmsCount   = (clone $farmQuery)->where('status', 'Active')->count();

        $totalIncome        = (float) (clone $incomeQuery)->sum('total_amount');
        $incomeReceived     = (float) (clone $incomeQuery)->sum('payment_received');
        $incomePending      = (float) (clone $incomeQuery)->sum('pending_amount');

        $totalExpense       = (float) (clone $expenseQuery)->sum('amount');
        $labourExpense      = (float) (clone $expenseQuery)->where('category', 'Labour')->sum('amount');

        $totalLabours       = (clone $labourQuery)->count();
        $activeLabours      = (clone $labourQuery)->where('status', 'Active')->count();
        $totalAdvanceGiven  = (float) (clone $labourQuery)->sum('total_advance');
        $totalAdvanceBalance= (float) (clone $labourQuery)->sum('advance_balance');
        $pendingLabourPay   = (float) (clone $labourQuery)->sum('pending_amount');

        // Net Agriculture Profit = Total Income - Total Expense
        $netProfit = round($totalIncome - $totalExpense, 2);

        // Recent Activity
        $recentIncomes = (clone $incomeQuery)->latest('income_date')->latest('id')->take(5)->get();
        $recentExpenses = (clone $expenseQuery)->latest('expense_date')->latest('id')->take(5)->get();
        $farmsList = (clone $farmQuery)->withCount(['labours', 'expenses', 'incomes'])->get();

        // Monthly comparison (Last 6 months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $monthStr = $m->format('Y-m');
            $monthLabel = $m->format('M Y');

            $mIncome = (float) (clone $incomeQuery)
                ->whereYear('income_date', $m->year)
                ->whereMonth('income_date', $m->month)
                ->sum('total_amount');

            $mExpense = (float) (clone $expenseQuery)
                ->whereYear('expense_date', $m->year)
                ->whereMonth('expense_date', $m->month)
                ->sum('amount');

            $monthlyTrends[] = [
                'month'   => $monthLabel,
                'income'  => $mIncome,
                'expense' => $mExpense,
                'profit'  => round($mIncome - $mExpense, 2),
            ];
        }

        // Dropdowns for filtering
        $allFarms = AgriFarm::where('status', 'Active')->orderBy('farm_name')->get();
        $allProjects = Project::where('status', 'active')->orderBy('project_name')->get();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.agriculture.dashboard', compact(
            'totalFarms',
            'totalLandArea',
            'activeFarmsCount',
            'totalIncome',
            'incomeReceived',
            'incomePending',
            'totalExpense',
            'labourExpense',
            'totalLabours',
            'activeLabours',
            'totalAdvanceGiven',
            'totalAdvanceBalance',
            'pendingLabourPay',
            'netProfit',
            'recentIncomes',
            'recentExpenses',
            'farmsList',
            'monthlyTrends',
            'allFarms',
            'allProjects',
            'firms'
        ));
    }
}
