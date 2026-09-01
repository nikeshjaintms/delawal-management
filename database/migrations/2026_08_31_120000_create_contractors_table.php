<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id');
            $table->unsignedBigInteger('project_id');
            $table->string('contractor_name');
            $table->string('mobile', 20)->nullable();
            $table->string('aadhar_no', 30)->nullable();
            $table->string('pan_no', 30)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('ifsc_code', 30)->nullable();
            $table->string('branch_name', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractors');
    }
};
