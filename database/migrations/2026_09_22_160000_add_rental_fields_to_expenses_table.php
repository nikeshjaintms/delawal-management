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
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'rental_id')) {
                $table->unsignedBigInteger('rental_id')->nullable()->after('property_id')->index();
            }
            if (!Schema::hasColumn('expenses', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('rental_id')->index();
            }
            if (!Schema::hasColumn('expenses', 'is_tenant_recoverable')) {
                $table->boolean('is_tenant_recoverable')->default(false)->after('amount');
            }
            if (!Schema::hasColumn('expenses', 'recovery_amount')) {
                $table->decimal('recovery_amount', 15, 2)->nullable()->after('is_tenant_recoverable');
            }
            if (!Schema::hasColumn('expenses', 'recovery_status')) {
                $table->string('recovery_status')->nullable()->default('Pending')->after('recovery_amount');
            }
            if (!Schema::hasColumn('expenses', 'expense_subcategory')) {
                $table->string('expense_subcategory')->nullable()->after('expense_category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'rental_id')) {
                $table->dropColumn('rental_id');
            }
            if (Schema::hasColumn('expenses', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }
            if (Schema::hasColumn('expenses', 'is_tenant_recoverable')) {
                $table->dropColumn('is_tenant_recoverable');
            }
            if (Schema::hasColumn('expenses', 'recovery_amount')) {
                $table->dropColumn('recovery_amount');
            }
            if (Schema::hasColumn('expenses', 'recovery_status')) {
                $table->dropColumn('recovery_status');
            }
            if (Schema::hasColumn('expenses', 'expense_subcategory')) {
                $table->dropColumn('expense_subcategory');
            }
        });
    }
};
