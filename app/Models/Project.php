<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'project_name',
        'project_code',
        'project_type',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'description',
        'status',
        'project_image',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
