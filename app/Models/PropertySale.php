<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertySale extends Model
{
    protected $fillable = [
        'firm_id',
        'property_id',
        'customer_id',
        'broker_id',
        'sale_date',
        'invoice_no',
        'sale_amount',
        'taxable_amount',
        'cgst_rate',
        'cgst_amount',
        'sgst_rate',
        'sgst_amount',
        'igst_rate',
        'igst_amount',
        'total_gst',
        'grand_total',
        'hsn_code',
        'booking_amount',
        'remaining_amount',
        'payment_status',
        'sale_status',
        'agreement_file',
        'note',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'property_sale_id');
    }
}
