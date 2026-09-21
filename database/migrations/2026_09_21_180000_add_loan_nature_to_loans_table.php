<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'loan_nature')) {
                $table->string('loan_nature', 20)->default('taken')->after('firm_id');
                $table->index('loan_nature');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (Schema::hasColumn('loans', 'loan_nature')) {
                $table->dropIndex(['loan_nature']);
                $table->dropColumn('loan_nature');
            }
        });
    }
};
