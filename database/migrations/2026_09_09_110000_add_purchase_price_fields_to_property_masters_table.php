<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
                if (!Schema::hasColumn('property_masters', 'purchase_price')) {
                    $table->decimal('purchase_price', 15, 2)->default(0.00)->after('property_code');
                }
                if (!Schema::hasColumn('property_masters', 'purchase_date')) {
                    $table->date('purchase_date')->nullable()->after('purchase_price');
                }
                if (!Schema::hasColumn('property_masters', 'purchase_rate')) {
                    $table->decimal('purchase_rate', 15, 2)->nullable()->after('purchase_date');
                }
                if (!Schema::hasColumn('property_masters', 'total_area')) {
                    $table->decimal('total_area', 15, 2)->nullable()->after('purchase_rate');
                }
                if (!Schema::hasColumn('property_masters', 'area_unit')) {
                    $table->string('area_unit', 50)->default('Sq.Ft')->after('total_area');
                }
                if (!Schema::hasColumn('property_masters', 'seller_name')) {
                    $table->string('seller_name')->nullable()->after('area_unit');
                }
                if (!Schema::hasColumn('property_masters', 'vendor_id')) {
                    $table->unsignedBigInteger('vendor_id')->nullable()->after('seller_name');
                }
                if (!Schema::hasColumn('property_masters', 'payment_mode')) {
                    $table->string('payment_mode')->nullable()->after('vendor_id');
                }
                if (!Schema::hasColumn('property_masters', 'payment_status')) {
                    $table->string('payment_status')->default('paid')->after('payment_mode');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('property_masters')) {
            Schema::table('property_masters', function (Blueprint $table) {
                $columns = ['purchase_price', 'purchase_date', 'purchase_rate', 'total_area', 'area_unit', 'seller_name', 'vendor_id', 'payment_mode', 'payment_status'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('property_masters', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
