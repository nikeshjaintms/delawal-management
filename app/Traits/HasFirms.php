<?php

namespace App\Traits;

use App\Models\Firm;
use Illuminate\Support\Facades\Auth;

trait HasFirms
{
    /**
     * Boot the HasFirms trait for an Eloquent model.
     * Automatically assigns firm_id on model creation if available in session or auth context.
     */
    protected static function bootHasFirms(): void
    {
        static::creating(function ($model) {
            if (empty($model->firm_id)) {
                if (session()->has('firm_id') && session('firm_id')) {
                    $model->firm_id = session('firm_id');
                } elseif (Auth::check() && !empty(Auth::user()->firm_id)) {
                    $model->firm_id = Auth::user()->firm_id;
                }
            }
        });
    }

    /**
     * Get all assigned firms for the model (Many-to-Many).
     */
    public function firms()
    {
        return $this->morphToMany(Firm::class, 'firmable', 'firmables')->withTimestamps();
    }

    /**
     * Get the primary firm for backward compatibility.
     */
    public function firm()
    {
        return $this->belongsTo(Firm::class, 'firm_id');
    }

    /**
     * Sync assigned firms for the model and maintain primary firm_id.
     */
    public function syncFirms($firmIds): void
    {
        $firmIds = array_filter((array) $firmIds);
        if (empty($firmIds)) {
            return;
        }

        $this->firms()->sync($firmIds);

        $primaryFirmId = reset($firmIds);
        if ($this->firm_id != $primaryFirmId) {
            $this->firm_id = $primaryFirmId;
            if ($this->exists) {
                $this->saveQuietly();
            }
        }
    }

    /**
     * Scope query to filter records by firm ID(s).
     */
    public function scopeForFirms($query, $firmIds)
    {
        $ids = array_filter((array) $firmIds);
        if (empty($ids)) {
            return $query;
        }

        return $query->where(function ($q) use ($ids) {
            $q->whereHas('firms', function ($fq) use ($ids) {
                $fq->whereIn('firms.id', $ids);
            })->orWhereIn($this->getTable() . '.firm_id', $ids);
        });
    }

    /**
     * Accessor to get comma-separated list of assigned firm names.
     */
    public function getFirmNamesAttribute(): string
    {
        if ($this->relationLoaded('firms') && $this->firms->isNotEmpty()) {
            return $this->firms->pluck('firm_name')->implode(', ');
        }
        return $this->firm->firm_name ?? '—';
    }
}
