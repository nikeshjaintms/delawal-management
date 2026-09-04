<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Firm;
use App\Models\PropertyMaster;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Customer;
use App\Models\Broker;
use App\Models\Vendor;
use App\Models\Tenant;
use App\Models\PaymentMode;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\Booking;
use App\Models\PropertySale;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\RentalPayment;
use App\Models\Material;
use App\Models\PurchaseOrder;
use App\Models\StockInward;
use App\Models\StockOutward;
use App\Models\Income;
use App\Models\Receipt;
use App\Models\Loan;
use App\Models\LoanEmiSchedule;
use App\Models\Ledger;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SystemQualityAuditTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $firm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::first() ?? User::factory()->create(['is_admin' => 1]);
        $this->firm = Firm::first() ?? Firm::create(['firm_name' => 'QA Firm', 'status' => 'active']);
    }

    /** @test */
    public function test_01_all_main_get_routes_render_cleanly()
    {
        $firmRoutes = [
            'dashboard',
            'customers.index',
            'customers.create',
            'brokers.index',
            'brokers.create',
            'broker-commissions.index',
            'vendors.index',
            'vendors.create',
            'tenants.index',
            'tenants.create',
            'property-types.index',
            'payment-modes.index',
            'expense-categories.index',
            'property-masters.index',
            'property-masters.create',
            'projects.index',
            'projects.create',
            'properties.index',
            'properties.create',
            'property-sales.index',
            'property-sales.create',
            'property-documents.index',
            'property-availability.index',
            'payments.index',
            'payments.create',
            'rentals.index',
            'rentals.create',
            'bookings.index',
            'bookings.create',
            'purchases.index',
            'purchases.create',
            'purchase-orders.index',
            'purchase-orders.create',
            'expenses.index',
            'expenses.create',
            'materials.index',
            'materials.create',
            'stock-inwards.index',
            'stock-inwards.create',
            'stock-outwards.index',
            'stock-outwards.create',
            'stock-report.index',
            'incomes.index',
            'incomes.create',
            'receipts.index',
            'receipts.create',
            'expense-report.index',
            'loans.index',
            'loans.create',
            'emi-schedules.index',
            'loan-report.index',
            'ledgers.index',
            'credit-notes.index',
            'debit-notes.index',
            'reports.index',
            'reports.sales',
            'reports.payments',
            'reports.rentals',
            'reports.inventory',
            'reports.profit-loss',
            'reports.balance-sheet',
            'reports.cash-flow',
            'reports.gst-sales',
            'reports.gst-purchase',
            'reports.credit-note',
            'reports.debit-note',
            'forms.index',
            'forms.create',
            'invoice-settings.index',
        ];

        foreach ($firmRoutes as $route) {
            $response = $this->actingAs($this->admin)->withSession(['firm_id' => $this->firm->id])->get(route($route));
            $this->assertTrue(in_array($response->status(), [200, 302]), "Firm Route '{$route}' failed with status: " . $response->status());
        }

        $adminOnlyRoutes = [
            'users.index',
            'users.create',
            'roles.index',
            'roles.create',
            'audit-logs.index',
            'backups.index',
            'firm-master.index',
            'financial-years.index',
        ];

        foreach ($adminOnlyRoutes as $route) {
            $response = $this->actingAs($this->admin)->withSession(['firm_id' => null, 'login_type' => 'admin'])->get(route($route));
            $this->assertTrue(in_array($response->status(), [200, 302]), "Admin Route '{$route}' failed with status: " . $response->status());
        }
    }

    /** @test */
    public function test_02_property_master_project_and_property_hierarchy()
    {
        $pm = PropertyMaster::create([
            'firm_id' => $this->firm->id,
            'property_name' => 'QA Master Scheme ' . uniqid(),
            'survey_number' => '101/A',
            'status' => 'active'
        ]);
        $this->assertNotNull($pm->id);

        $project = Project::create([
            'firm_id' => $this->firm->id,
            'property_id' => $pm->id,
            'project_code' => 'PRJ-' . uniqid(),
            'project_name' => 'QA Project ' . uniqid(),
            'status' => 'active'
        ]);
        $project->load('propertyMaster');
        $this->assertEquals($pm->id, $project->property_id);

        $property = Property::create([
            'firm_id' => $this->firm->id,
            'project_id' => $project->id,
            'property_name' => 'QA Unit ' . uniqid(),
            'unit_no' => 'A-101',
            'status' => 'available',
            'base_price' => 2500000
        ]);
        $this->assertEquals($project->id, $property->project->id);
    }

    /** @test */
    public function test_03_booking_and_payment_flow()
    {
        $customer = Customer::create([
            'firm_id' => $this->firm->id,
            'name' => 'QA Customer ' . uniqid(),
            'mobile' => '9876543210',
            'status' => 'active'
        ]);

        $property = Property::create([
            'firm_id' => $this->firm->id,
            'property_name' => 'Booking Unit ' . uniqid(),
            'status' => 'available',
            'base_price' => 3000000
        ]);

        $booking = Booking::create([
            'firm_id' => $this->firm->id,
            'customer_id' => $customer->id,
            'property_id' => $property->id,
            'booking_date' => date('Y-m-d'),
            'total_amount' => 3000000,
            'booking_amount' => 150000,
            'status' => 'confirmed'
        ]);
        $this->assertNotNull($booking->id);

        $sale = PropertySale::create([
            'firm_id' => $this->firm->id,
            'customer_id' => $customer->id,
            'property_id' => $property->id,
            'sale_date' => date('Y-m-d'),
            'sale_price' => 3000000,
            'paid_amount' => 150000,
            'status' => 'active'
        ]);
        $this->assertNotNull($sale->id);

        $payment = Payment::create([
            'firm_id' => $this->firm->id,
            'property_id' => $property->id,
            'property_sale_id' => $sale->id,
            'customer_id' => $customer->id,
            'payment_date' => date('Y-m-d'),
            'amount' => 50000,
            'status' => 'completed'
        ]);
        $this->assertEquals($sale->id, $payment->propertySale->id);
    }

    /** @test */
    public function test_04_expense_creation_with_project_and_direct_property()
    {
        $project = Project::create([
            'firm_id' => $this->firm->id,
            'project_code' => 'EXP-PRJ-' . uniqid(),
            'project_name' => 'Expense Project ' . uniqid(),
            'status' => 'active'
        ]);

        $property = Property::create([
            'firm_id' => $this->firm->id,
            'property_name' => 'Direct Property ' . uniqid(),
            'status' => 'available'
        ]);

        $expense = Expense::create([
            'firm_id' => $this->firm->id,
            'project_id' => $project->id,
            'property_id' => $property->id,
            'expense_title' => 'QA Construction Materials',
            'expense_date' => date('Y-m-d'),
            'amount' => 12500,
            'approval_status' => 'Approved'
        ]);
        $this->assertNotNull($expense->id);
        $this->assertEquals($project->id, $expense->project->id);
        $this->assertEquals($property->id, $expense->property->id);
    }

    /** @test */
    public function test_05_rental_and_tenant_with_agreements()
    {
        $tenant = Tenant::create([
            'firm_id' => $this->firm->id,
            'name' => 'QA Rental Tenant ' . uniqid(),
            'mobile' => '9988776655',
            'occupation' => 'Business',
            'status' => 'active'
        ]);

        $property = Property::create([
            'firm_id' => $this->firm->id,
            'property_name' => 'Lease Unit ' . uniqid(),
            'status' => 'available'
        ]);

        $rental = Rental::create([
            'firm_id' => $this->firm->id,
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'tenant_mobile' => $tenant->mobile,
            'property_id' => $property->id,
            'agreement_no' => 'AGR-2026-' . uniqid(),
            'rent_amount' => 20000,
            'deposit_amount' => 40000,
            'maintenance_amount' => 1500,
            'rent_start_date' => date('Y-m-d'),
            'status' => 'active'
        ]);
        $this->assertNotNull($rental->id);
        $this->assertEquals($tenant->id, $rental->tenant->id);

        $rentalPayment = RentalPayment::create([
            'firm_id' => $this->firm->id,
            'rental_id' => $rental->id,
            'property_id' => $property->id,
            'payment_date' => date('Y-m-d'),
            'payment_month' => (int)date('m'),
            'payment_year' => (int)date('Y'),
            'rent_amount' => 20000,
            'paid_amount' => 20000,
            'pending_amount' => 0,
            'payment_status' => 'paid'
        ]);
        $this->assertEquals($rental->id, $rentalPayment->rental->id);
    }

    /** @test */
    public function test_06_income_and_receipt_creation()
    {
        $property = Property::create([
            'firm_id' => $this->firm->id,
            'property_name' => 'Income Unit ' . uniqid(),
            'status' => 'available'
        ]);

        $income = Income::create([
            'firm_id' => $this->firm->id,
            'property_id' => $property->id,
            'income_date' => date('Y-m-d'),
            'income_type' => 'Property Sale',
            'amount' => 75000,
            'status' => 'active'
        ]);
        $this->assertNotNull($income->id);

        $receipt = Receipt::create([
            'firm_id' => $this->firm->id,
            'receipt_no' => 'RCP-TEST-' . uniqid(),
            'receipt_date' => date('Y-m-d'),
            'received_from' => 'QA Customer',
            'amount' => 75000,
            'status' => 'active'
        ]);
        $this->assertNotNull($receipt->id);
    }

    /** @test */
    public function test_07_loan_and_emi_schedule()
    {
        $loan = Loan::create([
            'firm_id' => $this->firm->id,
            'loan_type' => 'given',
            'borrower_name' => 'QA Borrower ' . uniqid(),
            'loan_amount' => 500000,
            'interest_rate' => 10,
            'tenure_months' => 12,
            'loan_start_date' => date('Y-m-d'),
            'status' => 'active'
        ]);
        $this->assertNotNull($loan->id);

        $emi = LoanEmiSchedule::create([
            'firm_id' => $this->firm->id,
            'loan_id' => $loan->id,
            'emi_year' => (int)date('Y'),
            'emi_month' => (int)date('m'),
            'emi_date' => date('Y-m-d', strtotime('+1 month')),
            'emi_amount' => 44166.67,
            'paid_amount' => 0,
            'pending_amount' => 44166.67,
            'emi_status' => 'pending'
        ]);
        $this->assertEquals($loan->id, $emi->loan->id);
    }

    /** @test */
    public function test_08_pdf_generation_endpoints()
    {
        $pdfRoutes = [
            'expense-report.pdf',
            'stock-report.pdf',
            'loan-report.pdf',
            'reports.profit-loss.pdf',
            'reports.sales.pdf',
            'reports.payments.pdf',
            'reports.rentals.pdf',
            'reports.inventory.pdf',
            'reports.gst-sales.pdf',
            'reports.gst-purchase.pdf',
            'reports.credit-note.pdf',
            'reports.debit-note.pdf',
        ];

        foreach ($pdfRoutes as $route) {
            $response = $this->actingAs($this->admin)->withSession(['firm_id' => $this->firm->id])->get(route($route));
            $this->assertEquals(200, $response->status(), "PDF route '{$route}' failed with status: " . $response->status());
        }
    }

    /** @test */
    public function test_09_financial_year_creation_and_activation()
    {
        $uniqueName = 'FY-' . rand(1000, 9999) . '-' . rand(1000, 9999);
        $response = $this->actingAs($this->admin)->post(route('financial-years.store'), [
            'year_name' => $uniqueName,
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('financial-years.index'));
        $this->assertDatabaseHas('financial_years', [
            'year_name' => $uniqueName,
            'is_active' => 1,
            'status' => 'active',
        ]);
    }
}
