<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->string('bank_name');
            $table->string('loan_type');
            $table->unsignedBigInteger('property_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->decimal('loan_amount',    15, 2);
            $table->decimal('interest_rate',  8,  4);
            $table->decimal('emi_amount',     15, 2);
            $table->date('loan_start_date');
            $table->date('loan_end_date');
            $table->unsignedInteger('total_emi_months');
            $table->decimal('paid_amount',    15, 2)->default(0);
            $table->decimal('pending_amount', 15, 2)->default(0);
            $table->string('loan_status')->default('Active');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
