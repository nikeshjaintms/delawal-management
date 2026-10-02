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
        Schema::table('property_sales', function (Blueprint $table) {
            $table->foreignId('seller_id')->nullable()->after('customer_id')->constrained('sellers')->nullOnDelete();
            $table->string('seller_name')->nullable()->after('seller_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_sales', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropColumn(['seller_id', 'seller_name']);
        });
    }
};
