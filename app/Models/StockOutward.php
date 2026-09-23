<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOutward extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id', 'project_id', 'contractor_id', 'outward_number', 'stock_inward_number', 'material_id', 'property_id',
        'outward_date', 'quantity', 'vehicle_no', 'driver_name', 'lr_no', 'transport_name', 'used_for', 'remarks',
    ];

    protected $casts = [
        'outward_date' => 'date',
    ];

    public function firm()       { return $this->belongsTo(Firm::class); }
    public function project()    { return $this->belongsTo(Project::class); }
    public function contractor() { return $this->belongsTo(Contractor::class); }
    public function material()   { return $this->belongsTo(Material::class); }
    public function property()   { return $this->belongsTo(Property::class); }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'stock_outward_property')->withTimestamps();
    }

    public function getAllPropertiesAttribute()
    {
        if ($this->relationLoaded('properties') && $this->properties->isNotEmpty()) {
            return $this->properties;
        }
        $props = $this->properties()->get();
        if ($props->isNotEmpty()) {
            return $props;
        }
        if ($this->property_id && $this->property) {
            return collect([$this->property]);
        }
        return collect();
    }

    public function getPropertyNamesAttribute(): string
    {
        $names = $this->all_properties->map(function($p) {
            return ($p->unit_no ? 'Unit '.$p->unit_no : 'Plot #'.$p->id) . ($p->property_name ? ' ('.$p->property_name.')' : '');
        })->filter()->values();

        return $names->isNotEmpty() ? $names->implode(', ') : ($this->property ? (($this->property->unit_no ? 'Unit '.$this->property->unit_no : 'Plot #'.$this->property->id) . ' ('.$this->property->property_name.')') : '—');
    }
}
