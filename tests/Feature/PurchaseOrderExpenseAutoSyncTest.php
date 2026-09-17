<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Firm;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PurchaseOrderExpenseAutoSyncTest extends TestCase
{
    use DatabaseMigrations;

    public function test_purchase_order_creation_automatically_creates_expense_and_updates_project_totals(): void
    {
        $firm = Firm::create([
            'firm_name' => 'Delawala Developers',
            'email' => 'info@delawala.com',
            'phone' => '9876543210',
            'city' => 'Surat',
            'state' => 'Gujarat',
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@delawala.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'firm_id' => $firm->id,
            'status' => 'active',
        ]);

        $project = Project::create([
            'firm_id' => $firm->id,
            'project_name' => 'Royal Palms Luxury Project',
            'project_code' => 'RPLP-001',
            'status' => 'active',
        ]);

        $vendor = Vendor::create([
            'firm_id' => $firm->id,
            'name' => 'Shree Ram Steels & Cement',
            'mobile' => '9822334455',
            'status' => 'active',
        ]);

        $cat = MaterialCategory::create([
            'firm_id' => $firm->id,
            'category_name' => 'Construction Materials',
            'status' => 'active',
        ]);

        $material = Material::create([
            'firm_id' => $firm->id,
            'material_category_id' => $cat->id,
            'material_name' => 'TMT 500D Steel Bars 12mm',
            'unit' => 'ton',
            'unit_price' => 50000.0,
            'status' => 'active',
        ]);

        // 1. Create a Purchase Order
        $poData = [
            'firm_id' => $firm->id,
            'project_id' => $project->id,
            'vendor_id' => $vendor->id,
            'supplier_name' => 'UltraTech Regional Depot',
            'po_date' => '2026-09-17',
            'delivery_date' => '2026-09-25',
            'status' => 'Approved',
            'items' => [
                [
                    'material_id' => $material->id,
                    'qty' => 10,
                    'rate' => 50000.0,
                    'discount_pct' => 0,
                    'gst_pct' => 18,
                ]
            ],
            'remarks' => 'Order for Phase 1 foundation steel work.',
        ];

        $response = $this->actingAs($admin)->post(route('purchase-orders.store'), $poData);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('purchase-orders.index'));

        $po = PurchaseOrder::where('project_id', $project->id)->latest('id')->first();
        $this->assertNotNull($po);
        $this->assertEquals(590000.0, (float) $po->grand_total);
        $this->assertEquals('UltraTech Regional Depot', $po->supplier_name);

        // 2. Check that an Expense was automatically created and linked to the PO
        $expense = Expense::where('purchase_order_id', $po->id)->first();
        $this->assertNotNull($expense, 'Expense should be automatically created from Purchase Order');
        $this->assertEquals((float) $po->grand_total, (float) $expense->amount);
        $this->assertEquals($project->id, $expense->project_id);
        $this->assertEquals('Approved', $expense->approval_status);

        // 3. Check Project totals
        $project->refresh();
        $this->assertEquals((float) $po->grand_total, $project->total_expenses);
        $this->assertEquals((float) $po->grand_total, $project->po_expenses_total);
        $this->assertEquals(0.0, $project->direct_expenses_total);

        // 4. Test viewing the project page shows the expense ledger and PO
        $viewResponse = $this->actingAs($admin)->get(route('projects.show', $project->id));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Project Expenses &amp; Material Procurement', false);
        $viewResponse->assertSee($po->po_number);
        $viewResponse->assertSee('PO #');

        // 5. Test updating the Purchase Order updates the linked Expense
        $updateData = $poData;
        $updateData['items'][0]['qty'] = 12;

        $updateResponse = $this->actingAs($admin)->put(route('purchase-orders.update', $po->id), $updateData);
        $updateResponse->assertSessionHasNoErrors();

        $po->refresh();
        $expense->refresh();
        $this->assertEquals(708000.0, (float) $po->grand_total);
        $this->assertEquals(708000.0, (float) $expense->amount);
        $this->assertEquals(708000.0, $project->refresh()->total_expenses);
    }
}
