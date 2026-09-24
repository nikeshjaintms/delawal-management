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
        'broker_id',
        'broker_commission_type',
        'broker_commission_rate',
        'broker_commission_amount',
        'broker_commission_paid',
        'broker_commission_due',
        'broker_commission_payment_mode',
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
        'sale_date'                => 'date',
        'sale_amount'              => 'decimal:2',
        'booking_amount'           => 'decimal:2',
        'remaining_amount'         => 'decimal:2',
        'grand_total'              => 'decimal:2',
        'broker_commission_rate'   => 'decimal:2',
        'broker_commission_amount' => 'decimal:2',
        'broker_commission_paid'   => 'decimal:2',
        'broker_commission_due'    => 'decimal:2',
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
     * Total Purchase Cost of all assigned properties in this sale
     */
    public function getTotalPurchaseCostAttribute(): float
    {
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
     * Gross Profit = Total Sale Value - Total Purchase Cost
     */
    public function getGrossProfitAttribute(): float
    {
        $sale = (float)($this->sale_amount ?? $this->grand_total ?? 0);
        $cost = $this->total_purchase_cost;
        return round($sale - $cost, 2);
    }

    /**
     * Net Profit = Total Sale Value - Total Purchase Cost - Broker Commission
     */
    public function getNetProfitAttribute(): float
    {
        $sale = (float)($this->sale_amount ?? $this->grand_total ?? 0);
        $cost = $this->total_purchase_cost;
        $comm = (float)($this->broker_commission_amount ?? 0);
        return round($sale - $cost - $comm, 2);
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
     * ROI % (Return on Investment) = (Net Profit / Total Purchase Cost) * 100
     */
    public function getRoiPercentageAttribute(): float
    {
        $cost = $this->total_purchase_cost;
        if ($cost <= 0) {
            return 0.0;
        }
        return round(($this->net_profit / $cost) * 100, 2);
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
}
