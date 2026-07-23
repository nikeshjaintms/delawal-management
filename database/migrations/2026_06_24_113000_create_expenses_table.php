<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->unsignedBigInteger('property_id')->nullable();
            $table->date('expense_date');
            $table->string('expense_category')->nullable();
            $table->string('expense_title');
            $table->decimal('amount', 15, 2);
            $table->string('payment_mode')->nullable();
            $table->string('paid_to')->nullable();
            $table->string('bill_no')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
