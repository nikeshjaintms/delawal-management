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
        if (Schema::hasTable('rental_payments')) {
            Schema::table('rental_payments', function (Blueprint $table) {
                if (!Schema::hasColumn('rental_payments', 'maintenance_amount')) {
                    $table->decimal('maintenance_amount', 15, 2)->default(0)->after('rent_amount');
                }
                if (!Schema::hasColumn('rental_payments', 'total_amount')) {
                    $table->decimal('total_amount', 15, 2)->default(0)->after('maintenance_amount');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('rental_payments')) {
            Schema::table('rental_payments', function (Blueprint $table) {
                if (Schema::hasColumn('rental_payments', 'total_amount')) {
                    $table->dropColumn('total_amount');
                }
                if (Schema::hasColumn('rental_payments', 'maintenance_amount')) {
                    $table->dropColumn('maintenance_amount');
                }
            });
        }
    }
};
