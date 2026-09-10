<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Firm;
use App\Models\Role;
use App\Models\PropertyMaster;
use App\Models\Property;
use App\Models\Project;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PropertyMasterPurchasePriceTest extends TestCase
{
    use DatabaseTransactions;

    protected function tearDown(): void
    {
        Vendor::where('name', 'like', '%Test%')->delete();
        PropertyMaster::where('property_name', 'like', '%Test%')->orWhere('property_name', 'like', '%Delawala Royal Land%')->delete();
        Project::where('project_name', 'like', '%Test%')->orWhere('project_name', 'like', '%Delawala Grand Enclave%')->delete();
        parent::tearDown();
    }

    /** @test */
    public function it_creates_property_master_with_purchase_price_and_financial_details()
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);
        $firm = Firm::firstOrCreate(['firm_name' => 'Delawala Properties'], ['status' => 'active']);
        $user = User::firstOrCreate(
            ['email' => 'admin_test_pm@delawala.com'],
            ['name' => 'Admin Tester', 'password' => bcrypt('password'), 'role_id' => $role->id, 'firm_id' => $firm->id, 'status' => 'active']
        );

        $vendor = Vendor::firstOrCreate(
            ['name' => 'Test Landlord Vendor'],
            ['mobile' => '9876543210', 'firm_id' => $firm->id, 'status' => 'active']
        );

        $response = $this->actingAs($user)->post(route('property-masters.store'), [
            'firm_id'        => $firm->id,
            'property_name'  => 'Delawala Royal Land #101',
            'property_code'  => 'PROP-ROYAL-101',
            'status'         => 'active',
            'purchase_price' => 7500000.00,
            'purchase_date'  => '2026-09-01',
            'purchase_rate'  => 1500.00,
            'total_area'     => 5000.00,
            'area_unit'      => 'Sq.Ft',
            'vendor_id'      => $vendor->id,
            'payment_mode'   => 'Bank Transfer / RTGS / NEFT',
            'city'           => 'Surat',
            'location'       => 'Vesu',
        ]);

        $response->assertRedirect();

        $pm = PropertyMaster::where('property_name', 'Delawala Royal Land #101')->first();
        $this->assertNotNull($pm);
        $this->assertEquals(7500000.00, (float)$pm->purchase_price);
        $this->assertEquals('2026-09-01', $pm->purchase_date ? $pm->purchase_date->format('Y-m-d') : null);
        $this->assertEquals(1500.00, (float)$pm->purchase_rate);
        $this->assertEquals(5000.00, (float)$pm->total_area);
        $this->assertEquals($vendor->id, $pm->vendor_id);

        // Test show view contains purchase price
        $showRes = $this->actingAs($user)->get(route('property-masters.show', $pm->id));
        $showRes->assertStatus(200);
        $showRes->assertSee('7,500,000.00');
        $showRes->assertSee('Test Landlord Vendor');
    }

    /** @test */
    public function it_calculates_partial_payment_and_due_amount_automatically()
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);
        $firm = Firm::firstOrCreate(['firm_name' => 'Delawala Properties'], ['status' => 'active']);
        $user = User::firstOrCreate(
            ['email' => 'admin_test_pm@delawala.com'],
            ['name' => 'Admin Tester', 'password' => bcrypt('password'), 'role_id' => $role->id, 'firm_id' => $firm->id, 'status' => 'active']
        );

        // 1. Partial payment: 50,00,000 purchase price, 20,00,000 paid -> 30,00,000 due, status: partial
        $res = $this->actingAs($user)->post(route('property-masters.store'), [
            'firm_id'        => $firm->id,
            'property_name'  => 'Partial Payment Land #202',
            'property_code'  => 'PROP-PARTIAL-202',
            'status'         => 'active',
            'purchase_price' => 5000000.00,
            'paid_amount'    => 2000000.00,
            'payment_status' => 'partial',
        ]);
        $res->assertRedirect();

        $pm = PropertyMaster::where('property_code', 'PROP-PARTIAL-202')->first();
        $this->assertNotNull($pm);
        $this->assertEquals(5000000.00, (float)$pm->purchase_price);
        $this->assertEquals(2000000.00, (float)$pm->paid_amount);
        $this->assertEquals(3000000.00, (float)$pm->due_amount);
        $this->assertEquals('partial', $pm->payment_status);
        $this->assertEquals(40.0, $pm->paid_percentage);

        // 2. Update to full paid: 50,00,000 paid -> 0 due, status: paid
        $updateRes = $this->actingAs($user)->put(route('property-masters.update', $pm->id), [
            'firm_id'        => $firm->id,
            'property_name'  => 'Partial Payment Land #202 (Updated)',
            'property_code'  => 'PROP-PARTIAL-202',
            'status'         => 'active',
            'purchase_price' => 5000000.00,
            'paid_amount'    => 5000000.00,
        ]);
        $updateRes->assertRedirect();

        $pm->refresh();
        $this->assertEquals(5000000.00, (float)$pm->paid_amount);
        $this->assertEquals(0.00, (float)$pm->due_amount);
        $this->assertEquals('paid', $pm->payment_status);
        $this->assertEquals(100.0, $pm->paid_percentage);
    }

    /** @test */
    public function it_adds_single_plot_and_bulk_generates_plots_directly_under_property_master()
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);
        $firm = Firm::firstOrCreate(['firm_name' => 'Delawala Properties'], ['status' => 'active']);
        $user = User::firstOrCreate(
            ['email' => 'admin_test_pm@delawala.com'],
            ['name' => 'Admin Tester', 'password' => bcrypt('password'), 'role_id' => $role->id, 'firm_id' => $firm->id, 'status' => 'active']
        );

        $pm = PropertyMaster::firstOrCreate(
            ['property_name' => 'Master Direct Plots Test'],
            ['firm_id' => $firm->id, 'status' => 'active', 'property_code' => 'PROP-DIRECT-TEST', 'purchase_rate' => 1200.00]
        );

        // 1. Add single plot
        $singleRes = $this->actingAs($user)->post(route('property-masters.add-plot', $pm->id), [
            'property_name' => 'Plot A1',
            'unit_no'       => '1',
            'size'          => 1500,
            'size_unit'     => 'sq.ft',
            'purchase_rate' => 1200,
            'price'         => 1800,
            'status'        => 'available',
        ]);
        $singleRes->assertRedirect(route('property-masters.show', $pm->id));

        $this->assertDatabaseHas('properties', [
            'property_master_id' => $pm->id,
            'property_name'      => 'Plot A1',
            'unit_no'            => '1',
            'size'               => 1500,
        ]);

        // 2. Bulk generate 5 plots
        $bulkRes = $this->actingAs($user)->post(route('property-masters.bulk-generate-plots', $pm->id), [
            'total_plots'   => 5,
            'plot_prefix'   => 'Plot ',
            'start_number'  => 2,
            'size'          => 1200,
            'purchase_rate' => 1200,
            'price'         => 1600,
        ]);
        $bulkRes->assertRedirect(route('property-masters.show', $pm->id));

        $this->assertEquals(6, Property::where('property_master_id', $pm->id)->count());
    }

    /** @test */
    public function it_creates_a_project_linking_multiple_property_masters_and_assigning_plots()
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);
        $firm = Firm::firstOrCreate(['firm_name' => 'Delawala Properties'], ['status' => 'active']);
        $user = User::firstOrCreate(
            ['email' => 'admin_test_pm@delawala.com'],
            ['name' => 'Admin Tester', 'password' => bcrypt('password'), 'role_id' => $role->id, 'firm_id' => $firm->id, 'status' => 'active']
        );

        $pm1 = PropertyMaster::create([
            'firm_id'       => $firm->id,
            'property_name' => 'Land Part A',
            'property_code' => 'PROP-A',
            'status'        => 'active',
        ]);

        $pm2 = PropertyMaster::create([
            'firm_id'       => $firm->id,
            'property_name' => 'Land Part B',
            'property_code' => 'PROP-B',
            'status'        => 'active',
        ]);

        $plot1 = Property::create([
            'firm_id'            => $firm->id,
            'property_master_id' => $pm1->id,
            'property_name'      => 'Plot A-1',
            'property_code'      => 'P-A-1',
            'status'             => 'available',
        ]);

        $plot2 = Property::create([
            'firm_id'            => $firm->id,
            'property_master_id' => $pm2->id,
            'property_name'      => 'Plot B-1',
            'property_code'      => 'P-B-1',
            'status'             => 'available',
        ]);

        // Create Project selecting both Property Masters and both plots
        $response = $this->actingAs($user)->post(route('projects.store'), [
            'firm_id'           => $firm->id,
            'property_ids'      => [$pm1->id, $pm2->id],
            'project_name'      => 'Grand Combined Township',
            'project_code'      => 'PRJ-GRAND-01',
            'project_type'      => 'Plotted Development',
            'status'            => 'active',
            'selected_plot_ids' => [$plot1->id, $plot2->id],
        ]);

        $response->assertRedirect();

        $project = Project::where('project_name', 'Grand Combined Township')->first();
        $this->assertNotNull($project);
        $this->assertEquals(2, $project->propertyMasters()->count());
        $this->assertTrue($project->propertyMasters->contains('id', $pm1->id));
        $this->assertTrue($project->propertyMasters->contains('id', $pm2->id));

        $this->assertEquals(2, $project->properties()->count());
        $this->assertEquals($project->id, $plot1->fresh()->project_id);
        $this->assertEquals($project->id, $plot2->fresh()->project_id);
    }
}
