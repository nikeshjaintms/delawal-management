<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BrokerCommission;
use App\Models\ContractorPayment;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Firm;
use App\Models\Income;
use App\Models\Partner;
use App\Models\PartnerPayout;
use App\Models\PartnerShare;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyMaster;
use App\Models\PropertyMasterPayment;
use App\Models\PropertySale;
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

class PartnerProfitSharingTest extends TestCase
{
    protected User $admin;
    protected User $staff;
    protected Firm $firm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createMinimalSqliteSchema();

        Artisan::call('migrate', [
            '--path' => 'database/migrations/2026_11_27_160000_create_partner_profit_sharing_tables.php',
        ]);

        $adminRole = Role::create(['name' => 'Super Admin', 'description' => 'Admin']);
        $staffRole = Role::create(['name' => 'Accountant', 'description' => 'Staff']);

        $this->firm = Firm::create([
            'firm_name'  => 'Delawala Infra',
            'owner_name' => 'Owner',
            'email'      => 'firm@delawala.test',
            'mobile'     => '9876543210',
            'status'     => 'active',
        ]);

        $this->admin = User::create([
            'name'     => 'System Admin',
            'email'    => 'admin@delawala.test',
            'password' => bcrypt('password'),
            'role_id'  => $adminRole->id,
            'firm_id'  => $this->firm->id,
            'status'   => 'active',
        ]);

        $this->staff = User::create([
            'name'     => 'Staff Member',
            'email'    => 'staff@delawala.test',
            'password' => bcrypt('password'),
            'role_id'  => $staffRole->id,
            'firm_id'  => $this->firm->id,
            'role'     => 'staff',
            'status'   => 'active',
        ]);
    }

    private function createMinimalSqliteSchema(): void
    {
        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('role_name')->nullable();
            $t->string('description')->nullable();
            $t->timestamps();
        });

        Schema::create('permissions', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->string('key')->nullable();
            $t->string('module')->nullable();
            $t->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('role_id');
            $t->unsignedBigInteger('permission_id');
            $t->timestamps();
        });

        Schema::create('firms', function (Blueprint $t) {
            $t->id();
            $t->string('firm_name');
            $t->string('owner_name')->nullable();
            $t->string('email')->nullable();
            $t->string('mobile')->nullable();
            $t->string('address')->nullable();
            $t->string('city')->nullable();
            $t->string('state')->nullable();
            $t->string('pincode')->nullable();
            $t->string('gst_no')->nullable();
            $t->string('firm_logo')->nullable();
            $t->string('digital_signature')->nullable();
            $t->string('status')->default('active');
            $t->timestamps();
        });

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->foreignId('role_id')->nullable();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->string('role')->nullable();
            $t->string('status')->default('active');
            $t->rememberToken();
            $t->timestamps();
        });

        Schema::create('projects', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->unsignedBigInteger('property_id')->nullable();
            $t->string('project_name');
            $t->string('project_code')->nullable();
            $t->string('status')->default('active');
            $t->timestamps();
        });

        Schema::create('property_masters', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->string('property_name');
            $t->string('property_code')->nullable();
            $t->decimal('purchase_price', 15, 2)->default(0);
            $t->decimal('paid_amount', 15, 2)->default(0);
            $t->decimal('due_amount', 15, 2)->default(0);
            $t->decimal('purchase_rate', 15, 2)->nullable();
            $t->decimal('total_area', 15, 2)->nullable();
            $t->string('status')->default('active');
            $t->timestamps();
        });

        Schema::create('project_property_master', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('project_id');
            $t->unsignedBigInteger('property_master_id');
            $t->timestamps();
        });

        Schema::create('properties', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->unsignedBigInteger('property_master_id')->nullable();
            $t->unsignedBigInteger('project_id')->nullable();
            $t->string('property_name');
            $t->string('property_code')->nullable();
            $t->string('unit_no')->nullable();
            $t->decimal('size', 12, 2)->nullable();
            $t->decimal('price', 15, 2)->default(0);
            $t->decimal('purchase_rate', 15, 2)->nullable();
            $t->string('status')->default('available');
            $t->timestamps();
        });

        Schema::create('customers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->string('name');
            $t->string('mobile')->nullable();
            $t->string('status')->default('active');
            $t->timestamps();
        });

        Schema::create('payment_modes', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('status')->default('active');
            $t->timestamps();
        });

        Schema::create('bookings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->foreignId('property_id')->nullable();
            $t->foreignId('customer_id')->nullable();
            $t->date('booking_date')->nullable();
            $t->decimal('total_amount', 15, 2)->default(0);
            $t->decimal('final_amount', 15, 2)->default(0);
            $t->decimal('booking_amount', 15, 2)->default(0);
            $t->decimal('remaining_amount', 15, 2)->default(0);
            $t->string('status')->default('confirmed');
            $t->string('payment_status')->default('pending');
            $t->timestamps();
        });

        Schema::create('booking_property', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('booking_id');
            $t->unsignedBigInteger('property_id');
            $t->timestamps();
        });

        Schema::create('property_sales', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->foreignId('property_id')->nullable();
            $t->foreignId('customer_id')->nullable();
            $t->date('sale_date')->nullable();
            $t->decimal('sale_amount', 15, 2)->default(0);
            $t->decimal('purchase_cost', 15, 2)->default(0);
            $t->decimal('property_expenses', 15, 2)->default(0);
            $t->decimal('booking_amount', 15, 2)->default(0);
            $t->decimal('remaining_amount', 15, 2)->default(0);
            $t->decimal('broker_commission_amount', 15, 2)->default(0);
            $t->string('payment_status')->default('pending');
            $t->string('sale_status')->default('sold');
            $t->timestamps();
        });

        Schema::create('property_sale_property', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('property_sale_id');
            $t->unsignedBigInteger('property_id');
            $t->timestamps();
        });

        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->foreignId('property_sale_id')->nullable();
            $t->foreignId('customer_id')->nullable();
            $t->foreignId('property_id')->nullable();
            $t->decimal('total_amount', 15, 2)->default(0);
            $t->decimal('paid_amount', 15, 2)->default(0);
            $t->decimal('pending_amount', 15, 2)->default(0);
            $t->decimal('payment_amount', 15, 2)->default(0);
            $t->string('payment_mode')->nullable();
            $t->date('payment_date')->nullable();
            $t->string('status')->default('received');
            $t->timestamps();
        });

        Schema::create('expenses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->foreignId('project_id')->nullable();
            $t->foreignId('property_id')->nullable();
            $t->date('expense_date')->nullable();
            $t->string('expense_type')->nullable();
            $t->string('expense_title')->nullable();
            $t->decimal('amount', 15, 2)->default(0);
            $t->string('approval_status')->default('Approved');
            $t->timestamps();
        });

        Schema::create('expense_property', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('expense_id');
            $t->unsignedBigInteger('property_id');
            $t->timestamps();
        });

        Schema::create('contractor_payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->foreignId('project_id')->nullable();
            $t->foreignId('property_id')->nullable();
            $t->decimal('amount', 15, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('property_master_payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('property_master_id')->nullable();
            $t->foreignId('firm_id')->nullable();
            $t->decimal('amount', 15, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('broker_commissions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->foreignId('property_id')->nullable();
            $t->foreignId('booking_id')->nullable();
            $t->foreignId('property_sale_id')->nullable();
            $t->decimal('commission_amount', 15, 2)->default(0);
            $t->string('payment_status')->default('pending');
            $t->timestamps();
        });

        Schema::create('rentals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->foreignId('property_id')->nullable();
            $t->timestamps();
        });

        Schema::create('rental_property', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('rental_id');
            $t->unsignedBigInteger('property_id');
            $t->timestamps();
        });

        Schema::create('rental_payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->foreignId('rental_id')->nullable();
            $t->foreignId('property_id')->nullable();
            $t->decimal('paid_amount', 15, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('incomes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->foreignId('property_id')->nullable();
            $t->decimal('amount', 15, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('stock_inwards', function (Blueprint $t) {
            $t->id();
            $t->foreignId('firm_id')->nullable();
            $t->foreignId('project_id')->nullable();
            $t->unsignedBigInteger('purchase_order_id')->nullable();
            $t->decimal('total_amount', 15, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('user_name')->nullable();
            $t->string('user_role')->nullable();
            $t->string('module_name')->nullable();
            $t->string('action_type')->nullable();
            $t->text('description')->nullable();
            $t->string('ip_address')->nullable();
            $t->timestamps();
        });
    }

    public function test_partners_module_is_admin_only(): void
    {
        $this->withSession(['login_type' => 'firm', 'firm_id' => $this->firm->id])
            ->get(route('partners.index'))
            ->assertStatus(403);

        $this->flushSession();
        $this->actingAs($this->staff)
            ->withSession(['login_type' => 'admin'])
            ->get(route('partners.index'))
            ->assertStatus(403);

        $this->flushSession();
        $this->actingAs($this->admin)
            ->withSession(['login_type' => 'admin'])
            ->get(route('partners.index'))
            ->assertStatus(200);
    }

    public function test_partner_crud_filters_and_cross_partner_guards(): void
    {
        $this->actingAs($this->admin)->withSession(['login_type' => 'admin']);

        $this->get(route('partners.create'))->assertStatus(200);

        $p1 = Partner::create([
            'firm_id'   => $this->firm->id,
            'name'      => 'Silent Alpha',
            'mobile'    => '9000011111',
            'is_silent' => true,
            'status'    => 'active',
        ]);
        $p2 = Partner::create([
            'firm_id'   => $this->firm->id,
            'name'      => 'Paper Beta',
            'mobile'    => '9000022222',
            'is_silent' => false,
            'status'    => 'inactive',
        ]);

        $this->get(route('partners.index', ['partner_type' => 'silent']))
            ->assertStatus(200)
            ->assertSee('Silent Alpha')
            ->assertDontSee('Paper Beta');

        $this->get(route('partners.index', ['search' => 'Beta', 'status' => 'inactive']))
            ->assertStatus(200)
            ->assertSee('Paper Beta')
            ->assertDontSee('Silent Alpha');

        $this->get(route('partners.edit', $p1->id))->assertStatus(200);

        $this->put(route('partners.update', $p1->id), [
            'firm_id'   => $this->firm->id,
            'name'      => 'Silent Alpha Updated',
            'mobile'    => '9000011111',
            'is_silent' => 1,
            'status'    => 'active',
            'notes'     => 'Updated private note',
        ])->assertRedirect(route('partners.show', $p1->id));

        $this->assertDatabaseHas('partners', [
            'id'   => $p1->id,
            'name' => 'Silent Alpha Updated',
        ]);

        $share = PartnerShare::create([
            'firm_id'          => $this->firm->id,
            'partner_id'       => $p1->id,
            'scope_type'       => PartnerShare::SCOPE_FIRM,
            'share_percentage' => 20,
            'status'           => 'active',
        ]);

        // Update share percentage
        $this->put(route('partners.shares.update', [$p1->id, $share->id]), [
            'firm_id'          => $this->firm->id,
            'scope_type'       => PartnerShare::SCOPE_FIRM,
            'share_percentage' => 30,
            'status'           => 'active',
        ])->assertRedirect(route('partners.show', $p1->id));

        $this->assertEquals(30.00, (float) $share->fresh()->share_percentage);

        // Cross-partner share/payout ownership mismatch returns 404
        $this->delete(route('partners.shares.destroy', [$p2->id, $share->id]))
            ->assertStatus(404);

        $this->delete(route('partners.destroy', $p2->id))
            ->assertRedirect(route('partners.index'));
        $this->assertDatabaseMissing('partners', ['id' => $p2->id]);
    }

    public function test_silent_partner_creation_share_cap_and_profit_payout_flow(): void
    {
        $customer = Customer::create([
            'firm_id' => $this->firm->id,
            'name'    => 'Buyer One',
            'mobile'  => '9999988888',
            'status'  => 'active',
        ]);

        $project = Project::create([
            'firm_id'      => $this->firm->id,
            'project_name' => 'Green Valley',
            'project_code' => 'PRJ-001',
            'status'       => 'active',
        ]);

        $plot = Property::create([
            'firm_id'       => $this->firm->id,
            'project_id'    => $project->id,
            'property_name' => 'Plot 1',
            'property_code' => 'GV-01',
            'unit_no'       => '1',
            'price'         => 1000000,
            'purchase_rate' => 400000,
            'status'        => 'sold',
        ]);

        $sale = PropertySale::create([
            'firm_id'          => $this->firm->id,
            'property_id'      => $plot->id,
            'customer_id'      => $customer->id,
            'sale_date'        => '2026-11-01',
            'sale_amount'      => 1000000,
            'purchase_cost'    => 400000,
            'booking_amount'   => 600000,
            'remaining_amount' => 400000,
            'payment_status'   => 'partial',
            'sale_status'      => 'sold',
        ]);

        Payment::create([
            'firm_id'          => $this->firm->id,
            'property_sale_id' => $sale->id,
            'customer_id'      => $customer->id,
            'property_id'      => $plot->id,
            'total_amount'     => 1000000,
            'paid_amount'      => 600000,
            'pending_amount'   => 400000,
            'payment_amount'   => 600000,
            'payment_mode'     => 'Bank Transfer',
            'payment_date'     => '2026-11-01',
            'status'           => 'received',
        ]);

        Expense::create([
            'firm_id'         => $this->firm->id,
            'project_id'      => $project->id,
            'expense_date'    => '2026-11-05',
            'expense_type'    => 'Project',
            'expense_title'   => 'Road Leveling',
            'amount'          => 100000,
            'approval_status' => 'Approved',
        ]);

        $this->actingAs($this->admin)
            ->withSession(['login_type' => 'admin'])
            ->post(route('partners.store'), [
                'firm_id'                  => $this->firm->id,
                'name'                     => 'Silent Partner A',
                'mobile'                   => '9876500000',
                'is_silent'                => 1,
                'status'                   => 'active',
                'initial_scope_type'       => 'project',
                'initial_project_id'       => $project->id,
                'initial_share_percentage' => 25,
            ])
            ->assertRedirect();

        $partner = Partner::where('name', 'Silent Partner A')->firstOrFail();
        $share = PartnerShare::where('partner_id', $partner->id)->firstOrFail();

        $service = app(PartnerProfitService::class);
        $metrics = $service->calculateForShare($share);
        $this->assertEquals(500000.00, $metrics['realized_profit']);
        $this->assertEquals(125000.00, $metrics['realized_entitled']);
        $this->assertEquals(125000.00, $metrics['balance_due']);

        // Exceeding 100% on same project is rejected
        $partner2 = Partner::create(['name' => 'Silent Partner B', 'is_silent' => true, 'status' => 'active']);
        $this->actingAs($this->admin)
            ->withSession(['login_type' => 'admin'])
            ->post(route('partners.shares.store', $partner2->id), [
                'firm_id'          => $this->firm->id,
                'scope_type'       => 'project',
                'project_id'       => $project->id,
                'share_percentage' => 80,
            ])
            ->assertSessionHas('error');

        $mode = PaymentMode::create(['name' => 'UPI', 'status' => 'active']);
        $this->actingAs($this->admin)
            ->withSession(['login_type' => 'admin'])
            ->post(route('partners.payouts.store', $partner->id), [
                'partner_share_id' => $share->id,
                'payout_date'      => '2026-11-10',
                'amount'           => 50000,
                'payment_mode_id'  => $mode->id,
                'reference_no'     => 'UTR-12345',
                'remarks'          => 'First profit installment',
            ])
            ->assertRedirect(route('partners.show', $partner->id));

        // Overdraw test: pay another 1,00,000 (total 1,50,000 vs 1,25,000 entitled -> 25,000 overdrawn)
        $this->actingAs($this->admin)
            ->withSession(['login_type' => 'admin'])
            ->post(route('partners.payouts.store', $partner->id), [
                'partner_share_id' => $share->id,
                'payout_date'      => '2026-11-12',
                'amount'           => 100000,
                'payment_mode_id'  => $mode->id,
            ])
            ->assertRedirect(route('partners.show', $partner->id));

        $overdrawnMetrics = $service->calculateForShare($share);
        $this->assertEquals(150000.00, $overdrawnMetrics['paid_amount']);
        $this->assertEquals(0.00, $overdrawnMetrics['balance_due']);
        $this->assertEquals(25000.00, $overdrawnMetrics['overdrawn_amount']);

        // Delete second payout & verify balance returns to 75,000
        $secondPayout = PartnerPayout::where('partner_id', $partner->id)->latest('id')->firstOrFail();
        $this->delete(route('partners.payouts.destroy', [$partner->id, $secondPayout->id]))
            ->assertRedirect(route('partners.show', $partner->id));

        $afterMetrics = $service->calculateForShare($share);
        $this->assertEquals(50000.00, $afterMetrics['paid_amount']);
        $this->assertEquals(75000.00, $afterMetrics['balance_due']);
        $this->assertEquals(1, Expense::count());

        $payout = PartnerPayout::firstOrFail();
        $this->get(route('partners.show', $partner->id))->assertStatus(200);
        $this->get(route('partner-payouts.index', ['partner_id' => $partner->id, 'firm_id' => $this->firm->id]))->assertStatus(200);
        $this->get(route('partners.detail-pdf', $partner->id))->assertStatus(200);
        $this->get(route('partners.payouts.receipt-pdf', [$partner->id, $payout->id]))->assertStatus(200);
    }

    public function test_firm_property_master_individual_property_and_loss_calculations(): void
    {
        $customer = Customer::create(['firm_id' => $this->firm->id, 'name' => 'C1', 'mobile' => '9876543211', 'status' => 'active']);

        $pm = PropertyMaster::create([
            'firm_id'        => $this->firm->id,
            'property_name'  => 'Sunrise Land',
            'property_code'  => 'SL-01',
            'purchase_price' => 500000,
            'paid_amount'    => 300000,
            'due_amount'     => 200000,
            'status'         => 'active',
        ]);

        PropertyMasterPayment::create([
            'property_master_id' => $pm->id,
            'firm_id'            => $this->firm->id,
            'amount'             => 300000,
        ]);

        $unitA = Property::create([
            'firm_id'            => $this->firm->id,
            'property_master_id' => $pm->id,
            'property_name'      => 'Sunrise Plot A',
            'property_code'      => 'SL-A',
            'unit_no'            => 'A',
            'price'              => 800000,
            'status'             => 'available',
        ]);

        $unitB = Property::create([
            'firm_id'            => $this->firm->id,
            'property_master_id' => $pm->id,
            'property_name'      => 'Sunrise Plot B',
            'property_code'      => 'SL-B',
            'unit_no'            => 'B',
            'price'              => 800000,
            'status'             => 'sold',
        ]);

        // Unsold unit A has a booking advance of 4,50,000
        Booking::create([
            'firm_id'          => $this->firm->id,
            'property_id'      => $unitA->id,
            'customer_id'      => $customer->id,
            'booking_date'     => '2026-11-02',
            'total_amount'     => 800000,
            'final_amount'     => 800000,
            'booking_amount'   => 450000,
            'remaining_amount' => 350000,
            'status'           => 'confirmed',
            'payment_status'   => 'partial',
        ]);

        // Unit B sold for 7,00,000 with 5,00,000 collected; also has rental 50,000 & income 20,000
        $saleB = PropertySale::create([
            'firm_id'        => $this->firm->id,
            'property_id'    => $unitB->id,
            'customer_id'    => $customer->id,
            'sale_date'      => '2026-11-04',
            'sale_amount'    => 700000,
            'purchase_cost'  => 250000,
            'booking_amount' => 500000,
            'sale_status'    => 'sold',
        ]);

        RentalPayment::create([
            'firm_id'     => $this->firm->id,
            'property_id' => $unitB->id,
            'paid_amount' => 50000,
        ]);

        Income::create([
            'firm_id'     => $this->firm->id,
            'property_id' => $unitB->id,
            'amount'      => 20000,
        ]);

        ContractorPayment::create([
            'firm_id'     => $this->firm->id,
            'property_id' => $unitB->id,
            'amount'      => 30000,
        ]);

        BrokerCommission::create([
            'firm_id'           => $this->firm->id,
            'property_id'       => $unitB->id,
            'property_sale_id'  => $saleB->id,
            'commission_amount' => 10000,
            'payment_status'    => 'paid',
        ]);

        StockInward::create([
            'firm_id'           => $this->firm->id,
            'purchase_order_id' => null,
            'total_amount'      => 40000,
        ]);

        $partner = Partner::create(['firm_id' => $this->firm->id, 'name' => 'Firm & Unit Partner', 'is_silent' => true, 'status' => 'active']);

        // 1. PropertyMaster share (40%):
        // Inflow = 450k (booking A) + 500k (sale B) + 50k (rental) + 20k (income) = 10,20,000
        // Outflow = 300k (land paid) + 30k (contractor) + 10k (commission paid) = 3,40,000
        // Realized Profit = 6,80,000 -> 40% = 2,72,000
        $pmShare = PartnerShare::create([
            'firm_id'            => $this->firm->id,
            'partner_id'         => $partner->id,
            'scope_type'         => PartnerShare::SCOPE_PROPERTY_MASTER,
            'property_master_id' => $pm->id,
            'share_percentage'   => 40,
            'status'             => 'active',
        ]);

        // 2. Individual Property share on Unit B (50%):
        // Unit B effective purchase cost = 500k / 2 plots = 250k
        // Inflow = 500k + 50k + 20k = 5,70,000; Outflow = 250k + 30k + 10k = 2,90,000
        // Realized Profit = 2,80,000 -> 50% = 1,40,000
        $unitShare = PartnerShare::create([
            'firm_id'          => $this->firm->id,
            'partner_id'       => $partner->id,
            'scope_type'       => PartnerShare::SCOPE_PROPERTY,
            'property_id'      => $unitB->id,
            'share_percentage' => 50,
            'status'           => 'active',
        ]);

        // 3. Firm share (10%):
        // Inflow = 10,20,000; Outflow = 3,40,000 + 40,000 (direct stock inward) = 3,80,000
        // Realized Profit = 6,40,000 -> 10% = 64,000
        $firmShare = PartnerShare::create([
            'firm_id'          => $this->firm->id,
            'partner_id'       => $partner->id,
            'scope_type'       => PartnerShare::SCOPE_FIRM,
            'share_percentage' => 10,
            'status'           => 'active',
        ]);

        // 4. Loss-making unit (Unit A with huge expense -> negative realized profit => 0 entitled)
        Expense::create([
            'firm_id'     => $this->firm->id,
            'property_id' => $unitA->id,
            'amount'      => 900000,
        ]);
        $lossShare = PartnerShare::create([
            'firm_id'          => $this->firm->id,
            'partner_id'       => $partner->id,
            'scope_type'       => PartnerShare::SCOPE_PROPERTY,
            'property_id'      => $unitA->id,
            'share_percentage' => 25,
            'status'           => 'active',
        ]);

        $service = app(PartnerProfitService::class);

        $pmMetrics   = $service->calculateForShare($pmShare);
        $unitMetrics = $service->calculateForShare($unitShare);
        $firmMetrics = $service->calculateForShare($firmShare);
        $lossMetrics = $service->calculateForShare($lossShare);

        $this->assertEquals(280000.00, $unitMetrics['realized_profit']);
        $this->assertEquals(140000.00, $unitMetrics['realized_entitled']);

        // PM includes Unit A's 9,00,000 expense now -> 6,80,000 - 9,00,000 = -2,20,000 -> entitled = 0
        $this->assertEquals(-220000.00, $pmMetrics['realized_profit']);
        $this->assertEquals(0.00, $pmMetrics['realized_entitled']);

        // Firm includes Unit A's 9,00,000 expense now -> 6,40,000 - 9,00,000 = -2,60,000 -> entitled = 0
        $this->assertEquals(-260000.00, $firmMetrics['realized_profit']);
        $this->assertEquals(0.00, $firmMetrics['realized_entitled']);

        // Loss unit entitled = 0
        $this->assertLessThan(0, $lossMetrics['realized_profit']);
        $this->assertEquals(0.00, $lossMetrics['realized_entitled']);
        $this->assertEquals(0.00, $lossMetrics['balance_due']);
    }

    public function test_validation_rules_for_shares_and_payouts(): void
    {
        $this->actingAs($this->admin)->withSession(['login_type' => 'admin']);

        $partner = Partner::create([
            'firm_id'   => $this->firm->id,
            'name'      => 'Validation Partner',
            'is_silent' => true,
            'status'    => 'active',
        ]);

        // Project scope without project_id fails validation
        $this->post(route('partners.shares.store', $partner->id), [
            'firm_id'          => $this->firm->id,
            'scope_type'       => 'project',
            'share_percentage' => 15,
        ])->assertSessionHasErrors('project_id');

        // Share > 100% fails validation
        $this->post(route('partners.shares.store', $partner->id), [
            'firm_id'          => $this->firm->id,
            'scope_type'       => 'firm',
            'share_percentage' => 120,
        ])->assertSessionHasErrors('share_percentage');

        $share = PartnerShare::create([
            'firm_id'          => $this->firm->id,
            'partner_id'       => $partner->id,
            'scope_type'       => PartnerShare::SCOPE_FIRM,
            'share_percentage' => 15,
            'status'           => 'active',
        ]);

        // Payout amount <= 0 fails validation
        $this->post(route('partners.payouts.store', $partner->id), [
            'partner_share_id' => $share->id,
            'payout_date'      => '2026-11-10',
            'amount'           => 0,
        ])->assertSessionHasErrors('amount');
    }
}
