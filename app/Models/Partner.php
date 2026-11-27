<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'firm_id',
        'name',
        'mobile',
        'email',
        'is_silent',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'is_silent' => 'boolean',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function shares()
    {
        return $this->hasMany(PartnerShare::class)->latest('id');
    }

    public function payouts()
    {
        return $this->hasMany(PartnerPayout::class)->latest('payout_date')->latest('id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getPartnerTypeLabelAttribute(): string
    {
        return $this->is_silent ? 'Silent Partner (Off-Paper)' : 'Active / On-Paper Partner';
    }
}
