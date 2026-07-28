<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('material_issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->string('mi_number')->unique();
            $table->date('issue_date');
            $table->unsignedBigInteger('property_id')->nullable(); // represents Site
            $table->string('employee_name');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('material_issues');
    }
};
