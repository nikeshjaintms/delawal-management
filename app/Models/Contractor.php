<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'firm_id',
        'project_id',
        'contractor_name',
        'mobile',
        'aadhar_no',
        'pan_no',
        'bank_name',
        'account_number',
        'ifsc_code',
        'branch_name',
        'address',
        'status',
        'created_by',
        'updated_by',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
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
