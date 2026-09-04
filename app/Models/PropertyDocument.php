<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyDocument extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'property_master_id',
        'property_id',
        'document_type',
        'document_title',
        'document_file',
        'document_number',
        'expiry_date',
        'remarks',
        'status',
        'created_by',
    ];

    protected $casts = [
        'expiry_date' => 'date',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ── Helpers ── */
    public static function documentTypes(): array
    {
        return [
            'Sale Deed',
            'Agreement to Sale',
            'Title Deed',
            '7/12 & 8-A Extract',
            'NOC',
            'NA Order / Permission',
            'Occupancy Certificate',
            'Completion Certificate',
            'Building / Layout Plan',
            'Property Tax Receipt',
            'Encumbrance Certificate',
            'Power of Attorney',
            'Lease Agreement',
            'Rental Agreement',
            'Insurance',
            'Other',
        ];
    }
}
