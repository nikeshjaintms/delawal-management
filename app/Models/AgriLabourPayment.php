<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgriLabourPayment extends Model
{
    use \App\Traits\HasFirms;

    protected $table = 'agri_labour_payments';

    protected $fillable = [
        'firm_id',
        'farm_id',
        'labour_id',
        'payment_type',
        'payment_date',
        'working_days',
        'daily_wage_rate',
        'gross_amount',
        'advance_deducted',
        'amount',
        'payment_mode_id',
        'payment_mode',
        'reference_no',
        'payment_status',
        'sync_to_expense',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'payment_date'     => 'date',
        'working_days'     => 'decimal:2',
        'daily_wage_rate'  => 'decimal:2',
        'gross_amount'     => 'decimal:2',
        'advance_deducted' => 'decimal:2',
        'amount'           => 'decimal:2',
        'sync_to_expense'  => 'boolean',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function farm()
    {
        return $this->belongsTo(AgriFarm::class, 'farm_id');
    }

    public function labour()
    {
        return $this->belongsTo(AgriLabour::class, 'labour_id');
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function agriExpense()
    {
        return $this->hasOne(AgriExpense::class, 'labour_payment_id');
    }
}
