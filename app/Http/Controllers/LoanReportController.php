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
        $query = Loan::with(['firms', 'firm', 'property', 'customer', 'paymentMode']);

        if (!Auth::user()->isAdmin()) {
            $query->forFirms([Auth::user()->firm_id]);
        } elseif ($request->filled('firm_ids') || $request->filled('firm_id')) {
            $firmIds = $request->input('firm_ids', (array)$request->firm_id);
            $query->forFirms($firmIds);
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
        $loans      = $this->getReportData($request);
        $summaries  = $this->buildSummaries($loans);

        $firms      = \App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get();
        $properties = Property::orderBy('property_name')->get();
        $customers  = Customer::orderBy('name')->get();

        return view('admin.loan-report.index', array_merge(
            compact('loans', 'firms', 'properties', 'customers'),
            $summaries
        ));
    }

    public function exportPdf(Request $request)
    {
        $loans     = $this->getReportData($request);
        $summaries = $this->buildSummaries($loans);

        return view('admin.loan-report.pdf', array_merge(
            compact('loans'),
            $summaries
        ));
    }

    public function exportExcel(Request $request)
    {
        $loans    = $this->getReportData($request);
        $filename = 'loan-report-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($loans, $request) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['Delawala Properties & Management - Loan Report']);
            fputcsv($handle, ['Generated on', date('d M Y, h:i A')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'Firm(s)', 'Loan Type', 'Bank Name', 'Person Name', 'Relationship',
                'Mobile', 'Customer', 'Property',
                'Loan Amount (₹)', 'EMI Amount (₹)', 'Paid Amount (₹)', 'Pending Amount (₹)',
                'Payment Mode', 'Loan Date', 'Status'
            ]);

            foreach ($loans as $l) {
                fputcsv($handle, [
                    $l->firm_names,
                    $l->loan_type,
                    $l->bank_name ?? '',
                    $l->person_name ?? '',
                    $l->relationship ?? '',
                    $l->mobile_number ?? '',
                    $l->customer?->name ?? '',
                    $l->property?->property_name ?? '',
                    number_format($l->loan_amount, 2),
                    number_format($l->emi_amount ?? 0, 2),
                    number_format($l->paid_amount, 2),
                    number_format($l->pending_amount, 2),
                    $l->paymentMode?->name ?? '',
                    $l->loan_start_date,
                    $l->loan_status
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
