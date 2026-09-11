<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('purchase_payments')) {
            Schema::create('purchase_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('purchase_id');
                $table->unsignedBigInteger('firm_id')->nullable();
                $table->unsignedBigInteger('payment_mode_id')->nullable();
                $table->decimal('amount', 15, 2)->default(0);
                $table->date('payment_date');
                $table->string('payment_mode')->default('Cash');
                $table->string('reference_no')->nullable();
                $table->string('bank_name')->nullable();
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            });
        }

        // Backfill existing purchases with initial payments if paid_amount > 0
        if (Schema::hasTable('purchases')) {
            $existing = DB::table('purchases')->where('paid_amount', '>', 0)->get();
            foreach ($existing as $p) {
                $alreadyExists = DB::table('purchase_payments')->where('purchase_id', $p->id)->exists();
                if (!$alreadyExists) {
                    DB::table('purchase_payments')->insert([
                        'purchase_id'  => $p->id,
                        'firm_id'      => $p->firm_id,
                        'amount'       => (float)$p->paid_amount,
                        'payment_date' => $p->purchase_date ?: date('Y-m-d'),
                        'payment_mode' => $p->payment_mode ?: 'Cash',
                        'reference_no' => $p->reference_no,
                        'bank_name'    => null,
                        'remarks'      => 'Initial Payment / Advance',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_payments');
    }
};
