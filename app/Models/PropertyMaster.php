<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyMaster extends Model
{
    use \App\Traits\HasFirms;

    protected $table = 'property_masters';

    const PROPERTY_TYPES = [
        'Land'        => 'Land',
        'Plot'        => 'Plot',
        'House'       => 'House',
        'Flat'        => 'Flat',
        'Commercial'  => 'Commercial',
        'Villa'       => 'Villa',
        'Farmhouse'   => 'Farmhouse',
        'Industrial'  => 'Industrial',
        'Other'       => 'Other',
    ];

    protected $fillable = [
        'firm_id',
        'property_name',
        'property_code',
        'property_type',
        'purchase_price',
        'paid_amount',
        'due_amount',
        'purchase_date',
        'purchase_rate',
        'total_area',
        'area_unit',
        'total_units_count',
        'unit_numbers_list',
        'unit_prefix',
        'seller_name',
        'vendor_id',
        'broker_id',
        'broker_name',
        'broker_commission_type',
        'broker_commission_rate',
        'broker_commission_amount',
        'broker_commission_paid',
        'broker_commission_due',
        'broker_commission_payment_mode',
        'broker_commission_status',
        'broker_notes',
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
        'purchase_price'           => 'decimal:2',
        'paid_amount'              => 'decimal:2',
        'due_amount'               => 'decimal:2',
        'purchase_rate'            => 'decimal:2',
        'total_area'               => 'decimal:2',
        'total_units_count'        => 'integer',
        'broker_commission_rate'   => 'decimal:2',
        'broker_commission_amount' => 'decimal:2',
        'broker_commission_paid'   => 'decimal:2',
        'broker_commission_due'    => 'decimal:2',
        'purchase_date'            => 'date',
    ];

    /**
     * Parse a human string like "1-10, 30, 35" or "1 to 10, 30, 35" into an array of individual unit numbers.
     */
    public static function parseUnitNumbersString(?string $input): array
    {
        if (empty($input)) {
            return [];
        }

        // Normalize separators: replace " to ", " TO ", " - ", "..", etc.
        $rawSegments = preg_split('/[,;\n\r]+/', $input);
        $units = [];

        foreach ($rawSegments as $segment) {
            $segment = trim($segment);
            if ($segment === '') continue;

            // Match patterns like "1 to 10", "1-10", "1..10" or "A-1 to A-10" or "Plot 1 to Plot 10"
            if (preg_match('/^(.*?)\s*(\d+)\s*(?:-|to|\.\.)\s*(?:.*?)(\d+)$/i', $segment, $m)) {
                $prefix = trim($m[1]);
                $start = (int)$m[2];
                $end = (int)$m[3];

                if ($start <= $end && ($end - $start) <= 500) {
                    for ($i = $start; $i <= $end; $i++) {
                        $units[] = ($prefix ? $prefix . ' ' : '') . $i;
                    }
                } elseif ($start > $end && ($start - $end) <= 500) {
                    for ($i = $start; $i >= $end; $i--) {
                        $units[] = ($prefix ? $prefix . ' ' : '') . $i;
                    }
                } else {
                    $units[] = $segment;
                }
            } else {
                $units[] = $segment;
            }
        }

        return array_values(array_unique(array_filter(array_map('trim', $units))));
    }

    /**
     * Get array of parsed unit numbers
     */
    public function getParsedUnitNumbersAttribute(): array
    {
        return self::parseUnitNumbersString($this->unit_numbers_list);
    }

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

    public function payments()
    {
        return $this->hasMany(PropertyMasterPayment::class, 'property_master_id')
            ->orderBy('payment_date', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Recalculate and update paid_amount, due_amount, payment_status based on payments table
     */
    public function recalculatePaymentStatus(): void
    {
        $totalPaid = (float)$this->payments()->sum('amount');
        $totalPrice = (float)($this->purchase_price ?? 0);
        $due = max(0, $totalPrice - $totalPaid);

        if ($totalPrice > 0) {
            if ($totalPaid >= $totalPrice) {
                $status = 'paid';
            } elseif ($totalPaid > 0) {
                $status = 'partial';
            } else {
                $status = 'unpaid';
            }
        } else {
            $status = $totalPaid > 0 ? 'paid' : 'unpaid';
        }

        $latestPayment = $this->payments()->latest('payment_date')->first();

        $this->updateQuietly([
            'paid_amount'    => $totalPaid,
            'due_amount'     => $due,
            'payment_status' => $status,
            'payment_mode'   => $latestPayment ? $latestPayment->payment_mode : $this->payment_mode,
        ]);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
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
