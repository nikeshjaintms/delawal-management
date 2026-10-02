<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertySale extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'property_id',
        'customer_id',
        'seller_id',
        'seller_name',
        'broker_id',
        'broker_name',
        'purchase_date',
        'purchase_cost',
        'property_expenses',
        'total_area',
        'area_unit',
        'purchase_rate',
        'sell_rate',
        'broker_commission_type',
        'broker_commission_rate',
        'broker_commission_amount',
        'broker_commission_paid',
        'broker_commission_due',
        'broker_commission_payment_mode',
        'broker_commission_payment_date',
        'broker_commission_status',
        'broker_notes',
        'sale_date',
        'invoice_no',
        'sale_amount',
        'taxable_amount',
        'cgst_rate',
        'cgst_amount',
        'sgst_rate',
        'sgst_amount',
        'igst_rate',
        'igst_amount',
        'total_gst',
        'grand_total',
        'hsn_code',
        'booking_amount',
        'remaining_amount',
        'payment_status',
        'sale_status',
        'agreement_file',
        'note',
    ];

    protected $casts = [
        'purchase_date'                  => 'date',
        'purchase_cost'                  => 'decimal:2',
        'property_expenses'              => 'decimal:2',
        'total_area'                     => 'decimal:2',
        'purchase_rate'                  => 'decimal:2',
        'sell_rate'                      => 'decimal:2',
        'sale_date'                      => 'date',
        'sale_amount'                    => 'decimal:2',
        'booking_amount'                 => 'decimal:2',
        'remaining_amount'               => 'decimal:2',
        'grand_total'                    => 'decimal:2',
        'broker_commission_rate'         => 'decimal:2',
        'broker_commission_amount'       => 'decimal:2',
        'broker_commission_paid'         => 'decimal:2',
        'broker_commission_due'          => 'decimal:2',
        'broker_commission_payment_date' => 'date',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_sale_property')->withTimestamps();
    }

    public function getAllPropertiesAttribute()
    {
        if ($this->relationLoaded('properties') && $this->properties->isNotEmpty()) {
            return $this->properties;
        }
        if ($this->properties()->exists()) {
            return $this->properties;
        }
        return $this->property ? collect([$this->property]) : collect([]);
    }

    public function getPropertyNamesAttribute(): string
    {
        $props = $this->all_properties;
        if ($props->isNotEmpty()) {
            return $props->pluck('property_name')->implode(', ');
        }
        return $this->property->property_name ?? '—';
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'property_sale_id')->latest('payment_date')->latest('id');
    }

    /**
     * Recalculate paid_amount, remaining_amount, and payment_status based on payments table
     */
    public function recalculatePaymentStatus(): void
    {
        $saleTotal = (float)($this->sale_amount ?? 0);
        $paymentsCount = $this->payments()->count();

        if ($paymentsCount > 0) {
            $paid = (float)$this->payments()->sum('payment_amount');
        } else {
            $paid = (float)($this->booking_amount ?? 0);
        }

        $due = max(0.00, round($saleTotal - $paid, 2));

        $status = 'pending';
        if ($saleTotal > 0) {
            if ($paid >= $saleTotal) {
                $status = 'paid';
            } elseif ($paid > 0) {
                $status = 'partial';
            }
        }

        $this->updateQuietly([
            'booking_amount'   => $paid,
            'remaining_amount' => $due,
            'payment_status'   => $status,
        ]);
    }

    /**
     * Get percentage of sale amount paid
     */
    public function getPaidPercentageAttribute(): float
    {
        $price = (float)($this->sale_amount ?? $this->grand_total ?? 0);
        $paid  = (float)($this->booking_amount ?? 0);
        if ($price <= 0) {
            return $paid > 0 ? 100.0 : 0.0;
        }
        return min(100.0, round(($paid / $price) * 100, 1));
    }

    /**
     * Effective Acquisition / Purchase Date
     */
    public function getEffectivePurchaseDateAttribute()
    {
        if ($this->purchase_date) {
            return $this->purchase_date;
        }
        $propDate = $this->all_properties->pluck('purchase_date')->filter()->min();
        if ($propDate) {
            return $propDate;
        }
        return $this->property?->propertyMaster?->purchase_date;
    }

    /**
     * Holding Duration (e.g. 2 Years 3 Months)
     */
    public function getHeldDurationTextAttribute(): string
    {
        $pDate = $this->effective_purchase_date;
        $sDate = $this->sale_date;
        if (!$pDate || !$sDate) {
            return 'Duration N/A';
        }

        $p = \Carbon\Carbon::parse($pDate);
        $s = \Carbon\Carbon::parse($sDate);
        if ($p->gt($s)) {
            return 'Same day / Recent';
        }

        $diffYears = (int)$p->diffInYears($s);
        $diffMonths = (int)($p->copy()->addYears($diffYears)->diffInMonths($s));
        $diffDays = (int)($p->copy()->addYears($diffYears)->addMonths($diffMonths)->diffInDays($s));

        if ($diffYears >= 1) {
            return "Held for {$diffYears} yr" . ($diffYears > 1 ? 's ' : ' ') . ($diffMonths > 0 ? "{$diffMonths} mo" . ($diffMonths > 1 ? 's' : '') : '');
        } elseif ($diffMonths >= 1) {
            return "Held for {$diffMonths} month" . ($diffMonths > 1 ? 's ' : ' ') . ($diffDays > 0 ? "{$diffDays} d" : '');
        } else {
            $days = max(1, $p->diffInDays($s));
            return "Held for {$days} day" . ($days > 1 ? 's' : '');
        }
    }

    /**
     * Total Purchase Cost of all assigned properties in this sale
     */
    public function getTotalPurchaseCostAttribute(): float
    {
        if (!is_null($this->purchase_cost) && (float)$this->purchase_cost > 0) {
            return (float)$this->purchase_cost;
        }
        $props = $this->all_properties;
        if ($props->isEmpty()) {
            return 0.0;
        }
        $total = 0.0;
        foreach ($props as $p) {
            $total += (float)($p->effective_purchase_cost ?? 0);
        }
        return round($total, 2);
    }

    /**
     * Total Incurred Property Expenses
     */
    public function getTotalPropertyExpensesAttribute(): float
    {
        if (!is_null($this->property_expenses)) {
            return (float)$this->property_expenses;
        }
        $props = $this->all_properties;
        if ($props->isEmpty()) {
            return 0.0;
        }
        $total = 0.0;
        foreach ($props as $p) {
            $total += (float)($p->total_expenses ?? 0);
        }
        return round($total, 2);
    }

    /**
     * Total Invested Basis = Purchase Cost + Incurred Property Expenses
     */
    public function getTotalCostBasisAttribute(): float
    {
        return round($this->total_purchase_cost + $this->total_property_expenses, 2);
    }

    /**
     * Gross Profit = Total Sale Value - Total Purchase Cost - Property Expenses
     */
    public function getGrossProfitAttribute(): float
    {
        $sale = (float)($this->sale_amount ?? $this->grand_total ?? 0);
        $costBasis = $this->total_cost_basis;
        return round($sale - $costBasis, 2);
    }

    /**
     * Net Profit = Total Sale Value - Total Purchase Cost - Property Expenses - Broker Commission
     */
    public function getNetProfitAttribute(): float
    {
        $sale = (float)($this->sale_amount ?? $this->grand_total ?? 0);
        $costBasis = $this->total_cost_basis;
        $comm = (float)($this->broker_commission_amount ?? 0);
        return round($sale - $costBasis - $comm, 2);
    }

    /**
     * Profit Margin % = (Net Profit / Total Sale Value) * 100
     */
    public function getProfitMarginPercentageAttribute(): float
    {
        $sale = (float)($this->sale_amount ?? $this->grand_total ?? 0);
        if ($sale <= 0) {
            return 0.0;
        }
        return round(($this->net_profit / $sale) * 100, 2);
    }

    /**
     * ROI % (Return on Investment) = (Net Profit / Total Invested Cost) * 100
     */
    public function getRoiPercentageAttribute(): float
    {
        $costBasis = $this->total_cost_basis;
        if ($costBasis <= 0) {
            return 0.0;
        }
        return round(($this->net_profit / $costBasis) * 100, 2);
    }

    /**
     * Profit Status: 'profit', 'loss', or 'breakeven'
     */
    public function getProfitStatusAttribute(): string
    {
        $np = $this->net_profit;
        if ($np > 0.01) {
            return 'profit';
        } elseif ($np < -0.01) {
            return 'loss';
        }
        return 'breakeven';
    }

    /**
     * Get broker display name (from relation or manual broker_name)
     */
    public function getBrokerDisplayNameAttribute(): string
    {
        return $this->broker->name ?? ($this->broker_name ?: 'Direct / None');
    }
}
