<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('outward_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outward_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('qty_dispatch', 12, 3)->default(0.000);
            $table->timestamps();

            $table->foreign('outward_id')->references('id')->on('outwards')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('outward_items');
    }
};
