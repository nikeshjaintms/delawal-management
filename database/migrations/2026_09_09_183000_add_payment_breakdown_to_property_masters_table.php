<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('property_masters')) {
            Schema::table('property_masters', function (Blueprint $table) {
                if (!Schema::hasColumn('property_masters', 'paid_amount')) {
                    $table->decimal('paid_amount', 15, 2)->default(0.00)->after('purchase_price');
                }
                if (!Schema::hasColumn('property_masters', 'due_amount')) {
                    $table->decimal('due_amount', 15, 2)->default(0.00)->after('paid_amount');
                }
            });

            // Initialize existing records
            DB::statement("
                UPDATE property_masters
                SET paid_amount = IF(payment_status = 'paid' OR payment_status IS NULL, purchase_price, 0),
                    due_amount = IF(payment_status = 'paid' OR payment_status IS NULL, 0, purchase_price)
                WHERE purchase_price > 0
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('property_masters')) {
            Schema::table('property_masters', function (Blueprint $table) {
                if (Schema::hasColumn('property_masters', 'due_amount')) {
                    $table->dropColumn('due_amount');
                }
                if (Schema::hasColumn('property_masters', 'paid_amount')) {
                    $table->dropColumn('paid_amount');
                }
            });
        }
    }
};
