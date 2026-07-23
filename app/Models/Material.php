<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id', 'material_category_id', 'material_name',
        'unit', 'opening_stock', 'current_stock', 'minimum_stock', 'status',
    ];

    public function firm()             { return $this->belongsTo(Firm::class); }
    public function materialCategory() { return $this->belongsTo(MaterialCategory::class); }
    public function stockInwards()     { return $this->hasMany(StockInward::class); }
    public function stockOutwards()    { return $this->hasMany(StockOutward::class); }
}
