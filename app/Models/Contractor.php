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

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'contractor_project', 'contractor_id', 'project_id')->withTimestamps();
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'contractor_property', 'contractor_id', 'property_id')->withTimestamps();
    }

    public function syncProjects($projectIds): void
    {
        $projectIds = array_values(array_filter((array) $projectIds));
        $this->projects()->sync($projectIds);
        
        $primaryProjectId = !empty($projectIds) ? $projectIds[0] : null;
        if ($this->project_id != $primaryProjectId) {
            $this->project_id = $primaryProjectId;
            if ($this->exists) {
                $this->saveQuietly();
            }
        }
    }

    public function syncProperties($propertyIds): void
    {
        $propertyIds = array_values(array_filter((array) $propertyIds));
        $this->properties()->sync($propertyIds);
    }

    public function getProjectNamesAttribute(): string
    {
        if ($this->relationLoaded('projects') && $this->projects->isNotEmpty()) {
            return $this->projects->pluck('project_name')->implode(', ');
        }
        return $this->project->project_name ?? '—';
    }

    public function getPropertyNamesAttribute(): string
    {
        if ($this->relationLoaded('properties') && $this->properties->isNotEmpty()) {
            return $this->properties->map(function ($p) {
                return $p->property_name . ($p->property_code ? ' (' . $p->property_code . ')' : '');
            })->implode(', ');
        }
        return '—';
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
