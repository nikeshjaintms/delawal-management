<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stock_outward_property')) {
            Schema::create('stock_outward_property', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('stock_outward_id');
                $table->unsignedBigInteger('property_id');
                $table->timestamps();

                $table->foreign('stock_outward_id')->references('id')->on('stock_outwards')->onDelete('cascade');
                $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
                $table->unique(['stock_outward_id', 'property_id'], 'stock_outward_prop_unique');
            });
        }

        // Backfill existing stock outwards with their property_id
        if (Schema::hasTable('stock_outwards')) {
            $existingOutwards = DB::table('stock_outwards')->whereNotNull('property_id')->get(['id', 'property_id', 'created_at', 'updated_at']);
            foreach ($existingOutwards as $so) {
                DB::table('stock_outward_property')->insertOrIgnore([
                    'stock_outward_id' => $so->id,
                    'property_id'      => $so->property_id,
                    'created_at'       => $so->created_at ?? now(),
                    'updated_at'       => $so->updated_at ?? now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_outward_property');
    }
};
