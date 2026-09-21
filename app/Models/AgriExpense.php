<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgriExpense extends Model
{
    use \App\Traits\HasFirms;

    const CATEGORIES = [
        'Labour',
        'Seeds',
        'Fertilizer',
        'Pesticides',
        'Irrigation',
        'Electricity',
        'Water',
        'Equipment',
        'Machinery',
        'Fuel',
        'Transportation',
        'Maintenance',
        'Contractor',
        'Other',
    ];

    const EXPENSE_TYPES = [
        'Direct Farm Expense',
        'Labour Payment',
        'Vendor Material',
        'Contractor Work',
        'Machinery Rental',
        'Other',
    ];

    protected $table = 'agri_expenses';

    protected $fillable = [
        'firm_id',
        'farm_id',
        'project_id',
        'property_id',
        'expense_date',
        'category',
        'expense_type',
        'vendor_id',
        'contractor_id',
        'labour_id',
        'labour_payment_id',
        'amount',
        'payment_mode_id',
        'payment_method',
        'payment_status',
        'bill_no',
        'invoice_no',
        'attachment',
        'description',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function farm()
    {
        return $this->belongsTo(AgriFarm::class, 'farm_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function labour()
    {
        return $this->belongsTo(AgriLabour::class, 'labour_id');
    }

    public function labourPayment()
    {
        return $this->belongsTo(AgriLabourPayment::class, 'labour_payment_id');
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
