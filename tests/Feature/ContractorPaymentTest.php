<?php

namespace Tests\Feature;

use App\Models\Contractor;
use App\Models\ContractorPayment;
use App\Models\Firm;
use App\Models\PaymentMode;
use App\Models\Project;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContractorPaymentTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $firm;
    protected $project;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Admin']);

        $this->firm = Firm::firstOrCreate(
            ['firm_name' => 'QA Delawala Construction Test'],
            [
                'email'  => 'qa_contractor_firm@delawala.com',
                'mobile' => '9876543210',
                'status' => 'active',
            ]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'qa_contractor_admin@delawala.com'],
            [
                'name'     => 'Contractor Admin QA',
                'password' => bcrypt('password'),
                'role_id'  => $role->id,
                'firm_id'  => $this->firm->id,
                'status'   => 'active',
            ]
        );

        $this->project = Project::firstOrCreate(
            ['project_code' => 'PRJ-CON-TEST-01'],
            [
                'firm_id'      => $this->firm->id,
                'project_name' => 'Royal Heritage Residency QA',
                'status'       => 'active',
            ]
        );

        PaymentMode::firstOrCreate(['name' => 'Cash'], ['status' => 'active']);
        PaymentMode::firstOrCreate(['name' => 'Bank Transfer'], ['status' => 'active']);
        PaymentMode::firstOrCreate(['name' => 'Cheque'], ['status' => 'active']);
        PaymentMode::firstOrCreate(['name' => 'UPI'], ['status' => 'active']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_a_contractor_with_contract_amount_and_initial_advance()
    {
        $uniqueName = 'Ramesh Patel & Sons (Civil Contractor) ' . uniqid();
        $response = $this->actingAs($this->admin)->withSession(['firm_id' => $this->firm->id])->post(route('contractors.store'), [
            'firm_ids'               => [$this->firm->id],
            'project_ids'            => [$this->project->id],
            'contractor_name'        => $uniqueName,
            'mobile'                 => '9876543210',
            'contract_amount'        => 300000,
            'work_type'              => 'Civil Construction Work',
            'contract_date'          => date('Y-m-d'),
            'initial_payment_amount' => 50000,
            'initial_payment_mode'   => 'Bank Transfer',
            'initial_reference_no'   => 'NEFT-2026-001',
            'status'                 => 'active',
        ]);

        $response->assertRedirect(route('contractors.index'));

        $contractor = Contractor::where('contractor_name', $uniqueName)->first();
        $this->assertNotNull($contractor);
        $this->assertEquals(300000, $contractor->contract_amount);
        $this->assertEquals(50000, $contractor->paid_amount);
        $this->assertEquals(250000, $contractor->due_amount);
        $this->assertEquals('partial', $contractor->payment_status);

        $this->assertDatabaseHas('contractor_payments', [
            'contractor_id' => $contractor->id,
            'amount'        => 50000,
            'payment_mode'  => 'Bank Transfer',
            'payment_type'  => 'Advance',
            'reference_no'  => 'NEFT-2026-001',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_records_multiple_contractor_payment_installments_and_updates_status()
    {
        $uniqueName = 'Shreeji Electrical Works ' . uniqid();
        $contractor = Contractor::create([
            'firm_id'         => $this->firm->id,
            'project_id'      => $this->project->id,
            'contractor_name' => $uniqueName,
            'contract_amount' => 100000,
            'paid_amount'     => 0,
            'due_amount'      => 100000,
            'payment_status'  => 'unpaid',
            'status'          => 'active',
        ]);
        $contractor->syncFirms([$this->firm->id]);
        $contractor->syncProjects([$this->project->id]);

        // 1st Installment (₹30,000)
        $res1 = $this->actingAs($this->admin)->withSession(['firm_id' => $this->firm->id])->post(route('contractors.payments.store', $contractor->id), [
            'amount'        => 30000,
            'payment_date'  => '2026-09-01',
            'payment_mode'  => 'Cash',
            'payment_type'  => 'Advance',
            'remarks'       => '1st Advance Installment',
        ]);
        $res1->assertRedirect(route('contractors.show', $contractor->id));

        $contractor->refresh();
        $this->assertEquals(30000, $contractor->paid_amount);
        $this->assertEquals(70000, $contractor->due_amount);
        $this->assertEquals('partial', $contractor->payment_status);

        // 2nd Installment (₹40,000 Running Bill)
        $res2 = $this->actingAs($this->admin)->withSession(['firm_id' => $this->firm->id])->post(route('contractors.payments.store', $contractor->id), [
            'amount'        => 40000,
            'payment_date'  => '2026-09-10',
            'payment_mode'  => 'Cheque',
            'reference_no'  => 'CHQ-887711',
            'payment_type'  => 'Running Bill',
            'remarks'       => '2nd RA Bill payment',
        ]);
        $res2->assertRedirect(route('contractors.show', $contractor->id));

        $contractor->refresh();
        $this->assertEquals(70000, $contractor->paid_amount);
        $this->assertEquals(30000, $contractor->due_amount);
        $this->assertEquals('partial', $contractor->payment_status);

        // 3rd Installment (₹30,000 Final Settlement)
        $res3 = $this->actingAs($this->admin)->withSession(['firm_id' => $this->firm->id])->post(route('contractors.payments.store', $contractor->id), [
            'amount'        => 30000,
            'payment_date'  => '2026-09-15',
            'payment_mode'  => 'UPI',
            'reference_no'  => 'UPI-9922001',
            'payment_type'  => 'Final Settlement',
            'remarks'       => 'Final bill full settlement',
        ]);
        $res3->assertRedirect(route('contractors.show', $contractor->id));

        $contractor->refresh();
        $this->assertEquals(100000, $contractor->paid_amount);
        $this->assertEquals(0, $contractor->due_amount);
        $this->assertEquals('paid', $contractor->payment_status);
        $this->assertCount(3, $contractor->payments);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_a_contractor_payment_and_recalculates_balance()
    {
        $uniqueName = 'Om Plumbing Services ' . uniqid();
        $contractor = Contractor::create([
            'firm_id'         => $this->firm->id,
            'project_id'      => $this->project->id,
            'contractor_name' => $uniqueName,
            'contract_amount' => 50000,
            'paid_amount'     => 0,
            'due_amount'      => 50000,
            'payment_status'  => 'unpaid',
            'status'          => 'active',
        ]);

        $payment = ContractorPayment::create([
            'contractor_id' => $contractor->id,
            'firm_id'       => $this->firm->id,
            'amount'        => 20000,
            'payment_date'  => date('Y-m-d'),
            'payment_mode'  => 'Cash',
        ]);
        $contractor->recalculatePaymentStatus();

        $contractor->refresh();
        $this->assertEquals(20000, $contractor->paid_amount);
        $this->assertEquals(30000, $contractor->due_amount);

        // Delete payment
        $response = $this->actingAs($this->admin)->withSession(['firm_id' => $this->firm->id])
            ->delete(route('contractors.payments.destroy', [$contractor->id, $payment->id]));

        $response->assertRedirect(route('contractors.show', $contractor->id));
        $this->assertDatabaseMissing('contractor_payments', ['id' => $payment->id]);

        $contractor->refresh();
        $this->assertEquals(0, $contractor->paid_amount);
        $this->assertEquals(50000, $contractor->due_amount);
        $this->assertEquals('unpaid', $contractor->payment_status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_displays_contractor_index_with_kpi_summary_totals_and_payments_breakdown()
    {
        $uniqueName = 'Shiv Electrical Works ' . uniqid();
        $contractor = Contractor::create([
            'firm_id'         => $this->firm->id,
            'project_id'      => $this->project->id,
            'contractor_name' => $uniqueName,
            'contract_amount' => 0, // No fixed contract
            'paid_amount'     => 0,
            'due_amount'      => 0,
            'payment_status'  => 'unpaid',
            'status'          => 'active',
        ]);

        ContractorPayment::create([
            'contractor_id' => $contractor->id,
            'firm_id'       => $this->firm->id,
            'amount'        => 7500,
            'payment_date'  => date('Y-m-d'),
            'payment_mode'  => 'Cash',
        ]);
        $contractor->recalculatePaymentStatus();

        $response = $this->actingAs($this->admin)->withSession(['firm_id' => $this->firm->id])
            ->get(route('contractors.index'));

        $response->assertOk();
        $response->assertViewHas('totalPaid');
        $response->assertViewHas('totalContract');
        $response->assertViewHas('totalDue');
        $response->assertViewHas('totalContractors');
        $response->assertSee('Total Paid (All Payments)');
        $response->assertSee('₹7,500.00');
        $response->assertSee('#1');
        $response->assertSee('1 payment');
    }
}

