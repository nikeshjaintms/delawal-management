<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id', 'project_id', 'contractor_id', 'material_category_id', 'material_name',
        'specification', 'unit', 'unit_price', 'total_price', 'opening_stock', 'current_stock', 'damaged_stock', 'minimum_stock', 'status',
    ];

    public function firm()             { return $this->belongsTo(Firm::class); }
    public function project()          { return $this->belongsTo(Project::class); }
    public function contractor()       { return $this->belongsTo(Contractor::class); }
    public function materialCategory() { return $this->belongsTo(MaterialCategory::class); }
    public function category()         { return $this->belongsTo(MaterialCategory::class, 'material_category_id'); }
    public function stockInwards()     { return $this->hasMany(StockInward::class); }
    public function stockOutwards()    { return $this->hasMany(StockOutward::class); }
}
