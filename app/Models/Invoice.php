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

    /**
     * Generate the next guaranteed unique invoice number (firm-wise or system-wide).
     * Prevents duplicate key errors by inspecting the highest DB sequence and incrementing safely.
     */
    public static function generateNextInvoiceNumber(string $type = 'sales', ?int $firmId = null): string
    {
        if (!$firmId) {
            $isFirm = session('login_type') === 'firm' && session('firm_id');
            $user = \Illuminate\Support\Facades\Auth::user();
            $firmId = $isFirm ? session('firm_id') : ($user && $user->firm_id ? $user->firm_id : null);
        }

        // Try to get active setting specifically linked to this firm
        $activeSetting = null;
        if ($firmId) {
            $activeSetting = \App\Models\InvoiceSetting::where('status', 'active')
                ->whereHas('firms', function ($q) use ($firmId) {
                    $q->where('firms.id', $firmId);
                })
                ->with('financialYear')
                ->first();
        }

        // Fallback to global active setting if no firm-specific setting found
        if (!$activeSetting) {
            $activeSetting = \App\Models\InvoiceSetting::activeSetting();
        }

        $prefixField = match ($type) {
            'sale', 'sales' => 'sales_prefix',
            'purchase', 'material_purchase' => 'purchase_prefix',
            'booking' => 'booking_prefix',
            'rental' => 'rental_prefix',
            'payment' => 'payment_prefix',
            'receipt' => 'receipt_prefix',
            'expense', 'contractor' => 'expense_prefix',
            'income' => 'income_prefix',
            'loan' => 'loan_prefix',
            default => $type . '_prefix',
        };

        $prefix = 'INV';
        if ($activeSetting) {
            $prefix = $activeSetting->$prefixField ?? ($activeSetting->sales_prefix ?? 'INV');
        }

        $year = ($activeSetting && $activeSetting->financialYear)
            ? substr($activeSetting->financialYear->year_name, 0, 4)
            : date('Y');

        // Search for existing invoice numbers starting with prefix-year-
        $searchPrefix = "{$prefix}-{$year}-";
        $existingQuery = self::where('invoice_no', 'like', "{$searchPrefix}%");
        if ($firmId) {
            $existingQuery->where('firm_id', $firmId);
        }
        $existingInvoices = $existingQuery->pluck('invoice_no')->toArray();

        $maxNum = 0;
        foreach ($existingInvoices as $invNo) {
            $parts = explode('-', $invNo);
            $last = end($parts);
            if (is_numeric($last)) {
                $val = (int) $last;
                if ($val > $maxNum) {
                    $maxNum = $val;
                }
            }
        }

        if ($activeSetting && $activeSetting->current_number > $maxNum) {
            $maxNum = $activeSetting->current_number - 1;
        }

        $nextNum = $maxNum + 1;
        $candidate = sprintf('%s-%s-%d', $prefix, $year, $nextNum);

        // Safety loop to ensure uniqueness
        while (self::where('invoice_no', $candidate)->exists()) {
            $nextNum++;
            $candidate = sprintf('%s-%s-%d', $prefix, $year, $nextNum);
        }

        // Update active setting counter
        if ($activeSetting) {
            $activeSetting->update(['current_number' => $nextNum + 1]);
        }

        return $candidate;
    }
}
