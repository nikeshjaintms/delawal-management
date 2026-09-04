<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('alternate_mobile')->nullable()->after('mobile');
            $table->string('occupation')->nullable()->after('city');
            $table->text('permanent_address')->nullable()->after('address');
            $table->string('emergency_contact_name')->nullable()->after('occupation');
            $table->string('emergency_contact_mobile')->nullable()->after('emergency_contact_name');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'alternate_mobile',
                'occupation',
                'permanent_address',
                'emergency_contact_name',
                'emergency_contact_mobile',
            ]);
        });
    }
};
