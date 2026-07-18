<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Ensure required roles exist ──────────────────────────
        $roleDefinitions = [
            ['name' => 'Admin',         'role_name' => 'Admin',         'description' => 'Full system access — all modules',        'status' => 'active'],
            ['name' => 'Manager',       'role_name' => 'Manager',       'description' => 'Operations and property management',       'status' => 'active'],
            ['name' => 'Sales User',    'role_name' => 'Sales User',    'description' => 'Sales, bookings, and customer management', 'status' => 'active'],
            ['name' => 'Accountant',    'role_name' => 'Accountant',    'description' => 'Finance, accounts, and reports',           'status' => 'active'],
            ['name' => 'Viewer',        'role_name' => 'Viewer',        'description' => 'Read-only access to all modules',          'status' => 'active'],
            // Keep legacy roles
            ['name' => 'Super Admin',   'role_name' => 'Super Admin',   'description' => 'Super administrator',                     'status' => 'active'],
            ['name' => 'Firm Admin',    'role_name' => 'Firm Admin',    'description' => 'Firm level administrator',                 'status' => 'active'],
            ['name' => 'Rental Manager','role_name' => 'Rental Manager','description' => 'Rental module management',                'status' => 'active'],
        ];

        foreach ($roleDefinitions as $rd) {
            Role::firstOrCreate(['name' => $rd['name']], $rd);
        }

        // ── 2. Modules mapped to permission keys ─────────────────────
        // Format: [ 'Display Name' => 'permission_key_prefix' ]
        $modules = [
            'Dashboard'           => 'dashboard',
            'User Management'     => 'user_management',
            'Role & Permission'   => 'role_permission',
            'Form Management'     => 'form_management',
            'Customer'            => 'customer',
            'Broker'              => 'broker',
            'Broker Commissions'  => 'broker_commission',
            'Vendor'              => 'vendor',
            'Tenant'              => 'tenant',
            'Property Type'       => 'property_type',
            'Payment Mode'        => 'payment_mode',
            'Expense Category'    => 'expense_category',
            'Property'            => 'property',
            'Property Sales'      => 'property_sales',
            'Purchase'            => 'purchase',
            'Sales'               => 'sales',
            'Booking'             => 'booking',
            'Rental'              => 'rental',
            'Inventory'           => 'inventory',
            'Expense'             => 'expense',
            'Income'              => 'income',
            'Payment'             => 'payment',
            'Receipt'             => 'receipt',
            'Loan'                => 'loan',
            'Reports'             => 'reports',
            'Loan Report'         => 'loan_report',
            'Sales Report'        => 'sales_report',
            'Payment Report'      => 'payment_report',
            'Rental Report'       => 'rental_report',
            'Inventory Report'    => 'inventory_report',
            'Expense Report'      => 'expense_report',
            'Audit Logs'          => 'audit_logs',
            'Backup'              => 'backup',
            'Ledger'              => 'ledger',
            'Credit Note'         => 'credit_note',
            'Debit Note'          => 'debit_note',
        ];

        $actions = ['view', 'add', 'edit', 'delete', 'print', 'export'];

        // ── 3. Create all permissions ────────────────────────────────
        foreach ($modules as $moduleName => $prefix) {
            foreach ($actions as $action) {
                $key = $prefix . '_' . $action;
                Permission::firstOrCreate(
                    ['permission_key' => $key],
                    ['module_name' => $moduleName, 'action' => $action]
                );
            }
        }

        // ── 4. Assign ALL permissions to Admin + Super Admin ─────────
        $adminRoleNames = ['Admin', 'Super Admin', 'Firm Admin'];
        $allPermissions = Permission::pluck('id')->toArray();

        foreach ($adminRoleNames as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) continue;
            foreach ($allPermissions as $permId) {
                RolePermission::firstOrCreate([
                    'role_id'       => $role->id,
                    'permission_id' => $permId,
                ]);
            }
        }

        // ── 5. Assign view-only permissions to Viewer ─────────────────
        $viewerRole = Role::where('name', 'Viewer')->first();
        if ($viewerRole) {
            RolePermission::where('role_id', $viewerRole->id)->delete();
            $viewPerms = Permission::where('action', 'view')->pluck('id')->toArray();
            foreach ($viewPerms as $permId) {
                RolePermission::firstOrCreate([
                    'role_id'       => $viewerRole->id,
                    'permission_id' => $permId,
                ]);
            }
        }

        // ── 6. Manager: all except User/Role management ───────────────
        $managerRole = Role::where('name', 'Manager')->first();
        if ($managerRole) {
            RolePermission::where('role_id', $managerRole->id)->delete();
            $managerPerms = Permission::whereNotIn('module_name', [
                'User Management', 'Role & Permission',
            ])->pluck('id')->toArray();
            foreach ($managerPerms as $permId) {
                RolePermission::firstOrCreate([
                    'role_id'       => $managerRole->id,
                    'permission_id' => $permId,
                ]);
            }
        }

        // ── 7. Sales User: sales-related modules ──────────────────────
        $salesRole = Role::where('name', 'Sales User')->first();
        if ($salesRole) {
            RolePermission::where('role_id', $salesRole->id)->delete();
            $salesModules = ['Dashboard', 'Customer', 'Broker', 'Property', 'Property Sales',
                             'Booking', 'Sales', 'Payment', 'Receipt'];
            $salesPerms = Permission::whereIn('module_name', $salesModules)->pluck('id')->toArray();
            foreach ($salesPerms as $permId) {
                RolePermission::firstOrCreate([
                    'role_id'       => $salesRole->id,
                    'permission_id' => $permId,
                ]);
            }
        }

        // ── 8. Accountant: finance modules ────────────────────────────
        $accountantRole = Role::where('name', 'Accountant')->first();
        if ($accountantRole) {
            RolePermission::where('role_id', $accountantRole->id)->delete();
            $financeModules = ['Dashboard', 'Expense', 'Income', 'Payment', 'Receipt',
                               'Loan', 'Ledger', 'Credit Note', 'Debit Note',
                               'Reports', 'Expense Report', 'Loan Report',
                               'Payment Report', 'Sales Report'];
            $financePerms = Permission::whereIn('module_name', $financeModules)->pluck('id')->toArray();
            foreach ($financePerms as $permId) {
                RolePermission::firstOrCreate([
                    'role_id'       => $accountantRole->id,
                    'permission_id' => $permId,
                ]);
            }
        }

        $this->command->info('Roles and permissions seeded successfully.');
        $this->command->info('Total permissions: ' . Permission::count());
        $this->command->info('Total role_permissions: ' . RolePermission::count());
    }
}
