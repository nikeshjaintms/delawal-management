<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->date('ledger_date');
            $table->unsignedBigInteger('property_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('broker_id')->nullable();
            $table->string('transaction_type');
            $table->string('transaction_title');
            $table->decimal('debit_amount',  15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->string('payment_mode')->nullable();
            $table->string('reference_no')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
