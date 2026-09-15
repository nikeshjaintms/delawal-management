<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractorPayment extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'contractor_id',
        'firm_id',
        'project_id',
        'property_id',
        'payment_mode_id',
        'amount',
        'payment_date',
        'payment_mode',
        'reference_no',
        'bank_name',
        'bill_no',
        'document_file',
        'payment_type',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
