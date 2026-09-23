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
        if (!Schema::hasTable('rental_property')) {
            Schema::create('rental_property', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('rental_id');
                $table->unsignedBigInteger('property_id');
                $table->timestamps();

                $table->foreign('rental_id')->references('id')->on('rentals')->onDelete('cascade');
                $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
                $table->unique(['rental_id', 'property_id']);
            });
        }

        // Backfill existing rentals with their property_id
        if (Schema::hasTable('rentals')) {
            $existingRentals = \Illuminate\Support\Facades\DB::table('rentals')
                ->whereNotNull('property_id')
                ->get(['id', 'property_id', 'created_at', 'updated_at']);

            foreach ($existingRentals as $r) {
                \Illuminate\Support\Facades\DB::table('rental_property')->insertOrIgnore([
                    'rental_id'   => $r->id,
                    'property_id' => $r->property_id,
                    'created_at'  => $r->created_at ?? now(),
                    'updated_at'  => $r->updated_at ?? now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_property');
    }
};
