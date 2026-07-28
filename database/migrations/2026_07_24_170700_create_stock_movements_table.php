<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->unsignedBigInteger('material_id');
            $table->string('reference_type'); // Material Receive, Material Issue, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('qty_in', 12, 3)->default(0.000);
            $table->decimal('qty_out', 12, 3)->default(0.000);
            $table->decimal('qty_damaged', 12, 3)->default(0.000);
            $table->decimal('balance_stock', 15, 3)->default(0.000);
            $table->decimal('balance_damaged', 15, 3)->default(0.000);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('stock_movements');
    }
};
