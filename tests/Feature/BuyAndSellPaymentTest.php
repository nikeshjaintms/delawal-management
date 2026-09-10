<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Firm;
use App\Models\Role;
use App\Models\Purchase;
use App\Models\PropertySale;
use App\Models\Property;
use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BuyAndSellPaymentTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $firm;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);
        $this->firm = Firm::firstOrCreate(
            ['firm_name' => 'Delawala BuySell Test Firm'],
            ['status' => 'active', 'email' => 'buysell_firm@delawala.com', 'mobile' => '9898989898']
        );
        $this->user = User::firstOrCreate(
            ['email' => 'buysell_admin@delawala.com'],
            ['name' => 'BuySell Admin', 'password' => bcrypt('password'), 'role_id' => $role->id, 'firm_id' => $this->firm->id, 'status' => 'active']
        );
    }

    protected function tearDown(): void
    {
        Vendor::where('name', 'like', '%Test%')->delete();
        Customer::where('name', 'like', '%Test%')->delete();
        Purchase::where('property_code', 'BUY-99')->delete();
        PropertySale::where('note', 'like', '%Test%')->delete();
        Firm::where('firm_name', 'like', '%Test%')->delete();
        User::where('email', 'like', '%buysell%')->delete();
        parent::tearDown();
    }

    /** @test */
    public function it_creates_and_updates_property_buy_with_partial_payment_and_auto_due_calculation()
    {
        $vendor = Vendor::firstOrCreate(
            ['name' => 'Test Vendor Buy'],
            ['mobile' => '9898989898', 'firm_id' => $this->firm->id, 'status' => 'active']
        );

        // 1. Create with partial payment: 30,00,000 purchase price, 10,00,000 paid -> 20,00,000 due, status: partial
        $response = $this->actingAs($this->user)->post(route('property-masters.store'), [
            'firm_id'         => $this->firm->id,
            'vendor_id'       => $vendor->id,
            'property_name'   => 'Buy Test Land Plot #99',
            'property_code'   => 'BUY-99',
            'property_type'   => 'Residential Plot',
            'purchase_date'   => '2026-09-09',
            'purchase_price'  => 3000000.00,
            'paid_amount'     => 1000000.00,
            'total_area'      => 5000,
            'area_unit'       => 'Sq.Ft',
            'status'          => 'active',
        ]);
        $response->assertRedirect();

        $pm = \App\Models\PropertyMaster::where('property_code', 'BUY-99')->first();
        $this->assertNotNull($pm);
        $this->assertEquals(3000000.00, (float)$pm->purchase_price);
        $this->assertEquals(1000000.00, (float)$pm->paid_amount);
        $this->assertEquals(2000000.00, (float)$pm->due_amount);
        $this->assertEquals('partial', $pm->payment_status);
        $this->assertEquals(33.3, $pm->paid_percentage);

        // Test show view contains paid and due
        $showRes = $this->actingAs($this->user)->get(route('property-masters.show', $pm->id));
        $showRes->assertStatus(200);
        $showRes->assertSee('3,000,000.00');
        $showRes->assertSee('1,000,000.00');
        $showRes->assertSee('2,000,000.00');

        // 2. Update to full paid: 30,00,000 paid -> 0 due, status: paid
        $updateRes = $this->actingAs($this->user)->put(route('property-masters.update', $pm->id), [
            'firm_id'         => $this->firm->id,
            'vendor_id'       => $vendor->id,
            'property_name'   => 'Buy Test Land Plot #99 (Updated)',
            'property_code'   => 'BUY-99',
            'property_type'   => 'Residential Plot',
            'purchase_date'   => '2026-09-09',
            'purchase_price'  => 3000000.00,
            'paid_amount'     => 3000000.00,
            'total_area'      => 5000,
            'area_unit'       => 'Sq.Ft',
            'status'          => 'active',
        ]);
        $updateRes->assertRedirect();

        $pm->refresh();
        $this->assertEquals(3000000.00, (float)$pm->paid_amount);
        $this->assertEquals(0.00, (float)$pm->due_amount);
        $this->assertEquals('paid', $pm->payment_status);
        $this->assertEquals(100.0, $pm->paid_percentage);
    }

    /** @test */
    public function it_creates_and_updates_property_sale_with_partial_payment_and_auto_due_calculation()
    {
        $property = Property::firstOrCreate(
            ['property_code' => 'P-SELL-TEST-88'],
            ['firm_id' => $this->firm->id, 'property_name' => 'Sell Test Plot #88', 'price' => 4500000.00, 'status' => 'available']
        );

        $customer = Customer::firstOrCreate(
            ['name' => 'Test Customer Sell'],
            ['mobile' => '9797979797', 'firm_id' => $this->firm->id, 'status' => 'active']
        );

        // 1. Create with partial payment: 45,00,000 sale amount, 15,00,000 booking/paid -> 30,00,000 remaining, status: partial
        $response = $this->actingAs($this->user)->post(route('property-sales.store'), [
            'firm_id'          => $this->firm->id,
            'property_id'      => $property->id,
            'customer_id'      => $customer->id,
            'sale_date'        => '2026-09-09',
            'sale_amount'      => 4500000.00,
            'booking_amount'   => 1500000.00,
            'payment_status'   => 'partial',
            'sale_status'      => 'booked',
        ]);
        $response->assertRedirect();

        $sale = PropertySale::where('property_id', $property->id)->first();
        $this->assertNotNull($sale);
        $this->assertEquals(4500000.00, (float)$sale->sale_amount);
        $this->assertEquals(1500000.00, (float)$sale->booking_amount);
        $this->assertEquals(3000000.00, (float)$sale->remaining_amount);
        $this->assertEquals('partial', $sale->payment_status);
        $this->assertEquals(33.3, $sale->paid_percentage);

        // Test show view contains sale and booking amounts
        $showRes = $this->actingAs($this->user)->get(route('property-sales.show', $sale->id));
        $showRes->assertStatus(200);
        $showRes->assertSee('4,500,000.00');
        $showRes->assertSee('1,500,000.00');
        $showRes->assertSee('3,000,000.00');

        // 2. Update to full paid: 45,00,000 paid -> 0 remaining, status: paid
        $updateRes = $this->actingAs($this->user)->put(route('property-sales.update', $sale->id), [
            'firm_id'          => $this->firm->id,
            'property_id'      => $property->id,
            'customer_id'      => $customer->id,
            'sale_date'        => '2026-09-09',
            'sale_amount'      => 4500000.00,
            'booking_amount'   => 4500000.00,
            'payment_status'   => 'paid',
            'sale_status'      => 'sold',
        ]);
        $updateRes->assertRedirect();

        $sale->refresh();
        $this->assertEquals(4500000.00, (float)$sale->booking_amount);
        $this->assertEquals(0.00, (float)$sale->remaining_amount);
        $this->assertEquals('paid', $sale->payment_status);
        $this->assertEquals(100.0, $sale->paid_percentage);
    }
}
