<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyMaster extends Model
{
    use \App\Traits\HasFirms;

    protected $table = 'property_masters';

    protected $fillable = [
        'firm_id',
        'property_name',
        'property_code',
        'location',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'description',
        'status',
        'main_image',
        'document_file',
        'created_by',
        'updated_by',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'property_id');
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
