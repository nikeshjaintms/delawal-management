<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Firm;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseVendorDropdownTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Firm $firm;
    protected Project $project;
    protected Vendor $vendor1;
    protected Vendor $vendor2;
    protected ExpenseCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['guard_name' => 'web', 'description' => 'Super Administrator']
        );

        $this->firm = Firm::firstOrCreate(
            ['firm_name' => 'Delawala Infrastructure Group'],
            ['email' => 'qa_expense_firm@delawala.com', 'mobile' => '9876543210', 'status' => 'active']
        );

        $this->user = User::firstOrCreate(
            ['email' => 'qa_expense_admin@delawala.com'],
            ['name' => 'QA Expense Admin', 'password' => bcrypt('password'), 'role' => 'admin', 'role_id' => $role->id, 'firm_id' => $this->firm->id, 'status' => 'active']
        );
        $this->user->firms()->syncWithoutDetaching([$this->firm->id]);

        $this->project = Project::create([
            'firm_id' => $this->firm->id,
            'project_name' => 'Delawala Skyline Heights',
            'project_code' => 'DSH-2026',
            'status' => 'active',
        ]);

        $this->vendor1 = Vendor::create([
            'firm_id' => $this->firm->id,
            'name' => 'UltraTech Concrete & Supply Ltd',
            'mobile' => '9876543210',
            'city' => 'Surat',
            'status' => 'active',
        ]);

        $this->vendor2 = Vendor::create([
            'firm_id' => $this->firm->id,
            'name' => 'Tata Steel & Wire Distributors',
            'mobile' => '9123456780',
            'city' => 'Navsari',
            'status' => 'active',
        ]);

        $this->category = ExpenseCategory::create([
            'firm_id' => $this->firm->id,
            'name' => 'Raw Materials & Concrete',
            'status' => 'active',
        ]);
    }

    public function test_create_expense_with_selected_vendor_dropdown()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('expenses.store'), [
            'firm_ids'            => [$this->firm->id],
            'project_id'          => $this->project->id,
            'expense_title'       => 'Cement Consignment Batch A',
            'expense_date'        => date('Y-m-d'),
            'expense_category_id' => $this->category->id,
            'vendor_id'           => $this->vendor1->id,
            'paid_to'             => '', // Should auto-fill from vendor name
            'amount'              => 85000.00,
            'payment_mode'        => 'Bank Transfer',
            'bill_no'             => 'BILL-UTC-101',
            'approval_status'     => 'Approved',
            'remarks'             => 'Direct batch procurement',
        ]);

        $response->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'expense_title'       => 'Cement Consignment Batch A',
            'vendor_id'           => $this->vendor1->id,
            'paid_to'             => 'UltraTech Concrete & Supply Ltd',
            'amount'              => 85000.00,
            'payment_mode'        => 'Bank Transfer',
            'approval_status'     => 'Approved',
        ]);
    }

    public function test_create_expense_with_custom_payee_name()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('expenses.store'), [
            'firm_ids'            => [$this->firm->id],
            'project_id'          => $this->project->id,
            'expense_title'       => 'Local Excavation Labor Charges',
            'expense_date'        => date('Y-m-d'),
            'expense_category_id' => $this->category->id,
            'vendor_id'           => '',
            'paid_to'             => 'Ramesh Bhai Contractor',
            'amount'              => 15000.00,
            'payment_mode'        => 'Cash',
            'approval_status'     => 'Approved',
        ]);

        $response->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'expense_title'       => 'Local Excavation Labor Charges',
            'vendor_id'           => null,
            'paid_to'             => 'Ramesh Bhai Contractor',
            'amount'              => 15000.00,
        ]);
    }

    public function test_update_expense_from_custom_payee_to_registered_vendor()
    {
        $this->actingAs($this->user);

        $expense = Expense::create([
            'firm_id'             => $this->firm->id,
            'project_id'          => $this->project->id,
            'expense_title'       => 'Steel Reinforcement Mesh',
            'expense_date'        => date('Y-m-d'),
            'expense_category_id' => $this->category->id,
            'vendor_id'           => null,
            'paid_to'             => 'Local Hardware Shop',
            'amount'              => 42000.00,
            'payment_mode'        => 'UPI',
            'approval_status'     => 'Pending',
        ]);

        $response = $this->put(route('expenses.update', $expense->id), [
            'firm_ids'            => [$this->firm->id],
            'project_id'          => $this->project->id,
            'expense_title'       => 'Steel Reinforcement Mesh - Final',
            'expense_date'        => date('Y-m-d'),
            'expense_category_id' => $this->category->id,
            'vendor_id'           => $this->vendor2->id,
            'paid_to'             => '',
            'amount'              => 42000.00,
            'payment_mode'        => 'UPI',
            'approval_status'     => 'Approved',
        ]);

        $response->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'id'              => $expense->id,
            'vendor_id'       => $this->vendor2->id,
            'paid_to'         => 'Tata Steel & Wire Distributors',
            'approval_status' => 'Approved',
        ]);
    }

    public function test_expense_index_and_show_render_vendor_link()
    {
        $this->actingAs($this->user);

        $expense = Expense::create([
            'firm_id'             => $this->firm->id,
            'project_id'          => $this->project->id,
            'expense_title'       => 'Bulk Cement Consignment',
            'expense_date'        => date('Y-m-d'),
            'expense_category_id' => $this->category->id,
            'vendor_id'           => $this->vendor1->id,
            'paid_to'             => $this->vendor1->name,
            'amount'              => 120000.00,
            'payment_mode'        => 'Bank Transfer',
            'approval_status'     => 'Approved',
        ]);

        // Index page should show vendor
        $indexResponse = $this->get(route('expenses.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($this->vendor1->name);

        // Show page should show vendor link
        $showResponse = $this->get(route('expenses.show', $expense->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($this->vendor1->name);
        $showResponse->assertSee(route('vendors.show', $this->vendor1->id));

        // Filter by vendor
        $filterResponse = $this->get(route('expenses.index', ['filter_vendor' => $this->vendor1->id]));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Bulk Cement Consignment');
    }
}
