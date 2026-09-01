<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'property_id',
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
        'created_by',
        'updated_by',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function property()
    {
        return $this->belongsTo(PropertyMaster::class, 'property_id');
    }

    public function propertyMaster()
    {
        return $this->belongsTo(PropertyMaster::class, 'property_id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'project_id')
            ->orderByRaw('CAST(COALESCE(NULLIF(unit_no, ""), id) AS UNSIGNED) ASC, id ASC');
    }

    public function bulks()
    {
        return $this->hasMany(Property::class, 'project_id')
            ->orderByRaw('CAST(COALESCE(NULLIF(unit_no, ""), id) AS UNSIGNED) ASC, id ASC');
    }

    public function contractors()
    {
        return $this->hasMany(Contractor::class, 'project_id')->latest();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get property address dynamically from parent PropertyMaster.
     */
    public function getDisplayAddressAttribute(): string
    {
        if ($this->propertyMaster && !empty($this->propertyMaster->full_address)) {
            return $this->propertyMaster->full_address;
        }
        $parts = array_filter([$this->address, $this->city, $this->state, $this->pincode ? ('- ' . $this->pincode) : null]);
        return count($parts) ? implode(', ', $parts) : '-';
    }
}
