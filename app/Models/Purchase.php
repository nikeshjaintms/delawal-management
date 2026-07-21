<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id', 'vendor_id', 'item_name', 'purchase_date',
        'purchase_amount', 'quantity', 'payment_mode',
        'payment_status', 'reference_no', 'remarks', 'status',
    ];

    public function firm()   { return $this->belongsTo(Firm::class); }
    public function vendor() { return $this->belongsTo(Vendor::class); }
}
