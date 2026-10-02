<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->addIndexIfNotExists('properties', 'properties_firm_status_idx', ['firm_id', 'status']);
        $this->addIndexIfNotExists('properties', 'properties_master_idx', ['property_master_id']);
        $this->addIndexIfNotExists('properties', 'properties_project_idx', ['project_id']);

        $this->addIndexIfNotExists('property_sales', 'sales_firm_status_idx', ['firm_id', 'sale_status']);
        $this->addIndexIfNotExists('property_sales', 'sales_prop_id_idx', ['property_id']);
        $this->addIndexIfNotExists('property_sales', 'sales_customer_id_idx', ['customer_id']);
        $this->addIndexIfNotExists('property_sales', 'sales_broker_id_idx', ['broker_id']);
        $this->addIndexIfNotExists('property_sales', 'sales_date_idx', ['sale_date']);

        $this->addIndexIfNotExists('payments', 'payments_firm_status_idx', ['firm_id', 'status']);
        $this->addIndexIfNotExists('payments', 'payments_sale_id_idx', ['property_sale_id']);
        $this->addIndexIfNotExists('payments', 'payments_date_idx', ['payment_date']);

        $this->addIndexIfNotExists('expenses', 'expenses_firm_date_idx', ['firm_id', 'expense_date']);
        $this->addIndexIfNotExists('expenses', 'expenses_prop_id_idx', ['property_id']);
        $this->addIndexIfNotExists('expenses', 'expenses_project_id_idx', ['project_id']);

        $this->addIndexIfNotExists('bookings', 'bookings_firm_status_idx', ['firm_id', 'status']);
        $this->addIndexIfNotExists('bookings', 'bookings_prop_id_idx', ['property_id']);

        $this->addIndexIfNotExists('property_masters', 'prop_masters_firm_status_idx', ['firm_id', 'status']);
    }

    private function addIndexIfNotExists(string $table, string $indexName, array $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        try {
            $existing = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            if (empty($existing)) {
                Schema::table($table, function (Blueprint $t) use ($columns, $indexName) {
                    $t->index($columns, $indexName);
                });
            }
        } catch (\Throwable $e) {
            // Silently ignore if index already exists or column missing
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op on rollback
    }
};
