<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)->default(0)->after('purchase_amount');
            }
            if (!Schema::hasColumn('purchases', 'due_amount')) {
                $table->decimal('due_amount', 15, 2)->default(0)->after('paid_amount');
            }
        });

        // Initialize existing purchases data
        $purchases = DB::table('purchases')->get();
        foreach ($purchases as $p) {
            $total = (float)($p->purchase_amount ?? 0);
            $status = strtolower($p->payment_status ?? 'unpaid');
            if ($status === 'paid') {
                $paid = $total;
                $due = 0.0;
            } elseif ($status === 'partial') {
                $paid = round($total / 2, 2);
                $due = max(0, $total - $paid);
            } else {
                $paid = 0.0;
                $due = $total;
            }

            DB::table('purchases')->where('id', $p->id)->update([
                'paid_amount' => $paid,
                'due_amount'  => $due,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'due_amount')) {
                $table->dropColumn('due_amount');
            }
            if (Schema::hasColumn('purchases', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
        });
    }
};
