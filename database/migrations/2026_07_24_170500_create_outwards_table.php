<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('outwards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->string('outward_number')->unique();
            $table->unsignedBigInteger('material_issue_id');
            $table->date('dispatch_date');
            $table->string('vehicle_no')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('lr_no')->nullable();
            $table->string('transport_name')->nullable();
            $table->string('status')->default('Pending'); // Pending, Dispatched, Delivered
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('material_issue_id')->references('id')->on('material_issues')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('outwards');
    }
};
