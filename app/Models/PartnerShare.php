<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerShare extends Model
{
    public const SCOPE_FIRM            = 'firm';
    public const SCOPE_PROJECT         = 'project';
    public const SCOPE_PROPERTY_MASTER = 'property_master';
    public const SCOPE_PROPERTY        = 'property';

    public const SCOPES = [
        self::SCOPE_FIRM            => 'Entire Firm',
        self::SCOPE_PROJECT         => 'Project',
        self::SCOPE_PROPERTY_MASTER => 'Property / Land Master',
        self::SCOPE_PROPERTY        => 'Individual Plot / Unit',
    ];

    protected $fillable = [
        'firm_id',
        'partner_id',
        'scope_type',
        'project_id',
        'property_master_id',
        'property_id',
        'share_percentage',
        'effective_from',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'share_percentage' => 'decimal:2',
        'effective_from'   => 'date',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function propertyMaster()
    {
        return $this->belongsTo(PropertyMaster::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function payouts()
    {
        return $this->hasMany(PartnerPayout::class, 'partner_share_id')->latest('payout_date')->latest('id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getScopeLabelAttribute(): string
    {
        return self::SCOPES[$this->scope_type] ?? ucfirst(str_replace('_', ' ', $this->scope_type));
    }

    public function getTargetNameAttribute(): string
    {
        return match ($this->scope_type) {
            self::SCOPE_FIRM            => $this->firm?->firm_name ?? 'Firm #' . $this->firm_id,
            self::SCOPE_PROJECT         => $this->project?->project_name ?? 'Project #' . $this->project_id,
            self::SCOPE_PROPERTY_MASTER => $this->propertyMaster?->property_name ?? 'Property #' . $this->property_master_id,
            self::SCOPE_PROPERTY        => $this->property?->property_name ?? 'Unit #' . $this->property_id,
            default                     => '—',
        };
    }
}
