<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('firm_id')->constrained('roles')->nullOnDelete();
            $table->string('mobile_number')->nullable()->after('email');
            $table->string('status')->default('active')->after('role');
        });

        DB::table('users')->where('role', 'admin')->update(['role_id' => 1]);
        DB::table('users')->where('role', '!=', 'admin')->update(['role_id' => 2]);
        DB::table('users')->whereNull('role_id')->update(['role_id' => 2]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn(['mobile_number', 'status']);
        });
    }
};
