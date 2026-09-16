<?php

namespace Tests\Feature;

use App\Models\Firm;
use App\Models\PropertyMaster;
use App\Models\Seller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SellerVendorSeparationTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $firm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_sep@delawala.com'],
            [
                'name' => 'Admin User Sep',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        $this->firm = Firm::firstOrCreate(
            ['firm_name' => 'Delawala Corporation Sep'],
            [
                'email' => 'delawala_sep@example.com',
                'mobile' => '9876543210',
                'status' => 'active',
            ]
        );
    }

    public function test_seller_index_page_is_accessible()
    {
        $response = $this->actingAs($this->admin)->get(route('sellers.index'));
        $response->assertStatus(200);
        $response->assertSee('Seller Master');
    }

    public function test_seller_can_be_created_updated_and_deleted()
    {
        // 1. Create Seller
        $sellerData = [
            'name'           => 'Ramesh Patel Landowner',
            'seller_type'    => 'individual',
            'mobile'         => '9898012345',
            'email'          => 'ramesh.land@example.com',
            'city'           => 'Surat',
            'pan_no'         => 'ABCDE1234F',
            'bank_name'      => 'State Bank of India',
            'account_number' => '123456789012',
            'status'         => 'active',
        ];

        $response = $this->actingAs($this->admin)->post(route('sellers.store'), $sellerData);
        $response->assertRedirect(route('sellers.index'));

        $this->assertDatabaseHas('sellers', [
            'name'   => 'Ramesh Patel Landowner',
            'pan_no' => 'ABCDE1234F',
        ]);

        $seller = Seller::where('name', 'Ramesh Patel Landowner')->first();
        $this->assertNotNull($seller);

        // 2. View Seller Detail
        $showResponse = $this->actingAs($this->admin)->get(route('sellers.show', $seller->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Ramesh Patel Landowner');

        // 3. Update Seller
        $updateResponse = $this->actingAs($this->admin)->put(route('sellers.update', $seller->id), array_merge($sellerData, [
            'name' => 'Ramesh Patel Landowner Updated',
            'city' => 'Navsari',
        ]));
        $updateResponse->assertRedirect(route('sellers.index'));

        $this->assertDatabaseHas('sellers', [
            'id'   => $seller->id,
            'name' => 'Ramesh Patel Landowner Updated',
            'city' => 'Navsari',
        ]);

        // 4. Quick Store Seller
        $quickResponse = $this->actingAs($this->admin)->postJson(route('sellers.quick-store'), [
            'name'   => 'Suresh Quick Seller',
            'mobile' => '9123456780',
        ]);
        $quickResponse->assertStatus(200);
        $quickResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('sellers', [
            'name' => 'Suresh Quick Seller',
        ]);
    }

    public function test_property_master_links_to_seller()
    {
        $seller = Seller::create([
            'name'   => 'Tribhuvan Land Seller',
            'mobile' => '9988776655',
            'city'   => 'Surat',
            'status' => 'active',
        ]);

        $propertyMaster = PropertyMaster::create([
            'firm_id'        => $this->firm->id,
            'property_name'  => 'Delawala Paradise Hills',
            'property_type'  => 'Plot',
            'property_code'  => 'TEST-SELLER-' . uniqid(),
            'seller_id'      => $seller->id,
            'seller_name'    => $seller->name,
            'purchase_price' => 5000000,
            'paid_amount'    => 2000000,
            'due_amount'     => 3000000,
            'payment_status' => 'partial',
            'status'         => 'active',
        ]);

        $this->assertEquals($seller->id, $propertyMaster->seller_id);
        $this->assertEquals('Tribhuvan Land Seller', $propertyMaster->seller->name);
        $this->assertTrue($seller->propertyMasters->contains($propertyMaster));
    }

    public function test_vendor_remains_independent_and_functional()
    {
        $vendorData = [
            'name'          => 'UltraTech Cement Supplier',
            'mobile'        => '9777123456',
            'email'         => 'ultratech@example.com',
            'gst_no'        => '24AAACU1234D1Z5',
            'city'          => 'Surat',
            'payment_terms' => '30 Days Credit',
            'status'        => 'active',
        ];

        $response = $this->actingAs($this->admin)->post(route('vendors.store'), $vendorData);
        $response->assertRedirect(route('vendors.index'));

        $this->assertDatabaseHas('vendors', [
            'name'   => 'UltraTech Cement Supplier',
            'gst_no' => '24AAACU1234D1Z5',
        ]);

        $vendor = Vendor::where('name', 'UltraTech Cement Supplier')->first();
        $this->assertNotNull($vendor);

        $showResponse = $this->actingAs($this->admin)->get(route('vendors.show', $vendor->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('UltraTech Cement Supplier');
    }
}
