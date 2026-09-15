<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Property;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Sync all properties that have active bookings (status != 'cancelled')
        DB::table('properties')
            ->whereIn('id', function ($query) {
                $query->select('property_id')
                    ->from('bookings')
                    ->where('status', '!=', 'cancelled')
                    ->whereNotNull('property_id');
            })
            ->update(['status' => 'booked']);

        // 2. Sync all properties that have active sales (sale_status != 'cancelled')
        DB::table('properties')
            ->whereIn('id', function ($query) {
                $query->select('property_id')
                    ->from('property_sales')
                    ->where('sale_status', '!=', 'cancelled')
                    ->whereNotNull('property_id');
            })
            ->update(['status' => 'sold']);

        // 3. Sync all properties that have active rentals (status = 'active')
        DB::table('properties')
            ->whereIn('id', function ($query) {
                $query->select('property_id')
                    ->from('rentals')
                    ->where('status', 'active')
                    ->whereNotNull('property_id');
            })
            ->where('status', '!=', 'sold')
            ->update(['status' => 'rented']);

        // 4. Revert any properties marked 'booked' that have NO active bookings, sales, or rentals
        DB::table('properties')
            ->where('status', 'booked')
            ->whereNotIn('id', function ($query) {
                $query->select('property_id')
                    ->from('bookings')
                    ->where('status', '!=', 'cancelled')
                    ->whereNotNull('property_id');
            })
            ->whereNotIn('id', function ($query) {
                $query->select('property_id')
                    ->from('property_sales')
                    ->where('sale_status', '!=', 'cancelled')
                    ->whereNotNull('property_id');
            })
            ->whereNotIn('id', function ($query) {
                $query->select('property_id')
                    ->from('rentals')
                    ->where('status', 'active')
                    ->whereNotNull('property_id');
            })
            ->update(['status' => 'available']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to revert
    }
};
