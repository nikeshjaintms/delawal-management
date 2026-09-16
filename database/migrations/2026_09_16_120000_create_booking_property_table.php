<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('booking_property')) {
            Schema::create('booking_property', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('booking_id');
                $table->unsignedBigInteger('property_id');
                $table->timestamps();

                $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
                $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
                $table->unique(['booking_id', 'property_id']);
            });
        }

        // Backfill existing bookings with their property_id
        if (Schema::hasTable('bookings')) {
            $existingBookings = DB::table('bookings')->whereNotNull('property_id')->get(['id', 'property_id', 'created_at', 'updated_at']);
            foreach ($existingBookings as $b) {
                DB::table('booking_property')->insertOrIgnore([
                    'booking_id'  => $b->id,
                    'property_id' => $b->property_id,
                    'created_at'  => $b->created_at ?? now(),
                    'updated_at'  => $b->updated_at ?? now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_property');
    }
};
