<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->unsignedBigInteger('property_type_id')->nullable();
            $table->string('property_code')->nullable();
            $table->string('property_name');
            $table->string('location')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('size')->nullable();
            $table->string('size_unit')->nullable();
            $table->string('unit_no')->nullable();
            $table->string('floor_no')->nullable();
            $table->string('facing')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->string('status')->default('available');
            $table->text('description')->nullable();
            $table->string('main_image')->nullable();
            $table->string('document_file')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('property_type_id')->references('id')->on('property_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
