<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgriLabour extends Model
{
    use \App\Traits\HasFirms;

    protected $table = 'agri_labours';

    protected $fillable = [
        'firm_id',
        'farm_id',
        'name',
        'mobile_number',
        'labour_type',
        'field_crop',
        'joining_date',
        'daily_wage',
        'fixed_salary',
        'total_earned',
        'total_advance',
        'total_paid',
        'advance_balance',
        'pending_amount',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'joining_date'    => 'date',
        'daily_wage'      => 'decimal:2',
        'fixed_salary'    => 'decimal:2',
        'total_earned'    => 'decimal:2',
        'total_advance'   => 'decimal:2',
        'total_paid'      => 'decimal:2',
        'advance_balance' => 'decimal:2',
        'pending_amount'  => 'decimal:2',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function farm()
    {
        return $this->belongsTo(AgriFarm::class, 'farm_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(AgriLabourPayment::class, 'labour_id');
    }

    public function expenses()
    {
        return $this->hasMany(AgriExpense::class, 'labour_id');
    }

    /**
     * Recalculate labour balances (total earned, total advance, total paid, advance balance, pending amount)
     */
    public function recalculateBalances(): void
    {
        $this->refresh();
        $payments = $this->payments()->get();

        $totalEarned = 0;
        $totalAdvance = 0;
        $advanceDeducted = 0;
        $netPaid = 0;

        foreach ($payments as $p) {
            if ($p->payment_type === 'Advance Given') {
                $totalAdvance += (float) $p->amount;
            } elseif ($p->payment_type === 'Advance Deduction') {
                $advanceDeducted += (float) $p->advance_deducted;
            } else {
                // Salary / Daily Wage / Bonus
                $totalEarned += (float) ($p->gross_amount > 0 ? $p->gross_amount : $p->amount);
                $netPaid += (float) $p->amount;
                $advanceDeducted += (float) $p->advance_deducted;
            }
        }

        $advanceBalance = max(0, round($totalAdvance - $advanceDeducted, 2));
        $pendingAmount = max(0, round($totalEarned - $netPaid - $advanceDeducted, 2));

        $this->update([
            'total_earned'    => round($totalEarned, 2),
            'total_advance'   => round($totalAdvance, 2),
            'total_paid'      => round($netPaid, 2),
            'advance_balance' => $advanceBalance,
            'pending_amount'  => $pendingAmount,
        ]);
    }
}
