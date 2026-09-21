<?php

namespace App\Http\Controllers\Agriculture;

use App\Http\Controllers\Controller;
use App\Models\AgriFarm;
use App\Models\AgriLabour;
use App\Models\AgriLabourPayment;
use App\Models\AgriExpense;
use App\Models\AgriIncome;
use App\Models\Firm;
use App\Models\Project;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Contractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgriReportController extends Controller
{
    private function getFirmsAndDropdowns()
    {
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $farmQuery = AgriFarm::where('status', 'Active')->orderBy('farm_name');
        $projQuery = Project::where('status', 'active')->orderBy('project_name');
        $labourQuery = AgriLabour::orderBy('name');

        if ($firmId && (!$user || !$user->isAdmin())) {
            $farmQuery->where('firm_id', $firmId);
            $projQuery->where('firm_id', $firmId);
            $labourQuery->where('firm_id', $firmId);
        }

        return [
            'firms'        => $firms,
            'farms'        => $farmQuery->get(),
            'projects'     => $projQuery->get(),
            'labours'      => $labourQuery->get(),
            'categories'   => AgriExpense::CATEGORIES,
            'incomeTypes'  => AgriIncome::INCOME_TYPES,
            'labourTypes'  => AgriLabourController::LABOUR_TYPES,
        ];
    }

    public function index(Request $request)
    {
        $reportType = $request->input('report_type', 'profit_loss');
        $dropdowns = $this->getFirmsAndDropdowns();

        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        // Date range default to current month or financial year if not provided
        $fromDate = $request->input('from_date');
        $toDate   = $request->input('to_date');

        // Fetch data depending on report type
        $reportData = [];

        if ($reportType === 'income') {
            $query = AgriIncome::with(['farm', 'project', 'property', 'customer', 'paymentMode', 'firm']);
            if (!$isAdmin) $query->forFirms([$firmId]);
            elseif ($request->filled('firm_id')) $query->forFirms([$request->firm_id]);

            if ($request->filled('farm_id')) $query->where('farm_id', $request->farm_id);
            if ($request->filled('income_type')) $query->where('income_type', $request->income_type);
            if ($fromDate) $query->whereDate('income_date', '>=', $fromDate);
            if ($toDate) $query->whereDate('income_date', '<=', $toDate);

            $incomes = $query->orderBy('income_date', 'desc')->get();
            $reportData = [
                'incomes'       => $incomes,
                'totalAmount'   => $incomes->sum('total_amount'),
                'totalReceived' => $incomes->sum('payment_received'),
                'totalPending'  => $incomes->sum('pending_amount'),
            ];
        } elseif ($reportType === 'expense') {
            $query = AgriExpense::with(['farm', 'project', 'property', 'vendor', 'contractor', 'labour', 'paymentMode', 'firm']);
            if (!$isAdmin) $query->forFirms([$firmId]);
            elseif ($request->filled('firm_id')) $query->forFirms([$request->firm_id]);

            if ($request->filled('farm_id')) $query->where('farm_id', $request->farm_id);
            if ($request->filled('category')) $query->where('category', $request->category);
            if ($fromDate) $query->whereDate('expense_date', '>=', $fromDate);
            if ($toDate) $query->whereDate('expense_date', '<=', $toDate);

            $expenses = $query->orderBy('expense_date', 'desc')->get();
            $reportData = [
                'expenses'    => $expenses,
                'totalAmount' => $expenses->sum('amount'),
            ];
        } elseif ($reportType === 'labour') {
            $query = AgriLabour::with(['farm', 'firm', 'payments.paymentMode']);
            if (!$isAdmin) $query->forFirms([$firmId]);
            elseif ($request->filled('firm_id')) $query->forFirms([$request->firm_id]);

            if ($request->filled('farm_id')) $query->where('farm_id', $request->farm_id);
            if ($request->filled('labour_type')) $query->where('labour_type', $request->labour_type);

            $labours = $query->orderBy('name')->get();

            $paymentsQuery = AgriLabourPayment::with(['labour', 'farm']);
            if (!$isAdmin) $paymentsQuery->forFirms([$firmId]);
            elseif ($request->filled('firm_id')) $paymentsQuery->forFirms([$request->firm_id]);
            if ($request->filled('farm_id')) $paymentsQuery->where('farm_id', $request->farm_id);
            if ($fromDate) $paymentsQuery->whereDate('payment_date', '>=', $fromDate);
            if ($toDate) $paymentsQuery->whereDate('payment_date', '<=', $toDate);
            $payments = $paymentsQuery->orderBy('payment_date', 'desc')->get();

            $reportData = [
                'labours'              => $labours,
                'payments'             => $payments,
                'totalEarned'          => $labours->sum('total_earned'),
                'totalAdvance'         => $labours->sum('total_advance'),
                'totalPaid'            => $labours->sum('total_paid'),
                'totalAdvanceBalance'  => $labours->sum('advance_balance'),
                'totalPending'         => $labours->sum('pending_amount'),
            ];
        } elseif ($reportType === 'farm') {
            $query = AgriFarm::with(['property', 'project', 'firm', 'labours', 'expenses', 'incomes']);
            if (!$isAdmin) $query->forFirms([$firmId]);
            elseif ($request->filled('firm_id')) $query->forFirms([$request->firm_id]);

            if ($request->filled('farm_id')) $query->where('id', $request->farm_id);
            if ($request->filled('project_id')) $query->where('project_id', $request->project_id);

            $farms = $query->orderBy('farm_name')->get();
            $farmSummaries = [];
            $totalInc = 0; $totalExp = 0; $totalArea = 0;

            foreach ($farms as $f) {
                $incQ = $f->incomes();
                $expQ = $f->expenses();
                if ($fromDate) {
                    $incQ->whereDate('income_date', '>=', $fromDate);
                    $expQ->whereDate('expense_date', '>=', $fromDate);
                }
                if ($toDate) {
                    $incQ->whereDate('income_date', '<=', $toDate);
                    $expQ->whereDate('expense_date', '<=', $toDate);
                }

                $fInc = (float)$incQ->sum('total_amount');
                $fExp = (float)$expQ->sum('amount');
                $fProfit = round($fInc - $fExp, 2);

                $totalInc += $fInc;
                $totalExp += $fExp;
                $totalArea += (float)$f->land_area;

                $farmSummaries[] = [
                    'farm'         => $f,
                    'land_area'    => $f->land_area . ' ' . $f->area_unit,
                    'crop'         => $f->crop_activity ?: '—',
                    'total_income' => $fInc,
                    'total_expense'=> $fExp,
                    'net_profit'   => $fProfit,
                ];
            }

            $reportData = [
                'farmSummaries' => $farmSummaries,
                'totalArea'     => $totalArea,
                'totalIncome'   => $totalInc,
                'totalExpense'  => $totalExp,
                'netProfit'     => round($totalInc - $totalExp, 2),
            ];
        } else {
            // Profit / Loss Report
            $incQuery = AgriIncome::query();
            $expQuery = AgriExpense::query();

            if (!$isAdmin) {
                $incQuery->forFirms([$firmId]);
                $expQuery->forFirms([$firmId]);
            } elseif ($request->filled('firm_id')) {
                $incQuery->forFirms([$request->firm_id]);
                $expQuery->forFirms([$request->firm_id]);
            }

            if ($request->filled('farm_id')) {
                $incQuery->where('farm_id', $request->farm_id);
                $expQuery->where('farm_id', $request->farm_id);
            }

            if ($fromDate) {
                $incQuery->whereDate('income_date', '>=', $fromDate);
                $expQuery->whereDate('expense_date', '>=', $fromDate);
            }
            if ($toDate) {
                $incQuery->whereDate('income_date', '<=', $toDate);
                $expQuery->whereDate('expense_date', '<=', $toDate);
            }

            $totalIncome   = (float) (clone $incQuery)->sum('total_amount');
            $totalExpense  = (float) (clone $expQuery)->sum('amount');
            $labourExpense = (float) (clone $expQuery)->where('category', 'Labour')->sum('amount');
            $netProfit     = round($totalIncome - $totalExpense, 2);

            // Category-wise Expense breakdown
            $categoryBreakdown = (clone $expQuery)
                ->selectRaw('category, SUM(amount) as total')
                ->groupBy('category')
                ->orderByDesc('total')
                ->pluck('total', 'category')
                ->toArray();

            // Income type breakdown
            $incomeBreakdown = (clone $incQuery)
                ->selectRaw('income_type, SUM(total_amount) as total')
                ->groupBy('income_type')
                ->orderByDesc('total')
                ->pluck('total', 'income_type')
                ->toArray();

            $reportData = [
                'totalIncome'       => $totalIncome,
                'totalExpense'      => $totalExpense,
                'labourExpense'     => $labourExpense,
                'netProfit'         => $netProfit,
                'categoryBreakdown' => $categoryBreakdown,
                'incomeBreakdown'   => $incomeBreakdown,
            ];
        }

        return view('admin.agriculture.reports.index', array_merge(
            $dropdowns,
            compact('reportType', 'reportData', 'fromDate', 'toDate')
        ));
    }

    public function printReport(Request $request)
    {
        $reportType = $request->input('report_type', 'profit_loss');
        $fromDate   = $request->input('from_date');
        $toDate     = $request->input('to_date');

        // Reuse report generation logic for print view
        $indexResponse = $this->index($request);
        $data = $indexResponse->getData();

        return view('admin.agriculture.reports.print', (array)$data);
    }
}
