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
        if (Schema::hasTable('property_masters')) {
            DB::statement("ALTER TABLE property_masters 
                MODIFY purchase_price DECIMAL(20, 2) DEFAULT 0.00,
                MODIFY paid_amount DECIMAL(20, 2) DEFAULT 0.00,
                MODIFY due_amount DECIMAL(20, 2) DEFAULT 0.00,
                MODIFY purchase_rate DECIMAL(20, 2) NULL,
                MODIFY total_area DECIMAL(20, 2) NULL,
                MODIFY broker_commission_rate DECIMAL(8, 2) NULL,
                MODIFY broker_commission_amount DECIMAL(20, 2) DEFAULT 0.00,
                MODIFY broker_commission_paid DECIMAL(20, 2) DEFAULT 0.00,
                MODIFY broker_commission_due DECIMAL(20, 2) DEFAULT 0.00");
        }

        if (Schema::hasTable('property_master_payments')) {
            DB::statement("ALTER TABLE property_master_payments 
                MODIFY amount DECIMAL(20, 2) NOT NULL DEFAULT 0.00");
        }

        if (Schema::hasTable('purchases')) {
            DB::statement("ALTER TABLE purchases 
                MODIFY purchase_amount DECIMAL(20, 2) DEFAULT 0.00,
                MODIFY paid_amount DECIMAL(20, 2) DEFAULT 0.00,
                MODIFY due_amount DECIMAL(20, 2) DEFAULT 0.00");
        }

        if (Schema::hasTable('purchase_payments')) {
            DB::statement("ALTER TABLE purchase_payments 
                MODIFY amount DECIMAL(20, 2) NOT NULL DEFAULT 0.00");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('property_masters')) {
            DB::statement("ALTER TABLE property_masters 
                MODIFY purchase_price DECIMAL(15, 2) DEFAULT 0.00,
                MODIFY paid_amount DECIMAL(15, 2) DEFAULT 0.00,
                MODIFY due_amount DECIMAL(15, 2) DEFAULT 0.00,
                MODIFY purchase_rate DECIMAL(15, 2) NULL,
                MODIFY total_area DECIMAL(15, 2) NULL,
                MODIFY broker_commission_rate DECIMAL(15, 2) NULL,
                MODIFY broker_commission_amount DECIMAL(15, 2) DEFAULT 0.00,
                MODIFY broker_commission_paid DECIMAL(15, 2) DEFAULT 0.00,
                MODIFY broker_commission_due DECIMAL(15, 2) DEFAULT 0.00");
        }

        if (Schema::hasTable('property_master_payments')) {
            DB::statement("ALTER TABLE property_master_payments 
                MODIFY amount DECIMAL(15, 2) NOT NULL DEFAULT 0.00");
        }

        if (Schema::hasTable('purchases')) {
            DB::statement("ALTER TABLE purchases 
                MODIFY purchase_amount DECIMAL(15, 2) DEFAULT 0.00,
                MODIFY paid_amount DECIMAL(15, 2) DEFAULT 0.00,
                MODIFY due_amount DECIMAL(15, 2) DEFAULT 0.00");
        }

        if (Schema::hasTable('purchase_payments')) {
            DB::statement("ALTER TABLE purchase_payments 
                MODIFY amount DECIMAL(15, 2) NOT NULL DEFAULT 0.00");
        }
    }
};
