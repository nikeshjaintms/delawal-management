<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('broker_id')->nullable();
            $table->date('booking_date')->nullable();
            $table->decimal('booking_amount', 15, 2)->nullable();
            $table->date('agreement_date')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, cancelled
            $table->string('payment_status')->default('unpaid'); // unpaid, partial, paid
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('bookings');
    }
};
