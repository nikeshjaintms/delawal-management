<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'firm_id', 'bank_name', 'loan_type', 'property_id', 'customer_id',
        'loan_amount', 'interest_rate', 'emi_amount',
        'loan_start_date', 'loan_end_date', 'total_emi_months',
        'paid_amount', 'pending_amount', 'loan_status', 'remarks',
    ];

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

    public function emiSchedules()
    {
        return $this->hasMany(LoanEmiSchedule::class)->orderBy('emi_date');
    }
}
