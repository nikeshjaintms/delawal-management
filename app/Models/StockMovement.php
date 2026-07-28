<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'material_id',
        'reference_type',
        'reference_id',
        'qty_in',
        'qty_out',
        'qty_damaged',
        'balance_stock',
        'balance_damaged',
        'remarks',
        'created_by',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
