<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $fillable = ['form_id', 'submitted_data'];

    protected $casts = [
        'submitted_data' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
