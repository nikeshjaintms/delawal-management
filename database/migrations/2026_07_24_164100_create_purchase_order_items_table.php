<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('material_id')->nullable();
            $table->decimal('qty', 12, 2)->default(1.00);
            $table->decimal('rate', 12, 2)->default(0.00);
            $table->decimal('discount_pct', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('taxable_amount', 12, 2)->default(0.00);
            $table->decimal('gst_pct', 5, 2)->default(0.00);
            $table->decimal('gst_amount', 12, 2)->default(0.00);
            $table->decimal('line_total', 15, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('purchase_order_items');
    }
};
