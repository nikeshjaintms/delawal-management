<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'firm_id',
        'name',
        'mobile',
        'email',
        'gst_no',
        'address',
        'city',
        'payment_terms',
        'status',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }
}
