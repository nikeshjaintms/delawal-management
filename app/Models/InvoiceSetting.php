<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'financial_year_id',
        'sales_prefix', 'purchase_prefix', 'booking_prefix', 'rental_prefix',
        'payment_prefix', 'receipt_prefix', 'expense_prefix', 'income_prefix', 'loan_prefix',
        'starting_number', 'current_number', 'status',
    ];

    public function firms()
    {
        return $this->belongsToMany(Firm::class, 'tax_gst_setting_firm', 'invoice_setting_id', 'firm_id')->withTimestamps();
    }

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class);
    }

    /**
     * Generate the next invoice number for a given prefix type.
     * Example: INV-2026-0002
     */
    public function generateNumber(string $type): string
    {
        return Invoice::generateNextInvoiceNumber($type);
    }

    /** Increment current_number and return the generated invoice number */
    public function nextNumber(string $type): string
    {
        return Invoice::generateNextInvoiceNumber($type);
    }

    public static function activeSetting(): ?self
    {
        return self::where('status', 'active')->with('financialYear')->first();
    }
}
