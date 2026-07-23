<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->string('credit_note_no')->nullable();
            $table->date('credit_note_date');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('related_invoice_no')->nullable();   // links to property_sales invoice_no
            $table->unsignedBigInteger('property_sale_id')->nullable();
            $table->text('reason')->nullable();
            $table->decimal('taxable_amount', 15, 2)->default(0);
            $table->decimal('cgst_rate',   6, 2)->nullable();
            $table->decimal('cgst_amount', 15, 2)->default(0);
            $table->decimal('sgst_rate',   6, 2)->nullable();
            $table->decimal('sgst_amount', 15, 2)->default(0);
            $table->decimal('igst_rate',   6, 2)->nullable();
            $table->decimal('igst_amount', 15, 2)->default(0);
            $table->decimal('total_gst',   15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0); // grand total of credit
            $table->string('status')->default('Pending');         // Pending / Approved / Rejected
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('property_sale_id')->references('id')->on('property_sales')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
    }
};
