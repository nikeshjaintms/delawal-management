<?php

namespace Tests\Feature;

use App\Models\Contractor;
use App\Models\Firm;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Project;
use App\Models\PropertyMaster;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PurchaseOrderMultiContractorTest extends TestCase
{
    use DatabaseMigrations;

    private User $admin;
    private Firm $firm;
    private Vendor $vendor;
    private Material $material;
    private Project $project;
    private Contractor $contractorA;
    private Contractor $contractorB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->firm = Firm::create([
            'firm_name' => 'Delawala Test Firm',
            'email'     => 'test@firm.com',
            'city'      => 'Surat',
            'state'     => 'Gujarat',
            'status'    => 'active',
        ]);

        $this->admin = User::create([
            'name'     => 'Test Admin',
            'email'    => 'admin@example.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'firm_id'  => $this->firm->id,
        ]);

        $this->vendor = Vendor::create([
            'firm_id' => $this->firm->id,
            'name'    => 'Shreeji Building Materials',
            'mobile'  => '9876543210',
            'state'   => 'Gujarat',
            'status'  => 'active',
        ]);

        $category = MaterialCategory::create([
            'firm_id'       => $this->firm->id,
            'category_name' => 'Cement & Concrete',
            'status'        => 'active',
        ]);

        $this->material = Material::create([
            'firm_id'              => $this->firm->id,
            'material_category_id' => $category->id,
            'material_name'        => 'Ultratech Cement (50kg)',
            'unit'                 => 'Bags',
            'unit_price'           => 380.00,
            'status'               => 'active',
        ]);

        $pm = PropertyMaster::create([
            'firm_id'       => $this->firm->id,
            'property_name' => 'Delawala Heights Master',
            'status'        => 'active',
        ]);

        $this->project = Project::create([
            'firm_id'            => $this->firm->id,
            'property_master_id' => $pm->id,
            'project_name'       => 'Delawala Horizon Towers',
            'project_code'       => 'PRJ-DEL',
            'status'             => 'active',
        ]);

        $this->contractorA = Contractor::create([
            'firm_id'         => $this->firm->id,
            'project_id'      => $this->project->id,
            'contractor_name' => 'Patel RCC Structure Works',
            'contractor_type' => 'Civil Contractor',
            'mobile'          => '9825000001',
            'status'          => 'active',
        ]);

        $this->contractorB = Contractor::create([
            'firm_id'         => $this->firm->id,
            'project_id'      => $this->project->id,
            'contractor_name' => 'Sharma Brick & Plastering',
            'contractor_type' => 'Masonry Contractor',
            'mobile'          => '9825000002',
            'status'          => 'active',
        ]);
    }

    public function test_purchase_order_can_be_created_with_multiple_contractors(): void
    {
        $payload = [
            'firm_id'        => $this->firm->id,
            'project_id'     => $this->project->id,
            'contractor_ids' => [$this->contractorA->id, $this->contractorB->id],
            'vendor_id'      => $this->vendor->id,
            'po_date'        => '2026-09-17',
            'delivery_date'  => '2026-09-25',
            'status'         => 'Approved',
            'items'          => [
                [
                    'material_id'  => $this->material->id,
                    'qty'          => 200,
                    'rate'         => 380.00,
                    'discount_pct' => 0,
                    'gst_pct'      => 18,
                ],
            ],
            'remarks'        => 'Bulk cement procurement for multiple contractors',
        ];

        $response = $this->actingAs($this->admin)->post(route('purchase-orders.store'), $payload);
        $response->assertRedirect(route('purchase-orders.index'));

        $this->assertDatabaseHas('purchase_orders', [
            'firm_id'   => $this->firm->id,
            'vendor_id' => $this->vendor->id,
            'status'    => 'Approved',
        ]);

        $po = PurchaseOrder::with(['contractors', 'items'])->latest('id')->first();
        $this->assertNotNull($po);
        $this->assertCount(2, $po->contractors);
        $this->assertEquals(2, $po->all_contractors->count());
        $this->assertStringContainsString('Patel RCC Structure Works', $po->contractor_names);
        $this->assertStringContainsString('Sharma Brick & Plastering', $po->contractor_names);

        $this->assertDatabaseHas('contractor_purchase_order', [
            'purchase_order_id' => $po->id,
            'contractor_id'     => $this->contractorA->id,
        ]);
        $this->assertDatabaseHas('contractor_purchase_order', [
            'purchase_order_id' => $po->id,
            'contractor_id'     => $this->contractorB->id,
        ]);
    }

    public function test_purchase_order_can_be_updated_with_synced_contractors(): void
    {
        $po = PurchaseOrder::create([
            'firm_id'         => $this->firm->id,
            'project_id'      => $this->project->id,
            'contractor_id'   => $this->contractorA->id,
            'po_number'       => 'PO-2026-0001',
            'vendor_id'       => $this->vendor->id,
            'po_date'         => '2026-09-17',
            'status'          => 'Draft',
            'sub_total'       => 76000,
            'taxable_amount'  => 76000,
            'grand_total'     => 89680,
            'created_by'      => $this->admin->id,
        ]);
        $po->contractors()->sync([$this->contractorA->id]);

        $updatePayload = [
            'firm_id'        => $this->firm->id,
            'project_id'     => $this->project->id,
            'contractor_ids' => [$this->contractorB->id],
            'vendor_id'      => $this->vendor->id,
            'po_date'        => '2026-09-18',
            'status'         => 'Pending',
            'items'          => [
                [
                    'material_id'  => $this->material->id,
                    'qty'          => 100,
                    'rate'         => 380.00,
                    'discount_pct' => 0,
                    'gst_pct'      => 18,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('purchase-orders.update', $po->id), $updatePayload);
        $response->assertRedirect(route('purchase-orders.index'));

        $po->refresh();
        $this->assertCount(1, $po->contractors);
        $this->assertEquals($this->contractorB->id, $po->contractors->first()->id);
        $this->assertEquals('Sharma Brick & Plastering', $po->contractor_names);
    }

    public function test_purchase_orders_index_filters_and_displays_multiple_contractors(): void
    {
        $po = PurchaseOrder::create([
            'firm_id'         => $this->firm->id,
            'project_id'      => $this->project->id,
            'contractor_id'   => $this->contractorA->id,
            'po_number'       => 'PO-2026-9999',
            'vendor_id'       => $this->vendor->id,
            'po_date'         => '2026-09-17',
            'status'          => 'Approved',
            'sub_total'       => 76000,
            'grand_total'     => 89680,
            'created_by'      => $this->admin->id,
        ]);
        $po->contractors()->sync([$this->contractorA->id, $this->contractorB->id]);

        $response = $this->actingAs($this->admin)->get(route('purchase-orders.index', [
            'filter_contractor' => $this->contractorB->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('PO-2026-9999');
        $response->assertSee('2 Contractors');
        $response->assertSee('Patel RCC Structure Works');
        $response->assertSee('Sharma Brick & Plastering');
    }
}
