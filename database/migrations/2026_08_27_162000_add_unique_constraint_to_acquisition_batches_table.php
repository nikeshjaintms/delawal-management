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
        Schema::table('acquisition_batches', function (Blueprint $table) {
            // Ensure unique batch_name per property_master_id
            $table->unique(['property_master_id', 'batch_name'], 'acq_batches_prop_batch_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acquisition_batches', function (Blueprint $table) {
            $table->dropUnique('acq_batches_prop_batch_name_unique');
        });
    }
};
