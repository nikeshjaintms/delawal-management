<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (!Schema::hasColumn('materials', 'unit_price')) {
                $table->decimal('unit_price', 15, 2)->default(0)->nullable()->after('unit');
            }
            if (!Schema::hasColumn('materials', 'total_price')) {
                $table->decimal('total_price', 15, 2)->default(0)->nullable()->after('unit_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (Schema::hasColumn('materials', 'unit_price')) {
                $table->dropColumn('unit_price');
            }
            if (Schema::hasColumn('materials', 'total_price')) {
                $table->dropColumn('total_price');
            }
        });
    }
};
