<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ReportsController — Main Reports Hub
 *
 * This controller handles the reports landing page and all
 * individual report pages that don't already have a dedicated controller.
 *
 * Reports with dedicated controllers (leave untouched):
 *   - expense-report  → ExpenseReportController
 *   - loan-report     → LoanReportController
 *   - stock-report    → StockReportController
 *
 * New reports handled here:
 *   - GST Sales Report
 *   - GST Purchase Report
 *   - Credit Note
 *   - Debit Note
 *   - Profit & Loss Statement
 *   - Balance Sheet
 *   - Cash Flow Report
 *   - Sales Report       (property-sales based)
 *   - Payment Report     (payments based)
 *   - Rental Report      (rentals based)
 *   - Inventory Report   (materials / stock based)
 */
class ReportsController extends Controller
{
    // ---------------------------------------------------------------
    // Shared — scope to current user's firm
    // ---------------------------------------------------------------
    private function firmId(): int
    {
        return Auth::user()->firm_id;
    }

    // ---------------------------------------------------------------
    // Reports Hub — landing page
    // ---------------------------------------------------------------
    public function index()
    {
        return view('admin.reports.index');
    }

    // ---------------------------------------------------------------
    // GST Reports
    // ---------------------------------------------------------------
    public function gstSales(Request $request)
    {
        $firmId = $this->firmId();

        $query = \App\Models\PropertySale::with(['property', 'customer', 'broker'])
            ->where('firm_id', $firmId);

        // Date filters
        if ($request->filled('from_date')) {
            $query->whereDate('sale_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('sale_date', '<=', $request->to_date);
        }

        // Customer filter
        if ($request->filled('filter_customer')) {
            $query->where('customer_id', $request->filter_customer);
        }

        // Status filter
        if ($request->filled('filter_status')) {
            $query->where('payment_status', $request->filter_status);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();

        // Compute GST totals safely — use existing fields or calculate from sale_amount
        $sales->transform(function ($sale) {
            // If taxable_amount is null, fall back to sale_amount
            $sale->computed_taxable = $sale->taxable_amount ?? $sale->sale_amount ?? 0;
            $sale->computed_cgst    = $sale->cgst_amount  ?? 0;
            $sale->computed_sgst    = $sale->sgst_amount  ?? 0;
            $sale->computed_igst    = $sale->igst_amount  ?? 0;
            $sale->computed_total_gst = $sale->computed_cgst + $sale->computed_sgst + $sale->computed_igst;
            // If grand_total is null, derive it
            $sale->computed_grand_total = $sale->grand_total
                ?? ($sale->computed_taxable + $sale->computed_total_gst);
            return $sale;
        });

        // Summary totals
        $totalInvoices    = $sales->count();
        $totalTaxable     = $sales->sum('computed_taxable');
        $totalCgst        = $sales->sum('computed_cgst');
        $totalSgst        = $sales->sum('computed_sgst');
        $totalIgst        = $sales->sum('computed_igst');
        $totalGst         = $sales->sum('computed_total_gst');
        $grandTotal       = $sales->sum('computed_grand_total');

        $customers = \App\Models\Customer::where('firm_id', $firmId)->orderBy('name')->get();

        return view('admin.reports.gst-sales', compact(
            'sales', 'customers',
            'totalInvoices', 'totalTaxable',
            'totalCgst', 'totalSgst', 'totalIgst',
            'totalGst', 'grandTotal'
        ));
    }

    // ---------------------------------------------------------------
    // GST Sales — shared data helper + PDF / Excel exports
    // ---------------------------------------------------------------
    private function getGstSalesData(Request $request)
    {
        $firmId = $this->firmId();
        $query  = \App\Models\PropertySale::with(['property', 'customer', 'broker'])
            ->where('firm_id', $firmId);

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

    public function gstSalesExportPdf(Request $request)
    {
        $sales      = $this->getGstSalesData($request);
        $totalTaxable = $sales->sum('computed_taxable');
        $totalCgst  = $sales->sum('computed_cgst');
        $totalSgst  = $sales->sum('computed_sgst');
        $totalIgst  = $sales->sum('computed_igst');
        $totalGst   = $sales->sum('computed_total_gst');
        $grandTotal = $sales->sum('computed_grand_total');
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
    // GST Purchase Report — shared data helper + index + PDF + Excel
    // ---------------------------------------------------------------
    private function getGstPurchaseData(Request $request)
    {
        $firmId = $this->firmId();

        $query = \App\Models\Expense::with(['vendor', 'property', 'expenseCategory'])
            ->where('firm_id', $firmId);

        if ($request->filled('from_date'))    $query->whereDate('expense_date', '>=', $request->from_date);
        if ($request->filled('to_date'))      $query->whereDate('expense_date', '<=', $request->to_date);
        if ($request->filled('filter_vendor'))$query->where('vendor_id', $request->filter_vendor);
        if ($request->filled('filter_status'))$query->where('approval_status', $request->filter_status);
        if ($request->filled('filter_category')) $query->where('expense_category_id', $request->filter_category);

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        // Safe GST derivation — fallback to amount if taxable_amount is null
        $expenses->transform(function ($e) {
            $e->computed_taxable    = $e->taxable_amount ?? $e->amount ?? 0;
            $e->computed_cgst       = $e->cgst_amount  ?? 0;
            $e->computed_sgst       = $e->sgst_amount  ?? 0;
            $e->computed_igst       = $e->igst_amount  ?? 0;
            $e->computed_total_gst  = $e->computed_cgst + $e->computed_sgst + $e->computed_igst;
            $e->computed_grand_total = $e->grand_total ?? ($e->computed_taxable + $e->computed_total_gst);
            return $e;
        });

        return $expenses;
    }

    public function gstPurchase(Request $request)
    {
        $firmId   = $this->firmId();
        $expenses = $this->getGstPurchaseData($request);

        $totalBills    = $expenses->count();
        $totalTaxable  = $expenses->sum('computed_taxable');
        $totalCgst     = $expenses->sum('computed_cgst');
        $totalSgst     = $expenses->sum('computed_sgst');
        $totalIgst     = $expenses->sum('computed_igst');
        $totalGst      = $expenses->sum('computed_total_gst');
        $grandTotal    = $expenses->sum('computed_grand_total');

        $vendors    = \App\Models\Vendor::where('firm_id', $firmId)->orderBy('name')->get();
        $categories = \App\Models\ExpenseCategory::where('firm_id', $firmId)
            ->where('status', 'active')->orderBy('name')->get();

        return view('admin.reports.gst-purchase', compact(
            'expenses', 'vendors', 'categories',
            'totalBills', 'totalTaxable',
            'totalCgst', 'totalSgst', 'totalIgst',
            'totalGst', 'grandTotal'
        ));
    }

    public function gstPurchaseExportPdf(Request $request)
    {
        $expenses    = $this->getGstPurchaseData($request);
        $totalTaxable = $expenses->sum('computed_taxable');
        $totalCgst   = $expenses->sum('computed_cgst');
        $totalSgst   = $expenses->sum('computed_sgst');
        $totalIgst   = $expenses->sum('computed_igst');
        $totalGst    = $expenses->sum('computed_total_gst');
        $grandTotal  = $expenses->sum('computed_grand_total');

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
    // Credit Note Report — shared helper + index + PDF + Excel
    // ---------------------------------------------------------------
    private function getCreditNoteData(Request $request)
    {
        $firmId = $this->firmId();
        $query  = \App\Models\CreditNote::with('customer')
            ->where('firm_id', $firmId);

        if ($request->filled('from_date'))       $query->whereDate('credit_note_date', '>=', $request->from_date);
        if ($request->filled('to_date'))         $query->whereDate('credit_note_date', '<=', $request->to_date);
        if ($request->filled('filter_customer')) $query->where('customer_id', $request->filter_customer);
        if ($request->filled('filter_status'))   $query->where('status', $request->filter_status);

        return $query->orderBy('credit_note_date', 'desc')->get();
    }

    public function creditNote(Request $request)
    {
        $firmId  = $this->firmId();
        $notes   = $this->getCreditNoteData($request);

        $totalNotes    = $notes->count();
        $totalTaxable  = $notes->sum('taxable_amount');
        $totalCgst     = $notes->sum('cgst_amount');
        $totalSgst     = $notes->sum('sgst_amount');
        $totalIgst     = $notes->sum('igst_amount');
        $totalGst      = $notes->sum('total_gst');
        $totalCredit   = $notes->sum('credit_amount');

        $customers = \App\Models\Customer::where('firm_id', $firmId)->orderBy('name')->get();

        return view('admin.reports.credit-note', compact(
            'notes', 'customers',
            'totalNotes', 'totalTaxable',
            'totalCgst', 'totalSgst', 'totalIgst',
            'totalGst', 'totalCredit'
        ));
    }

    public function creditNoteExportPdf(Request $request)
    {
        $notes       = $this->getCreditNoteData($request);
        $totalTaxable = $notes->sum('taxable_amount');
        $totalCgst   = $notes->sum('cgst_amount');
        $totalSgst   = $notes->sum('sgst_amount');
        $totalIgst   = $notes->sum('igst_amount');
        $totalGst    = $notes->sum('total_gst');
        $totalCredit = $notes->sum('credit_amount');

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
    // Debit Note Report — shared helper + index + PDF + Excel
    // ---------------------------------------------------------------
    private function getDebitNoteData(Request $request)
    {
        $firmId = $this->firmId();
        $query  = \App\Models\DebitNote::with('vendor')
            ->where('firm_id', $firmId);

        if ($request->filled('from_date'))      $query->whereDate('debit_note_date', '>=', $request->from_date);
        if ($request->filled('to_date'))        $query->whereDate('debit_note_date', '<=', $request->to_date);
        if ($request->filled('filter_vendor'))  $query->where('vendor_id', $request->filter_vendor);
        if ($request->filled('filter_status'))  $query->where('status', $request->filter_status);

        return $query->orderBy('debit_note_date', 'desc')->get();
    }

    public function debitNote(Request $request)
    {
        $firmId = $this->firmId();
        $notes  = $this->getDebitNoteData($request);

        $totalNotes   = $notes->count();
        $totalTaxable = $notes->sum('taxable_amount');
        $totalCgst    = $notes->sum('cgst_amount');
        $totalSgst    = $notes->sum('sgst_amount');
        $totalIgst    = $notes->sum('igst_amount');
        $totalGst     = $notes->sum('total_gst');
        $totalDebit   = $notes->sum('debit_amount');

        $vendors = \App\Models\Vendor::where('firm_id', $firmId)->orderBy('name')->get();

        return view('admin.reports.debit-note', compact(
            'notes', 'vendors',
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
        $firmId   = $this->firmId();
        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate   = $request->filled('to_date')   ? $request->to_date   : null;

        // ── INCOME SECTION ──────────────────────────────────────────

        // 1. Property Sales Income — actual payment_amount received (payments table)
        $salesIncome = \App\Models\Payment::where('firm_id', $firmId)
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->sum('payment_amount');

        // 2. Rental Income — actual paid_amount from rental_payments
        //    rental_payments has no firm_id; join via rentals
        $rentalIncome = \App\Models\RentalPayment::whereHas(
                'rental', fn($q) => $q->where('firm_id', $firmId)
            )
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->sum('paid_amount');

        $totalIncome = $salesIncome + $rentalIncome;

        // ── EXPENSE SECTION ─────────────────────────────────────────

        // 3. Operating Expenses (expenses table)
        $operatingExpense = \App\Models\Expense::where('firm_id', $firmId)
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->sum('amount');

        // 4. Loan EMI paid (interest portion approximated as full EMI paid)
        //    Using loan_emi_schedules.paid_amount where emi_status = Paid/Partial
        $loanEmiPaid = \App\Models\LoanEmiSchedule::whereHas(
                'loan', fn($q) => $q->where('firm_id', $firmId)
            )
            ->whereIn('emi_status', ['Paid', 'Partial'])
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->sum('paid_amount');

        $totalExpense = $operatingExpense + $loanEmiPaid;

        // ── NET ──────────────────────────────────────────────────────
        $netProfitLoss = $totalIncome - $totalExpense;

        // ── DETAIL ROWS for the P&L table ────────────────────────────
        $rows = collect([
            // Income rows
            ['particular' => 'Property Sales Receipts',  'type' => 'income',  'amount' => $salesIncome],
            ['particular' => 'Rental Income Received',   'type' => 'income',  'amount' => $rentalIncome],
            // Expense rows
            ['particular' => 'Operating Expenses',       'type' => 'expense', 'amount' => $operatingExpense],
            ['particular' => 'Loan EMI Payments',        'type' => 'expense', 'amount' => $loanEmiPaid],
        ]);

        // ── Expense category breakdown ───────────────────────────────
        $expenseByCategory = \App\Models\Expense::where('firm_id', $firmId)
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->selectRaw('COALESCE(expense_category, "Uncategorised") as category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return view('admin.reports.profit-loss', compact(
            'salesIncome', 'rentalIncome', 'totalIncome',
            'operatingExpense', 'loanEmiPaid', 'totalExpense',
            'netProfitLoss', 'rows', 'expenseByCategory'
        ));
    }

    // ── P&L Export helpers ───────────────────────────────────────────
    public function profitLossExportPdf(Request $request)
    {
        $firmId   = $this->firmId();
        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate   = $request->filled('to_date')   ? $request->to_date   : null;

        $salesIncome = \App\Models\Payment::where('firm_id', $firmId)
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->sum('payment_amount');

        $rentalIncome = \App\Models\RentalPayment::whereHas(
                'rental', fn($q) => $q->where('firm_id', $firmId)
            )
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->sum('paid_amount');

        $totalIncome = $salesIncome + $rentalIncome;

        $operatingExpense = \App\Models\Expense::where('firm_id', $firmId)
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->sum('amount');

        $loanEmiPaid = \App\Models\LoanEmiSchedule::whereHas(
                'loan', fn($q) => $q->where('firm_id', $firmId)
            )
            ->whereIn('emi_status', ['Paid', 'Partial'])
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->sum('paid_amount');

        $totalExpense = $operatingExpense + $loanEmiPaid;
        $netProfitLoss = $totalIncome - $totalExpense;

        $expenseByCategory = \App\Models\Expense::where('firm_id', $firmId)
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->selectRaw('COALESCE(expense_category, "Uncategorised") as category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return view('admin.reports.profit-loss-pdf', compact(
            'salesIncome', 'rentalIncome', 'totalIncome',
            'operatingExpense', 'loanEmiPaid', 'totalExpense',
            'netProfitLoss', 'expenseByCategory'
        ));
    }

    public function profitLossExportExcel(Request $request)
    {
        $firmId   = $this->firmId();
        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate   = $request->filled('to_date')   ? $request->to_date   : null;

        $salesIncome = \App\Models\Payment::where('firm_id', $firmId)
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->sum('payment_amount');

        $rentalIncome = \App\Models\RentalPayment::whereHas(
                'rental', fn($q) => $q->where('firm_id', $firmId)
            )
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->sum('paid_amount');

        $operatingExpense = \App\Models\Expense::where('firm_id', $firmId)
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->sum('amount');

        $loanEmiPaid = \App\Models\LoanEmiSchedule::whereHas(
                'loan', fn($q) => $q->where('firm_id', $firmId)
            )
            ->whereIn('emi_status', ['Paid', 'Partial'])
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->sum('paid_amount');

        $totalIncome   = $salesIncome + $rentalIncome;
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
            fputcsv($h, ['Property Sales Receipts', 'Income',  number_format($salesIncome, 2)]);
            fputcsv($h, ['Rental Income Received',  'Income',  number_format($rentalIncome, 2)]);
            fputcsv($h, ['Total Income',            'TOTAL',   number_format($totalIncome, 2)]);
            fputcsv($h, ['']);
            fputcsv($h, ['Operating Expenses',      'Expense', number_format($operatingExpense, 2)]);
            fputcsv($h, ['Loan EMI Payments',        'Expense', number_format($loanEmiPaid, 2)]);
            fputcsv($h, ['Total Expenses',           'TOTAL',   number_format($totalExpense, 2)]);
            fputcsv($h, ['']);
            fputcsv($h, ['Net ' . ($net >= 0 ? 'Profit' : 'Loss'), 'NET', number_format(abs($net), 2)]);
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function balanceSheet(Request $request)
    {
        $firmId  = $this->firmId();
        $asOnDate = $request->filled('as_on_date') ? $request->as_on_date : null;

        // ── ASSETS ────────────────────────────────────────────────────

        // 1. Cash / Bank — total payment_amount received up to as_on_date
        $cashReceived = \App\Models\Payment::where('firm_id', $firmId)
            ->when($asOnDate, fn($q) => $q->whereDate('payment_date', '<=', $asOnDate))
            ->sum('payment_amount');

        // 2. Rental income collected up to as_on_date
        $rentalCashReceived = \App\Models\RentalPayment::whereHas(
                'rental', fn($q) => $q->where('firm_id', $firmId)
            )
            ->when($asOnDate, fn($q) => $q->whereDate('payment_date', '<=', $asOnDate))
            ->sum('paid_amount');

        // 3. Receivables — remaining_amount on property sales (what customers still owe)
        $receivables = \App\Models\PropertySale::where('firm_id', $firmId)
            ->when($asOnDate, fn($q) => $q->whereDate('sale_date', '<=', $asOnDate))
            ->whereIn('payment_status', ['pending', 'partial'])
            ->sum('remaining_amount');

        // 4. Property value — unsold properties at listed price
        $propertyValue = \App\Models\Property::where('firm_id', $firmId)
            ->whereIn('status', ['available', 'booked'])
            ->sum('price');

        // 5. Security deposits held (rental deposits)
        $securityDeposits = \App\Models\Rental::where('firm_id', $firmId)
            ->where('rental_status', 'active')
            ->when($asOnDate, fn($q) => $q->whereDate('rent_start_date', '<=', $asOnDate))
            ->sum('security_deposit');

        $totalAssets = $cashReceived + $rentalCashReceived + $receivables
                     + $propertyValue + $securityDeposits;

        // ── LIABILITIES ───────────────────────────────────────────────

        // 6. Outstanding loan principal
        $loanOutstanding = \App\Models\Loan::where('firm_id', $firmId)
            ->when($asOnDate, fn($q) => $q->whereDate('loan_start_date', '<=', $asOnDate))
            ->sum('pending_amount');

        // 7. Unpaid vendor expenses (expenses that are still pending approval / unpaid)
        $unpaidExpenses = \App\Models\Expense::where('firm_id', $firmId)
            ->when($asOnDate, fn($q) => $q->whereDate('expense_date', '<=', $asOnDate))
            ->where('approval_status', 'Pending')
            ->sum('amount');

        // 8. Credit notes issued (amounts owed back to customers)
        $creditNotePayable = \App\Models\CreditNote::where('firm_id', $firmId)
            ->when($asOnDate, fn($q) => $q->whereDate('credit_note_date', '<=', $asOnDate))
            ->whereIn('status', ['Pending', 'Approved'])
            ->sum('credit_amount');

        $totalLiabilities = $loanOutstanding + $unpaidExpenses + $creditNotePayable;

        // ── EQUITY ────────────────────────────────────────────────────
        $netWorth = $totalAssets - $totalLiabilities;

        // ── For display in controller ─────────────────────────────────
        $loanTotal = \App\Models\Loan::where('firm_id', $firmId)->sum('loan_amount');
        $loanPaid  = \App\Models\Loan::where('firm_id', $firmId)->sum('paid_amount');

        return view('admin.reports.balance-sheet', compact(
            // Assets
            'cashReceived', 'rentalCashReceived', 'receivables',
            'propertyValue', 'securityDeposits', 'totalAssets',
            // Liabilities
            'loanOutstanding', 'unpaidExpenses', 'creditNotePayable',
            'loanTotal', 'loanPaid', 'totalLiabilities',
            // Equity
            'netWorth'
        ));
    }

    public function balanceSheetExportExcel(Request $request)
    {
        $firmId   = $this->firmId();
        $asOnDate = $request->filled('as_on_date') ? $request->as_on_date : null;

        $cashReceived       = \App\Models\Payment::where('firm_id', $firmId)
            ->when($asOnDate, fn($q) => $q->whereDate('payment_date', '<=', $asOnDate))->sum('payment_amount');
        $rentalCashReceived = \App\Models\RentalPayment::whereHas('rental', fn($q) => $q->where('firm_id', $firmId))
            ->when($asOnDate, fn($q) => $q->whereDate('payment_date', '<=', $asOnDate))->sum('paid_amount');
        $receivables        = \App\Models\PropertySale::where('firm_id', $firmId)
            ->when($asOnDate, fn($q) => $q->whereDate('sale_date', '<=', $asOnDate))
            ->whereIn('payment_status', ['pending','partial'])->sum('remaining_amount');
        $propertyValue      = \App\Models\Property::where('firm_id', $firmId)->whereIn('status',['available','booked'])->sum('price');
        $securityDeposits   = \App\Models\Rental::where('firm_id', $firmId)->where('rental_status','active')
            ->when($asOnDate, fn($q) => $q->whereDate('rent_start_date', '<=', $asOnDate))->sum('security_deposit');
        $totalAssets        = $cashReceived + $rentalCashReceived + $receivables + $propertyValue + $securityDeposits;

        $loanOutstanding    = \App\Models\Loan::where('firm_id', $firmId)
            ->when($asOnDate, fn($q) => $q->whereDate('loan_start_date', '<=', $asOnDate))->sum('pending_amount');
        $unpaidExpenses     = \App\Models\Expense::where('firm_id', $firmId)
            ->when($asOnDate, fn($q) => $q->whereDate('expense_date', '<=', $asOnDate))
            ->where('approval_status','Pending')->sum('amount');
        $creditNotePayable  = \App\Models\CreditNote::where('firm_id', $firmId)
            ->when($asOnDate, fn($q) => $q->whereDate('credit_note_date', '<=', $asOnDate))
            ->whereIn('status',['Pending','Approved'])->sum('credit_amount');
        $totalLiabilities   = $loanOutstanding + $unpaidExpenses + $creditNotePayable;
        $netWorth           = $totalAssets - $totalLiabilities;

        $filename = 'balance-sheet-' . ($asOnDate ?? date('Y-m-d')) . '.csv';
        $headers  = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="'.$filename.'"'];
        $callback = function () use ($cashReceived, $rentalCashReceived, $receivables, $propertyValue,
                                     $securityDeposits, $totalAssets, $loanOutstanding, $unpaidExpenses,
                                     $creditNotePayable, $totalLiabilities, $netWorth) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($h, ['Particular', 'Category', 'Amount (₹)']);
            fputcsv($h, ['Cash / Bank Receipts (Sales)',  'Asset', number_format($cashReceived, 2)]);
            fputcsv($h, ['Rental Income Collected',       'Asset', number_format($rentalCashReceived, 2)]);
            fputcsv($h, ['Receivables (Pending Sales)',   'Asset', number_format($receivables, 2)]);
            fputcsv($h, ['Property Value (Unsold)',       'Asset', number_format($propertyValue, 2)]);
            fputcsv($h, ['Security Deposits Held',        'Asset', number_format($securityDeposits, 2)]);
            fputcsv($h, ['TOTAL ASSETS',                  'TOTAL', number_format($totalAssets, 2)]);
            fputcsv($h, ['']);
            fputcsv($h, ['Outstanding Loan Balance',      'Liability', number_format($loanOutstanding, 2)]);
            fputcsv($h, ['Unpaid / Pending Expenses',     'Liability', number_format($unpaidExpenses, 2)]);
            fputcsv($h, ['Credit Notes Payable',          'Liability', number_format($creditNotePayable, 2)]);
            fputcsv($h, ['TOTAL LIABILITIES',             'TOTAL',     number_format($totalLiabilities, 2)]);
            fputcsv($h, ['']);
            fputcsv($h, ['NET WORTH (EQUITY)', 'NET', number_format($netWorth, 2)]);
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function cashFlow(Request $request)
    {
        $firmId   = $this->firmId();
        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate   = $request->filled('to_date')   ? $request->to_date   : null;

        // ── INFLOW TRANSACTIONS ─────────────────────────────────────

        // 1. Property sale payments received (payments table)
        $salesPayments = \App\Models\Payment::with(['customer', 'propertySale.property'])
            ->where('firm_id', $firmId)
            ->whereNotNull('payment_date')
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

        // 2. Rental payments received (rental_payments table)
        $rentalPayments = \App\Models\RentalPayment::with(['rental.property'])
            ->whereHas('rental', fn($q) => $q->where('firm_id', $firmId))
            ->whereNotNull('payment_date')
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

        // ── OUTFLOW TRANSACTIONS ────────────────────────────────────

        // 3. Expenses paid (expenses table)
        $expensePayments = \App\Models\Expense::where('firm_id', $firmId)
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->orderBy('expense_date')
            ->get()
            ->map(fn($e) => [
                'date'         => $e->expense_date,
                'particular'   => $e->expense_title
                                 . ($e->expense_category ? ' (' . $e->expense_category . ')' : ''),
                'type'         => 'outflow',
                'section'      => 'Expenses Paid',
                'payment_mode' => $e->payment_mode ?? '—',
                'amount'       => (float) $e->amount,
            ]);

        // 4. Loan EMI repayments paid (loan_emi_schedules table)
        $loanRepayments = \App\Models\LoanEmiSchedule::with('loan')
            ->whereHas('loan', fn($q) => $q->where('firm_id', $firmId))
            ->whereIn('emi_status', ['Paid', 'Partial'])
            ->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')
            ->get()
            ->map(fn($e) => [
                'date'         => $e->payment_date,
                'particular'   => 'Loan EMI — ' . ($e->loan?->bank_name ?? 'Bank')
                                 . ' (' . $e->emi_month . '/' . $e->emi_year . ')',
                'type'         => 'outflow',
                'section'      => 'Loan Repayment',
                'payment_mode' => $e->payment_mode ?? '—',
                'amount'       => (float) $e->paid_amount,
            ]);

        // ── MERGE & SORT all transactions by date ───────────────────
        $allTransactions = $salesPayments
            ->concat($rentalPayments)
            ->concat($expensePayments)
            ->concat($loanRepayments)
            ->sortBy('date')
            ->values();

        // ── SUMMARY TOTALS ──────────────────────────────────────────
        $totalSalesInflow  = $salesPayments->sum('amount');
        $totalRentalInflow = $rentalPayments->sum('amount');
        $totalInflow       = $totalSalesInflow + $totalRentalInflow;

        $totalExpenseOutflow = $expensePayments->sum('amount');
        $totalLoanOutflow    = $loanRepayments->sum('amount');
        $totalOutflow        = $totalExpenseOutflow + $totalLoanOutflow;

        $netCashFlow = $totalInflow - $totalOutflow;

        // ── MONTHLY SUMMARY for chart-style table ───────────────────
        $monthlyRows = $allTransactions
            ->groupBy(fn($t) => substr($t['date'], 0, 7)) // YYYY-MM
            ->map(function ($group, $month) {
                $in  = $group->where('type', 'inflow')->sum('amount');
                $out = $group->where('type', 'outflow')->sum('amount');
                return ['month' => $month, 'inflow' => $in, 'outflow' => $out, 'net' => $in - $out];
            })
            ->sortKeys()
            ->values();

        return view('admin.reports.cash-flow', compact(
            'allTransactions',
            'totalSalesInflow', 'totalRentalInflow', 'totalInflow',
            'totalExpenseOutflow', 'totalLoanOutflow', 'totalOutflow',
            'netCashFlow', 'monthlyRows'
        ));
    }

    // ── Cash Flow Export Excel ──────────────────────────────────────
    public function cashFlowExportExcel(Request $request)
    {
        $firmId   = $this->firmId();
        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate   = $request->filled('to_date')   ? $request->to_date   : null;

        $salesPayments = \App\Models\Payment::with(['customer','propertySale.property'])
            ->where('firm_id', $firmId)->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')->get()
            ->map(fn($p) => ['date' => $p->payment_date,
                'particular'   => 'Sales Receipt — '.($p->customer?->name ?? '-'),
                'type'         => 'Inflow', 'section' => 'Sales Payment Received',
                'payment_mode' => $p->payment_mode ?? '-', 'amount' => (float)$p->payment_amount]);

        $rentalPayments = \App\Models\RentalPayment::with(['rental'])
            ->whereHas('rental', fn($q) => $q->where('firm_id', $firmId))
            ->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')->get()
            ->map(fn($r) => ['date' => $r->payment_date,
                'particular'   => 'Rental Income — '.($r->rental?->tenant_name ?? '-'),
                'type'         => 'Inflow', 'section' => 'Rental Payment Received',
                'payment_mode' => $r->payment_mode ?? '-', 'amount' => (float)$r->paid_amount]);

        $expensePayments = \App\Models\Expense::where('firm_id', $firmId)
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate,   fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->orderBy('expense_date')->get()
            ->map(fn($e) => ['date' => $e->expense_date,
                'particular'   => $e->expense_title, 'type' => 'Outflow',
                'section'      => 'Expenses Paid',
                'payment_mode' => $e->payment_mode ?? '-', 'amount' => (float)$e->amount]);

        $loanRepayments = \App\Models\LoanEmiSchedule::with('loan')
            ->whereHas('loan', fn($q) => $q->where('firm_id', $firmId))
            ->whereIn('emi_status', ['Paid','Partial'])->whereNotNull('payment_date')
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
    // Sales Report  (property-sales based)
    // ---------------------------------------------------------------
    // Sales Report
    // ---------------------------------------------------------------
    private function getSalesData(Request $request)
    {
        $firmId = $this->firmId();
        $query  = \App\Models\PropertySale::with(['property', 'customer', 'broker'])
            ->where('firm_id', $firmId);

        if ($request->filled('from_date'))       $query->whereDate('sale_date', '>=', $request->from_date);
        if ($request->filled('to_date'))         $query->whereDate('sale_date', '<=', $request->to_date);
        if ($request->filled('filter_property')) $query->where('property_id', $request->filter_property);
        if ($request->filled('filter_customer')) $query->where('customer_id', $request->filter_customer);
        if ($request->filled('filter_status'))   $query->where('payment_status', $request->filter_status);

        return $query->orderBy('sale_date', 'desc')->get();
    }

    public function sales(Request $request)
    {
        $firmId  = $this->firmId();
        $records = $this->getSalesData($request);

        // Preload payment totals for all sales in ONE query — avoids N+1
        $saleIds        = $records->pluck('id');
        $paymentTotals  = \App\Models\Payment::whereIn('property_sale_id', $saleIds)
            ->selectRaw('property_sale_id, SUM(payment_amount) as total_received')
            ->groupBy('property_sale_id')
            ->pluck('total_received', 'property_sale_id');

        // Attach received to each record as a computed attribute
        $records->each(function ($s) use ($paymentTotals) {
            $s->received_amount = (float) ($paymentTotals[$s->id] ?? 0);
        });

        $totalSale      = $records->sum('sale_amount');
        $totalReceived  = $records->sum('received_amount');
        $totalPending   = $records->sum('remaining_amount');
        $totalBookings  = $records->count();

        $properties = \App\Models\Property::where('firm_id', $firmId)->orderBy('property_name')->get();
        $customers  = \App\Models\Customer::where('firm_id', $firmId)->orderBy('name')->get();

        return view('admin.reports.sales', compact(
            'records', 'totalSale', 'totalReceived', 'totalPending',
            'totalBookings', 'properties', 'customers'
        ));
    }

    public function salesExportPdf(Request $request)
    {
        $records  = $this->getSalesData($request);

        $saleIds       = $records->pluck('id');
        $paymentTotals = \App\Models\Payment::whereIn('property_sale_id', $saleIds)
            ->selectRaw('property_sale_id, SUM(payment_amount) as total_received')
            ->groupBy('property_sale_id')
            ->pluck('total_received', 'property_sale_id');

        $records->each(function ($s) use ($paymentTotals) {
            $s->received_amount = (float) ($paymentTotals[$s->id] ?? 0);
        });

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
        $records  = $this->getSalesData($request);

        // Preload payment totals in one query
        $saleIds       = $records->pluck('id');
        $paymentTotals = \App\Models\Payment::whereIn('property_sale_id', $saleIds)
            ->selectRaw('property_sale_id, SUM(payment_amount) as total_received')
            ->groupBy('property_sale_id')
            ->pluck('total_received', 'property_sale_id');

        $records->each(function ($s) use ($paymentTotals) {
            $s->received_amount = (float) ($paymentTotals[$s->id] ?? 0);
        });

        $totalSale     = $records->sum('sale_amount');
        $totalReceived = $records->sum('received_amount');
        $totalPending  = $records->sum('remaining_amount');

        $filename = 'sales-report-' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($records, $paymentTotals, $request, $totalSale, $totalReceived, $totalPending) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Report Header
            fputcsv($h, ['Delawala Properties & Management - Sales Report']);
            fputcsv($h, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
                fputcsv($h, ['Date Range', $fromDisplay . ' to ' . $toDisplay]);
            }
            fputcsv($h, []); // Blank row

            // Summary Section
            fputcsv($h, ['SUMMARY']);
            fputcsv($h, ['Total Bookings', $records->count()]);
            fputcsv($h, ['Total Sale Value', number_format($totalSale, 2)]);
            fputcsv($h, ['Total Received', number_format($totalReceived, 2)]);
            fputcsv($h, ['Total Pending', number_format($totalPending, 2)]);
            fputcsv($h, []); // Blank row

            // Data Header
            fputcsv($h, [
                'Sr', 'Sale Date', 'Invoice No', 'Customer', 'Property',
                'Broker', 'Sale Amount', 'Booking Amount',
                'Received Amount', 'Remaining Amount',
                'Payment Status', 'Sale Status',
            ]);

            // Data Rows
            foreach ($records as $i => $s) {
                $received = (float) ($paymentTotals[$s->id] ?? 0);
                $pending  = $s->remaining_amount ?? max(0, ($s->sale_amount ?? 0) - $received);
                fputcsv($h, [
                    $i + 1,
                    $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '-',
                    $s->invoice_no ?? '-',
                    $s->customer?->name ?? '-',
                    $s->property?->property_name ?? '-',
                    $s->broker?->name ?? '-',
                    number_format($s->sale_amount ?? 0, 2),
                    number_format($s->booking_amount ?? 0, 2),
                    number_format($received, 2),
                    number_format($pending, 2),
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
        $firmId = $this->firmId();
        $query  = \App\Models\Payment::with(['propertySale.property', 'customer'])
            ->where('firm_id', $firmId);

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
        $firmId  = $this->firmId();
        $records = $this->getPaymentsData($request);

        $totalReceived    = $records->sum('payment_amount');
        $totalPending     = $records->sum('pending_amount');
        $totalTransactions= $records->count();
        $todayCollection  = \App\Models\Payment::where('firm_id', $firmId)
            ->whereDate('payment_date', today())
            ->sum('payment_amount');

        $customers  = \App\Models\Customer::where('firm_id', $firmId)->orderBy('name')->get();
        $properties = \App\Models\Property::where('firm_id', $firmId)->orderBy('property_name')->get();

        return view('admin.reports.payments', compact(
            'records', 'totalReceived', 'totalPending',
            'totalTransactions', 'todayCollection',
            'customers', 'properties'
        ));
    }

    public function paymentsExportPdf(Request $request)
    {
        $records          = $this->getPaymentsData($request);
        $totalReceived    = $records->sum('payment_amount');
        $totalPending     = $records->sum('pending_amount');
        $totalTransactions= $records->count();

        return view('admin.reports.payments-pdf', compact(
            'records', 'totalReceived', 'totalPending', 'totalTransactions'
        ));
    }

    public function paymentsExportExcel(Request $request)
    {
        $records  = $this->getPaymentsData($request);

        $totalReceived    = $records->sum('payment_amount');
        $totalPending     = $records->sum('pending_amount');
        $totalTransactions = $records->count();

        $filename = 'payment-report-' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($records, $request, $totalReceived, $totalPending, $totalTransactions) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Report Header
            fputcsv($h, ['Delawala Properties & Management - Payment Report']);
            fputcsv($h, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
                fputcsv($h, ['Date Range', $fromDisplay . ' to ' . $toDisplay]);
            }
            fputcsv($h, []); // Blank row

            // Summary Section
            fputcsv($h, ['SUMMARY']);
            fputcsv($h, ['Total Transactions', $totalTransactions]);
            fputcsv($h, ['Total Received', number_format($totalReceived, 2)]);
            fputcsv($h, ['Total Pending', number_format($totalPending, 2)]);
            fputcsv($h, []); // Blank row

            // Data Header
            fputcsv($h, [
                'Sr', 'Payment Date', 'Customer', 'Property',
                'Invoice / Booking No', 'Payment Mode', 'Transaction Ref',
                'Paid Amount', 'Pending Amount', 'Status', 'Remarks',
            ]);

            // Data Rows
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
        $firmId = $this->firmId();

        // Base: rental_payments joined with rental → property
        $query = \App\Models\RentalPayment::with(['rental.property'])
            ->whereHas('rental', fn($q) => $q->where('firm_id', $firmId));

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
            $query->whereHas('rental', fn($q) =>
                $q->where('property_id', $request->filter_property)
            );
        }

        return $query->orderByDesc('payment_year')
                     ->orderByDesc('payment_month')
                     ->get();
    }

    public function rentals(Request $request)
    {
        $firmId = $this->firmId();

        $records      = $this->getRentalsData($request);

        $totalRentAmt = $records->sum('rent_amount');
        $totalReceived= $records->sum('paid_amount');
        $totalPending = $records->sum('pending_amount');
        $totalActive  = \App\Models\Rental::where('firm_id', $firmId)
            ->where('rental_status', 'active')->count();

        $properties = \App\Models\Property::where('firm_id', $firmId)->orderBy('property_name')->get();

        return view('admin.reports.rentals', compact(
            'records', 'totalRentAmt', 'totalReceived',
            'totalPending', 'totalActive', 'properties'
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
        $records  = $this->getRentalsData($request);

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

            // Report Header
            fputcsv($h, ['Delawala Properties & Management - Rental Report']);
            fputcsv($h, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
                fputcsv($h, ['Date Range', $fromDisplay . ' to ' . $toDisplay]);
            }
            fputcsv($h, []); // Blank row

            // Summary Section
            fputcsv($h, ['SUMMARY']);
            fputcsv($h, ['Total Records', $records->count()]);
            fputcsv($h, ['Total Rent Amount', number_format($totalRentAmt, 2)]);
            fputcsv($h, ['Total Received', number_format($totalReceived, 2)]);
            fputcsv($h, ['Total Pending', number_format($totalPending, 2)]);
            fputcsv($h, []); // Blank row

            // Data Header
            fputcsv($h, [
                'Sr', 'Payment Date', 'Month/Year', 'Tenant Name',
                'Tenant Mobile', 'Property', 'Monthly Rent',
                'Paid Amount', 'Pending Amount', 'Payment Mode', 'Status',
            ]);

            // Data Rows
            foreach ($records as $i => $rp) {
                fputcsv($h, [
                    $i + 1,
                    $rp->payment_date
                        ? \Carbon\Carbon::parse($rp->payment_date)->format('d M Y')
                        : '-',
                    $rp->payment_month . ' ' . $rp->payment_year,
                    $rp->rental?->tenant_name  ?? '-',
                    $rp->rental?->tenant_mobile ?? '-',
                    $rp->rental?->property?->property_name ?? '-',
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
        $firmId = $this->firmId();

        $query = \App\Models\Material::with('materialCategory')
            ->where('firm_id', $firmId);

        // Filter: Material / Item Name
        if ($request->filled('filter_material')) {
            $query->where('material_name', 'like', '%' . $request->filter_material . '%');
        }

        // Filter: Category
        if ($request->filled('filter_category')) {
            $query->where('material_category_id', $request->filter_category);
        }

        // Filter: Supplier / Vendor Name
        if ($request->filled('filter_supplier')) {
            $query->whereHas('stockInwards', function($q) use ($request) {
                $q->where('supplier_name', $request->filter_supplier);
            });
        }

        $materials = $query->orderBy('material_name')->get();

        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate = $request->filled('to_date') ? $request->to_date : null;

        $materials = $materials->map(function ($m) use ($fromDate, $toDate, $request) {
            // Inwards before from_date
            $inwardsBefore = 0;
            if ($fromDate) {
                $inwardsBefore = \App\Models\StockInward::where('material_id', $m->id)
                    ->whereDate('inward_date', '<', $fromDate)
                    ->sum('quantity');
            }

            // Outwards before from_date
            $outwardsBefore = 0;
            if ($fromDate) {
                $outwardsBefore = \App\Models\StockOutward::where('material_id', $m->id)
                    ->whereDate('outward_date', '<', $fromDate)
                    ->sum('quantity');
            }

            // Opening stock at the start of the date range
            $m->computed_opening = (float)$m->opening_stock + (float)$inwardsBefore - (float)$outwardsBefore;

            // Inwards within range
            $inwardQuery = \App\Models\StockInward::where('material_id', $m->id);
            if ($fromDate) {
                $inwardQuery->whereDate('inward_date', '>=', $fromDate);
            }
            if ($toDate) {
                $inwardQuery->whereDate('inward_date', '<=', $toDate);
            }
            if ($request->filled('filter_supplier')) {
                $inwardQuery->where('supplier_name', $request->filter_supplier);
            }
            $m->computed_inward = (float)$inwardQuery->sum('quantity');

            // Outwards within range
            $outwardQuery = \App\Models\StockOutward::where('material_id', $m->id);
            if ($fromDate) {
                $outwardQuery->whereDate('outward_date', '>=', $fromDate);
            }
            if ($toDate) {
                $outwardQuery->whereDate('outward_date', '<=', $toDate);
            }
            $m->computed_outward = (float)$outwardQuery->sum('quantity');

            // Available stock at the end of range
            $m->computed_available = $m->computed_opening + $m->computed_inward - $m->computed_outward;

            // Latest Activity Date
            $latestInwardDate = \App\Models\StockInward::where('material_id', $m->id)->max('inward_date');
            $latestOutwardDate = \App\Models\StockOutward::where('material_id', $m->id)->max('outward_date');
            
            $dates = array_filter([$latestInwardDate, $latestOutwardDate]);
            if (!empty($dates)) {
                $m->latest_date = \Carbon\Carbon::parse(max($dates))->format('d M Y');
            } else {
                $m->latest_date = $m->created_at->format('d M Y');
            }

            // Status determined by Available Stock
            if ($m->computed_available <= 0) {
                $m->stock_status = 'Out of Stock';
            } elseif ($m->computed_available <= ($m->minimum_stock ?? 5)) {
                $m->stock_status = 'Low Stock';
            } else {
                $m->stock_status = 'In Stock';
            }

            return $m;
        });

        // Filter by Stock Status
        if ($request->filled('filter_status')) {
            $statusFilter = $request->filter_status;
            $materials = $materials->filter(function($m) use ($statusFilter) {
                if ($statusFilter === 'in_stock') return $m->stock_status === 'In Stock';
                if ($statusFilter === 'low_stock') return $m->stock_status === 'Low Stock';
                if ($statusFilter === 'out_of_stock') return $m->stock_status === 'Out of Stock';
                return true;
            });
        }

        return $materials;
    }

    public function inventory(Request $request)
    {
        $firmId = $this->firmId();
        $materials = $this->getInventoryData($request);

        $totalMaterials = $materials->count();
        $totalStockQty  = $materials->sum('computed_available');
        $lowStockItems  = $materials->where('stock_status', 'Low Stock')->count();
        $outOfStockItems = $materials->where('stock_status', 'Out of Stock')->count();

        $categories = \App\Models\MaterialCategory::where('firm_id', $firmId)
            ->where('status', 'active')
            ->orderBy('category_name')
            ->get();

        $suppliers = \App\Models\StockInward::where('firm_id', $firmId)
            ->whereNotNull('supplier_name')
            ->where('supplier_name', '!=', '')
            ->distinct()
            ->orderBy('supplier_name')
            ->pluck('supplier_name');

        return view('admin.reports.inventory', compact(
            'materials', 'categories', 'suppliers',
            'totalMaterials', 'totalStockQty', 'lowStockItems', 'outOfStockItems'
        ));
    }

    public function inventoryExportPdf(Request $request)
    {
        $materials = $this->getInventoryData($request);
        
        $totalMaterials = $materials->count();
        $totalStockQty  = $materials->sum('computed_available');
        $lowStockItems  = $materials->where('stock_status', 'Low Stock')->count();
        $outOfStockItems = $materials->where('stock_status', 'Out of Stock')->count();

        return view('admin.reports.inventory-pdf', compact(
            'materials', 'totalMaterials', 'totalStockQty', 'lowStockItems', 'outOfStockItems'
        ));
    }

    public function inventoryExportExcel(Request $request)
    {
        $materials = $this->getInventoryData($request);
        $filename  = 'inventory-report-' . date('Y-m-d') . '.csv';

        $totalMaterials  = $materials->count();
        $totalStockQty   = $materials->sum('computed_available');
        $lowStockItems   = $materials->where('stock_status', 'Low Stock')->count();
        $outOfStockItems = $materials->where('stock_status', 'Out of Stock')->count();

        $headers   = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($materials, $request, $totalMaterials, $totalStockQty, $lowStockItems, $outOfStockItems) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Report Header
            fputcsv($h, ['Delawala Properties & Management - Inventory Report']);
            fputcsv($h, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
                fputcsv($h, ['Date Range', $fromDisplay . ' to ' . $toDisplay]);
            }
            fputcsv($h, []); // Blank row

            // Summary Section
            fputcsv($h, ['SUMMARY']);
            fputcsv($h, ['Total Materials', $totalMaterials]);
            fputcsv($h, ['Total Stock Qty', number_format($totalStockQty, 2)]);
            fputcsv($h, ['Low Stock Items', $lowStockItems]);
            fputcsv($h, ['Out of Stock Items', $outOfStockItems]);
            fputcsv($h, []); // Blank row

            // Data Header
            fputcsv($h, [
                'Sr', 'Last Activity Date', 'Material / Item Name', 'Category',
                'Opening Stock', 'Stock In', 'Stock Out',
                'Available Stock', 'Stock Status', 'Unit'
            ]);

            // Data Rows
            foreach ($materials as $i => $m) {
                fputcsv($h, [
                    $i + 1,
                    $m->latest_date,
                    $m->material_name,
                    $m->materialCategory?->category_name ?? '-',
                    number_format($m->computed_opening, 3),
                    number_format($m->computed_inward, 3),
                    number_format($m->computed_outward, 3),
                    number_format($m->computed_available, 3),
                    $m->stock_status,
                    $m->unit ?? '-'
                ]);
            }
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }
}
