<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BrokerController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PropertyTypeController;
use App\Http\Controllers\PaymentModeController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertySaleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\RentalPaymentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MaterialCategoryController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\StockInwardController;
use App\Http\Controllers\StockOutwardController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\ExpenseReportController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanReportController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\DebitNoteController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormSubmissionController;
use App\Http\Controllers\FirmController;
use App\Http\Controllers\FinancialYearController;
use App\Http\Controllers\InvoiceSettingController;
use App\Http\Controllers\PropertyDocumentController;
use App\Http\Controllers\PropertyStatusController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/firm-selection', [AuthController::class, 'showFirmSelection'])->name('firm-selection');
Route::post('/firm-selection', [AuthController::class, 'submitFirmSelection'])->name('firm-selection.submit');

Route::middleware(['erp.auth', \App\Http\Middleware\AuditLogMiddleware::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Masters ──────────────────────────────────────────────────────
    Route::resource('customers', CustomerController::class)->middleware(['permission:customer_view']);
    Route::resource('brokers', BrokerController::class)->middleware(['permission:broker_view']);
    Route::get('broker-commissions/export-excel', [\App\Http\Controllers\BrokerCommissionController::class, 'exportExcel'])->name('broker-commissions.excel')->middleware(['permission:broker_commission_export']);
    Route::get('broker-commissions/export-pdf', [\App\Http\Controllers\BrokerCommissionController::class, 'exportPdf'])->name('broker-commissions.pdf')->middleware(['permission:broker_commission_print']);
    Route::resource('broker-commissions', \App\Http\Controllers\BrokerCommissionController::class)->middleware(['permission:broker_commission_view']);
    Route::patch('broker-commissions/{brokerCommission}/toggle-status', [\App\Http\Controllers\BrokerCommissionController::class, 'toggleStatus'])->name('broker-commissions.toggle-status')->middleware(['permission:broker_commission_edit']);
    Route::resource('vendors', VendorController::class)->middleware(['permission:vendor_view']);
    Route::resource('tenants', TenantController::class)->middleware(['permission:tenant_view']);
    Route::resource('property-types', PropertyTypeController::class)->middleware(['permission:property_type_view']);
    Route::resource('payment-modes', PaymentModeController::class)->middleware(['permission:payment_mode_view']);
    Route::resource('expense-categories', ExpenseCategoryController::class)->middleware(['permission:expense_category_view']);

    // ── Property ─────────────────────────────────────────────────────
    Route::resource('properties', PropertyController::class)->middleware(['permission:property_view']);
    Route::resource('property-sales', PropertySaleController::class)->middleware(['permission:property_sales_view']);
    Route::resource('property-documents', PropertyDocumentController::class)->middleware(['permission:property_documents_view']);
    Route::resource('property-availability', PropertyStatusController::class)->middleware(['permission:property_view']);
    Route::resource('payments', PaymentController::class)->middleware(['permission:payment_view']);
    Route::get('payments/booking-info/{id}', [PaymentController::class, 'getBookingInfo'])->name('payments.booking-info')->middleware(['permission:payment_view']);
    Route::resource('rentals', RentalController::class)->middleware(['permission:rental_view']);
    Route::get('rentals/{rental}/payments', [RentalPaymentController::class, 'index'])->name('rental-payments.index')->middleware(['permission:rental_view']);
    Route::get('rentals/{rental}/payments/create', [RentalPaymentController::class, 'create'])->name('rental-payments.create')->middleware(['permission:rental_view']);
    Route::post('rentals/{rental}/payments', [RentalPaymentController::class, 'store'])->name('rental-payments.store')->middleware(['permission:rental_view']);
    Route::delete('rentals/{rental}/payments/{rentalPayment}', [RentalPaymentController::class, 'destroy'])->name('rental-payments.destroy')->middleware(['permission:rental_view']);

    // ── Booking & Purchase ───────────────────────────────────────────
    Route::resource('bookings', \App\Http\Controllers\BookingController::class)->middleware(['permission:booking_view']);
    Route::resource('purchases', \App\Http\Controllers\PurchaseController::class)->middleware(['permission:purchase_view']);

    // ── Inventory ────────────────────────────────────────────────────
    Route::resource('expenses', ExpenseController::class)->middleware(['permission:expense_view']);
    Route::resource('material-categories', MaterialCategoryController::class)->middleware(['permission:inventory_view']);
    Route::resource('materials', MaterialController::class)->middleware(['permission:inventory_view']);
    Route::resource('stock-inwards', StockInwardController::class)->middleware(['permission:inventory_view']);
    Route::resource('stock-outwards', StockOutwardController::class)->middleware(['permission:inventory_view']);
    Route::get('stock-report', [StockReportController::class, 'index'])->name('stock-report.index')->middleware(['permission:inventory_view']);
    Route::get('stock-report/export-pdf', [StockReportController::class, 'exportPdf'])->name('stock-report.pdf')->middleware(['permission:inventory_export']);
    Route::get('stock-report/export-excel', [StockReportController::class, 'exportExcel'])->name('stock-report.excel')->middleware(['permission:inventory_export']);

    // ── Finance ──────────────────────────────────────────────────────
    Route::resource('incomes', \App\Http\Controllers\IncomeController::class)->middleware(['permission:income_view']);
    Route::resource('receipts', \App\Http\Controllers\ReceiptController::class)->middleware(['permission:receipt_view']);

    // ── Expense Report ───────────────────────────────────────────────
    Route::get('expense-report', [ExpenseReportController::class, 'index'])->name('expense-report.index')->middleware(['permission:expense_report_view']);
    Route::get('expense-report/export-pdf', [ExpenseReportController::class, 'exportPdf'])->name('expense-report.pdf')->middleware(['permission:expense_report_export']);
    Route::get('expense-report/export-excel', [ExpenseReportController::class, 'exportExcel'])->name('expense-report.excel')->middleware(['permission:expense_report_export']);

    // ── Loan Management ──────────────────────────────────────────────
    Route::resource('loans', LoanController::class)->middleware(['permission:loan_view']);
    Route::get('loans/{loan}/emi-schedule', [LoanController::class, 'emiSchedule'])->name('loans.emi-schedule')->middleware(['permission:loan_view']);
    Route::post('loans/{loan}/emi-schedule/{emi}/pay', [LoanController::class, 'emiPay'])->name('loans.emi-pay')->middleware(['permission:loan_view']);

    // ── Loan Report ──────────────────────────────────────────────────
    Route::get('loan-report', [LoanReportController::class, 'index'])->name('loan-report.index')->middleware(['permission:loan_report_view']);
    Route::get('loan-report/export-pdf', [LoanReportController::class, 'exportPdf'])->name('loan-report.pdf')->middleware(['permission:loan_report_export']);
    Route::get('loan-report/export-excel', [LoanReportController::class, 'exportExcel'])->name('loan-report.excel')->middleware(['permission:loan_report_export']);

    // ── GST / Accounts ───────────────────────────────────────────────
    Route::resource('ledgers', LedgerController::class)->middleware(['permission:ledger_view']);
    Route::resource('credit-notes', CreditNoteController::class)->middleware(['permission:credit_note_view']);
    Route::resource('debit-notes', DebitNoteController::class)->middleware(['permission:debit_note_view']);

    // ── Reports Module ───────────────────────────────────────────────
    Route::prefix('reports')->name('reports.')->middleware(['permission:reports_view'])->group(function () {
        Route::get('/',          [ReportsController::class, 'index'])->name('index');
        Route::get('gst-sales',  [ReportsController::class, 'gstSales'])->name('gst-sales');
        Route::get('gst-sales/export-pdf',  [ReportsController::class, 'gstSalesExportPdf'])->name('gst-sales.pdf');
        Route::get('gst-sales/export-excel',[ReportsController::class, 'gstSalesExportExcel'])->name('gst-sales.excel');
        Route::get('gst-purchase',[ReportsController::class,'gstPurchase'])->name('gst-purchase');
        Route::get('gst-purchase/export-pdf',  [ReportsController::class,'gstPurchaseExportPdf'])->name('gst-purchase.pdf');
        Route::get('gst-purchase/export-excel',[ReportsController::class,'gstPurchaseExportExcel'])->name('gst-purchase.excel');
        Route::get('credit-note',[ReportsController::class, 'creditNote'])->name('credit-note');
        Route::get('credit-note/export-pdf',  [ReportsController::class, 'creditNoteExportPdf'])->name('credit-note.pdf');
        Route::get('credit-note/export-excel',[ReportsController::class, 'creditNoteExportExcel'])->name('credit-note.excel');
        Route::get('debit-note', [ReportsController::class, 'debitNote'])->name('debit-note');
        Route::get('debit-note/export-pdf',  [ReportsController::class, 'debitNoteExportPdf'])->name('debit-note.pdf');
        Route::get('debit-note/export-excel',[ReportsController::class, 'debitNoteExportExcel'])->name('debit-note.excel');
        Route::get('profit-loss',[ReportsController::class, 'profitLoss'])->name('profit-loss');
        Route::get('profit-loss/export-pdf',  [ReportsController::class, 'profitLossExportPdf'])->name('profit-loss.pdf');
        Route::get('profit-loss/export-excel',[ReportsController::class, 'profitLossExportExcel'])->name('profit-loss.excel');
        Route::get('balance-sheet',[ReportsController::class,'balanceSheet'])->name('balance-sheet');
        Route::get('balance-sheet/export-excel',[ReportsController::class,'balanceSheetExportExcel'])->name('balance-sheet.excel');
        Route::get('cash-flow',  [ReportsController::class, 'cashFlow'])->name('cash-flow');
        Route::get('cash-flow/export-excel',[ReportsController::class,'cashFlowExportExcel'])->name('cash-flow.excel');
        Route::get('sales',      [ReportsController::class, 'sales'])->name('sales');
        Route::get('sales/export-pdf',  [ReportsController::class,'salesExportPdf'])->name('sales.pdf');
        Route::get('sales/export-excel',[ReportsController::class,'salesExportExcel'])->name('sales.excel');
        Route::get('payments',   [ReportsController::class, 'payments'])->name('payments');
        Route::get('payments/export-pdf',  [ReportsController::class,'paymentsExportPdf'])->name('payments.pdf');
        Route::get('payments/export-excel',[ReportsController::class,'paymentsExportExcel'])->name('payments.excel');
        Route::get('rentals',    [ReportsController::class, 'rentals'])->name('rentals');
        Route::get('rentals/export-pdf',  [ReportsController::class,'rentalsExportPdf'])->name('rentals.pdf');
        Route::get('rentals/export-excel',[ReportsController::class,'rentalsExportExcel'])->name('rentals.excel');
        Route::get('inventory',  [ReportsController::class, 'inventory'])->name('inventory');
        Route::get('inventory/export-pdf',  [ReportsController::class, 'inventoryExportPdf'])->name('inventory.pdf');
        Route::get('inventory/export-excel',[ReportsController::class, 'inventoryExportExcel'])->name('inventory.excel');
    });

    // ── Admin-Only Restricted Routes ─────────────────────────────────
    Route::middleware(['admin.only'])->group(function () {
        // ── User Management ──────────────────────────────────────────────
        Route::resource('users', UserController::class)->middleware(['permission:user_management_view']);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status')->middleware(['permission:user_management_edit']);

        // ── Role & Permission Management ─────────────────────────────────
        Route::resource('roles', RoleController::class)->middleware(['permission:role_permission_view']);
        Route::patch('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status')->middleware(['permission:role_permission_edit']);
        Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions')->middleware(['permission:role_permission_view']);
        Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update')->middleware(['permission:role_permission_edit']);

        // ── Form Management ──────────────────────────────────────────────
        Route::resource('forms', FormController::class)->middleware(['permission:form_management_view']);
        Route::patch('forms/{form}/toggle-status', [FormController::class, 'toggleStatus'])->name('forms.toggle-status')->middleware(['permission:form_management_edit']);
        Route::post('forms/{form}/submit', [FormController::class, 'submit'])->name('forms.submit')->middleware(['permission:form_management_view']);
        Route::resource('form-submissions', FormSubmissionController::class)->only(['index', 'show', 'destroy'])->middleware(['permission:form_management_view']);

        // ── Audit Logs ───────────────────────────────────────────────────
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index')->middleware(['permission:audit_logs_view']);
        Route::post('audit-logs/track', [AuditLogController::class, 'track'])->name('audit-logs.track');

        // ── Backup System ────────────────────────────────────────────────
        Route::get('backups', [BackupController::class, 'index'])->name('backups.index')->middleware(['permission:backup_view']);
        Route::post('backups/generate', [BackupController::class, 'generate'])->name('backups.generate')->middleware(['permission:backup_add']);
        Route::get('backups/{id}/download', [BackupController::class, 'download'])->name('backups.download')->middleware(['permission:backup_view']);
        Route::delete('backups/{id}', [BackupController::class, 'destroy'])->name('backups.destroy')->middleware(['permission:backup_delete']);

        // ── Firm Management ──────────────────────────────────────────────
        Route::resource('firm-master', FirmController::class);
        Route::resource('financial-years', FinancialYearController::class);
        Route::patch('financial-years/{financialYear}/set-active', [FinancialYearController::class, 'setActive'])->name('financial-years.set-active');
        Route::resource('invoice-settings', InvoiceSettingController::class);
        Route::get('invoice-settings/{invoiceSetting}/preview', [InvoiceSettingController::class, 'preview'])->name('invoice-settings.preview');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});