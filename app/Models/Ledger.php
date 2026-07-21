<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'ledger_date',
        'property_id',
        'customer_id',
        'vendor_id',
        'broker_id',
        'transaction_type',
        'transaction_title',
        'debit_amount',
        'credit_amount',
        'payment_mode',
        'reference_no',
        'remarks',
    ];

    public function firm()     { return $this->belongsTo(Firm::class); }
    public function property() { return $this->belongsTo(Property::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function vendor()   { return $this->belongsTo(Vendor::class); }
    public function broker()   { return $this->belongsTo(Broker::class); }
}
