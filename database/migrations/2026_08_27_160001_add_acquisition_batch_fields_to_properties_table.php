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
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'property_master_id')) {
                $table->unsignedBigInteger('property_master_id')->nullable()->after('firm_id');
                $table->foreign('property_master_id')->references('id')->on('property_masters')->onDelete('cascade');
            }
            if (!Schema::hasColumn('properties', 'acquisition_batch_id')) {
                $table->unsignedBigInteger('acquisition_batch_id')->nullable()->after('property_master_id');
                $table->foreign('acquisition_batch_id')->references('id')->on('acquisition_batches')->onDelete('set null');
            }
            if (!Schema::hasColumn('properties', 'purchase_rate')) {
                $table->decimal('purchase_rate', 15, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('properties', 'purchase_date')) {
                $table->date('purchase_date')->nullable()->after('purchase_rate');
            }
        });

        // Backfill existing properties property_master_id from their project
        $projects = DB::table('projects')->whereNotNull('property_id')->get();
        foreach ($projects as $project) {
            DB::table('properties')
                ->where('project_id', $project->id)
                ->whereNull('property_master_id')
                ->update(['property_master_id' => $project->property_id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'acquisition_batch_id')) {
                $table->dropForeign(['acquisition_batch_id']);
                $table->dropColumn('acquisition_batch_id');
            }
            if (Schema::hasColumn('properties', 'property_master_id')) {
                $table->dropForeign(['property_master_id']);
                $table->dropColumn('property_master_id');
            }
            if (Schema::hasColumn('properties', 'purchase_rate')) {
                $table->dropColumn('purchase_rate');
            }
            if (Schema::hasColumn('properties', 'purchase_date')) {
                $table->dropColumn('purchase_date');
            }
        });
    }
};
