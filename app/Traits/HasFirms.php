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
     * Get the firm that owns the model.
     */
    public function firm()
    {
        return $this->belongsTo(Firm::class, 'firm_id');
    }
}
