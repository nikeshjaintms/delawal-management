<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'firm_id', 'property_id', 'customer_id', 'broker_id',
        'booking_date', 'booking_amount', 'agreement_date',
        'status', 'payment_status', 'remarks',
    ];

    public function firm()       { return $this->belongsTo(Firm::class); }
    public function property()   { return $this->belongsTo(Property::class); }
    public function customer()   { return $this->belongsTo(Customer::class); }
    public function broker()     { return $this->belongsTo(Broker::class); }
}
