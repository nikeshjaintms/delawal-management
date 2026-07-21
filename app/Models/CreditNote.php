<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'credit_note_no',
        'credit_note_date',
        'customer_id',
        'related_invoice_no',
        'property_sale_id',
        'reason',
        'taxable_amount',
        'cgst_rate',
        'cgst_amount',
        'sgst_rate',
        'sgst_amount',
        'igst_rate',
        'igst_amount',
        'total_gst',
        'credit_amount',
        'status',
        'notes',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function propertySale()
    {
        return $this->belongsTo(PropertySale::class);
    }
}
