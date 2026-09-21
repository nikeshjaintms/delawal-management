<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'loan_id',
        'firm_id',
        'payment_mode_id',
        'amount',
        'payment_date',
        'payment_mode',
        'reference_no',
        'remarks',
        'created_by',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
