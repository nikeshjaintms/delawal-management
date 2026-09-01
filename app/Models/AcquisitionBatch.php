<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcquisitionBatch extends Model
{
    use \App\Traits\HasFirms;

    protected $table = 'acquisition_batches';

    protected $fillable = [
        'firm_id',
        'property_master_id',
        'batch_name',
        'batch_number',
        'purchase_date',
        'purchase_rate',
        'rate_unit',
        'total_plots',
        'total_purchase_amount',
        'status',
        'description',
        'document_file',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_date'         => 'date',
        'purchase_rate'         => 'decimal:2',
        'total_purchase_amount' => 'decimal:2',
        'total_plots'           => 'integer',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function propertyMaster()
    {
        return $this->belongsTo(PropertyMaster::class, 'property_master_id');
    }

    public function plots()
    {
        return $this->hasMany(Property::class, 'acquisition_batch_id')
            ->orderByRaw('CAST(COALESCE(NULLIF(unit_no, ""), id) AS UNSIGNED) ASC, id ASC');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'acquisition_batch_id')
            ->orderByRaw('CAST(COALESCE(NULLIF(unit_no, ""), id) AS UNSIGNED) ASC, id ASC');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getAvailablePlotsCountAttribute()
    {
        return $this->plots()->whereNull('project_id')->where('status', 'available')->count();
    }

    public function getAssignedPlotsCountAttribute()
    {
        return $this->plots()->whereNotNull('project_id')->count();
    }
}
