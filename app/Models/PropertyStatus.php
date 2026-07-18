<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyStatus extends Model
{
    protected $fillable = [
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
    public function property()
    {
        return $this->belongsTo(Property::class);
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
