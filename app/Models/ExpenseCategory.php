<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function firms()
    {
        return $this->belongsToMany(Firm::class, 'expense_category_firm', 'expense_category_id', 'firm_id')->withTimestamps();
    }
}
