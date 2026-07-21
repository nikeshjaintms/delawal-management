<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broker extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'name',
        'mobile',
        'email',
        'address',
        'city',
        'commission_percentage',
        'status',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }
}
