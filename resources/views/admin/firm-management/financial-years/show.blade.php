@extends('admin.layouts.app')
@section('title', $financialYear->year_name . ' — Financial Year')
@section('page-title','Firm Management')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }

.header-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-primary-custom:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

.btn-secondary-custom, a.btn-secondary-custom, button.btn-secondary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #1E293B !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #475569 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-secondary-custom:hover { background: #334155 !important; color: #FFFFFF !important; transform: translateY(-2px); border-color: #64748B !important; }

.detail-card, .card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 26px 30px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 24px;
    max-width: 800px;
}

.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media(max-width:480px){.detail-grid{grid-template-columns:1fr}}

.detail-item {
    padding: 16px 18px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1px solid rgba(255, 255, 255, 0.10) !important; border-radius: 14px;
    transition: all .2s ease;
}
.detail-item:hover { border-color: rgba(59, 130, 246, 0.35) !important; background: rgba(16, 22, 34, 0.85) !important; }

.detail-label {
    font-size: 11px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.8px; color: #94A3B8 !important; margin-bottom: 6px;
    display: flex; align-items: center; gap: 6px;
}
.detail-label i { color: #60A5FA !important; font-size: 12px; }

.detail-value { font-size: 15px; font-weight: 700; color: #FFFFFF !important; word-break: break-word; }

.badge { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.active-indicator {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important;
    font-size: 12px; font-weight: 700; border-radius: 20px; padding: 4px 12px;
    border: 1px solid rgba(59, 130, 246, 0.35) !important;
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>{{ $financialYear->year_name }}</h2>
        <p>Financial year details and duration overview.</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('financial-years.edit', $financialYear) }}" class="btn-primary-custom"><i class="fa fa-edit"></i> Edit</a>
        <a href="{{ route('financial-years.index') }}" class="btn-secondary-custom"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="detail-card">
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar"></i> Year Name</div>
            <div class="detail-value">
                {{ $financialYear->year_name }}
                @if($financialYear->is_active)
                    <span class="active-indicator" style="margin-left:8px"><i class="fa-solid fa-circle-dot"></i> Current</span>
                @endif
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-signal"></i> Status</div>
            <div class="detail-value"><span class="badge badge-{{ $financialYear->status }}">{{ ucfirst($financialYear->status) }}</span></div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar-check"></i> Start Date</div>
            <div class="detail-value">{{ $financialYear->start_date->format('d M Y') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar-xmark"></i> End Date</div>
            <div class="detail-value">{{ $financialYear->end_date->format('d M Y') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-toggle-on"></i> Is Active</div>
            <div class="detail-value">{{ $financialYear->is_active ? 'Yes — Current Year' : 'No' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-clock"></i> Created At</div>
            <div class="detail-value">{{ $financialYear->created_at->format('d M Y, h:i A') }}</div>
        </div>
    </div>
</div>
@endsection
