<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = ['firm_id', 'form_name', 'form_type', 'description', 'status'];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function fields()
    {
        return $this->hasMany(FormField::class)->orderBy('sort_order', 'asc');
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }
}
