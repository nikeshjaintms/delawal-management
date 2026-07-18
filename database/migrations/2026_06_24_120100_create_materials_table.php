<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->unsignedBigInteger('material_category_id')->nullable();
            $table->string('material_name');
            $table->string('unit')->nullable();
            $table->decimal('opening_stock', 15, 3)->default(0);
            $table->decimal('current_stock', 15, 3)->default(0);
            $table->decimal('minimum_stock', 15, 3)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('material_category_id')->references('id')->on('material_categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
