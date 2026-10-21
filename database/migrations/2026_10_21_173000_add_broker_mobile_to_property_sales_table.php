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
        if (Schema::hasTable('property_sales')) {
            Schema::table('property_sales', function (Blueprint $table) {
                if (!Schema::hasColumn('property_sales', 'broker_mobile')) {
                    $table->string('broker_mobile', 25)->nullable()->after('broker_name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('property_sales')) {
            Schema::table('property_sales', function (Blueprint $table) {
                if (Schema::hasColumn('property_sales', 'broker_mobile')) {
                    $table->dropColumn('broker_mobile');
                }
            });
        }
    }
};
