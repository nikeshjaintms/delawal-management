<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_inwards') && !Schema::hasColumn('stock_inwards', 'contractor_id')) {
            Schema::table('stock_inwards', function (Blueprint $table) {
                $table->foreignId('contractor_id')->nullable()->after('project_id')->constrained('contractors')->nullOnDelete();
            });
        }

        if (Schema::hasTable('stock_outwards') && !Schema::hasColumn('stock_outwards', 'contractor_id')) {
            Schema::table('stock_outwards', function (Blueprint $table) {
                $table->foreignId('contractor_id')->nullable()->after('project_id')->constrained('contractors')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stock_inwards') && Schema::hasColumn('stock_inwards', 'contractor_id')) {
            Schema::table('stock_inwards', function (Blueprint $table) {
                $table->dropForeign(['contractor_id']);
                $table->dropColumn('contractor_id');
            });
        }

        if (Schema::hasTable('stock_outwards') && Schema::hasColumn('stock_outwards', 'contractor_id')) {
            Schema::table('stock_outwards', function (Blueprint $table) {
                $table->dropForeign(['contractor_id']);
                $table->dropColumn('contractor_id');
            });
        }
    }
};
