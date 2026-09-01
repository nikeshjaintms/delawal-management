<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentMode;
use App\Models\Firm;

return new class extends Migration
{
    public function up(): void
    {
        $defaultModes = [
            'Cash',
            'Bank Transfer',
            'Cheque',
            'UPI',
            'NEFT',
            'RTGS',
            'Mobile Banking',
            'Other',
        ];

        foreach ($defaultModes as $modeName) {
            PaymentMode::firstOrCreate(
                ['name' => $modeName],
                [
                    'description' => $modeName . ' payment method',
                    'status' => 'active',
                ]
            );
        }

        $firmIds = Firm::pluck('id')->toArray();
        if (!empty($firmIds)) {
            $paymentModes = PaymentMode::all();
            foreach ($paymentModes as $pm) {
                $pm->firms()->syncWithoutDetaching($firmIds);
            }
        }
    }

    public function down(): void
    {
        // No-op
    }
};
