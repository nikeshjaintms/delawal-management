<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('item_name');
            $table->date('purchase_date');
            $table->decimal('purchase_amount', 15, 2);
            $table->integer('quantity')->default(1);
            $table->string('payment_mode')->nullable(); // Cash, Bank, UPI, Cheque
            $table->string('payment_status')->default('unpaid'); // unpaid, partial, paid
            $table->string('reference_no')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('purchases');
    }
};
