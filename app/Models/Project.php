<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'property_id',
        'project_name',
        'project_code',
        'project_type',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'description',
        'status',
        'project_image',
        'created_by',
        'updated_by',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function propertyMasters()
    {
        return $this->belongsToMany(PropertyMaster::class, 'project_property_master', 'project_id', 'property_master_id')
            ->withTimestamps();
    }

    public function contractors()
    {
        return $this->belongsToMany(Contractor::class, 'contractor_project', 'project_id', 'contractor_id')
            ->withTimestamps();
    }

    public function syncPropertyMasters($propertyMasterIds): void
    {
        $propertyMasterIds = array_values(array_filter((array) $propertyMasterIds));
        $this->propertyMasters()->sync($propertyMasterIds);

        $primaryId = !empty($propertyMasterIds) ? $propertyMasterIds[0] : null;
        if ($this->property_id != $primaryId) {
            $this->property_id = $primaryId;
            if ($this->exists) {
                $this->saveQuietly();
            }
        }

        if (!empty($propertyMasterIds)) {
            \App\Models\Property::whereIn('property_master_id', $propertyMasterIds)
                ->where(function ($q) {
                    $q->whereNull('project_id')->orWhere('project_id', $this->id);
                })
                ->update(['project_id' => $this->id]);
        }

        \App\Models\Property::whereNotIn('property_master_id', $propertyMasterIds)
            ->where('project_id', $this->id)
            ->whereNotNull('property_master_id')
            ->update(['project_id' => null]);
    }

    public function property()
    {
        return $this->belongsTo(PropertyMaster::class, 'property_id');
    }

    public function propertyMaster()
    {
        return $this->belongsTo(PropertyMaster::class, 'property_id');
    }

    public function getPropertyNamesAttribute(): string
    {
        if ($this->relationLoaded('propertyMasters') && $this->propertyMasters->isNotEmpty()) {
            return $this->propertyMasters->pluck('property_name')->implode(', ');
        }
        return $this->propertyMaster->property_name ?? '—';
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'project_id')
            ->whereNull('property_master_id')
            ->orderByRaw('CAST(COALESCE(NULLIF(unit_no, ""), id) AS UNSIGNED) ASC, id ASC');
    }

    public function bulks()
    {
        return $this->hasMany(Property::class, 'project_id')
            ->whereNull('property_master_id')
            ->orderByRaw('CAST(COALESCE(NULLIF(unit_no, ""), id) AS UNSIGNED) ASC, id ASC');
    }

    public function legacyContractors()
    {
        return $this->hasMany(Contractor::class, 'project_id')->latest();
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'project_id')->latest();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'project_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'project_id');
    }

    public function getTotalExpensesAttribute(): float
    {
        return (float) ($this->expenses()->sum('amount') ?? 0);
    }

    public function getPoExpensesTotalAttribute(): float
    {
        return (float) ($this->expenses()->whereNotNull('purchase_order_id')->sum('amount') ?? 0);
    }

    public function getDirectExpensesTotalAttribute(): float
    {
        return (float) ($this->expenses()->whereNull('purchase_order_id')->sum('amount') ?? 0);
    }

    /**
     * Get the highest existing plot sequence number for this project.
     */
    public function getHighestPlotSequenceNumber(): int
    {
        $plots = Property::where('project_id', $this->id)->get();
        if ($plots->isEmpty()) {
            return 0;
        }

        $maxNumber = 0;
        foreach ($plots as $plot) {
            if (is_numeric($plot->unit_no) && (int) $plot->unit_no > $maxNumber) {
                $maxNumber = (int) $plot->unit_no;
            }
            if (preg_match('/(\d+)\s*$/', (string) $plot->property_name, $m)) {
                $num = (int) $m[1];
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
            if (preg_match('/-(\d+)$/', (string) $plot->property_code, $m)) {
                $num = (int) $m[1];
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        return $maxNumber;
    }

    /**
     * Get the next starting plot sequence number for this project.
     */
    public function getNextPlotSequenceNumber(): int
    {
        return $this->getHighestPlotSequenceNumber() + 1;
    }
}

