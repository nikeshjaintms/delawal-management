<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('material_receive_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_receive_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('qty_ordered', 12, 3)->default(0.000);
            $table->decimal('qty_received', 12, 3)->default(0.000);
            $table->decimal('qty_damaged', 12, 3)->default(0.000);
            $table->decimal('rate', 12, 2)->default(0.00);
            $table->decimal('discount_pct', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('taxable_amount', 12, 2)->default(0.00);
            $table->decimal('gst_pct', 5, 2)->default(0.00);
            $table->decimal('gst_amount', 12, 2)->default(0.00);
            $table->decimal('line_total', 15, 2)->default(0.00);
            $table->string('warehouse')->nullable();
            $table->timestamps();

            $table->foreign('material_receive_id')->references('id')->on('material_receives')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('material_receive_items');
    }
};
