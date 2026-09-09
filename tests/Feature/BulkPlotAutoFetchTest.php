<?php

namespace Tests\Feature;

use App\Models\Firm;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyMaster;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BulkPlotAutoFetchTest extends TestCase
{
    use DatabaseTransactions;
    protected $user;
    protected $firm;
    protected $propertyMaster;
    protected $project;
    protected $propertyType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->firm = Firm::firstOrCreate(
            ['firm_name' => 'Delawala Properties'],
            ['status' => 'active', 'email' => 'info@delawala.com', 'mobile' => '9876543210']
        );

        $this->user = User::firstOrCreate(
            ['email' => 'admin@delawala.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'is_admin' => 1,
                'role_id' => 1,
                'status' => 'active',
                'firm_id' => $this->firm->id,
            ]
        );

        $this->propertyMaster = PropertyMaster::firstOrCreate(
            ['property_code' => 'PROP-TEST-001'],
            [
                'firm_id' => $this->firm->id,
                'property_name' => 'GALAXY HOMES MASTER',
                'city' => 'VAGRA',
                'location' => 'Highway Road',
                'address' => 'Galaxy Homes Sector 1, Vagra',
                'status' => 'active',
            ]
        );

        $this->project = Project::firstOrCreate(
            ['project_code' => 'PRJ-TEST-001'],
            [
                'firm_id' => $this->firm->id,
                'property_id' => $this->propertyMaster->id,
                'project_name' => 'GALAXY HOMES',
                'city' => 'VAGRA',
                'location' => 'Highway Road',
                'address' => 'Galaxy Homes Sector 1, Vagra',
                'status' => 'ongoing',
            ]
        );

        $this->propertyType = PropertyType::firstOrCreate(
            ['name' => 'Plot'],
            ['status' => 'active']
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fetches_master_and_project_info_via_api()
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('properties.master-info', ['project_id' => $this->project->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'firm_id' => $this->firm->id,
                'property_master_id' => $this->propertyMaster->id,
                'project_id' => $this->project->id,
                'project_name' => 'GALAXY HOMES',
                'city' => 'VAGRA',
            ]
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_a_bulk_plot_and_automatically_links_property_master_id()
    {
        $plotCode = 'P-TEST-' . uniqid();

        $response = $this->actingAs($this->user)
            ->post(route('properties.store'), [
                'firm_id' => $this->firm->id,
                'project_id' => $this->project->id,
                'property_type_id' => $this->propertyType->id,
                'property_code' => $plotCode,
                'property_name' => 'House No A1',
                'unit_no' => 'A1',
                'city' => 'VAGRA',
                'location' => 'Highway Road',
                'address' => 'Galaxy Homes Sector 1, Vagra',
                'size' => '1255',
                'size_unit' => 'sq.ft',
                'price' => 3000000,
                'status' => 'available',
            ]);

        $response->assertRedirect(route('properties.index'));

        $this->assertDatabaseHas('properties', [
            'property_code' => $plotCode,
            'property_master_id' => $this->propertyMaster->id,
            'project_id' => $this->project->id,
            'property_name' => 'House No A1',
            'city' => 'VAGRA',
            'price' => 3000000,
        ]);
    }
}
