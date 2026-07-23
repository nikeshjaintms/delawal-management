<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('forms') && !Schema::hasColumn('forms', 'firm_id')) {
            Schema::table('forms', function (Blueprint $table) {
                $table->foreignId('firm_id')->nullable()->after('id')->constrained('firms')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('form_submissions') && !Schema::hasColumn('form_submissions', 'firm_id')) {
            Schema::table('form_submissions', function (Blueprint $table) {
                $table->foreignId('firm_id')->nullable()->after('id')->constrained('firms')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('forms') && Schema::hasColumn('forms', 'firm_id')) {
            Schema::table('forms', function (Blueprint $table) {
                $table->dropForeign(['firm_id']);
                $table->dropColumn('firm_id');
            });
        }

        if (Schema::hasTable('form_submissions') && Schema::hasColumn('form_submissions', 'firm_id')) {
            Schema::table('form_submissions', function (Blueprint $table) {
                $table->dropForeign(['firm_id']);
                $table->dropColumn('firm_id');
            });
        }
    }
};
