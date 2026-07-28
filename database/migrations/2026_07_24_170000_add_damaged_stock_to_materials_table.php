<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('materials', function (Blueprint $table) {
            $table->decimal('damaged_stock', 15, 3)->default(0.000)->after('current_stock');
        });
    }

    public function down(): void {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('damaged_stock');
        });
    }
};
