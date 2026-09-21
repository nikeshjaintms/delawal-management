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
        // 1. Agriculture Farms / Land Table
        if (!Schema::hasTable('agri_farms')) {
            Schema::create('agri_farms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('firm_id')->nullable()->index();
                $table->unsignedBigInteger('property_id')->nullable()->index();
                $table->unsignedBigInteger('project_id')->nullable()->index();
                $table->string('farm_name');
                $table->string('owner_seller_name')->nullable();
                $table->string('village')->nullable();
                $table->string('taluka')->nullable();
                $table->string('district')->nullable();
                $table->string('survey_no')->nullable();
                $table->decimal('land_area', 10, 2)->default(0.00);
                $table->string('area_unit')->default('Acre'); // Acre, Bigha, Guntha, Hectare, Sq. Yard
                $table->string('farm_type')->default('Owned'); // Owned, Leased, Contract Farming, Shared, Other
                $table->string('crop_activity')->nullable(); // Wheat, Cotton, Mango, Rice, Mixed, etc.
                $table->date('start_date')->nullable();
                $table->string('status')->default('Active'); // Active, Under Preparation, Harvested, Fallow, Inactive
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('firm_id')->references('id')->on('firms')->onDelete('set null');
                $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            });
        }

        // 2. Agriculture Labours Table
        if (!Schema::hasTable('agri_labours')) {
            Schema::create('agri_labours', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('firm_id')->nullable()->index();
                $table->unsignedBigInteger('farm_id')->nullable()->index();
                $table->string('name');
                $table->string('mobile_number')->nullable();
                $table->string('labour_type')->default('Normal Labour'); // Fixed Labour, Normal Labour, Advance Labour
                $table->string('field_crop')->nullable();
                $table->date('joining_date')->nullable();
                $table->decimal('daily_wage', 12, 2)->default(0.00);
                $table->decimal('fixed_salary', 12, 2)->default(0.00);
                $table->decimal('total_earned', 12, 2)->default(0.00);
                $table->decimal('total_advance', 12, 2)->default(0.00);
                $table->decimal('total_paid', 12, 2)->default(0.00);
                $table->decimal('advance_balance', 12, 2)->default(0.00);
                $table->decimal('pending_amount', 12, 2)->default(0.00);
                $table->string('status')->default('Active'); // Active, Inactive
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('firm_id')->references('id')->on('firms')->onDelete('set null');
                $table->foreign('farm_id')->references('id')->on('agri_farms')->onDelete('set null');
            });
        }

        // 3. Agriculture Labour Payments & Wage Logs
        if (!Schema::hasTable('agri_labour_payments')) {
            Schema::create('agri_labour_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('firm_id')->nullable()->index();
                $table->unsignedBigInteger('farm_id')->nullable()->index();
                $table->unsignedBigInteger('labour_id')->index();
                $table->string('payment_type')->default('Daily Wage'); // Salary, Daily Wage, Advance Given, Advance Deduction, Bonus
                $table->date('payment_date');
                $table->decimal('working_days', 6, 2)->default(0.00);
                $table->decimal('daily_wage_rate', 10, 2)->default(0.00);
                $table->decimal('gross_amount', 12, 2)->default(0.00);
                $table->decimal('advance_deducted', 12, 2)->default(0.00);
                $table->decimal('amount', 12, 2)->default(0.00);
                $table->unsignedBigInteger('payment_mode_id')->nullable();
                $table->string('payment_mode')->nullable();
                $table->string('reference_no')->nullable();
                $table->string('payment_status')->default('Paid'); // Paid, Pending, Partial
                $table->boolean('sync_to_expense')->default(true);
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('firm_id')->references('id')->on('firms')->onDelete('set null');
                $table->foreign('farm_id')->references('id')->on('agri_farms')->onDelete('set null');
                $table->foreign('labour_id')->references('id')->on('agri_labours')->onDelete('cascade');
                $table->foreign('payment_mode_id')->references('id')->on('payment_modes')->onDelete('set null');
            });
        }

        // 4. Agriculture Expenses Table
        if (!Schema::hasTable('agri_expenses')) {
            Schema::create('agri_expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('firm_id')->nullable()->index();
                $table->unsignedBigInteger('farm_id')->nullable()->index();
                $table->unsignedBigInteger('project_id')->nullable()->index();
                $table->unsignedBigInteger('property_id')->nullable()->index();
                $table->date('expense_date');
                $table->string('category')->default('Other'); // Labour, Seeds, Fertilizer, Pesticides, Irrigation, Electricity, Water, Equipment, Machinery, Fuel, Transportation, Maintenance, Contractor, Other
                $table->string('expense_type')->default('Direct Farm Expense');
                $table->unsignedBigInteger('vendor_id')->nullable();
                $table->unsignedBigInteger('contractor_id')->nullable();
                $table->unsignedBigInteger('labour_id')->nullable();
                $table->unsignedBigInteger('labour_payment_id')->nullable();
                $table->decimal('amount', 12, 2)->default(0.00);
                $table->unsignedBigInteger('payment_mode_id')->nullable();
                $table->string('payment_method')->nullable();
                $table->string('payment_status')->default('Paid'); // Paid, Pending, Partial
                $table->string('bill_no')->nullable();
                $table->string('invoice_no')->nullable();
                $table->string('attachment')->nullable();
                $table->text('description')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('firm_id')->references('id')->on('firms')->onDelete('set null');
                $table->foreign('farm_id')->references('id')->on('agri_farms')->onDelete('set null');
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
                $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
                $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
                $table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('set null');
                $table->foreign('labour_id')->references('id')->on('agri_labours')->onDelete('set null');
                $table->foreign('labour_payment_id')->references('id')->on('agri_labour_payments')->onDelete('set null');
                $table->foreign('payment_mode_id')->references('id')->on('payment_modes')->onDelete('set null');
            });
        }

        // 5. Agriculture Incomes Table
        if (!Schema::hasTable('agri_incomes')) {
            Schema::create('agri_incomes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('firm_id')->nullable()->index();
                $table->unsignedBigInteger('farm_id')->nullable()->index();
                $table->unsignedBigInteger('project_id')->nullable()->index();
                $table->unsignedBigInteger('property_id')->nullable()->index();
                $table->date('income_date');
                $table->string('income_type')->default('Crop Sale'); // Crop Sale, Agricultural Product Sale, Land/Farm Income, Rental/Lease Income, Government Subsidy, Other Income
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->string('buyer_name')->nullable();
                $table->string('crop_product');
                $table->decimal('quantity', 12, 2)->default(0.00);
                $table->string('unit')->default('Kg'); // Kg, Quintal, Ton, Bag, Box, Litre, Mon (20kg), Other
                $table->decimal('rate', 12, 2)->default(0.00);
                $table->decimal('total_amount', 12, 2)->default(0.00);
                $table->decimal('payment_received', 12, 2)->default(0.00);
                $table->decimal('pending_amount', 12, 2)->default(0.00);
                $table->unsignedBigInteger('payment_mode_id')->nullable();
                $table->string('payment_method')->nullable();
                $table->string('payment_status')->default('Received'); // Received, Pending, Partial
                $table->string('invoice_no')->nullable();
                $table->string('attachment')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('firm_id')->references('id')->on('firms')->onDelete('set null');
                $table->foreign('farm_id')->references('id')->on('agri_farms')->onDelete('set null');
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
                $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
                $table->foreign('payment_mode_id')->references('id')->on('payment_modes')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agri_incomes');
        Schema::dropIfExists('agri_expenses');
        Schema::dropIfExists('agri_labour_payments');
        Schema::dropIfExists('agri_labours');
        Schema::dropIfExists('agri_farms');
    }
};
