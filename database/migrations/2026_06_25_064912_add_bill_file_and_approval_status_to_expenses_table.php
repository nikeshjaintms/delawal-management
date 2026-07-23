<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('bill_file')->nullable()->after('bill_no');
            $table->string('approval_status')->default('Pending')->after('bill_file');
            // Also add expense_category_id FK alongside the existing string column
            $table->unsignedBigInteger('expense_category_id')->nullable()->after('expense_category');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['expense_category_id']);
            $table->dropColumn(['bill_file', 'approval_status', 'expense_category_id']);
        });
    }
};
