<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'name',
        'mobile',
        'alternate_mobile',
        'email',
        'address',
        'permanent_address',
        'city',
        'occupation',
        'emergency_contact_name',
        'emergency_contact_mobile',
        'identity_type',
        'identity_number',
        'document_file',
        'status',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
