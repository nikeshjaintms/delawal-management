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
            $table->string('broker_commission_type')->default('percentage')->nullable()->after('broker_id'); // 'percentage', 'fixed'
            $table->decimal('broker_commission_rate', 10, 2)->nullable()->after('broker_commission_type');
            $table->decimal('broker_commission_amount', 15, 2)->default(0.00)->nullable()->after('broker_commission_rate');
            $table->decimal('broker_commission_paid', 15, 2)->default(0.00)->nullable()->after('broker_commission_amount');
            $table->decimal('broker_commission_due', 15, 2)->default(0.00)->nullable()->after('broker_commission_paid');
            $table->string('broker_commission_payment_mode')->nullable()->after('broker_commission_due');
            $table->string('broker_commission_status')->default('unpaid')->nullable()->after('broker_commission_payment_mode'); // unpaid, partial, paid
            $table->text('broker_notes')->nullable()->after('broker_commission_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_sales', function (Blueprint $table) {
            $table->dropColumn([
                'broker_commission_type',
                'broker_commission_rate',
                'broker_commission_amount',
                'broker_commission_paid',
                'broker_commission_due',
                'broker_commission_payment_mode',
                'broker_commission_status',
                'broker_notes',
            ]);
        });
    }
};
