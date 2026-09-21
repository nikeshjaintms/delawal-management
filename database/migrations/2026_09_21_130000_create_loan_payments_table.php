<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('loan_payments')) {
            Schema::create('loan_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('loan_id');
                $table->unsignedBigInteger('firm_id');
                $table->unsignedBigInteger('payment_mode_id')->nullable();
                $table->decimal('amount', 15, 2);
                $table->date('payment_date');
                $table->string('payment_mode', 100)->nullable();
                $table->string('reference_no', 150)->nullable();
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
                $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
                $table->foreign('payment_mode_id')->references('id')->on('payment_modes')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
    }
};
