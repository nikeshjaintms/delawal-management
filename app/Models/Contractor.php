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
        'contract_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'work_type',
        'contract_date',
        'contract_notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'contract_amount' => 'decimal:2',
        'paid_amount'     => 'decimal:2',
        'due_amount'      => 'decimal:2',
        'contract_date'   => 'date',
    ];

    public function payments()
    {
        return $this->hasMany(ContractorPayment::class)->orderBy('payment_date', 'desc')->orderBy('id', 'desc');
    }

    public function recalculatePaymentStatus(): void
    {
        $totalPaid = (float) $this->payments()->sum('amount');
        $contractAmount = (float) ($this->contract_amount ?? 0.00);
        $dueAmount = max(0.00, $contractAmount - $totalPaid);

        $status = 'unpaid';
        if ($contractAmount > 0) {
            if ($totalPaid >= $contractAmount) {
                $status = 'paid';
            } elseif ($totalPaid > 0) {
                $status = 'partial';
            } else {
                $status = 'unpaid';
            }
        } elseif ($totalPaid > 0) {
            $status = 'paid';
        }

        $this->update([
            'paid_amount'    => $totalPaid,
            'due_amount'     => $dueAmount,
            'payment_status' => $status,
        ]);
    }

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
