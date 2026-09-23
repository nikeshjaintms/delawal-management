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

    public function contractors()
    {
        return $this->belongsToMany(Contractor::class, 'contractor_property', 'property_id', 'contractor_id')->withTimestamps();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function expensesList()
    {
        return $this->belongsToMany(Expense::class, 'expense_property')->withTimestamps();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function bookingsList()
    {
        return $this->belongsToMany(Booking::class, 'booking_property')->withTimestamps();
    }

    public function getActiveBookingAttribute()
    {
        if ($this->relationLoaded('bookings') && $this->bookings->isNotEmpty()) {
            $b = $this->bookings->where('status', '!=', 'cancelled')->first();
            if ($b) return $b;
        }
        if ($this->relationLoaded('bookingsList') && $this->bookingsList->isNotEmpty()) {
            $b = $this->bookingsList->where('status', '!=', 'cancelled')->first();
            if ($b) return $b;
        }
        return $this->bookingsList()->where('bookings.status', '!=', 'cancelled')->first()
            ?: $this->bookings()->where('bookings.status', '!=', 'cancelled')->first();
    }

    public function sales()
    {
        return $this->hasMany(PropertySale::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function getActiveRentalAttribute()
    {
        if ($this->relationLoaded('rentals') && $this->rentals->isNotEmpty()) {
            $r = $this->rentals->where('rental_status', 'active')->first();
            if ($r) return $r;
        }
        return $this->rentals()->where('rental_status', 'active')->first();
    }

    /**
     * Synchronize all property statuses based on active bookings, sales, and rentals.
     */
    public static function syncAllStatuses(): void
    {
        // 1. Mark properties with active bookings as 'booked'
        \Illuminate\Support\Facades\DB::table('properties')
            ->where(function ($q) {
                $q->whereIn('id', function ($query) {
                    $query->select('property_id')
                        ->from('bookings')
                        ->where('status', '!=', 'cancelled')
                        ->whereNotNull('property_id');
                })->orWhereIn('id', function ($query) {
                    $query->select('booking_property.property_id')
                        ->from('booking_property')
                        ->join('bookings', 'booking_property.booking_id', '=', 'bookings.id')
                        ->where('bookings.status', '!=', 'cancelled');
                });
            })
            ->where('status', 'available')
            ->update(['status' => 'booked']);

        // 2. Mark properties with active property sales as 'sold'
        \Illuminate\Support\Facades\DB::table('properties')
            ->whereIn('id', function ($query) {
                $query->select('property_id')
                    ->from('property_sales')
                    ->where('sale_status', '!=', 'cancelled')
                    ->whereNotNull('property_id');
            })
            ->whereIn('status', ['available', 'booked'])
            ->update(['status' => 'sold']);

        // 3. Mark properties with active rentals as 'rented'
        \Illuminate\Support\Facades\DB::table('properties')
            ->whereIn('id', function ($query) {
                $query->select('property_id')
                    ->from('rentals')
                    ->where('rental_status', 'active')
                    ->whereNotNull('property_id');
            })
            ->where('status', '!=', 'sold')
            ->update(['status' => 'rented']);

        // 4. Revert any properties that are marked 'booked' or 'rented' but have NO active bookings, sales, or rentals
        \Illuminate\Support\Facades\DB::table('properties')
            ->whereIn('status', ['booked', 'rented'])
            ->whereNotIn('id', function ($query) {
                $query->select('property_id')
                    ->from('bookings')
                    ->where('status', '!=', 'cancelled')
                    ->whereNotNull('property_id');
            })
            ->whereNotIn('id', function ($query) {
                $query->select('booking_property.property_id')
                    ->from('booking_property')
                    ->join('bookings', 'booking_property.booking_id', '=', 'bookings.id')
                    ->where('bookings.status', '!=', 'cancelled');
            })
            ->whereNotIn('id', function ($query) {
                $query->select('property_id')
                    ->from('property_sales')
                    ->where('sale_status', '!=', 'cancelled')
                    ->whereNotNull('property_id');
            })
            ->whereNotIn('id', function ($query) {
                $query->select('property_id')
                    ->from('rentals')
                    ->where('rental_status', 'active')
                    ->whereNotNull('property_id');
            })
            ->update(['status' => 'available']);

        // 5. Ensure Property Master plots remain strictly isolated from Project module plots
        try {
            \Illuminate\Support\Facades\DB::table('properties')
                ->whereNotNull('property_master_id')
                ->whereNotNull('project_id')
                ->update(['project_id' => null]);
        } catch (\Throwable $e) {
            // Silently ignore if table does not exist
        }
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

    /**
     * Naturally sort any Property collection by property_name and unit_no in human sequential order.
     */
    public static function naturalSort($collection)
    {
        return $collection->sort(function ($a, $b) {
            // Group by property_master_id if present
            $masterA = $a->property_master_id ?? 0;
            $masterB = $b->property_master_id ?? 0;
            if ($masterA !== $masterB) {
                return $masterA <=> $masterB;
            }

            // Group by project_id if present
            $projA = $a->project_id ?? 0;
            $projB = $b->project_id ?? 0;
            if ($projA !== $projB) {
                return $projA <=> $projB;
            }

            $nameA = trim((string) ($a->property_name ?? ''));
            $nameB = trim((string) ($b->property_name ?? ''));
            if ($nameA !== '' && $nameB !== '') {
                $cmp = strnatcasecmp($nameA, $nameB);
                if ($cmp !== 0) return $cmp;
            }

            $unitA = trim((string) ($a->unit_no ?? ''));
            $unitB = trim((string) ($b->unit_no ?? ''));
            if ($unitA !== '' && $unitB !== '') {
                $cmp = strnatcasecmp($unitA, $unitB);
                if ($cmp !== 0) return $cmp;
            }

            return ($a->id ?? 0) <=> ($b->id ?? 0);
        })->values();
    }
}
