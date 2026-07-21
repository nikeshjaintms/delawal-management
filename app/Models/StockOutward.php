<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOutward extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id', 'material_id', 'property_id',
        'outward_date', 'quantity', 'used_for', 'remarks',
    ];

    public function firm()     { return $this->belongsTo(Firm::class); }
    public function material() { return $this->belongsTo(Material::class); }
    public function property() { return $this->belongsTo(Property::class); }
}
