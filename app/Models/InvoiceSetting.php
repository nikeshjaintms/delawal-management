<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    protected $fillable = [
        'financial_year_id',
        'sales_prefix', 'purchase_prefix', 'booking_prefix', 'rental_prefix',
        'payment_prefix', 'receipt_prefix', 'expense_prefix', 'income_prefix', 'loan_prefix',
        'starting_number', 'current_number', 'status',
    ];

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class);
    }

    /**
     * Generate the next invoice number for a given prefix type.
     * Example: SAL-2026-0001
     */
    public function generateNumber(string $type): string
    {
        $prefixField = $type . '_prefix';
        $prefix = $this->$prefixField ?? strtoupper(substr($type, 0, 3));

        $year = $this->financialYear
            ? substr($this->financialYear->year_name, 0, 4)
            : date('Y');

        $number = str_pad($this->current_number, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$year}-{$number}";
    }

    /** Increment current_number and return the generated invoice number */
    public function nextNumber(string $type): string
    {
        $invoice = $this->generateNumber($type);
        $this->increment('current_number');
        return $invoice;
    }

    public static function activeSetting(): ?self
    {
        return self::where('status', 'active')->with('financialYear')->first();
    }
}
