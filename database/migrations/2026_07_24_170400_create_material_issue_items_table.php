<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('material_issue_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_issue_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('qty_issued', 12, 3)->default(0.000);
            $table->decimal('qty_outwarded', 12, 3)->default(0.000);
            $table->timestamps();

            $table->foreign('material_issue_id')->references('id')->on('material_issues')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('material_issue_items');
    }
};
