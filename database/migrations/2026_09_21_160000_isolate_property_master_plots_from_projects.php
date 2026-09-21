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
        // 1. For plots associated with a Project, ensure property_master_id is null
        // so they belong strictly to Project inventory and show in Project View.
        DB::table('properties')
            ->whereNotNull('project_id')
            ->update(['property_master_id' => null]);

        // 2. For plots created under Property Masters, ensure project_id is null
        // so they belong strictly to Property Master inventory.
        DB::table('properties')
            ->whereNotNull('property_master_id')
            ->whereNull('project_id')
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
