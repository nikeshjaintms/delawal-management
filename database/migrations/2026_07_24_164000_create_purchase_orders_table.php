<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->string('po_number')->unique();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->date('po_date');
            $table->date('delivery_date')->nullable();
            $table->string('status')->default('Draft'); // Draft, Pending, Approved, Ordered, Received, Cancelled
            $table->decimal('sub_total', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('taxable_amount', 15, 2)->default(0.00);
            $table->decimal('cgst_amount', 15, 2)->default(0.00);
            $table->decimal('sgst_amount', 15, 2)->default(0.00);
            $table->decimal('igst_amount', 15, 2)->default(0.00);
            $table->decimal('grand_total', 15, 2)->default(0.00);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('purchase_orders');
    }
};
