<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Property;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseReportController extends Controller
{
    const PAYMENT_MODES     = ['Cash', 'Bank Transfer', 'UPI', 'Cheque', 'Other'];
    const APPROVAL_STATUSES = ['Pending', 'Approved', 'Rejected'];

    // ------------------------------------------------------------------
    // Build the filtered expense collection (used by all 3 actions)
    // ------------------------------------------------------------------
    private function getReportData(Request $request)
    {
        $query = Expense::with(['firm', 'property', 'expenseCategory']);

        if (!Auth::user()->isAdmin()) {
            $query->where('firm_id', Auth::user()->firm_id);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('from_date')) {
            $query->where('expense_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('expense_date', '<=', $request->to_date);
        }
        if ($request->filled('filter_property')) {
            $query->where('property_id', $request->filter_property);
        }
        if ($request->filled('filter_category')) {
            $query->where('expense_category_id', $request->filter_category);
        }
        if ($request->filled('filter_vendor')) {
            $vendorVal = $request->filter_vendor;
            $query->where(function($q) use ($vendorVal) {
                $q->where('vendor_id', $vendorVal)
                  ->orWhere('paid_to', 'like', '%' . $vendorVal . '%');
            });
        }
        if ($request->filled('filter_mode')) {
            $query->where('payment_mode', $request->filter_mode);
        }
        if ($request->filled('filter_status')) {
            $query->where('approval_status', $request->filter_status);
        }

        return $query->orderBy('expense_date', 'desc')->get();
    }

    // ------------------------------------------------------------------
    // Build summary arrays from the collection
    // ------------------------------------------------------------------
    private function buildSummaries($expenses): array
    {
        // Monthly summary
        $monthly = [];
        foreach ($expenses as $e) {
            $key = \Carbon\Carbon::parse($e->expense_date)->format('M Y');
            $monthly[$key] = ($monthly[$key] ?? 0) + $e->amount;
        }
        arsort($monthly);

        // Category-wise summary
        $byCategory = [];
        foreach ($expenses as $e) {
            $cat = $e->expense_category ?: 'Uncategorised';
            $byCategory[$cat] = ($byCategory[$cat] ?? 0) + $e->amount;
        }
        arsort($byCategory);

        // Property-wise summary
        $byProperty = [];
        foreach ($expenses as $e) {
            $prop = $e->property?->property_name ?? 'General';
            $byProperty[$prop] = ($byProperty[$prop] ?? 0) + $e->amount;
        }
        arsort($byProperty);

        return compact('monthly', 'byCategory', 'byProperty');
    }

    // ------------------------------------------------------------------
    // INDEX
    // ------------------------------------------------------------------
    public function index(Request $request)
    {
        $expenses = $this->getReportData($request);

        $totalAmount = $expenses->sum('amount');
        $paidAmount = $expenses->where('approval_status', 'Approved')->sum('amount');
        $pendingAmount = $expenses->where('approval_status', 'Pending')->sum('amount');

        $todayQuery = Expense::whereDate('expense_date', today());
        if (!Auth::user()->isAdmin()) {
            $todayQuery->where('firm_id', Auth::user()->firm_id);
        } elseif ($request->filled('firm_id')) {
            $todayQuery->where('firm_id', $request->firm_id);
        }
        $todayAmount = $todayQuery->sum('amount');

        $summaries = $this->buildSummaries($expenses);

        $firms = \App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get();

        $propQuery = Property::orderBy('property_name');
        $catQuery  = ExpenseCategory::where('status', 'active')->orderBy('name');
        $venQuery  = \App\Models\Vendor::orderBy('name');

        if (!Auth::user()->isAdmin()) {
            $propQuery->where('firm_id', Auth::user()->firm_id);
            $catQuery->where('firm_id', Auth::user()->firm_id);
            $venQuery->where('firm_id', Auth::user()->firm_id);
        } elseif ($request->filled('firm_id')) {
            $propQuery->where('firm_id', $request->firm_id);
            $catQuery->where('firm_id', $request->firm_id);
            $venQuery->where('firm_id', $request->firm_id);
        }

        $properties = $propQuery->get();
        $categories = $catQuery->get();
        $vendors    = $venQuery->get();

        return view('admin.expense-report.index', array_merge(
            compact('expenses', 'firms', 'properties', 'categories', 'vendors', 'totalAmount', 'paidAmount', 'pendingAmount', 'todayAmount'),
            $summaries
        ));
    }

    // ------------------------------------------------------------------
    // PDF EXPORT  (opens print-ready view)
    // ------------------------------------------------------------------
    public function exportPdf(Request $request)
    {
        $firmId = Auth::user()->firm_id;
        $expenses = $this->getReportData($request);
        
        $totalAmount = $expenses->sum('amount');
        $paidAmount = $expenses->where('approval_status', 'Approved')->sum('amount');
        $pendingAmount = $expenses->where('approval_status', 'Pending')->sum('amount');

        $todayAmount = Expense::where('firm_id', $firmId)
            ->whereDate('expense_date', today())
            ->sum('amount');

        $summaries = $this->buildSummaries($expenses);

        $properties = Property::where('firm_id', $firmId)
            ->orderBy('property_name')->get();

        $categories = ExpenseCategory::where('firm_id', $firmId)
            ->orderBy('name')->get();

        $vendors = \App\Models\Vendor::where('firm_id', $firmId)->orderBy('name')->get();

        return view('admin.expense-report.pdf', array_merge(
            compact('expenses', 'properties', 'categories', 'vendors', 'totalAmount', 'paidAmount', 'pendingAmount', 'todayAmount'),
            $summaries
        ));
    }

    // ------------------------------------------------------------------
    // EXCEL EXPORT  (CSV stream)
    // ------------------------------------------------------------------
    public function exportExcel(Request $request)
    {
        $expenses = $this->getReportData($request);
        $filename = 'expense-report-' . date('Y-m-d') . '.csv';

        $totalAmount = $expenses->sum('amount');
        $paidAmount = $expenses->where('approval_status', 'Approved')->sum('amount');
        $pendingAmount = $expenses->where('approval_status', 'Pending')->sum('amount');

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($expenses, $request, $totalAmount, $paidAmount, $pendingAmount) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Report Header
            fputcsv($handle, ['Delawala Properties & Management - Expense Report']);
            fputcsv($handle, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
                fputcsv($handle, ['Date Range', $fromDisplay . ' to ' . $toDisplay]);
            }
            fputcsv($handle, []); // Blank row

            // Summary Section
            fputcsv($handle, ['SUMMARY']);
            fputcsv($handle, ['Total Expenses', number_format($totalAmount, 2)]);
            fputcsv($handle, ['Paid / Approved', number_format($paidAmount, 2)]);
            fputcsv($handle, ['Pending', number_format($pendingAmount, 2)]);
            fputcsv($handle, ['Total Records', $expenses->count()]);
            fputcsv($handle, []); // Blank row

            // Data Header
            fputcsv($handle, [
                'Expense Date', 'Property', 'Category', 'Expense Title',
                'Amount (₹)', 'Payment Mode', 'Paid To', 'Bill No', 'Approval Status',
            ]);

            // Data Rows
            foreach ($expenses as $e) {
                fputcsv($handle, [
                    \Carbon\Carbon::parse($e->expense_date)->format('d M Y'),
                    $e->property?->property_name ?? 'General',
                    $e->expense_category ?? '-',
                    $e->expense_title,
                    number_format($e->amount, 2),
                    $e->payment_mode ?? '-',
                    $e->paid_to ?? '-',
                    $e->bill_no ?? '-',
                    $e->approval_status ?? 'Pending',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
