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
            if (!Schema::hasColumn('expenses', 'expense_type')) {
                $table->string('expense_type')->nullable()->after('expense_category');
            }
            if (!Schema::hasColumn('expenses', 'description')) {
                $table->text('description')->nullable()->after('expense_title');
            }
            if (!Schema::hasColumn('expenses', 'reference_no')) {
                $table->string('reference_no')->nullable()->after('payment_mode');
            }
            if (!Schema::hasColumn('expenses', 'payment_account')) {
                $table->string('payment_account')->nullable()->after('reference_no');
            }
            if (!Schema::hasColumn('expenses', 'notes')) {
                $table->text('notes')->nullable()->after('remarks');
            }
            if (Schema::hasColumn('expenses', 'expense_title')) {
                $table->string('expense_title')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('expenses', 'expense_type')) {
                $columnsToDrop[] = 'expense_type';
            }
            if (Schema::hasColumn('expenses', 'description')) {
                $columnsToDrop[] = 'description';
            }
            if (Schema::hasColumn('expenses', 'reference_no')) {
                $columnsToDrop[] = 'reference_no';
            }
            if (Schema::hasColumn('expenses', 'payment_account')) {
                $columnsToDrop[] = 'payment_account';
            }
            if (Schema::hasColumn('expenses', 'notes')) {
                $columnsToDrop[] = 'notes';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
