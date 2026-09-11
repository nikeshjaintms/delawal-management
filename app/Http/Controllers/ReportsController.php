<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\DebitNote;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Firm;
use App\Models\Loan;
use App\Models\LoanEmiSchedule;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Property;
use App\Models\PropertySale;
use App\Models\PropertyType;
use App\Models\Rental;
use App\Models\RentalPayment;
use App\Models\StockInward;
use App\Models\StockOutward;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ReportsController — Main Reports Hub & Handlers
 */
class ReportsController extends Controller
{
    // ---------------------------------------------------------------
    // Shared Firm Scoping Helper
    // ---------------------------------------------------------------
    private function applyFirmScope($query, Request $request, string $firmCol = 'firm_id', ?string $relation = null)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();

        if (!$isAdmin) {
            $firmId = $user ? $user->firm_id : session('firm_id');
            if ($firmId) {
                if ($relation) {
                    $query->whereHas($relation, fn($q) => $q->where($firmCol, $firmId));
                } else {
                    $query->where($firmCol, $firmId);
                }
            }
        } elseif ($request->filled('firm_id')) {
            $firmId = $request->firm_id;
            if ($relation) {
                $query->whereHas($relation, fn($q) => $q->where($firmCol, $firmId));
            } else {
                $query->where($firmCol, $firmId);
            }
        }
        return $query;
    }

    // ---------------------------------------------------------------
    // Reports Hub — Landing Page
    // ---------------------------------------------------------------
    public function index()
    {
        return view('admin.reports.index');
    }

    // ---------------------------------------------------------------
    // GST Sales Report
    // ---------------------------------------------------------------
    private function getGstSalesData(Request $request)
    {
        $query = PropertySale::with(['property', 'customer', 'broker', 'firm']);
        $this->applyFirmScope($query, $request);

        if ($request->filled('from_date'))       $query->whereDate('sale_date', '>=', $request->from_date);
        if ($request->filled('to_date'))         $query->whereDate('sale_date', '<=', $request->to_date);
        if ($request->filled('filter_customer')) $query->where('customer_id', $request->filter_customer);
        if ($request->filled('filter_status'))   $query->where('payment_status', $request->filter_status);

        $sales = $query->orderBy('sale_date', 'desc')->get();
        $sales->transform(function ($s) {
            $s->computed_taxable     = $s->taxable_amount ?? $s->sale_amount ?? 0;
            $s->computed_cgst        = $s->cgst_amount  ?? 0;
            $s->computed_sgst        = $s->sgst_amount  ?? 0;
            $s->computed_igst        = $s->igst_amount  ?? 0;
            $s->computed_total_gst   = $s->computed_cgst + $s->computed_sgst + $s->computed_igst;
            $s->computed_grand_total = $s->grand_total ?? ($s->computed_taxable + $s->computed_total_gst);
            return $s;
        });
        return $sales;
    }

    public function gstSales(Request $request)
    {
        $sales = $this->getGstSalesData($request);

        $totalInvoices = $sales->count();
        $totalTaxable  = $sales->sum('computed_taxable');
        $totalCgst     = $sales->sum('computed_cgst');
        $totalSgst     = $sales->sum('computed_sgst');
        $totalIgst     = $sales->sum('computed_igst');
        $totalGst      = $sales->sum('computed_total_gst');
        $grandTotal    = $sales->sum('computed_grand_total');

        $custQuery = Customer::orderBy('name');
        $this->applyFirmScope($custQuery, $request);
        $customers = $custQuery->get();
        $firms     = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.reports.gst-sales', compact(
            'sales', 'customers', 'firms',
            'totalInvoices', 'totalTaxable',
            'totalCgst', 'totalSgst', 'totalIgst',
            'totalGst', 'grandTotal'
        ));
    }

    public function gstSalesExportPdf(Request $request)
    {
        $sales        = $this->getGstSalesData($request);
        $totalTaxable = $sales->sum('computed_taxable');
        $totalCgst    = $sales->sum('computed_cgst');
        $totalSgst    = $sales->sum('computed_sgst');
        $totalIgst    = $sales->sum('computed_igst');
        $totalGst     = $sales->sum('computed_total_gst');
        $grandTotal   = $sales->sum('computed_grand_total');

        return view('admin.reports.gst-sales-pdf', compact(
            'sales', 'totalTaxable', 'totalCgst', 'totalSgst',
            'totalIgst', 'totalGst', 'grandTotal'
        ));
    }

    public function gstSalesExportExcel(Request $request)
    {
        $sales    = $this->getGstSalesData($request);
        $filename = 'gst-sales-report-' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($sales) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, ['Sr','Invoice No','Date','Customer','Property',
                'Taxable Amt','CGST%','CGST Amt','SGST%','SGST Amt',
                'IGST%','IGST Amt','Total GST','Grand Total','HSN Code','Status']);
            foreach ($sales as $i => $s) {
                fputcsv($h, [
                    $i + 1, $s->invoice_no ?? '-',
                    $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '-',
                    $s->customer?->name ?? '-',
                    $s->property?->property_name ?? '-',
                    number_format($s->computed_taxable, 2),
                    $s->cgst_rate ?? '0', number_format($s->computed_cgst, 2),
                    $s->sgst_rate ?? '0', number_format($s->computed_sgst, 2),
                    $s->igst_rate ?? '0', number_format($s->computed_igst, 2),
                    number_format($s->computed_total_gst, 2),
                    number_format($s->computed_grand_total, 2),
                    $s->hsn_code ?? '-', $s->payment_status ?? '-',
                ]);
            }
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------------
    // GST Purchase Report
    // ---------------------------------------------------------------
    private function getGstPurchaseData(Request $request)
    {
        $query = Expense::with(['vendor', 'property', 'expenseCategory', 'firm']);
        $this->applyFirmScope($query, $request);

        if ($request->filled('from_date'))       $query->whereDate('expense_date', '>=', $request->from_date);
        if ($request->filled('to_date'))         $query->whereDate('expense_date', '<=', $request->to_date);
        if ($request->filled('filter_vendor'))   $query->where('vendor_id', $request->filter_vendor);
        if ($request->filled('filter_status'))   $query->where('approval_status', $request->filter_status);
        if ($request->filled('filter_category')) $query->where('expense_category_id', $request->filter_category);

        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $expenses->transform(function ($e) {
            $e->computed_taxable     = $e->taxable_amount ?? $e->amount ?? 0;
            $e->computed_cgst        = $e->cgst_amount  ?? 0;
            $e->computed_sgst        = $e->sgst_amount  ?? 0;
            $e->computed_igst        = $e->igst_amount  ?? 0;
            $e->computed_total_gst   = $e->computed_cgst + $e->computed_sgst + $e->computed_igst;
            $e->computed_grand_total = $e->grand_total ?? ($e->computed_taxable + $e->computed_total_gst);
            return $e;
        });

        return $expenses;
    }

    public function gstPurchase(Request $request)
    {
        $expenses = $this->getGstPurchaseData($request);

        $totalBills   = $expenses->count();
        $totalTaxable = $expenses->sum('computed_taxable');
        $totalCgst    = $expenses->sum('computed_cgst');
        $totalSgst    = $expenses->sum('computed_sgst');
        $totalIgst    = $expenses->sum('computed_igst');
        $totalGst     = $expenses->sum('computed_total_gst');
        $grandTotal   = $expenses->sum('computed_grand_total');

        $venQuery = Vendor::orderBy('name');
        $this->applyFirmScope($venQuery, $request);
        $vendors = $venQuery->get();

        $catQuery = ExpenseCategory::where('status', 'active')->orderBy('name');
        $this->applyFirmScope($catQuery, $request, 'id', 'firms');
        $categories = $catQuery->get();

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.reports.gst-purchase', compact(
            'expenses', 'vendors', 'categories', 'firms',
            'totalBills', 'totalTaxable',
            'totalCgst', 'totalSgst', 'totalIgst',
            'totalGst', 'grandTotal'
        ));
    }

    public function gstPurchaseExportPdf(Request $request)
    {
        $expenses     = $this->getGstPurchaseData($request);
        $totalTaxable = $expenses->sum('computed_taxable');
        $totalCgst    = $expenses->sum('computed_cgst');
        $totalSgst    = $expenses->sum('computed_sgst');
        $totalIgst    = $expenses->sum('computed_igst');
        $totalGst     = $expenses->sum('computed_total_gst');
        $grandTotal   = $expenses->sum('computed_grand_total');

        return view('admin.reports.gst-purchase-pdf', compact(
            'expenses', 'totalTaxable', 'totalCgst', 'totalSgst',
            'totalIgst', 'totalGst', 'grandTotal'
        ));
    }

    public function gstPurchaseExportExcel(Request $request)
    {
        $expenses = $this->getGstPurchaseData($request);
        $filename = 'gst-purchase-report-' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($expenses) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, [
                'Sr', 'Bill No', 'Invoice No', 'Date', 'Vendor / Supplier',
                'Expense Title', 'Category', 'HSN Code',
                'Taxable Amt', 'CGST%', 'CGST Amt', 'SGST%', 'SGST Amt',
                'IGST%', 'IGST Amt', 'Total GST', 'Grand Total',
                'Payment Mode', 'Approval Status',
            ]);
            foreach ($expenses as $i => $e) {
                fputcsv($h, [
                    $i + 1,
                    $e->bill_no    ?? '-',
                    $e->invoice_no ?? '-',
                    \Carbon\Carbon::parse($e->expense_date)->format('d M Y'),
                    $e->vendor?->name ?? ($e->paid_to ?? '-'),
                    $e->expense_title,
                    $e->expense_category ?? '-',
                    $e->hsn_code   ?? '-',
                    number_format($e->computed_taxable, 2),
                    $e->cgst_rate  ?? '0',
                    number_format($e->computed_cgst, 2),
                    $e->sgst_rate  ?? '0',
                    number_format($e->computed_sgst, 2),
                    $e->igst_rate  ?? '0',
                    number_format($e->computed_igst, 2),
                    number_format($e->computed_total_gst, 2),
                    number_format($e->computed_grand_total, 2),
                    $e->payment_mode     ?? '-',
                    $e->approval_status  ?? '-',
                ]);
            }
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------------
    // Credit Note Report
    // ---------------------------------------------------------------
    private function getCreditNoteData(Request $request)
    {
        $query = CreditNote::with(['customer', 'firm']);
        $this->applyFirmScope($query, $request);

        if ($request->filled('from_date'))       $query->whereDate('credit_note_date', '>=', $request->from_date);
        if ($request->filled('to_date'))         $query->whereDate('credit_note_date', '<=', $request->to_date);
        if ($request->filled('filter_customer')) $query->where('customer_id', $request->filter_customer);
        if ($request->filled('filter_status'))   $query->where('status', $request->filter_status);

        return $query->orderBy('credit_note_date', 'desc')->get();
    }

    public function creditNote(Request $request)
    {
        $notes = $this->getCreditNoteData($request);

        $totalNotes   = $notes->count();
        $totalTaxable = $notes->sum('taxable_amount');
        $totalCgst    = $notes->sum('cgst_amount');
        $totalSgst    = $notes->sum('sgst_amount');
        $totalIgst    = $notes->sum('igst_amount');
        $totalGst     = $notes->sum('total_gst');
        $totalCredit  = $notes->sum('credit_amount');

        $custQuery = Customer::orderBy('name');
        $this->applyFirmScope($custQuery, $request);
        $customers = $custQuery->get();
        $firms     = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.reports.credit-note', compact(
            'notes', 'customers', 'firms',
            'totalNotes', 'totalTaxable',
            'totalCgst', 'totalSgst', 'totalIgst',
            'totalGst', 'totalCredit'
        ));
    }

    public function creditNoteExportPdf(Request $request)
    {
        $notes        = $this->getCreditNoteData($request);
        $totalTaxable = $notes->sum('taxable_amount');
        $totalCgst    = $notes->sum('cgst_amount');
        $totalSgst    = $notes->sum('sgst_amount');
        $totalIgst    = $notes->sum('igst_amount');
        $totalGst     = $notes->sum('total_gst');
        $totalCredit  = $notes->sum('credit_amount');

        return view('admin.reports.credit-note-pdf', compact(
            'notes', 'totalTaxable', 'totalCgst', 'totalSgst',
            'totalIgst', 'totalGst', 'totalCredit'
        ));
    }

    public function creditNoteExportExcel(Request $request)
    {
        $notes    = $this->getCreditNoteData($request);
        $filename = 'credit-note-report-' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($notes) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, [
                'Sr', 'Credit Note No', 'Date', 'Customer',
                'Related Invoice No', 'Reason',
                'Taxable Amt', 'CGST%', 'CGST Amt', 'SGST%', 'SGST Amt',
                'IGST%', 'IGST Amt', 'Total GST', 'Credit Amount', 'Status',
            ]);
            foreach ($notes as $i => $n) {
                fputcsv($h, [
                    $i + 1,
                    $n->credit_note_no     ?? '-',
                    \Carbon\Carbon::parse($n->credit_note_date)->format('d M Y'),
                    $n->customer?->name    ?? '-',
                    $n->related_invoice_no ?? '-',
                    $n->reason             ?? '-',
                    number_format($n->taxable_amount, 2),
                    $n->cgst_rate  ?? '0', number_format($n->cgst_amount, 2),
                    $n->sgst_rate  ?? '0', number_format($n->sgst_amount, 2),
                    $n->igst_rate  ?? '0', number_format($n->igst_amount, 2),
                    number_format($n->total_gst,     2),
                    number_format($n->credit_amount, 2),
                    $n->status,
                ]);
            }
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------------
    // Debit Note Report
    // ---------------------------------------------------------------
    private function getDebitNoteData(Request $request)
    {
        $query = DebitNote::with(['vendor', 'firm']);
        $this->applyFirmScope($query, $request);

        if ($request->filled('from_date'))      $query->whereDate('debit_note_date', '>=', $request->from_date);
        if ($request->filled('to_date'))        $query->whereDate('debit_note_date', '<=', $request->to_date);
        if ($request->filled('filter_vendor'))  $query->where('vendor_id', $request->filter_vendor);
        if ($request->filled('filter_status'))  $query->where('status', $request->filter_status);

        return $query->orderBy('debit_note_date', 'desc')->get();
    }

    public function debitNote(Request $request)
    {
        $notes = $this->getDebitNoteData($request);

        $totalNotes   = $notes->count();
        $totalTaxable = $notes->sum('taxable_amount');
        $totalCgst    = $notes->sum('cgst_amount');
        $totalSgst    = $notes->sum('sgst_amount');
        $totalIgst    = $notes->sum('igst_amount');
        $totalGst     = $notes->sum('total_gst');
        $totalDebit   = $notes->sum('debit_amount');

        $venQuery = Vendor::orderBy('name');
        $this->applyFirmScope($venQuery, $request);
        $vendors = $venQuery->get();
        $firms   = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.reports.debit-note', compact(
            'notes', 'vendors', 'firms',
            'totalNotes', 'totalTaxable',
            'totalCgst', 'totalSgst', 'totalIgst',
            'totalGst', 'totalDebit'
        ));
    }

    public function debitNoteExportPdf(Request $request)
    {
        $notes        = $this->getDebitNoteData($request);
        $totalTaxable = $notes->sum('taxable_amount');
        $totalCgst    = $notes->sum('cgst_amount');
        $totalSgst    = $notes->sum('sgst_amount');
        $totalIgst    = $notes->sum('igst_amount');
        $totalGst     = $notes->sum('total_gst');
        $totalDebit   = $notes->sum('debit_amount');

        return view('admin.reports.debit-note-pdf', compact(
            'notes', 'totalTaxable', 'totalCgst', 'totalSgst',
            'totalIgst', 'totalGst', 'totalDebit'
        ));
    }

    public function debitNoteExportExcel(Request $request)
    {
        $notes    = $this->getDebitNoteData($request);
        $filename = 'debit-note-report-' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($notes) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, [
                'Sr', 'Debit Note No', 'Date', 'Vendor / Supplier',
                'Related Bill No', 'Reason',
                'Taxable Amt', 'CGST%', 'CGST Amt', 'SGST%', 'SGST Amt',
                'IGST%', 'IGST Amt', 'Total GST', 'Debit Amount', 'Status',
            ]);
            foreach ($notes as $i => $n) {
                fputcsv($h, [
                    $i + 1,
                    $n->debit_note_no   ?? '-',
                    \Carbon\Carbon::parse($n->debit_note_date)->format('d M Y'),
                    $n->vendor?->name   ?? '-',
                    $n->related_bill_no ?? '-',
                    $n->reason          ?? '-',
                    number_format($n->taxable_amount, 2),
                    $n->cgst_rate  ?? '0', number_format($n->cgst_amount, 2),
                    $n->sgst_rate  ?? '0', number_format($n->sgst_amount, 2),
                    $n->igst_rate  ?? '0', number_format($n->igst_amount, 2),
                    number_format($n->total_gst,    2),
                    number_format($n->debit_amount, 2),
                    $n->status,
                ]);
            }
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------------
    // Profit & Loss Statement
    // ---------------------------------------------------------------
    public function profitLoss(Request $request)
    {
        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate   = $request->filled('to_date')   ? $request->to_date   : null;

        // 1. Property Sales & Bookings Income
        $payQuery = Payment::query();
        $this->applyFirmScope($payQuery, $request);
        $salesPaymentIncome = $payQuery
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->sum('payment_amount');

        $bkQuery = Booking::query();
        $this->applyFirmScope($bkQuery, $request);
        $bookingIncome = $bkQuery
            ->when($fromDate, fn($q) => $q->whereDate('booking_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('booking_date', '<=', $toDate))
            ->sum('booking_amount');

        $salesIncome = max($salesPaymentIncome, $bookingIncome);

        // 2. Rental Income
        $rentPayQuery = RentalPayment::query();
        $this->applyFirmScope($rentPayQuery, $request, 'firm_id', 'rental');
        $rentalIncome = $rentPayQuery
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->sum('paid_amount');

        $totalIncome = $salesIncome + $rentalIncome;

        // 3. Operating Expenses
        $expQuery = Expense::query();
        $this->applyFirmScope($expQuery, $request);
        $operatingExpense = $expQuery
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->sum('amount');

        // 4. Loan EMI Payments
        $loanQuery = LoanEmiSchedule::whereIn('emi_status', ['Paid', 'Partial']);
        $this->applyFirmScope($loanQuery, $request, 'firm_id', 'loan');
        $loanEmiPaid = $loanQuery
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->sum('paid_amount');

        $totalExpense  = $operatingExpense + $loanEmiPaid;
        $netProfitLoss = $totalIncome - $totalExpense;

        $rows = collect([
            ['particular' => 'Property Sales & Bookings Receipts', 'type' => 'income',  'amount' => $salesIncome],
            ['particular' => 'Rental Income Received',              'type' => 'income',  'amount' => $rentalIncome],
            ['particular' => 'Operating Expenses',                  'type' => 'expense', 'amount' => $operatingExpense],
            ['particular' => 'Loan EMI Payments',                   'type' => 'expense', 'amount' => $loanEmiPaid],
        ]);

        $expCatQuery = Expense::query();
        $this->applyFirmScope($expCatQuery, $request);
        $expenseByCategory = $expCatQuery
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->selectRaw('COALESCE(expense_category, "Uncategorised") as category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.reports.profit-loss', compact(
            'salesIncome', 'rentalIncome', 'totalIncome',
            'operatingExpense', 'loanEmiPaid', 'totalExpense',
            'netProfitLoss', 'rows', 'expenseByCategory', 'firms'
        ));
    }

    public function profitLossExportPdf(Request $request)
    {
        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate   = $request->filled('to_date')   ? $request->to_date   : null;

        $payQuery = Payment::query();
        $this->applyFirmScope($payQuery, $request);
        $salesPaymentIncome = $payQuery->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('payment_date', '<=', $toDate))->sum('payment_amount');

        $bkQuery = Booking::query();
        $this->applyFirmScope($bkQuery, $request);
        $bookingIncome = $bkQuery->when($fromDate, fn($q) => $q->whereDate('booking_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('booking_date', '<=', $toDate))->sum('booking_amount');

        $salesIncome = max($salesPaymentIncome, $bookingIncome);

        $rentPayQuery = RentalPayment::query();
        $this->applyFirmScope($rentPayQuery, $request, 'firm_id', 'rental');
        $rentalIncome = $rentPayQuery->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('payment_date', '<=', $toDate))->sum('paid_amount');

        $totalIncome = $salesIncome + $rentalIncome;

        $expQuery = Expense::query();
        $this->applyFirmScope($expQuery, $request);
        $operatingExpense = $expQuery->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('expense_date', '<=', $toDate))->sum('amount');

        $loanQuery = LoanEmiSchedule::whereIn('emi_status', ['Paid', 'Partial']);
        $this->applyFirmScope($loanQuery, $request, 'firm_id', 'loan');
        $loanEmiPaid = $loanQuery->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('payment_date', '<=', $toDate))->sum('paid_amount');

        $totalExpense  = $operatingExpense + $loanEmiPaid;
        $netProfitLoss = $totalIncome - $totalExpense;

        $expCatQuery = Expense::query();
        $this->applyFirmScope($expCatQuery, $request);
        $expenseByCategory = $expCatQuery->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->selectRaw('COALESCE(expense_category, "Uncategorised") as category, SUM(amount) as total')
            ->groupBy('category')->orderByDesc('total')->get();

        return view('admin.reports.profit-loss-pdf', compact(
            'salesIncome', 'rentalIncome', 'totalIncome',
            'operatingExpense', 'loanEmiPaid', 'totalExpense',
            'netProfitLoss', 'expenseByCategory'
        ));
    }

    public function profitLossExportExcel(Request $request)
    {
        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate   = $request->filled('to_date')   ? $request->to_date   : null;

        $payQuery = Payment::query();
        $this->applyFirmScope($payQuery, $request);
        $salesPaymentIncome = $payQuery->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('payment_date', '<=', $toDate))->sum('payment_amount');

        $bkQuery = Booking::query();
        $this->applyFirmScope($bkQuery, $request);
        $bookingIncome = $bkQuery->when($fromDate, fn($q) => $q->whereDate('booking_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('booking_date', '<=', $toDate))->sum('booking_amount');

        $salesIncome = max($salesPaymentIncome, $bookingIncome);

        $rentPayQuery = RentalPayment::query();
        $this->applyFirmScope($rentPayQuery, $request, 'firm_id', 'rental');
        $rentalIncome = $rentPayQuery->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('payment_date', '<=', $toDate))->sum('paid_amount');

        $totalIncome = $salesIncome + $rentalIncome;

        $expQuery = Expense::query();
        $this->applyFirmScope($expQuery, $request);
        $operatingExpense = $expQuery->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('expense_date', '<=', $toDate))->sum('amount');

        $loanQuery = LoanEmiSchedule::whereIn('emi_status', ['Paid', 'Partial']);
        $this->applyFirmScope($loanQuery, $request, 'firm_id', 'loan');
        $loanEmiPaid = $loanQuery->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('payment_date', '<=', $toDate))->sum('paid_amount');

        $totalExpense  = $operatingExpense + $loanEmiPaid;
        $net           = $totalIncome - $totalExpense;

        $filename = 'profit-loss-' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($salesIncome, $rentalIncome, $totalIncome,
                                     $operatingExpense, $loanEmiPaid, $totalExpense, $net) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, ['Particular', 'Type', 'Amount (₹)']);
            fputcsv($h, ['Property Sales & Bookings Receipts', 'Income',  number_format($salesIncome, 2)]);
            fputcsv($h, ['Rental Income Received',             'Income',  number_format($rentalIncome, 2)]);
            fputcsv($h, ['Total Income',                       'TOTAL',   number_format($totalIncome, 2)]);
            fputcsv($h, ['']);
            fputcsv($h, ['Operating Expenses',                 'Expense', number_format($operatingExpense, 2)]);
            fputcsv($h, ['Loan EMI Payments',                  'Expense', number_format($loanEmiPaid, 2)]);
            fputcsv($h, ['Total Expenses',                      'TOTAL',   number_format($totalExpense, 2)]);
            fputcsv($h, ['']);
            fputcsv($h, ['Net ' . ($net >= 0 ? 'Profit' : 'Loss'), 'NET', number_format(abs($net), 2)]);
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------------
    // Balance Sheet
    // ---------------------------------------------------------------
    public function balanceSheet(Request $request)
    {
        $asOnDate = $request->filled('as_on_date') ? $request->as_on_date : null;

        $payQuery = Payment::query();
        $this->applyFirmScope($payQuery, $request);
        $cashFromPayments = $payQuery->when($asOnDate, fn($q) => $q->whereDate('payment_date', '<=', $asOnDate))->sum('payment_amount');

        $bkQuery = Booking::query();
        $this->applyFirmScope($bkQuery, $request);
        $cashFromBookings = $bkQuery->when($asOnDate, fn($q) => $q->whereDate('booking_date', '<=', $asOnDate))->sum('booking_amount');

        $cashReceived = max($cashFromPayments, $cashFromBookings);

        $rentPayQuery = RentalPayment::query();
        $this->applyFirmScope($rentPayQuery, $request, 'firm_id', 'rental');
        $rentalCashReceived = $rentPayQuery->when($asOnDate, fn($q) => $q->whereDate('payment_date', '<=', $asOnDate))->sum('paid_amount');

        $saleRecQuery = PropertySale::query();
        $this->applyFirmScope($saleRecQuery, $request);
        $receivablesFromSales = $saleRecQuery->when($asOnDate, fn($q) => $q->whereDate('sale_date', '<=', $asOnDate))
            ->whereIn('payment_status', ['pending', 'partial'])->sum('remaining_amount');

        $bkRecQuery = Booking::query();
        $this->applyFirmScope($bkRecQuery, $request);
        $receivablesFromBookings = $bkRecQuery->when($asOnDate, fn($q) => $q->whereDate('booking_date', '<=', $asOnDate))
            ->whereIn('payment_status', ['unpaid', 'partial'])->sum('remaining_amount');

        $receivables = max($receivablesFromSales, $receivablesFromBookings);

        $propQuery = Property::whereIn('status', ['available', 'booked']);
        $this->applyFirmScope($propQuery, $request);
        $propertyValue = $propQuery->sum('price');

        $rentQuery = Rental::where('rental_status', 'active');
        $this->applyFirmScope($rentQuery, $request);
        $securityDeposits = $rentQuery->when($asOnDate, fn($q) => $q->whereDate('rent_start_date', '<=', $asOnDate))->sum('security_deposit');

        $totalAssets = $cashReceived + $rentalCashReceived + $receivables + $propertyValue + $securityDeposits;

        $loanQuery = Loan::query();
        $this->applyFirmScope($loanQuery, $request);
        $loanOutstanding = $loanQuery->when($asOnDate, fn($q) => $q->whereDate('loan_start_date', '<=', $asOnDate))->sum('pending_amount');
        $loanTotal = (clone $loanQuery)->sum('loan_amount');
        $loanPaid  = (clone $loanQuery)->sum('paid_amount');

        $expQuery = Expense::where('approval_status', 'Pending');
        $this->applyFirmScope($expQuery, $request);
        $unpaidExpenses = $expQuery->when($asOnDate, fn($q) => $q->whereDate('expense_date', '<=', $asOnDate))->sum('amount');

        $cnQuery = CreditNote::whereIn('status', ['Pending', 'Approved']);
        $this->applyFirmScope($cnQuery, $request);
        $creditNotePayable = $cnQuery->when($asOnDate, fn($q) => $q->whereDate('credit_note_date', '<=', $asOnDate))->sum('credit_amount');

        $totalLiabilities = $loanOutstanding + $unpaidExpenses + $creditNotePayable;
        $netWorth = $totalAssets - $totalLiabilities;

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.reports.balance-sheet', compact(
            'cashReceived', 'rentalCashReceived', 'receivables',
            'propertyValue', 'securityDeposits', 'totalAssets',
            'loanOutstanding', 'unpaidExpenses', 'creditNotePayable',
            'loanTotal', 'loanPaid', 'totalLiabilities',
            'netWorth', 'firms'
        ));
    }

    public function balanceSheetExportExcel(Request $request)
    {
        $asOnDate = $request->filled('as_on_date') ? $request->as_on_date : null;

        $payQuery = Payment::query();
        $this->applyFirmScope($payQuery, $request);
        $cashFromPayments = $payQuery->when($asOnDate, fn($q) => $q->whereDate('payment_date', '<=', $asOnDate))->sum('payment_amount');

        $bkQuery = Booking::query();
        $this->applyFirmScope($bkQuery, $request);
        $cashFromBookings = $bkQuery->when($asOnDate, fn($q) => $q->whereDate('booking_date', '<=', $asOnDate))->sum('booking_amount');

        $cashReceived = max($cashFromPayments, $cashFromBookings);

        $rentPayQuery = RentalPayment::query();
        $this->applyFirmScope($rentPayQuery, $request, 'firm_id', 'rental');
        $rentalCashReceived = $rentPayQuery->when($asOnDate, fn($q) => $q->whereDate('payment_date', '<=', $asOnDate))->sum('paid_amount');

        $saleRecQuery = PropertySale::query();
        $this->applyFirmScope($saleRecQuery, $request);
        $receivablesFromSales = $saleRecQuery->when($asOnDate, fn($q) => $q->whereDate('sale_date', '<=', $asOnDate))
            ->whereIn('payment_status', ['pending', 'partial'])->sum('remaining_amount');

        $bkRecQuery = Booking::query();
        $this->applyFirmScope($bkRecQuery, $request);
        $receivablesFromBookings = $bkRecQuery->when($asOnDate, fn($q) => $q->whereDate('booking_date', '<=', $asOnDate))
            ->whereIn('payment_status', ['unpaid', 'partial'])->sum('remaining_amount');

        $receivables = max($receivablesFromSales, $receivablesFromBookings);

        $propQuery = Property::whereIn('status', ['available', 'booked']);
        $this->applyFirmScope($propQuery, $request);
        $propertyValue = $propQuery->sum('price');

        $rentQuery = Rental::where('rental_status', 'active');
        $this->applyFirmScope($rentQuery, $request);
        $securityDeposits = $rentQuery->when($asOnDate, fn($q) => $q->whereDate('rent_start_date', '<=', $asOnDate))->sum('security_deposit');

        $totalAssets = $cashReceived + $rentalCashReceived + $receivables + $propertyValue + $securityDeposits;

        $loanQuery = Loan::query();
        $this->applyFirmScope($loanQuery, $request);
        $loanOutstanding = $loanQuery->when($asOnDate, fn($q) => $q->whereDate('loan_start_date', '<=', $asOnDate))->sum('pending_amount');

        $expQuery = Expense::where('approval_status', 'Pending');
        $this->applyFirmScope($expQuery, $request);
        $unpaidExpenses = $expQuery->when($asOnDate, fn($q) => $q->whereDate('expense_date', '<=', $asOnDate))->sum('amount');

        $cnQuery = CreditNote::whereIn('status', ['Pending', 'Approved']);
        $this->applyFirmScope($cnQuery, $request);
        $creditNotePayable = $cnQuery->when($asOnDate, fn($q) => $q->whereDate('credit_note_date', '<=', $asOnDate))->sum('credit_amount');

        $totalLiabilities = $loanOutstanding + $unpaidExpenses + $creditNotePayable;
        $netWorth = $totalAssets - $totalLiabilities;

        $filename = 'balance-sheet-' . ($asOnDate ?? date('Y-m-d')) . '.csv';
        $headers  = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="'.$filename.'"'];
        $callback = function () use ($cashReceived, $rentalCashReceived, $receivables, $propertyValue,
                                     $securityDeposits, $totalAssets, $loanOutstanding, $unpaidExpenses,
                                     $creditNotePayable, $totalLiabilities, $netWorth) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($h, ['Particular', 'Category', 'Amount (₹)']);
            fputcsv($h, ['Cash / Bank Receipts (Sales & Bookings)', 'Asset', number_format($cashReceived, 2)]);
            fputcsv($h, ['Rental Income Collected',                  'Asset', number_format($rentalCashReceived, 2)]);
            fputcsv($h, ['Receivables (Pending Sales/Bookings)',     'Asset', number_format($receivables, 2)]);
            fputcsv($h, ['Property Value (Unsold)',                  'Asset', number_format($propertyValue, 2)]);
            fputcsv($h, ['Security Deposits Held',                   'Asset', number_format($securityDeposits, 2)]);
            fputcsv($h, ['TOTAL ASSETS',                             'TOTAL', number_format($totalAssets, 2)]);
            fputcsv($h, ['']);
            fputcsv($h, ['Outstanding Loan Balance',                 'Liability', number_format($loanOutstanding, 2)]);
            fputcsv($h, ['Unpaid / Pending Expenses',                'Liability', number_format($unpaidExpenses, 2)]);
            fputcsv($h, ['Credit Notes Payable',                     'Liability', number_format($creditNotePayable, 2)]);
            fputcsv($h, ['TOTAL LIABILITIES',                        'TOTAL',     number_format($totalLiabilities, 2)]);
            fputcsv($h, ['']);
            fputcsv($h, ['NET WORTH (EQUITY)', 'NET', number_format($netWorth, 2)]);
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------------
    // Cash Flow Report
    // ---------------------------------------------------------------
    public function cashFlow(Request $request)
    {
        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate   = $request->filled('to_date')   ? $request->to_date   : null;

        // 1. Sales Payments
        $payQuery = Payment::with(['customer', 'propertySale.property', 'firm']);
        $this->applyFirmScope($payQuery, $request);
        $salesPayments = $payQuery->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')
            ->get()
            ->map(fn($p) => [
                'date'         => $p->payment_date,
                'particular'   => 'Sales Receipt — ' . ($p->customer?->name ?? 'Customer')
                                 . ($p->propertySale?->property?->property_name
                                    ? ' (' . $p->propertySale->property->property_name . ')' : ''),
                'type'         => 'inflow',
                'section'      => 'Sales Payment Received',
                'payment_mode' => $p->payment_mode ?? '—',
                'amount'       => (float) $p->payment_amount,
            ]);

        // If payments table is empty, include bookings
        if ($salesPayments->isEmpty()) {
            $bkQuery = Booking::with(['customer', 'property', 'firm']);
            $this->applyFirmScope($bkQuery, $request);
            $salesPayments = $bkQuery->whereNotNull('booking_date')->where('booking_amount', '>', 0)
                ->when($fromDate, fn($q) => $q->whereDate('booking_date', '>=', $fromDate))
                ->when($toDate,   fn($q) => $q->whereDate('booking_date', '<=', $toDate))
                ->orderBy('booking_date')
                ->get()
                ->map(fn($b) => [
                    'date'         => $b->booking_date,
                    'particular'   => 'Booking Token (' . ucfirst($b->booking_type ?? 'booking') . ') — ' . ($b->customer?->name ?? 'Customer')
                                     . ($b->property?->property_name ? ' (' . $b->property->property_name . ')' : ''),
                    'type'         => 'inflow',
                    'section'      => 'Booking Advance Received',
                    'payment_mode' => $b->payment_mode ?? ($b->paymentMode->name ?? '—'),
                    'amount'       => (float) $b->booking_amount,
                ]);
        }

        // 2. Rental Payments
        $rentQuery = RentalPayment::with(['rental.property', 'rental.firm']);
        $this->applyFirmScope($rentQuery, $request, 'firm_id', 'rental');
        $rentalPayments = $rentQuery->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')
            ->get()
            ->map(fn($r) => [
                'date'         => $r->payment_date,
                'particular'   => 'Rental Income — ' . ($r->rental?->tenant_name ?? 'Tenant')
                                 . ' / ' . $r->payment_month . ' ' . $r->payment_year,
                'type'         => 'inflow',
                'section'      => 'Rental Payment Received',
                'payment_mode' => $r->payment_mode ?? '—',
                'amount'       => (float) $r->paid_amount,
            ]);

        // 3. Expenses
        $expQuery = Expense::query();
        $this->applyFirmScope($expQuery, $request);
        $expensePayments = $expQuery
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->orderBy('expense_date')
            ->get()
            ->map(fn($e) => [
                'date'         => $e->expense_date,
                'particular'   => $e->expense_title . ($e->expense_category ? ' (' . $e->expense_category . ')' : ''),
                'type'         => 'outflow',
                'section'      => 'Expenses Paid',
                'payment_mode' => $e->payment_mode ?? '—',
                'amount'       => (float) $e->amount,
            ]);

        // 4. Loan EMI Repayments
        $loanQuery = LoanEmiSchedule::with('loan')->whereIn('emi_status', ['Paid', 'Partial']);
        $this->applyFirmScope($loanQuery, $request, 'firm_id', 'loan');
        $loanRepayments = $loanQuery->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')
            ->get()
            ->map(fn($e) => [
                'date'         => $e->payment_date,
                'particular'   => 'Loan EMI — ' . ($e->loan?->bank_name ?? 'Bank') . ' (' . $e->emi_month . '/' . $e->emi_year . ')',
                'type'         => 'outflow',
                'section'      => 'Loan Repayment',
                'payment_mode' => $e->payment_mode ?? '—',
                'amount'       => (float) $e->paid_amount,
            ]);

        $allTransactions = $salesPayments
            ->concat($rentalPayments)
            ->concat($expensePayments)
            ->concat($loanRepayments)
            ->sortBy('date')
            ->values();

        $totalSalesInflow  = $salesPayments->sum('amount');
        $totalRentalInflow = $rentalPayments->sum('amount');
        $totalInflow       = $totalSalesInflow + $totalRentalInflow;

        $totalExpenseOutflow = $expensePayments->sum('amount');
        $totalLoanOutflow    = $loanRepayments->sum('amount');
        $totalOutflow        = $totalExpenseOutflow + $totalLoanOutflow;

        $netCashFlow = $totalInflow - $totalOutflow;

        $monthlyRows = $allTransactions
            ->groupBy(fn($t) => substr($t['date'], 0, 7))
            ->map(function ($group, $month) {
                $in  = $group->where('type', 'inflow')->sum('amount');
                $out = $group->where('type', 'outflow')->sum('amount');
                return ['month' => $month, 'inflow' => $in, 'outflow' => $out, 'net' => $in - $out];
            })
            ->sortKeys()
            ->values();

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.reports.cash-flow', compact(
            'allTransactions',
            'totalSalesInflow', 'totalRentalInflow', 'totalInflow',
            'totalExpenseOutflow', 'totalLoanOutflow', 'totalOutflow',
            'netCashFlow', 'monthlyRows', 'firms'
        ));
    }

    public function cashFlowExportExcel(Request $request)
    {
        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate   = $request->filled('to_date')   ? $request->to_date   : null;

        $payQuery = Payment::with(['customer','propertySale.property']);
        $this->applyFirmScope($payQuery, $request);
        $salesPayments = $payQuery->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')->get()
            ->map(fn($p) => ['date' => $p->payment_date,
                'particular'   => 'Sales Receipt — '.($p->customer?->name ?? '-'),
                'type'         => 'Inflow', 'section' => 'Sales Payment Received',
                'payment_mode' => $p->payment_mode ?? '-', 'amount' => (float)$p->payment_amount]);

        if ($salesPayments->isEmpty()) {
            $bkQuery = Booking::with(['customer', 'property']);
            $this->applyFirmScope($bkQuery, $request);
            $salesPayments = $bkQuery->whereNotNull('booking_date')->where('booking_amount', '>', 0)
                ->when($fromDate, fn($q) => $q->whereDate('booking_date', '>=', $fromDate))
                ->when($toDate,   fn($q) => $q->whereDate('booking_date', '<=', $toDate))
                ->orderBy('booking_date')->get()
                ->map(fn($b) => ['date' => $b->booking_date,
                    'particular'   => 'Booking Token — '.($b->customer?->name ?? '-'),
                    'type'         => 'Inflow', 'section' => 'Booking Advance Received',
                    'payment_mode' => $b->payment_mode ?? ($b->paymentMode->name ?? '-'), 'amount' => (float)$b->booking_amount]);
        }

        $rentQuery = RentalPayment::with(['rental']);
        $this->applyFirmScope($rentQuery, $request, 'firm_id', 'rental');
        $rentalPayments = $rentQuery->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')->get()
            ->map(fn($r) => ['date' => $r->payment_date,
                'particular'   => 'Rental Income — '.($r->rental?->tenant_name ?? '-'),
                'type'         => 'Inflow', 'section' => 'Rental Payment Received',
                'payment_mode' => $r->payment_mode ?? '-', 'amount' => (float)$r->paid_amount]);

        $expQuery = Expense::query();
        $this->applyFirmScope($expQuery, $request);
        $expensePayments = $expQuery
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->orderBy('expense_date')->get()
            ->map(fn($e) => ['date' => $e->expense_date,
                'particular'   => $e->expense_title, 'type' => 'Outflow',
                'section'      => 'Expenses Paid',
                'payment_mode' => $e->payment_mode ?? '-', 'amount' => (float)$e->amount]);

        $loanQuery = LoanEmiSchedule::with('loan')->whereIn('emi_status', ['Paid','Partial']);
        $this->applyFirmScope($loanQuery, $request, 'firm_id', 'loan');
        $loanRepayments = $loanQuery->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')->get()
            ->map(fn($e) => ['date' => $e->payment_date,
                'particular'   => 'Loan EMI — '.($e->loan?->bank_name ?? '-'),
                'type'         => 'Outflow', 'section' => 'Loan Repayment',
                'payment_mode' => $e->payment_mode ?? '-', 'amount' => (float)$e->paid_amount]);

        $all = $salesPayments->concat($rentalPayments)->concat($expensePayments)
                             ->concat($loanRepayments)->sortBy('date')->values();

        $filename = 'cash-flow-' . date('Y-m-d') . '.csv';
        $headers  = ['Content-Type' => 'text/csv; charset=UTF-8',
                     'Content-Disposition' => 'attachment; filename="'.$filename.'"'];
        $callback = function () use ($all) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($h, ['Date', 'Particular', 'Section', 'Type', 'Payment Mode', 'Amount (₹)']);
            foreach ($all as $row) {
                fputcsv($h, [
                    \Carbon\Carbon::parse($row['date'])->format('d M Y'),
                    $row['particular'], $row['section'], $row['type'],
                    $row['payment_mode'], number_format($row['amount'], 2),
                ]);
            }
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------------
    // Sales Report
    // ---------------------------------------------------------------
    private function getSalesData(Request $request)
    {
        $query = PropertySale::with(['property', 'customer', 'broker', 'firm']);
        $this->applyFirmScope($query, $request);

        if ($request->filled('from_date'))       $query->whereDate('sale_date', '>=', $request->from_date);
        if ($request->filled('to_date'))         $query->whereDate('sale_date', '<=', $request->to_date);
        if ($request->filled('filter_property')) $query->where('property_id', $request->filter_property);
        if ($request->filled('filter_customer')) $query->where('customer_id', $request->filter_customer);
        if ($request->filled('filter_status'))   $query->where('payment_status', $request->filter_status);

        $sales = $query->orderBy('sale_date', 'desc')->get();

        $saleIds = $sales->pluck('id');
        $paymentTotals = Payment::whereIn('property_sale_id', $saleIds)
            ->selectRaw('property_sale_id, SUM(payment_amount) as total_received')
            ->groupBy('property_sale_id')
            ->pluck('total_received', 'property_sale_id');

        $sales->each(function ($s) use ($paymentTotals) {
            $received = (float) ($paymentTotals[$s->id] ?? 0);
            if ($received <= 0 && $s->booking_amount > 0) {
                $received = (float) $s->booking_amount;
            }
            $s->received_amount = $received;
        });

        return $sales;
    }

    public function sales(Request $request)
    {
        $records = $this->getSalesData($request);

        $totalSale     = $records->sum('sale_amount');
        $totalReceived = $records->sum('received_amount');
        $totalPending  = $records->sum('remaining_amount');
        $totalBookings = $records->count();

        $propQuery = Property::orderBy('property_name');
        $custQuery = Customer::orderBy('name');
        $this->applyFirmScope($propQuery, $request);
        $this->applyFirmScope($custQuery, $request);

        $properties = $propQuery->get();
        $customers  = $custQuery->get();
        $firms      = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.reports.sales', compact(
            'records', 'totalSale', 'totalReceived', 'totalPending',
            'totalBookings', 'properties', 'customers', 'firms'
        ));
    }

    public function salesExportPdf(Request $request)
    {
        $records       = $this->getSalesData($request);
        $totalSale     = $records->sum('sale_amount');
        $totalReceived = $records->sum('received_amount');
        $totalPending  = $records->sum('remaining_amount');
        $totalBookings = $records->count();

        return view('admin.reports.sales-pdf', compact(
            'records', 'totalSale', 'totalReceived', 'totalPending', 'totalBookings'
        ));
    }

    public function salesExportExcel(Request $request)
    {
        $records = $this->getSalesData($request);

        $totalSale     = $records->sum('sale_amount');
        $totalReceived = $records->sum('received_amount');
        $totalPending  = $records->sum('remaining_amount');

        $filename = 'sales-report-' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($records, $request, $totalSale, $totalReceived, $totalPending) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($h, ['Delawala Properties & Management - Sales Report']);
            fputcsv($h, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay   = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
                fputcsv($h, ['Date Range', $fromDisplay . ' to ' . $toDisplay]);
            }
            fputcsv($h, []);

            fputcsv($h, ['SUMMARY']);
            fputcsv($h, ['Total Records', $records->count()]);
            fputcsv($h, ['Total Sale Value', number_format($totalSale, 2)]);
            fputcsv($h, ['Total Received', number_format($totalReceived, 2)]);
            fputcsv($h, ['Total Pending', number_format($totalPending, 2)]);
            fputcsv($h, []);

            fputcsv($h, [
                'Sr', 'Sale Date', 'Invoice No', 'Customer', 'Property',
                'Broker', 'Sale Amount', 'Booking Amount',
                'Received Amount', 'Remaining Amount',
                'Payment Status', 'Sale Status',
            ]);

            foreach ($records as $i => $s) {
                fputcsv($h, [
                    $i + 1,
                    $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '-',
                    $s->invoice_no ?? '-',
                    $s->customer?->name ?? '-',
                    $s->property?->property_name ?? '-',
                    $s->broker?->name ?? '-',
                    number_format($s->sale_amount ?? 0, 2),
                    number_format($s->booking_amount ?? 0, 2),
                    number_format($s->received_amount ?? 0, 2),
                    number_format($s->remaining_amount ?? 0, 2),
                    ucfirst($s->payment_status ?? 'pending'),
                    ucfirst($s->sale_status ?? '-'),
                ]);
            }
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------------
    // Payment Report
    // ---------------------------------------------------------------
    private function getPaymentsData(Request $request)
    {
        $query = Payment::with(['propertySale.property', 'customer', 'property', 'firm']);
        $this->applyFirmScope($query, $request);

        if ($request->filled('from_date'))       $query->whereDate('payment_date', '>=', $request->from_date);
        if ($request->filled('to_date'))         $query->whereDate('payment_date', '<=', $request->to_date);
        if ($request->filled('filter_mode'))     $query->where('payment_mode', $request->filter_mode);
        if ($request->filled('filter_status'))   $query->where('status', $request->filter_status);
        if ($request->filled('filter_customer')) $query->where('customer_id', $request->filter_customer);
        if ($request->filled('filter_property')) $query->where('property_id', $request->filter_property);

        return $query->orderBy('payment_date', 'desc')->get();
    }

    public function payments(Request $request)
    {
        $records = $this->getPaymentsData($request);

        $totalReceived     = $records->sum('payment_amount');
        $totalPending      = $records->sum('pending_amount');
        $totalTransactions = $records->count();

        $todayQuery = Payment::whereDate('payment_date', today());
        $this->applyFirmScope($todayQuery, $request);
        $todayCollection = $todayQuery->sum('payment_amount');

        $custQuery = Customer::orderBy('name');
        $propQuery = Property::orderBy('property_name');
        $this->applyFirmScope($custQuery, $request);
        $this->applyFirmScope($propQuery, $request);

        $customers  = $custQuery->get();
        $properties = $propQuery->get();
        $firms      = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.reports.payments', compact(
            'records', 'totalReceived', 'totalPending',
            'totalTransactions', 'todayCollection',
            'customers', 'properties', 'firms'
        ));
    }

    public function paymentsExportPdf(Request $request)
    {
        $records           = $this->getPaymentsData($request);
        $totalReceived     = $records->sum('payment_amount');
        $totalPending      = $records->sum('pending_amount');
        $totalTransactions = $records->count();

        return view('admin.reports.payments-pdf', compact(
            'records', 'totalReceived', 'totalPending', 'totalTransactions'
        ));
    }

    public function paymentsExportExcel(Request $request)
    {
        $records = $this->getPaymentsData($request);

        $totalReceived     = $records->sum('payment_amount');
        $totalPending      = $records->sum('pending_amount');
        $totalTransactions = $records->count();

        $filename = 'payment-report-' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($records, $request, $totalReceived, $totalPending, $totalTransactions) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($h, ['Delawala Properties & Management - Payment Report']);
            fputcsv($h, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay   = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
                fputcsv($h, ['Date Range', $fromDisplay . ' to ' . $toDisplay]);
            }
            fputcsv($h, []);

            fputcsv($h, ['SUMMARY']);
            fputcsv($h, ['Total Transactions', $totalTransactions]);
            fputcsv($h, ['Total Received', number_format($totalReceived, 2)]);
            fputcsv($h, ['Total Pending', number_format($totalPending, 2)]);
            fputcsv($h, []);

            fputcsv($h, [
                'Sr', 'Payment Date', 'Customer', 'Property',
                'Invoice / Booking No', 'Payment Mode', 'Transaction Ref',
                'Paid Amount', 'Pending Amount', 'Status', 'Remarks',
            ]);

            foreach ($records as $i => $p) {
                fputcsv($h, [
                    $i + 1,
                    $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d M Y') : '-',
                    $p->customer?->name ?? '-',
                    $p->propertySale?->property?->property_name ?? '-',
                    $p->propertySale?->invoice_no ?? '-',
                    $p->payment_mode ?? '-',
                    $p->transaction_ref ?? '-',
                    number_format($p->payment_amount, 2),
                    number_format($p->pending_amount, 2),
                    ucfirst($p->status ?? 'pending'),
                    $p->remarks ?? '-',
                ]);
            }
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------------
    // Rental Report
    // ---------------------------------------------------------------
    private function getRentalsData(Request $request)
    {
        $query = RentalPayment::with([
            'rental.property.propertyType',
            'firm',
            'rental.firm',
            'property.propertyType'
        ]);
        $this->applyFirmScope($query, $request, 'firm_id', 'rental');

        if ($request->filled('from_date'))
            $query->whereDate('payment_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('payment_date', '<=', $request->to_date);
        if ($request->filled('filter_status'))
            $query->where('payment_status', $request->filter_status);
        if ($request->filled('filter_mode'))
            $query->where('payment_mode', $request->filter_mode);
        if ($request->filled('filter_tenant')) {
            $search = $request->filter_tenant;
            $query->whereHas('rental', fn($q) =>
                $q->where('tenant_name', 'like', "%{$search}%")
            );
        }
        if ($request->filled('filter_property')) {
            $query->where(function($q) use ($request) {
                $q->where('property_id', $request->filter_property)
                  ->orWhereHas('rental', fn($r) => $r->where('property_id', $request->filter_property));
            });
        }
        if ($request->filled('filter_property_type')) {
            $typeId = $request->filter_property_type;
            $query->where(function($q) use ($typeId) {
                $q->whereHas('property', fn($p) => $p->where('property_type_id', $typeId))
                  ->orWhereHas('rental.property', fn($p) => $p->where('property_type_id', $typeId));
            });
        }

        return $query->orderByDesc('payment_year')
                     ->orderByDesc('payment_month')
                     ->get();
    }

    public function rentals(Request $request)
    {
        $records = $this->getRentalsData($request);

        $totalRentAmt  = $records->sum('rent_amount');
        $totalReceived = $records->sum('paid_amount');
        $totalPending  = $records->sum('pending_amount');

        $activeQuery = Rental::where('rental_status', 'active');
        $propQuery   = Property::orderBy('property_name');
        $this->applyFirmScope($activeQuery, $request);
        $this->applyFirmScope($propQuery, $request);

        $totalActive    = $activeQuery->count();
        $properties     = $propQuery->get();
        $firms          = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $propertyTypes  = PropertyType::where('status', 'active')->orderBy('name')->get();

        return view('admin.reports.rentals', compact(
            'records', 'totalRentAmt', 'totalReceived',
            'totalPending', 'totalActive', 'properties', 'firms', 'propertyTypes'
        ));
    }

    public function rentalsExportPdf(Request $request)
    {
        $records       = $this->getRentalsData($request);
        $totalRentAmt  = $records->sum('rent_amount');
        $totalReceived = $records->sum('paid_amount');
        $totalPending  = $records->sum('pending_amount');

        return view('admin.reports.rentals-pdf', compact(
            'records', 'totalRentAmt', 'totalReceived', 'totalPending'
        ));
    }

    public function rentalsExportExcel(Request $request)
    {
        $records       = $this->getRentalsData($request);
        $totalRentAmt  = $records->sum('rent_amount');
        $totalReceived = $records->sum('paid_amount');
        $totalPending  = $records->sum('pending_amount');

        $filename = 'rental-report-' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($records, $request, $totalRentAmt, $totalReceived, $totalPending) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($h, ['Delawala Properties & Management - Rental Report']);
            fputcsv($h, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay   = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
                fputcsv($h, ['Date Range', $fromDisplay . ' to ' . $toDisplay]);
            }
            fputcsv($h, []);

            fputcsv($h, ['SUMMARY']);
            fputcsv($h, ['Total Records', $records->count()]);
            fputcsv($h, ['Total Rent Amount', number_format($totalRentAmt, 2)]);
            fputcsv($h, ['Total Received', number_format($totalReceived, 2)]);
            fputcsv($h, ['Total Pending', number_format($totalPending, 2)]);
            fputcsv($h, []);

            fputcsv($h, [
                'Sr', 'Firm', 'Payment Date', 'Month/Year', 'Tenant Name',
                'Tenant Mobile', 'Property Name', 'Property Type', 'Property Code', 'Monthly Rent',
                'Paid Amount', 'Pending Amount', 'Payment Mode', 'Status',
            ]);

            foreach ($records as $i => $rp) {
                fputcsv($h, [
                    $i + 1,
                    $rp->firm->firm_name ?? $rp->rental?->firm?->firm_name ?? '-',
                    $rp->payment_date ? \Carbon\Carbon::parse($rp->payment_date)->format('d M Y') : '-',
                    $rp->payment_month . ' ' . $rp->payment_year,
                    $rp->rental?->tenant_name  ?? '-',
                    $rp->rental?->tenant_mobile ?? '-',
                    $rp->property->property_name ?? $rp->rental?->property?->property_name ?? '-',
                    $rp->property->propertyType->name ?? $rp->rental?->property?->propertyType->name ?? '-',
                    $rp->property->property_code ?? $rp->rental?->property?->property_code ?? '-',
                    number_format($rp->rent_amount, 2),
                    number_format($rp->paid_amount, 2),
                    number_format($rp->pending_amount, 2),
                    $rp->payment_mode ?? '-',
                    ucfirst($rp->payment_status ?? 'pending'),
                ]);
            }
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------------
    // Inventory Report
    // ---------------------------------------------------------------
    private function getInventoryData(Request $request)
    {
        $query = Material::with(['materialCategory', 'firm']);
        $this->applyFirmScope($query, $request);

        if ($request->filled('filter_material')) {
            $query->where('material_name', 'like', '%' . $request->filter_material . '%');
        }

        if ($request->filled('filter_category')) {
            $query->where('material_category_id', $request->filter_category);
        }

        if ($request->filled('filter_supplier')) {
            $query->whereHas('stockInwards', function($q) use ($request) {
                $q->where('supplier_name', $request->filter_supplier);
            });
        }

        $materials = $query->orderBy('material_name')->get();

        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate   = $request->filled('to_date') ? $request->to_date : null;

        $materials = $materials->map(function ($m) use ($fromDate, $toDate, $request) {
            $inwardsBefore = 0;
            if ($fromDate) {
                $inwardsBefore = StockInward::where('material_id', $m->id)
                    ->whereDate('inward_date', '<', $fromDate)
                    ->sum('quantity');
            }

            $outwardsBefore = 0;
            if ($fromDate) {
                $outwardsBefore = StockOutward::where('material_id', $m->id)
                    ->whereDate('outward_date', '<', $fromDate)
                    ->sum('quantity');
            }

            $m->computed_opening = (float)$m->opening_stock + (float)$inwardsBefore - (float)$outwardsBefore;

            $inwardQuery = StockInward::where('material_id', $m->id);
            if ($fromDate) $inwardQuery->whereDate('inward_date', '>=', $fromDate);
            if ($toDate)   $inwardQuery->whereDate('inward_date', '<=', $toDate);
            if ($request->filled('filter_supplier')) {
                $inwardQuery->where('supplier_name', $request->filter_supplier);
            }
            $m->computed_inward = (float)$inwardQuery->sum('quantity');

            $outwardQuery = StockOutward::where('material_id', $m->id);
            if ($fromDate) $outwardQuery->whereDate('outward_date', '>=', $fromDate);
            if ($toDate)   $outwardQuery->whereDate('outward_date', '<=', $toDate);
            $m->computed_outward = (float)$outwardQuery->sum('quantity');

            $m->computed_available = $m->computed_opening + $m->computed_inward - $m->computed_outward;

            $latestInwardDate  = StockInward::where('material_id', $m->id)->max('inward_date');
            $latestOutwardDate = StockOutward::where('material_id', $m->id)->max('outward_date');
            
            $dates = array_filter([$latestInwardDate, $latestOutwardDate]);
            if (!empty($dates)) {
                $m->latest_date = \Carbon\Carbon::parse(max($dates))->format('d M Y');
            } else {
                $m->latest_date = $m->created_at->format('d M Y');
            }

            if ($m->computed_available <= 0) {
                $m->stock_status = 'Out of Stock';
            } elseif ($m->computed_available <= ($m->minimum_stock ?? 5)) {
                $m->stock_status = 'Low Stock';
            } else {
                $m->stock_status = 'In Stock';
            }

            return $m;
        });

        if ($request->filled('filter_status')) {
            $statusFilter = $request->filter_status;
            $materials = $materials->filter(function($m) use ($statusFilter) {
                if ($statusFilter === 'in_stock')     return $m->stock_status === 'In Stock';
                if ($statusFilter === 'low_stock')    return $m->stock_status === 'Low Stock';
                if ($statusFilter === 'out_of_stock') return $m->stock_status === 'Out of Stock';
                return true;
            });
        }

        return $materials;
    }

    public function inventory(Request $request)
    {
        $materials = $this->getInventoryData($request);

        $totalMaterials  = $materials->count();
        $totalStockQty   = $materials->sum('computed_available');
        $lowStockItems   = $materials->where('stock_status', 'Low Stock')->count();
        $outOfStockItems = $materials->where('stock_status', 'Out of Stock')->count();

        $catQuery = MaterialCategory::where('status', 'active')->orderBy('category_name');
        $this->applyFirmScope($catQuery, $request);
        $categories = $catQuery->get();

        $suppQuery = StockInward::whereNotNull('supplier_name')
            ->where('supplier_name', '!=', '')
            ->distinct();
        $this->applyFirmScope($suppQuery, $request);
        $suppliers = $suppQuery->orderBy('supplier_name')->pluck('supplier_name');

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.reports.inventory', compact(
            'materials', 'categories', 'suppliers', 'firms',
            'totalMaterials', 'totalStockQty', 'lowStockItems', 'outOfStockItems'
        ));
    }

    public function inventoryExportPdf(Request $request)
    {
        $materials       = $this->getInventoryData($request);
        $totalMaterials  = $materials->count();
        $totalStockQty   = $materials->sum('computed_available');
        $lowStockItems   = $materials->where('stock_status', 'Low Stock')->count();
        $outOfStockItems = $materials->where('stock_status', 'Out of Stock')->count();

        return view('admin.reports.inventory-pdf', compact(
            'materials', 'totalMaterials', 'totalStockQty', 'lowStockItems', 'outOfStockItems'
        ));
    }

    public function inventoryExportExcel(Request $request)
    {
        $materials = $this->getInventoryData($request);
        $filename  = 'inventory-report-' . date('Y-m-d') . '.csv';
        $headers   = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($materials, $request) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($h, ['Delawala Properties & Management - Inventory Report']);
            fputcsv($h, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay   = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
                fputcsv($h, ['Date Range', $fromDisplay . ' to ' . $toDisplay]);
            }
            fputcsv($h, []);

            fputcsv($h, ['SUMMARY']);
            fputcsv($h, ['Total Materials', $materials->count()]);
            fputcsv($h, ['Total Available Stock', number_format($materials->sum('computed_available'), 2)]);
            fputcsv($h, ['Low Stock Items', $materials->where('stock_status', 'Low Stock')->count()]);
            fputcsv($h, ['Out of Stock Items', $materials->where('stock_status', 'Out of Stock')->count()]);
            fputcsv($h, []);

            fputcsv($h, [
                'Sr', 'Material Name', 'Category', 'Unit',
                'Opening Stock', 'Inward Qty', 'Outward Qty',
                'Available Stock', 'Min Stock Level', 'Stock Status', 'Latest Activity',
            ]);

            foreach ($materials as $i => $m) {
                fputcsv($h, [
                    $i + 1,
                    $m->material_name,
                    $m->materialCategory->category_name ?? '-',
                    $m->unit ?? '-',
                    number_format($m->computed_opening, 2),
                    number_format($m->computed_inward, 2),
                    number_format($m->computed_outward, 2),
                    number_format($m->computed_available, 2),
                    number_format($m->minimum_stock ?? 0, 2),
                    $m->stock_status,
                    $m->latest_date ?? '-',
                ]);
            }
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }
}
