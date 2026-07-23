<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrokerCommission extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'broker_id',
        'property_id',
        'customer_id',
        'booking_id',
        'commission_type',
        'commission_value',
        'commission_amount',
        'payment_status',
        'payment_date',
        'remarks',
        'status',
        'created_by',
    ];

    public function firm()       { return $this->belongsTo(Firm::class); }
    public function broker()     { return $this->belongsTo(Broker::class); }
    public function property()   { return $this->belongsTo(Property::class); }
    public function customer()   { return $this->belongsTo(Customer::class); }
    public function booking()    { return $this->belongsTo(Booking::class); }
    public function creator()    { return $this->belongsTo(User::class, 'created_by'); }
}
