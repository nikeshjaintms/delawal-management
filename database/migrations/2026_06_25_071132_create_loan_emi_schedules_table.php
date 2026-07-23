<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_emi_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->unsignedInteger('emi_month');  // 1-12
            $table->unsignedInteger('emi_year');
            $table->date('emi_date');
            $table->decimal('emi_amount',     15, 2);
            $table->decimal('paid_amount',    15, 2)->default(0);
            $table->decimal('pending_amount', 15, 2);
            $table->date('payment_date')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('emi_status')->default('Pending'); // Pending|Paid|Partial|Overdue
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_emi_schedules');
    }
};
