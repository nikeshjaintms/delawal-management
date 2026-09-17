<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('contractor_purchase_order')) {
            Schema::create('contractor_purchase_order', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
                $table->foreignId('contractor_id')->constrained('contractors')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['purchase_order_id', 'contractor_id'], 'po_contractor_unique');
            });
        }

        // Backfill existing single contractor assignments
        if (Schema::hasTable('purchase_orders') && Schema::hasColumn('purchase_orders', 'contractor_id')) {
            $existing = DB::table('purchase_orders')
                ->whereNotNull('contractor_id')
                ->select('id', 'contractor_id', 'created_at', 'updated_at')
                ->get();

            foreach ($existing as $row) {
                // Verify contractor exists to avoid FK error
                $contractorExists = DB::table('contractors')->where('id', $row->contractor_id)->exists();
                if ($contractorExists) {
                    DB::table('contractor_purchase_order')->updateOrInsert(
                        [
                            'purchase_order_id' => $row->id,
                            'contractor_id'     => $row->contractor_id,
                        ],
                        [
                            'created_at' => $row->created_at ?? now(),
                            'updated_at' => $row->updated_at ?? now(),
                        ]
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_purchase_order');
    }
};
