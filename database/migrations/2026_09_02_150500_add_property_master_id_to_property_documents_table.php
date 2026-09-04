<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_documents', function (Blueprint $table) {
            $table->foreignId('property_master_id')->nullable()->after('firm_id')->constrained('property_masters')->nullOnDelete();
            $table->unsignedBigInteger('property_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('property_documents', function (Blueprint $table) {
            $table->dropForeign(['property_master_id']);
            $table->dropColumn('property_master_id');
        });
    }
};
