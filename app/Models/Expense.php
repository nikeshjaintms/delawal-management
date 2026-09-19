<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use \App\Traits\HasFirms;

    const PAYMENT_MODES = [
        'Cash',
        'Bank Transfer',
        'UPI',
        'Cheque',
        'Other',
    ];

    const APPROVAL_STATUSES = [
        'Pending',
        'Approved',
        'Rejected',
    ];

    protected $fillable = [
        'firm_id',
        'project_id',
        'property_id',
        'vendor_id',
        'purchase_order_id',
        'expense_date',
        'expense_category_id',
        'expense_category',
        'expense_type',
        'expense_title',
        'description',
        'amount',
        'taxable_amount',
        'cgst_rate',
        'cgst_amount',
        'sgst_rate',
        'sgst_amount',
        'igst_rate',
        'igst_amount',
        'total_gst',
        'grand_total',
        'payment_mode',
        'reference_no',
        'payment_account',
        'paid_to',
        'bill_no',
        'invoice_no',
        'hsn_code',
        'bill_file',
        'approval_status',
        'remarks',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'expense_property')->withTimestamps();
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

    public function syncProperties($propertyIds): void
    {
        $propertyIds = array_filter((array) $propertyIds);
        $this->properties()->sync($propertyIds);

        $primaryPropId = reset($propertyIds) ?: null;
        if ($this->property_id != $primaryPropId) {
            $this->property_id = $primaryPropId;
            if ($this->exists) {
                $this->saveQuietly();
            }
        }
    }

    public function getPropertyNamesAttribute(): string
    {
        if ($this->relationLoaded('properties') && $this->properties->isNotEmpty()) {
            return $this->properties->map(function ($p) {
                return $p->property_name . ($p->unit_no ? ' (Unit ' . $p->unit_no . ')' : '');
            })->implode(', ');
        }
        if ($this->property) {
            return $this->property->property_name . ($this->property->unit_no ? ' (Unit ' . $this->property->unit_no . ')' : '');
        }
        return '—';
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
}
