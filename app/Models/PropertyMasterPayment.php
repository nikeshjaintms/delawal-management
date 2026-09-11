<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyMasterPayment extends Model
{
    protected $fillable = [
        'property_master_id',
        'firm_id',
        'payment_mode_id',
        'amount',
        'payment_date',
        'payment_mode',
        'reference_no',
        'bank_name',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function propertyMaster()
    {
        return $this->belongsTo(PropertyMaster::class);
    }

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
