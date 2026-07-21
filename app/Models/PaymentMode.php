<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'name',
        'description',
        'status',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }
}
