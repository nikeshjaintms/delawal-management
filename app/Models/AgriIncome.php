<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgriIncome extends Model
{
    use \App\Traits\HasFirms;

    const INCOME_TYPES = [
        'Crop Sale',
        'Agricultural Product Sale',
        'Land/Farm Income',
        'Rental/Lease Income',
        'Government Subsidy',
        'Other Income',
    ];

    const UNITS = [
        'Kg',
        'Quintal',
        'Ton',
        'Bag',
        'Box',
        'Litre',
        'Mon (20kg)',
        'Other',
    ];

    protected $table = 'agri_incomes';

    protected $fillable = [
        'firm_id',
        'farm_id',
        'project_id',
        'property_id',
        'income_date',
        'income_type',
        'customer_id',
        'buyer_name',
        'crop_product',
        'quantity',
        'unit',
        'rate',
        'total_amount',
        'payment_received',
        'pending_amount',
        'payment_mode_id',
        'payment_method',
        'payment_status',
        'invoice_no',
        'attachment',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'income_date'      => 'date',
        'quantity'         => 'decimal:2',
        'rate'             => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'payment_received' => 'decimal:2',
        'pending_amount'   => 'decimal:2',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function farm()
    {
        return $this->belongsTo(AgriFarm::class, 'farm_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get Buyer Display Name
     */
    public function getBuyerDisplayNameAttribute(): string
    {
        if ($this->customer) {
            return $this->customer->name;
        }
        return $this->buyer_name ?: 'Direct Buyer';
    }
}
