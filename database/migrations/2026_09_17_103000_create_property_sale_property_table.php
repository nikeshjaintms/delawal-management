<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('property_sale_property')) {
            Schema::create('property_sale_property', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('property_sale_id');
                $table->unsignedBigInteger('property_id');
                $table->timestamps();

                $table->foreign('property_sale_id')->references('id')->on('property_sales')->onDelete('cascade');
                $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
                $table->unique(['property_sale_id', 'property_id'], 'prop_sale_prop_unique');
            });
        }

        // Backfill existing property_sales with their property_id
        if (Schema::hasTable('property_sales')) {
            $existingSales = DB::table('property_sales')->whereNotNull('property_id')->get(['id', 'property_id', 'created_at', 'updated_at']);
            foreach ($existingSales as $s) {
                DB::table('property_sale_property')->insertOrIgnore([
                    'property_sale_id' => $s->id,
                    'property_id'      => $s->property_id,
                    'created_at'       => $s->created_at ?? now(),
                    'updated_at'       => $s->updated_at ?? now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('property_sale_property');
    }
};
