<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'name',
        'mobile',
        'alternate_mobile',
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

    public function propertySales()
    {
        return $this->hasMany(PropertySale::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function ledgers()
    {
        return $this->hasMany(Ledger::class);
    }
}