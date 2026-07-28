<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('stock_inwards', function (Blueprint $table) {
            $table->string('inward_number')->nullable()->after('firm_id');
            $table->unsignedBigInteger('purchase_order_id')->nullable()->after('inward_number');
            $table->string('challan_no')->nullable()->after('bill_no');
            $table->string('vehicle_no')->nullable()->after('challan_no');
            $table->string('warehouse')->nullable()->after('vehicle_no');
            $table->decimal('gst_pct', 8, 2)->default(0.00)->after('rate');
            $table->decimal('gst_amount', 15, 2)->default(0.00)->after('gst_pct');
            $table->decimal('discount_pct', 8, 2)->default(0.00)->after('gst_amount');
            $table->decimal('discount_amount', 15, 2)->default(0.00)->after('discount_pct');
            $table->decimal('qty_damaged', 15, 3)->default(0.000)->after('quantity');
            $table->decimal('qty_ordered', 15, 3)->default(0.000)->after('quantity');

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
        });

        Schema::table('stock_outwards', function (Blueprint $table) {
            $table->string('outward_number')->nullable()->after('firm_id');
            $table->string('stock_inward_number')->nullable()->after('outward_number');
            $table->string('vehicle_no')->nullable()->after('quantity');
            $table->string('driver_name')->nullable()->after('vehicle_no');
            $table->string('lr_no')->nullable()->after('driver_name');
            $table->string('transport_name')->nullable()->after('lr_no');
        });
    }

    public function down(): void {
        Schema::table('stock_inwards', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
            $table->dropColumn([
                'inward_number', 'purchase_order_id', 'challan_no', 'vehicle_no', 'warehouse',
                'gst_pct', 'gst_amount', 'discount_pct', 'discount_amount', 'qty_damaged', 'qty_ordered'
            ]);
        });

        Schema::table('stock_outwards', function (Blueprint $table) {
            $table->dropColumn([
                'outward_number', 'stock_inward_number', 'vehicle_no', 'driver_name', 'lr_no', 'transport_name'
            ]);
        });
    }
};
