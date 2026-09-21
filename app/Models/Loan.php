<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id', 'loan_nature', 'bank_name', 'loan_type', 'has_emi', 'property_id', 'customer_id',
        'loan_amount', 'interest_rate', 'emi_amount',
        'loan_start_date', 'loan_end_date', 'total_emi_months',
        'paid_amount', 'pending_amount', 'loan_status', 'remarks',
        'person_name', 'mobile_number', 'relationship', 'payment_mode_id',
    ];

    protected $casts = [
        'has_emi' => 'boolean',
    ];

    public function isGiven(): bool
    {
        return $this->loan_nature === 'given';
    }

    public function isTaken(): bool
    {
        return $this->loan_nature !== 'given';
    }

    public function scopeTaken($query)
    {
        return $query->where('loan_nature', 'taken');
    }

    public function scopeGiven($query)
    {
        return $query->where('loan_nature', 'given');
    }

    public function getPartyDisplayNameAttribute(): string
    {
        if ($this->isGiven()) {
            if ($this->customer) {
                return $this->customer->name . ' (Customer)';
            }
            if ($this->person_name) {
                return $this->person_name . ($this->relationship ? " ({$this->relationship})" : '');
            }
            return 'Borrower Party';
        }

        // Loan Taken
        if ($this->loan_type === 'Personal Loan' && $this->person_name) {
            return $this->person_name . ($this->relationship ? " ({$this->relationship})" : '');
        }
        return $this->bank_name ?: 'Bank / Lender';
    }

    public function firm()
    {
        return $this->belongsTo(Firm::class);
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
        return $this->belongsTo(PaymentMode::class);
    }

    public function emiSchedules()
    {
        return $this->hasMany(LoanEmiSchedule::class)->orderBy('emi_date');
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class)->orderBy('payment_date', 'desc');
    }
}
