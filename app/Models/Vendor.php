<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'project_id',
        'name',
        'mobile',
        'email',
        'gst_no',
        'address',
        'city',
        'payment_terms',
        'status',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'vendor_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'vendor_id');
    }

    public function debitNotes()
    {
        return $this->hasMany(DebitNote::class, 'vendor_id');
    }

    public function propertyMasters()
    {
        return $this->hasMany(PropertyMaster::class, 'vendor_id');
    }
}
