<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('property_master_payments')) {
            Schema::create('property_master_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('property_master_id');
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

                $table->foreign('property_master_id')->references('id')->on('property_masters')->onDelete('cascade');
            });
        }

        // Backfill existing property_masters with initial payment if paid_amount > 0
        if (Schema::hasTable('property_masters')) {
            $existing = DB::table('property_masters')->where('paid_amount', '>', 0)->get();
            foreach ($existing as $pm) {
                $alreadyExists = DB::table('property_master_payments')->where('property_master_id', $pm->id)->exists();
                if (!$alreadyExists) {
                    DB::table('property_master_payments')->insert([
                        'property_master_id' => $pm->id,
                        'firm_id'            => $pm->firm_id,
                        'amount'             => (float)$pm->paid_amount,
                        'payment_date'       => $pm->purchase_date ?: date('Y-m-d'),
                        'payment_mode'       => $pm->payment_mode ?: 'Cash',
                        'reference_no'       => null,
                        'bank_name'          => null,
                        'remarks'            => 'Initial Payment / Advance',
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('property_master_payments');
    }
};
