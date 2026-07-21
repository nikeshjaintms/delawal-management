<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Property;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanReportController extends Controller
{
    private function getReportData(Request $request)
    {
        $query = Loan::with(['firm', 'property', 'customer']);

        if (!Auth::user()->isAdmin()) {
            $query->where('firm_id', Auth::user()->firm_id);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('filter_status'))    $query->where('loan_status', $request->filter_status);
        if ($request->filled('filter_property'))  $query->where('property_id', $request->filter_property);
        if ($request->filled('filter_customer'))  $query->where('customer_id', $request->filter_customer);
        if ($request->filled('filter_loan_type')) $query->where('loan_type', $request->filter_loan_type);
        if ($request->filled('filter_bank'))      $query->where('bank_name', 'like', '%' . $request->filter_bank . '%');
        if ($request->filled('from_date'))        $query->where('loan_start_date', '>=', $request->from_date);
        if ($request->filled('to_date'))          $query->where('loan_start_date', '<=', $request->to_date);

        return $query->orderBy('loan_start_date', 'desc')->get();
    }

    private function buildSummaries($loans): array
    {
        $byBank = [];
        foreach ($loans as $l) {
            $key = $l->bank_name;
            $byBank[$key] = ($byBank[$key] ?? 0) + $l->loan_amount;
        }
        arsort($byBank);

        $byCustomer = [];
        foreach ($loans as $l) {
            $key = $l->customer?->name ?? 'General';
            $byCustomer[$key] = ($byCustomer[$key] ?? 0) + $l->loan_amount;
        }
        arsort($byCustomer);

        $byType = [];
        foreach ($loans as $l) {
            $byType[$l->loan_type] = ($byType[$l->loan_type] ?? 0) + $l->loan_amount;
        }
        arsort($byType);

        return compact('byBank', 'byCustomer', 'byType');
    }

    public function index(Request $request)
    {
        $loans     = $this->getReportData($request);
        $summaries = $this->buildSummaries($loans);

        $firms     = \App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get();
        $propQuery = Property::orderBy('property_name');
        $custQuery = Customer::where('status', 'active')->orderBy('name');

        if (!Auth::user()->isAdmin()) {
            $propQuery->where('firm_id', Auth::user()->firm_id);
            $custQuery->where('firm_id', Auth::user()->firm_id);
        } elseif ($request->filled('firm_id')) {
            $propQuery->where('firm_id', $request->firm_id);
            $custQuery->where('firm_id', $request->firm_id);
        }

        $properties = $propQuery->get();
        $customers  = $custQuery->get();

        return view('admin.loan-report.index', array_merge(
            compact('loans', 'firms', 'properties', 'customers'),
            $summaries
        ));
    }

    public function exportPdf(Request $request)
    {
        $loans     = $this->getReportData($request);
        $summaries = $this->buildSummaries($loans);
        $firms     = \App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get();

        $propQuery = Property::orderBy('property_name');
        $custQuery = Customer::orderBy('name');

        if (!Auth::user()->isAdmin()) {
            $propQuery->where('firm_id', Auth::user()->firm_id);
            $custQuery->where('firm_id', Auth::user()->firm_id);
        } elseif ($request->filled('firm_id')) {
            $propQuery->where('firm_id', $request->firm_id);
            $custQuery->where('firm_id', $request->firm_id);
        }

        $properties = $propQuery->get();
        $customers  = $custQuery->get();

        return view('admin.loan-report.pdf', array_merge(
            compact('loans', 'firms', 'properties', 'customers'),
            $summaries
        ));
    }

    public function exportExcel(Request $request)
    {
        $loans    = $this->getReportData($request);
        $filename = 'loan-report-' . date('Y-m-d') . '.csv';

        $totalLoan    = $loans->sum('loan_amount');
        $totalPaid    = $loans->sum('paid_amount');
        $totalPending = $loans->sum('pending_amount');

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($loans, $request, $totalLoan, $totalPaid, $totalPending) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Report Header
            fputcsv($handle, ['Delawala Properties & Management - Loan Report']);
            fputcsv($handle, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
                fputcsv($handle, ['Date Range', $fromDisplay . ' to ' . $toDisplay]);
            }
            fputcsv($handle, []); // Blank row

            // Summary Section
            fputcsv($handle, ['SUMMARY']);
            fputcsv($handle, ['Total Loan Amount', number_format($totalLoan, 2)]);
            fputcsv($handle, ['Total Loans', $loans->count()]);
            fputcsv($handle, ['Total Paid', number_format($totalPaid, 2)]);
            fputcsv($handle, ['Total Pending', number_format($totalPending, 2)]);
            fputcsv($handle, []); // Blank row

            // Data Header
            fputcsv($handle, [
                'Bank Name', 'Loan Type', 'Customer', 'Property',
                'Loan Amount (₹)', 'Interest Rate (%)', 'EMI Amount (₹)',
                'Start Date', 'End Date', 'Total EMIs',
                'Paid (₹)', 'Pending (₹)', 'Status',
            ]);

            // Data Rows
            foreach ($loans as $l) {
                fputcsv($handle, [
                    $l->bank_name,
                    $l->loan_type,
                    $l->customer?->name ?? '-',
                    $l->property?->property_name ?? '-',
                    number_format($l->loan_amount, 2),
                    $l->interest_rate,
                    number_format($l->emi_amount, 2),
                    \Carbon\Carbon::parse($l->loan_start_date)->format('d M Y'),
                    \Carbon\Carbon::parse($l->loan_end_date)->format('d M Y'),
                    $l->total_emi_months,
                    number_format($l->paid_amount, 2),
                    number_format($l->pending_amount, 2),
                    $l->loan_status,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
