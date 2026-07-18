<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->unsignedBigInteger('property_id');
            $table->string('tenant_name');
            $table->string('tenant_mobile');
            $table->string('tenant_email')->nullable();
            $table->decimal('rent_amount', 15, 2);
            $table->decimal('security_deposit', 15, 2)->nullable();
            $table->date('rent_start_date');
            $table->date('rent_end_date')->nullable();
            $table->integer('rent_due_date')->nullable()->comment('Day of month rent is due, e.g. 5');
            $table->string('payment_status')->default('pending');
            $table->string('rental_status')->default('active');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
