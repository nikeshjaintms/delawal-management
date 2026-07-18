<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // GST Purchase fields — all nullable, zero impact on existing rows
            $table->unsignedBigInteger('vendor_id')->nullable()->after('property_id');
            $table->string('invoice_no')->nullable()->after('bill_no');
            $table->string('hsn_code')->nullable()->after('invoice_no');
            $table->decimal('taxable_amount', 15, 2)->nullable()->after('amount');
            $table->decimal('cgst_rate',   6, 2)->nullable()->after('taxable_amount');
            $table->decimal('cgst_amount', 15, 2)->nullable()->default(0)->after('cgst_rate');
            $table->decimal('sgst_rate',   6, 2)->nullable()->after('cgst_amount');
            $table->decimal('sgst_amount', 15, 2)->nullable()->default(0)->after('sgst_rate');
            $table->decimal('igst_rate',   6, 2)->nullable()->after('sgst_amount');
            $table->decimal('igst_amount', 15, 2)->nullable()->default(0)->after('igst_rate');
            $table->decimal('total_gst',   15, 2)->nullable()->default(0)->after('igst_amount');
            $table->decimal('grand_total', 15, 2)->nullable()->after('total_gst');

            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn([
                'vendor_id', 'invoice_no', 'hsn_code',
                'taxable_amount',
                'cgst_rate', 'cgst_amount',
                'sgst_rate', 'sgst_amount',
                'igst_rate', 'igst_amount',
                'total_gst', 'grand_total',
            ]);
        });
    }
};
