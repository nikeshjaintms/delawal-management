<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'property_sale_id',
        'customer_id',
        'property_id',
        'total_amount',
        'paid_amount',
        'pending_amount',
        'payment_amount',
        'payment_mode',
        'transaction_ref',
        'payment_date',
        'status',
        'remarks',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function propertySale()
    {
        return $this->belongsTo(PropertySale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
