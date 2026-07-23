<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'property_id',
        'tenant_name',
        'tenant_mobile',
        'tenant_email',
        'rent_amount',
        'security_deposit',
        'rent_start_date',
        'rent_end_date',
        'rent_due_date',
        'payment_status',
        'rental_status',
        'remarks',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function rentalPayments()
    {
        return $this->hasMany(RentalPayment::class);
    }
}
