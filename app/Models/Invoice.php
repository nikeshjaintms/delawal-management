<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'project_id',
        'invoice_no',
        'invoice_type',
        'invoice_date',
        'due_date',
        'customer_id',
        'tenant_id',
        'contractor_id',
        'vendor_id',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'recipient_address',
        'recipient_gstin',
        'property_sale_id',
        'rental_id',
        'purchase_order_id',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_type',
        'tax_percent',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'tax_amount',
        'round_off',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'payment_status',
        'status',
        'bank_name',
        'bank_account_no',
        'bank_ifsc',
        'bank_branch',
        'terms_conditions',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'invoice_date'    => 'date',
        'due_date'        => 'date',
        'subtotal'        => 'decimal:2',
        'discount_value'  => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percent'     => 'decimal:2',
        'cgst_amount'     => 'decimal:2',
        'sgst_amount'     => 'decimal:2',
        'igst_amount'     => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'round_off'       => 'decimal:2',
        'total_amount'    => 'decimal:2',
        'paid_amount'     => 'decimal:2',
        'balance_amount'  => 'decimal:2',
    ];

    /* ── Relationships ── */

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function propertySale()
    {
        return $this->belongsTo(PropertySale::class);
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class)->latest('payment_date');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ── Accessors & Helpers ── */

    public function getTypeLabelAttribute(): string
    {
        return match ($this->invoice_type) {
            'sale'              => 'Property / Plot Sale',
            'rental'            => 'Rental / Lease',
            'contractor'        => 'Contractor / Labour',
            'material_purchase' => 'Material / Purchase',
            'custom'            => 'General Invoice',
            default             => ucfirst(str_replace('_', ' ', $this->invoice_type)),
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->invoice_type) {
            'sale'              => 'badge-sale',
            'rental'            => 'badge-rental',
            'contractor'        => 'badge-contractor',
            'material_purchase' => 'badge-material',
            default             => 'badge-general',
        };
    }

    public function getPaymentBadgeClassAttribute(): string
    {
        return match ($this->payment_status) {
            'paid'           => 'badge-paid',
            'partially_paid' => 'badge-partial',
            default          => 'badge-unpaid',
        };
    }

    public function recalculateBalances(): self
    {
        $totalPaid = $this->payments()->sum('amount');
        $this->paid_amount = $totalPaid;
        $this->balance_amount = max(0, $this->total_amount - $totalPaid);

        if ($this->balance_amount <= 0 && $this->total_amount > 0) {
            $this->payment_status = 'paid';
        } elseif ($totalPaid > 0) {
            $this->payment_status = 'partially_paid';
        } else {
            $this->payment_status = 'unpaid';
        }

        $this->saveQuietly();
        return $this;
    }
}
