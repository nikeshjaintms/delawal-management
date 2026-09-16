<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Firm;
use App\Models\Role;
use App\Models\Property;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\PaymentMode;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MultiPropertyBookingTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $firm;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);
        $this->firm = Firm::firstOrCreate(
            ['firm_name' => 'Multi Property QA Firm'],
            ['email' => 'multipropqa@example.com', 'mobile' => '9876543210', 'status' => 'active']
        );
        $this->admin = User::first() ?? User::create([
            'name'     => 'Multi Booking Tester',
            'email'    => 'multibooktester@example.com',
            'password' => bcrypt('password'),
            'role_id'  => $role->id,
            'firm_id'  => $this->firm->id,
            'status'   => 'active'
        ]);
    }

    /** @test */
    public function it_creates_a_booking_with_more_than_two_properties_and_syncs_status()
    {
        $customer = Customer::create([
            'firm_id' => $this->firm->id,
            'name'    => 'Nehal Multi Client',
            'mobile'  => '9876543299',
            'status'  => 'active'
        ]);

        // Create 3 plots (more than 2)
        $p1 = Property::create([
            'firm_id'       => $this->firm->id,
            'property_name' => 'Plot 10',
            'unit_no'       => '10',
            'property_code' => 'PLT-10',
            'price'         => 50000.00,
            'status'        => 'available'
        ]);
        $p2 = Property::create([
            'firm_id'       => $this->firm->id,
            'property_name' => 'Plot 11',
            'unit_no'       => '11',
            'property_code' => 'PLT-11',
            'price'         => 55000.00,
            'status'        => 'available'
        ]);
        $p3 = Property::create([
            'firm_id'       => $this->firm->id,
            'property_name' => 'Plot 12',
            'unit_no'       => '12',
            'property_code' => 'PLT-12',
            'price'         => 60000.00,
            'status'        => 'available'
        ]);

        $pm = PaymentMode::firstOrCreate(['name' => 'NEFT'], ['status' => 'active']);

        $postData = [
            'firm_id'          => $this->firm->id,
            'property_ids'     => [$p1->id, $p2->id, $p3->id],
            'customer_id'      => $customer->id,
            'booking_type'     => 'booking',
            'booking_date'     => date('Y-m-d'),
            'total_amount'     => 165000.00,
            'discount_type'    => 'percentage',
            'discount_value'   => 0,
            'discount_amount'  => 0,
            'final_amount'     => 165000.00,
            'booking_amount'   => 50000.00,
            'remaining_amount' => 115000.00,
            'payment_mode_id'  => $pm->id,
            'payment_mode'     => $pm->name,
            'status'           => 'confirmed',
            'payment_status'   => 'partial',
        ];

        // 1. Submit store request
        $response = $this->actingAs($this->admin)
            ->withSession(['firm_id' => $this->firm->id])
            ->post(route('bookings.store'), $postData);

        $response->assertRedirect(route('bookings.index'));

        // 2. Verify booking created and linked to all 3 properties
        $booking = Booking::where('customer_id', $customer->id)->latest()->first();
        $this->assertNotNull($booking);
        $this->assertEquals(3, $booking->properties()->count());
        $this->assertTrue($booking->properties->contains('id', $p1->id));
        $this->assertTrue($booking->properties->contains('id', $p2->id));
        $this->assertTrue($booking->properties->contains('id', $p3->id));

        // 3. Verify all 3 properties marked as booked in DB
        $this->assertEquals('booked', $p1->fresh()->status);
        $this->assertEquals('booked', $p2->fresh()->status);
        $this->assertEquals('booked', $p3->fresh()->status);

        // 4. Verify show page displays all 3 properties
        $showResponse = $this->actingAs($this->admin)->get(route('bookings.show', $booking->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Plot 10');
        $showResponse->assertSee('Plot 11');
        $showResponse->assertSee('Plot 12');
        $showResponse->assertSee('3 Units');

        // 5. Update booking: remove Plot 12, keep Plot 10 and Plot 11
        $updateData = array_merge($postData, [
            'property_ids' => [$p1->id, $p2->id],
            'total_amount' => 105000.00,
            'final_amount' => 105000.00,
            'remaining_amount' => 55000.00,
        ]);

        $updateResponse = $this->actingAs($this->admin)
            ->withSession(['firm_id' => $this->firm->id])
            ->put(route('bookings.update', $booking->id), $updateData);

        $updateResponse->assertRedirect(route('bookings.index'));

        // Plot 10 and Plot 11 remain booked, Plot 12 reverted to available
        $this->assertEquals('booked', $p1->fresh()->status);
        $this->assertEquals('booked', $p2->fresh()->status);
        $this->assertEquals('available', $p3->fresh()->status);

        // 6. Delete booking: reverts remaining booked properties to available
        $delResponse = $this->actingAs($this->admin)
            ->withSession(['firm_id' => $this->firm->id])
            ->delete(route('bookings.destroy', $booking->id));

        $delResponse->assertRedirect(route('bookings.index'));

        $this->assertEquals('available', $p1->fresh()->status);
        $this->assertEquals('available', $p2->fresh()->status);
        $this->assertEquals('available', $p3->fresh()->status);
    }
}
