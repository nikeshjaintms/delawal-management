<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockInward extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id', 'project_id', 'contractor_id', 'inward_number', 'purchase_order_id', 'material_id', 'property_id',
        'inward_date', 'quantity', 'qty_ordered', 'qty_damaged', 'rate',
        'gst_pct', 'gst_amount', 'discount_pct', 'discount_amount', 'total_amount',
        'supplier_name', 'bill_no', 'challan_no', 'vehicle_no', 'warehouse', 'remarks',
    ];

    protected $casts = [
        'inward_date' => 'date',
    ];

    public function firm()       { return $this->belongsTo(Firm::class); }
    public function project()    { return $this->belongsTo(Project::class); }
    public function contractor() { return $this->belongsTo(Contractor::class); }
    public function material()   { return $this->belongsTo(Material::class); }
    public function property()   { return $this->belongsTo(Property::class); }
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
}
