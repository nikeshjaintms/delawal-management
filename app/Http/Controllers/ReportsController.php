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
use App\Models\Project;
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

    private function getAvailableProjects(Request $request)
    {
        $projQuery = Project::orderBy('project_name');
        $this->applyFirmScope($projQuery, $request);
        return $projQuery->get();
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

        if ($request->filled('from_date'))
            $query->whereDate('sale_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('sale_date', '<=', $request->to_date);
        if ($request->filled('filter_customer'))
            $query->where('customer_id', $request->filter_customer);
        if ($request->filled('filter_status'))
            $query->where('payment_status', $request->filter_status);
        if ($request->filled('filter_project')) {
            $projectId = $request->filter_project;
            $query->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();
        $sales->transform(function ($s) {
            $s->computed_taxable = $s->taxable_amount ?? $s->sale_amount ?? 0;
            $s->computed_cgst = $s->cgst_amount ?? 0;
            $s->computed_sgst = $s->sgst_amount ?? 0;
            $s->computed_igst = $s->igst_amount ?? 0;
            $s->computed_total_gst = $s->computed_cgst + $s->computed_sgst + $s->computed_igst;
            $s->computed_grand_total = $s->grand_total ?? ($s->computed_taxable + $s->computed_total_gst);
            return $s;
        });
        return $sales;
    }

    public function gstSales(Request $request)
    {
        $sales = $this->getGstSalesData($request);

        $totalInvoices = $sales->count();
        $totalTaxable = $sales->sum('computed_taxable');
        $totalCgst = $sales->sum('computed_cgst');
        $totalSgst = $sales->sum('computed_sgst');
        $totalIgst = $sales->sum('computed_igst');
        $totalGst = $sales->sum('computed_total_gst');
        $grandTotal = $sales->sum('computed_grand_total');

        $custQuery = Customer::orderBy('name');
        $this->applyFirmScope($custQuery, $request);
        $customers = $custQuery->get();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $projects = $this->getAvailableProjects($request);

        return view('admin.reports.gst-sales', compact(
            'sales', 'customers', 'firms', 'projects',
            'totalInvoices', 'totalTaxable',
            'totalCgst', 'totalSgst', 'totalIgst',
            'totalGst', 'grandTotal'
        ));
    }

    public function gstSalesExportPdf(Request $request)
    {
        $sales = $this->getGstSalesData($request);
        $totalTaxable = $sales->sum('computed_taxable');
        $totalCgst = $sales->sum('computed_cgst');
        $totalSgst = $sales->sum('computed_sgst');
        $totalIgst = $sales->sum('computed_igst');
        $totalGst = $sales->sum('computed_total_gst');
        $grandTotal = $sales->sum('computed_grand_total');

        return view('admin.reports.gst-sales-pdf', compact(
            'sales', 'totalTaxable', 'totalCgst', 'totalSgst',
            'totalIgst', 'totalGst', 'grandTotal'
        ));
    }

    public function gstSalesExportExcel(Request $request)
    {
        $sales = $this->getGstSalesData($request);
        $filename = 'gst-sales-report-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($sales) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, ['Sr', 'Invoice No', 'Date', 'Customer', 'Property',
                'Taxable Amt', 'CGST%', 'CGST Amt', 'SGST%', 'SGST Amt',
                'IGST%', 'IGST Amt', 'Total GST', 'Grand Total', 'HSN Code', 'Status']);
            foreach ($sales as $i => $s) {
                fputcsv($h, [
                    $i + 1,
                    $s->invoice_no ?? '-',
                    $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '-',
                    $s->customer?->name ?? '-',
                    $s->property?->property_name ?? '-',
                    number_format($s->computed_taxable, 2),
                    $s->cgst_rate ?? '0',
                    number_format($s->computed_cgst, 2),
                    $s->sgst_rate ?? '0',
                    number_format($s->computed_sgst, 2),
                    $s->igst_rate ?? '0',
                    number_format($s->computed_igst, 2),
                    number_format($s->computed_total_gst, 2),
                    number_format($s->computed_grand_total, 2),
                    $s->hsn_code ?? '-',
                    $s->payment_status ?? '-',
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

        if ($request->filled('from_date'))
            $query->whereDate('expense_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('expense_date', '<=', $request->to_date);
        if ($request->filled('filter_vendor'))
            $query->where('vendor_id', $request->filter_vendor);
        if ($request->filled('filter_status'))
            $query->where('approval_status', $request->filter_status);
        if ($request->filled('filter_category'))
            $query->where('expense_category_id', $request->filter_category);
        if ($request->filled('filter_project')) {
            $projectId = $request->filter_project;
            $query
                ->where('project_id', $projectId)
                ->whereNull('property_id')
                ->whereDoesntHave('properties');
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $expenses->transform(function ($e) {
            $e->computed_taxable = $e->taxable_amount ?? $e->amount ?? 0;
            $e->computed_cgst = $e->cgst_amount ?? 0;
            $e->computed_sgst = $e->sgst_amount ?? 0;
            $e->computed_igst = $e->igst_amount ?? 0;
            $e->computed_total_gst = $e->computed_cgst + $e->computed_sgst + $e->computed_igst;
            $e->computed_grand_total = $e->grand_total ?? ($e->computed_taxable + $e->computed_total_gst);
            return $e;
        });

        return $expenses;
    }

    public function gstPurchase(Request $request)
    {
        $expenses = $this->getGstPurchaseData($request);

        $totalBills = $expenses->count();
        $totalTaxable = $expenses->sum('computed_taxable');
        $totalCgst = $expenses->sum('computed_cgst');
        $totalSgst = $expenses->sum('computed_sgst');
        $totalIgst = $expenses->sum('computed_igst');
        $totalGst = $expenses->sum('computed_total_gst');
        $grandTotal = $expenses->sum('computed_grand_total');

        $venQuery = Vendor::orderBy('name');
        $this->applyFirmScope($venQuery, $request);
        $vendors = $venQuery->get();

        $catQuery = ExpenseCategory::where('status', 'active')->orderBy('name');
        $this->applyFirmScope($catQuery, $request, 'firms.id', 'firms');
        $categories = $catQuery->get();

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $projects = $this->getAvailableProjects($request);

        return view('admin.reports.gst-purchase', compact(
            'expenses', 'vendors', 'categories', 'firms', 'projects',
            'totalBills', 'totalTaxable',
            'totalCgst', 'totalSgst', 'totalIgst',
            'totalGst', 'grandTotal'
        ));
    }

    public function gstPurchaseExportPdf(Request $request)
    {
        $expenses = $this->getGstPurchaseData($request);
        $totalTaxable = $expenses->sum('computed_taxable');
        $totalCgst = $expenses->sum('computed_cgst');
        $totalSgst = $expenses->sum('computed_sgst');
        $totalIgst = $expenses->sum('computed_igst');
        $totalGst = $expenses->sum('computed_total_gst');
        $grandTotal = $expenses->sum('computed_grand_total');

        return view('admin.reports.gst-purchase-pdf', compact(
            'expenses', 'totalTaxable', 'totalCgst', 'totalSgst',
            'totalIgst', 'totalGst', 'grandTotal'
        ));
    }

    public function gstPurchaseExportExcel(Request $request)
    {
        $expenses = $this->getGstPurchaseData($request);
        $filename = 'gst-purchase-report-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($expenses) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, [
                'Sr',
                'Bill No',
                'Invoice No',
                'Date',
                'Vendor / Supplier',
                'Expense Title',
                'Category',
                'HSN Code',
                'Taxable Amt',
                'CGST%',
                'CGST Amt',
                'SGST%',
                'SGST Amt',
                'IGST%',
                'IGST Amt',
                'Total GST',
                'Grand Total',
                'Payment Mode',
                'Approval Status',
            ]);
            foreach ($expenses as $i => $e) {
                fputcsv($h, [
                    $i + 1,
                    $e->bill_no ?? '-',
                    $e->invoice_no ?? '-',
                    \Carbon\Carbon::parse($e->expense_date)->format('d M Y'),
                    $e->vendor?->name ?? ($e->paid_to ?? '-'),
                    $e->expense_title,
                    $e->expense_category ?? '-',
                    $e->hsn_code ?? '-',
                    number_format($e->computed_taxable, 2),
                    $e->cgst_rate ?? '0',
                    number_format($e->computed_cgst, 2),
                    $e->sgst_rate ?? '0',
                    number_format($e->computed_sgst, 2),
                    $e->igst_rate ?? '0',
                    number_format($e->computed_igst, 2),
                    number_format($e->computed_total_gst, 2),
                    number_format($e->computed_grand_total, 2),
                    $e->payment_mode ?? '-',
                    $e->approval_status ?? '-',
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

        if ($request->filled('from_date'))
            $query->whereDate('credit_note_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('credit_note_date', '<=', $request->to_date);
        if ($request->filled('filter_customer'))
            $query->where('customer_id', $request->filter_customer);
        if ($request->filled('filter_status'))
            $query->where('status', $request->filter_status);
        if ($request->filled('filter_project')) {
            $projectId = $request->filter_project;
            $query->whereHas('propertySale', function ($ps) use ($projectId) {
                $ps
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }

        return $query->orderBy('credit_note_date', 'desc')->get();
    }

    public function creditNote(Request $request)
    {
        $notes = $this->getCreditNoteData($request);

        $totalNotes = $notes->count();
        $totalTaxable = $notes->sum('taxable_amount');
        $totalCgst = $notes->sum('cgst_amount');
        $totalSgst = $notes->sum('sgst_amount');
        $totalIgst = $notes->sum('igst_amount');
        $totalGst = $notes->sum('total_gst');
        $totalCredit = $notes->sum('credit_amount');

        $custQuery = Customer::orderBy('name');
        $this->applyFirmScope($custQuery, $request);
        $customers = $custQuery->get();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $projects = $this->getAvailableProjects($request);

        return view('admin.reports.credit-note', compact(
            'notes', 'customers', 'firms', 'projects',
            'totalNotes', 'totalTaxable',
            'totalCgst', 'totalSgst', 'totalIgst',
            'totalGst', 'totalCredit'
        ));
    }

    public function creditNoteExportPdf(Request $request)
    {
        $notes = $this->getCreditNoteData($request);
        $totalTaxable = $notes->sum('taxable_amount');
        $totalCgst = $notes->sum('cgst_amount');
        $totalSgst = $notes->sum('sgst_amount');
        $totalIgst = $notes->sum('igst_amount');
        $totalGst = $notes->sum('total_gst');
        $totalCredit = $notes->sum('credit_amount');

        return view('admin.reports.credit-note-pdf', compact(
            'notes', 'totalTaxable', 'totalCgst', 'totalSgst',
            'totalIgst', 'totalGst', 'totalCredit'
        ));
    }

    public function creditNoteExportExcel(Request $request)
    {
        $notes = $this->getCreditNoteData($request);
        $filename = 'credit-note-report-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($notes) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, [
                'Sr',
                'Credit Note No',
                'Date',
                'Customer',
                'Related Invoice No',
                'Reason',
                'Taxable Amt',
                'CGST%',
                'CGST Amt',
                'SGST%',
                'SGST Amt',
                'IGST%',
                'IGST Amt',
                'Total GST',
                'Credit Amount',
                'Status',
            ]);
            foreach ($notes as $i => $n) {
                fputcsv($h, [
                    $i + 1,
                    $n->credit_note_no ?? '-',
                    \Carbon\Carbon::parse($n->credit_note_date)->format('d M Y'),
                    $n->customer?->name ?? '-',
                    $n->related_invoice_no ?? '-',
                    $n->reason ?? '-',
                    number_format($n->taxable_amount, 2),
                    $n->cgst_rate ?? '0',
                    number_format($n->cgst_amount, 2),
                    $n->sgst_rate ?? '0',
                    number_format($n->sgst_amount, 2),
                    $n->igst_rate ?? '0',
                    number_format($n->igst_amount, 2),
                    number_format($n->total_gst, 2),
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

        if ($request->filled('from_date'))
            $query->whereDate('debit_note_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('debit_note_date', '<=', $request->to_date);
        if ($request->filled('filter_vendor'))
            $query->where('vendor_id', $request->filter_vendor);
        if ($request->filled('filter_status'))
            $query->where('status', $request->filter_status);
        if ($request->filled('filter_project')) {
            $projectId = $request->filter_project;
            $query->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('vendor', fn($v) => $v->where('project_id', $projectId))
                    ->orWhereExists(function ($sub) use ($projectId) {
                        $sub
                            ->selectRaw(1)
                            ->from('expenses')
                            ->whereColumn('expenses.bill_no', 'debit_notes.related_bill_no')
                            ->where('expenses.project_id', $projectId)
                            ->whereNull('expenses.property_id');
                    });
            });
        }

        return $query->orderBy('debit_note_date', 'desc')->get();
    }

    public function debitNote(Request $request)
    {
        $notes = $this->getDebitNoteData($request);

        $totalNotes = $notes->count();
        $totalTaxable = $notes->sum('taxable_amount');
        $totalCgst = $notes->sum('cgst_amount');
        $totalSgst = $notes->sum('sgst_amount');
        $totalIgst = $notes->sum('igst_amount');
        $totalGst = $notes->sum('total_gst');
        $totalDebit = $notes->sum('debit_amount');

        $venQuery = Vendor::orderBy('name');
        $this->applyFirmScope($venQuery, $request);
        $vendors = $venQuery->get();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $projects = $this->getAvailableProjects($request);

        return view('admin.reports.debit-note', compact(
            'notes', 'vendors', 'firms', 'projects',
            'totalNotes', 'totalTaxable',
            'totalCgst', 'totalSgst', 'totalIgst',
            'totalGst', 'totalDebit'
        ));
    }

    public function debitNoteExportPdf(Request $request)
    {
        $notes = $this->getDebitNoteData($request);
        $totalTaxable = $notes->sum('taxable_amount');
        $totalCgst = $notes->sum('cgst_amount');
        $totalSgst = $notes->sum('sgst_amount');
        $totalIgst = $notes->sum('igst_amount');
        $totalGst = $notes->sum('total_gst');
        $totalDebit = $notes->sum('debit_amount');

        return view('admin.reports.debit-note-pdf', compact(
            'notes', 'totalTaxable', 'totalCgst', 'totalSgst',
            'totalIgst', 'totalGst', 'totalDebit'
        ));
    }

    public function debitNoteExportExcel(Request $request)
    {
        $notes = $this->getDebitNoteData($request);
        $filename = 'debit-note-report-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($notes) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, [
                'Sr',
                'Debit Note No',
                'Date',
                'Vendor / Supplier',
                'Related Bill No',
                'Reason',
                'Taxable Amt',
                'CGST%',
                'CGST Amt',
                'SGST%',
                'SGST Amt',
                'IGST%',
                'IGST Amt',
                'Total GST',
                'Debit Amount',
                'Status',
            ]);
            foreach ($notes as $i => $n) {
                fputcsv($h, [
                    $i + 1,
                    $n->debit_note_no ?? '-',
                    \Carbon\Carbon::parse($n->debit_note_date)->format('d M Y'),
                    $n->vendor?->name ?? '-',
                    $n->related_bill_no ?? '-',
                    $n->reason ?? '-',
                    number_format($n->taxable_amount, 2),
                    $n->cgst_rate ?? '0',
                    number_format($n->cgst_amount, 2),
                    $n->sgst_rate ?? '0',
                    number_format($n->sgst_amount, 2),
                    $n->igst_rate ?? '0',
                    number_format($n->igst_amount, 2),
                    number_format($n->total_gst, 2),
                    number_format($n->debit_amount, 2),
                    $n->status,
                ]);
            }
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------------
    // Profit & Loss Statement (Standard Accounting Method)
    // ---------------------------------------------------------------
    private function getProfitLossData(Request $request)
    {
        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate = $request->filled('to_date') ? $request->to_date : null;
        $projectId = $request->filled('filter_project') ? $request->filter_project : null;

        // ── 1. OPERATING REVENUE (TURNOVER) ──
        $salesQuery = PropertySale::with(['properties.propertyMaster', 'property.propertyMaster'])
            ->where('sale_status', '!=', 'cancelled');
        $this->applyFirmScope($salesQuery, $request);
        if ($projectId) {
            $salesQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $salesList = $salesQuery
            ->when($fromDate, fn($q) => $q->whereDate('sale_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('sale_date', '<=', $toDate))
            ->get();

        $propertySalesRevenue = (float) $salesList->sum('sale_amount');

        $rentPayQuery = RentalPayment::query();
        $this->applyFirmScope($rentPayQuery, $request, 'firm_id', 'rental');
        if ($projectId) {
            $rentPayQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('rental.property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('rental.properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $rentalRevenue = (float) $rentPayQuery
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->sum('paid_amount');

        $incQuery = \App\Models\Income::query();
        $this->applyFirmScope($incQuery, $request);
        if ($projectId) {
            $incQuery->whereHas('property', fn($p) => $p->where('project_id', $projectId));
        }
        $otherIncome = (float) $incQuery
            ->when($fromDate, fn($q) => $q->whereDate('income_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('income_date', '<=', $toDate))
            ->sum('amount');

        $totalRevenue = $propertySalesRevenue + $rentalRevenue + $otherIncome;

        // ── 2. COST OF SALES / DIRECT COSTS (COGS) ──
        $propertyPurchaseCost = (float) $salesList->sum(fn($s) => $s->total_purchase_cost);

        $matInwardQuery = StockInward::query();
        $this->applyFirmScope($matInwardQuery, $request);
        if ($projectId) {
            $matInwardQuery->where('project_id', $projectId);
        }
        $materialPurchaseCost = (float) $matInwardQuery
            ->when($fromDate, fn($q) => $q->whereDate('inward_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('inward_date', '<=', $toDate))
            ->sum('total_amount');

        $totalCostOfSales = $propertyPurchaseCost + $materialPurchaseCost;

        // ── 3. GROSS PROFIT ──
        $grossProfit = $totalRevenue - $totalCostOfSales;
        $grossProfitMargin = $totalRevenue > 0 ? round(($grossProfit / $totalRevenue) * 100, 2) : 0.0;

        // ── 4. OPERATING & INDIRECT EXPENSES ──
        $brokerCommissions = (float) $salesList->sum('broker_commission_amount');

        $expQuery = Expense::query();
        $this->applyFirmScope($expQuery, $request);
        if ($projectId) {
            $expQuery
                ->where('project_id', $projectId)
                ->whereNull('property_id')
                ->whereDoesntHave('properties');
        }
        $operatingExpenses = (float) $expQuery
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->sum('amount');

        $loanQuery = LoanEmiSchedule::whereIn('emi_status', ['Paid', 'Partial']);
        $this->applyFirmScope($loanQuery, $request, 'firm_id', 'loan');
        if ($projectId) {
            $loanQuery->whereHas('loan.property', fn($p) => $p->where('project_id', $projectId));
        }
        $loanEmiPaid = (float) $loanQuery
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->sum('paid_amount');

        $totalOperatingExpenses = $brokerCommissions + $operatingExpenses + $loanEmiPaid;

        // ── 5. NET PROFIT / (LOSS) ──
        $netProfitLoss = $grossProfit - $totalOperatingExpenses;
        $netProfitMargin = $totalRevenue > 0 ? round(($netProfitLoss / $totalRevenue) * 100, 2) : 0.0;

        $expenseByCategory = $expQuery
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->selectRaw('COALESCE(expense_category, "General Expenses") as category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return compact(
            'propertySalesRevenue', 'rentalRevenue', 'otherIncome', 'totalRevenue',
            'propertyPurchaseCost', 'materialPurchaseCost', 'totalCostOfSales',
            'grossProfit', 'grossProfitMargin',
            'brokerCommissions', 'operatingExpenses', 'loanEmiPaid', 'totalOperatingExpenses',
            'netProfitLoss', 'netProfitMargin', 'expenseByCategory'
        );
    }

    public function profitLoss(Request $request)
    {
        $pl = $this->getProfitLossData($request);
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $projects = $this->getAvailableProjects($request);

        return view('admin.reports.profit-loss', array_merge($pl, compact('firms', 'projects')));
    }

    public function profitLossExportPdf(Request $request)
    {
        $pl = $this->getProfitLossData($request);
        return view('admin.reports.profit-loss-pdf', $pl);
    }

    public function profitLossExportExcel(Request $request)
    {
        $pl = $this->getProfitLossData($request);

        $filename = 'profit-loss-statement-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($pl, $request) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($h, ['DELAWALA PROPERTIES - PROFIT & LOSS STATEMENT (ACCOUNTING METHOD)']);
            fputcsv($h, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
                fputcsv($h, ['Period', $fromDisplay . ' to ' . $toDisplay]);
            }
            fputcsv($h, []);

            fputcsv($h, ['PART I: OPERATING REVENUE (INCOME)', 'TYPE', 'AMOUNT (₹)']);
            fputcsv($h, ['  Property Sales Revenue', 'Revenue', number_format($pl['propertySalesRevenue'], 2)]);
            fputcsv($h, ['  Rental Income Received', 'Revenue', number_format($pl['rentalRevenue'], 2)]);
            fputcsv($h, ['  Other Business Incomes', 'Revenue', number_format($pl['otherIncome'], 2)]);
            fputcsv($h, ['TOTAL REVENUE (A)', 'TOTAL', number_format($pl['totalRevenue'], 2)]);
            fputcsv($h, []);

            fputcsv($h, ['PART II: COST OF SALES / DIRECT COSTS (COGS)', 'TYPE', 'AMOUNT (₹)']);
            fputcsv($h, ['  Property Acquisition Cost (Lidhi Price of Sold Units)', 'Direct Cost', number_format($pl['propertyPurchaseCost'], 2)]);
            fputcsv($h, ['  Material Stock Purchases & Construction Direct Costs', 'Direct Cost', number_format($pl['materialPurchaseCost'], 2)]);
            fputcsv($h, ['TOTAL COST OF SALES (B)', 'TOTAL', number_format($pl['totalCostOfSales'], 2)]);
            fputcsv($h, []);

            fputcsv($h, ['GROSS PROFIT / (LOSS) (A - B)', 'GROSS MARGIN ' . $pl['grossProfitMargin'] . '%', number_format($pl['grossProfit'], 2)]);
            fputcsv($h, []);

            fputcsv($h, ['PART III: OPERATING & INDIRECT EXPENSES', 'TYPE', 'AMOUNT (₹)']);
            fputcsv($h, ['  Broker Commissions / Selling Incentive', 'Selling Expense', number_format($pl['brokerCommissions'], 2)]);
            fputcsv($h, ['  Operating & Administrative Expenses', 'Admin Expense', number_format($pl['operatingExpenses'], 2)]);
            fputcsv($h, ['  Finance Charges & Loan EMI Outflows', 'Finance Expense', number_format($pl['loanEmiPaid'], 2)]);
            fputcsv($h, ['TOTAL OPERATING EXPENSES (C)', 'TOTAL', number_format($pl['totalOperatingExpenses'], 2)]);
            fputcsv($h, []);

            fputcsv($h, ['NET PROFIT / (LOSS) BEFORE TAX', 'NET MARGIN ' . $pl['netProfitMargin'] . '%', number_format($pl['netProfitLoss'], 2)]);
            fclose($h);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------------
    // Balance Sheet (Standard Double-Entry Method)
    // ---------------------------------------------------------------
    public function balanceSheet(Request $request)
    {
        $asOnDate = $request->filled('as_on_date') ? $request->as_on_date : null;
        $projectId = $request->filled('filter_project') ? $request->filter_project : null;

        // ── 1. ASSETS (CURRENT & NON-CURRENT) ──
        // Liquid Cash & Bank from all payments
        $payQuery = Payment::query();
        $this->applyFirmScope($payQuery, $request);
        if ($projectId) {
            $payQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('propertySale.property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('propertySale.properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $cashFromPayments = $payQuery->when($asOnDate, fn($q) => $q->whereDate('payment_date', '<=', $asOnDate))->sum('payment_amount');

        $bkQuery = Booking::query();
        $this->applyFirmScope($bkQuery, $request);
        if ($projectId) {
            $bkQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $cashFromBookings = $bkQuery->when($asOnDate, fn($q) => $q->whereDate('booking_date', '<=', $asOnDate))->sum('booking_amount');

        $cashReceived = max($cashFromPayments, $cashFromBookings);

        $rentPayQuery = RentalPayment::query();
        $this->applyFirmScope($rentPayQuery, $request, 'firm_id', 'rental');
        if ($projectId) {
            $rentPayQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('rental.property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('rental.properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $rentalCashReceived = $rentPayQuery->when($asOnDate, fn($q) => $q->whereDate('payment_date', '<=', $asOnDate))->sum('paid_amount');

        $incQuery = \App\Models\Income::query();
        $this->applyFirmScope($incQuery, $request);
        if ($projectId) {
            $incQuery->whereHas('property', fn($p) => $p->where('project_id', $projectId));
        }
        $otherIncomeReceived = $incQuery->when($asOnDate, fn($q) => $q->whereDate('income_date', '<=', $asOnDate))->sum('amount');

        $totalCashAndBank = $cashReceived + $rentalCashReceived + $otherIncomeReceived;

        // Accounts Receivable (Debtors: Sales Remaining + Booking Remaining + Rent Overdue)
        $saleRecQuery = PropertySale::query();
        $this->applyFirmScope($saleRecQuery, $request);
        if ($projectId) {
            $saleRecQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $receivablesFromSales = $saleRecQuery
            ->when($asOnDate, fn($q) => $q->whereDate('sale_date', '<=', $asOnDate))
            ->whereIn('payment_status', ['pending', 'partial'])
            ->sum('remaining_amount');

        $bkRecQuery = Booking::query();
        $this->applyFirmScope($bkRecQuery, $request);
        if ($projectId) {
            $bkRecQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $receivablesFromBookings = $bkRecQuery
            ->when($asOnDate, fn($q) => $q->whereDate('booking_date', '<=', $asOnDate))
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->sum('remaining_amount');

        $receivables = max($receivablesFromSales, $receivablesFromBookings);

        // Inventory Stock: Available properties valuation
        $propQuery = Property::whereIn('status', ['available', 'booked']);
        $this->applyFirmScope($propQuery, $request);
        if ($projectId) {
            $propQuery->where('project_id', $projectId);
        }
        $propertyValue = $propQuery->sum('price');

        // Material inventory valuation
        $matQuery = StockInward::query();
        $this->applyFirmScope($matQuery, $request);
        if ($projectId) {
            $matQuery->where('project_id', $projectId);
        }
        $materialStockValue = $matQuery->when($asOnDate, fn($q) => $q->whereDate('inward_date', '<=', $asOnDate))->sum('total_amount');

        // Security Deposits
        $rentQuery = Rental::where('rental_status', 'active');
        $this->applyFirmScope($rentQuery, $request);
        if ($projectId) {
            $rentQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $securityDeposits = $rentQuery->when($asOnDate, fn($q) => $q->whereDate('rent_start_date', '<=', $asOnDate))->sum('security_deposit');

        $totalAssets = $totalCashAndBank + $receivables + $propertyValue + $materialStockValue + $securityDeposits;

        // ── 2. LIABILITIES ──
        $loanQuery = Loan::query();
        $this->applyFirmScope($loanQuery, $request);
        if ($projectId) {
            $loanQuery->whereHas('property', fn($p) => $p->where('project_id', $projectId));
        }
        $loanOutstanding = $loanQuery->when($asOnDate, fn($q) => $q->whereDate('loan_start_date', '<=', $asOnDate))->sum('pending_amount');
        $loanTotal = (clone $loanQuery)->sum('loan_amount');
        $loanPaid = (clone $loanQuery)->sum('paid_amount');

        $expQuery = Expense::where('approval_status', 'Pending');
        $this->applyFirmScope($expQuery, $request);
        if ($projectId) {
            $expQuery
                ->where('project_id', $projectId)
                ->whereNull('property_id')
                ->whereDoesntHave('properties');
        }
        $unpaidExpenses = $expQuery->when($asOnDate, fn($q) => $q->whereDate('expense_date', '<=', $asOnDate))->sum('amount');

        $cnQuery = CreditNote::whereIn('status', ['Pending', 'Approved']);
        $this->applyFirmScope($cnQuery, $request);
        if ($projectId) {
            $cnQuery->whereHas('propertySale', function ($ps) use ($projectId) {
                $ps
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $creditNotePayable = $cnQuery->when($asOnDate, fn($q) => $q->whereDate('credit_note_date', '<=', $asOnDate))->sum('credit_amount');

        $totalLiabilities = $loanOutstanding + $unpaidExpenses + $creditNotePayable;

        // ── 3. EQUITY & NET CAPITAL ──
        $netWorth = $totalAssets - $totalLiabilities;

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $projects = $this->getAvailableProjects($request);

        return view('admin.reports.balance-sheet', compact(
            'cashReceived', 'rentalCashReceived', 'totalCashAndBank', 'receivables',
            'propertyValue', 'materialStockValue', 'securityDeposits', 'totalAssets',
            'loanOutstanding', 'unpaidExpenses', 'creditNotePayable',
            'loanTotal', 'loanPaid', 'totalLiabilities',
            'netWorth', 'firms', 'projects'
        ));
    }

    public function balanceSheetExportPdf(Request $request)
    {
        $view = $this->balanceSheet($request);
        return view('admin.reports.balance-sheet-pdf', $view->getData());
    }

    public function balanceSheetExportExcel(Request $request)
    {
        $asOnDate = $request->filled('as_on_date') ? $request->as_on_date : null;
        $projectId = $request->filled('filter_project') ? $request->filter_project : null;

        $payQuery = Payment::query();
        $this->applyFirmScope($payQuery, $request);
        if ($projectId) {
            $payQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('propertySale.property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('propertySale.properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $cashFromPayments = $payQuery->when($asOnDate, fn($q) => $q->whereDate('payment_date', '<=', $asOnDate))->sum('payment_amount');

        $bkQuery = Booking::query();
        $this->applyFirmScope($bkQuery, $request);
        if ($projectId) {
            $bkQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $cashFromBookings = $bkQuery->when($asOnDate, fn($q) => $q->whereDate('booking_date', '<=', $asOnDate))->sum('booking_amount');

        $cashReceived = max($cashFromPayments, $cashFromBookings);

        $rentPayQuery = RentalPayment::query();
        $this->applyFirmScope($rentPayQuery, $request, 'firm_id', 'rental');
        if ($projectId) {
            $rentPayQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('rental.property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('rental.properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $rentalCashReceived = $rentPayQuery->when($asOnDate, fn($q) => $q->whereDate('payment_date', '<=', $asOnDate))->sum('paid_amount');

        $saleRecQuery = PropertySale::query();
        $this->applyFirmScope($saleRecQuery, $request);
        if ($projectId) {
            $saleRecQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $receivablesFromSales = $saleRecQuery
            ->when($asOnDate, fn($q) => $q->whereDate('sale_date', '<=', $asOnDate))
            ->whereIn('payment_status', ['pending', 'partial'])
            ->sum('remaining_amount');

        $bkRecQuery = Booking::query();
        $this->applyFirmScope($bkRecQuery, $request);
        if ($projectId) {
            $bkRecQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $receivablesFromBookings = $bkRecQuery
            ->when($asOnDate, fn($q) => $q->whereDate('booking_date', '<=', $asOnDate))
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->sum('remaining_amount');

        $receivables = max($receivablesFromSales, $receivablesFromBookings);

        $propQuery = Property::whereIn('status', ['available', 'booked']);
        $this->applyFirmScope($propQuery, $request);
        if ($projectId) {
            $propQuery->where('project_id', $projectId);
        }
        $propertyValue = $propQuery->sum('price');

        $rentQuery = Rental::where('rental_status', 'active');
        $this->applyFirmScope($rentQuery, $request);
        if ($projectId) {
            $rentQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $securityDeposits = $rentQuery->when($asOnDate, fn($q) => $q->whereDate('rent_start_date', '<=', $asOnDate))->sum('security_deposit');

        $totalAssets = $cashReceived + $rentalCashReceived + $receivables + $propertyValue + $securityDeposits;

        $loanQuery = Loan::query();
        $this->applyFirmScope($loanQuery, $request);
        if ($projectId) {
            $loanQuery->whereHas('property', fn($p) => $p->where('project_id', $projectId));
        }
        $loanOutstanding = $loanQuery->when($asOnDate, fn($q) => $q->whereDate('loan_start_date', '<=', $asOnDate))->sum('pending_amount');

        $expQuery = Expense::where('approval_status', 'Pending');
        $this->applyFirmScope($expQuery, $request);
        if ($projectId) {
            $expQuery
                ->where('project_id', $projectId)
                ->whereNull('property_id')
                ->whereDoesntHave('properties');
        }
        $unpaidExpenses = $expQuery->when($asOnDate, fn($q) => $q->whereDate('expense_date', '<=', $asOnDate))->sum('amount');

        $cnQuery = CreditNote::whereIn('status', ['Pending', 'Approved']);
        $this->applyFirmScope($cnQuery, $request);
        if ($projectId) {
            $cnQuery->whereHas('propertySale', function ($ps) use ($projectId) {
                $ps
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $creditNotePayable = $cnQuery->when($asOnDate, fn($q) => $q->whereDate('credit_note_date', '<=', $asOnDate))->sum('credit_amount');

        $totalLiabilities = $loanOutstanding + $unpaidExpenses + $creditNotePayable;
        $netWorth = $totalAssets - $totalLiabilities;

        $filename = 'balance-sheet-' . ($asOnDate ?? date('Y-m-d')) . '.csv';
        $headers = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="' . $filename . '"'];
        $callback = function () use ($cashReceived, $rentalCashReceived, $receivables, $propertyValue,
                $securityDeposits, $totalAssets, $loanOutstanding, $unpaidExpenses,
                $creditNotePayable, $totalLiabilities, $netWorth) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, ['Particular', 'Category', 'Amount (₹)']);
            fputcsv($h, ['Cash / Bank Receipts (Sales & Bookings)', 'Asset', number_format($cashReceived, 2)]);
            fputcsv($h, ['Rental Income Collected', 'Asset', number_format($rentalCashReceived, 2)]);
            fputcsv($h, ['Receivables (Pending Sales/Bookings)', 'Asset', number_format($receivables, 2)]);
            fputcsv($h, ['Property Value (Unsold)', 'Asset', number_format($propertyValue, 2)]);
            fputcsv($h, ['Security Deposits Held', 'Asset', number_format($securityDeposits, 2)]);
            fputcsv($h, ['TOTAL ASSETS', 'TOTAL', number_format($totalAssets, 2)]);
            fputcsv($h, ['']);
            fputcsv($h, ['Outstanding Loan Balance', 'Liability', number_format($loanOutstanding, 2)]);
            fputcsv($h, ['Unpaid / Pending Expenses', 'Liability', number_format($unpaidExpenses, 2)]);
            fputcsv($h, ['Credit Notes Payable', 'Liability', number_format($creditNotePayable, 2)]);
            fputcsv($h, ['TOTAL LIABILITIES', 'TOTAL', number_format($totalLiabilities, 2)]);
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
        $toDate = $request->filled('to_date') ? $request->to_date : null;
        $projectId = $request->filled('filter_project') ? $request->filter_project : null;

        // 1. Sales Payments
        $payQuery = Payment::with(['customer', 'propertySale.property', 'firm']);
        $this->applyFirmScope($payQuery, $request);
        if ($projectId) {
            $payQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('propertySale.property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('propertySale.properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $salesPayments = $payQuery
            ->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')
            ->get()
            ->map(fn($p) => [
                'date' => $p->payment_date,
                'particular' => 'Sales Receipt — ' . ($p->customer?->name ?? 'Customer')
                    . ($p->propertySale?->property?->property_name
                        ? ' (' . $p->propertySale->property->property_name . ')'
                        : ''),
                'type' => 'inflow',
                'section' => 'Sales Payment Received',
                'payment_mode' => $p->payment_mode ?? '—',
                'amount' => (float) $p->payment_amount,
            ]);

        // If payments table is empty, include bookings
        if ($salesPayments->isEmpty()) {
            $bkQuery = Booking::with(['customer', 'property', 'firm']);
            $this->applyFirmScope($bkQuery, $request);
            if ($projectId) {
                $bkQuery->where(function ($q) use ($projectId) {
                    $q
                        ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                        ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
                });
            }
            $salesPayments = $bkQuery
                ->whereNotNull('booking_date')
                ->where('booking_amount', '>', 0)
                ->when($fromDate, fn($q) => $q->whereDate('booking_date', '>=', $fromDate))
                ->when($toDate, fn($q) => $q->whereDate('booking_date', '<=', $toDate))
                ->orderBy('booking_date')
                ->get()
                ->map(fn($b) => [
                    'date' => $b->booking_date,
                    'particular' => 'Booking Token (' . ucfirst($b->booking_type ?? 'booking') . ') — ' . ($b->customer?->name ?? 'Customer')
                        . ($b->property?->property_name ? ' (' . $b->property->property_name . ')' : ''),
                    'type' => 'inflow',
                    'section' => 'Booking Advance Received',
                    'payment_mode' => $b->payment_mode ?? ($b->paymentMode->name ?? '—'),
                    'amount' => (float) $b->booking_amount,
                ]);
        }

        // 2. Rental Payments
        $rentQuery = RentalPayment::with(['rental.property', 'rental.firm']);
        $this->applyFirmScope($rentQuery, $request, 'firm_id', 'rental');
        if ($projectId) {
            $rentQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('rental.property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('rental.properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $rentalPayments = $rentQuery
            ->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')
            ->get()
            ->map(fn($r) => [
                'date' => $r->payment_date,
                'particular' => 'Rental Income — ' . ($r->rental?->tenant_name ?? 'Tenant')
                    . ' / ' . $r->payment_month . ' ' . $r->payment_year,
                'type' => 'inflow',
                'section' => 'Rental Payment Received',
                'payment_mode' => $r->payment_mode ?? '—',
                'amount' => (float) $r->paid_amount,
            ]);

        // 3. Expenses
        $expQuery = Expense::query();
        $this->applyFirmScope($expQuery, $request);
        if ($projectId) {
            $expQuery
                ->where('project_id', $projectId)
                ->whereNull('property_id')
                ->whereDoesntHave('properties');
        }
        $expensePayments = $expQuery
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->orderBy('expense_date')
            ->get()
            ->map(fn($e) => [
                'date' => $e->expense_date,
                'particular' => $e->expense_title . ($e->expense_category ? ' (' . $e->expense_category . ')' : ''),
                'type' => 'outflow',
                'section' => 'Expenses Paid',
                'payment_mode' => $e->payment_mode ?? '—',
                'amount' => (float) $e->amount,
            ]);

        // 4. Loan EMI Repayments
        $loanQuery = LoanEmiSchedule::with('loan')->whereIn('emi_status', ['Paid', 'Partial']);
        $this->applyFirmScope($loanQuery, $request, 'firm_id', 'loan');
        if ($projectId) {
            $loanQuery->whereHas('loan.property', fn($p) => $p->where('project_id', $projectId));
        }
        $loanRepayments = $loanQuery
            ->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')
            ->get()
            ->map(fn($e) => [
                'date' => $e->payment_date,
                'particular' => 'Loan EMI — ' . ($e->loan?->bank_name ?? 'Bank') . ' (' . $e->emi_month . '/' . $e->emi_year . ')',
                'type' => 'outflow',
                'section' => 'Loan Repayment',
                'payment_mode' => $e->payment_mode ?? '—',
                'amount' => (float) $e->paid_amount,
            ]);

        $allTransactions = $salesPayments
            ->concat($rentalPayments)
            ->concat($expensePayments)
            ->concat($loanRepayments)
            ->sortBy('date')
            ->values();

        $totalSalesInflow = $salesPayments->sum('amount');
        $totalRentalInflow = $rentalPayments->sum('amount');
        $totalInflow = $totalSalesInflow + $totalRentalInflow;

        $totalExpenseOutflow = $expensePayments->sum('amount');
        $totalLoanOutflow = $loanRepayments->sum('amount');
        $totalOutflow = $totalExpenseOutflow + $totalLoanOutflow;

        $netCashFlow = $totalInflow - $totalOutflow;

        $monthlyRows = $allTransactions
            ->groupBy(fn($t) => substr($t['date'], 0, 7))
            ->map(function ($group, $month) {
                $in = $group->where('type', 'inflow')->sum('amount');
                $out = $group->where('type', 'outflow')->sum('amount');
                return ['month' => $month, 'inflow' => $in, 'outflow' => $out, 'net' => $in - $out];
            })
            ->sortKeys()
            ->values();

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $projects = $this->getAvailableProjects($request);

        return view('admin.reports.cash-flow', compact(
            'allTransactions',
            'totalSalesInflow', 'totalRentalInflow', 'totalInflow',
            'totalExpenseOutflow', 'totalLoanOutflow', 'totalOutflow',
            'netCashFlow', 'monthlyRows', 'firms', 'projects'
        ));
    }

    public function cashFlowExportPdf(Request $request)
    {
        $view = $this->cashFlow($request);
        return view('admin.reports.cash-flow-pdf', $view->getData());
    }

    public function cashFlowExportExcel(Request $request)
    {
        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate = $request->filled('to_date') ? $request->to_date : null;
        $projectId = $request->filled('filter_project') ? $request->filter_project : null;

        $payQuery = Payment::with(['customer', 'propertySale.property']);
        $this->applyFirmScope($payQuery, $request);
        if ($projectId) {
            $payQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('propertySale.property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('propertySale.properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $salesPayments = $payQuery
            ->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')
            ->get()
            ->map(fn($p) => ['date' => $p->payment_date,
                'particular' => 'Sales Receipt — ' . ($p->customer?->name ?? '-'),
                'type' => 'Inflow', 'section' => 'Sales Payment Received',
                'payment_mode' => $p->payment_mode ?? '-', 'amount' => (float) $p->payment_amount]);

        if ($salesPayments->isEmpty()) {
            $bkQuery = Booking::with(['customer', 'property']);
            $this->applyFirmScope($bkQuery, $request);
            if ($projectId) {
                $bkQuery->where(function ($q) use ($projectId) {
                    $q
                        ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                        ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
                });
            }
            $salesPayments = $bkQuery
                ->whereNotNull('booking_date')
                ->where('booking_amount', '>', 0)
                ->when($fromDate, fn($q) => $q->whereDate('booking_date', '>=', $fromDate))
                ->when($toDate, fn($q) => $q->whereDate('booking_date', '<=', $toDate))
                ->orderBy('booking_date')
                ->get()
                ->map(fn($b) => ['date' => $b->booking_date,
                    'particular' => 'Booking Token — ' . ($b->customer?->name ?? '-'),
                    'type' => 'Inflow', 'section' => 'Booking Advance Received',
                    'payment_mode' => $b->payment_mode ?? ($b->paymentMode->name ?? '-'), 'amount' => (float) $b->booking_amount]);
        }

        $rentQuery = RentalPayment::with(['rental']);
        $this->applyFirmScope($rentQuery, $request, 'firm_id', 'rental');
        if ($projectId) {
            $rentQuery->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('rental.property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('rental.properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $rentalPayments = $rentQuery
            ->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')
            ->get()
            ->map(fn($r) => ['date' => $r->payment_date,
                'particular' => 'Rental Income — ' . ($r->rental?->tenant_name ?? '-'),
                'type' => 'Inflow', 'section' => 'Rental Payment Received',
                'payment_mode' => $r->payment_mode ?? '-', 'amount' => (float) $r->paid_amount]);

        $expQuery = Expense::query();
        $this->applyFirmScope($expQuery, $request);
        if ($projectId) {
            $expQuery->where(function ($q) use ($projectId) {
                $q
                    ->where('project_id', $projectId)
                    ->orWhereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }
        $expensePayments = $expQuery
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('expense_date', '<=', $toDate))
            ->orderBy('expense_date')
            ->get()
            ->map(fn($e) => ['date' => $e->expense_date,
                'particular' => $e->expense_title, 'type' => 'Outflow',
                'section' => 'Expenses Paid',
                'payment_mode' => $e->payment_mode ?? '-', 'amount' => (float) $e->amount]);

        $loanQuery = LoanEmiSchedule::with('loan')->whereIn('emi_status', ['Paid', 'Partial']);
        $this->applyFirmScope($loanQuery, $request, 'firm_id', 'loan');
        if ($projectId) {
            $loanQuery->whereHas('loan.property', fn($p) => $p->where('project_id', $projectId));
        }
        $loanRepayments = $loanQuery
            ->whereNotNull('payment_date')
            ->when($fromDate, fn($q) => $q->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('payment_date', '<=', $toDate))
            ->orderBy('payment_date')
            ->get()
            ->map(fn($e) => ['date' => $e->payment_date,
                'particular' => 'Loan EMI — ' . ($e->loan?->bank_name ?? '-'),
                'type' => 'Outflow', 'section' => 'Loan Repayment',
                'payment_mode' => $e->payment_mode ?? '-', 'amount' => (float) $e->paid_amount]);

        $all = $salesPayments
            ->concat($rentalPayments)
            ->concat($expensePayments)
            ->concat($loanRepayments)
            ->sortBy('date')
            ->values();

        $filename = 'cash-flow-' . date('Y-m-d') . '.csv';
        $headers = ['Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'];
        $callback = function () use ($all) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, ['Date', 'Particular', 'Section', 'Type', 'Payment Mode', 'Amount (₹)']);
            foreach ($all as $row) {
                fputcsv($h, [
                    \Carbon\Carbon::parse($row['date'])->format('d M Y'),
                    $row['particular'],
                    $row['section'],
                    $row['type'],
                    $row['payment_mode'],
                    number_format($row['amount'], 2),
                ]);
            }
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------------
    // Sales Report (Accounting Method)
    // ---------------------------------------------------------------
    private function getSalesData(Request $request)
    {
        $query = PropertySale::with([
            'property.propertyMaster',
            'properties.propertyMaster',
            'customer',
            'broker',
            'firm'
        ]);
        $this->applyFirmScope($query, $request);

        if ($request->filled('from_date'))
            $query->whereDate('sale_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('sale_date', '<=', $request->to_date);
        if ($request->filled('filter_property'))
            $query->where('property_id', $request->filter_property);
        if ($request->filled('filter_customer'))
            $query->where('customer_id', $request->filter_customer);
        if ($request->filled('filter_status'))
            $query->where('payment_status', $request->filter_status);
        if ($request->filled('filter_project')) {
            $projectId = $request->filter_project;
            $query->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $projectId));
            });
        }

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

        $totalSale = (float) $records->sum('sale_amount');
        $totalPurchaseCost = (float) $records->sum(fn($s) => (float) $s->total_purchase_cost);
        $totalCommission = (float) $records->sum('broker_commission_amount');
        $totalNetProfit = (float) $records->sum(fn($s) => (float) $s->net_profit);
        $profitMargin = $totalSale > 0 ? round(($totalNetProfit / $totalSale) * 100, 2) : 0.0;
        $totalReceived = (float) $records->sum('received_amount');
        $totalPending = (float) $records->sum('remaining_amount');
        $totalBookings = $records->count();

        $propQuery = Property::orderBy('property_name');
        $custQuery = Customer::orderBy('name');
        $this->applyFirmScope($propQuery, $request);
        $this->applyFirmScope($custQuery, $request);
        if ($request->filled('filter_project')) {
            $propQuery->where('project_id', $request->filter_project);
        }

        $properties = $propQuery->get();
        $customers = $custQuery->get();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $projects = $this->getAvailableProjects($request);

        return view('admin.reports.sales', compact(
            'records', 'totalSale', 'totalPurchaseCost', 'totalCommission', 'totalNetProfit',
            'profitMargin', 'totalReceived', 'totalPending',
            'totalBookings', 'properties', 'customers', 'firms', 'projects'
        ));
    }

    public function salesExportPdf(Request $request)
    {
        $records = $this->getSalesData($request);
        $totalSale = (float) $records->sum('sale_amount');
        $totalPurchaseCost = (float) $records->sum(fn($s) => (float) $s->total_purchase_cost);
        $totalCommission = (float) $records->sum('broker_commission_amount');
        $totalNetProfit = (float) $records->sum(fn($s) => (float) $s->net_profit);
        $profitMargin = $totalSale > 0 ? round(($totalNetProfit / $totalSale) * 100, 2) : 0.0;
        $totalReceived = (float) $records->sum('received_amount');
        $totalPending = (float) $records->sum('remaining_amount');
        $totalBookings = $records->count();

        return view('admin.reports.sales-pdf', compact(
            'records', 'totalSale', 'totalPurchaseCost', 'totalCommission', 'totalNetProfit',
            'profitMargin', 'totalReceived', 'totalPending', 'totalBookings'
        ));
    }

    public function salesExportExcel(Request $request)
    {
        $records = $this->getSalesData($request);

        $totalSale = (float) $records->sum('sale_amount');
        $totalPurchaseCost = (float) $records->sum(fn($s) => (float) $s->total_purchase_cost);
        $totalCommission = (float) $records->sum('broker_commission_amount');
        $totalNetProfit = (float) $records->sum(fn($s) => (float) $s->net_profit);
        $profitMargin = $totalSale > 0 ? round(($totalNetProfit / $totalSale) * 100, 2) : 0.0;
        $totalReceived = (float) $records->sum('received_amount');
        $totalPending = (float) $records->sum('remaining_amount');

        $filename = 'sales-accounting-report-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($records, $request, $totalSale, $totalPurchaseCost, $totalCommission, $totalNetProfit, $profitMargin, $totalReceived, $totalPending) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($h, ['Delawala Properties & Management - Sales Accounting Report']);
            fputcsv($h, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
                fputcsv($h, ['Date Range', $fromDisplay . ' to ' . $toDisplay]);
            }
            fputcsv($h, []);

            fputcsv($h, ['ACCOUNTING SUMMARY']);
            fputcsv($h, ['Total Bookings / Units Sold', $records->count()]);
            fputcsv($h, ['Total Sale Value (Turnover)', number_format($totalSale, 2)]);
            fputcsv($h, ['Total Purchase Cost (Lidhi Price)', number_format($totalPurchaseCost, 2)]);
            fputcsv($h, ['Total Broker Commission', number_format($totalCommission, 2)]);
            fputcsv($h, ['Realized Net Profit', number_format($totalNetProfit, 2)]);
            fputcsv($h, ['Net Profit Margin %', $profitMargin . '%']);
            fputcsv($h, ['Total Received', number_format($totalReceived, 2)]);
            fputcsv($h, ['Total Pending', number_format($totalPending, 2)]);
            fputcsv($h, []);

            fputcsv($h, [
                'Sr',
                'Sale Date',
                'Invoice No',
                'Customer',
                'Property / Unit',
                'Broker',
                'Sale Value (₹)',
                'Purchase Cost (Lidhi Price ₹)',
                'Commission (₹)',
                'Net Profit (₹)',
                'Profit Margin %',
                'Received Amount (₹)',
                'Remaining Amount (₹)',
                'Payment Status',
                'Sale Status',
            ]);

            foreach ($records as $i => $s) {
                fputcsv($h, [
                    $i + 1,
                    $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '-',
                    $s->invoice_no ?? '-',
                    $s->customer?->name ?? '-',
                    $s->property?->property_name ?? ($s->properties->pluck('property_name')->implode(', ') ?: '-'),
                    $s->broker?->name ?? '-',
                    number_format($s->sale_amount ?? 0, 2),
                    number_format($s->total_purchase_cost ?? 0, 2),
                    number_format($s->broker_commission_amount ?? 0, 2),
                    number_format($s->net_profit ?? 0, 2),
                    $s->profit_margin_percentage . '%',
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
    // Payment Report (Unified Inflows & Outflows: મળ્યા & આપ્યા)
    // ---------------------------------------------------------------
    private function getPaymentsData(Request $request)
    {
        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate = $request->filled('to_date') ? $request->to_date : null;
        $projectId = $request->filled('filter_project') ? $request->filter_project : null;
        $linkedPmId = $projectId ? \App\Models\Project::find($projectId)?->property_id : null;
        $propId = $request->filled('filter_property') ? $request->filter_property : null;
        $mode = $request->filled('filter_mode') ? $request->filter_mode : null;
        $status = $request->filled('filter_status') ? strtolower($request->filter_status) : null;
        $custId = $request->filled('filter_customer') ? $request->filter_customer : null;
        $payType = $request->input('payment_type', 'all');  // 'all', 'received', 'paid'

        $items = collect();

        // ── 1. RECEIVED PAYMENTS (મળ્યા / લીધા - Inflows) ──
        if ($payType === 'all' || $payType === 'received') {
            // (a) Customer Property Sale Payments
            $payQuery = Payment::with(['propertySale.property', 'propertySale.properties', 'customer', 'property', 'firm']);
            $this->applyFirmScope($payQuery, $request);
            if ($fromDate)
                $payQuery->whereDate('payment_date', '>=', $fromDate);
            if ($toDate)
                $payQuery->whereDate('payment_date', '<=', $toDate);
            if ($mode)
                $payQuery->where('payment_mode', $mode);
            if ($status)
                $payQuery->where('status', $status);
            if ($custId)
                $payQuery->where('customer_id', $custId);
            if ($propId) {
                $payQuery->where(function ($q) use ($propId) {
                    $q
                        ->where('property_id', $propId)
                        ->orWhereHas('propertySale', function ($sq) use ($propId) {
                            $sq
                                ->where('property_id', $propId)
                                ->orWhereHas('properties', fn($p) => $p->where('properties.id', $propId));
                        });
                });
            }
            if ($projectId) {
                $payQuery->where(function ($q) use ($projectId, $linkedPmId) {
                    $q
                        ->whereHas('property', function ($p) use ($projectId, $linkedPmId) {
                            $p->where('project_id', $projectId)
                              ->when($linkedPmId, fn($sq) => $sq->orWhere('property_master_id', $linkedPmId));
                        })
                        ->orWhereHas('propertySale.property', function ($p) use ($projectId, $linkedPmId) {
                            $p->where('project_id', $projectId)
                              ->when($linkedPmId, fn($sq) => $sq->orWhere('property_master_id', $linkedPmId));
                        })
                        ->orWhereHas('propertySale.properties', function ($p) use ($projectId, $linkedPmId) {
                            $p->where('project_id', $projectId)
                              ->when($linkedPmId, fn($sq) => $sq->orWhere('property_master_id', $linkedPmId));
                        });
                });
            }

            foreach ($payQuery->get() as $p) {
                $st = strtolower($p->status ?? 'paid');
                $propName = $p->property?->property_name
                    ?: ($p->propertySale?->property?->property_name
                        ?: ($p->propertySale?->properties?->pluck('property_name')->join(', ') ?: '—'));
                $items->push((object) [
                    'id' => 'PAY-' . $p->id,
                    'raw_id' => $p->id,
                    'model_type' => 'payment',
                    'direction' => 'received',
                    'direction_label' => 'Received',
                    'payment_date' => $p->payment_date,
                    'party_name' => $p->customer?->name ?? 'Customer',
                    'party_sub' => $p->customer?->mobile ? 'Mob: ' . $p->customer->mobile : 'Customer Receipt',
                    'property_name' => $propName,
                    'particular' => 'Sales Receipt / Installment',
                    'reference' => $p->propertySale?->invoice_no ?: ($p->transaction_ref ? 'Ref: ' . $p->transaction_ref : '—'),
                    'payment_mode' => $p->payment_mode ?? 'Cash',
                    'amount' => (float) $p->payment_amount,
                    'pending_amount' => (float) $p->pending_amount,
                    'status' => ucfirst($p->status ?? 'paid'),
                    'status_class' => match ($st) {
                        'paid' => 'pb-paid',
                        'partial' => 'pb-partial',
                        'cancelled' => 'pb-cancelled',
                        default => 'pb-pending',
                    },
                    'view_url' => route('payments.show', $p->id),
                ]);
            }

            // (b) Direct Sales that have booking_amount > 0 but no entries in Payment table
            $directSalesQuery = PropertySale::with(['customer', 'property', 'properties', 'firm'])
                ->where('booking_amount', '>', 0)
                ->whereDoesntHave('payments');
            $this->applyFirmScope($directSalesQuery, $request);
            if ($fromDate)
                $directSalesQuery->whereDate('sale_date', '>=', $fromDate);
            if ($toDate)
                $directSalesQuery->whereDate('sale_date', '<=', $toDate);
            if ($custId)
                $directSalesQuery->where('customer_id', $custId);
            if ($propId) {
                $directSalesQuery->where(function ($q) use ($propId) {
                    $q
                        ->where('property_id', $propId)
                        ->orWhereHas('properties', fn($p) => $p->where('properties.id', $propId));
                });
            }
            if ($projectId) {
                $directSalesQuery->where(function ($q) use ($projectId, $linkedPmId) {
                    $q
                        ->whereHas('property', function ($p) use ($projectId, $linkedPmId) {
                            $p->where('project_id', $projectId)
                              ->when($linkedPmId, fn($sq) => $sq->orWhere('property_master_id', $linkedPmId));
                        })
                        ->orWhereHas('properties', function ($p) use ($projectId, $linkedPmId) {
                            $p->where('project_id', $projectId)
                              ->when($linkedPmId, fn($sq) => $sq->orWhere('property_master_id', $linkedPmId));
                        });
                });
            }
            foreach ($directSalesQuery->get() as $ds) {
                $st = strtolower($ds->payment_status ?? 'paid');
                $propNames = $ds->properties->isNotEmpty()
                    ? $ds->properties->pluck('property_name')->join(', ')
                    : ($ds->property?->property_name ?? '—');
                $items->push((object) [
                    'id' => 'SALE-' . $ds->id,
                    'raw_id' => $ds->id,
                    'model_type' => 'property_sale',
                    'direction' => 'received',
                    'direction_label' => 'Received',
                    'payment_date' => $ds->sale_date,
                    'party_name' => $ds->customer?->name ?? 'Customer',
                    'party_sub' => $ds->customer?->mobile ? 'Mob: ' . $ds->customer->mobile : 'Customer Advance / Down Payment',
                    'property_name' => $propNames,
                    'particular' => 'Direct Booking / Down Payment',
                    'reference' => $ds->invoice_no ?: ('Sale #' . $ds->id),
                    'payment_mode' => 'Cash / Direct',
                    'amount' => (float) $ds->booking_amount,
                    'pending_amount' => (float) $ds->remaining_amount,
                    'status' => ucfirst($ds->payment_status ?? 'paid'),
                    'status_class' => match ($st) {
                        'paid' => 'pb-paid',
                        'partial' => 'pb-partial',
                        'cancelled' => 'pb-cancelled',
                        default => 'pb-pending',
                    },
                    'view_url' => route('property-sales.show', $ds->id),
                ]);
            }

            // (c) Rental Income Payments (if not filtering specific customer)
            if (!$custId) {
                $rentQuery = RentalPayment::with(['rental.property', 'rental.properties', 'rental.firm', 'property']);
                $this->applyFirmScope($rentQuery, $request, 'firm_id', 'rental');
                if ($fromDate)
                    $rentQuery->whereDate('payment_date', '>=', $fromDate);
                if ($toDate)
                    $rentQuery->whereDate('payment_date', '<=', $toDate);
                if ($mode)
                    $rentQuery->where('payment_mode', $mode);
                if ($status)
                    $rentQuery->where('status', $status);
                if ($propId) {
                    $rentQuery->where(function ($q) use ($propId) {
                        $q
                            ->where('property_id', $propId)
                            ->orWhereHas('rental.property', fn($p) => $p->where('id', $propId))
                            ->orWhereHas('rental.properties', fn($p) => $p->where('properties.id', $propId));
                    });
                }
                if ($projectId) {
                    $rentQuery->where(function ($q) use ($projectId, $linkedPmId) {
                        $q
                            ->whereHas('property', function ($p) use ($projectId, $linkedPmId) {
                                $p->where('project_id', $projectId)
                                  ->when($linkedPmId, fn($sq) => $sq->orWhere('property_master_id', $linkedPmId));
                            })
                            ->orWhereHas('rental.property', function ($p) use ($projectId, $linkedPmId) {
                                $p->where('project_id', $projectId)
                                  ->when($linkedPmId, fn($sq) => $sq->orWhere('property_master_id', $linkedPmId));
                            })
                            ->orWhereHas('rental.properties', function ($p) use ($projectId, $linkedPmId) {
                                $p->where('project_id', $projectId)
                                  ->when($linkedPmId, fn($sq) => $sq->orWhere('property_master_id', $linkedPmId));
                            });
                    });
                }

                foreach ($rentQuery->get() as $r) {
                    $st = strtolower($r->status ?? 'paid');
                    $propName = $r->property?->property_name
                        ?: ($r->rental?->property?->property_name
                            ?: ($r->rental?->properties?->pluck('property_name')->join(', ') ?: '—'));
                    $items->push((object) [
                        'id' => 'RNT-' . $r->id,
                        'raw_id' => $r->id,
                        'model_type' => 'rental_payment',
                        'direction' => 'received',
                        'direction_label' => 'Received',
                        'payment_date' => $r->payment_date,
                        'party_name' => $r->rental?->tenant_name ?? 'Tenant',
                        'party_sub' => $r->rental?->tenant_mobile ? 'Mob: ' . $r->rental->tenant_mobile : 'Tenant Rental',
                        'property_name' => $propName,
                        'particular' => 'Rental Income' . ($r->payment_month ? ' (' . $r->payment_month . ' ' . $r->payment_year . ')' : ''),
                        'reference' => $r->receipt_no ?: ($r->transaction_id ? 'Txn: ' . $r->transaction_id : 'Rent Receipt'),
                        'payment_mode' => $r->payment_mode ?? 'Cash',
                        'amount' => (float) $r->paid_amount,
                        'pending_amount' => (float) ($r->pending_amount ?? 0),
                        'status' => ucfirst($r->status ?? 'paid'),
                        'status_class' => match ($st) {
                            'paid' => 'pb-paid',
                            'partial' => 'pb-partial',
                            'cancelled' => 'pb-cancelled',
                            default => 'pb-pending',
                        },
                        'view_url' => $r->rental_id ? route('rental-payments.index', $r->rental_id) : route('rentals.index'),
                    ]);
                }
            }
        }

        // ── 2. PAID PAYMENTS (Outflows) ──
        if (($payType === 'all' || $payType === 'paid') && !$custId) {
            // (a) Expenses Paid
            $expQuery = Expense::with(['property', 'properties', 'vendor', 'expenseCategory', 'firm']);
            $this->applyFirmScope($expQuery, $request);
            if ($fromDate)
                $expQuery->whereDate('expense_date', '>=', $fromDate);
            if ($toDate)
                $expQuery->whereDate('expense_date', '<=', $toDate);
            if ($mode)
                $expQuery->where('payment_mode', $mode);
            if ($propId) {
                $expQuery->where(function ($q) use ($propId) {
                    $q
                        ->where('property_id', $propId)
                        ->orWhereHas('properties', fn($p) => $p->where('properties.id', $propId));
                });
            }
            if ($projectId) {
                $expQuery->where(function ($q) use ($projectId, $linkedPmId) {
                    $q
                        ->where('project_id', $projectId)
                        ->orWhereHas('property', function ($p) use ($projectId, $linkedPmId) {
                            $p->where('project_id', $projectId)
                              ->when($linkedPmId, fn($sq) => $sq->orWhere('property_master_id', $linkedPmId));
                        })
                        ->orWhereHas('properties', function ($p) use ($projectId, $linkedPmId) {
                            $p->where('project_id', $projectId)
                              ->when($linkedPmId, fn($sq) => $sq->orWhere('property_master_id', $linkedPmId));
                        });
                });
            }
            if ($status) {
                if ($status === 'paid' || $status === 'approved') {
                    $expQuery->where('approval_status', 'Approved');
                } elseif ($status === 'pending') {
                    $expQuery->where('approval_status', 'Pending');
                } elseif ($status === 'cancelled' || $status === 'rejected') {
                    $expQuery->where('approval_status', 'Rejected');
                }
            }

            foreach ($expQuery->get() as $e) {
                $appr = $e->approval_status ?? 'Approved';
                $stClass = match (strtolower($appr)) {
                    'approved', 'paid' => 'pb-paid',
                    'rejected' => 'pb-cancelled',
                    default => 'pb-pending',
                };
                $propName = $e->property?->property_name
                    ?: ($e->properties->isNotEmpty() ? $e->properties->pluck('property_name')->join(', ') : 'General');
                $items->push((object) [
                    'id' => 'EXP-' . $e->id,
                    'raw_id' => $e->id,
                    'model_type' => 'expense',
                    'direction' => 'paid',
                    'direction_label' => 'Paid',
                    'payment_date' => $e->expense_date,
                    'party_name' => $e->paid_to ?: ($e->vendor?->name ?? 'Vendor / Payee'),
                    'party_sub' => $e->expenseCategory?->name ?? ($e->expense_category ?? 'Expense Outflow'),
                    'property_name' => $propName,
                    'particular' => 'Expense — ' . ($e->expense_title ?: ($e->expenseCategory?->name ?? 'Paid')),
                    'reference' => $e->bill_no ? 'Bill #' . $e->bill_no : ($e->payment_reference ?? '—'),
                    'payment_mode' => $e->payment_mode ?? 'Cash',
                    'amount' => (float) $e->amount,
                    'pending_amount' => strtolower($appr) === 'pending' ? (float) $e->amount : 0.0,
                    'status' => strtolower($appr) === 'approved' ? 'Paid' : ucfirst($appr),
                    'status_class' => $stClass,
                    'view_url' => route('expenses.show', $e->id),
                ]);
            }

            // (b) Loan EMI Repayments Paid
            $loanQuery = LoanEmiSchedule::with(['loan.property', 'loan'])->whereIn('emi_status', ['Paid', 'Partial']);
            $this->applyFirmScope($loanQuery, $request, 'firm_id', 'loan');
            if ($fromDate)
                $loanQuery->whereDate('payment_date', '>=', $fromDate);
            if ($toDate)
                $loanQuery->whereDate('payment_date', '<=', $toDate);
            if ($mode)
                $loanQuery->where('payment_mode', $mode);
            if ($propId)
                $loanQuery->whereHas('loan.property', fn($p) => $p->where('id', $propId));
            if ($projectId) {
                $loanQuery->whereHas('loan.property', function ($p) use ($projectId, $linkedPmId) {
                    $p->where('project_id', $projectId)
                      ->when($linkedPmId, fn($sq) => $sq->orWhere('property_master_id', $linkedPmId));
                });
            }

            foreach ($loanQuery->get() as $emi) {
                $st = strtolower($emi->emi_status ?? 'paid');
                $items->push((object) [
                    'id' => 'EMI-' . $emi->id,
                    'raw_id' => $emi->id,
                    'model_type' => 'loan_emi',
                    'direction' => 'paid',
                    'direction_label' => 'Paid',
                    'payment_date' => $emi->payment_date,
                    'party_name' => $emi->loan?->bank_name ?: ($emi->loan?->person_name ?? 'Bank / Lender'),
                    'party_sub' => 'Loan Repayment EMI',
                    'property_name' => $emi->loan?->property?->property_name ?? '—',
                    'particular' => 'Loan EMI — ' . ($emi->emi_month ?? '') . '/' . ($emi->emi_year ?? ''),
                    'reference' => $emi->transaction_reference ?: 'EMI Due: ' . $emi->emi_date,
                    'payment_mode' => $emi->payment_mode ?? 'Bank Transfer',
                    'amount' => (float) $emi->paid_amount,
                    'pending_amount' => max(0, (float) $emi->emi_amount - (float) $emi->paid_amount),
                    'status' => ucfirst($emi->emi_status ?? 'Paid'),
                    'status_class' => match ($st) {
                        'paid' => 'pb-paid',
                        'partial' => 'pb-partial',
                        default => 'pb-pending',
                    },
                    'view_url' => route('loans.show', $emi->loan_id),
                ]);
            }
        }

        // Sort all transactions by payment_date descending
        return $items->sortByDesc(fn($x) => ($x->payment_date ?? '') . ' ' . $x->id)->values();
    }

    public function payments(Request $request)
    {
        $records = $this->getPaymentsData($request);

        $paymentType = $request->input('payment_type', 'all');

        $totalReceived = $records->where('direction', 'received')->sum('amount');
        $totalPaid = $records->where('direction', 'paid')->sum('amount');
        $netBalance = $totalReceived - $totalPaid;
        $totalPending = $records->sum('pending_amount');
        $totalTransactions = $records->count();

        // Calculate counts for tabs (unfiltered by payment_type)
        $unfilteredReq = $request->duplicate();
        $unfilteredReq->merge(['payment_type' => 'all']);
        $allRecords = $this->getPaymentsData($unfilteredReq);

        $countAll = $allRecords->count();
        $countReceived = $allRecords->where('direction', 'received')->count();
        $countPaid = $allRecords->where('direction', 'paid')->count();
        $sumAllReceived = $allRecords->where('direction', 'received')->sum('amount');
        $sumAllPaid = $allRecords->where('direction', 'paid')->sum('amount');

        // Today's summary
        $todayReceived = $allRecords->filter(fn($r) => $r->direction === 'received' && $r->payment_date && \Carbon\Carbon::parse($r->payment_date)->isToday())->sum('amount');
        $todayPaid = $allRecords->filter(fn($r) => $r->direction === 'paid' && $r->payment_date && \Carbon\Carbon::parse($r->payment_date)->isToday())->sum('amount');

        $custQuery = Customer::orderBy('name');
        $propQuery = Property::with('project')->orderBy('property_name');
        $this->applyFirmScope($custQuery, $request);
        $this->applyFirmScope($propQuery, $request);

        // Keep all properties so client-side JavaScript can dynamically filter by data-project-id
        $properties = $propQuery->get();
        $customers = $custQuery->get();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $projects = $this->getAvailableProjects($request);

        return view('admin.reports.payments', compact(
            'records', 'totalReceived', 'totalPaid', 'netBalance', 'totalPending',
            'totalTransactions', 'todayReceived', 'todayPaid',
            'customers', 'properties', 'firms', 'projects',
            'paymentType', 'countAll', 'countReceived', 'countPaid',
            'sumAllReceived', 'sumAllPaid'
        ));
    }

    public function paymentsExportPdf(Request $request)
    {
        $records = $this->getPaymentsData($request);
        $paymentType = $request->input('payment_type', 'all');
        $totalReceived = $records->where('direction', 'received')->sum('amount');
        $totalPaid = $records->where('direction', 'paid')->sum('amount');
        $netBalance = $totalReceived - $totalPaid;
        $totalPending = $records->sum('pending_amount');
        $totalTransactions = $records->count();

        return view('admin.reports.payments-pdf', compact(
            'records', 'totalReceived', 'totalPaid', 'netBalance', 'totalPending',
            'totalTransactions', 'paymentType'
        ));
    }

    public function paymentsExportExcel(Request $request)
    {
        $records = $this->getPaymentsData($request);
        $paymentType = $request->input('payment_type', 'all');
        $totalReceived = $records->where('direction', 'received')->sum('amount');
        $totalPaid = $records->where('direction', 'paid')->sum('amount');
        $netBalance = $totalReceived - $totalPaid;
        $totalPending = $records->sum('pending_amount');
        $totalTransactions = $records->count();

        $filename = 'payment-report-' . $paymentType . '-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($records, $request, $totalReceived, $totalPaid, $netBalance, $totalPending, $totalTransactions, $paymentType) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($h, ['Delawala Properties & Management - Payment Report (' . ucfirst($paymentType) . ')']);
            fputcsv($h, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
                fputcsv($h, ['Date Range', $fromDisplay . ' to ' . $toDisplay]);
            }
            fputcsv($h, []);

            fputcsv($h, ['SUMMARY']);
            fputcsv($h, ['Total Transactions', $totalTransactions]);
            fputcsv($h, ['Total Received (મળ્યા)', number_format($totalReceived, 2)]);
            fputcsv($h, ['Total Paid (આપ્યા)', number_format($totalPaid, 2)]);
            fputcsv($h, ['Net Balance (મળ્યા - આપ્યા)', number_format($netBalance, 2)]);
            fputcsv($h, ['Total Pending', number_format($totalPending, 2)]);
            fputcsv($h, []);

            fputcsv($h, [
                'Sr', 'Payment Date', 'Type (મળ્યા / આપ્યા)', 'Party Name',
                'Property / Project', 'Particular', 'Reference / Bill No',
                'Payment Mode', 'Amount (₹)', 'Pending Amount (₹)', 'Status'
            ]);

            foreach ($records as $i => $r) {
                fputcsv($h, [
                    $i + 1,
                    $r->payment_date ? \Carbon\Carbon::parse($r->payment_date)->format('d M Y') : '-',
                    $r->direction === 'received' ? 'Received (મળ્યા)' : 'Paid (આપ્યા)',
                    $r->party_name ?? '-',
                    $r->property_name ?? '-',
                    $r->particular ?? '-',
                    $r->reference ?? '-',
                    $r->payment_mode ?? '-',
                    number_format($r->amount, 2),
                    number_format($r->pending_amount, 2),
                    $r->status ?? 'Paid',
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
                $q->where('tenant_name', 'like', "%{$search}%"));
        }
        if ($request->filled('filter_property')) {
            $query->where(function ($q) use ($request) {
                $q
                    ->where('property_id', $request->filter_property)
                    ->orWhereHas('rental', fn($r) => $r->where('property_id', $request->filter_property));
            });
        }
        if ($request->filled('filter_property_type')) {
            $typeId = $request->filter_property_type;
            $query->where(function ($q) use ($typeId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('property_type_id', $typeId))
                    ->orWhereHas('rental.property', fn($p) => $p->where('property_type_id', $typeId));
            });
        }
        if ($request->filled('filter_project')) {
            $projectId = $request->filter_project;
            $query->where(function ($q) use ($projectId) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('rental.property', fn($p) => $p->where('project_id', $projectId))
                    ->orWhereHas('rental.properties', fn($p) => $p->where('project_id', $projectId));
            });
        }

        return $query
            ->orderByDesc('payment_year')
            ->orderByDesc('payment_month')
            ->get();
    }

    public function rentals(Request $request)
    {
        $records = $this->getRentalsData($request);

        $totalRentAmt = $records->sum('rent_amount');
        $totalReceived = $records->sum('paid_amount');
        $totalPending = $records->sum('pending_amount');

        $activeQuery = Rental::where('rental_status', 'active');
        $propQuery = Property::orderBy('property_name');
        $this->applyFirmScope($activeQuery, $request);
        $this->applyFirmScope($propQuery, $request);
        if ($request->filled('filter_project')) {
            $activeQuery->where(function ($q) use ($request) {
                $q
                    ->whereHas('property', fn($p) => $p->where('project_id', $request->filter_project))
                    ->orWhereHas('properties', fn($p) => $p->where('project_id', $request->filter_project));
            });
            $propQuery->where('project_id', $request->filter_project);
        }

        $totalActive = $activeQuery->count();
        $properties = $propQuery->get();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $propertyTypes = PropertyType::where('status', 'active')->orderBy('name')->get();
        $projects = $this->getAvailableProjects($request);

        return view('admin.reports.rentals', compact(
            'records', 'totalRentAmt', 'totalReceived',
            'totalPending', 'totalActive', 'properties', 'firms', 'propertyTypes', 'projects'
        ));
    }

    public function rentalsExportPdf(Request $request)
    {
        $records = $this->getRentalsData($request);
        $totalRentAmt = $records->sum('rent_amount');
        $totalReceived = $records->sum('paid_amount');
        $totalPending = $records->sum('pending_amount');

        return view('admin.reports.rentals-pdf', compact(
            'records', 'totalRentAmt', 'totalReceived', 'totalPending'
        ));
    }

    public function rentalsExportExcel(Request $request)
    {
        $records = $this->getRentalsData($request);
        $totalRentAmt = $records->sum('rent_amount');
        $totalReceived = $records->sum('paid_amount');
        $totalPending = $records->sum('pending_amount');

        $filename = 'rental-report-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($records, $request, $totalRentAmt, $totalReceived, $totalPending) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($h, ['Delawala Properties & Management - Rental Report']);
            fputcsv($h, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
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
                'Sr',
                'Firm',
                'Payment Date',
                'Month/Year',
                'Tenant Name',
                'Tenant Mobile',
                'Property Name',
                'Property Type',
                'Property Code',
                'Monthly Rent',
                'Paid Amount',
                'Pending Amount',
                'Payment Mode',
                'Status',
            ]);

            foreach ($records as $i => $rp) {
                fputcsv($h, [
                    $i + 1,
                    $rp->firm->firm_name ?? $rp->rental?->firm?->firm_name ?? '-',
                    $rp->payment_date ? \Carbon\Carbon::parse($rp->payment_date)->format('d M Y') : '-',
                    $rp->payment_month . ' ' . $rp->payment_year,
                    $rp->rental?->tenant_name ?? '-',
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
            $query->whereHas('stockInwards', function ($q) use ($request) {
                $q->where('supplier_name', $request->filter_supplier);
            });
        }

        if ($request->filled('filter_project')) {
            $projectId = $request->filter_project;
            $query->where(function ($q) use ($projectId) {
                $q
                    ->where('project_id', $projectId)
                    ->orWhereHas('stockInwards', fn($si) => $si->where('project_id', $projectId))
                    ->orWhereHas('stockOutwards', fn($so) => $so->where('project_id', $projectId));
            });
        }

        $materials = $query->orderBy('material_name')->get();

        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate = $request->filled('to_date') ? $request->to_date : null;

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

            $m->computed_opening = (float) $m->opening_stock + (float) $inwardsBefore - (float) $outwardsBefore;

            $inwardQuery = StockInward::where('material_id', $m->id);
            if ($fromDate)
                $inwardQuery->whereDate('inward_date', '>=', $fromDate);
            if ($toDate)
                $inwardQuery->whereDate('inward_date', '<=', $toDate);
            if ($request->filled('filter_supplier')) {
                $inwardQuery->where('supplier_name', $request->filter_supplier);
            }
            if ($request->filled('filter_project')) {
                $inwardQuery->where('project_id', $request->filter_project);
            }
            $m->computed_inward = (float) $inwardQuery->sum('quantity');

            $outwardQuery = StockOutward::where('material_id', $m->id);
            if ($fromDate)
                $outwardQuery->whereDate('outward_date', '>=', $fromDate);
            if ($toDate)
                $outwardQuery->whereDate('outward_date', '<=', $toDate);
            if ($request->filled('filter_project')) {
                $outwardQuery->where('project_id', $request->filter_project);
            }
            $m->computed_outward = (float) $outwardQuery->sum('quantity');

            $m->computed_available = $m->computed_opening + $m->computed_inward - $m->computed_outward;

            $latestInwardDate = StockInward::where('material_id', $m->id)->max('inward_date');
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
            $materials = $materials->filter(function ($m) use ($statusFilter) {
                if ($statusFilter === 'in_stock')
                    return $m->stock_status === 'In Stock';
                if ($statusFilter === 'low_stock')
                    return $m->stock_status === 'Low Stock';
                if ($statusFilter === 'out_of_stock')
                    return $m->stock_status === 'Out of Stock';
                return true;
            });
        }

        return $materials;
    }

    public function inventory(Request $request)
    {
        $materials = $this->getInventoryData($request);

        $totalMaterials = $materials->count();
        $totalStockQty = $materials->sum('computed_available');
        $lowStockItems = $materials->where('stock_status', 'Low Stock')->count();
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
        $projects = $this->getAvailableProjects($request);

        return view('admin.reports.inventory', compact(
            'materials', 'categories', 'suppliers', 'firms', 'projects',
            'totalMaterials', 'totalStockQty', 'lowStockItems', 'outOfStockItems'
        ));
    }

    public function inventoryExportPdf(Request $request)
    {
        $materials = $this->getInventoryData($request);
        $totalMaterials = $materials->count();
        $totalStockQty = $materials->sum('computed_available');
        $lowStockItems = $materials->where('stock_status', 'Low Stock')->count();
        $outOfStockItems = $materials->where('stock_status', 'Out of Stock')->count();

        return view('admin.reports.inventory-pdf', compact(
            'materials', 'totalMaterials', 'totalStockQty', 'lowStockItems', 'outOfStockItems'
        ));
    }

    public function inventoryExportExcel(Request $request)
    {
        $materials = $this->getInventoryData($request);
        $filename = 'inventory-report-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($materials, $request) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($h, ['Delawala Properties & Management - Inventory Report']);
            fputcsv($h, ['Generated on', date('d M Y, h:i A')]);
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $fromDisplay = $request->filled('from_date') ? \Carbon\Carbon::parse($request->from_date)->format('d M Y') : 'All time';
                $toDisplay = $request->filled('to_date') ? \Carbon\Carbon::parse($request->to_date)->format('d M Y') : 'Now';
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
                'Sr',
                'Material Name',
                'Category',
                'Unit',
                'Opening Stock',
                'Inward Qty',
                'Outward Qty',
                'Available Stock',
                'Min Stock Level',
                'Stock Status',
                'Latest Activity',
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
