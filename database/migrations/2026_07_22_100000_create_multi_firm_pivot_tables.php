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
        // 1. Create pivot tables
        Schema::create('property_type_firm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_type_id')->constrained('property_types')->cascadeOnDelete();
            $table->foreignId('firm_id')->constrained('firms')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('expense_category_firm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->cascadeOnDelete();
            $table->foreignId('firm_id')->constrained('firms')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('payment_mode_firm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_mode_id')->constrained('payment_modes')->cascadeOnDelete();
            $table->foreignId('firm_id')->constrained('firms')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('tax_gst_setting_firm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_setting_id')->constrained('invoice_settings')->cascadeOnDelete();
            $table->foreignId('firm_id')->constrained('firms')->cascadeOnDelete();
            $table->timestamps();
        });

        // 2. Transfer existing data
        $now = now();

        DB::table('property_types')->whereNotNull('firm_id')->chunkById(100, function ($rows) use ($now) {
            $inserts = [];
            foreach ($rows as $row) {
                $inserts[] = [
                    'property_type_id' => $row->id,
                    'firm_id'          => $row->firm_id,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }
            DB::table('property_type_firm')->insert($inserts);
        });

        DB::table('expense_categories')->whereNotNull('firm_id')->chunkById(100, function ($rows) use ($now) {
            $inserts = [];
            foreach ($rows as $row) {
                $inserts[] = [
                    'expense_category_id' => $row->id,
                    'firm_id'             => $row->firm_id,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ];
            }
            DB::table('expense_category_firm')->insert($inserts);
        });

        DB::table('payment_modes')->whereNotNull('firm_id')->chunkById(100, function ($rows) use ($now) {
            $inserts = [];
            foreach ($rows as $row) {
                $inserts[] = [
                    'payment_mode_id' => $row->id,
                    'firm_id'         => $row->firm_id,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }
            DB::table('payment_mode_firm')->insert($inserts);
        });

        DB::table('invoice_settings')->whereNotNull('firm_id')->chunkById(100, function ($rows) use ($now) {
            $inserts = [];
            foreach ($rows as $row) {
                $inserts[] = [
                    'invoice_setting_id' => $row->id,
                    'firm_id'            => $row->firm_id,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];
            }
            DB::table('tax_gst_setting_firm')->insert($inserts);
        });

        // 3. Drop firm_id foreign keys and columns from original tables
        Schema::table('property_types', function (Blueprint $table) {
            $table->dropForeign(['firm_id']);
            $table->dropColumn('firm_id');
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropForeign(['firm_id']);
            $table->dropColumn('firm_id');
        });

        Schema::table('payment_modes', function (Blueprint $table) {
            $table->dropForeign(['firm_id']);
            $table->dropColumn('firm_id');
        });

        Schema::table('invoice_settings', function (Blueprint $table) {
            $table->dropForeign(['firm_id']);
            $table->dropColumn('firm_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add columns
        Schema::table('property_types', function (Blueprint $table) {
            $table->foreignId('firm_id')->nullable()->constrained('firms')->cascadeOnDelete();
        });
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->foreignId('firm_id')->nullable()->constrained('firms')->cascadeOnDelete();
        });
        Schema::table('payment_modes', function (Blueprint $table) {
            $table->foreignId('firm_id')->nullable()->constrained('firms')->cascadeOnDelete();
        });
        Schema::table('invoice_settings', function (Blueprint $table) {
            $table->foreignId('firm_id')->nullable()->constrained('firms')->cascadeOnDelete();
        });

        // Backfill reverse
        DB::table('property_type_firm')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                DB::table('property_types')->where('id', $row->property_type_id)->update(['firm_id' => $row->firm_id]);
            }
        });
        DB::table('expense_category_firm')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                DB::table('expense_categories')->where('id', $row->expense_category_id)->update(['firm_id' => $row->firm_id]);
            }
        });
        DB::table('payment_mode_firm')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                DB::table('payment_modes')->where('id', $row->payment_mode_id)->update(['firm_id' => $row->firm_id]);
            }
        });
        DB::table('tax_gst_setting_firm')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                DB::table('invoice_settings')->where('id', $row->invoice_setting_id)->update(['firm_id' => $row->firm_id]);
            }
        });

        // Drop pivot tables
        Schema::dropIfExists('property_type_firm');
        Schema::dropIfExists('expense_category_firm');
        Schema::dropIfExists('payment_mode_firm');
        Schema::dropIfExists('tax_gst_setting_firm');
    }
};
