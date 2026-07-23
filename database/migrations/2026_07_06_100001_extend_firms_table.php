<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('firms', function (Blueprint $table) {
            // Only add columns that don't already exist
            if (!Schema::hasColumn('firms', 'owner_name')) {
                $table->string('owner_name')->nullable()->after('firm_name');
            }
            if (!Schema::hasColumn('firms', 'alternate_mobile')) {
                $table->string('alternate_mobile')->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('firms', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('firms', 'pincode')) {
                $table->string('pincode', 10)->nullable()->after('state');
            }
            if (!Schema::hasColumn('firms', 'pan_number')) {
                $table->string('pan_number')->nullable()->after('gst_no');
            }
            if (!Schema::hasColumn('firms', 'firm_logo')) {
                $table->string('firm_logo')->nullable()->after('pan_number');
            }
            if (!Schema::hasColumn('firms', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('firm_logo');
            }
            if (!Schema::hasColumn('firms', 'account_number')) {
                $table->string('account_number')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('firms', 'ifsc_code')) {
                $table->string('ifsc_code')->nullable()->after('account_number');
            }
            if (!Schema::hasColumn('firms', 'branch_name')) {
                $table->string('branch_name')->nullable()->after('ifsc_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('firms', function (Blueprint $table) {
            $table->dropColumn([
                'owner_name', 'alternate_mobile', 'state', 'pincode',
                'pan_number', 'firm_logo', 'bank_name', 'account_number',
                'ifsc_code', 'branch_name',
            ]);
        });
    }
};
