<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debit_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->string('debit_note_no')->nullable();
            $table->date('debit_note_date');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('related_bill_no')->nullable();    // links to expenses / purchase bill
            $table->text('reason')->nullable();
            $table->decimal('taxable_amount', 15, 2)->default(0);
            $table->decimal('cgst_rate',   6, 2)->nullable();
            $table->decimal('cgst_amount', 15, 2)->default(0);
            $table->decimal('sgst_rate',   6, 2)->nullable();
            $table->decimal('sgst_amount', 15, 2)->default(0);
            $table->decimal('igst_rate',   6, 2)->nullable();
            $table->decimal('igst_amount', 15, 2)->default(0);
            $table->decimal('total_gst',   15, 2)->default(0);
            $table->decimal('debit_amount', 15, 2)->default(0); // grand total of debit
            $table->string('status')->default('Pending');        // Pending / Approved / Rejected
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debit_notes');
    }
};
