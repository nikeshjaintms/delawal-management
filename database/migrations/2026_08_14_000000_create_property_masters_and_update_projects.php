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
        // 1. Create property_masters table if it doesn't exist
        if (!Schema::hasTable('property_masters')) {
            Schema::create('property_masters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('firm_id');
                $table->string('property_name');
                $table->string('property_code')->nullable();
                $table->string('location')->nullable();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->string('pincode')->nullable();
                $table->text('description')->nullable();
                $table->string('status')->default('active');
                $table->string('main_image')->nullable();
                $table->string('document_file')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                $table->unique(['firm_id', 'property_code']);
            });
        }

        // 2. Add property_id to projects table
        if (!Schema::hasColumn('projects', 'property_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->unsignedBigInteger('property_id')->nullable()->after('firm_id');
                $table->foreign('property_id')->references('id')->on('property_masters')->onDelete('cascade');
            });
        }

        // 3. Migrate existing Projects to have a parent PropertyMaster
        $projects = DB::table('projects')->whereNull('property_id')->get();
        foreach ($projects as $project) {
            $masterId = DB::table('property_masters')->insertGetId([
                'firm_id'       => $project->firm_id,
                'property_name' => $project->project_name . ' Property',
                'property_code' => 'PROP-' . str_pad($project->id, 4, '0', STR_PAD_LEFT),
                'address'       => $project->address,
                'city'          => $project->city,
                'state'         => $project->state,
                'country'       => $project->country,
                'pincode'       => $project->pincode,
                'description'   => $project->description,
                'status'        => $project->status ?? 'active',
                'main_image'    => $project->project_image ?? null,
                'created_by'    => $project->created_by ?? null,
                'updated_by'    => $project->updated_by ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            DB::table('projects')->where('id', $project->id)->update([
                'property_id' => $masterId,
            ]);
        }

        // 4. Ensure all existing Bulk records in properties table belong to a Project
        $firstProject = DB::table('projects')->first();
        if ($firstProject) {
            DB::table('properties')->whereNull('project_id')->update([
                'project_id' => $firstProject->id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('projects', 'property_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropForeign(['property_id']);
                $table->dropColumn('property_id');
            });
        }

        Schema::dropIfExists('property_masters');
    }
};
