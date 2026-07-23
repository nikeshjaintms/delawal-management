<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function firms()
    {
        return $this->belongsToMany(Firm::class, 'property_type_firm', 'property_type_id', 'firm_id')->withTimestamps();
    }
}
