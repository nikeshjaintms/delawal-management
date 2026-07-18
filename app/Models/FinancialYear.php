<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialYear extends Model
{
    protected $fillable = [
        'year_name', 'start_date', 'end_date', 'is_active', 'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    public function invoiceSettings()
    {
        return $this->hasMany(InvoiceSetting::class);
    }

    /** Deactivate all other years before activating this one */
    public function activateExclusively(): void
    {
        self::where('id', '!=', $this->id)->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }

    public static function activeYear(): ?self
    {
        return self::where('is_active', true)->first();
    }
}
