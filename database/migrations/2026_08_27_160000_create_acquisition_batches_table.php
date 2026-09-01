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
        if (!Schema::hasTable('acquisition_batches')) {
            Schema::create('acquisition_batches', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('firm_id');
                $table->unsignedBigInteger('property_master_id');
                $table->string('batch_name');
                $table->string('batch_number')->nullable();
                $table->date('purchase_date')->nullable();
                $table->decimal('purchase_rate', 15, 2)->default(0.00);
                $table->string('rate_unit')->default('per_plot'); // per_plot, per_sqft, per_sqyd
                $table->integer('total_plots')->default(0);
                $table->decimal('total_purchase_amount', 15, 2)->default(0.00);
                $table->string('status')->default('active'); // active, completed, archived
                $table->text('description')->nullable();
                $table->string('document_file')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
                $table->foreign('property_master_id')->references('id')->on('property_masters')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acquisition_batches');
    }
};
