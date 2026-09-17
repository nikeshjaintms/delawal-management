<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PurchaseOrder;
use App\Models\Expense;
use App\Models\ExpenseCategory;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'purchase_order_id')) {
                $table->unsignedBigInteger('purchase_order_id')->nullable()->after('vendor_id');
                if (DB::getDriverName() !== 'sqlite') {
                    $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
                    $table->index('purchase_order_id');
                }
            }
        });

        // Auto-sync all existing Purchase Orders to Expenses
        try {
            $defaultCat = ExpenseCategory::firstOrCreate(
                ['name' => 'Material & Procurement'],
                ['status' => 'active', 'description' => 'Procurement & Material Purchase Orders', 'firm_id' => 1]
            );

            $purchaseOrders = PurchaseOrder::with(['vendor', 'project', 'firm'])->get();
            foreach ($purchaseOrders as $po) {
                $amount = (float)($po->grand_total ?: ($po->taxable_amount ?: $po->sub_total ?: 0));
                if ($amount <= 0) continue;

                $approvalStatus = 'Pending';
                if (in_array($po->status, ['Approved', 'Ordered', 'Received'])) {
                    $approvalStatus = 'Approved';
                } elseif ($po->status === 'Cancelled') {
                    $approvalStatus = 'Rejected';
                }

                $expense = Expense::updateOrCreate(
                    ['purchase_order_id' => $po->id],
                    [
                        'firm_id'             => $po->firm_id ?: 1,
                        'project_id'          => $po->project_id,
                        'vendor_id'           => $po->vendor_id,
                        'expense_date'        => $po->po_date ?: now()->toDateString(),
                        'expense_category_id' => $defaultCat->id,
                        'expense_category'    => $defaultCat->name,
                        'expense_title'       => 'PO #' . $po->po_number . ($po->vendor ? ' - ' . $po->vendor->name : ''),
                        'amount'              => $amount,
                        'taxable_amount'      => $po->taxable_amount ?: $amount,
                        'cgst_amount'         => $po->cgst_amount ?: 0,
                        'sgst_amount'         => $po->sgst_amount ?: 0,
                        'igst_amount'         => $po->igst_amount ?: 0,
                        'total_gst'           => (($po->cgst_amount ?? 0) + ($po->sgst_amount ?? 0) + ($po->igst_amount ?? 0)),
                        'grand_total'         => $po->grand_total ?: $amount,
                        'paid_to'             => $po->vendor ? $po->vendor->name : 'Supplier',
                        'bill_no'             => $po->po_number,
                        'remarks'             => 'Auto-synced from Purchase Order #' . $po->po_number . ($po->remarks ? '. ' . $po->remarks : ''),
                        'approval_status'     => $approvalStatus,
                    ]
                );

                if ($po->firm_id) {
                    $expense->firms()->syncWithoutDetaching([$po->firm_id]);
                }
            }
        } catch (\Throwable $e) {
            // Ignore if seeding or testing
        }
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'purchase_order_id')) {
                if (DB::getDriverName() !== 'sqlite') {
                    $table->dropForeign(['purchase_order_id']);
                }
                $table->dropColumn('purchase_order_id');
            }
        });
        Schema::enableForeignKeyConstraints();
    }
};
