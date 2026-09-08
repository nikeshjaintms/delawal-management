<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->nullable()->after('booking_date');
            $table->string('discount_type')->default('percentage')->nullable()->after('total_amount'); // percentage, fixed
            $table->decimal('discount_value', 15, 2)->default(0)->nullable()->after('discount_type');
            $table->decimal('discount_amount', 15, 2)->default(0)->nullable()->after('discount_value');
            $table->decimal('final_amount', 15, 2)->nullable()->after('discount_amount');
            $table->decimal('remaining_amount', 15, 2)->nullable()->after('booking_amount');
            $table->unsignedBigInteger('payment_mode_id')->nullable()->after('remaining_amount');
            $table->string('payment_mode')->nullable()->after('payment_mode_id');
            $table->string('transaction_ref')->nullable()->after('payment_mode');

            $table->foreign('payment_mode_id')->references('id')->on('payment_modes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['payment_mode_id']);
            $table->dropColumn([
                'total_amount',
                'discount_type',
                'discount_value',
                'discount_amount',
                'final_amount',
                'remaining_amount',
                'payment_mode_id',
                'payment_mode',
                'transaction_ref',
            ]);
        });
    }
};
