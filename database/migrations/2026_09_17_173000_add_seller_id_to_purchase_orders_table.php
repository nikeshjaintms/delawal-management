<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'seller_id')) {
                $table->unsignedBigInteger('seller_id')->nullable()->after('vendor_id');
                if (DB::getDriverName() !== 'sqlite') {
                    $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('set null');
                    $table->index('seller_id');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'seller_id')) {
                if (DB::getDriverName() !== 'sqlite') {
                    $table->dropForeign(['seller_id']);
                }
                $table->dropColumn('seller_id');
            }
        });
        Schema::enableForeignKeyConstraints();
    }
};
