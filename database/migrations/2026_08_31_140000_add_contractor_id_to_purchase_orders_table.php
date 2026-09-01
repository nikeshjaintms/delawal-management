<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'contractor_id')) {
                $table->unsignedBigInteger('contractor_id')->nullable()->after('project_id');
                $table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'contractor_id')) {
                $table->dropForeign(['contractor_id']);
                $table->dropColumn('contractor_id');
            }
        });
    }
};
