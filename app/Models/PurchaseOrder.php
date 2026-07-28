<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
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
