@extends('admin.layouts.app')
@section('title','Property Status — Details')
@section('page-title','Property Availability')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.btn-pc, .btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-pc:hover, .btn-primary-custom:hover {
    background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50);
}

.btn-sc, .btn-secondary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: rgba(255, 255, 255, 0.06) !important;
    color: #CBD5E1 !important; font-size: 14px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-sc:hover, .btn-secondary-custom:hover {
    background: rgba(255, 255, 255, 0.12) !important; color: #FFFFFF !important; transform: translateY(-2px);
}

.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }
.header-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.detail-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 28px 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 24px;
    max-width: 860px;
}

.section-heading {
    font-size: 13.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #60A5FA !important;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex;
    align-items: center;
    gap: 8px;
}

.detail-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; }
@media(max-width:768px){.detail-grid{grid-template-columns:1fr 1fr}}
@media(max-width:480px){.detail-grid{grid-template-columns:1fr}}

.detail-label {
    font-size: 11.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #94A3B8 !important;
    margin-bottom: 6px;
}
.detail-value { font-size: 15px; font-weight: 700; color: #FFFFFF !important; }
.prop-link { color: #60A5FA !important; text-decoration: none; font-weight: 700; }
.prop-link:hover { text-decoration: underline; }

/* Status Badges */
.badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; font-size: 11.5px; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: .4px; }
.badge i { font-size: 7px; }
.badge-available   { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-booked      { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-sold        { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-rented      { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.badge-reserved    { background: rgba(139, 92, 246, 0.18) !important; color: #A78BFA !important; border: 1px solid rgba(139, 92, 246, 0.35) !important; }
.badge-under_maintenance { background: rgba(148, 163, 184, 0.18) !important; color: #CBD5E1 !important; border: 1px solid rgba(148, 163, 184, 0.35) !important; }

.prop-type-pill { display: inline-flex; align-items: center; gap: 5px; background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; font-size: 12px; font-weight: 700; border-radius: 6px; padding: 4px 10px; border: 1px solid rgba(59, 130, 246, 0.30); }
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
                    <span style="color:#94A3B8;font-weight:400">—</span>
                @endif
            </div>
        </div>
        <div>
            <div class="detail-label">Unit / Plot / Flat No</div>
            <div class="detail-value">{{ $record->property->unit_no ?? '—' }}</div>
        </div>
        <div>
            <div class="detail-label">Property Code</div>
            <div class="detail-value"><code class="code-chip">{{ $record->property->property_code ?? '—' }}</code></div>
        </div>
        <div>
            <div class="detail-label">City</div>
            <div class="detail-value">{{ $record->property->city ?? '—' }}</div>
        </div>
        <div>
            <div class="detail-label">Location</div>
            <div class="detail-value">{{ $record->property->location ?? '—' }}</div>
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
            <div class="detail-value" style="font-size:13.5px;color:#CBD5E1">
                {{ $record->created_at->format('d M Y, h:i A') }}
            </div>
        </div>
        <div>
            <div class="detail-label">Last Updated</div>
            <div class="detail-value" style="font-size:13.5px;color:#CBD5E1">
                {{ $record->updated_at->format('d M Y, h:i A') }}
            </div>
        </div>
        @if($record->remarks)
        <div style="grid-column:1/-1">
            <div class="detail-label">Remarks</div>
            <div class="detail-value" style="font-size:14px;line-height:1.6;color:#CBD5E1">
                {{ $record->remarks }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
