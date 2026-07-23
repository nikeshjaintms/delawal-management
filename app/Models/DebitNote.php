<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebitNote extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'debit_note_no',
        'debit_note_date',
        'vendor_id',
        'related_bill_no',
        'reason',
        'taxable_amount',
        'cgst_rate',
        'cgst_amount',
        'sgst_rate',
        'sgst_amount',
        'igst_rate',
        'igst_amount',
        'total_gst',
        'debit_amount',
        'status',
        'notes',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
