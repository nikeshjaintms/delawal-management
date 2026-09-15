<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            if (!Schema::hasColumn('contractors', 'contract_amount')) {
                $table->decimal('contract_amount', 15, 2)->default(0)->after('address');
            }
            if (!Schema::hasColumn('contractors', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)->default(0)->after('contract_amount');
            }
            if (!Schema::hasColumn('contractors', 'due_amount')) {
                $table->decimal('due_amount', 15, 2)->default(0)->after('paid_amount');
            }
            if (!Schema::hasColumn('contractors', 'payment_status')) {
                $table->string('payment_status', 20)->default('unpaid')->after('due_amount');
            }
            if (!Schema::hasColumn('contractors', 'work_type')) {
                $table->string('work_type', 100)->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('contractors', 'contract_date')) {
                $table->date('contract_date')->nullable()->after('work_type');
            }
            if (!Schema::hasColumn('contractors', 'contract_notes')) {
                $table->text('contract_notes')->nullable()->after('contract_date');
            }
        });

        if (!Schema::hasTable('contractor_payments')) {
            Schema::create('contractor_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contractor_id');
                $table->unsignedBigInteger('firm_id');
                $table->unsignedBigInteger('project_id')->nullable();
                $table->unsignedBigInteger('property_id')->nullable();
                $table->unsignedBigInteger('payment_mode_id')->nullable();
                $table->decimal('amount', 15, 2);
                $table->date('payment_date');
                $table->string('payment_mode', 100);
                $table->string('reference_no', 150)->nullable();
                $table->string('bank_name', 150)->nullable();
                $table->string('bill_no', 150)->nullable();
                $table->string('document_file')->nullable();
                $table->string('payment_type', 50)->nullable();
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('cascade');
                $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
                $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
                $table->foreign('payment_mode_id')->references('id')->on('payment_modes')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_payments');

        Schema::table('contractors', function (Blueprint $table) {
            $cols = ['contract_amount', 'paid_amount', 'due_amount', 'payment_status', 'work_type', 'contract_date', 'contract_notes'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('contractors', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
