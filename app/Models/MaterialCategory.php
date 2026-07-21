<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialCategory extends Model
{
    use \App\Traits\HasFirms;

    protected $fillable = ['firm_id', 'category_name', 'description', 'status'];

    public function firm() { return $this->belongsTo(Firm::class); }
    public function materials() { return $this->hasMany(Material::class); }
}
