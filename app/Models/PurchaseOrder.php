<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'project_id',
        'contractor_id',
        'po_number',
        'vendor_id',
        'po_date',
        'delivery_date',
        'status',
        'sub_total',
        'discount_amount',
        'taxable_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'grand_total',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'po_date' => 'date',
        'delivery_date' => 'date',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function contractors()
    {
        return $this->belongsToMany(Contractor::class, 'contractor_purchase_order', 'purchase_order_id', 'contractor_id')->withTimestamps();
    }

    public function getAllContractorsAttribute()
    {
        if ($this->relationLoaded('contractors') && $this->contractors->isNotEmpty()) {
            return $this->contractors;
        }
        $cons = $this->contractors()->get();
        if ($cons->isNotEmpty()) {
            return $cons;
        }
        if ($this->contractor_id && $this->contractor) {
            return collect([$this->contractor]);
        }
        return collect();
    }

    public function getContractorNamesAttribute(): string
    {
        $names = $this->all_contractors->pluck('contractor_name')->filter()->values();
        return $names->isNotEmpty() ? $names->implode(', ') : ($this->contractor->contractor_name ?? '—');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
