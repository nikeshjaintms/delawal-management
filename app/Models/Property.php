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
        'price' => 'decimal:2',
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
            if ($b)
                return $b;
        }
        if ($this->relationLoaded('bookingsList') && $this->bookingsList->isNotEmpty()) {
            $b = $this->bookingsList->where('status', '!=', 'cancelled')->first();
            if ($b)
                return $b;
        }
        return $this->bookingsList()->where('bookings.status', '!=', 'cancelled')->first()
            ?: $this->bookings()->where('bookings.status', '!=', 'cancelled')->first();
    }

    public function sales()
    {
        return $this->hasMany(PropertySale::class);
    }

    public function salesList()
    {
        return $this->belongsToMany(PropertySale::class, 'property_sale_property')->withTimestamps();
    }

    public function getActiveSaleAttribute()
    {
        if ($this->relationLoaded('sales') && $this->sales->isNotEmpty()) {
            $s = $this->sales->where('sale_status', '!=', 'cancelled')->first();
            if ($s)
                return $s;
        }
        if ($this->relationLoaded('salesList') && $this->salesList->isNotEmpty()) {
            $s = $this->salesList->where('sale_status', '!=', 'cancelled')->first();
            if ($s)
                return $s;
        }
        return $this->salesList()->where('property_sales.sale_status', '!=', 'cancelled')->first()
            ?: $this->sales()->where('property_sales.sale_status', '!=', 'cancelled')->first();
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function rentalsList()
    {
        return $this->belongsToMany(Rental::class, 'rental_property')->withTimestamps();
    }

    public function getActiveRentalAttribute()
    {
        if ($this->relationLoaded('rentals') && $this->rentals->isNotEmpty()) {
            $r = $this->rentals->where('rental_status', 'active')->first();
            if ($r)
                return $r;
        }
        if ($this->relationLoaded('rentalsList') && $this->rentalsList->isNotEmpty()) {
            $r = $this->rentalsList->where('rental_status', 'active')->first();
            if ($r)
                return $r;
        }
        return $this->rentalsList()->where('rentals.rental_status', 'active')->first()
            ?: $this->rentals()->where('rentals.rental_status', 'active')->first();
    }

    /**
     * Synchronize all property statuses based on active bookings, sales, and rentals.
     */
    public static function syncAllStatuses(bool $force = false): void
    {
        if (!$force && \Illuminate\Support\Facades\Cache::has('properties_statuses_synced')) {
            return;
        }
        \Illuminate\Support\Facades\Cache::put('properties_statuses_synced', true, 180);  // 3-minute cooldown

        // 1. Mark properties with active bookings as 'booked'
        \Illuminate\Support\Facades\DB::table('properties')
            ->where(function ($q) {
                $q->whereIn('id', function ($query) {
                    $query
                        ->select('property_id')
                        ->from('bookings')
                        ->where('status', '!=', 'cancelled')
                        ->whereNotNull('property_id');
                })->orWhereIn('id', function ($query) {
                    $query
                        ->select('booking_property.property_id')
                        ->from('booking_property')
                        ->join('bookings', 'booking_property.booking_id', '=', 'bookings.id')
                        ->where('bookings.status', '!=', 'cancelled');
                });
            })
            ->where('status', 'available')
            ->update(['status' => 'booked']);

        // 2. Mark properties with active property sales as 'sold'
        \Illuminate\Support\Facades\DB::table('properties')
            ->where(function ($q) {
                $q->whereIn('id', function ($query) {
                    $query
                        ->select('property_id')
                        ->from('property_sales')
                        ->where('sale_status', '!=', 'cancelled')
                        ->whereNotNull('property_id');
                })->orWhereIn('id', function ($query) {
                    $query
                        ->select('property_sale_property.property_id')
                        ->from('property_sale_property')
                        ->join('property_sales', 'property_sale_property.property_sale_id', '=', 'property_sales.id')
                        ->where('property_sales.sale_status', '!=', 'cancelled');
                });
            })
            ->whereIn('status', ['available', 'booked'])
            ->update(['status' => 'sold']);

        // 3. Mark properties with active rentals as 'rented'
        \Illuminate\Support\Facades\DB::table('properties')
            ->where(function ($q) {
                $q->whereIn('id', function ($query) {
                    $query
                        ->select('property_id')
                        ->from('rentals')
                        ->where('rental_status', 'active')
                        ->whereNotNull('property_id');
                })->orWhereIn('id', function ($query) {
                    $query
                        ->select('rental_property.property_id')
                        ->from('rental_property')
                        ->join('rentals', 'rental_property.rental_id', '=', 'rentals.id')
                        ->where('rentals.rental_status', 'active');
                });
            })
            ->where('status', '!=', 'sold')
            ->update(['status' => 'rented']);

        // 4. Revert any properties that are marked 'booked' or 'rented' but have NO active bookings, sales, or rentals
        \Illuminate\Support\Facades\DB::table('properties')
            ->whereIn('status', ['booked', 'rented'])
            ->whereNotIn('id', function ($query) {
                $query
                    ->select('property_id')
                    ->from('bookings')
                    ->where('status', '!=', 'cancelled')
                    ->whereNotNull('property_id');
            })
            ->whereNotIn('id', function ($query) {
                $query
                    ->select('booking_property.property_id')
                    ->from('booking_property')
                    ->join('bookings', 'booking_property.booking_id', '=', 'bookings.id')
                    ->where('bookings.status', '!=', 'cancelled');
            })
            ->whereNotIn('id', function ($query) {
                $query
                    ->select('property_id')
                    ->from('property_sales')
                    ->where('sale_status', '!=', 'cancelled')
                    ->whereNotNull('property_id');
            })
            ->whereNotIn('id', function ($query) {
                $query
                    ->select('property_sale_property.property_id')
                    ->from('property_sale_property')
                    ->join('property_sales', 'property_sale_property.property_sale_id', '=', 'property_sales.id')
                    ->where('property_sales.sale_status', '!=', 'cancelled');
            })
            ->whereNotIn('id', function ($query) {
                $query
                    ->select('property_id')
                    ->from('rentals')
                    ->where('rental_status', 'active')
                    ->whereNotNull('property_id');
            })
            ->whereNotIn('id', function ($query) {
                $query
                    ->select('rental_property.property_id')
                    ->from('rental_property')
                    ->join('rentals', 'rental_property.rental_id', '=', 'rentals.id')
                    ->where('rentals.rental_status', 'active');
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
                if ($cmp !== 0)
                    return $cmp;
            }

            $unitA = trim((string) ($a->unit_no ?? ''));
            $unitB = trim((string) ($b->unit_no ?? ''));
            if ($unitA !== '' && $unitB !== '') {
                $cmp = strnatcasecmp($unitA, $unitB);
                if ($cmp !== 0)
                    return $cmp;
            }

            return ($a->id ?? 0) <=> ($b->id ?? 0);
        })->values();
    }

    /**
     * Get the effective purchase / acquisition cost of this unit.
     */
    public function getEffectivePurchaseCostAttribute(): float
    {
        // 1. If linked to a PropertyMaster
        if ($this->propertyMaster) {
            $pm = $this->propertyMaster;
            $pmPurchasePrice = (float) ($pm->purchase_price ?? 0);
            if ($pmPurchasePrice <= 0 && (float) ($pm->purchase_rate ?? 0) > 0 && (float) ($pm->total_area ?? 0) > 0) {
                $pmPurchasePrice = round((float) $pm->purchase_rate * (float) $pm->total_area, 2);
            }

            $totalPlots = max(1, (int) ($pm->total_units_count ?: $pm->plots()->count()));
            $isEntire = ($totalPlots <= 1) || str_contains((string) $this->property_name, '(Entire Property)') || str_ends_with((string) $this->property_code, '-ENTIRE');

            // Entire property master
            if ($isEntire) {
                if ($pmPurchasePrice > 0)
                    return $pmPurchasePrice;
                if ((float) ($this->price ?? 0) > 0)
                    return (float) $this->price;
            }

            // Sub-unit / Individual Plot under PropertyMaster
            if ($totalPlots > 1 && $pmPurchasePrice > 0) {
                return round($pmPurchasePrice / $totalPlots, 2);
            }
            if ((float) ($this->price ?? 0) > 0) {
                return (float) $this->price;
            }
            if ($pmPurchasePrice > 0) {
                return $pmPurchasePrice;
            }
        }

        // 2. If individual property with size and purchase_rate
        if ((float) ($this->purchase_rate ?? 0) > 0 && (float) ($this->size ?? 0) > 0) {
            return round((float) $this->purchase_rate * (float) $this->size, 2);
        }

        // 3. If price is set
        if ((float) ($this->price ?? 0) > 0) {
            return (float) $this->price;
        }

        // 4. Fallback to purchase_rate
        if ((float) ($this->purchase_rate ?? 0) > 0) {
            return (float) $this->purchase_rate;
        }

        return 0.0;
    }

    /**
     * Get total expenses attached to this property.
     */
    public function getTotalExpensesAttribute(): float
    {
        $pm = $this->property_master_id ? ($this->propertyMaster ?: \App\Models\PropertyMaster::find($this->property_master_id)) : null;
        $totalPlots = $pm ? max(1, (int) ($pm->total_units_count ?: $pm->plots()->count())) : 1;
        $isEntire = ($pm && $totalPlots <= 1) || str_contains((string) $this->property_name, '(Entire Property)') || str_ends_with((string) $this->property_code, '-ENTIRE');

        // 1. If this property represents an Entire Property Master
        if ($isEntire && $pm) {
            return (float) $pm->total_expenses;
        }

        // 2. Direct expenses on this individual plot (avoiding double counting between direct property_id column and expense_property pivot)
        $thisDirect = (float) Expense::where(function ($q) {
            $q
                ->where('property_id', $this->id)
                ->orWhereIn('id', function ($sub) {
                    $sub
                        ->select('expense_id')
                        ->from('expense_property')
                        ->where('property_id', $this->id);
                });
        })->sum('amount');

        // 3. If under PropertyMaster with multiple plots, allocate proportional share of general PM / linked project expenses
        if ($pm && $totalPlots > 1) {
            $allPlotIds = $pm->plots()->pluck('id')->toArray();
            $allPlotsDirectTotal = (float) Expense::where(function ($q) use ($allPlotIds) {
                $q
                    ->whereIn('property_id', $allPlotIds)
                    ->orWhereIn('id', function ($sub) use ($allPlotIds) {
                        $sub
                            ->select('expense_id')
                            ->from('expense_property')
                            ->whereIn('property_id', $allPlotIds);
                    });
            })->sum('amount');
            $generalPmExpenses = max(0, $pm->total_expenses - $allPlotsDirectTotal);
            $allocatedPmShare = round($generalPmExpenses / $totalPlots, 2);
            return round($thisDirect + $allocatedPmShare, 2);
        }

        // 4. If this unit belongs to a standalone Project, add its proportional share of general project expenses
        if ($this->project_id) {
            $projUnitsCount = static::where('project_id', $this->project_id)->count();
            if ($projUnitsCount > 0) {
                $generalProjExpenses = (float) Expense::where('project_id', $this->project_id)
                    ->whereNull('property_id')
                    ->whereNotIn('id', function ($q) {
                        $q->select('expense_id')->from('expense_property');
                    })
                    ->sum('amount');
                if ($generalProjExpenses > 0) {
                    return round($thisDirect + round($generalProjExpenses / $projUnitsCount, 2), 2);
                }
            }
        }

        return round($thisDirect, 2);
    }
}
