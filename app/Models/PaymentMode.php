<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function firms()
    {
        return $this->belongsToMany(Firm::class, 'payment_mode_firm', 'payment_mode_id', 'firm_id')->withTimestamps();
    }
}
