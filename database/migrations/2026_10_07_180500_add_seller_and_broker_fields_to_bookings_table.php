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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'seller_id')) {
                $table->foreignId('seller_id')->nullable()->after('customer_id')->constrained('sellers')->nullOnDelete();
            }
            if (!Schema::hasColumn('bookings', 'seller_name')) {
                $table->string('seller_name')->nullable()->after('seller_id');
            }
            if (!Schema::hasColumn('bookings', 'broker_name')) {
                $table->string('broker_name')->nullable()->after('broker_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'seller_id')) {
                $table->dropForeign(['seller_id']);
                $table->dropColumn('seller_id');
            }
            if (Schema::hasColumn('bookings', 'seller_name')) {
                $table->dropColumn('seller_name');
            }
            if (Schema::hasColumn('bookings', 'broker_name')) {
                $table->dropColumn('broker_name');
            }
        });
    }
};
