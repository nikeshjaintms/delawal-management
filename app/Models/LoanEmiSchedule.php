<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanEmiSchedule extends Model
{
    protected $fillable = [
        'loan_id', 'emi_month', 'emi_year', 'emi_date',
        'emi_amount', 'paid_amount', 'pending_amount',
        'payment_date', 'payment_mode', 'emi_status', 'remarks',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
