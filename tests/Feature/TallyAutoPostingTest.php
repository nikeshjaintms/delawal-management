<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Broker;
use App\Models\BrokerCommission;
use App\Models\Contractor;
use App\Models\ContractorPayment;
use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Firm;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Ledger;
use App\Models\Loan;
use App\Models\LoanEmiSchedule;
use App\Models\LoanPayment;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyMaster;
use App\Models\PropertyMasterPayment;
use App\Models\PropertySale;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\Receipt;
use App\Models\Rental;
use App\Models\RentalPayment;
use App\Models\Role;
use App\Models\StockInward;
use App\Models\User;
use App\Services\PartnerProfitService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TallyAutoPostingTest extends TestCase
{
    protected User $admin;
    protected Firm $firm;
    protected Customer $customer;
    protected Project $project;
    protected PaymentMode $cashMode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createMinimalSqliteSchema();

        Artisan::call('migrate', [
            '--path' => 'database/migrations/2026_11_27_160000_create_partner_profit_sharing_tables.php',
        ]);

        $adminRole = Role::create(['name' => 'Super Admin', 'description' => 'Admin']);
        $this->firm = Firm::create([
            'firm_name'  => 'Delawala Realty',
            'owner_name' => 'Owner',
            'email'      => 'realty@delawala.test',
            'mobile'     => '9876543210',
            'status'     => 'active',
        ]);

        $this->admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@delawala.test',
            'password' => bcrypt('password'),
            'role_id'  => $adminRole->id,
            'firm_id'  => $this->firm->id,
            'status'   => 'active',
        ]);

        $this->customer = Customer::create([
            'firm_id' => $this->firm->id,
            'name'    => 'Aarav Shah',
            'mobile'  => '9898989898',
            'status'  => 'active',
        ]);

        $this->project = Project::create([
            'firm_id'      => $this->firm->id,
            'project_name' => 'Emerald Heights',
            'project_code' => 'EH-01',
            'status'       => 'active',
        ]);

        $this->cashMode = PaymentMode::create(['name' => 'Cash', 'status' => 'active']);
    }

    private function createMinimalSqliteSchema(): void
    {
        Schema::create('roles', fn (Blueprint $t) => [$t->id(), $t->string('name'), $t->string('role_name')->nullable(), $t->string('description')->nullable(), $t->timestamps()]);
        Schema::create('permissions', fn (Blueprint $t) => [$t->id(), $t->string('name')->nullable(), $t->string('key')->nullable(), $t->string('module')->nullable(), $t->timestamps()]);
        Schema::create('role_permissions', fn (Blueprint $t) => [$t->id(), $t->unsignedBigInteger('role_id'), $t->unsignedBigInteger('permission_id'), $t->timestamps()]);
        Schema::create('firms', fn (Blueprint $t) => [$t->id(), $t->string('firm_name'), $t->string('owner_name')->nullable(), $t->string('email')->nullable(), $t->string('mobile')->nullable(), $t->string('address')->nullable(), $t->string('city')->nullable(), $t->string('state')->nullable(), $t->string('pincode')->nullable(), $t->string('gst_no')->nullable(), $t->string('firm_logo')->nullable(), $t->string('digital_signature')->nullable(), $t->string('status')->default('active'), $t->timestamps()]);
        Schema::create('firmables', fn (Blueprint $t) => [$t->id(), $t->unsignedBigInteger('firm_id'), $t->morphs('firmable'), $t->timestamps()]);
        Schema::create('users', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->foreignId('role_id')->nullable(), $t->string('name'), $t->string('email')->unique(), $t->string('password'), $t->string('role')->nullable(), $t->string('status')->default('active'), $t->rememberToken(), $t->timestamps()]);
        Schema::create('projects', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->unsignedBigInteger('property_id')->nullable(), $t->string('project_name'), $t->string('project_code')->nullable(), $t->string('status')->default('active'), $t->timestamps()]);
        Schema::create('property_masters', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->unsignedBigInteger('vendor_id')->nullable(), $t->unsignedBigInteger('broker_id')->nullable(), $t->string('property_name'), $t->string('property_code')->nullable(), $t->decimal('purchase_price', 15, 2)->default(0), $t->decimal('paid_amount', 15, 2)->default(0), $t->decimal('due_amount', 15, 2)->default(0), $t->decimal('purchase_rate', 15, 2)->nullable(), $t->decimal('total_area', 15, 2)->nullable(), $t->string('status')->default('active'), $t->timestamps()]);
        Schema::create('project_property_master', fn (Blueprint $t) => [$t->id(), $t->unsignedBigInteger('project_id'), $t->unsignedBigInteger('property_master_id'), $t->timestamps()]);
        Schema::create('properties', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->unsignedBigInteger('property_master_id')->nullable(), $t->unsignedBigInteger('project_id')->nullable(), $t->string('property_name'), $t->string('property_code')->nullable(), $t->string('unit_no')->nullable(), $t->decimal('size', 12, 2)->nullable(), $t->decimal('price', 15, 2)->default(0), $t->decimal('purchase_rate', 15, 2)->nullable(), $t->string('status')->default('available'), $t->timestamps()]);
        Schema::create('customers', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->string('name'), $t->string('mobile')->nullable(), $t->string('status')->default('active'), $t->timestamps()]);
        Schema::create('vendors', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->string('name'), $t->string('status')->default('active'), $t->timestamps()]);
        Schema::create('brokers', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->string('name'), $t->string('status')->default('active'), $t->timestamps()]);
        Schema::create('sellers', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->string('name'), $t->string('status')->default('active'), $t->timestamps()]);
        Schema::create('contractors', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->string('contractor_name')->nullable(), $t->string('name')->nullable(), $t->string('status')->default('active'), $t->timestamps()]);
        Schema::create('payment_modes', fn (Blueprint $t) => [$t->id(), $t->string('name'), $t->string('status')->default('active'), $t->timestamps()]);
        Schema::create('payment_mode_firm', fn (Blueprint $t) => [$t->id(), $t->unsignedBigInteger('payment_mode_id'), $t->unsignedBigInteger('firm_id'), $t->timestamps()]);
        Schema::create('bookings', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->foreignId('property_id')->nullable(), $t->foreignId('customer_id')->nullable(), $t->unsignedBigInteger('seller_id')->nullable(), $t->string('seller_name')->nullable(), $t->unsignedBigInteger('broker_id')->nullable(), $t->string('broker_name')->nullable(), $t->string('booking_type')->default('booking'), $t->date('booking_date')->nullable(), $t->decimal('total_amount', 15, 2)->default(0), $t->string('discount_type')->nullable(), $t->decimal('discount_value', 15, 2)->default(0), $t->decimal('discount_amount', 15, 2)->default(0), $t->decimal('final_amount', 15, 2)->default(0), $t->decimal('booking_amount', 15, 2)->default(0), $t->decimal('remaining_amount', 15, 2)->default(0), $t->unsignedBigInteger('payment_mode_id')->nullable(), $t->string('payment_mode')->nullable(), $t->string('transaction_ref')->nullable(), $t->date('agreement_date')->nullable(), $t->string('status')->default('confirmed'), $t->string('payment_status')->default('pending'), $t->text('remarks')->nullable(), $t->timestamps()]);
        Schema::create('booking_property', fn (Blueprint $t) => [$t->id(), $t->unsignedBigInteger('booking_id'), $t->unsignedBigInteger('property_id'), $t->timestamps()]);
        Schema::create('property_sales', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->foreignId('property_id')->nullable(), $t->foreignId('customer_id')->nullable(), $t->unsignedBigInteger('broker_id')->nullable(), $t->string('invoice_no')->nullable(), $t->date('sale_date')->nullable(), $t->decimal('sale_amount', 15, 2)->default(0), $t->decimal('purchase_cost', 15, 2)->default(0), $t->decimal('property_expenses', 15, 2)->default(0), $t->decimal('booking_amount', 15, 2)->default(0), $t->decimal('remaining_amount', 15, 2)->default(0), $t->decimal('broker_commission_amount', 15, 2)->default(0), $t->string('payment_status')->default('pending'), $t->string('sale_status')->default('sold'), $t->timestamps()]);
        Schema::create('property_sale_property', fn (Blueprint $t) => [$t->id(), $t->unsignedBigInteger('property_sale_id'), $t->unsignedBigInteger('property_id'), $t->timestamps()]);
        Schema::create('payments', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->foreignId('property_sale_id')->nullable(), $t->foreignId('customer_id')->nullable(), $t->foreignId('property_id')->nullable(), $t->decimal('total_amount', 15, 2)->default(0), $t->decimal('paid_amount', 15, 2)->default(0), $t->decimal('pending_amount', 15, 2)->default(0), $t->decimal('payment_amount', 15, 2)->default(0), $t->string('payment_mode')->nullable(), $t->string('transaction_ref')->nullable(), $t->date('payment_date')->nullable(), $t->string('status')->default('received'), $t->text('remarks')->nullable(), $t->timestamps()]);
        Schema::create('expenses', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->foreignId('project_id')->nullable(), $t->foreignId('property_id')->nullable(), $t->unsignedBigInteger('vendor_id')->nullable(), $t->date('expense_date')->nullable(), $t->string('expense_type')->nullable(), $t->string('expense_category')->nullable(), $t->string('expense_title')->nullable(), $t->decimal('amount', 15, 2)->default(0), $t->string('payment_mode')->nullable(), $t->string('bill_no')->nullable(), $t->string('reference_no')->nullable(), $t->string('paid_to')->nullable(), $t->string('approval_status')->default('Approved'), $t->timestamps()]);
        Schema::create('expense_property', fn (Blueprint $t) => [$t->id(), $t->unsignedBigInteger('expense_id'), $t->unsignedBigInteger('property_id'), $t->timestamps()]);
        Schema::create('expense_categories', fn (Blueprint $t) => [$t->id(), $t->string('name'), $t->string('status')->default('active'), $t->timestamps()]);
        Schema::create('contractor_payments', fn (Blueprint $t) => [$t->id(), $t->unsignedBigInteger('contractor_id')->nullable(), $t->foreignId('firm_id')->nullable(), $t->foreignId('project_id')->nullable(), $t->foreignId('property_id')->nullable(), $t->date('payment_date')->nullable(), $t->string('payment_mode')->nullable(), $t->string('reference_no')->nullable(), $t->string('bill_no')->nullable(), $t->string('payment_type')->nullable(), $t->text('remarks')->nullable(), $t->decimal('amount', 15, 2)->default(0), $t->timestamps()]);
        Schema::create('property_master_payments', fn (Blueprint $t) => [$t->id(), $t->foreignId('property_master_id')->nullable(), $t->foreignId('firm_id')->nullable(), $t->date('payment_date')->nullable(), $t->string('payment_mode')->nullable(), $t->string('reference_no')->nullable(), $t->text('remarks')->nullable(), $t->decimal('amount', 15, 2)->default(0), $t->timestamps()]);
        Schema::create('purchases', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->unsignedBigInteger('vendor_id')->nullable(), $t->unsignedBigInteger('property_id')->nullable(), $t->string('property_name')->nullable(), $t->string('item_name')->nullable(), $t->decimal('paid_amount', 15, 2)->default(0), $t->decimal('due_amount', 15, 2)->default(0), $t->date('purchase_date')->nullable(), $t->string('payment_mode')->nullable(), $t->string('payment_status')->nullable(), $t->timestamps()]);
        Schema::create('purchase_payments', fn (Blueprint $t) => [$t->id(), $t->unsignedBigInteger('purchase_id')->nullable(), $t->foreignId('firm_id')->nullable(), $t->date('payment_date')->nullable(), $t->string('payment_mode')->nullable(), $t->string('reference_no')->nullable(), $t->text('remarks')->nullable(), $t->decimal('amount', 15, 2)->default(0), $t->timestamps()]);
        Schema::create('broker_commissions', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->unsignedBigInteger('broker_id')->nullable(), $t->unsignedBigInteger('customer_id')->nullable(), $t->foreignId('property_id')->nullable(), $t->foreignId('booking_id')->nullable(), $t->foreignId('property_sale_id')->nullable(), $t->decimal('commission_amount', 15, 2)->default(0), $t->string('payment_status')->default('pending'), $t->date('payment_date')->nullable(), $t->text('remarks')->nullable(), $t->timestamps()]);
        Schema::create('rentals', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->foreignId('property_id')->nullable(), $t->string('tenant_name')->nullable(), $t->string('tenant_mobile')->nullable(), $t->decimal('security_deposit', 15, 2)->default(0), $t->date('rent_start_date')->nullable(), $t->string('rental_status')->default('active'), $t->timestamps()]);
        Schema::create('rental_property', fn (Blueprint $t) => [$t->id(), $t->unsignedBigInteger('rental_id'), $t->unsignedBigInteger('property_id'), $t->timestamps()]);
        Schema::create('rental_payments', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->foreignId('rental_id')->nullable(), $t->foreignId('property_id')->nullable(), $t->string('payment_month')->nullable(), $t->string('payment_year')->nullable(), $t->date('payment_date')->nullable(), $t->string('payment_mode')->nullable(), $t->string('receipt_no')->nullable(), $t->string('transaction_id')->nullable(), $t->string('status')->default('paid'), $t->decimal('paid_amount', 15, 2)->default(0), $t->decimal('pending_amount', 15, 2)->default(0), $t->timestamps()]);
        Schema::create('incomes', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->foreignId('property_id')->nullable(), $t->date('income_date')->nullable(), $t->string('income_type')->nullable(), $t->decimal('amount', 15, 2)->default(0), $t->unsignedBigInteger('payment_mode_id')->nullable(), $t->string('received_from')->nullable(), $t->string('reference_no')->nullable(), $t->text('description')->nullable(), $t->string('status')->default('active'), $t->timestamps()]);
        Schema::create('receipts', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->string('receipt_no')->nullable(), $t->date('receipt_date')->nullable(), $t->string('received_from')->nullable(), $t->decimal('amount', 15, 2)->default(0), $t->unsignedBigInteger('payment_mode_id')->nullable(), $t->string('reference_no')->nullable(), $t->text('remarks')->nullable(), $t->string('status')->default('active'), $t->timestamps()]);
        Schema::create('ledgers', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->date('ledger_date')->nullable(), $t->unsignedBigInteger('property_id')->nullable(), $t->unsignedBigInteger('customer_id')->nullable(), $t->unsignedBigInteger('vendor_id')->nullable(), $t->unsignedBigInteger('broker_id')->nullable(), $t->string('transaction_type'), $t->string('transaction_title'), $t->decimal('debit_amount', 15, 2)->default(0), $t->decimal('credit_amount', 15, 2)->default(0), $t->string('payment_mode')->nullable(), $t->string('reference_no')->nullable(), $t->text('remarks')->nullable(), $t->timestamps()]);
        Schema::create('loans', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->unsignedBigInteger('property_id')->nullable(), $t->unsignedBigInteger('customer_id')->nullable(), $t->string('bank_name')->nullable(), $t->string('person_name')->nullable(), $t->string('loan_nature')->default('taken'), $t->decimal('loan_amount', 15, 2)->default(0), $t->decimal('paid_amount', 15, 2)->default(0), $t->decimal('pending_amount', 15, 2)->default(0), $t->date('loan_start_date')->nullable(), $t->timestamps()]);
        Schema::create('loan_emi_schedules', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->foreignId('loan_id')->nullable(), $t->string('emi_month')->nullable(), $t->string('emi_year')->nullable(), $t->date('emi_date')->nullable(), $t->decimal('emi_amount', 15, 2)->default(0), $t->decimal('paid_amount', 15, 2)->default(0), $t->decimal('pending_amount', 15, 2)->default(0), $t->date('payment_date')->nullable(), $t->string('payment_mode')->nullable(), $t->string('transaction_reference')->nullable(), $t->string('emi_status')->default('Pending'), $t->text('remarks')->nullable(), $t->timestamps()]);
        Schema::create('loan_payments', fn (Blueprint $t) => [$t->id(), $t->foreignId('loan_id')->nullable(), $t->foreignId('firm_id')->nullable(), $t->decimal('amount', 15, 2)->default(0), $t->date('payment_date')->nullable(), $t->string('payment_mode')->nullable(), $t->string('reference_no')->nullable(), $t->text('remarks')->nullable(), $t->timestamps()]);
        Schema::create('invoices', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->unsignedBigInteger('customer_id')->nullable(), $t->unsignedBigInteger('vendor_id')->nullable(), $t->string('invoice_no')->nullable(), $t->string('invoice_type')->default('sale'), $t->string('recipient_name')->nullable(), $t->timestamps()]);
        Schema::create('invoice_payments', fn (Blueprint $t) => [$t->id(), $t->foreignId('invoice_id')->nullable(), $t->date('payment_date')->nullable(), $t->decimal('amount', 15, 2)->default(0), $t->unsignedBigInteger('payment_mode_id')->nullable(), $t->string('payment_mode')->nullable(), $t->string('transaction_reference')->nullable(), $t->text('notes')->nullable(), $t->timestamps()]);
        Schema::create('stock_inwards', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->foreignId('project_id')->nullable(), $t->unsignedBigInteger('purchase_order_id')->nullable(), $t->decimal('total_amount', 15, 2)->default(0), $t->timestamps()]);
        Schema::create('credit_notes', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->unsignedBigInteger('property_sale_id')->nullable(), $t->date('credit_note_date')->nullable(), $t->decimal('credit_amount', 15, 2)->default(0), $t->string('status')->default('Pending'), $t->timestamps()]);
        Schema::create('agri_incomes', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->unsignedBigInteger('project_id')->nullable(), $t->unsignedBigInteger('property_id')->nullable(), $t->date('income_date')->nullable(), $t->string('buyer_name')->nullable(), $t->string('crop_product')->nullable(), $t->string('income_type')->nullable(), $t->string('invoice_no')->nullable(), $t->string('payment_method')->nullable(), $t->decimal('payment_received', 15, 2)->default(0), $t->decimal('pending_amount', 15, 2)->default(0), $t->string('payment_status')->default('paid'), $t->timestamps()]);
        Schema::create('agri_farms', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->string('farm_name')->nullable(), $t->timestamps()]);
        Schema::create('agri_labours', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->string('name')->nullable(), $t->timestamps()]);
        Schema::create('agri_labour_payments', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->unsignedBigInteger('labour_id')->nullable(), $t->unsignedBigInteger('farm_id')->nullable(), $t->date('payment_date')->nullable(), $t->string('payment_mode')->nullable(), $t->string('payment_type')->nullable(), $t->string('reference_no')->nullable(), $t->string('payment_status')->default('paid'), $t->decimal('amount', 15, 2)->default(0), $t->timestamps()]);
        Schema::create('agri_expenses', fn (Blueprint $t) => [$t->id(), $t->foreignId('firm_id')->nullable(), $t->unsignedBigInteger('farm_id')->nullable(), $t->unsignedBigInteger('project_id')->nullable(), $t->unsignedBigInteger('property_id')->nullable(), $t->unsignedBigInteger('vendor_id')->nullable(), $t->date('expense_date')->nullable(), $t->string('payment_method')->nullable(), $t->decimal('amount', 15, 2)->default(0), $t->timestamps()]);
        Schema::create('audit_logs', fn (Blueprint $t) => [$t->id(), $t->unsignedBigInteger('user_id')->nullable(), $t->string('user_name')->nullable(), $t->string('user_role')->nullable(), $t->string('module_name')->nullable(), $t->string('action_type')->nullable(), $t->text('description')->nullable(), $t->string('ip_address')->nullable(), $t->timestamps()]);
    }

    public function test_booking_auto_posts_to_income_receipt_ledger_and_reports_without_double_counting(): void
    {
        $plot = Property::create([
            'firm_id'       => $this->firm->id,
            'project_id'    => $this->project->id,
            'property_name' => 'Plot 101',
            'property_code' => 'EH-101',
            'unit_no'       => '101',
            'price'         => 1000000,
            'status'        => 'available',
        ]);

        // 1. Create a Booking with ₹75,000 token advance
        $booking = Booking::create([
            'firm_id'          => $this->firm->id,
            'property_id'      => $plot->id,
            'customer_id'      => $this->customer->id,
            'booking_type'     => 'booking',
            'booking_date'     => '2026-11-20',
            'total_amount'     => 1000000,
            'final_amount'     => 1000000,
            'booking_amount'   => 75000,
            'remaining_amount' => 925000,
            'payment_mode_id'  => $this->cashMode->id,
            'payment_mode'     => 'Cash',
            'transaction_ref'  => 'BK-REF-01',
            'status'           => 'confirmed',
            'payment_status'   => 'partial',
        ]);

        // Assert Ledger, Receipt, and Income were all auto-created
        $this->assertDatabaseHas('ledgers', [
            'firm_id'          => $this->firm->id,
            'customer_id'      => $this->customer->id,
            'transaction_type' => 'Payment Received',
            'credit_amount'    => 75000,
            'reference_no'     => 'BK-REF-01',
        ]);
        $this->assertDatabaseHas('receipts', [
            'firm_id'       => $this->firm->id,
            'receipt_no'    => "RCT-BK-{$booking->id}",
            'received_from' => 'Aarav Shah',
            'amount'        => 75000,
        ]);
        $this->assertDatabaseHas('incomes', [
            'firm_id'     => $this->firm->id,
            'income_type' => 'Booking Advance',
            'amount'      => 75000,
        ]);

        // 2. Update Booking amount to ₹1,25,000 -> updates existing entries (no duplicates)
        $booking->update(['booking_amount' => 125000, 'remaining_amount' => 875000]);
        $this->assertEquals(1, Ledger::count());
        $this->assertEquals(1, Receipt::count());
        $this->assertEquals(1, Income::count());
        $this->assertEquals(125000.0, (float) Ledger::first()->credit_amount);
        $this->assertEquals(125000.0, (float) Receipt::first()->amount);
        $this->assertEquals(125000.0, (float) Income::first()->amount);

        // 3. Also add a separate PropertySale + Payment on ANOTHER plot -> Cash Flow & Payments Report must show BOTH!
        $plot2 = Property::create([
            'firm_id'       => $this->firm->id,
            'project_id'    => $this->project->id,
            'property_name' => 'Plot 102',
            'property_code' => 'EH-102',
            'unit_no'       => '102',
            'price'         => 500000,
            'status'        => 'sold',
        ]);

        $sale = PropertySale::create([
            'firm_id'          => $this->firm->id,
            'property_id'      => $plot2->id,
            'customer_id'      => $this->customer->id,
            'sale_date'        => '2026-11-21',
            'sale_amount'      => 500000,
            'purchase_cost'    => 200000,
            'booking_amount'   => 200000,
            'remaining_amount' => 300000,
            'payment_status'   => 'partial',
            'sale_status'      => 'sold',
        ]);

        Payment::create([
            'firm_id'          => $this->firm->id,
            'property_sale_id' => $sale->id,
            'customer_id'      => $this->customer->id,
            'property_id'      => $plot2->id,
            'total_amount'     => 500000,
            'paid_amount'      => 200000,
            'pending_amount'   => 300000,
            'payment_amount'   => 200000,
            'payment_mode'     => 'Cash',
            'payment_date'     => '2026-11-21',
            'status'           => 'partial',
        ]);

        $this->actingAs($this->admin)->withSession(['login_type' => 'admin']);

        // Cash flow must include BOTH ₹2,00,000 sale payment AND ₹1,25,000 booking advance = ₹3,25,000
        $cf = $this->get(route('reports.cash-flow'));
        $cf->assertStatus(200);
        $this->assertEquals(325000.0, (float) $cf->viewData('totalInflow'));

        // Balance sheet cashReceived must also be ₹3,25,000 (not max(200k, 125k)) and no double count from [AUTO:BK#] income
        $bs = $this->get(route('reports.balance-sheet'));
        $bs->assertStatus(200);
        $this->assertEquals(325000.0, (float) $bs->viewData('cashReceived'));
        $this->assertEquals(325000.0, (float) $bs->viewData('totalCashAndBank'));

        // Payments report must include BOTH Booking Advance and Sale Payment
        $pr = $this->get(route('reports.payments'));
        $pr->assertStatus(200);
        $this->assertEquals(325000.0, (float) $pr->viewData('totalReceived'));

        // PartnerProfitService must NOT double-count [AUTO:BK#] income
        $partner = \App\Models\Partner::create(['firm_id' => $this->firm->id, 'name' => 'P1', 'is_silent' => true, 'status' => 'active']);
        $share = \App\Models\PartnerShare::create([
            'firm_id'          => $this->firm->id,
            'partner_id'       => $partner->id,
            'scope_type'       => \App\Models\PartnerShare::SCOPE_FIRM,
            'share_percentage' => 50,
            'status'           => 'active',
        ]);
        $metrics = app(PartnerProfitService::class)->calculateForShare($share);
        $this->assertEquals(325000.0, $metrics['realized_inflow']);

        // 4. Cancelling the booking removes its auto-posted Ledger, Receipt, and Income
        $booking->update(['status' => 'cancelled']);
        $this->assertDatabaseMissing('incomes', ['income_type' => 'Booking Advance']);
        $this->assertDatabaseMissing('receipts', ['receipt_no' => "RCT-BK-{$booking->id}"]);
    }

    public function test_auto_posting_across_all_erp_modules_and_tally_sync_command(): void
    {
        $plot = Property::create([
            'firm_id'       => $this->firm->id,
            'project_id'    => $this->project->id,
            'property_name' => 'Plot 201',
            'property_code' => 'EH-201',
            'unit_no'       => '201',
            'price'         => 800000,
            'status'        => 'available',
        ]);

        // RentalPayment -> Ledger + Receipt
        $rental = Rental::create(['firm_id' => $this->firm->id, 'property_id' => $plot->id, 'tenant_name' => 'Tenant X']);
        RentalPayment::create([
            'firm_id'       => $this->firm->id,
            'rental_id'     => $rental->id,
            'property_id'   => $plot->id,
            'payment_month' => 'November',
            'payment_year'  => '2026',
            'payment_date'  => '2026-11-05',
            'payment_mode'  => 'UPI',
            'paid_amount'   => 15000,
        ]);

        // Manual Income -> Ledger + Receipt (no recursion)
        Income::create([
            'firm_id'         => $this->firm->id,
            'property_id'     => $plot->id,
            'income_date'     => '2026-11-06',
            'income_type'     => 'Commission',
            'amount'          => 10000,
            'payment_mode_id' => $this->cashMode->id,
            'received_from'   => 'Partner Referral',
            'status'          => 'active',
        ]);

        // Expense -> Ledger Debit
        Expense::create([
            'firm_id'       => $this->firm->id,
            'project_id'    => $this->project->id,
            'property_id'   => $plot->id,
            'expense_date'  => '2026-11-07',
            'expense_type'  => 'Property',
            'expense_title' => 'Gate Paint',
            'amount'        => 5000,
            'payment_mode'  => 'Cash',
        ]);

        // PropertyMasterPayment -> Ledger Debit
        $pm = PropertyMaster::create(['firm_id' => $this->firm->id, 'property_name' => 'West Land', 'purchase_price' => 400000]);
        PropertyMasterPayment::create([
            'property_master_id' => $pm->id,
            'firm_id'            => $this->firm->id,
            'payment_date'       => '2026-11-08',
            'amount'             => 100000,
            'payment_mode'       => 'Bank Transfer',
        ]);

        // ContractorPayment -> Ledger Debit
        $contractor = Contractor::create(['firm_id' => $this->firm->id, 'contractor_name' => 'BuildWell Co']);
        ContractorPayment::create([
            'contractor_id' => $contractor->id,
            'firm_id'       => $this->firm->id,
            'project_id'    => $this->project->id,
            'payment_date'  => '2026-11-09',
            'amount'        => 25000,
            'payment_mode'  => 'Cash',
        ]);

        // Loan EMI + LoanPayment -> Ledger Debit
        $loan = Loan::create(['firm_id' => $this->firm->id, 'bank_name' => 'HDFC Bank', 'loan_nature' => 'taken', 'loan_amount' => 500000]);
        LoanEmiSchedule::create([
            'firm_id'      => $this->firm->id,
            'loan_id'      => $loan->id,
            'emi_month'    => '11',
            'emi_year'     => '2026',
            'emi_date'     => '2026-11-10',
            'emi_amount'   => 12000,
            'paid_amount'  => 12000,
            'payment_date' => '2026-11-10',
            'emi_status'   => 'Paid',
        ]);
        LoanPayment::create([
            'firm_id'      => $this->firm->id,
            'loan_id'      => $loan->id,
            'amount'       => 8000,
            'payment_date' => '2026-11-11',
        ]);

        // Paid BrokerCommission -> Ledger Debit
        $broker = Broker::create(['firm_id' => $this->firm->id, 'name' => 'Broker Raj']);
        BrokerCommission::create([
            'firm_id'           => $this->firm->id,
            'broker_id'         => $broker->id,
            'property_id'       => $plot->id,
            'commission_amount' => 7000,
            'payment_status'    => 'paid',
            'payment_date'      => '2026-11-12',
        ]);

        // PurchasePayment -> Ledger Debit
        $purchase = Purchase::create(['firm_id' => $this->firm->id, 'property_name' => 'Corner Shop']);
        PurchasePayment::create([
            'firm_id'      => $this->firm->id,
            'purchase_id'  => $purchase->id,
            'amount'       => 30000,
            'payment_date' => '2026-11-13',
        ]);

        // InvoicePayment -> Ledger Credit + Receipt
        $inv = Invoice::create(['firm_id' => $this->firm->id, 'invoice_no' => 'INV-001', 'invoice_type' => 'sale', 'recipient_name' => 'Aarav Shah']);
        InvoicePayment::create([
            'invoice_id'   => $inv->id,
            'payment_date' => '2026-11-14',
            'amount'       => 20000,
            'payment_mode' => 'UPI',
        ]);

        // Verify Credits = 15k + 10k + 20k = 45,000; Debits = 5k + 100k + 25k + 12k + 8k + 7k + 30k = 187,000
        $this->assertEquals(45000.0, (float) Ledger::sum('credit_amount'));
        $this->assertEquals(187000.0, (float) Ledger::sum('debit_amount'));
        $this->assertEquals(3, Receipt::count()); // Rental, Income, InvoicePayment

        // Run php artisan tally:sync twice -> still exact same counts (idempotent!)
        $ledgerCount = Ledger::count();
        $receiptCount = Receipt::count();
        Artisan::call('tally:sync');
        Artisan::call('tally:sync');
        $this->assertEquals($ledgerCount, Ledger::count());
        $this->assertEquals($receiptCount, Receipt::count());

        // LedgerController renders cleanly for Admin
        $this->actingAs($this->admin)->withSession(['login_type' => 'admin'])
            ->get(route('ledgers.index'))
            ->assertStatus(200)
            ->assertSee('Gate Paint')
            ->assertSee('Rent Received');
    }
}
