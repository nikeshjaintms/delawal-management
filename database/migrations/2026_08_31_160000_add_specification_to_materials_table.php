<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (!Schema::hasColumn('materials', 'specification')) {
                $table->string('specification')->nullable()->after('material_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (Schema::hasColumn('materials', 'specification')) {
                $table->dropColumn('specification');
            }
        });
    }
};
