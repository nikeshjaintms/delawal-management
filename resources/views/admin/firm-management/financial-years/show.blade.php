@extends('admin.layouts.app')
@section('title', $financialYear->year_name . ' — Financial Year')
@section('page-title','Firm Management')

@section('content')
<style>
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px}
.header-actions{display:flex;gap:10px;flex-wrap:wrap}
.detail-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;padding:28px 32px;box-shadow:var(--card-shadow);max-width:700px}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}
@media(max-width:480px){.detail-grid{grid-template-columns:1fr}}
.detail-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--text-secondary);margin-bottom:5px}
.detail-value{font-size:15px;font-weight:600;color:var(--text-primary)}
.badge{display:inline-block;padding:4px 10px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase}
.badge-active{background:rgba(16,185,129,.1);color:#059669}
.badge-inactive{background:rgba(239,68,68,.1);color:#DC2626}
.active-indicator{display:inline-flex;align-items:center;gap:5px;background:rgba(59,130,246,.1);color:#1D4ED8;font-size:12px;font-weight:700;border-radius:20px;padding:4px 12px}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>{{ $financialYear->year_name }}</h2>
        <p>Financial year details.</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('financial-years.edit', $financialYear) }}" class="btn-gold"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
        <a href="{{ route('financial-years.index') }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="detail-card">
    <div class="detail-grid">
        <div>
            <div class="detail-label">Year Name</div>
            <div class="detail-value">
                {{ $financialYear->year_name }}
                @if($financialYear->is_active)
                    <span class="active-indicator" style="margin-left:8px"><i class="fa-solid fa-circle-dot"></i> Current</span>
                @endif
            </div>
        </div>
        <div>
            <div class="detail-label">Status</div>
            <div class="detail-value"><span class="badge badge-{{ $financialYear->status }}">{{ ucfirst($financialYear->status) }}</span></div>
        </div>
        <div>
            <div class="detail-label">Start Date</div>
            <div class="detail-value">{{ $financialYear->start_date->format('d M Y') }}</div>
        </div>
        <div>
            <div class="detail-label">End Date</div>
            <div class="detail-value">{{ $financialYear->end_date->format('d M Y') }}</div>
        </div>
        <div>
            <div class="detail-label">Is Active</div>
            <div class="detail-value">{{ $financialYear->is_active ? 'Yes — Current Year' : 'No' }}</div>
        </div>
        <div>
            <div class="detail-label">Created At</div>
            <div class="detail-value" style="font-weight:400;font-size:14px">{{ $financialYear->created_at->format('d M Y, h:i A') }}</div>
        </div>
    </div>
</div>
@endsection
