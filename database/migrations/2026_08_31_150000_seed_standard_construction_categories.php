<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Constants\ConstructionMaterials;

return new class extends Migration
{
    public function up(): void
    {
        $firms = DB::table('firms')->get();
        if ($firms->isEmpty()) {
            return;
        }

        $now = now();
        foreach ($firms as $firm) {
            foreach (ConstructionMaterials::CATALOG as $catName => $catData) {
                $exists = DB::table('material_categories')
                    ->where('firm_id', $firm->id)
                    ->where('category_name', $catName)
                    ->exists();

                if (!$exists) {
                    DB::table('material_categories')->insert([
                        'firm_id'       => $firm->id,
                        'project_id'    => null,
                        'category_name' => $catName,
                        'description'   => $catData['description'] ?? null,
                        'status'        => 'active',
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Don't drop data on rollback
    }
};
