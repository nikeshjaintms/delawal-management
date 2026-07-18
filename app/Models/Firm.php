<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Firm extends Model
{
    protected $fillable = [
        'firm_name', 'owner_name', 'email', 'password', 'mobile', 'alternate_mobile',
        'address', 'city', 'state', 'pincode',
        'gst_no', 'pan_number', 'firm_logo',
        'bank_name', 'account_number', 'ifsc_code', 'branch_name',
        'status',
    ];

    protected $hidden = ['password'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function financialYears()
    {
        return $this->hasMany(FinancialYear::class);
    }
}
