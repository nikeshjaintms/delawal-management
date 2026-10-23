<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('properties:sync-status', function () {
    $this->info('Synchronizing property statuses from bookings, sales, and rentals...');
    \App\Models\Property::syncAllStatuses();
    $this->info('✓ All property statuses synchronized successfully!');
})->purpose('Synchronize property statuses based on active bookings, sales, and rentals');

Artisan::command('brokers:sync-commissions', function () {
    $this->info('Synchronizing old Property Sales & Bookings to Brokers and Broker Commissions...');

    $sales = \App\Models\PropertySale::all();
    $saleCount = 0;
    foreach ($sales as $sale) {
        if ($sale->broker_id || $sale->broker_name) {
            $brokerId = $sale->broker_id;
            if (!$brokerId && $sale->broker_name) {
                $bName = trim($sale->broker_name);
                $bMobile = trim((string) $sale->broker_mobile);
                $cleanMobile = null;
                if ($bMobile !== '') {
                    $digits = preg_replace('/[^0-9]/', '', $bMobile);
                    if (strlen($digits) === 10) {
                        $cleanMobile = $digits;
                    } elseif (strlen($digits) > 10) {
                        $cleanMobile = substr($digits, -10);
                    }
                }
                $existing = null;
                if ($cleanMobile) {
                    $existing = \App\Models\Broker::where('mobile', $cleanMobile)->first();
                }
                if (!$existing) {
                    $existing = \App\Models\Broker::whereRaw('LOWER(name) = ?', [strtolower($bName)])
                        ->where(function ($q) use ($sale) {
                            $q->where('firm_id', $sale->firm_id)
                              ->orWhereHas('firms', fn($fq) => $fq->where('firms.id', $sale->firm_id));
                        })->first();
                }
                if (!$existing) {
                    $existing = \App\Models\Broker::create([
                        'firm_id'               => $sale->firm_id,
                        'name'                  => $bName,
                        'mobile'                => $cleanMobile ?: null,
                        'commission_percentage' => ($sale->broker_commission_type === 'percentage') ? $sale->broker_commission_rate : null,
                        'status'                => 'active',
                    ]);
                    $existing->syncFirms([$sale->firm_id]);
                }
                $brokerId = $existing->id;
                $sale->updateQuietly(['broker_id' => $brokerId]);
            }

            if ($brokerId && ((float) ($sale->broker_commission_amount ?? 0) > 0 || (float) ($sale->broker_commission_rate ?? 0) > 0)) {
                $commPaymentStatus = match (strtolower((string) $sale->broker_commission_status)) {
                    'paid'    => 'paid',
                    'partial' => 'partial',
                    default   => 'pending',
                };
                $commType = in_array($sale->broker_commission_type, ['percentage', 'fixed']) ? $sale->broker_commission_type : 'percentage';
                $commValue = ($commType === 'percentage') ? (float) ($sale->broker_commission_rate ?? 0) : (float) ($sale->broker_commission_amount ?? 0);
                $commAmount = (float) ($sale->broker_commission_amount ?? 0);
                if ($commAmount <= 0 && $commType === 'percentage' && $commValue > 0 && (float) $sale->sale_amount > 0) {
                    $commAmount = round(((float) $sale->sale_amount * $commValue) / 100, 2);
                }

                $paymentDate = $sale->broker_commission_payment_date 
                    ? $sale->broker_commission_payment_date->format('Y-m-d') 
                    : ($sale->sale_date ? $sale->sale_date->format('Y-m-d') : now()->toDateString());

                $commission = \App\Models\BrokerCommission::updateOrCreate(
                    ['property_sale_id' => $sale->id],
                    [
                        'firm_id'           => $sale->firm_id,
                        'broker_id'         => $brokerId,
                        'property_id'       => $sale->property_id,
                        'customer_id'       => $sale->customer_id,
                        'booking_id'        => null,
                        'property_sale_id'  => $sale->id,
                        'commission_type'   => $commType,
                        'commission_value'  => $commValue,
                        'commission_amount' => $commAmount,
                        'payment_status'    => $commPaymentStatus,
                        'payment_date'      => $paymentDate,
                        'remarks'           => $sale->broker_notes ?: ('Property Sale: ' . ($sale->property_names ?: 'Property #' . $sale->property_id)),
                        'status'            => 'active',
                    ]
                );
                if ($sale->firm_id) {
                    $commission->syncFirms([$sale->firm_id]);
                }
                $saleCount++;
            }
        }
    }

    $this->info("✓ Processed {$sales->count()} sales ({$saleCount} commissions synchronized)!");
})->purpose('Synchronize old property sales to brokers and broker commissions');

