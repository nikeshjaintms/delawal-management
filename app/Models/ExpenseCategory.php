<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'firm_id',
        'name',
        'description',
        'status',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }
}
