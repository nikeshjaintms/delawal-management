<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'firm_id',
        'property_type_id',
        'property_code',
        'property_name',
        'location',
        'address',
        'city',
        'size',
        'size_unit',
        'unit_no',
        'floor_no',
        'facing',
        'price',
        'status',
        'description',
        'main_image',
        'document_file',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function documents()
    {
        return $this->hasMany(\App\Models\PropertyDocument::class);
    }
}
