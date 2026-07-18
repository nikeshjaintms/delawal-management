<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalPayment extends Model
{
    protected $fillable = [
        'rental_id',
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

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}
