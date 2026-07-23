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
        // 1. Add column as nullable first
        Schema::table('invoice_settings', function (Blueprint $table) {
            $table->foreignId('firm_id')->nullable()->after('id')->constrained('firms')->cascadeOnDelete();
        });

        // 2. Backfill with the first firm ID (or fallback to 1)
        $firstFirm = DB::table('firms')->first();
        $firmId = $firstFirm ? $firstFirm->id : 1;
        DB::table('invoice_settings')->whereNull('firm_id')->update(['firm_id' => $firmId]);

        // 3. Make column non-nullable
        Schema::table('invoice_settings', function (Blueprint $table) {
            $table->foreignId('firm_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_settings', function (Blueprint $table) {
            $table->dropForeign(['firm_id']);
            $table->dropColumn('firm_id');
        });
    }
};
