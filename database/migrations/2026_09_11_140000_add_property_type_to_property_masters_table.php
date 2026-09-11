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
        if (Schema::hasTable('property_masters')) {
            Schema::table('property_masters', function (Blueprint $table) {
                if (!Schema::hasColumn('property_masters', 'property_type')) {
                    $table->string('property_type', 100)->nullable()->after('property_code');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('property_masters')) {
            Schema::table('property_masters', function (Blueprint $table) {
                if (Schema::hasColumn('property_masters', 'property_type')) {
                    $table->dropColumn('property_type');
                }
            });
        }
    }
};
