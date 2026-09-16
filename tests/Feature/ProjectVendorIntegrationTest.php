<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Firm;
use App\Models\Role;
use App\Models\Project;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProjectVendorIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected Firm $firm;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['name' => 'Admin'],
            ['display_name' => 'Administrator']
        );

        $this->firm = Firm::firstOrCreate(
            ['email' => 'firm_test_pv@delawala.com'],
            [
                'firm_name' => 'Delawala Test Firm PV',
                'mobile'    => '9999999999',
                'status'    => 'active',
            ]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_pv@delawala.com'],
            [
                'name'      => 'Test Admin PV',
                'password'  => bcrypt('password'),
                'firm_id'   => $this->firm->id,
                'role_id'   => $role->id,
                'role'      => 'admin',
                'status'    => 'active',
            ]
        );

        $this->project = Project::create([
            'firm_id'      => $this->firm->id,
            'project_name' => 'Skyline Heights',
            'project_code' => 'PRJ-SKY',
            'project_type' => 'Commercial',
            'status'       => 'active',
        ]);
    }

    public function test_vendor_can_be_created_with_specific_project(): void
    {
        $response = $this->actingAs($this->admin)->post(route('vendors.store'), [
            'firm_id'       => $this->firm->id,
            'project_id'    => $this->project->id,
            'name'          => 'Apex Steel & Cement',
            'mobile'        => '9876543210',
            'email'         => 'apex@supplier.com',
            'city'          => 'Ahmedabad',
            'payment_terms' => '30 Days Net',
            'status'        => 'active',
        ]);

        $response->assertRedirect(route('vendors.index'));

        $this->assertDatabaseHas('vendors', [
            'name'       => 'Apex Steel & Cement',
            'project_id' => $this->project->id,
            'mobile'     => '9876543210',
        ]);

        $vendor = Vendor::where('name', 'Apex Steel & Cement')->first();
        $this->assertNotNull($vendor);
        $this->assertEquals($this->project->id, $vendor->project->id);
        $this->assertEquals('Skyline Heights', $vendor->project->project_name);
    }

    public function test_vendor_can_be_created_as_general_vendor_without_project(): void
    {
        $response = $this->actingAs($this->admin)->post(route('vendors.store'), [
            'firm_id'       => $this->firm->id,
            'project_id'    => null,
            'name'          => 'Global Electricals',
            'mobile'        => '9876543211',
            'email'         => 'global@supplier.com',
            'city'          => 'Surat',
            'payment_terms' => 'Advance',
            'status'        => 'active',
        ]);

        $response->assertRedirect(route('vendors.index'));

        $this->assertDatabaseHas('vendors', [
            'name'       => 'Global Electricals',
            'project_id' => null,
        ]);

        $vendor = Vendor::where('name', 'Global Electricals')->first();
        $this->assertNull($vendor->project);
    }

    public function test_vendors_can_be_filtered_by_project_on_index(): void
    {
        $projectB = Project::create([
            'firm_id'      => $this->firm->id,
            'project_name' => 'Green Valley Residency',
            'project_code' => 'PRJ-GV',
            'status'       => 'active',
        ]);

        $v1 = Vendor::create([
            'firm_id'    => $this->firm->id,
            'project_id' => $this->project->id,
            'name'       => 'Skyline Vendor',
            'mobile'     => '9876500001',
            'status'     => 'active',
        ]);

        $v2 = Vendor::create([
            'firm_id'    => $this->firm->id,
            'project_id' => $projectB->id,
            'name'       => 'Green Valley Vendor',
            'mobile'     => '9876500002',
            'status'     => 'active',
        ]);

        $v3 = Vendor::create([
            'firm_id'    => $this->firm->id,
            'project_id' => null,
            'name'       => 'General Supplier All',
            'mobile'     => '9876500003',
            'status'     => 'active',
        ]);

        // Filter for project A
        $resA = $this->actingAs($this->admin)->get(route('vendors.index', ['project_id' => $this->project->id]));
        $resA->assertStatus(200);
        $resA->assertSee('Skyline Vendor');
        $resA->assertDontSee('Green Valley Vendor');

        // Filter for project B
        $resB = $this->actingAs($this->admin)->get(route('vendors.index', ['project_id' => $projectB->id]));
        $resB->assertStatus(200);
        $resB->assertSee('Green Valley Vendor');
        $resB->assertDontSee('Skyline Vendor');

        // Filter for general
        $resGen = $this->actingAs($this->admin)->get(route('vendors.index', ['project_id' => 'general']));
        $resGen->assertStatus(200);
        $resGen->assertSee('General Supplier All');
    }

    public function test_project_has_many_vendors_relationship(): void
    {
        $v1 = Vendor::create([
            'firm_id'    => $this->firm->id,
            'project_id' => $this->project->id,
            'name'       => 'Vendor One',
            'mobile'     => '9111111111',
            'status'     => 'active',
        ]);

        $v2 = Vendor::create([
            'firm_id'    => $this->firm->id,
            'project_id' => $this->project->id,
            'name'       => 'Vendor Two',
            'mobile'     => '9222222222',
            'status'     => 'active',
        ]);

        $this->assertEquals(2, $this->project->vendors()->count());
        $this->assertTrue($this->project->vendors->pluck('name')->contains('Vendor One'));
        $this->assertTrue($this->project->vendors->pluck('name')->contains('Vendor Two'));
    }
}
