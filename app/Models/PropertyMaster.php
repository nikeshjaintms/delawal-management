<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyMaster extends Model
{
    use \App\Traits\HasFirms;

    protected $table = 'property_masters';

    protected $fillable = [
        'firm_id',
        'property_name',
        'property_code',
        'purchase_price',
        'paid_amount',
        'due_amount',
        'purchase_date',
        'purchase_rate',
        'total_area',
        'area_unit',
        'seller_name',
        'vendor_id',
        'payment_mode',
        'payment_status',
        'location',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'description',
        'status',
        'main_image',
        'document_file',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'paid_amount'    => 'decimal:2',
        'due_amount'     => 'decimal:2',
        'purchase_rate'  => 'decimal:2',
        'total_area'     => 'decimal:2',
        'purchase_date'  => 'date',
    ];

    /**
     * Get payment percentage completed
     */
    public function getPaidPercentageAttribute(): float
    {
        $price = floatval($this->purchase_price ?? 0);
        $paid  = floatval($this->paid_amount ?? 0);
        if ($price <= 0) {
            return $paid > 0 ? 100.0 : 0.0;
        }
        return round(min(100, max(0, ($paid / $price) * 100)), 1);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function plots()
    {
        return $this->hasMany(Property::class, 'property_master_id')
            ->orderByRaw('CAST(COALESCE(NULLIF(unit_no, ""), id) AS UNSIGNED) ASC, id ASC');
    }

    public function bulkPlots()
    {
        return $this->hasMany(Property::class, 'property_master_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_property_master', 'property_master_id', 'project_id')
            ->withTimestamps();
    }

    /**
     * Get all projects linked via pivot table, direct property_id, or assigned plots
     */
    public function getAllProjectsAttribute()
    {
        $pivot = $this->relationLoaded('projects') ? $this->projects : $this->projects()->get();
        if ($pivot->isNotEmpty()) {
            return $pivot;
        }

        $directProjects = Project::where('property_id', $this->id)->get();
        $plotProjectIds = Property::where('property_master_id', $this->id)
            ->whereNotNull('project_id')
            ->pluck('project_id')
            ->unique()
            ->toArray();
        $plotProjects = !empty($plotProjectIds) ? Project::whereIn('id', $plotProjectIds)->get() : collect();

        return $directProjects->concat($plotProjects)->unique('id')->values();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get complete formatted property address.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [];
        if (!empty($this->address)) {
            $parts[] = $this->address;
        }
        if (!empty($this->location) && strpos($this->address ?? '', $this->location) === false) {
            $parts[] = $this->location;
        }
        if (!empty($this->city) && strpos($this->address ?? '', $this->city) === false) {
            $parts[] = $this->city;
        }
        if (!empty($this->state) && strpos($this->address ?? '', $this->state) === false) {
            $parts[] = $this->state;
        }
        if (!empty($this->pincode)) {
            $lastIndex = count($parts) - 1;
            if ($lastIndex >= 0) {
                $parts[$lastIndex] .= ' - ' . $this->pincode;
            } else {
                $parts[] = $this->pincode;
            }
        }
        return implode(', ', $parts);
    }

    /**
     * Get the highest existing plot sequence number across all acquisition batches for this property.
     */
    public function getHighestPlotSequenceNumber(): int
    {
        $plots = Property::where('property_master_id', $this->id)->get();
        if ($plots->isEmpty()) {
            return 0;
        }

        $maxNumber = 0;
        foreach ($plots as $plot) {
            // 1. Check unit_no if numeric
            if (is_numeric($plot->unit_no) && (int)$plot->unit_no > $maxNumber) {
                $maxNumber = (int)$plot->unit_no;
            }

            // 2. Check property_name if ends in number (e.g. "Plot 14")
            if (preg_match('/(\d+)\s*$/', (string)$plot->property_name, $m)) {
                $num = (int)$m[1];
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }

            // 3. Check property_code (e.g. "P-AMAN-B6-015")
            if (preg_match('/-(\d+)$/', (string)$plot->property_code, $m)) {
                $num = (int)$m[1];
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        return $maxNumber;
    }

    /**
     * Get the next starting plot sequence number for this property.
     */
    public function getNextPlotSequenceNumber(): int
    {
        return $this->getHighestPlotSequenceNumber() + 1;
    }
}
