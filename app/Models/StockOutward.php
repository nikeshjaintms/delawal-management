<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOutward extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id', 'project_id', 'contractor_id', 'outward_number', 'stock_inward_number', 'material_id', 'property_id',
        'outward_date', 'quantity', 'vehicle_no', 'driver_name', 'lr_no', 'transport_name', 'used_for', 'remarks',
    ];

    protected $casts = [
        'outward_date' => 'date',
    ];

    public function firm()       { return $this->belongsTo(Firm::class); }
    public function project()    { return $this->belongsTo(Project::class); }
    public function contractor() { return $this->belongsTo(Contractor::class); }
    public function material()   { return $this->belongsTo(Material::class); }
    public function property()   { return $this->belongsTo(Property::class); }
}
