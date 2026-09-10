<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'property_master_id',
        'project_id',
        'property_type_id',
        'property_code',
        'property_name',
        'location',
        'address',
        'city',
        'size',
        'size_unit',
        'unit_no',
        'floor_no',
        'facing',
        'price',
        'purchase_rate',
        'purchase_date',
        'status',
        'description',
        'main_image',
        'document_file',
    ];

    protected $casts = [
        'price'         => 'decimal:2',
        'purchase_rate' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function propertyMaster()
    {
        return $this->belongsTo(PropertyMaster::class, 'property_master_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function documents()
    {
        return $this->hasMany(\App\Models\PropertyDocument::class);
    }

    public function rentalPayments()
    {
        return $this->hasMany(RentalPayment::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function getFormattedSizeAttribute(): string
    {
        if (empty($this->size)) {
            return '-';
        }

        $sizeStr = trim((string) $this->size);

        // If size already contains descriptive text/units (e.g. "1255 sq.ft Built Up", "530 sq.ft"), return as-is
        if (preg_match('/[a-zA-Z]/', $sizeStr)) {
            return $sizeStr;
        }

        // If purely numeric, append size_unit if valid and not a project/firm name
        $unit = trim((string) ($this->size_unit ?? ''));
        if (!empty($unit)) {
            $invalidUnits = ['galaxy homes', 'delawala', 'default', 'none', 'null'];
            if (!in_array(strtolower($unit), $invalidUnits) && !Project::where('project_name', $unit)->exists()) {
                return $sizeStr . ' ' . $unit;
            }
        }

        return $sizeStr . ' sq.ft';
    }
}
