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
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'payment_mode')) {
                    $table->string('payment_mode', 50)->nullable()->after('payment_status');
                }
                if (!Schema::hasColumn('invoices', 'payment_mode_id')) {
                    $table->unsignedBigInteger('payment_mode_id')->nullable()->after('payment_mode');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (Schema::hasColumn('invoices', 'payment_mode_id')) {
                    $table->dropColumn('payment_mode_id');
                }
                if (Schema::hasColumn('invoices', 'payment_mode')) {
                    $table->dropColumn('payment_mode');
                }
            });
        }
    }
};
