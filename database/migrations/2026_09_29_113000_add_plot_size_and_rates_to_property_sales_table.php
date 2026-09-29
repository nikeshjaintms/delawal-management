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
        Schema::table('property_sales', function (Blueprint $table) {
            $table->decimal('total_area', 15, 2)->nullable()->after('property_expenses');
            $table->string('area_unit', 50)->nullable()->default('Sq.Ft')->after('total_area');
            $table->decimal('purchase_rate', 15, 2)->nullable()->after('area_unit');
            $table->decimal('sell_rate', 15, 2)->nullable()->after('purchase_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_sales', function (Blueprint $table) {
            $table->dropColumn(['total_area', 'area_unit', 'purchase_rate', 'sell_rate']);
        });
    }
};
