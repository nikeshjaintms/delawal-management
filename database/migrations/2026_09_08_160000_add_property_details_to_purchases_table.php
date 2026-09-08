<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('property_name')->nullable()->after('vendor_id');
            $table->string('property_type')->nullable()->after('property_name');
            $table->string('property_code')->nullable()->after('property_type');
            $table->string('location')->nullable()->after('property_code');
            $table->text('address')->nullable()->after('location');
            $table->string('survey_no')->nullable()->after('address');
            $table->string('tp_no')->nullable()->after('survey_no');
            $table->string('fp_no')->nullable()->after('tp_no');
            $table->decimal('area', 15, 2)->nullable()->after('fp_no');
            $table->string('area_unit')->nullable()->after('area');
            $table->string('item_name')->nullable()->change();
        });
    }

    public function down(): void {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn([
                'property_name',
                'property_type',
                'property_code',
                'location',
                'address',
                'survey_no',
                'tp_no',
                'fp_no',
                'area',
                'area_unit',
            ]);
        });
    }
};
