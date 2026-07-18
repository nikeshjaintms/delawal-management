<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->string('receipt_no')->nullable();
            $table->date('receipt_date');
            $table->string('received_from');
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('payment_mode_id')->nullable();
            $table->string('reference_no')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('payment_mode_id')->references('id')->on('payment_modes')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('receipts');
    }
};
