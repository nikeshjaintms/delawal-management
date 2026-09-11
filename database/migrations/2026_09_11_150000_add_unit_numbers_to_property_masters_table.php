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
        Schema::table('property_masters', function (Blueprint $table) {
            $table->integer('total_units_count')->nullable()->after('area_unit');
            $table->text('unit_numbers_list')->nullable()->after('total_units_count');
            $table->string('unit_prefix')->nullable()->default('Plot ')->after('unit_numbers_list');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_masters', function (Blueprint $table) {
            $table->dropColumn(['total_units_count', 'unit_numbers_list', 'unit_prefix']);
        });
    }
};
