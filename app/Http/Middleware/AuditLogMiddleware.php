<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AuditLog;

class AuditLogMiddleware
{
    /**
     * Route-prefix → human-readable module name map.
     */
    private array $moduleMap = [
        'customers'          => 'Customer',
        'brokers'            => 'Broker',
        'vendors'            => 'Vendor',
        'tenants'            => 'Tenant',
        'property-types'     => 'Property Type',
        'payment-modes'      => 'Payment Mode',
        'expense-categories' => 'Expense Category',
        'properties'         => 'Property',
        'property-sales'     => 'Property Sale',
        'payments'           => 'Payment',
        'rentals'            => 'Rental',
        'rental-payments'    => 'Rental Payment',
        'expenses'           => 'Expense',
        'material-categories'=> 'Material Category',
        'materials'          => 'Material',
        'stock-inwards'      => 'Stock Inward',
        'stock-outwards'     => 'Stock Outward',
        'loans'              => 'Loan',
        'ledgers'            => 'Ledger',
        'credit-notes'       => 'Credit Note',
        'debit-notes'        => 'Debit Note',
        'backups'            => 'Backup System',
        'users'              => 'User Management',
        'forms'              => 'Form Management',
        'form-submissions'   => 'Form Submission',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track successful responses
        $status = $response->getStatusCode();
        if ($status < 200 || $status >= 400) {
            return $response;
        }

        $routeName = $request->route()?->getName() ?? '';
        $method    = $request->method();
        $path      = $request->getPathInfo();

        // ── 1. Export / Report actions ─────────────────────────────
        $isPdf   = str_contains($routeName, '.pdf')   || str_contains($path, 'export-pdf');
        $isExcel = str_contains($routeName, '.excel') || str_contains($path, 'export-excel');

        if ($isPdf || $isExcel) {
            $module = $this->resolveReportModule($routeName, $path);
            $action = $isPdf ? 'Export PDF' : 'Export Excel';
            $format = $isPdf ? 'PDF' : 'Excel';
            AuditLog::log($module, $action, "Exported {$module} in {$format} format");
            return $response;
        }

        // ── 2. Backup generate ────────────────────────────────────
        // Note: Backup Generate is logged directly in BackupController to log detail (e.g. filename)
        if ($routeName === 'backups.generate' && $method === 'POST') {
            return $response;
        }

        // ── 3. CRUD actions (POST = Create, PUT/PATCH = Update, DELETE = Delete) ──
        // Only track on redirects (successful store/update/destroy return redirect)
        if ($status >= 300 && $status < 400 && in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            [$module, $action, $description] = $this->resolveCrudAction($routeName, $method, $request);
            if ($module && $action) {
                AuditLog::log($module, $action, $description);
            }
        }

        return $response;
    }

    private function resolveCrudAction(string $routeName, string $method, Request $request): array
    {
        // Match route name patterns like "customers.store", "customers.update", "customers.destroy"
        foreach ($this->moduleMap as $prefix => $moduleName) {
            if (str_starts_with($routeName, $prefix . '.')) {
                $suffix = substr($routeName, strlen($prefix) + 1);

                if ($suffix === 'store' || ($method === 'POST' && !str_contains($routeName, '.'))) {
                    return [$moduleName, 'Create Record', "New {$moduleName} record was created"];
                }
                if (in_array($suffix, ['update'])) {
                    return [$moduleName, 'Update Record', "{$moduleName} record was updated"];
                }
                if ($suffix === 'destroy') {
                    return [$moduleName, 'Delete Record', "{$moduleName} record was deleted"];
                }
                // Nested resource actions (e.g. rental-payments.store)
                if (str_contains($suffix, 'store')) {
                    return [$moduleName, 'Create Record', "New {$moduleName} record was created"];
                }
                if (str_contains($suffix, 'pay')) {
                    return [$moduleName, 'Update Record', "{$moduleName} payment was recorded"];
                }
            }
        }

        // Loan EMI pay
        if (str_contains($routeName, 'loans.emi-pay')) {
            return ['Loan', 'Update Record', 'Loan EMI payment was recorded'];
        }

        return [null, null, ''];
    }

    private function resolveReportModule(string $routeName, string $path): string
    {
        $reportMap = [
            'stock-report'   => 'Stock Report',
            'expense-report' => 'Expense Report',
            'loan-report'    => 'Loan Report',
            'gst-sales'      => 'GST Sales Report',
            'gst-purchase'   => 'GST Purchase Report',
            'credit-note'    => 'Credit Note Report',
            'debit-note'     => 'Debit Note Report',
            'profit-loss'    => 'Profit & Loss Report',
            'sales'          => 'Sales Report',
            'payments'       => 'Payment Report',
            'rentals'        => 'Rental Report',
            'inventory'      => 'Inventory Report',
        ];

        foreach ($reportMap as $key => $name) {
            if (str_contains($routeName, $key) || str_contains($path, $key)) {
                return $name;
            }
        }

        return 'Report';
    }
}
