@extends('admin.layouts.app')

@section('title', $propertyMaster->property_name . ' - Property Details')
@section('page-title', 'Property Management')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.breadcrumb-nav {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(20, 27, 41, 0.60);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.10);
    border-radius: 12px;
    font-size: 13px;
    color: #94A3B8;
    font-weight: 600;
    margin-bottom: 20px;
}
.breadcrumb-nav a {
    color: #60A5FA;
    text-decoration: none;
    font-weight: 700;
    transition: color 0.15s;
}
.breadcrumb-nav a:hover { color: #93C5FD; }
.breadcrumb-nav .separator { font-size: 10px; color: #64748B; }
.breadcrumb-nav .active { color: #FFFFFF; font-weight: 700; }

.crud-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 22px;
    flex-wrap: wrap;
    gap: 15px;
}

.crud-title h2 {
    font-size: 28px;
    font-weight: 800;
    color: #FFFFFF !important;
    margin-bottom: 4px;
    letter-spacing: -0.3px;
}

.crud-title p {
    font-size: 14px;
    color: #CBD5E1 !important;
    font-weight: 600 !important;
    margin: 0;
}

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 24px;
}

/* ── Top Hero Card Layout ── */
.property-hero-grid {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 24px;
    align-items: center;
}
@media (max-width: 992px) {
    .property-hero-grid {
        grid-template-columns: 1fr;
        gap: 18px;
    }
}

.property-hero-avatar {
    width: 120px;
    height: 120px;
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.15);
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.30) 0%, rgba(139, 92, 246, 0.25) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #60A5FA;
    font-size: 42px;
    flex-shrink: 0;
    box-shadow: 0 8px 24px rgba(0,0,0,0.30);
    position: relative;
}
.property-hero-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.property-meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
}
.pm-item {
    display: flex;
    flex-direction: column;
}
.pm-label {
    font-size: 11px;
    font-weight: 700;
    color: #94A3B8;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 3px;
}
.pm-value {
    font-size: 14px;
    font-weight: 700;
    color: #FFFFFF;
}

/* ── KPI Summary Cards Strip ── */
.kpi-strip {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
@media (max-width: 1200px) { .kpi-strip { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px) { .kpi-strip { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px) { .kpi-strip { grid-template-columns: 1fr; } }

.kpi-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(16px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 16px !important;
    padding: 14px 18px !important;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.30) !important;
}
.kpi-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.kpi-content {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.kpi-label {
    font-size: 11px;
    font-weight: 700;
    color: #94A3B8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kpi-val {
    font-size: 17px;
    font-weight: 800;
    color: #FFFFFF;
    line-height: 1.2;
    margin-top: 2px;
}

/* ── Custom Action Buttons ── */
.btn-gold {
    background: linear-gradient(135deg, #D4AF37 0%, #AA820A 100%);
    color: #FFFFFF !important;
    padding: 8px 18px;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    box-shadow: 0 4px 14px rgba(212, 175, 55, 0.25);
    text-decoration: none;
}
.btn-gold:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(212, 175, 55, 0.35);
}

.btn-secondary-custom {
    background: rgba(255, 255, 255, 0.08);
    color: #CBD5E1 !important;
    border: 1px solid rgba(255, 255, 255, 0.15);
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-secondary-custom:hover {
    background: rgba(255, 255, 255, 0.14);
    color: #FFFFFF !important;
    border-color: rgba(255, 255, 255, 0.25);
}

.btn-success-custom {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.25) 0%, rgba(5, 150, 105, 0.35) 100%);
    color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.40);
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-success-custom:hover {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.35) 0%, rgba(5, 150, 105, 0.50) 100%);
    border-color: #10B981;
    color: #FFFFFF !important;
}

.btn-purple-custom {
    background: linear-gradient(135deg, rgba(168, 85, 247, 0.22) 0%, rgba(147, 51, 234, 0.32) 100%);
    color: #C084FC !important;
    border: 1px solid rgba(168, 85, 247, 0.38);
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-purple-custom:hover {
    background: linear-gradient(135deg, rgba(168, 85, 247, 0.35) 0%, rgba(147, 51, 234, 0.48) 100%);
    border-color: #A855F7;
    color: #FFFFFF !important;
}

/* ── Badges ── */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    text-transform: capitalize;
}
.badge-active { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); }
.badge-inactive { background: rgba(239, 68, 68, 0.18); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.35); }
.badge-available { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); }
.badge-in-project { background: rgba(59, 130, 246, 0.18); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.35); }
.badge-booked { background: rgba(245, 158, 11, 0.18); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); }
.badge-sold { background: rgba(168, 85, 247, 0.18); color: #C084FC; border: 1px solid rgba(168, 85, 247, 0.35); }
.badge-paid { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); }
.badge-partial { background: rgba(245, 158, 11, 0.18); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); }
.badge-unpaid { background: rgba(239, 68, 68, 0.18); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.35); }

/* ── Luxury Table Design ── */
.custom-table-responsive {
    overflow-x: auto;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.10);
    background: rgba(15, 23, 42, 0.40);
}
.custom-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
    text-align: left;
}
.custom-table th {
    background: rgba(30, 41, 59, 0.70);
    color: #94A3B8;
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    padding: 12px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    white-space: nowrap;
}
.custom-table td {
    padding: 12px 16px;
    color: #E2E8F0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    vertical-align: middle;
}
.custom-table tbody tr:hover {
    background: rgba(59, 130, 246, 0.05);
}
.custom-table tbody tr:last-child td {
    border-bottom: none;
}

/* ── Filter / Search Controls ── */
.filter-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
}
.filter-tab-btn {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #94A3B8;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}
.filter-tab-btn.active, .filter-tab-btn:hover {
    background: rgba(59, 130, 246, 0.22);
    border-color: #3B82F6;
    color: #FFFFFF;
}

.search-input-wrap {
    position: relative;
    min-width: 240px;
}
.search-input-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748B;
    font-size: 13px;
}
.search-input-box {
    width: 100%;
    padding: 7px 12px 7px 34px;
    background: rgba(15, 23, 42, 0.60);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 8px;
    color: #FFFFFF;
    font-size: 13px;
    outline: none;
    transition: border-color 0.2s;
}
.search-input-box:focus {
    border-color: #60A5FA;
}

/* ── Modals ── */
.modal-backdrop-custom {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(8px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 20px;
}
.modal-backdrop-custom.active {
    display: flex;
}
.modal-box-custom {
    background: #0F172A;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 18px;
    width: 100%;
    max-width: 620px;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.60);
    overflow: hidden;
    animation: modalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.modal-header-custom {
    padding: 18px 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(30, 41, 59, 0.50);
}
.modal-header-custom h3 {
    font-size: 17px;
    font-weight: 800;
    color: #FFFFFF;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.modal-close-btn {
    background: transparent;
    border: none;
    color: #94A3B8;
    font-size: 20px;
    cursor: pointer;
    transition: color 0.15s;
    line-height: 1;
}
.modal-close-btn:hover { color: #FFFFFF; }
.modal-body-custom {
    padding: 24px;
    max-height: 78vh;
    overflow-y: auto;
}
.m-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}
@media (max-width: 600px) { .m-form-row { grid-template-columns: 1fr; gap: 12px; } }
.m-form-group { display: flex; flex-direction: column; }
.m-form-label {
    font-size: 12.5px;
    font-weight: 700;
    color: #CBD5E1;
    margin-bottom: 6px;
}
.m-form-label span { color: #EF4444; }
.m-form-control {
    width: 100%;
    padding: 9px 13px;
    background: rgba(30, 41, 59, 0.70);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 8px;
    color: #FFFFFF;
    font-size: 13.5px;
    outline: none;
    transition: all 0.2s;
}
.m-form-control:focus {
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
}
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-nav">
    <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house"></i></a>
    <span class="separator">/</span>
    <a href="{{ route('property-masters.index') }}">Property Masters</a>
    <span class="separator">/</span>
    <span class="active">{{ $propertyMaster->property_name }}</span>
</div>

<!-- Header -->
<div class="crud-header">
    <div class="crud-title">
        <h2>{{ $propertyMaster->property_name }}</h2>
        <p>Comprehensive property details, plot management, and project associations.</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('property-masters.detail-pdf', $propertyMaster->id) }}" target="_blank" class="btn-secondary-custom" style="background: rgba(252, 105, 0, 0.18) !important; border-color: rgba(252, 105, 0, 0.45) !important; color: #FF8A3D !important;">
            <i class="fa-solid fa-file-pdf"></i> Print / PDF Dossier
        </a>
        <a href="{{ route('property-masters.edit', $propertyMaster->id) }}" class="btn-secondary-custom">
            <i class="fa-solid fa-pen-to-square"></i> Edit Property
        </a>
        <a href="{{ route('projects.create', ['property_id' => $propertyMaster->id]) }}" class="btn-gold">
            <i class="fa-solid fa-diagram-project"></i> Create Project
        </a>
        <a href="{{ route('property-masters.index') }}" class="btn-secondary-custom">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.18); border: 1px solid rgba(16, 185, 129, 0.35); color: #34D399; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div style="background: rgba(239, 68, 68, 0.18); border: 1px solid rgba(239, 68, 68, 0.35); color: #F87171; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-xmark"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@php
    $totalPlots      = $propertyMaster->plots->count();
    $unassignedPlots = $propertyMaster->plots->whereNull('project_id')->where('status', 'available')->count();
    $assignedPlots   = $propertyMaster->plots->whereNotNull('project_id')->count();
    $bookedSoldPlots = $propertyMaster->plots->whereIn('status', ['booked', 'sold', 'reserved'])->count();
    
    $totalAreaSqft   = $propertyMaster->plots->sum(function($p) {
        $val = floatval(preg_replace('/[^0-9.]/', '', $p->size ?? '0'));
        if (strtolower($p->size_unit ?? '') === 'sq.yard' || strtolower($p->size_unit ?? '') === 'sq.yd') {
            return $val * 9;
        }
        return $val;
    });
    if ($totalAreaSqft == 0 && !empty($propertyMaster->total_area)) {
        $val = floatval($propertyMaster->total_area);
        if (in_array(strtolower($propertyMaster->area_unit ?? ''), ['sq.yd', 'sq.yard', 'sqyd'])) {
            $totalAreaSqft = $val * 9;
        } else {
            $totalAreaSqft = $val;
        }
    }
@endphp

<!-- ================================================================
     HERO OVERVIEW CARD
================================================================ -->
<div class="card-box">
    <div class="property-hero-grid">
        <div class="property-hero-avatar">
            @if($propertyMaster->main_image)
                <img src="{{ asset('storage/' . $propertyMaster->main_image) }}" alt="{{ $propertyMaster->property_name }}">
            @else
                <i class="fa-solid fa-city"></i>
            @endif
        </div>

        <div class="property-meta-grid">
            <div class="pm-item">
                <span class="pm-label">Property Code</span>
                <span class="pm-value"><code style="background: rgba(59, 130, 246, 0.15); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.30); padding: 3px 8px; border-radius: 6px; font-size: 12.5px; font-weight: 800;">{{ $propertyMaster->property_code }}</code></span>
            </div>
            <div class="pm-item">
                <span class="pm-label">Firm</span>
                <span class="pm-value" style="color: #93C5FD;">{{ $propertyMaster->firm->firm_name ?? '-' }}</span>
            </div>
            <div class="pm-item">
                <span class="pm-label" style="color: #60A5FA;"><i class="fa-solid fa-money-bill-wave"></i> Total Purchase Price</span>
                <span class="pm-value" style="color: #60A5FA; font-size: 17px; font-weight: 800;">
                    @if($propertyMaster->purchase_price > 0)
                        ₹{{ number_format($propertyMaster->purchase_price, 2) }}
                    @else
                        <span style="color: #94A3B8; font-size: 14px; font-weight: 500;">—</span>
                    @endif
                </span>
            </div>
            <div class="pm-item">
                <span class="pm-label" style="color: #34D399;"><i class="fa-solid fa-circle-check"></i> Paid Amount</span>
                <span class="pm-value" style="color: #34D399; font-size: 16px; font-weight: 800;">
                    ₹{{ number_format($propertyMaster->paid_amount ?? 0, 2) }}
                </span>
            </div>
            <div class="pm-item">
                <span class="pm-label" style="color: #F87171;"><i class="fa-solid fa-clock-rotate-left"></i> Due Balance</span>
                <span class="pm-value" style="color: #F87171; font-size: 16px; font-weight: 800;">
                    ₹{{ number_format($propertyMaster->due_amount ?? 0, 2) }}
                </span>
            </div>
            <div class="pm-item">
                <span class="pm-label"><i class="fa-solid fa-receipt"></i> Payment Status</span>
                <span class="pm-value">
                    @php $pStatus = $propertyMaster->payment_status ?? 'unpaid'; @endphp
                    @if($pStatus === 'paid')
                        <span class="badge badge-paid"><i class="fa-solid fa-circle-check"></i> Full Paid</span>
                    @elseif($pStatus === 'partial')
                        <span class="badge badge-partial"><i class="fa-solid fa-hourglass-half"></i> Partial Paid</span>
                    @else
                        <span class="badge badge-unpaid"><i class="fa-solid fa-circle-xmark"></i> Unpaid</span>
                    @endif
                </span>
            </div>
            <div class="pm-item">
                <span class="pm-label"><i class="fa-solid fa-calendar-day"></i> Purchase Date</span>
                <span class="pm-value">{{ $propertyMaster->purchase_date ? date('d M, Y', strtotime($propertyMaster->purchase_date)) : '—' }}</span>
            </div>
            <div class="pm-item">
                <span class="pm-label"><i class="fa-solid fa-user-tie"></i> Seller / Vendor</span>
                <span class="pm-value">
                    @if($propertyMaster->vendor)
                        {{ $propertyMaster->vendor->name }}
                    @elseif($propertyMaster->seller_name)
                        {{ $propertyMaster->seller_name }}
                    @else
                        <span style="color: #94A3B8;">—</span>
                    @endif
                </span>
            </div>
            <div class="pm-item">
                <span class="pm-label"><i class="fa-solid fa-ruler-combined"></i> Total Land Area</span>
                <span class="pm-value">
                    @if($propertyMaster->total_area)
                        {{ number_format($propertyMaster->total_area, 2) }} {{ $propertyMaster->area_unit ?? 'Sq.Ft' }}
                    @else
                        <span style="color: #94A3B8;">—</span>
                    @endif
                </span>
            </div>
            <div class="pm-item">
                <span class="pm-label">City &amp; Location</span>
                <span class="pm-value">{{ $propertyMaster->city ?: '-' }} @if($propertyMaster->location) <small style="color:#94A3B8;">({{ $propertyMaster->location }})</small> @endif</span>
            </div>
            <div class="pm-item">
                <span class="pm-label">Status</span>
                <span class="pm-value">
                    <span class="badge {{ $propertyMaster->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        <i class="fa-solid fa-circle-dot"></i> {{ ucfirst($propertyMaster->status) }}
                    </span>
                </span>
            </div>
            <div class="pm-item" style="grid-column: 1 / -1;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <span class="pm-label" style="margin-bottom: 0;">Payment Progress ({{ $propertyMaster->paid_percentage }}% Paid)</span>
                    <span style="font-size: 12px; color: #94A3B8; font-weight: 600;">₹{{ number_format($propertyMaster->paid_amount ?? 0, 2) }} of ₹{{ number_format($propertyMaster->purchase_price ?? 0, 2) }}</span>
                </div>
                <div style="width: 100%; height: 8px; background: rgba(255,255,255,0.08); border-radius: 999px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="width: {{ $propertyMaster->paid_percentage }}%; height: 100%; background: linear-gradient(90deg, #10B981, #34D399); border-radius: 999px; transition: width 0.3s ease;"></div>
                </div>
            </div>
            @if($propertyMaster->address || $propertyMaster->notes)
            <div class="pm-item" style="grid-column: 1 / -1;">
                <span class="pm-label">Address &amp; Notes</span>
                <span class="pm-value" style="font-size: 13.5px; font-weight: 600; color: #CBD5E1;">
                    @if($propertyMaster->address) {{ $propertyMaster->address }}, @endif
                    @if($propertyMaster->state) {{ $propertyMaster->state }} @endif
                    @if($propertyMaster->pincode) - {{ $propertyMaster->pincode }} @endif
                    @if($propertyMaster->notes) <div style="margin-top: 4px; font-size: 12.5px; color: #94A3B8;"><i class="fa-solid fa-note-sticky"></i> {{ $propertyMaster->notes }}</div> @endif
                </span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- ================================================================
     KPI SUMMARY STRIP
================================================================ -->
<div class="kpi-strip" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(59, 130, 246, 0.18); color: #60A5FA;">
            <i class="fa-solid fa-border-all"></i>
        </div>
        <div class="kpi-content">
            <span class="kpi-label">Total Plots</span>
            <span class="kpi-val">{{ $totalPlots }}</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(16, 185, 129, 0.18); color: #34D399;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="kpi-content">
            <span class="kpi-label">Available Free</span>
            <span class="kpi-val" style="color: #34D399;">{{ $unassignedPlots }}</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(168, 85, 247, 0.18); color: #C084FC;">
            <i class="fa-solid fa-diagram-project"></i>
        </div>
        <div class="kpi-content">
            <span class="kpi-label">In Projects</span>
            <span class="kpi-val" style="color: #C084FC;">{{ $assignedPlots }}</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(16, 185, 129, 0.18); color: #34D399;">
            <i class="fa-solid fa-money-bill-wave"></i>
        </div>
        <div class="kpi-content">
            <span class="kpi-label">Paid Amount</span>
            <span class="kpi-val" style="color: #34D399; font-size: 15px;">₹{{ number_format($propertyMaster->paid_amount ?? 0, 2) }}</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(239, 68, 68, 0.18); color: #F87171;">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div class="kpi-content">
            <span class="kpi-label">Due Balance</span>
            <span class="kpi-val" style="color: #F87171; font-size: 15px;">₹{{ number_format($propertyMaster->due_amount ?? 0, 2) }}</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(20, 184, 166, 0.18); color: #2DD4BF;">
            <i class="fa-solid fa-ruler-combined"></i>
        </div>
        <div class="kpi-content">
            <span class="kpi-label">Total Sq. Ft</span>
            <span class="kpi-val" style="color: #2DD4BF;">{{ number_format($totalAreaSqft) }}</span>
        </div>
    </div>
</div>

<!-- ================================================================
     DIRECT PLOTS MANAGEMENT SECTION
================================================================ -->
<div class="card-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h3 style="font-size: 19px; font-weight: 800; color: #FFFFFF; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-shapes" style="color: #60A5FA;"></i>
                Plots &amp; Units Inventory ({{ $totalPlots }})
            </h3>
            <p style="font-size: 13px; color: #94A3B8; margin: 3px 0 0 0;">
                Directly manage plots, generate sequential units, or import from Excel.
            </p>
        </div>
        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('property-masters.plots.template') }}" class="btn-secondary-custom" title="Download Excel format template">
                <i class="fa-solid fa-file-excel" style="color: #10B981;"></i> Template
            </a>
            <button type="button" class="btn-secondary-custom" onclick="openModal('importExcelModal')">
                <i class="fa-solid fa-file-import" style="color: #60A5FA;"></i> Import Excel
            </button>
            <button type="button" class="btn-purple-custom" onclick="openModal('bulkPlotsModal')">
                <i class="fa-solid fa-bolt"></i> Bulk Generate
            </button>
            <button type="button" class="btn-gold" onclick="openModal('addPlotModal')">
                <i class="fa-solid fa-plus"></i> Add Plot
            </button>
        </div>
    </div>

    <!-- Filter Strip -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
        <div class="filter-tabs">
            <button type="button" class="filter-tab-btn active" onclick="filterPlots('all', this)">All ({{ $totalPlots }})</button>
            <button type="button" class="filter-tab-btn" onclick="filterPlots('available', this)">Available Free ({{ $unassignedPlots }})</button>
            <button type="button" class="filter-tab-btn" onclick="filterPlots('in-project', this)">In Project ({{ $assignedPlots }})</button>
            <button type="button" class="filter-tab-btn" onclick="filterPlots('booked', this)">Booked</button>
            <button type="button" class="filter-tab-btn" onclick="filterPlots('sold', this)">Sold</button>
        </div>

        <div class="search-input-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="plotSearchInput" class="search-input-box" placeholder="Search plot name, unit, code..." oninput="searchPlots(this.value)">
        </div>
    </div>

    <!-- Plots Table -->
    <div class="custom-table-responsive">
        <table class="custom-table" id="plotsTable">
            <thead>
                <tr>
                    <th style="width: 80px;">Unit #</th>
                    <th>Plot Name</th>
                    <th>Plot Code</th>
                    <th>Type</th>
                    <th>Size &amp; Facing</th>
                    <th>Purchase Rate / Price</th>
                    <th>Assigned Project</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($propertyMaster->plots as $plot)
                    @php
                        $filterCategory = 'available';
                        if ($plot->status === 'booked' || $plot->status === 'sold' || $plot->status === 'reserved') {
                            $filterCategory = $plot->status;
                        } elseif ($plot->project_id) {
                            $filterCategory = 'in-project';
                        }
                    @endphp
                    <tr class="plot-row" data-category="{{ $filterCategory }}" data-search="{{ strtolower($plot->property_name . ' ' . $plot->property_code . ' ' . $plot->unit_no . ' ' . ($plot->project?->project_name ?? '')) }}">
                        <td>
                            <strong style="color: #FFFFFF; font-size: 14px;">#{{ $plot->unit_no ?: $plot->id }}</strong>
                        </td>
                        <td>
                            <strong style="color: #F8FAFC; font-size: 14px;">{{ $plot->property_name }}</strong>
                        </td>
                        <td>
                            <code style="background: rgba(255, 255, 255, 0.08); color: #60A5FA; padding: 2px 6px; border-radius: 4px; font-size: 12px;">{{ $plot->property_code }}</code>
                        </td>
                        <td>
                            <span style="color: #CBD5E1;">{{ $plot->propertyType->name ?? 'Plot' }}</span>
                        </td>
                        <td>
                            <div style="font-size: 13px;">
                                @if($plot->size)
                                    <span style="color: #2DD4BF; font-weight: 700;">{{ $plot->formatted_size }}</span>
                                @else
                                    <span style="color: #94A3B8;">—</span>
                                @endif
                                @if($plot->facing)
                                    <small style="color: #94A3B8; display: block;">Facing: {{ $plot->facing }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 13px;">
                                <strong style="color: #FBBF24;">₹{{ number_format($plot->purchase_rate ?: 0, 2) }}</strong>
                                @if($plot->price && $plot->price != $plot->purchase_rate)
                                    <small style="color: #94A3B8; display: block;">Sell: ₹{{ number_format($plot->price, 2) }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($plot->project)
                                <a href="{{ route('projects.show', $plot->project->id) }}" style="color: #93C5FD; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-diagram-project" style="font-size: 11px;"></i> {{ $plot->project->project_name }}
                                </a>
                            @else
                                <span style="color: #94A3B8; font-size: 12px; font-style: italic;">Unassigned (Free)</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $plot->status }}">
                                <i class="fa-solid fa-circle-dot"></i> {{ ucfirst($plot->status) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('properties.edit', $plot->id) }}" class="btn-secondary-custom" style="padding: 4px 8px; font-size: 12px;" title="Edit Plot Details">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('properties.destroy', $plot->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plot?')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-secondary-custom" style="padding: 4px 8px; font-size: 12px; color: #F87171 !important;" title="Delete Plot">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; color: #94A3B8; padding: 36px;">
                            <div style="font-size: 36px; margin-bottom: 8px; color: #475569;"><i class="fa-solid fa-border-none"></i></div>
                            <strong style="color: #FFFFFF; font-size: 15px; display: block; margin-bottom: 4px;">No plots created yet</strong>
                            <span>Add single plots, generate multiple sequential plots, or import from Excel.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ================================================================
     ASSOCIATED PROJECTS SECTION
================================================================ -->
<div class="card-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h3 style="font-size: 18px; font-weight: 800; color: #FFFFFF; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-diagram-project" style="color: #A78BFA;"></i>
                Associated Projects ({{ $propertyMaster->projects->count() }})
            </h3>
            <p style="font-size: 13px; color: #94A3B8; margin: 3px 0 0 0;">
                Projects using land or plots from this Property Master.
            </p>
        </div>
        <a href="{{ route('projects.create', ['property_id' => $propertyMaster->id]) }}" class="btn-gold" style="font-size: 13px; padding: 6px 14px;">
            <i class="fa-solid fa-plus"></i> New Project
        </a>
    </div>

    <div class="custom-table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Plots Allocated</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($propertyMaster->projects as $project)
                    @php
                        $plotsCount = $project->properties()->where('property_master_id', $propertyMaster->id)->count();
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('projects.show', $project->id) }}" style="color: #FFFFFF; font-weight: 800; text-decoration: none; font-size: 14.5px;">
                                {{ $project->project_name }}
                            </a>
                        </td>
                        <td><code style="background: rgba(255, 255, 255, 0.08); color: #60A5FA; padding: 2px 6px; border-radius: 4px; font-size: 12.5px;">{{ $project->project_code }}</code></td>
                        <td><span style="color: #CBD5E1; text-transform: capitalize;">{{ $project->project_type }}</span></td>
                        <td>
                            <strong style="color: #34D399;">{{ $plotsCount }} Plots</strong> from this Master
                        </td>
                        <td>
                            <span class="badge {{ $project->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('projects.show', $project->id) }}" class="btn-secondary-custom" style="padding: 6px 14px; min-height: 32px; font-size: 12.5px;">
                                Open Project <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94A3B8; padding: 24px; font-weight: 600;">
                            No Projects created under this Property yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ================================================================
     MODAL 1: ADD SINGLE PLOT
================================================================ -->
<div class="modal-backdrop-custom" id="addPlotModal">
    <div class="modal-box-custom">
        <div class="modal-header-custom">
            <h3><i class="fa-solid fa-plus-circle" style="color: #D4AF37;"></i> Add Single Plot</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('addPlotModal')">&times;</button>
        </div>
        <form action="{{ route('property-masters.add-plot', $propertyMaster->id) }}" method="POST">
            @csrf
            <div class="modal-body-custom">
                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Plot Name <span>*</span></label>
                        <input type="text" name="property_name" class="m-form-control" placeholder="e.g. Plot 15" required>
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Unit / Plot Number</label>
                        <input type="text" name="unit_no" class="m-form-control" value="{{ $propertyMaster->getNextPlotSequenceNumber() }}" placeholder="e.g. 15">
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Property Type</label>
                        <select name="property_type_id" class="m-form-control">
                            @foreach($propertyTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Facing Direction</label>
                        <select name="facing" class="m-form-control">
                            <option value="">Select Direction</option>
                            <option value="East">East</option>
                            <option value="West">West</option>
                            <option value="North">North</option>
                            <option value="South">South</option>
                            <option value="North-East">North-East</option>
                            <option value="North-West">North-West</option>
                            <option value="South-East">South-East</option>
                            <option value="South-West">South-West</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Plot Size</label>
                        <input type="number" step="0.01" name="size" class="m-form-control" placeholder="e.g. 1200">
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Size Unit</label>
                        <select name="size_unit" class="m-form-control">
                            <option value="sq.ft" selected>sq.ft</option>
                            <option value="sq.yard">sq.yard</option>
                            <option value="sq.meter">sq.meter</option>
                            <option value="acre">acre</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Purchase Rate (₹)</label>
                        <input type="number" step="0.01" name="purchase_rate" value="{{ $propertyMaster->purchase_rate }}" class="m-form-control" placeholder="e.g. 1500">
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Selling / Expected Price (₹)</label>
                        <input type="number" step="0.01" name="price" value="{{ $propertyMaster->purchase_rate }}" class="m-form-control" placeholder="e.g. 2000">
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Initial Status <span>*</span></label>
                        <select name="status" class="m-form-control" required>
                            <option value="available" selected>Available (Free)</option>
                            <option value="booked">Booked</option>
                            <option value="sold">Sold</option>
                            <option value="reserved">Reserved</option>
                        </select>
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Assign to Project (Optional)</label>
                        <select name="project_id" class="m-form-control">
                            <option value="">-- No Project (Keep in Master Inventory) --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="m-form-group">
                    <label class="m-form-label">Description / Remarks</label>
                    <textarea name="description" class="m-form-control" rows="2" placeholder="Optional notes..."></textarea>
                </div>
            </div>
            <div style="padding: 16px 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); display: flex; justify-content: flex-end; gap: 10px; background: rgba(30, 41, 59, 0.50);">
                <button type="button" class="btn-secondary-custom" onclick="closeModal('addPlotModal')">Cancel</button>
                <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Save Plot</button>
            </div>
        </form>
    </div>
</div>

<!-- ================================================================
     MODAL 2: BULK PLOTS GENERATOR
================================================================ -->
<div class="modal-backdrop-custom" id="bulkPlotsModal">
    <div class="modal-box-custom">
        <div class="modal-header-custom">
            <h3><i class="fa-solid fa-bolt" style="color: #A855F7;"></i> Bulk Generate Plots</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('bulkPlotsModal')">&times;</button>
        </div>
        <form action="{{ route('property-masters.bulk-generate-plots', $propertyMaster->id) }}" method="POST">
            @csrf
            <div class="modal-body-custom">
                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Total Plots to Generate <span>*</span></label>
                        <input type="number" min="1" max="1000" name="total_plots" class="m-form-control" value="10" required>
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Plot Name Prefix</label>
                        <input type="text" name="plot_prefix" class="m-form-control" value="Plot " placeholder="e.g. Plot ">
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Starting Unit Number</label>
                        <input type="number" min="1" name="start_number" class="m-form-control" value="{{ $propertyMaster->getNextPlotSequenceNumber() }}" placeholder="e.g. 1">
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Property Type</label>
                        <select name="property_type_id" class="m-form-control">
                            @foreach($propertyTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Default Size per Plot</label>
                        <input type="number" step="0.01" name="size" class="m-form-control" placeholder="e.g. 1200">
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Size Unit</label>
                        <select name="size_unit" class="m-form-control">
                            <option value="sq.ft" selected>sq.ft</option>
                            <option value="sq.yard">sq.yard</option>
                            <option value="sq.meter">sq.meter</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Purchase Rate (₹)</label>
                        <input type="number" step="0.01" name="purchase_rate" value="{{ $propertyMaster->purchase_rate }}" class="m-form-control" placeholder="e.g. 1500">
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Selling Price (₹)</label>
                        <input type="number" step="0.01" name="price" value="{{ $propertyMaster->purchase_rate }}" class="m-form-control" placeholder="e.g. 2200">
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Facing Direction</label>
                        <select name="facing" class="m-form-control">
                            <option value="">Not Specified</option>
                            <option value="East">East</option>
                            <option value="West">West</option>
                            <option value="North">North</option>
                            <option value="South">South</option>
                        </select>
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Assign Directly to Project (Optional)</label>
                        <select name="project_id" class="m-form-control">
                            <option value="">-- Keep in Available Master Inventory --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div style="padding: 16px 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); display: flex; justify-content: flex-end; gap: 10px; background: rgba(30, 41, 59, 0.50);">
                <button type="button" class="btn-secondary-custom" onclick="closeModal('bulkPlotsModal')">Cancel</button>
                <button type="submit" class="btn-purple-custom"><i class="fa-solid fa-bolt"></i> Generate Plots Now</button>
            </div>
        </form>
    </div>
</div>

<!-- ================================================================
     MODAL 3: IMPORT EXCEL
================================================================ -->
<div class="modal-backdrop-custom" id="importExcelModal">
    <div class="modal-box-custom">
        <div class="modal-header-custom">
            <h3><i class="fa-solid fa-file-excel" style="color: #10B981;"></i> Import Plots from Excel / CSV</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('importExcelModal')">&times;</button>
        </div>
        <form action="{{ route('property-masters.import-plots', $propertyMaster->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body-custom">
                <div style="background: rgba(59, 130, 246, 0.10); border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 10px; padding: 14px; margin-bottom: 18px; font-size: 13px; color: #CBD5E1;">
                    <strong style="color: #60A5FA; display: block; margin-bottom: 4px;"><i class="fa-solid fa-circle-info"></i> Instructions:</strong>
                    Upload an Excel (.xlsx, .xls) or CSV file with plot columns (Plot No, Plot Name, Size, Facing, Purchase Rate, Price).
                    <div style="margin-top: 8px;">
                        <a href="{{ route('property-masters.plots.template') }}" style="color: #34D399; font-weight: 700; text-decoration: underline;">
                            <i class="fa-solid fa-download"></i> Download sample template file
                        </a>
                    </div>
                </div>

                <div class="m-form-group" style="margin-bottom: 16px;">
                    <label class="m-form-label">Select Excel / CSV File <span>*</span></label>
                    <input type="file" name="excel_file" class="m-form-control" accept=".xlsx,.xls,.csv" required>
                </div>

                <div class="m-form-group">
                    <label class="m-form-label">Assign Imported Plots to Project (Optional)</label>
                    <select name="project_id" class="m-form-control">
                        <option value="">-- Keep in Available Master Inventory --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->project_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="padding: 16px 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); display: flex; justify-content: flex-end; gap: 10px; background: rgba(30, 41, 59, 0.50);">
                <button type="button" class="btn-secondary-custom" onclick="closeModal('importExcelModal')">Cancel</button>
                <button type="submit" class="btn-success-custom"><i class="fa-solid fa-upload"></i> Upload &amp; Import</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('active');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('active');
}

window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-backdrop-custom')) {
        e.target.classList.remove('active');
    }
});

let currentCategory = 'all';
let currentSearch = '';

function filterPlots(category, btn) {
    currentCategory = category;
    document.querySelectorAll('.filter-tab-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    applyPlotFilters();
}

function searchPlots(query) {
    currentSearch = (query || '').toLowerCase().trim();
    applyPlotFilters();
}

function applyPlotFilters() {
    const rows = document.querySelectorAll('.plot-row');
    rows.forEach(row => {
        const cat = row.getAttribute('data-category');
        const searchTxt = row.getAttribute('data-search') || '';

        const matchesCat = (currentCategory === 'all') || (cat === currentCategory);
        const matchesSearch = !currentSearch || searchTxt.includes(currentSearch);

        if (matchesCat && matchesSearch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endsection
