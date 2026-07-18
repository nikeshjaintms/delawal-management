<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'firm_id',
        'name',
        'mobile',
        'email',
        'address',
        'city',
        'customer_type',
        'status',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }
}