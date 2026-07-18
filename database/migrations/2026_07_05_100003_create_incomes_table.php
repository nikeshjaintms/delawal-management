<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->date('income_date');
            $table->string('income_type'); // Rent, Sale, Commission, Other
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('payment_mode_id')->nullable();
            $table->string('received_from')->nullable();
            $table->string('reference_no')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('payment_mode_id')->references('id')->on('payment_modes')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('incomes');
    }
};
