<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->string('person_name')->nullable()->after('customer_id');
            $table->string('mobile_number')->nullable()->after('person_name');
            $table->string('relationship')->nullable()->after('mobile_number');
            $table->unsignedBigInteger('payment_mode_id')->nullable()->after('relationship');
            
            $table->string('bank_name')->nullable()->change();
            $table->decimal('interest_rate', 8, 4)->nullable()->change();
            $table->decimal('emi_amount', 15, 2)->nullable()->change();
            $table->date('loan_end_date')->nullable()->change();
            $table->unsignedInteger('total_emi_months')->nullable()->change();
            
            $table->foreign('payment_mode_id')->references('id')->on('payment_modes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['payment_mode_id']);
            $table->dropColumn(['person_name', 'mobile_number', 'relationship', 'payment_mode_id']);
            
            $table->string('bank_name')->nullable(false)->change();
            $table->decimal('interest_rate', 8, 4)->nullable(false)->change();
            $table->decimal('emi_amount', 15, 2)->nullable(false)->change();
            $table->date('loan_end_date')->nullable(false)->change();
            $table->unsignedInteger('total_emi_months')->nullable(false)->change();
        });
    }
};
