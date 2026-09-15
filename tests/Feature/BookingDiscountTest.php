<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Firm;
use App\Models\Property;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\PaymentMode;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookingDiscountTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $firm;

    protected function setUp(): void
    {
        parent::setUp();
        $role = \App\Models\Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);
        $this->firm = Firm::firstOrCreate(
            ['firm_name' => 'QA Firm'],
            ['email' => 'qafirm@example.com', 'mobile' => '9876500001', 'status' => 'active']
        );
        $this->admin = User::first() ?? User::create([
            'name' => 'QA Admin',
            'email' => 'qaadmin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'firm_id' => $this->firm->id,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function test_booking_creation_with_discount_and_payment_mode()
    {
        $customer = Customer::create([
            'firm_id' => $this->firm->id,
            'name'    => 'Booking Test Customer',
            'mobile'  => '9876500000',
            'status'  => 'active'
        ]);

        $property = Property::create([
            'firm_id'       => $this->firm->id,
            'property_name' => 'Discount Flat 101',
            'status'        => 'available',
            'price'         => 1000000.00
        ]);

        $pm = PaymentMode::firstOrCreate(['name' => 'Bank Transfer'], ['status' => 'active']);

        $data = [
            'firm_id'          => $this->firm->id,
            'property_id'      => $property->id,
            'customer_id'      => $customer->id,
            'booking_date'     => date('Y-m-d'),
            'total_amount'     => 1000000.00,
            'discount_type'    => 'percentage',
            'discount_value'   => 10.00, // 10%
            'discount_amount'  => 100000.00,
            'final_amount'     => 900000.00,
            'booking_amount'   => 200000.00,
            'remaining_amount' => 700000.00,
            'payment_mode_id'  => $pm->id,
            'payment_mode'     => $pm->name,
            'transaction_ref'  => 'TXN-998877',
            'status'           => 'confirmed',
            'payment_status'   => 'partial',
            'remarks'          => '10% promotional discount given'
        ];

        $response = $this->actingAs($this->admin)
            ->withSession(['firm_id' => $this->firm->id])
            ->post(route('bookings.store'), $data);

        $response->assertRedirect(route('bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'property_id'      => $property->id,
            'customer_id'      => $customer->id,
            'total_amount'     => 1000000.00,
            'discount_type'    => 'percentage',
            'discount_value'   => 10.00,
            'discount_amount'  => 100000.00,
            'final_amount'     => 900000.00,
            'booking_amount'   => 200000.00,
            'remaining_amount' => 700000.00,
            'payment_mode_id'  => $pm->id,
            'payment_mode'     => 'Bank Transfer',
            'transaction_ref'  => 'TXN-998877',
            'payment_status'   => 'partial',
            'status'           => 'confirmed'
        ]);
    }

    /** @test */
    public function test_project_grouping_vs_standalone_properties_in_booking()
    {
        // 1. Create 3 Property Masters
        $pm1 = \App\Models\PropertyMaster::create([
            'firm_id' => $this->firm->id,
            'property_name' => 'Aman Park Phase 1',
            'property_code' => 'TEST-PM-01',
            'total_area' => 5000,
            'purchase_price' => 500000,
            'status' => 'active',
        ]);
        $pm2 = \App\Models\PropertyMaster::create([
            'firm_id' => $this->firm->id,
            'property_name' => 'Aman Park Phase 2',
            'property_code' => 'TEST-PM-02',
            'total_area' => 6000,
            'purchase_price' => 600000,
            'status' => 'active',
        ]);
        $pm3 = \App\Models\PropertyMaster::create([
            'firm_id' => $this->firm->id,
            'property_name' => 'Aman Park Phase 3',
            'property_code' => 'TEST-PM-03',
            'total_area' => 7000,
            'purchase_price' => 700000,
            'status' => 'active',
        ]);

        // Standalone Property Master (no project)
        $standalonePm = \App\Models\PropertyMaster::create([
            'firm_id' => $this->firm->id,
            'property_name' => 'Shivam Farm Standalone',
            'property_code' => 'TEST-PM-04',
            'total_area' => 3000,
            'purchase_price' => 300000,
            'status' => 'active',
        ]);

        // 2. Create Project and group the 3 Property Masters
        $project = \App\Models\Project::create([
            'firm_id' => $this->firm->id,
            'project_name' => 'Aman Park Project Group',
            'project_code' => 'TEST-PRJ-01',
            'status' => 'active',
        ]);
        $project->syncPropertyMasters([$pm1->id, $pm2->id, $pm3->id]);

        // 3. Request create booking page and verify standalonePropertyMasters vs projects
        $response = $this->actingAs($this->admin)
            ->withSession(['firm_id' => $this->firm->id])
            ->get(route('bookings.create'));

        $response->assertStatus(200);

        $standaloneList = $response->viewData('standalonePropertyMasters');
        $this->assertTrue($standaloneList->contains('id', $standalonePm->id));
        $this->assertFalse($standaloneList->contains('id', $pm1->id));
        $this->assertFalse($standaloneList->contains('id', $pm2->id));
        $this->assertFalse($standaloneList->contains('id', $pm3->id));

        $projectList = $response->viewData('projects');
        $this->assertTrue($projectList->contains('id', $project->id));
    }
}
