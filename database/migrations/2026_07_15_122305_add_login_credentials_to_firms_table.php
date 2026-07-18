<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('firms', function (Blueprint $table) {
            if (!Schema::hasColumn('firms', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            // Change email column to be unique
            $table->string('email')->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('firms', function (Blueprint $table) {
            if (Schema::hasColumn('firms', 'password')) {
                $table->dropColumn('password');
            }
            $table->string('email')->nullable()->change();
        });
    }
};
