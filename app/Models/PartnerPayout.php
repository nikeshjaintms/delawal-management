<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerPayout extends Model
{
    protected $fillable = [
        'firm_id',
        'partner_id',
        'partner_share_id',
        'payout_date',
        'amount',
        'payment_mode_id',
        'payment_mode',
        'reference_no',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'payout_date' => 'date',
        'amount'      => 'decimal:2',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function share()
    {
        return $this->belongsTo(PartnerShare::class, 'partner_share_id');
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
