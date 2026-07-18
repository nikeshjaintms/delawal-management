<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'firm_id',
        'name',
        'mobile',
        'email',
        'address',
        'city',
        'identity_type',
        'identity_number',
        'document_file',
        'status',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }
}
