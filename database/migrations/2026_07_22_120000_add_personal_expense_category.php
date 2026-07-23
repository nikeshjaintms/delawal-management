<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\ExpenseCategory;
use App\Models\Firm;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $name = 'Personal';
        $now = now();

        // Find or create the expense category
        $category = ExpenseCategory::firstOrCreate(
            ['name' => $name],
            [
                'description' => 'Personal expenses category',
                'status'      => 'active',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]
        );

        // Associate with all active firms
        $firms = Firm::where('status', 'active')->get();
        foreach ($firms as $firm) {
            $exists = DB::table('expense_category_firm')
                ->where('expense_category_id', $category->id)
                ->where('firm_id', $firm->id)
                ->exists();

            if (!$exists) {
                DB::table('expense_category_firm')->insert([
                    'expense_category_id' => $category->id,
                    'firm_id'             => $firm->id,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $name = 'Personal';
        $category = ExpenseCategory::where('name', $name)->first();
        if ($category) {
            DB::table('expense_category_firm')->where('expense_category_id', $category->id)->delete();
            $category->delete();
        }
    }
};
