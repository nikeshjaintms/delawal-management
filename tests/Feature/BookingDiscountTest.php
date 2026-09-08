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
        $this->admin = User::first() ?? User::factory()->create(['is_admin' => 1]);
        $this->firm = Firm::first() ?? Firm::create(['firm_name' => 'QA Firm', 'status' => 'active']);
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
}
