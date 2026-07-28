<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('material_receives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->string('mr_number')->unique();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('vendor_id');
            $table->date('receive_date');
            $table->string('invoice_no')->nullable();
            $table->string('challan_no')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('warehouse')->nullable();
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
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('material_receives');
    }
};
