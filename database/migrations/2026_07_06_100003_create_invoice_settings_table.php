<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_year_id')->nullable()->constrained('financial_years')->nullOnDelete();
            $table->string('sales_prefix')->default('SAL');
            $table->string('purchase_prefix')->default('PUR');
            $table->string('booking_prefix')->default('BKG');
            $table->string('rental_prefix')->default('RNT');
            $table->string('payment_prefix')->default('PAY');
            $table->string('receipt_prefix')->default('REC');
            $table->string('expense_prefix')->default('EXP');
            $table->string('income_prefix')->default('INC');
            $table->string('loan_prefix')->default('LAN');
            $table->unsignedInteger('starting_number')->default(1);
            $table->unsignedInteger('current_number')->default(1);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_settings');
    }
};
