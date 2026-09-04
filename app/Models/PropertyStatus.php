<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyStatus extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'property_master_id',
        'property_id',
        'status',
        'status_date',
        'remarks',
        'updated_by',
    ];

    protected $casts = [
        'status_date' => 'date',
    ];

    /* ── Relationships ── */
    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function propertyMaster()
    {
        return $this->belongsTo(PropertyMaster::class, 'property_master_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function getTargetNameAttribute(): string
    {
        return $this->propertyMaster ? $this->propertyMaster->property_name : ($this->property ? $this->property->property_name : '—');
    }

    public function getTargetCodeAttribute(): string
    {
        return $this->propertyMaster ? ($this->propertyMaster->property_code ?? '—') : ($this->property ? ($this->property->property_code ?? $this->property->unit_no ?? '—') : '—');
    }

    public function getTargetLocationAttribute(): string
    {
        return $this->propertyMaster ? ($this->propertyMaster->location ?? $this->propertyMaster->city ?? '—') : ($this->property ? ($this->property->location ?? '—') : '—');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /* ── Status helpers ── */
    public static function statuses(): array
    {
        return [
            'available'          => 'Available',
            'booked'             => 'Booked',
            'sold'               => 'Sold',
            'rented'             => 'Rented',
            'reserved'           => 'Reserved',
            'under_maintenance'  => 'Under Maintenance',
        ];
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            'available'         => 'success',
            'booked'            => 'blue',
            'sold'              => 'danger',
            'rented'            => 'orange',
            'reserved'          => 'purple',
            'under_maintenance' => 'grey',
            default             => 'grey',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }
}
