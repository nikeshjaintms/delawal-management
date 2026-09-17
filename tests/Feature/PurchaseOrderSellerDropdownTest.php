<?php

namespace Tests\Feature;

use App\Models\Firm;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Seller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PurchaseOrderSellerDropdownTest extends TestCase
{
    use DatabaseMigrations;

    public function test_purchase_order_can_be_created_with_selected_seller_from_dropdown(): void
    {
        $firm = Firm::firstOrCreate(
            ['firm_name' => 'QA Seller PO Firm'],
            ['email' => 'qa_seller_po@delawala.com', 'mobile' => '9876543210', 'status' => 'active']
        );

        $admin = User::firstOrCreate(
            ['email' => 'qa_seller_admin@delawala.com'],
            ['name' => 'QA Admin', 'password' => bcrypt('password'), 'role' => 'admin', 'firm_id' => $firm->id, 'status' => 'active']
        );

        $project = Project::firstOrCreate(
            ['project_code' => 'PRJ-TEST-SEL-01'],
            ['firm_id' => $firm->id, 'project_name' => 'Royal Heritage Palms', 'status' => 'active']
        );

        $vendor = Vendor::firstOrCreate(
            ['name' => 'Ambuja Cements Regional Depot'],
            ['firm_id' => $firm->id, 'mobile' => '9877001122', 'status' => 'active']
        );

        $seller = Seller::firstOrCreate(
            ['name' => 'Gujarat Steel & Cement Traders'],
            ['firm_id' => $firm->id, 'mobile' => '9899112233', 'seller_type' => 'Store', 'status' => 'active']
        );

        $cat = MaterialCategory::firstOrCreate(
            ['category_name' => 'QA Raw Materials'],
            ['firm_id' => $firm->id, 'status' => 'active']
        );

        $material = Material::firstOrCreate(
            ['material_name' => 'QA OPC 53 Cement Bags'],
            ['firm_id' => $firm->id, 'material_category_id' => $cat->id, 'unit' => 'bag', 'unit_price' => 380, 'status' => 'active']
        );

        // 1. Verify Create form contains Seller dropdown data
        $createResponse = $this->actingAs($admin)->get(route('purchase-orders.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Seller / Supplier Store');
        $createResponse->assertSee('Gujarat Steel & Cement Traders');

        // 2. Submit PO with seller_id selected
        $poData = [
            'firm_id'       => $firm->id,
            'project_id'    => $project->id,
            'vendor_id'     => $vendor->id,
            'seller_id'     => $seller->id,
            'po_date'       => date('Y-m-d'),
            'delivery_date' => date('Y-m-d', strtotime('+5 days')),
            'status'        => 'Approved',
            'items'         => [
                [
                    'material_id'  => $material->id,
                    'qty'          => 100,
                    'rate'         => 380,
                    'discount_pct' => 0,
                    'gst_pct'      => 18,
                ]
            ],
            'remarks' => 'Order for Phase 1 cement batch with selected seller.',
        ];

        $storeResponse = $this->actingAs($admin)->post(route('purchase-orders.store'), $poData);
        $storeResponse->assertSessionHasNoErrors();
        $storeResponse->assertRedirect(route('purchase-orders.index'));

        $po = PurchaseOrder::where('project_id', $project->id)->latest('id')->first();
        $this->assertNotNull($po);
        $this->assertEquals($seller->id, $po->seller_id);
        $this->assertEquals('Gujarat Steel & Cement Traders', $po->supplier_name);
        $this->assertEquals(44840.0, (float) $po->grand_total);

        // 3. Verify Show and Index page renders seller details
        $showResponse = $this->actingAs($admin)->get(route('purchase-orders.show', $po->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Gujarat Steel & Cement Traders');

        $indexResponse = $this->actingAs($admin)->get(route('purchase-orders.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Gujarat Steel & Cement Traders');
    }
}
