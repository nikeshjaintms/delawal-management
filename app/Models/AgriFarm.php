<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgriFarm extends Model
{
    use \App\Traits\HasFirms;

    protected $table = 'agri_farms';

    protected $fillable = [
        'firm_id',
        'property_id',
        'project_id',
        'farm_name',
        'owner_seller_name',
        'village',
        'taluka',
        'district',
        'survey_no',
        'land_area',
        'area_unit',
        'farm_type',
        'crop_activity',
        'start_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'land_area'  => 'decimal:2',
        'start_date' => 'date',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function labours()
    {
        return $this->hasMany(AgriLabour::class, 'farm_id');
    }

    public function labourPayments()
    {
        return $this->hasMany(AgriLabourPayment::class, 'farm_id');
    }

    public function expenses()
    {
        return $this->hasMany(AgriExpense::class, 'farm_id');
    }

    public function incomes()
    {
        return $this->hasMany(AgriIncome::class, 'farm_id');
    }

    /**
     * Get Total Income for this farm
     */
    public function getTotalIncomeAttribute(): float
    {
        return (float) $this->incomes()->sum('total_amount');
    }

    /**
     * Get Total Expense for this farm (general expenses + labour expenses)
     */
    public function getTotalExpenseAttribute(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    /**
     * Get Net Profit for this farm
     */
    public function getNetProfitAttribute(): float
    {
        return round($this->total_income - $this->total_expense, 2);
    }
}
