<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Firm;
use App\Models\Role;
use App\Models\PropertyMaster;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PropertyMasterPurchasePriceTest extends TestCase
{
    use DatabaseTransactions;

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
        $this->assertEquals('2026-09-01', $pm->purchase_date);
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
    public function it_creates_acquisition_batch_even_with_unchecked_instant_generator_and_custom_rate()
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);
        $firm = Firm::firstOrCreate(['firm_name' => 'Delawala Properties'], ['status' => 'active']);
        $user = User::firstOrCreate(
            ['email' => 'admin_test_pm@delawala.com'],
            ['name' => 'Admin Tester', 'password' => bcrypt('password'), 'role_id' => $role->id, 'firm_id' => $firm->id, 'status' => 'active']
        );

        $pm = PropertyMaster::firstOrCreate(
            ['property_name' => 'Test Master for Batch'],
            ['firm_id' => $firm->id, 'status' => 'active', 'property_code' => 'PROP-BATCH-TEST']
        );

        $response = $this->actingAs($user)->post(route('acquisition-batches.store'), [
            'property_master_id'    => $pm->id,
            'firm_id'               => $firm->id,
            'batch_name'            => 'Batch 1',
            'purchase_date'         => '2026-09-09',
            'total_plots'           => 45,
            'purchase_rate'         => 1200.00,
            'rate_unit'             => 'per_plot',
            'total_purchase_amount' => 54000.00,
            'status'                => 'active',
            'generate_plots'        => '0',
            'plot_count'            => 0,
        ]);

        $response->assertRedirect(route('property-masters.show', $pm->id));
        $this->assertDatabaseHas('acquisition_batches', [
            'property_master_id'    => $pm->id,
            'purchase_rate'         => 1200.00,
            'total_purchase_amount' => 54000.00,
        ]);
    }

    /** @test */
    public function it_downloads_acquisition_batches_excel_template()
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);
        $firm = Firm::firstOrCreate(['firm_name' => 'Delawala Properties'], ['status' => 'active']);
        $user = User::firstOrCreate(
            ['email' => 'admin_test_pm@delawala.com'],
            ['name' => 'Admin Tester', 'password' => bcrypt('password'), 'role_id' => $role->id, 'firm_id' => $firm->id, 'status' => 'active']
        );

        $response = $this->actingAs($user)->get(route('acquisition-batches.download-template'));
        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
    }

    /** @test */
    public function it_deletes_project_and_unassigns_plots_to_inventory()
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);
        $firm = Firm::firstOrCreate(['firm_name' => 'Delawala Properties'], ['status' => 'active']);
        $user = User::firstOrCreate(
            ['email' => 'admin_test_pm@delawala.com'],
            ['name' => 'Admin Tester', 'password' => bcrypt('password'), 'role_id' => $role->id, 'firm_id' => $firm->id, 'status' => 'active']
        );

        $pm = PropertyMaster::firstOrCreate(
            ['property_name' => 'Delawala Land #DeleteTest'],
            ['firm_id' => $firm->id, 'status' => 'active', 'property_code' => 'PROP-DEL-TEST']
        );

        $project = \App\Models\Project::create([
            'firm_id'      => $firm->id,
            'property_id'  => $pm->id,
            'project_name' => 'Test Delete Project',
            'project_code' => 'PRJ-DEL-01',
            'project_type' => 'plots',
            'status'       => 'active',
        ]);

        $plot = \App\Models\Property::create([
            'firm_id'            => $firm->id,
            'property_master_id' => $pm->id,
            'project_id'         => $project->id,
            'property_name'      => 'Plot 999',
            'property_code'      => 'PLT-999',
            'status'             => 'available',
        ]);

        $contractor = \App\Models\Contractor::create([
            'firm_id'         => $firm->id,
            'project_id'      => $project->id,
            'contractor_name' => 'Test Contractor #1',
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->delete(route('projects.destroy', $project->id));
        $response->assertRedirect(route('property-masters.show', $pm->id));

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('contractors', ['id' => $contractor->id]);
        $plot->refresh();
        $this->assertNull($plot->project_id);
    }

    /** @test */
    public function it_accepts_excel_import_and_direct_generator_plot_sources()
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);
        $firm = Firm::firstOrCreate(['firm_name' => 'Delawala Properties'], ['status' => 'active']);
        $user = User::firstOrCreate(
            ['email' => 'admin_test_pm@delawala.com'],
            ['name' => 'Admin Tester', 'password' => bcrypt('password'), 'role_id' => $role->id, 'firm_id' => $firm->id, 'status' => 'active']
        );

        $pm = PropertyMaster::firstOrCreate(
            ['property_name' => 'Delawala Plot Source Test'],
            ['firm_id' => $firm->id, 'status' => 'active', 'property_code' => 'PROP-PST-01']
        );

        // Test Batch creation with plot_source = 'excel_import' and 'direct_generator'
        $response = $this->actingAs($user)->post(route('acquisition-batches.store'), [
            'property_master_id' => $pm->id,
            'batch_name'         => 'Batch Direct Gen',
            'purchase_date'      => '2026-09-09',
            'purchase_rate'      => 450,
            'rate_unit'          => 'per_sqyd',
            'plot_source'        => 'direct_generator',
            'plot_count'         => 3,
            'plot_prefix'        => 'P-DG-',
            'start_number'       => 1,
            'plot_size'          => 120,
            'plot_size_unit'     => 'Sq.Yd',
            'plot_facing'        => 'East',
            'status'             => 'active',
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $batch = \App\Models\AcquisitionBatch::where('property_master_id', $pm->id)->where('batch_name', 'Batch Direct Gen')->first();
        $this->assertNotNull($batch);

        // Test addPlots with plot_source = 'direct_generator'
        $response2 = $this->actingAs($user)->post(route('acquisition-batches.add-plots', $batch->id), [
            'plot_source'    => 'direct_generator',
            'plot_count'     => 2,
            'plot_prefix'    => 'P-DG-EXT-',
            'start_number'   => 1,
            'plot_size'      => 120,
            'plot_size_unit' => 'Sq.Yd',
            'plot_facing'    => 'West',
            'purchase_rate'  => 450,
        ]);
        $response2->assertSessionHasNoErrors();
    }

    /** @test */
    public function it_imports_plots_from_excel_with_various_size_formats()
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);
        $firm = Firm::firstOrCreate(['firm_name' => 'Delawala Properties'], ['status' => 'active']);
        $user = User::firstOrCreate(
            ['email' => 'admin_test_pm@delawala.com'],
            ['name' => 'Admin Tester', 'password' => bcrypt('password'), 'role_id' => $role->id, 'firm_id' => $firm->id, 'status' => 'active']
        );

        $pm = PropertyMaster::firstOrCreate(
            ['property_name' => 'Delawala Excel Import Size Test'],
            ['firm_id' => $firm->id, 'status' => 'active', 'property_code' => 'PROP-SZ-01', 'location' => 'Bharuch', 'city' => 'Bharuch']
        );

        $batch = \App\Models\AcquisitionBatch::create([
            'firm_id'            => $firm->id,
            'property_master_id' => $pm->id,
            'batch_name'         => 'Batch Excel Size Test',
            'batch_number'       => 'BATCH-SZ-001',
            'purchase_date'      => '2026-09-09',
            'purchase_rate'      => 1000,
            'rate_unit'          => 'per_plot',
            'total_plots'        => 0,
            'status'             => 'active',
        ]);

        // Create a CSV with various headers and size formats
        $csvContent = "Plot No,Plot Name,Size / Area,Size Unit,Facing,Purchase Rate\n";
        $csvContent .= "101,Plot 101,\"1,200 sq.ft\",sq.ft,East,1200\n";
        $csvContent .= "102,Plot 102,1500,sq.ft,West,1200\n";
        $csvContent .= "103,Plot 103,\"200 sq.yard\",sq.yard,North,1500\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'test_plots_') . '.csv';
        file_put_contents($tempFile, $csvContent);

        $uploadedFile = new \Illuminate\Http\UploadedFile($tempFile, 'plots.csv', 'text/csv', null, true);

        $response = $this->actingAs($user)->post(route('acquisition-batches.add-plots', $batch->id), [
            'plot_source' => 'excel',
            'excel_file'  => $uploadedFile,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        @unlink($tempFile);

        $p1 = \App\Models\Property::where('property_master_id', $pm->id)->where('unit_no', '101')->first();
        $this->assertNotNull($p1);
        $this->assertEquals('1,200 sq.ft', $p1->size);
        $this->assertEquals('East', $p1->facing);

        $p2 = \App\Models\Property::where('property_master_id', $pm->id)->where('unit_no', '102')->first();
        $this->assertNotNull($p2);
        $this->assertEquals('1500', $p2->size);

        $p3 = \App\Models\Property::where('property_master_id', $pm->id)->where('unit_no', '103')->first();
        $this->assertNotNull($p3);
        $this->assertEquals('200 sq.yard', $p3->size);
    }

    /** @test */
    public function it_imports_bulk_plots_with_exact_size_and_separate_project_field()
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);
        $firm = Firm::firstOrCreate(['firm_name' => 'Delawala Properties'], ['status' => 'active']);
        $user = User::firstOrCreate(
            ['email' => 'admin_test_pm@delawala.com'],
            ['name' => 'Admin Tester', 'password' => bcrypt('password'), 'role_id' => $role->id, 'firm_id' => $firm->id, 'status' => 'active']
        );

        $pm = PropertyMaster::firstOrCreate(
            ['property_name' => 'Delawala Galaxy Master'],
            ['firm_id' => $firm->id, 'status' => 'active', 'property_code' => 'PROP-GAL-01', 'location' => 'Vagra', 'city' => 'VAGRA']
        );

        $project = \App\Models\Project::firstOrCreate(
            ['project_name' => 'GALAXY HOMES', 'firm_id' => $firm->id],
            ['project_code' => 'PRJ-GAL-001', 'property_id' => $pm->id, 'project_type' => 'Plotted Development', 'status' => 'active']
        );

        $batch = \App\Models\AcquisitionBatch::create([
            'firm_id'            => $firm->id,
            'property_master_id' => $pm->id,
            'batch_name'         => 'Galaxy Batch 1',
            'batch_number'       => 'BATCH-GAL-001',
            'purchase_date'      => '2026-09-09',
            'purchase_rate'      => 3000000,
            'rate_unit'          => 'per_plot',
            'total_plots'        => 0,
            'status'             => 'active',
        ]);

        // Exact Excel layout reported by user
        $csvContent = "Firm,Code,Property Name,Project,City,Size,Price,Status,Image\n";
        $csvContent .= "Delawala Properties,P1,House No A1,GALAXY HOMES,VAGRA,\"1255 sq.ft Built Up\",3000000,AVAILABLE,plot-001.jpg\n";
        $csvContent .= "Delawala Properties,P2,House No A2,GALAXY HOMES,VAGRA,\"1255 sq.ft Built Up\",3000000,AVAILABLE,plot-002.jpg\n";
        $csvContent .= "Delawala Properties,P21,House No B1,GALAXY HOMES,VAGRA,\"530 sq.ft Built Up\",2000000,AVAILABLE,plot-021.jpg\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'test_galaxy_') . '.csv';
        file_put_contents($tempFile, $csvContent);

        $uploadedFile = new \Illuminate\Http\UploadedFile($tempFile, 'galaxy_plots.csv', 'text/csv', null, true);

        $response = $this->actingAs($user)->post(route('acquisition-batches.add-plots', $batch->id), [
            'plot_source' => 'excel',
            'excel_file'  => $uploadedFile,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        @unlink($tempFile);

        // Verify P1
        $p1 = \App\Models\Property::where('acquisition_batch_id', $batch->id)->where('property_code', 'P1')->first();
        $this->assertNotNull($p1, 'Plot P1 must exist');
        $this->assertEquals('House No A1', $p1->property_name);
        $this->assertEquals('P1', $p1->property_code);
        $this->assertEquals($project->id, $p1->project_id);
        $this->assertEquals('GALAXY HOMES', $p1->project->project_name);
        $this->assertEquals('1255 sq.ft Built Up', $p1->size);
        $this->assertEquals('1255 sq.ft Built Up', $p1->formatted_size);
        $this->assertNotEquals('GALAXY HOMES', $p1->size_unit);
        $this->assertStringNotContainsString('GALAXY HOMES', $p1->formatted_size);
        $this->assertEquals('3000000.00', $p1->price);
        $this->assertEquals('available', strtolower($p1->status));

        // Verify P2
        $p2 = \App\Models\Property::where('acquisition_batch_id', $batch->id)->where('property_code', 'P2')->first();
        $this->assertNotNull($p2, 'Plot P2 must exist');
        $this->assertEquals('House No A2', $p2->property_name);
        $this->assertEquals('P2', $p2->property_code);
        $this->assertEquals('1255 sq.ft Built Up', $p2->size);
        $this->assertEquals('1255 sq.ft Built Up', $p2->formatted_size);
        $this->assertStringNotContainsString('GALAXY HOMES', $p2->formatted_size);

        // Verify P21 (530 sq.ft Built Up)
        $p21 = \App\Models\Property::where('acquisition_batch_id', $batch->id)->where('property_code', 'P21')->first();
        $this->assertNotNull($p21, 'Plot P21 must exist');
        $this->assertEquals('House No B1', $p21->property_name);
        $this->assertEquals('P21', $p21->property_code);
        $this->assertEquals('530 sq.ft Built Up', $p21->size);
        $this->assertEquals('530 sq.ft Built Up', $p21->formatted_size);
        $this->assertStringNotContainsString('GALAXY HOMES', $p21->formatted_size);
    }
}



