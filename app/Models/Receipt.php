<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id', 'receipt_no', 'receipt_date', 'received_from',
        'amount', 'payment_mode_id', 'reference_no', 'remarks', 'status',
    ];

    public function firm()        { return $this->belongsTo(Firm::class); }
    public function paymentMode() { return $this->belongsTo(PaymentMode::class); }
}
