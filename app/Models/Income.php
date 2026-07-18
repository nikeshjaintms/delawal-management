<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        'firm_id', 'income_date', 'income_type', 'amount',
        'payment_mode_id', 'received_from', 'reference_no',
        'description', 'status',
    ];

    public function firm()        { return $this->belongsTo(Firm::class); }
    public function paymentMode() { return $this->belongsTo(PaymentMode::class); }
}
