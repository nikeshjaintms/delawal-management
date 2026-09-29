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
    padding: 10px 20px; min-height: 42px; background: #1E293B !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #475569 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-sc:hover, .btn-secondary-custom:hover {
    background: #334155 !important; color: #FFFFFF !important; transform: translateY(-2px); border-color: #64748B !important;
}

.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }
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
    max-width: 960px;
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

.detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }

.detail-label {
    font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px;
    color: #94A3B8 !important; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;
}
.detail-value { font-size: 15px; font-weight: 700; color: #FFFFFF !important; word-break: break-word; }

/* Status Badges */
.badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; font-size: 11.5px; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: .4px; }
.badge i { font-size: 7px; }
.badge-available         { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.4) !important; }
.badge-booked            { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.4) !important; }
.badge-sold              { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.4) !important; }
.badge-rented            { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.4) !important; }
.badge-reserved          { background: rgba(139, 92, 246, 0.18) !important; color: #A78BFA !important; border: 1px solid rgba(139, 92, 246, 0.4) !important; }
.badge-under_maintenance { background: rgba(148, 163, 184, 0.18) !important; color: #CBD5E1 !important; border: 1px solid rgba(148, 163, 184, 0.4) !important; }

.table-container { width: 100%; overflow-x: auto; background: rgba(16, 22, 34, 0.70); border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.10); margin-top: 14px; }
.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
.premium-table th {
    padding: 11px 14px; background: rgba(255, 255, 255, 0.05); color: #94A3B8; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1px solid rgba(255, 255, 255, 0.10);
}
.premium-table td { padding: 11px 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); color: #FFFFFF; }
</style>

@php
    $targetObj = $property ?? $propertyMaster;
    $targetStatus = $targetObj->status ?? 'available';
    $targetName = $targetObj->property_name ?? 'Property';
@endphp

<div class="crud-header">
    <div class="crud-title">
        <h2>Property Availability: {{ $targetName }}</h2>
        <p>Live status and timeline history for this unit / property.</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('property-availability.index') }}" class="btn-sc">
            <i class="fa fa-arrow-left"></i> Back to Status List
        </a>
    </div>
</div>

<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-building"></i> Current Unit &amp; Availability Status</div>
    <div class="detail-grid">
        <div>
            <div class="detail-label"><i class="fa-solid fa-hotel"></i> Property / Plot Name</div>
            <div class="detail-value" style="color: #60A5FA;">{{ $targetName }}</div>
        </div>
        <div>
            <div class="detail-label"><i class="fa-solid fa-circle-dot"></i> Current Live Status</div>
            <div class="detail-value">
                <span class="badge badge-{{ $targetStatus }}">
                    <i class="fa-solid fa-circle"></i>
                    {{ ucfirst(str_replace('_', ' ', $targetStatus)) }}
                </span>
            </div>
        </div>
        <div>
            <div class="detail-label"><i class="fa-solid fa-building-user"></i> Firm</div>
            <div class="detail-value">{{ $targetObj->firm->firm_name ?? 'N/A' }}</div>
        </div>
        @if($property)
            <div>
                <div class="detail-label"><i class="fa-solid fa-hashtag"></i> Code / Unit No</div>
                <div class="detail-value">{{ $property->property_code ?: ($property->unit_no ? 'Unit '.$property->unit_no : '—') }}</div>
            </div>
            <div>
                <div class="detail-label"><i class="fa-solid fa-ruler-combined"></i> Size / Area</div>
                <div class="detail-value">{{ $property->size ? $property->size.' '.($property->size_unit ?? 'Sq.Ft') : '—' }}</div>
            </div>
            <div>
                <div class="detail-label"><i class="fa-solid fa-indian-rupee-sign"></i> Price / Valuation</div>
                <div class="detail-value" style="color:#34D399;">₹{{ number_format((float)($property->price ?? 0), 2) }}</div>
            </div>
        @endif
    </div>
</div>

{{-- Status Timeline History --}}
<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-clock-rotate-left"></i> Status Change Timeline History ({{ $history->count() }})</div>
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th>Status</th>
                    <th>Status Date</th>
                    <th>Remarks</th>
                    <th>Updated By</th>
                    <th>Logged At</th>
                </tr>
            </thead>
            <tbody>
            @forelse($history as $i => $h)
                <tr>
                    <td style="color: #94A3B8;">{{ $i + 1 }}</td>
                    <td>
                        <span class="badge badge-{{ $h->status }}" style="font-size: 10px; padding: 3px 10px;">
                            {{ ucfirst(str_replace('_', ' ', $h->status)) }}
                        </span>
                    </td>
                    <td>{{ $h->status_date ? $h->status_date->format('d M Y') : '—' }}</td>
                    <td style="color: #CBD5E1;">{{ $h->remarks ?: '—' }}</td>
                    <td>{{ $h->updatedBy->name ?? 'System' }}</td>
                    <td style="color: #94A3B8; font-size: 11.5px;">{{ $h->created_at ? $h->created_at->format('d M Y, h:i A') : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" align="center" style="padding: 24px; color: #94A3B8;">
                        No manual status history logs recorded yet. Current status is synchronized live from system transactions.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
