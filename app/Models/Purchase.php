<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'vendor_id',
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
        'quantity',
        'payment_mode',
        'payment_status',
        'reference_no',
        'remarks',
        'status',
    ];

    public function firm()   { return $this->belongsTo(Firm::class); }
    public function vendor() { return $this->belongsTo(Vendor::class); }

    /**
     * Get display title: property_name or fallback to item_name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->property_name ?: ($this->item_name ?: 'Property Purchase');
    }
}
