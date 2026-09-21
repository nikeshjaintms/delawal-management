<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Any plot that was created under a Property Master must stay strictly within Property Master
        // and have project_id set to null so it does not bleed into Project inventory.
        DB::table('properties')
            ->whereNotNull('property_master_id')
            ->update(['project_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse action needed
    }
};
