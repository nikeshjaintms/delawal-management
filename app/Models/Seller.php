<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use \App\Traits\HasFirms;

    protected $table = 'sellers';

    protected $fillable = [
        'firm_id',
        'name',
        'seller_type',
        'contact_person',
        'phone',
        'mobile',
        'email',
        'pan_no',
        'aadhaar_no',
        'gst_no',
        'bank_name',
        'account_number',
        'ifsc_code',
        'branch_name',
        'address',
        'city',
        'state',
        'pincode',
        'remarks',
        'status',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function propertyMasters()
    {
        return $this->hasMany(PropertyMaster::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function getTotalLandAreaAttribute(): float
    {
        return (float) $this->propertyMasters()->sum('total_area');
    }

    public function getTotalDealsAmountAttribute(): float
    {
        return (float) $this->propertyMasters()->sum('purchase_price');
    }

    public function getTotalPaidAmountAttribute(): float
    {
        return (float) $this->propertyMasters()->sum('paid_amount');
    }

    public function getTotalDueAmountAttribute(): float
    {
        return (float) $this->propertyMasters()->sum('due_amount');
    }
}
