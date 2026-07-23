<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalPayment extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'rental_id',
        'property_id',
        'payment_month',
        'payment_year',
        'rent_amount',
        'paid_amount',
        'pending_amount',
        'payment_date',
        'payment_mode',
        'payment_status',
        'remarks',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
