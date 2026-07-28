<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('outward_items');
        Schema::dropIfExists('outwards');
        Schema::dropIfExists('material_issue_items');
        Schema::dropIfExists('material_issues');

        Schema::create('outwards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->string('outward_number')->unique();
            $table->unsignedBigInteger('material_receive_id');
            $table->unsignedBigInteger('property_id')->nullable(); // represents Site
            $table->date('dispatch_date');
            $table->string('vehicle_no')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('lr_no')->nullable();
            $table->string('transport_name')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('material_receive_id')->references('id')->on('material_receives')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

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
        Schema::dropIfExists('outwards');
    }
};
