<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expense_property')) {
            Schema::create('expense_property', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('expense_id');
                $table->unsignedBigInteger('property_id');
                $table->timestamps();

                $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
                $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
                $table->unique(['expense_id', 'property_id'], 'expense_property_unique');
            });
        }

        // Backfill existing expenses with their property_id
        if (Schema::hasTable('expenses')) {
            $existingExpenses = DB::table('expenses')->whereNotNull('property_id')->get(['id', 'property_id', 'created_at', 'updated_at']);
            foreach ($existingExpenses as $e) {
                DB::table('expense_property')->insertOrIgnore([
                    'expense_id'  => $e->id,
                    'property_id' => $e->property_id,
                    'created_at'  => $e->created_at ?? now(),
                    'updated_at'  => $e->updated_at ?? now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_property');
    }
};
