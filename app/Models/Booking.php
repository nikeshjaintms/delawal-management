<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id', 'property_id', 'customer_id', 'broker_id', 'booking_type',
        'booking_date', 'total_amount', 'discount_type', 'discount_value',
        'discount_amount', 'final_amount', 'booking_amount', 'remaining_amount',
        'payment_mode_id', 'payment_mode', 'transaction_ref', 'agreement_date',
        'status', 'payment_status', 'remarks',
    ];

    public function firm()        { return $this->belongsTo(Firm::class); }
    public function property()    { return $this->belongsTo(Property::class); }
    public function customer()    { return $this->belongsTo(Customer::class); }
    public function broker()      { return $this->belongsTo(Broker::class); }
    public function paymentMode() { return $this->belongsTo(PaymentMode::class, 'payment_mode_id'); }
}
