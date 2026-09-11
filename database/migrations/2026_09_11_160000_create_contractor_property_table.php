<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contractor_property')) {
            Schema::create('contractor_property', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contractor_id')->constrained('contractors')->cascadeOnDelete();
                $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['contractor_id', 'property_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_property');
    }
};
