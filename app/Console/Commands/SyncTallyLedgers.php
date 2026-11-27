<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BrokerCommission;
use App\Models\ContractorPayment;
use App\Models\Expense;
use App\Models\Income;
use App\Models\InvoicePayment;
use App\Models\LoanEmiSchedule;
use App\Models\LoanPayment;
use App\Models\Payment;
use App\Models\PropertyMasterPayment;
use App\Models\PropertySale;
use App\Models\PurchasePayment;
use App\Models\RentalPayment;
use App\Services\TallyAutoPostingService;
use Illuminate\Console\Command;

class SyncTallyLedgers extends Command
{
    protected $signature = 'tally:sync';
    protected $description = 'Backfill and synchronize all existing bookings, sales, payments, rentals, expenses, loans, and receipts into Tally Ledgers & Receipts safely.';

    public function handle(): int
    {
        $models = [
            Booking::class,
            PropertySale::class,
            Payment::class,
            RentalPayment::class,
            Income::class,
            Expense::class,
            PropertyMasterPayment::class,
            PurchasePayment::class,
            ContractorPayment::class,
            LoanEmiSchedule::class,
            LoanPayment::class,
            BrokerCommission::class,
            InvoicePayment::class,
        ];

        $count = 0;
        foreach ($models as $modelClass) {
            if (!class_exists($modelClass)) {
                continue;
            }
            $modelClass::withoutGlobalScope('data_isolation')->chunkById(200, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    TallyAutoPostingService::syncModel($row);
                    $count++;
                }
            });
        }

        $this->info("Tally auto-posting synchronized {$count} records safely.");
        return self::SUCCESS;
    }
}
