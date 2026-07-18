@extends('admin.layouts.app')
@section('title','Property Status — Details')
@section('page-title','Property Availability')

@section('content')
<style>
.btn-pc{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:linear-gradient(135deg,#1E5AA8,#2F6FE4);color:#fff!important;font-size:14px;font-weight:600;line-height:1;border:none;border-radius:10px;text-decoration:none!important;box-shadow:0 8px 20px rgba(47,111,228,.25);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-pc:hover{color:#fff!important;transform:translateY(-2px)}
.btn-sc{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:#fff;color:#1E5AA8!important;font-size:14px;font-weight:600;line-height:1;border:1px solid rgba(30,90,168,.25);border-radius:10px;text-decoration:none!important;box-shadow:0 6px 16px rgba(30,90,168,.12);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-sc:hover{background:#EEF3FA;color:#10233F!important;transform:translateY(-2px)}
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px}
.header-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.detail-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;padding:28px 32px;box-shadow:var(--card-shadow);margin-bottom:24px;max-width:800px}
.section-heading{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--blue);margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--blue-light);display:flex;align-items:center;gap:8px}
.detail-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:22px}
@media(max-width:768px){.detail-grid{grid-template-columns:1fr 1fr}}
@media(max-width:480px){.detail-grid{grid-template-columns:1fr}}
.detail-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--text-secondary);margin-bottom:5px}
.detail-value{font-size:14.5px;font-weight:600;color:var(--text-primary)}
.prop-link{color:var(--blue)!important;text-decoration:none;font-weight:600}
.prop-link:hover{text-decoration:underline}
/* status badges */
.badge{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;font-size:12px;font-weight:700;border-radius:20px;text-transform:uppercase;letter-spacing:.4px}
.badge i{font-size:10px}
.badge-available   {background:rgba(16,185,129,.12);color:#065F46;border:1px solid rgba(16,185,129,.25)}
.badge-booked      {background:rgba(59,130,246,.12);color:#1D4ED8;border:1px solid rgba(59,130,246,.25)}
.badge-sold        {background:rgba(239,68,68,.10);color:#991B1B;border:1px solid rgba(239,68,68,.22)}
.badge-rented      {background:rgba(249,115,22,.12);color:#9A3412;border:1px solid rgba(249,115,22,.25)}
.badge-reserved    {background:rgba(139,92,246,.12);color:#5B21B6;border:1px solid rgba(139,92,246,.25)}
.badge-under_maintenance{background:rgba(100,116,139,.10);color:#334155;border:1px solid rgba(100,116,139,.22)}
.prop-type-pill{display:inline-flex;align-items:center;gap:5px;background:var(--blue-light);color:var(--blue);font-size:12px;font-weight:600;border-radius:6px;padding:3px 10px;border:1px solid rgba(59,130,246,.15)}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Property Status Record</h2>
        <p>Full details of this availability / status entry.</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('property-availability.edit', $record) }}" class="btn-pc">
            <i class="fa fa-edit"></i> Edit
        </a>
        <a href="{{ route('property-availability.index') }}" class="btn-sc">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-building"></i> Property Details</div>
    <div class="detail-grid">
        <div>
            <div class="detail-label">Property Name</div>
            <div class="detail-value">
                <a href="{{ route('properties.show', $record->property_id) }}" class="prop-link">
                    {{ $record->property->property_name ?? '—' }}
                </a>
            </div>
        </div>
        <div>
            <div class="detail-label">Property Type</div>
            <div class="detail-value">
                @if($record->property?->propertyType)
                    <span class="prop-type-pill">{{ $record->property->propertyType->name }}</span>
                @else
                    <span style="color:var(--text-muted);font-weight:400">—</span>
                @endif
            </div>
        </div>
        <div>
            <div class="detail-label">Unit / Plot / Flat No</div>
            <div class="detail-value">{{ $record->property->unit_no ?? '—' }}</div>
        </div>
        <div>
            <div class="detail-label">Property Code</div>
            <div class="detail-value">{{ $record->property->property_code ?? '—' }}</div>
        </div>
        <div>
            <div class="detail-label">City</div>
            <div class="detail-value">{{ $record->property->city ?? '—' }}</div>
        </div>
        <div>
            <div class="detail-label">Location</div>
            <div class="detail-value" style="font-weight:400">{{ $record->property->location ?? '—' }}</div>
        </div>
    </div>
</div>

<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-circle-check"></i> Status Details</div>
    <div class="detail-grid">
        <div>
            <div class="detail-label">Current Status</div>
            <div class="detail-value">
                <span class="badge badge-{{ $record->status }}">
                    <i class="fa-solid fa-circle"></i>
                    {{ $record->status_label }}
                </span>
            </div>
        </div>
        <div>
            <div class="detail-label">Status Date</div>
            <div class="detail-value">{{ $record->status_date->format('d M Y') }}</div>
        </div>
        <div>
            <div class="detail-label">Updated By</div>
            <div class="detail-value">{{ $record->updatedBy->name ?? '—' }}</div>
        </div>
        <div>
            <div class="detail-label">Created At</div>
            <div class="detail-value" style="font-size:13.5px;font-weight:400">
                {{ $record->created_at->format('d M Y, h:i A') }}
            </div>
        </div>
        <div>
            <div class="detail-label">Last Updated</div>
            <div class="detail-value" style="font-size:13.5px;font-weight:400">
                {{ $record->updated_at->format('d M Y, h:i A') }}
            </div>
        </div>
        @if($record->remarks)
        <div style="grid-column:1/-1">
            <div class="detail-label">Remarks</div>
            <div class="detail-value" style="font-weight:400;font-size:14px;line-height:1.65">
                {{ $record->remarks }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
