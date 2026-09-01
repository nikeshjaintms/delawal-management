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

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function acquisitionBatches()
    {
        return $this->hasMany(AcquisitionBatch::class, 'property_master_id');
    }

    public function plots()
    {
        return $this->hasMany(Property::class, 'property_master_id')
            ->orderByRaw('CAST(COALESCE(NULLIF(unit_no, ""), id) AS UNSIGNED) ASC, id ASC');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'property_id');
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
