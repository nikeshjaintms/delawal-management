<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create pivot table for multiple projects
        if (!Schema::hasTable('contractor_project')) {
            Schema::create('contractor_project', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contractor_id');
                $table->unsignedBigInteger('project_id');
                $table->timestamps();

                $table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('cascade');
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
                $table->unique(['contractor_id', 'project_id']);
            });
        }

        // 2. Make project_id on contractors nullable if needed
        if (Schema::hasTable('contractors') && Schema::hasColumn('contractors', 'project_id')) {
            Schema::table('contractors', function (Blueprint $table) {
                $table->unsignedBigInteger('project_id')->nullable()->change();
            });
        }

        // 3. Backfill existing contractors.project_id into contractor_project pivot table
        $existing = DB::table('contractors')->whereNotNull('project_id')->get();
        foreach ($existing as $c) {
            DB::table('contractor_project')->insertOrIgnore([
                'contractor_id' => $c->id,
                'project_id'    => $c->project_id,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_project');
    }
};
