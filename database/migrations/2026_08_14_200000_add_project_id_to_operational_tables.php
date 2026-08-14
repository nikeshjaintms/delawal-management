<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. stock_inwards
        if (Schema::hasTable('stock_inwards') && !Schema::hasColumn('stock_inwards', 'project_id')) {
            Schema::table('stock_inwards', function (Blueprint $table) {
                $table->unsignedBigInteger('project_id')->nullable()->after('firm_id');
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            });
        }

        // 2. stock_outwards
        if (Schema::hasTable('stock_outwards') && !Schema::hasColumn('stock_outwards', 'project_id')) {
            Schema::table('stock_outwards', function (Blueprint $table) {
                $table->unsignedBigInteger('project_id')->nullable()->after('firm_id');
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            });
        }

        // 3. purchase_orders
        if (Schema::hasTable('purchase_orders') && !Schema::hasColumn('purchase_orders', 'project_id')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->unsignedBigInteger('project_id')->nullable()->after('firm_id');
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            });
        }

        // 4. materials
        if (Schema::hasTable('materials') && !Schema::hasColumn('materials', 'project_id')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->unsignedBigInteger('project_id')->nullable()->after('firm_id');
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            });
        }

        // 5. material_categories
        if (Schema::hasTable('material_categories') && !Schema::hasColumn('material_categories', 'project_id')) {
            Schema::table('material_categories', function (Blueprint $table) {
                $table->unsignedBigInteger('project_id')->nullable()->after('firm_id');
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            });
        }

        // Migrate existing data in stock_inwards
        $inwards = DB::table('stock_inwards')->get();
        foreach ($inwards as $in) {
            $projId = null;
            if ($in->property_id) {
                $p = DB::table('properties')->find($in->property_id);
                if ($p && $p->project_id) {
                    $projId = $p->project_id;
                } else {
                    $proj = DB::table('projects')->find($in->property_id);
                    if ($proj) {
                        $projId = $proj->id;
                    }
                }
            }
            if (!$projId) {
                $defaultProj = DB::table('projects')->where('firm_id', $in->firm_id)->first() ?? DB::table('projects')->first();
                if ($defaultProj) {
                    $projId = $defaultProj->id;
                }
            }
            if ($projId) {
                DB::table('stock_inwards')->where('id', $in->id)->update(['project_id' => $projId]);
            }
        }

        // Migrate existing data in stock_outwards
        $outwards = DB::table('stock_outwards')->get();
        foreach ($outwards as $out) {
            $projId = null;
            if ($out->property_id) {
                $p = DB::table('properties')->find($out->property_id);
                if ($p && $p->project_id) {
                    $projId = $p->project_id;
                } else {
                    $proj = DB::table('projects')->find($out->property_id);
                    if ($proj) {
                        $projId = $proj->id;
                    }
                }
            }
            if (!$projId) {
                $defaultProj = DB::table('projects')->where('firm_id', $out->firm_id)->first() ?? DB::table('projects')->first();
                if ($defaultProj) {
                    $projId = $defaultProj->id;
                }
            }
            if ($projId) {
                DB::table('stock_outwards')->where('id', $out->id)->update(['project_id' => $projId]);
            }
        }

        // Populate default project_id for purchase_orders, materials, material_categories if empty
        $defaultProj = DB::table('projects')->first();
        if ($defaultProj) {
            DB::table('purchase_orders')->whereNull('project_id')->update(['project_id' => $defaultProj->id]);
            DB::table('materials')->whereNull('project_id')->update(['project_id' => $defaultProj->id]);
            DB::table('material_categories')->whereNull('project_id')->update(['project_id' => $defaultProj->id]);
        }
    }

    public function down(): void
    {
        Schema::table('stock_inwards', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
        Schema::table('stock_outwards', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
        Schema::table('material_categories', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};
