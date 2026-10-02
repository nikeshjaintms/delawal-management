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
        if (Schema::hasTable('property_sales')) {
            Schema::table('property_sales', function (Blueprint $table) {
                if (!Schema::hasColumn('property_sales', 'broker_commission_payment_date')) {
                    $table->date('broker_commission_payment_date')->nullable()->after('broker_commission_payment_mode');
                }
            });
        }

        if (Schema::hasTable('property_masters')) {
            Schema::table('property_masters', function (Blueprint $table) {
                if (!Schema::hasColumn('property_masters', 'broker_commission_payment_date')) {
                    $table->date('broker_commission_payment_date')->nullable()->after('broker_commission_payment_mode');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('property_sales')) {
            Schema::table('property_sales', function (Blueprint $table) {
                if (Schema::hasColumn('property_sales', 'broker_commission_payment_date')) {
                    $table->dropColumn('broker_commission_payment_date');
                }
            });
        }

        if (Schema::hasTable('property_masters')) {
            Schema::table('property_masters', function (Blueprint $table) {
                if (Schema::hasColumn('property_masters', 'broker_commission_payment_date')) {
                    $table->dropColumn('broker_commission_payment_date');
                }
            });
        }
    }
};
