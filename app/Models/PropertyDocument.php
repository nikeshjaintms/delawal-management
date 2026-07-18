<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyDocument extends Model
{
    protected $fillable = [
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
    public function property()
    {
        return $this->belongsTo(Property::class);
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
            'NOC',
            'Occupancy Certificate',
            'Completion Certificate',
            'Building Plan',
            'Property Tax Receipt',
            'Encumbrance Certificate',
            'Power of Attorney',
            'Lease Agreement',
            'Rental Agreement',
            'Insurance',
            'Survey Map',
            'Other',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(): bool
    {
        return $this->expiry_date
            && !$this->expiry_date->isPast()
            && $this->expiry_date->diffInDays(now()) <= 30;
    }
}
