<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'dashboard' => 'Dashboard',
            'customer' => 'Customers',
            'broker' => 'Brokers',
            'broker_commission' => 'Broker Commissions',
            'vendor' => 'Vendors',
            'tenant' => 'Tenants',
            'property_type' => 'Property Type',
            'payment_mode' => 'Payment Mode',
            'expense_category' => 'Expense Category',
            'property' => 'Property',
            'property_sales' => 'Property Sales',
            'property_documents' => 'Property Documents',
            'purchase' => 'Purchase',
            'booking' => 'Booking',
            'rental' => 'Rental',
            'payment' => 'Payment',
            'receipt' => 'Receipt',
            'inventory' => 'Inventory',
            'expense' => 'Expense',
            'income' => 'Income',
            'loan' => 'Loan',
            'reports' => 'Reports',
            'loan_report' => 'Loan Report',
            'expense_report' => 'Expense Report',
            'ledger' => 'Ledger',
            'credit_note' => 'Credit Note',
            'debit_note' => 'Debit Note',
            'audit_logs' => 'Audit Logs',
            'backup' => 'Backup',
            'user_management' => 'User Management',
            'role_permission' => 'Role & Permission',
            'form_management' => 'Form Management',
        ];

        $actions = ['view', 'add', 'edit', 'delete', 'print', 'export'];

        $permissions = [];
        $now = now();

        foreach ($modules as $key => $name) {
            foreach ($actions as $action) {
                $permissions[] = [
                    'module_name' => $name,
                    'permission_key' => "{$key}_{$action}",
                    'action' => $action,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Clear existing permissions to avoid duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Insert in chunks to avoid memory issues
        foreach (array_chunk($permissions, 100) as $chunk) {
            DB::table('permissions')->insert($chunk);
        }

        $this->command->info('Permissions seeded successfully!');
    }
}
