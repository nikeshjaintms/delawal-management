<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_emi_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('firm_id')->nullable()->after('id');
            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('loan_emi_schedules', function (Blueprint $table) {
            $table->dropForeign(['firm_id']);
            $table->dropColumn('firm_id');
        });
    }
};
