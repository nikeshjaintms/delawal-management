<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentMode;
use App\Models\Firm;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $newModes = ['Mobile Banking', 'RTGS', 'NEFT'];
        $now = now();

        foreach ($newModes as $name) {
            // Find or create the payment mode
            $mode = PaymentMode::firstOrCreate(
                ['name' => $name],
                [
                    'description' => $name . ' payment method',
                    'status'      => 'active',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]
            );

            // Associate with all active firms
            $firms = Firm::where('status', 'active')->get();
            foreach ($firms as $firm) {
                $exists = DB::table('payment_mode_firm')
                    ->where('payment_mode_id', $mode->id)
                    ->where('firm_id', $firm->id)
                    ->exists();

                if (!$exists) {
                    DB::table('payment_mode_firm')->insert([
                        'payment_mode_id' => $mode->id,
                        'firm_id'          => $firm->id,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversible step is not strictly necessary for simple additions, but we can clean up
        $newModes = ['Mobile Banking', 'RTGS', 'NEFT'];
        foreach ($newModes as $name) {
            $mode = PaymentMode::where('name', $name)->first();
            if ($mode) {
                DB::table('payment_mode_firm')->where('payment_mode_id', $mode->id)->delete();
                $mode->delete();
            }
        }
    }
};
