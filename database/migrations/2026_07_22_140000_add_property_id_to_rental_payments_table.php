<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rental_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable()->after('rental_id');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
        });

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('
                UPDATE rental_payments 
                SET property_id = (SELECT property_id FROM rentals WHERE rentals.id = rental_payments.rental_id)
                WHERE rental_id IS NOT NULL
            ');
        } else {
            DB::statement('
                UPDATE rental_payments 
                JOIN rentals ON rental_payments.rental_id = rentals.id 
                SET rental_payments.property_id = rentals.property_id
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_payments', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropColumn('property_id');
        });
    }
};
