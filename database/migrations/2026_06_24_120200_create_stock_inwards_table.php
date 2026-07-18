<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_inwards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->unsignedBigInteger('material_id');
            $table->unsignedBigInteger('property_id')->nullable();
            $table->date('inward_date');
            $table->decimal('quantity', 15, 3);
            $table->decimal('rate', 15, 2)->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('bill_no')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_inwards');
    }
};
