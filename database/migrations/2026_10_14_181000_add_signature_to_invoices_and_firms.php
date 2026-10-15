<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'signature_image')) {
                    $table->string('signature_image')->nullable()->after('notes');
                }
            });
        }

        if (Schema::hasTable('firms')) {
            Schema::table('firms', function (Blueprint $table) {
                if (!Schema::hasColumn('firms', 'digital_signature')) {
                    $table->string('digital_signature')->nullable()->after('firm_logo');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (Schema::hasColumn('invoices', 'signature_image')) {
                    $table->dropColumn('signature_image');
                }
            });
        }

        if (Schema::hasTable('firms')) {
            Schema::table('firms', function (Blueprint $table) {
                if (Schema::hasColumn('firms', 'digital_signature')) {
                    $table->dropColumn('digital_signature');
                }
            });
        }
    }
};
