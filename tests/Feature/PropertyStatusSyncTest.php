<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Firm;
use App\Models\Property;
use App\Models\PropertyMaster;
use App\Models\Project;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\PropertySale;
use App\Models\Rental;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PropertyStatusSyncTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $firm;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);
        $this->firm = Firm::firstOrCreate(
            ['firm_name' => 'Status Sync QA Firm'],
            ['email' => 'syncqa@example.com', 'mobile' => '9876543999', 'status' => 'active']
        );
        $this->admin = User::first() ?? User::create([
            'name'     => 'Sync Tester',
            'email'    => 'synctester@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $role->id,
            'firm_id'  => $this->firm->id,
            'status'   => 'active'
        ]);
    }

    /** @test */
    public function it_synchronizes_property_status_to_booked_when_active_booking_exists()
    {
        // 1. Create a plot under project with status available
        $project = Project::create([
            'firm_id'      => $this->firm->id,
            'project_name' => 'Sync Test Aman Park',
            'project_code' => 'SYNC-AMAN-01',
            'status'       => 'active',
        ]);

        $prop = Property::create([
            'firm_id'       => $this->firm->id,
            'project_id'    => $project->id,
            'property_name' => 'House No A19',
            'property_code' => 'SYNC-A19',
            'status'        => 'available', // starts as available in DB
            'price'         => 4800000.00,
        ]);

        $customer = Customer::create([
            'firm_id' => $this->firm->id,
            'name'    => 'Iqbal Deva',
            'mobile'  => '9876511111',
            'status'  => 'active'
        ]);

        // 2. Insert booking directly simulating existing live booking created previously
        $booking = Booking::create([
            'firm_id'          => $this->firm->id,
            'property_id'      => $prop->id,
            'customer_id'      => $customer->id,
            'booking_type'     => 'booking',
            'booking_date'     => '2026-09-14',
            'total_amount'     => 4800000.00,
            'final_amount'     => 4800000.00,
            'booking_amount'   => 4800000.00,
            'remaining_amount' => 0.00,
            'status'           => 'pending', // pending booking
            'payment_status'   => 'paid',
        ]);

        // Before sync, property in DB is available
        $this->assertEquals('available', $prop->fresh()->status);

        // 3. Run sync
        Property::syncAllStatuses();

        // 4. Verify property status is now 'booked'
        $this->assertEquals('booked', $prop->fresh()->status);

        // 5. Test project show view displays Booked badge and customer name
        $response = $this->actingAs($this->admin)->get(route('projects.show', $project->id));
        $response->assertStatus(200);
        $response->assertSee('House No A19');
        $response->assertSee('Booked');
        $response->assertSee('Iqbal Deva');
    }

    /** @test */
    public function it_runs_artisan_properties_sync_status_command()
    {
        $this->artisan('properties:sync-status')
            ->expectsOutput('Synchronizing property statuses from bookings, sales, and rentals...')
            ->expectsOutput('✓ All property statuses synchronized successfully!')
            ->assertExitCode(0);
    }
}
