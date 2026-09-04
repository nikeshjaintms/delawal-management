<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->after('property_id');
            $table->string('agreement_no')->nullable()->after('tenant_id');
            $table->string('agreement_document')->nullable()->after('remarks');
            $table->integer('lock_in_period')->nullable()->comment('Lock-in period in months')->after('rent_due_date');
            $table->integer('notice_period')->nullable()->comment('Notice period in days')->after('lock_in_period');
            $table->decimal('maintenance_amount', 15, 2)->nullable()->default(0)->after('security_deposit');
            $table->string('meter_reading')->nullable()->after('notice_period');
            $table->decimal('escalation_percent', 5, 2)->nullable()->comment('Annual rent increase %')->after('meter_reading');
            $table->date('handover_date')->nullable()->after('rent_start_date');

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn([
                'tenant_id',
                'agreement_no',
                'agreement_document',
                'lock_in_period',
                'notice_period',
                'maintenance_amount',
                'meter_reading',
                'escalation_percent',
                'handover_date',
            ]);
        });
    }
};
