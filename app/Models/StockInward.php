<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockInward extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id', 'material_id', 'property_id',
        'inward_date', 'quantity', 'rate', 'total_amount',
        'supplier_name', 'bill_no', 'remarks',
    ];

    public function firm()     { return $this->belongsTo(Firm::class); }
    public function material() { return $this->belongsTo(Material::class); }
    public function property() { return $this->belongsTo(Property::class); }
}
