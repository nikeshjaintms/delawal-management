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
        // 1. Make brokers.mobile nullable and relax check constraint to permit null
        if (Schema::hasTable('brokers')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                try {
                    DB::statement("ALTER TABLE brokers DROP CONSTRAINT chk_broker_mobile;");
                } catch (\Throwable $e) {
                    // Ignore if constraint doesn't exist
                }
            }

            Schema::table('brokers', function (Blueprint $table) {
                $table->string('mobile', 255)->nullable()->change();
            });

            if ($driver === 'mysql') {
                try {
                    DB::statement("ALTER TABLE brokers ADD CONSTRAINT chk_broker_mobile CHECK (mobile IS NULL OR mobile REGEXP '^[0-9]{10}$');");
                } catch (\Throwable $e) {
                    // Ignore if check constraint failed
                }
            }
        }

        // 2. Add property_sale_id to broker_commissions table
        if (Schema::hasTable('broker_commissions')) {
            Schema::table('broker_commissions', function (Blueprint $table) {
                if (!Schema::hasColumn('broker_commissions', 'property_sale_id')) {
                    $table->unsignedBigInteger('property_sale_id')->nullable()->after('booking_id');
                    $table->foreign('property_sale_id')->references('id')->on('property_sales')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('broker_commissions')) {
            Schema::table('broker_commissions', function (Blueprint $table) {
                if (Schema::hasColumn('broker_commissions', 'property_sale_id')) {
                    $table->dropForeign(['property_sale_id']);
                    $table->dropColumn('property_sale_id');
                }
            });
        }
    }
};
