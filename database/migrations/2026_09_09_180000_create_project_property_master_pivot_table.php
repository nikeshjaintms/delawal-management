<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create project_property_master pivot table
        if (!Schema::hasTable('project_property_master')) {
            Schema::create('project_property_master', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('project_id');
                $table->unsignedBigInteger('property_master_id');
                $table->timestamps();

                $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
                $table->foreign('property_master_id')->references('id')->on('property_masters')->onDelete('cascade');
                $table->unique(['project_id', 'property_master_id'], 'proj_prop_master_unique');
            });
        }

        // 2. Migrate existing project.property_id relationships to project_property_master
        if (Schema::hasColumn('projects', 'property_id')) {
            $existingProjects = DB::table('projects')->whereNotNull('property_id')->get();
            foreach ($existingProjects as $proj) {
                if (DB::table('property_masters')->where('id', $proj->property_id)->exists()) {
                    DB::table('project_property_master')->updateOrInsert(
                        [
                            'project_id'          => $proj->id,
                            'property_master_id' => $proj->property_id,
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }

        // 3. Ensure all properties have property_master_id set (if they only had acquisition_batch_id)
        if (Schema::hasTable('acquisition_batches') && Schema::hasColumn('properties', 'acquisition_batch_id')) {
            DB::statement("
                UPDATE properties p
                INNER JOIN acquisition_batches b ON p.acquisition_batch_id = b.id
                SET p.property_master_id = b.property_master_id
                WHERE p.property_master_id IS NULL AND b.property_master_id IS NOT NULL
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_property_master');
    }
};
