<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'vendor_id',
        'property_id',
        'property_name',
        'property_type',
        'property_code',
        'location',
        'address',
        'survey_no',
        'tp_no',
        'fp_no',
        'area',
        'area_unit',
        'item_name',
        'purchase_date',
        'purchase_amount',
        'paid_amount',
        'due_amount',
        'quantity',
        'payment_mode',
        'payment_status',
        'reference_no',
        'remarks',
        'status',
    ];

    protected $casts = [
        'purchase_date'   => 'date',
        'purchase_amount' => 'decimal:2',
        'paid_amount'     => 'decimal:2',
        'due_amount'      => 'decimal:2',
    ];

    public function firm()     { return $this->belongsTo(Firm::class); }
    public function vendor()   { return $this->belongsTo(Vendor::class); }
    public function property() { return $this->belongsTo(Property::class); }

    /**
     * Get display title: property_name or fallback to item_name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->property_name ?: ($this->item_name ?: 'Property Purchase');
    }

    /**
     * Get percentage of purchase price paid
     */
    public function getPaidPercentageAttribute(): float
    {
        $price = (float)($this->purchase_amount ?? 0);
        $paid  = (float)($this->paid_amount ?? 0);
        if ($price <= 0) {
            return $paid > 0 ? 100.0 : 0.0;
        }
        return min(100.0, round(($paid / $price) * 100, 1));
    }
}
