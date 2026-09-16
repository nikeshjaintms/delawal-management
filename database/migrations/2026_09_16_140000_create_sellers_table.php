<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sellers')) {
            Schema::create('sellers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('firm_id')->nullable()->constrained('firms')->nullOnDelete();
                $table->string('name');
                $table->string('seller_type')->default('individual'); // individual, organization, joint_owner
                $table->string('contact_person')->nullable();
                $table->string('phone')->nullable();
                $table->string('mobile')->nullable();
                $table->string('email')->nullable();
                $table->string('pan_no')->nullable();
                $table->string('aadhaar_no')->nullable();
                $table->string('gst_no')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('account_number')->nullable();
                $table->string('ifsc_code')->nullable();
                $table->string('branch_name')->nullable();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('pincode')->nullable();
                $table->text('remarks')->nullable();
                $table->string('status')->default('active'); // active, inactive
                $table->timestamps();
            });
        }

        if (Schema::hasTable('property_masters') && !Schema::hasColumn('property_masters', 'seller_id')) {
            Schema::table('property_masters', function (Blueprint $table) {
                $table->foreignId('seller_id')->nullable()->after('unit_prefix')->constrained('sellers')->nullOnDelete();
            });
        }

        if (Schema::hasTable('purchases') && !Schema::hasColumn('purchases', 'seller_id')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->foreignId('seller_id')->nullable()->after('firm_id')->constrained('sellers')->nullOnDelete();
            });
        }

        // Migrate any existing seller_name from property_masters into sellers table
        if (Schema::hasTable('property_masters') && Schema::hasTable('sellers')) {
            $existingMasters = DB::table('property_masters')->whereNotNull('seller_name')->where('seller_name', '!=', '')->get();
            foreach ($existingMasters as $pm) {
                $name = trim($pm->seller_name);
                if (empty($name)) continue;

                $seller = DB::table('sellers')->where('name', $name)->where('firm_id', $pm->firm_id)->first();
                if (!$seller) {
                    $sellerId = DB::table('sellers')->insertGetId([
                        'firm_id'    => $pm->firm_id,
                        'name'       => $name,
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $sellerId = $seller->id;
                }

                if (Schema::hasColumn('property_masters', 'seller_id')) {
                    DB::table('property_masters')->where('id', $pm->id)->update(['seller_id' => $sellerId]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('purchases') && Schema::hasColumn('purchases', 'seller_id')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->dropForeign(['seller_id']);
                $table->dropColumn('seller_id');
            });
        }

        if (Schema::hasTable('property_masters') && Schema::hasColumn('property_masters', 'seller_id')) {
            Schema::table('property_masters', function (Blueprint $table) {
                $table->dropForeign(['seller_id']);
                $table->dropColumn('seller_id');
            });
        }

        Schema::dropIfExists('sellers');
    }
};
