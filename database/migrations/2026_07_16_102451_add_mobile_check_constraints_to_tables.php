<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enforce CHECK constraints for 10-digit numeric format on SQLite or MySQL
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'sqlite') {
            // Firms table
            DB::statement("ALTER TABLE firms ADD CONSTRAINT chk_firm_mobile CHECK (mobile REGEXP '^[0-9]{10}$');");
            DB::statement("ALTER TABLE firms ADD CONSTRAINT chk_firm_alt_mobile CHECK (alternate_mobile IS NULL OR alternate_mobile REGEXP '^[0-9]{10}$');");

            // Customers table
            DB::statement("ALTER TABLE customers ADD CONSTRAINT chk_customer_mobile CHECK (mobile REGEXP '^[0-9]{10}$');");

            // Brokers table
            DB::statement("ALTER TABLE brokers ADD CONSTRAINT chk_broker_mobile CHECK (mobile REGEXP '^[0-9]{10}$');");

            // Vendors table
            DB::statement("ALTER TABLE vendors ADD CONSTRAINT chk_vendor_mobile CHECK (mobile REGEXP '^[0-9]{10}$');");

            // Tenants table
            DB::statement("ALTER TABLE tenants ADD CONSTRAINT chk_tenant_mobile CHECK (mobile REGEXP '^[0-9]{10}$');");

            // Users table (mobile_number)
            DB::statement("ALTER TABLE users ADD CONSTRAINT chk_user_mobile CHECK (mobile_number REGEXP '^[0-9]{10}$');");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'sqlite') {
            DB::statement("ALTER TABLE firms DROP CONSTRAINT chk_firm_mobile;");
            DB::statement("ALTER TABLE firms DROP CONSTRAINT chk_firm_alt_mobile;");
            DB::statement("ALTER TABLE customers DROP CONSTRAINT chk_customer_mobile;");
            DB::statement("ALTER TABLE brokers DROP CONSTRAINT chk_broker_mobile;");
            DB::statement("ALTER TABLE vendors DROP CONSTRAINT chk_vendor_mobile;");
            DB::statement("ALTER TABLE tenants DROP CONSTRAINT chk_tenant_mobile;");
            DB::statement("ALTER TABLE users DROP CONSTRAINT chk_user_mobile;");
        }
    }
};
