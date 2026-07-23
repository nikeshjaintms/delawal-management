<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('firmables')) {
            Schema::create('firmables', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('firm_id');
                $table->unsignedBigInteger('firmable_id');
                $table->string('firmable_type');
                $table->timestamps();

                $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
                $table->index(['firmable_type', 'firmable_id']);
                $table->unique(['firm_id', 'firmable_id', 'firmable_type']);
            });
        }

        // Map tables to model classes
        $tablesToModels = [
            'incomes'              => 'App\\Models\\Income',
            'expenses'             => 'App\\Models\\Expense',
            'purchases'            => 'App\\Models\\Purchase',
            'receipts'             => 'App\\Models\\Receipt',
            'loans'                => 'App\\Models\\Loan',
            'loan_emi_schedules'   => 'App\\Models\\LoanEmiSchedule',
            'broker_commissions'   => 'App\\Models\\BrokerCommission',
            'bookings'             => 'App\\Models\\Booking',
            'customers'            => 'App\\Models\\Customer',
            'brokers'              => 'App\\Models\\Broker',
            'properties'           => 'App\\Models\\Property',
            'tenants'              => 'App\\Models\\Tenant',
            'rentals'              => 'App\\Models\\Rental',
            'rental_payments'      => 'App\\Models\\RentalPayment',
            'vendors'              => 'App\\Models\\Vendor',
            'materials'            => 'App\\Models\\Material',
            'stock_inwards'        => 'App\\Models\\StockInward',
            'stock_outwards'       => 'App\\Models\\StockOutward',
            'ledgers'              => 'App\\Models\\Ledger',
            'credit_notes'         => 'App\\Models\\CreditNote',
            'debit_notes'          => 'App\\Models\\DebitNote',
            'material_categories'  => 'App\\Models\\MaterialCategory',
            'expense_categories'   => 'App\\Models\\ExpenseCategory',
            'payment_modes'        => 'App\\Models\\PaymentMode',
            'property_types'       => 'App\\Models\\PropertyType',
            'property_statuses'    => 'App\\Models\\PropertyStatus',
            'property_documents'   => 'App\\Models\\PropertyDocument',
            'forms'                => 'App\\Models\\Form',
            'form_submissions'     => 'App\\Models\\FormSubmission',
            'users'                => 'App\\Models\\User',
        ];

        foreach ($tablesToModels as $tableName => $modelClass) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'firm_id')) {
                DB::statement("
                    INSERT IGNORE INTO firmables (firm_id, firmable_id, firmable_type, created_at, updated_at)
                    SELECT firm_id, id, ?, NOW(), NOW()
                    FROM {$tableName}
                    WHERE firm_id IS NOT NULL
                ", [$modelClass]);
            }
        }
    }

    public function down(): void {
        Schema::dropIfExists('firmables');
    }
};
