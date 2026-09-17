<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Firm;
use App\Models\Role;
use App\Models\Customer;
use App\Models\Property;
use App\Models\PropertySale;
use App\Models\Payment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PropertySaleMultiPlotAndInstallmentPaymentTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected Firm $firm;
    protected Customer $customer;
    protected Property $plot1;
    protected Property $plot2;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['name' => 'Admin'],
            ['display_name' => 'Administrator']
        );

        $this->firm = Firm::firstOrCreate(
            ['email' => 'firm_test_ps@delawala.com'],
            [
                'firm_name' => 'Delawala Test Firm PS',
                'mobile'    => '9888888888',
                'status'    => 'active',
            ]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_ps@delawala.com'],
            [
                'name'      => 'Test Admin PS',
                'password'  => bcrypt('password'),
                'firm_id'   => $this->firm->id,
                'role_id'   => $role->id,
                'role'      => 'admin',
                'status'    => 'active',
            ]
        );

        $this->customer = Customer::firstOrCreate(
            ['mobile' => '9123456789'],
            [
                'name'    => 'Nehal Test Customer',
                'firm_id' => $this->firm->id,
                'status'  => 'active',
            ]
        );

        $this->plot1 = Property::create([
            'firm_id'       => $this->firm->id,
            'property_name' => 'Plot 101 Test',
            'property_code' => 'DL-P-101',
            'unit_no'       => '101',
            'price'         => 3000000.00,
            'status'        => 'available',
        ]);

        $this->plot2 = Property::create([
            'firm_id'       => $this->firm->id,
            'property_name' => 'Plot 102 Test',
            'property_code' => 'DL-P-102',
            'unit_no'       => '102',
            'price'         => 3500000.00,
            'status'        => 'available',
        ]);
    }

    public function test_property_sale_created_with_multiple_plots_and_initial_booking_payment(): void
    {
        $response = $this->actingAs($this->admin)->post(route('property-sales.store'), [
            'firm_id'          => $this->firm->id,
            'property_ids'     => [$this->plot1->id, $this->plot2->id],
            'customer_id'      => $this->customer->id,
            'sale_date'        => '2026-09-17',
            'sale_amount'      => 6500000.00,
            'booking_amount'   => 2000000.00,
            'payment_status'   => 'partial',
            'sale_status'      => 'sold',
        ]);

        $response->assertRedirect(route('property-sales.index'));

        $sale = PropertySale::latest('id')->first();
        $this->assertNotNull($sale);
        $this->assertEquals(6500000.00, (float)$sale->sale_amount);
        $this->assertEquals(2000000.00, (float)$sale->booking_amount);
        $this->assertEquals(4500000.00, (float)$sale->remaining_amount);
        $this->assertEquals('partial', $sale->payment_status);

        // Verify both plots are attached in pivot table
        $this->assertEquals(2, $sale->properties()->count());
        $this->assertTrue($sale->properties->pluck('id')->contains($this->plot1->id));
        $this->assertTrue($sale->properties->pluck('id')->contains($this->plot2->id));

        // Verify all_properties and property_names
        $this->assertEquals(2, $sale->all_properties->count());
        $this->assertStringContainsString('Plot 101 Test', $sale->property_names);
        $this->assertStringContainsString('Plot 102 Test', $sale->property_names);

        // Verify plot statuses updated to 'sold'
        $this->assertEquals('sold', $this->plot1->fresh()->status);
        $this->assertEquals('sold', $this->plot2->fresh()->status);

        // Verify initial payment record created in payments table
        $this->assertEquals(1, $sale->payments()->count());
        $initPayment = $sale->payments()->first();
        $this->assertEquals(2000000.00, (float)$initPayment->payment_amount);
    }

    public function test_recording_second_and_subsequent_installment_payments(): void
    {
        $sale = PropertySale::create([
            'firm_id'          => $this->firm->id,
            'property_id'      => $this->plot1->id,
            'customer_id'      => $this->customer->id,
            'sale_date'        => '2026-09-17',
            'sale_amount'      => 6000000.00,
            'booking_amount'   => 2000000.00,
            'remaining_amount' => 4000000.00,
            'payment_status'   => 'partial',
            'sale_status'      => 'sold',
        ]);
        $sale->properties()->sync([$this->plot1->id, $this->plot2->id]);

        // Record 2nd payment: 2,500,000
        $payRes2 = $this->actingAs($this->admin)->post(route('property-sales.payments.store', $sale->id), [
            'payment_amount'  => 2500000.00,
            'payment_date'    => '2026-09-20',
            'payment_mode'    => 'Bank Transfer',
            'transaction_ref' => 'TXN-002-TEST',
            'remarks'         => 'Second Installment Payment',
        ]);
        $payRes2->assertRedirect();

        $sale->refresh();
        $this->assertEquals(4500000.00, (float)$sale->booking_amount);
        $this->assertEquals(1500000.00, (float)$sale->remaining_amount);
        $this->assertEquals('partial', $sale->payment_status);
        $this->assertEquals(2, $sale->payments()->count());

        // Record 3rd payment: 1,500,000 (Clears remaining balance)
        $payRes3 = $this->actingAs($this->admin)->post(route('property-sales.payments.store', $sale->id), [
            'payment_amount'  => 1500000.00,
            'payment_date'    => '2026-09-25',
            'payment_mode'    => 'UPI',
            'transaction_ref' => 'UPI-003-TEST',
            'remarks'         => 'Final Cleared Payment',
        ]);
        $payRes3->assertRedirect();

        $sale->refresh();
        $this->assertEquals(6000000.00, (float)$sale->booking_amount);
        $this->assertEquals(0.00, (float)$sale->remaining_amount);
        $this->assertEquals('paid', $sale->payment_status);
        $this->assertEquals(3, $sale->payments()->count());

        // Check index and show views render the multi-plots and installment badges
        $indexRes = $this->actingAs($this->admin)->get(route('property-sales.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Plot 101 Test');
        $indexRes->assertSee('Plot 102 Test');
        $indexRes->assertSee('3 Installments');

        $showRes = $this->actingAs($this->admin)->get(route('property-sales.show', $sale->id));
        $showRes->assertStatus(200);
        $showRes->assertSee('Plot 101 Test');
        $showRes->assertSee('Plot 102 Test');
        $showRes->assertSee('TXN-002-TEST');
        $showRes->assertSee('UPI-003-TEST');
    }

    public function test_deleting_installment_payment_recalculates_balance(): void
    {
        $sale = PropertySale::create([
            'firm_id'          => $this->firm->id,
            'property_id'      => $this->plot1->id,
            'customer_id'      => $this->customer->id,
            'sale_date'        => '2026-09-17',
            'sale_amount'      => 5000000.00,
            'booking_amount'   => 0.00,
            'remaining_amount' => 5000000.00,
            'payment_status'   => 'pending',
            'sale_status'      => 'sold',
        ]);

        $p1 = Payment::create([
            'firm_id'          => $this->firm->id,
            'property_sale_id' => $sale->id,
            'customer_id'      => $this->customer->id,
            'property_id'      => $this->plot1->id,
            'total_amount'     => 5000000.00,
            'paid_amount'      => 2000000.00,
            'pending_amount'   => 3000000.00,
            'payment_amount'   => 2000000.00,
            'payment_mode'     => 'Cash',
            'payment_date'     => '2026-09-17',
            'status'           => 'partial',
        ]);

        $p2 = Payment::create([
            'firm_id'          => $this->firm->id,
            'property_sale_id' => $sale->id,
            'customer_id'      => $this->customer->id,
            'property_id'      => $this->plot1->id,
            'total_amount'     => 5000000.00,
            'paid_amount'      => 5000000.00,
            'pending_amount'   => 0.00,
            'payment_amount'   => 3000000.00,
            'payment_mode'     => 'Bank Transfer',
            'payment_date'     => '2026-09-20',
            'status'           => 'paid',
        ]);

        $sale->recalculatePaymentStatus();
        $this->assertEquals(5000000.00, (float)$sale->booking_amount);
        $this->assertEquals('paid', $sale->payment_status);

        // Delete payment #2
        $delRes = $this->actingAs($this->admin)->delete(route('property-sales.payments.destroy', [$sale->id, $p2->id]));
        $delRes->assertRedirect();

        $sale->refresh();
        $this->assertEquals(2000000.00, (float)$sale->booking_amount);
        $this->assertEquals(3000000.00, (float)$sale->remaining_amount);
        $this->assertEquals('partial', $sale->payment_status);
        $this->assertEquals(1, $sale->payments()->count());
    }
}
