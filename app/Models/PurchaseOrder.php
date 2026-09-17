<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'project_id',
        'contractor_id',
        'po_number',
        'vendor_id',
        'seller_id',
        'supplier_name',
        'po_date',
        'delivery_date',
        'status',
        'sub_total',
        'discount_amount',
        'taxable_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'grand_total',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'po_date' => 'date',
        'delivery_date' => 'date',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function contractors()
    {
        return $this->belongsToMany(Contractor::class, 'contractor_purchase_order', 'purchase_order_id', 'contractor_id')->withTimestamps();
    }

    public function getAllContractorsAttribute()
    {
        if ($this->relationLoaded('contractors') && $this->contractors->isNotEmpty()) {
            return $this->contractors;
        }
        $cons = $this->contractors()->get();
        if ($cons->isNotEmpty()) {
            return $cons;
        }
        if ($this->contractor_id && $this->contractor) {
            return collect([$this->contractor]);
        }
        return collect();
    }

    public function getContractorNamesAttribute(): string
    {
        $names = $this->all_contractors->pluck('contractor_name')->filter()->values();
        return $names->isNotEmpty() ? $names->implode(', ') : ($this->contractor->contractor_name ?? '—');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function expense()
    {
        return $this->hasOne(Expense::class, 'purchase_order_id');
    }

    /**
     * Automatically sync this Purchase Order to Project Expenses
     */
    public function syncToExpense(): ?Expense
    {
        $amount = (float)($this->grand_total ?: ($this->taxable_amount ?: ($this->sub_total ?: 0)));
        if ($amount <= 0 && $this->items()->count() > 0) {
            $amount = (float)$this->items()->sum('line_total');
        }

        $approvalStatus = 'Pending';
        if (in_array($this->status, ['Approved', 'Ordered', 'Received'])) {
            $approvalStatus = 'Approved';
        } elseif ($this->status === 'Cancelled') {
            $approvalStatus = 'Rejected';
        }

        $firmId = $this->firm_id ?: 1;

        $category = ExpenseCategory::firstOrCreate(
            ['name' => 'Material & Procurement'],
            [
                'status'      => 'active',
                'description' => 'Material purchases and purchase orders',
                'firm_id'     => $firmId,
            ]
        );

        if ($firmId && method_exists($category, 'firms')) {
            $category->firms()->syncWithoutDetaching([$firmId]);
        }

        $vendorName = $this->vendor ? $this->vendor->name : ($this->seller ? $this->seller->name : ($this->supplier_name ?: 'Vendor / Supplier'));
        $sellerPart = $this->seller ? ' [Seller: ' . $this->seller->name . ']' : ($this->supplier_name ? ' (' . $this->supplier_name . ')' : '');
        $displayTitle = 'PO #' . $this->po_number . ($this->vendor ? ' - ' . $this->vendor->name : '') . $sellerPart;

        $expense = Expense::updateOrCreate(
            ['purchase_order_id' => $this->id],
            [
                'firm_id'             => $firmId,
                'project_id'          => $this->project_id,
                'vendor_id'           => $this->vendor_id,
                'expense_date'        => $this->po_date ?: now()->toDateString(),
                'expense_category_id' => $category->id,
                'expense_category'    => $category->name,
                'expense_title'       => $displayTitle,
                'amount'              => $amount,
                'taxable_amount'      => $this->taxable_amount ?: $amount,
                'cgst_amount'         => $this->cgst_amount ?: 0,
                'sgst_amount'         => $this->sgst_amount ?: 0,
                'igst_amount'         => $this->igst_amount ?: 0,
                'total_gst'           => (($this->cgst_amount ?? 0) + ($this->sgst_amount ?? 0) + ($this->igst_amount ?? 0)),
                'grand_total'         => $this->grand_total ?: $amount,
                'paid_to'             => $vendorName,
                'bill_no'             => $this->po_number,
                'remarks'             => 'Auto-synced from Purchase Order #' . $this->po_number . ($this->supplier_name ? ' [Supplier: ' . $this->supplier_name . ']' : '') . ($this->remarks ? '. ' . $this->remarks : ''),
                'approval_status'     => $approvalStatus,
            ]
        );

        if ($firmId && method_exists($expense, 'firms')) {
            $expense->firms()->syncWithoutDetaching([$firmId]);
        }

        return $expense;
    }
}
