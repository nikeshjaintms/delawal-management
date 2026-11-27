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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firm_id')->nullable()->constrained('firms')->nullOnDelete();
            $table->string('name');
            $table->string('mobile', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->boolean('is_silent')->default(true);
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['firm_id', 'status']);
            $table->index('is_silent');
        });

        Schema::create('partner_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firm_id')->constrained('firms')->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->string('scope_type', 30); // firm | project | property_master | property
            $table->foreignId('project_id')->nullable()->constrained('projects')->cascadeOnDelete();
            $table->foreignId('property_master_id')->nullable()->constrained('property_masters')->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->cascadeOnDelete();
            $table->decimal('share_percentage', 5, 2);
            $table->date('effective_from')->nullable();
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['firm_id', 'scope_type']);
            $table->index(['partner_id', 'status']);
        });

        Schema::create('partner_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firm_id')->constrained('firms')->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->foreignId('partner_share_id')->constrained('partner_shares')->cascadeOnDelete();
            $table->date('payout_date');
            $table->decimal('amount', 15, 2);
            $table->foreignId('payment_mode_id')->nullable()->constrained('payment_modes')->nullOnDelete();
            $table->string('payment_mode', 50)->nullable();
            $table->string('reference_no', 150)->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['partner_share_id', 'payout_date']);
            $table->index(['firm_id', 'partner_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_payouts');
        Schema::dropIfExists('partner_shares');
        Schema::dropIfExists('partners');
    }
};
