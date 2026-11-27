<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BrokerCommission;
use App\Models\ContractorPayment;
use App\Models\Expense;
use App\Models\Income;
use App\Models\InvoicePayment;
use App\Models\Ledger;
use App\Models\LoanEmiSchedule;
use App\Models\LoanPayment;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\PropertyMasterPayment;
use App\Models\PropertySale;
use App\Models\PurchasePayment;
use App\Models\Receipt;
use App\Models\RentalPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class TallyAutoPostingService
{
    private static bool $isSyncing = false;

    public static function syncModel(Model $model): void
    {
        if (self::$isSyncing) {
            return;
        }

        if (!self::requiredTablesExist()) {
            return;
        }

        self::$isSyncing = true;
        try {
            match (true) {
                $model instanceof Booking               => self::syncBooking($model),
                $model instanceof PropertySale          => self::syncPropertySale($model),
                $model instanceof Payment               => self::syncPayment($model),
                $model instanceof RentalPayment         => self::syncRentalPayment($model),
                $model instanceof Income                => self::syncIncome($model),
                $model instanceof Expense               => self::syncExpense($model),
                $model instanceof PropertyMasterPayment => self::syncPropertyMasterPayment($model),
                $model instanceof PurchasePayment       => self::syncPurchasePayment($model),
                $model instanceof ContractorPayment     => self::syncContractorPayment($model),
                $model instanceof LoanEmiSchedule       => self::syncLoanEmi($model),
                $model instanceof LoanPayment           => self::syncLoanPayment($model),
                $model instanceof BrokerCommission      => self::syncBrokerCommission($model),
                $model instanceof InvoicePayment        => self::syncInvoicePayment($model),
                default                                 => null,
            };
        } finally {
            self::$isSyncing = false;
        }
    }

    public static function deleteForModel(Model $model): void
    {
        if (self::$isSyncing || !self::requiredTablesExist()) {
            return;
        }

        self::$isSyncing = true;
        try {
            $tag = self::tagFor($model);
            if ($tag) {
                self::deleteSyncedRecords($tag);
            }
        } finally {
            self::$isSyncing = false;
        }
    }

    private static function syncBooking(Booking $booking): void
    {
        $tag = "BK#{$booking->id}";
        $amount = (float) ($booking->booking_amount ?? 0);
        $firmId = self::resolveFirmId($booking);

        if (!$firmId || $booking->status === 'cancelled' || $amount <= 0) {
            self::deleteSyncedRecords($tag);
            return;
        }

        $booking->loadMissing(['customer', 'property', 'properties', 'paymentMode']);
        $propNames = $booking->all_properties->pluck('property_name')->filter()->implode(', ') ?: 'Property Unit';
        $custName = $booking->customer?->name ?? 'Customer';
        $date = $booking->booking_date ?: $booking->created_at?->toDateString() ?: now()->toDateString();
        $modeName = $booking->payment_mode ?: ($booking->paymentMode?->name ?? 'Cash');
        $modeId = $booking->payment_mode_id ?: self::resolvePaymentModeId($modeName);

        self::upsertLedger($tag, [
            'firm_id'           => $firmId,
            'ledger_date'       => $date,
            'property_id'       => $booking->property_id,
            'customer_id'       => $booking->customer_id,
            'vendor_id'         => null,
            'broker_id'         => $booking->broker_id,
            'transaction_type'  => 'Payment Received',
            'transaction_title' => "Booking Advance — {$propNames}",
            'debit_amount'      => 0,
            'credit_amount'     => $amount,
            'payment_mode'      => $modeName,
            'reference_no'      => $booking->transaction_ref,
            'remarks'           => self::appendTag("Booking Token from {$custName}", $tag),
        ]);

        self::upsertReceipt($tag, [
            'firm_id'         => $firmId,
            'receipt_no'      => "RCT-BK-{$booking->id}",
            'receipt_date'    => $date,
            'received_from'   => $custName,
            'amount'          => $amount,
            'payment_mode_id' => $modeId,
            'reference_no'    => $booking->transaction_ref,
            'remarks'         => self::appendTag("Booking Advance for {$propNames}", $tag),
            'status'          => 'active',
        ]);

        // Only post to Income if the booked property has NOT already been converted to an active PropertySale
        $propIds = $booking->all_properties->pluck('id')->filter()->toArray();
        $hasActiveSale = !empty($propIds) && PropertySale::where('sale_status', '!=', 'cancelled')
            ->where(function ($q) use ($propIds) {
                $q->whereIn('property_id', $propIds)
                  ->orWhereHas('properties', fn ($pq) => $pq->whereIn('properties.id', $propIds));
            })
            ->exists();

        if ($hasActiveSale) {
            self::deleteSyncedIncome($tag);
        } else {
            self::upsertIncome($tag, [
                'firm_id'         => $firmId,
                'property_id'     => $booking->property_id,
                'income_date'     => $date,
                'income_type'     => 'Booking Advance',
                'amount'          => $amount,
                'payment_mode_id' => $modeId,
                'received_from'   => $custName,
                'reference_no'    => $booking->transaction_ref,
                'description'     => self::appendTag("Booking Advance for {$propNames}", $tag),
                'status'          => 'active',
            ]);
        }
    }

    private static function syncPropertySale(PropertySale $sale): void
    {
        $tag = "SALE#{$sale->id}";
        $firmId = self::resolveFirmId($sale);

        if (!$firmId || $sale->sale_status === 'cancelled') {
            self::deleteSyncedRecords($tag);
            self::deleteSyncedRecords("SALEADV#{$sale->id}");
            return;
        }

        $sale->loadMissing(['customer', 'property', 'properties', 'payments']);
        $propNames = $sale->property_names ?: ($sale->property?->property_name ?? 'Property Unit');
        $custName = $sale->customer?->name ?? 'Customer';
        $date = $sale->sale_date?->toDateString() ?: $sale->created_at?->toDateString() ?: now()->toDateString();
        $saleAmt = (float) ($sale->sale_amount ?? 0);

        // Remove temporary Booking Advance income rows for any properties now sold in this sale
        $soldPropIds = $sale->all_properties->pluck('id')->filter()->toArray();
        if (!empty($soldPropIds)) {
            $convertedBookingIds = Booking::where('status', '!=', 'cancelled')
                ->where(function ($q) use ($soldPropIds) {
                    $q->whereIn('property_id', $soldPropIds)
                      ->orWhereHas('properties', fn ($pq) => $pq->whereIn('properties.id', $soldPropIds));
                })
                ->pluck('id');
            foreach ($convertedBookingIds as $bkId) {
                self::deleteSyncedIncome("BK#{$bkId}");
            }
        }

        if ($saleAmt > 0) {
            self::upsertLedger($tag, [
                'firm_id'           => $firmId,
                'ledger_date'       => $date,
                'property_id'       => $sale->property_id,
                'customer_id'       => $sale->customer_id,
                'vendor_id'         => null,
                'broker_id'         => $sale->broker_id,
                'transaction_type'  => 'Sale',
                'transaction_title' => "Property Sale — {$propNames}",
                'debit_amount'      => 0,
                'credit_amount'     => $saleAmt,
                'payment_mode'      => null,
                'reference_no'      => $sale->invoice_no,
                'remarks'           => self::appendTag("Sale to {$custName}", $tag),
            ]);
        } else {
            self::deleteSyncedLedger($tag);
        }

        // If sale has initial booking_amount paid directly with NO rows in payments table, create a Receipt for it
        $directPaid = (float) ($sale->booking_amount ?? 0);
        if ($directPaid > 0 && $sale->payments()->count() === 0) {
            self::upsertReceipt("SALEADV#{$sale->id}", [
                'firm_id'         => $firmId,
                'receipt_no'      => "RCT-SALE-{$sale->id}",
                'receipt_date'    => $date,
                'received_from'   => $custName,
                'amount'          => $directPaid,
                'payment_mode_id' => null,
                'reference_no'    => $sale->invoice_no,
                'remarks'         => self::appendTag("Direct Sale Advance for {$propNames}", "SALEADV#{$sale->id}"),
                'status'          => 'active',
            ]);
        } else {
            self::deleteSyncedReceipt("SALEADV#{$sale->id}");
        }
    }

    private static function syncPayment(Payment $payment): void
    {
        $tag = "PAY#{$payment->id}";
        $amount = (float) ($payment->payment_amount ?? 0);
        $firmId = self::resolveFirmId($payment);

        if (!$firmId || $amount <= 0) {
            self::deleteSyncedRecords($tag);
            return;
        }

        $payment->loadMissing(['customer', 'property', 'propertySale.property']);
        $propName = $payment->property?->property_name ?: ($payment->propertySale?->property_names ?? 'Property Unit');
        $custName = $payment->customer?->name ?: ($payment->propertySale?->customer?->name ?? 'Customer');
        $date = $payment->payment_date ?: $payment->created_at?->toDateString() ?: now()->toDateString();
        $modeName = $payment->payment_mode ?: 'Cash';

        // Clear any temporary direct-sale-advance receipt since payment rows now track installments
        if ($payment->property_sale_id) {
            self::deleteSyncedReceipt("SALEADV#{$payment->property_sale_id}");
        }

        self::upsertLedger($tag, [
            'firm_id'           => $firmId,
            'ledger_date'       => $date,
            'property_id'       => $payment->property_id,
            'customer_id'       => $payment->customer_id,
            'vendor_id'         => null,
            'broker_id'         => null,
            'transaction_type'  => 'Payment Received',
            'transaction_title' => "Sale Installment — {$propName}",
            'debit_amount'      => 0,
            'credit_amount'     => $amount,
            'payment_mode'      => $modeName,
            'reference_no'      => $payment->transaction_ref,
            'remarks'           => self::appendTag("Received from {$custName}", $tag),
        ]);

        self::upsertReceipt($tag, [
            'firm_id'         => $firmId,
            'receipt_no'      => "RCT-PAY-{$payment->id}",
            'receipt_date'    => $date,
            'received_from'   => $custName,
            'amount'          => $amount,
            'payment_mode_id' => self::resolvePaymentModeId($modeName),
            'reference_no'    => $payment->transaction_ref,
            'remarks'         => self::appendTag("Sale Installment for {$propName}", $tag),
            'status'          => 'active',
        ]);
    }

    private static function syncRentalPayment(RentalPayment $rp): void
    {
        $tag = "RNT#{$rp->id}";
        $amount = (float) ($rp->paid_amount ?? 0);
        $rp->loadMissing(['rental.tenant', 'rental.property', 'property']);
        $firmId = $rp->firm_id ?: ($rp->rental?->firm_id ?? self::resolveFirmId($rp));

        if (!$firmId || $amount <= 0) {
            self::deleteSyncedRecords($tag);
            return;
        }

        $tenantName = $rp->rental?->tenant_name ?: ($rp->rental?->tenant?->name ?? 'Tenant');
        $propName = $rp->property?->property_name ?: ($rp->rental?->property?->property_name ?? 'Rental Unit');
        $date = $rp->payment_date ?: $rp->created_at?->toDateString() ?: now()->toDateString();
        $modeName = $rp->payment_mode ?: 'Cash';
        $period = trim(($rp->payment_month ?? '') . ' ' . ($rp->payment_year ?? ''));

        self::upsertLedger($tag, [
            'firm_id'           => $firmId,
            'ledger_date'       => $date,
            'property_id'       => $rp->property_id ?: $rp->rental?->property_id,
            'customer_id'       => null,
            'vendor_id'         => null,
            'broker_id'         => null,
            'transaction_type'  => 'Rent Received',
            'transaction_title' => "Rent Received — {$propName}" . ($period ? " ({$period})" : ''),
            'debit_amount'      => 0,
            'credit_amount'     => $amount,
            'payment_mode'      => $modeName,
            'reference_no'      => $rp->receipt_no ?? null,
            'remarks'           => self::appendTag("Rent from {$tenantName}", $tag),
        ]);

        self::upsertReceipt($tag, [
            'firm_id'         => $firmId,
            'receipt_no'      => $rp->receipt_no ?: "RCT-RNT-{$rp->id}",
            'receipt_date'    => $date,
            'received_from'   => $tenantName,
            'amount'          => $amount,
            'payment_mode_id' => self::resolvePaymentModeId($modeName),
            'reference_no'    => $rp->transaction_id ?? null,
            'remarks'         => self::appendTag("Rent for {$propName}" . ($period ? " ({$period})" : ''), $tag),
            'status'          => 'active',
        ]);
    }

    private static function syncIncome(Income $income): void
    {
        // Do not recursively sync auto-created Booking Advance rows back to Ledger/Receipt
        if (str_contains((string) $income->description, '[AUTO:')) {
            return;
        }

        $tag = "INC#{$income->id}";
        $amount = (float) ($income->amount ?? 0);
        $firmId = self::resolveFirmId($income);

        if (!$firmId || $amount <= 0 || ($income->status && $income->status !== 'active')) {
            self::deleteSyncedRecords($tag);
            return;
        }

        $income->loadMissing('paymentMode');
        $date = $income->income_date ?: $income->created_at?->toDateString() ?: now()->toDateString();
        $modeName = $income->paymentMode?->name ?? 'Cash';
        $from = $income->received_from ?: 'Other Income';

        self::upsertLedger($tag, [
            'firm_id'           => $firmId,
            'ledger_date'       => $date,
            'property_id'       => $income->property_id,
            'customer_id'       => null,
            'vendor_id'         => null,
            'broker_id'         => null,
            'transaction_type'  => 'Payment Received',
            'transaction_title' => ($income->income_type ?: 'Income') . " — {$from}",
            'debit_amount'      => 0,
            'credit_amount'     => $amount,
            'payment_mode'      => $modeName,
            'reference_no'      => $income->reference_no,
            'remarks'           => self::appendTag((string) ($income->description ?: 'Income entry'), $tag),
        ]);

        self::upsertReceipt($tag, [
            'firm_id'         => $firmId,
            'receipt_no'      => "RCT-INC-{$income->id}",
            'receipt_date'    => $date,
            'received_from'   => $from,
            'amount'          => $amount,
            'payment_mode_id' => $income->payment_mode_id,
            'reference_no'    => $income->reference_no,
            'remarks'         => self::appendTag((string) ($income->income_type ?: 'Income'), $tag),
            'status'          => 'active',
        ]);
    }

    private static function syncExpense(Expense $expense): void
    {
        $tag = "EXP#{$expense->id}";
        $amount = (float) ($expense->amount ?? 0);
        $firmId = self::resolveFirmId($expense);

        if (!$firmId || $amount <= 0) {
            self::deleteSyncedLedger($tag);
            return;
        }

        $date = $expense->expense_date?->toDateString() ?: $expense->created_at?->toDateString() ?: now()->toDateString();
        $title = $expense->expense_title ?: ($expense->expense_category ?: 'Expense');

        self::upsertLedger($tag, [
            'firm_id'           => $firmId,
            'ledger_date'       => $date,
            'property_id'       => $expense->property_id,
            'customer_id'       => null,
            'vendor_id'         => $expense->vendor_id,
            'broker_id'         => null,
            'transaction_type'  => 'Expense',
            'transaction_title' => $title,
            'debit_amount'      => $amount,
            'credit_amount'     => 0,
            'payment_mode'      => $expense->payment_mode ?: 'Cash',
            'reference_no'      => $expense->bill_no ?: $expense->reference_no,
            'remarks'           => self::appendTag((string) ($expense->paid_to ? "Paid to {$expense->paid_to}" : 'Expense'), $tag),
        ]);
    }

    private static function syncPropertyMasterPayment(PropertyMasterPayment $pmp): void
    {
        $tag = "PMP#{$pmp->id}";
        $amount = (float) ($pmp->amount ?? 0);
        $pmp->loadMissing('propertyMaster');
        $firmId = $pmp->firm_id ?: ($pmp->propertyMaster?->firm_id ?? self::resolveFirmId($pmp));

        if (!$firmId || $amount <= 0) {
            self::deleteSyncedLedger($tag);
            return;
        }

        $date = $pmp->payment_date?->toDateString() ?: $pmp->created_at?->toDateString() ?: now()->toDateString();
        $pmName = $pmp->propertyMaster?->property_name ?? 'Land / Property Master';

        self::upsertLedger($tag, [
            'firm_id'           => $firmId,
            'ledger_date'       => $date,
            'property_id'       => null,
            'customer_id'       => null,
            'vendor_id'         => $pmp->propertyMaster?->vendor_id,
            'broker_id'         => $pmp->propertyMaster?->broker_id,
            'transaction_type'  => 'Purchase',
            'transaction_title' => "Property Acquisition — {$pmName}",
            'debit_amount'      => $amount,
            'credit_amount'     => 0,
            'payment_mode'      => $pmp->payment_mode ?: 'Bank Transfer',
            'reference_no'      => $pmp->reference_no,
            'remarks'           => self::appendTag((string) ($pmp->remarks ?: 'Property purchase payment'), $tag),
        ]);
    }

    private static function syncPurchasePayment(PurchasePayment $pp): void
    {
        $tag = "PURP#{$pp->id}";
        $amount = (float) ($pp->amount ?? 0);
        $pp->loadMissing('purchase');
        $firmId = $pp->firm_id ?: ($pp->purchase?->firm_id ?? self::resolveFirmId($pp));

        if (!$firmId || $amount <= 0) {
            self::deleteSyncedLedger($tag);
            return;
        }

        $date = $pp->payment_date?->toDateString() ?: $pp->created_at?->toDateString() ?: now()->toDateString();
        $title = $pp->purchase?->property_name ?: ($pp->purchase?->item_name ?? 'Property Purchase');

        self::upsertLedger($tag, [
            'firm_id'           => $firmId,
            'ledger_date'       => $date,
            'property_id'       => $pp->purchase?->property_id,
            'customer_id'       => null,
            'vendor_id'         => $pp->purchase?->vendor_id,
            'broker_id'         => null,
            'transaction_type'  => 'Purchase',
            'transaction_title' => "Purchase Payment — {$title}",
            'debit_amount'      => $amount,
            'credit_amount'     => 0,
            'payment_mode'      => $pp->payment_mode ?: 'Cash',
            'reference_no'      => $pp->reference_no,
            'remarks'           => self::appendTag((string) ($pp->remarks ?: 'Purchase payment'), $tag),
        ]);
    }

    private static function syncContractorPayment(ContractorPayment $cp): void
    {
        $tag = "CP#{$cp->id}";
        $amount = (float) ($cp->amount ?? 0);
        $cp->loadMissing('contractor');
        $firmId = $cp->firm_id ?: ($cp->contractor?->firm_id ?? self::resolveFirmId($cp));

        if (!$firmId || $amount <= 0) {
            self::deleteSyncedLedger($tag);
            return;
        }

        $date = $cp->payment_date?->toDateString() ?: $cp->created_at?->toDateString() ?: now()->toDateString();
        $cName = $cp->contractor?->contractor_name ?: ($cp->contractor?->name ?? 'Contractor');

        self::upsertLedger($tag, [
            'firm_id'           => $firmId,
            'ledger_date'       => $date,
            'property_id'       => $cp->property_id,
            'customer_id'       => null,
            'vendor_id'         => null,
            'broker_id'         => null,
            'transaction_type'  => 'Expense',
            'transaction_title' => "Contractor Payment — {$cName}",
            'debit_amount'      => $amount,
            'credit_amount'     => 0,
            'payment_mode'      => $cp->payment_mode ?: 'Bank Transfer',
            'reference_no'      => $cp->reference_no ?: $cp->bill_no,
            'remarks'           => self::appendTag((string) ($cp->remarks ?: ($cp->payment_type ?: 'Contractor payment')), $tag),
        ]);
    }

    private static function syncLoanEmi(LoanEmiSchedule $emi): void
    {
        $tag = "EMI#{$emi->id}";
        $amount = (float) ($emi->paid_amount ?? 0);
        $emi->loadMissing('loan');
        $firmId = $emi->firm_id ?: ($emi->loan?->firm_id ?? self::resolveFirmId($emi));

        if (!$firmId || $amount <= 0 || !in_array($emi->emi_status, ['Paid', 'Partial'])) {
            self::deleteSyncedLedger($tag);
            return;
        }

        $date = $emi->payment_date ?: $emi->emi_date ?: now()->toDateString();
        $lender = $emi->loan?->bank_name ?: ($emi->loan?->person_name ?? 'Lender');

        self::upsertLedger($tag, [
            'firm_id'           => $firmId,
            'ledger_date'       => $date,
            'property_id'       => $emi->loan?->property_id,
            'customer_id'       => $emi->loan?->customer_id,
            'vendor_id'         => null,
            'broker_id'         => null,
            'transaction_type'  => 'Loan EMI',
            'transaction_title' => "Loan EMI — {$lender} ({$emi->emi_month}/{$emi->emi_year})",
            'debit_amount'      => $amount,
            'credit_amount'     => 0,
            'payment_mode'      => $emi->payment_mode ?: 'Bank Transfer',
            'reference_no'      => null,
            'remarks'           => self::appendTag((string) ($emi->remarks ?: 'Loan EMI repayment'), $tag),
        ]);
    }

    private static function syncLoanPayment(LoanPayment $lp): void
    {
        $tag = "LPAY#{$lp->id}";
        $amount = (float) ($lp->amount ?? 0);
        $lp->loadMissing('loan');
        $firmId = $lp->firm_id ?: ($lp->loan?->firm_id ?? self::resolveFirmId($lp));

        if (!$firmId || $amount <= 0) {
            self::deleteSyncedLedger($tag);
            return;
        }

        $date = $lp->payment_date ?: $lp->created_at?->toDateString() ?: now()->toDateString();
        $lender = $lp->loan?->bank_name ?: ($lp->loan?->person_name ?? 'Lender');
        $isGiven = ($lp->loan?->loan_nature === 'given');

        self::upsertLedger($tag, [
            'firm_id'           => $firmId,
            'ledger_date'       => $date,
            'property_id'       => $lp->loan?->property_id,
            'customer_id'       => $lp->loan?->customer_id,
            'vendor_id'         => null,
            'broker_id'         => null,
            'transaction_type'  => 'Loan EMI',
            'transaction_title' => ($isGiven ? "Loan Recovery — {$lender}" : "Loan Repayment — {$lender}"),
            'debit_amount'      => $isGiven ? 0 : $amount,
            'credit_amount'     => $isGiven ? $amount : 0,
            'payment_mode'      => $lp->payment_mode ?: 'Bank Transfer',
            'reference_no'      => $lp->reference_no,
            'remarks'           => self::appendTag((string) ($lp->remarks ?: 'Loan payment'), $tag),
        ]);
    }

    private static function syncBrokerCommission(BrokerCommission $bc): void
    {
        $tag = "BC#{$bc->id}";
        $amount = (float) ($bc->commission_amount ?? 0);
        $firmId = self::resolveFirmId($bc);

        if (!$firmId || $amount <= 0 || $bc->payment_status !== 'paid') {
            self::deleteSyncedLedger($tag);
            return;
        }

        $bc->loadMissing('broker');
        $date = $bc->payment_date ?: $bc->created_at?->toDateString() ?: now()->toDateString();
        $bName = $bc->broker?->name ?? 'Broker';

        self::upsertLedger($tag, [
            'firm_id'           => $firmId,
            'ledger_date'       => $date,
            'property_id'       => $bc->property_id,
            'customer_id'       => $bc->customer_id,
            'vendor_id'         => null,
            'broker_id'         => $bc->broker_id,
            'transaction_type'  => 'Expense',
            'transaction_title' => "Broker Commission — {$bName}",
            'debit_amount'      => $amount,
            'credit_amount'     => 0,
            'payment_mode'      => 'Bank / Cash',
            'reference_no'      => null,
            'remarks'           => self::appendTag((string) ($bc->remarks ?: 'Broker commission paid'), $tag),
        ]);
    }

    private static function syncInvoicePayment(InvoicePayment $ip): void
    {
        $tag = "INVP#{$ip->id}";
        $amount = (float) ($ip->amount ?? 0);
        $ip->loadMissing('invoice');
        $inv = $ip->invoice;
        $firmId = $inv?->firm_id;

        if (!$firmId || $amount <= 0) {
            self::deleteSyncedRecords($tag);
            return;
        }

        $date = $ip->payment_date?->toDateString() ?: $ip->created_at?->toDateString() ?: now()->toDateString();
        $modeName = $ip->payment_mode ?: 'Cash';
        $party = $inv->recipient_name ?: 'Customer';
        $isOutflow = in_array($inv->invoice_type, ['contractor', 'material_purchase']);

        self::upsertLedger($tag, [
            'firm_id'           => $firmId,
            'ledger_date'       => $date,
            'property_id'       => null,
            'customer_id'       => $inv->customer_id,
            'vendor_id'         => $inv->vendor_id,
            'broker_id'         => null,
            'transaction_type'  => $isOutflow ? 'Expense' : 'Payment Received',
            'transaction_title' => "Invoice #{$inv->invoice_no} — {$party}",
            'debit_amount'      => $isOutflow ? $amount : 0,
            'credit_amount'     => $isOutflow ? 0 : $amount,
            'payment_mode'      => $modeName,
            'reference_no'      => $ip->transaction_reference,
            'remarks'           => self::appendTag((string) ($ip->notes ?: 'Invoice payment'), $tag),
        ]);

        if (!$isOutflow) {
            self::upsertReceipt($tag, [
                'firm_id'         => $firmId,
                'receipt_no'      => "RCT-INV-{$ip->id}",
                'receipt_date'    => $date,
                'received_from'   => $party,
                'amount'          => $amount,
                'payment_mode_id' => $ip->payment_mode_id ?: self::resolvePaymentModeId($modeName),
                'reference_no'    => $ip->transaction_reference,
                'remarks'         => self::appendTag("Invoice #{$inv->invoice_no} payment", $tag),
                'status'          => 'active',
            ]);
        } else {
            self::deleteSyncedReceipt($tag);
        }
    }

    private static function upsertLedger(string $tag, array $data): void
    {
        $marker = "[AUTO:{$tag}]";
        $existing = Ledger::withoutGlobalScope('data_isolation')
            ->where('remarks', 'like', "%{$marker}%")
            ->first();

        if ($existing) {
            $existing->update($data);
        } else {
            $ledger = Ledger::create($data);
            $ledger->syncFirms([$data['firm_id']]);
        }
    }

    private static function upsertReceipt(string $tag, array $data): void
    {
        $marker = "[AUTO:{$tag}]";
        $existing = Receipt::withoutGlobalScope('data_isolation')
            ->where('remarks', 'like', "%{$marker}%")
            ->first();

        if ($existing) {
            $existing->update($data);
        } else {
            $receipt = Receipt::create($data);
            $receipt->syncFirms([$data['firm_id']]);
        }
    }

    private static function upsertIncome(string $tag, array $data): void
    {
        if (!Schema::hasTable('incomes')) {
            return;
        }
        $marker = "[AUTO:{$tag}]";
        $existing = Income::withoutGlobalScope('data_isolation')
            ->where('description', 'like', "%{$marker}%")
            ->first();

        if ($existing) {
            $existing->update($data);
        } else {
            $income = Income::create($data);
            $income->syncFirms([$data['firm_id']]);
        }
    }

    private static function deleteSyncedRecords(string $tag): void
    {
        self::deleteSyncedLedger($tag);
        self::deleteSyncedReceipt($tag);
        self::deleteSyncedIncome($tag);
    }

    private static function deleteSyncedLedger(string $tag): void
    {
        Ledger::withoutGlobalScope('data_isolation')
            ->where('remarks', 'like', "%[AUTO:{$tag}]%")
            ->delete();
    }

    private static function deleteSyncedReceipt(string $tag): void
    {
        Receipt::withoutGlobalScope('data_isolation')
            ->where('remarks', 'like', "%[AUTO:{$tag}]%")
            ->delete();
    }

    private static function deleteSyncedIncome(string $tag): void
    {
        if (!Schema::hasTable('incomes')) {
            return;
        }
        Income::withoutGlobalScope('data_isolation')
            ->where('description', 'like', "%[AUTO:{$tag}]%")
            ->delete();
    }

    private static function tagFor(Model $model): ?string
    {
        $id = $model->getKey();
        if (!$id) {
            return null;
        }

        return match (true) {
            $model instanceof Booking               => "BK#{$id}",
            $model instanceof PropertySale          => "SALE#{$id}",
            $model instanceof Payment               => "PAY#{$id}",
            $model instanceof RentalPayment         => "RNT#{$id}",
            $model instanceof Income                => "INC#{$id}",
            $model instanceof Expense               => "EXP#{$id}",
            $model instanceof PropertyMasterPayment => "PMP#{$id}",
            $model instanceof PurchasePayment       => "PURP#{$id}",
            $model instanceof ContractorPayment     => "CP#{$id}",
            $model instanceof LoanEmiSchedule       => "EMI#{$id}",
            $model instanceof LoanPayment           => "LPAY#{$id}",
            $model instanceof BrokerCommission      => "BC#{$id}",
            $model instanceof InvoicePayment        => "INVP#{$id}",
            default                                 => null,
        };
    }

    private static function resolveFirmId(Model $model): ?int
    {
        $firmId = $model->firm_id ?? null;
        if (!$firmId && session('firm_id')) {
            $firmId = session('firm_id');
        }
        if (!$firmId && auth()->check()) {
            $firmId = auth()->user()?->firm_id;
        }
        return $firmId ? (int) $firmId : null;
    }

    private static function resolvePaymentModeId(?string $modeName): ?int
    {
        if (empty($modeName) || !Schema::hasTable('payment_modes')) {
            return null;
        }
        return PaymentMode::whereRaw('LOWER(name) = ?', [strtolower(trim($modeName))])->value('id');
    }

    private static function appendTag(string $text, string $tag): string
    {
        $marker = "[AUTO:{$tag}]";
        $clean = trim(str_replace($marker, '', $text));
        return trim(($clean !== '' ? $clean . ' ' : '') . $marker);
    }

    private static function requiredTablesExist(): bool
    {
        return Schema::hasTable('ledgers') && Schema::hasTable('receipts');
    }
}
