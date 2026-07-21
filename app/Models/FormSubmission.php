<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = ['firm_id', 'form_id', 'submitted_data'];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    protected $casts = [
        'submitted_data' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
