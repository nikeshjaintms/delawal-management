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
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.kpi-purple { background: rgba(139, 92, 246, 0.20); border: 1px solid rgba(139, 92, 246, 0.40); color: #A78BFA; }
.kpi-blue   { background: rgba(59, 130, 246, 0.20); border: 1px solid rgba(59, 130, 246, 0.40); color: #60A5FA; }
.kpi-green  { background: rgba(16, 185, 129, 0.20); border: 1px solid rgba(16, 185, 129, 0.40); color: #34D399; }
.kpi-amber  { background: rgba(245, 158, 11, 0.20); border: 1px solid rgba(245, 158, 11, 0.40); color: #FBBF24; }
.kpi-teal   { background: rgba(20, 184, 166, 0.20); border: 1px solid rgba(20, 184, 166, 0.40); color: #2DD4BF; }

.kpi-content { display: flex; flex-direction: column; }
.kpi-label { font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px; }
.kpi-val { font-size: 17px; font-weight: 800; color: #FFFFFF; line-height: 1.2; margin-top: 2px; }

/* ── Buttons & Badges ── */
.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom, .btn-gold {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 18px; min-height: 40px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 14px rgba(37,99,235,0.35);
    cursor: pointer;
}
.btn-primary-custom:hover, .btn-gold:hover {
    background: #1D4ED8 !important; color: #FFFFFF !important;
}

.btn-secondary-custom, a.btn-secondary-custom, button.btn-secondary-custom, .btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 18px; min-height: 40px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.20) !important;
    border-radius: 10px; text-decoration: none !important; cursor: pointer;
}
.btn-secondary-custom:hover, .btn-outline:hover {
    background: rgba(255, 255, 255, 0.16) !important; color: #FFFFFF !important; border-color: rgba(255, 255, 255, 0.30) !important;
}

.btn-success-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 18px; min-height: 40px; background: #059669 !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 700; border: 1px solid #10B981 !important;
    border-radius: 10px; text-decoration: none !important; cursor: pointer; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.30);
}
.btn-success-custom:hover {
    background: #047857 !important; color: #FFFFFF !important;
}

.tbl-actions-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
}

.btn-tbl-edit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 32px;
    padding: 0 14px;
    background: #2563EB !important;
    color: #FFFFFF !important;
    font-size: 12.5px;
    font-weight: 700;
    border: 1px solid #3B82F6 !important;
    border-radius: 8px;
    text-decoration: none !important;
    cursor: pointer;
    box-sizing: border-box;
    line-height: 1;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35);
    transition: background 0.15s ease;
}
.btn-tbl-edit:hover {
    background: #1D4ED8 !important;
    color: #FFFFFF !important;
}

.btn-tbl-view {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 32px;
    padding: 0 14px;
    background: #059669 !important;
    color: #FFFFFF !important;
    font-size: 12.5px;
    font-weight: 700;
    border: 1px solid #10B981 !important;
    border-radius: 8px;
    text-decoration: none !important;
    cursor: pointer;
    box-sizing: border-box;
    line-height: 1;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.35);
    transition: background 0.15s ease;
}
.btn-tbl-view:hover {
    background: #047857 !important;
    color: #FFFFFF !important;
}

.badge {
    display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px;
    font-size: 11px; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.4px;
}
.badge-active, .badge-available { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive, .badge-sold { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-booked { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-project { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }

/* ── Acquisition Batch Card ── */
.batch-card {
    background: rgba(16, 22, 34, 0.75) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 18px !important;
    padding: 20px !important;
    margin-bottom: 20px;
}

.batch-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 16px;
}
.batch-title-wrap { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.batch-title { font-size: 18px; font-weight: 800; color: #FFFFFF !important; }

.batch-stats-strip {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 16px;
}
@media (max-width: 860px) { .batch-stats-strip { grid-template-columns: repeat(2, 1fr); } }

.bss-item { display: flex; flex-direction: column; }
.bss-label { font-size: 10.5px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px; }
.bss-val { font-size: 14.5px; font-weight: 800; color: #FFFFFF; margin-top: 2px; }

.table-wrapper {
    width: 100%;
    overflow-x: auto;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.10);
    background: rgba(10, 14, 23, 0.65);
}
.custom-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.custom-table th {
    padding: 12px 16px;
    background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important;
    font-weight: 800 !important;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    text-align: left;
    white-space: nowrap;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
}
.custom-table td {
    padding: 12px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    color: #E2E8F0 !important;
    font-weight: 600;
    vertical-align: middle;
    white-space: nowrap;
}
.custom-table tbody tr:hover { background: rgba(255, 255, 255, 0.04); }
.custom-table tbody tr:last-child td { border-bottom: none; }

/* ── Modal Styling ── */
.modal-backdrop-custom {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(8px);
    z-index: 1000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.modal-backdrop-custom.active { display: flex; }
.modal-box-custom {
    background: #101622;
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 20px;
    width: 100%;
    max-width: 680px;
    max-height: 90vh;
    overflow-y: auto;
    padding: 28px;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.60);
    position: relative;
}
.modal-header-custom {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.10);
}
.modal-header-custom h3 { font-size: 20px; font-weight: 800; color: #FFFFFF; }
.modal-close-btn {
    background: transparent; border: none; font-size: 20px; color: #94A3B8; cursor: pointer; transition: color .2s;
}
.modal-close-btn:hover { color: #FFFFFF; }

.m-form-group { margin-bottom: 16px; }
.m-form-label { display: block; font-size: 13px; font-weight: 700; color: #E2E8F0; margin-bottom: 6px; }
.m-form-label span { color: #EF4444; }
.m-form-control {
    width: 100%; padding: 10px 14px; background: rgba(255, 255, 255, 0.06);
    border: 1.5px solid rgba(255, 255, 255, 0.14); border-radius: 10px; color: #FFFFFF; font-size: 13.5px; outline: none; transition: border-color .2s;
}
.m-form-control:focus { border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25); }
select.m-form-control option { background: #101622; color: #FFFFFF; }
.m-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 580px) { .m-form-row { grid-template-columns: 1fr; gap: 0; } }

.generator-box {
    background: rgba(59, 130, 246, 0.08);
    border: 1px solid rgba(59, 130, 246, 0.25);
    border-radius: 14px;
    padding: 16px;
    margin-top: 14px;
    margin-bottom: 16px;
}
</style>

{{-- Breadcrumb --}}
<div class="breadcrumb-nav">
    <span><i class="fa-solid fa-city" style="color: #60A5FA; margin-right: 6px;"></i>Property Management</span>
    <i class="fa-solid fa-chevron-right separator"></i>
    <a href="{{ route('property-masters.index') }}">Property Master</a>
    <i class="fa-solid fa-chevron-right separator"></i>
    <span class="active">{{ $propertyMaster->property_name }}</span>
</div>

<div class="crud-header">
    <div class="crud-title">
        <h2>{{ $propertyMaster->property_name }}</h2>
        <p>Property Master, Acquisition Batches &amp; Projects Hierarchy</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <button type="button" class="btn-gold" onclick="openAddBatchModal()">
            <i class="fa-solid fa-layer-group"></i> + Add Acquisition Batch
        </button>
        <a href="{{ route('projects.create', ['property_id' => $propertyMaster->id]) }}" class="btn-success-custom">
            <i class="fa-solid fa-diagram-project"></i> + Create Project
        </a>
        @if(Auth::user() && Auth::user()->hasPermission('property_edit'))
            <a href="{{ route('property-masters.edit', $propertyMaster->id) }}" class="btn-secondary-custom">
                <i class="fa-regular fa-pen-to-square"></i> Edit
            </a>
        @endif
        <a href="{{ route('property-masters.index') }}" class="btn-secondary-custom">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.18); border: 1px solid rgba(16, 185, 129, 0.35); color: #34D399; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background: rgba(239, 68, 68, 0.18); border: 1px solid rgba(239, 68, 68, 0.35); color: #F87171; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
    </div>
@endif

@php
    $totalBatches   = $propertyMaster->acquisitionBatches->count();
    $totalPlots     = $propertyMaster->plots->count();
    $totalInvest    = $propertyMaster->acquisitionBatches->sum('total_purchase_amount');
    $unassignedPlots = $propertyMaster->plots->whereNull('project_id')->count();
    $assignedPlots  = $propertyMaster->plots->whereNotNull('project_id')->count();
@endphp

<!-- ================================================================
     TOP SECTION: FULL WIDTH PROPERTY MASTER OVERVIEW HERO CARD
================================================================ -->
<div class="card-box">
    <div class="property-hero-grid">
        <!-- Hero Avatar / Image -->
        <div class="property-hero-avatar">
            @if($propertyMaster->main_image)
                <img src="{{ asset('storage/' . $propertyMaster->main_image) }}" alt="{{ $propertyMaster->property_name }}">
            @else
                <i class="fa-solid fa-city"></i>
            @endif
        </div>

        <!-- Meta Grid -->
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
                <span class="pm-label">City</span>
                <span class="pm-value">{{ $propertyMaster->city ?? '-' }}</span>
            </div>
            <div class="pm-item">
                <span class="pm-label">Location / Area</span>
                <span class="pm-value">{{ $propertyMaster->location ?? '-' }}</span>
            </div>
            <div class="pm-item">
                <span class="pm-label">Status</span>
                <span class="pm-value">
                    <span class="badge {{ $propertyMaster->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        <i class="fa-solid fa-circle-dot"></i> {{ ucfirst($propertyMaster->status) }}
                    </span>
                </span>
            </div>
            <div class="pm-item" style="grid-column: span 2;">
                <span class="pm-label">Address &amp; Notes</span>
                <span class="pm-value" style="font-size: 13.5px; font-weight: 600; color: #CBD5E1;">
                    @if($propertyMaster->address) {{ $propertyMaster->address }}, @endif
                    @if($propertyMaster->state) {{ $propertyMaster->state }} @endif
                    @if($propertyMaster->pincode) - {{ $propertyMaster->pincode }} @endif
                    @if(!$propertyMaster->address && !$propertyMaster->city) <span style="color: #94A3B8;">No address specified</span> @endif
                </span>
            </div>
        </div>
    </div>

    @if($propertyMaster->description)
        <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid rgba(255, 255, 255, 0.08); font-size: 13px; color: #CBD5E1;">
            <strong style="color: #94A3B8; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 3px;">Description:</strong>
            {{ $propertyMaster->description }}
        </div>
    @endif
</div>

{{-- KPI Summary Strip (Full Width) --}}
<div class="kpi-strip">
    <div class="kpi-card">
        <div class="kpi-icon kpi-purple"><i class="fa-solid fa-layer-group"></i></div>
        <div class="kpi-content">
            <span class="kpi-label">Acquisition Batches</span>
            <span class="kpi-val">{{ $totalBatches }}</span>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon kpi-blue"><i class="fa-solid fa-boxes-stacked"></i></div>
        <div class="kpi-content">
            <span class="kpi-label">Total Plots Acquired</span>
            <span class="kpi-val">{{ $totalPlots }}</span>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon kpi-amber"><i class="fa-solid fa-indian-rupee-sign"></i></div>
        <div class="kpi-content">
            <span class="kpi-label">Total Purchase Value</span>
            <span class="kpi-val">₹{{ number_format($totalInvest, 2) }}</span>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon kpi-green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="kpi-content">
            <span class="kpi-label">Available / Unassigned</span>
            <span class="kpi-val">{{ $unassignedPlots }} Plots</span>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon kpi-teal"><i class="fa-solid fa-diagram-project"></i></div>
        <div class="kpi-content">
            <span class="kpi-label">Assigned to Projects</span>
            <span class="kpi-val">{{ $assignedPlots }} Plots</span>
        </div>
    </div>
</div>

<!-- ================================================================
     BOTTOM SECTION 1: ACQUISITION BATCHES (FULL WIDTH)
================================================================ -->
<div class="card-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h3 style="font-size: 18px; font-weight: 800; color: #FFFFFF; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-layer-group" style="color: #A78BFA;"></i>
                Acquisition Batches ({{ $totalBatches }})
            </h3>
            <p style="font-size: 13px; color: #94A3B8; margin: 3px 0 0 0;">
                Plots purchased at different times and rates remain separately identifiable.
            </p>
        </div>
        <button type="button" class="btn-gold" onclick="openAddBatchModal()" style="padding: 7px 16px; min-height: 36px; font-size: 13px;">
            <i class="fa-solid fa-plus"></i> Add Acquisition Batch
        </button>
    </div>

    @forelse($propertyMaster->acquisitionBatches as $batch)
        <div class="batch-card" id="batch-card-{{ $batch->id }}">
            <div class="batch-card-header">
                <div>
                    <div class="batch-title-wrap">
                        <span class="batch-title">{{ $batch->batch_name }}</span>
                        <code style="background: rgba(167, 139, 250, 0.18); color: #C4B5FD; border: 1px solid rgba(167, 139, 250, 0.35); padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 800;">
                            {{ $batch->batch_number ?: 'BATCH-' . str_pad($batch->id, 3, '0', STR_PAD_LEFT) }}
                        </code>
                        <span class="badge {{ $batch->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                            {{ ucfirst($batch->status) }}
                        </span>
                    </div>
                    @if($batch->description)
                        <div style="font-size: 13px; color: #94A3B8; margin-top: 4px;">{{ $batch->description }}</div>
                    @endif
                </div>
                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <button type="button" class="btn-success-custom" onclick="openAddPlotsModal({{ $batch->id }}, '{{ addslashes($batch->batch_name) }}', {{ $batch->purchase_rate }})" style="padding: 6px 12px; min-height: 32px; font-size: 12.5px;">
                        <i class="fa-solid fa-plus"></i> Add Plots
                    </button>
                    <button type="button" class="btn-secondary-custom" onclick="toggleBatchPlots({{ $batch->id }})" style="padding: 6px 12px; min-height: 32px; font-size: 12.5px;">
                        <i class="fa-solid fa-list-check"></i> <span id="toggle-text-{{ $batch->id }}">View Plots ({{ $batch->plots->count() }})</span>
                    </button>
                    <form action="{{ route('acquisition-batches.destroy', $batch->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this acquisition batch and its unbooked plots?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-secondary-custom" style="padding: 6px 10px; min-height: 32px; color: #FCA5A5 !important;" title="Delete Batch">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Batch Stat Strip -->
            <div class="batch-stats-strip">
                <div class="bss-item">
                    <span class="bss-label">Total Plots</span>
                    <span class="bss-val" style="color: #60A5FA;">{{ $batch->plots->count() }} Plots</span>
                </div>
                <div class="bss-item">
                    <span class="bss-label">Purchase Rate</span>
                    <span class="bss-val" style="color: #FBBF24;">₹{{ number_format($batch->purchase_rate, 2) }} <small style="font-size: 11px; font-weight: normal; color: #94A3B8;">/{{ str_replace('_', ' ', $batch->rate_unit) }}</small></span>
                </div>
                <div class="bss-item">
                    <span class="bss-label">Purchase Date</span>
                    <span class="bss-val">{{ $batch->purchase_date ? $batch->purchase_date->format('d M Y') : '-' }}</span>
                </div>
                <div class="bss-item">
                    <span class="bss-label">Total Amount</span>
                    <span class="bss-val" style="color: #34D399;">₹{{ number_format($batch->total_purchase_amount ?: ($batch->purchase_rate * $batch->plots->count()), 2) }}</span>
                </div>
                <div class="bss-item">
                    <span class="bss-label">Availability</span>
                    <span class="bss-val" style="font-size: 13px;">
                        <span style="color: #34D399;">{{ $batch->plots->whereNull('project_id')->count() }} Free</span> /
                        <span style="color: #60A5FA;">{{ $batch->plots->whereNotNull('project_id')->count() }} in Project</span>
                    </span>
                </div>
            </div>

            <!-- Expandable Plots List Table -->
            <div id="batch-plots-wrap-{{ $batch->id }}" style="display: none; margin-top: 14px;">
                <div class="table-wrapper">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Plot Name</th>
                                <th>Code</th>
                                <th>Size</th>
                                <th>Facing</th>
                                <th>Purchase Rate</th>
                                <th>Assigned Project</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batch->plots as $plot)
                                <tr>
                                    <td>
                                        <strong style="color: #FFFFFF;">{{ $plot->property_name }}</strong>
                                    </td>
                                    <td><code style="background: rgba(255,255,255,0.06); color: #60A5FA; padding: 2px 6px; border-radius: 4px; font-size: 12px;">{{ $plot->property_code }}</code></td>
                                    <td>{{ $plot->size ? $plot->size . ' ' . ($plot->size_unit ?? 'sq.ft') : '-' }}</td>
                                    <td>{{ $plot->facing ?: '-' }}</td>
                                    <td><strong style="color: #FBBF24;">₹{{ number_format($plot->purchase_rate ?: $batch->purchase_rate, 2) }}</strong></td>
                                    <td>
                                        @if($plot->project)
                                            <a href="{{ route('projects.show', $plot->project_id) }}" class="badge badge-project" style="text-decoration: none;">
                                                <i class="fa-solid fa-diagram-project"></i> {{ $plot->project->project_name }}
                                            </a>
                                        @else
                                            <span class="badge badge-available"><i class="fa-solid fa-unlock"></i> Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $plot->status }}">
                                            {{ ucfirst($plot->status) }}
                                        </span>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <div class="tbl-actions-wrap">
                                            <button type="button" class="btn-tbl-edit" onclick="openQuickEditPlotModal({{ $plot->id }}, '{{ addslashes($plot->property_name) }}', '{{ addslashes($plot->property_code) }}', '{{ $plot->size }}', '{{ $plot->size_unit }}', '{{ $plot->facing }}', '{{ $plot->purchase_rate }}', '{{ $plot->price }}', '{{ $plot->status }}', '{{ addslashes($plot->description ?? '') }}')">
                                                <i class="fa-regular fa-pen-to-square"></i> Edit
                                            </button>
                                            <a href="{{ route('properties.show', $plot->id) }}" class="btn-tbl-view">
                                                <i class="fa-regular fa-eye"></i> View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; color: #94A3B8; padding: 20px;">
                                        No plots created in this batch yet. Click <strong style="color: #34D399;">+ Add Plots</strong> above.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div style="text-align: center; padding: 36px; background: rgba(255, 255, 255, 0.02); border: 1px dashed rgba(255, 255, 255, 0.15); border-radius: 16px;">
            <div style="font-size: 32px; color: #A78BFA; margin-bottom: 10px;"><i class="fa-solid fa-layer-group"></i></div>
            <h4 style="font-size: 16px; font-weight: 700; color: #FFFFFF; margin-bottom: 6px;">No Acquisition Batches Added Yet</h4>
            <p style="font-size: 13.5px; color: #94A3B8; max-width: 460px; margin: 0 auto 16px auto;">
                Add plots in acquisition batches with their respective purchase rates and dates to start managing inventory under this Property.
            </p>
            <button type="button" class="btn-gold" onclick="openAddBatchModal()">
                <i class="fa-solid fa-plus"></i> Create First Acquisition Batch
            </button>
        </div>
    @endforelse
</div>

<!-- ================================================================
     BOTTOM SECTION 2: MANAGED PROJECTS (FULL WIDTH)
================================================================ -->
<div class="card-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h3 style="font-size: 18px; font-weight: 800; color: #FFFFFF; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-diagram-project" style="color: #38BDF8;"></i>
                Managed Projects ({{ $propertyMaster->projects->count() }})
            </h3>
            <p style="font-size: 13px; color: #94A3B8; margin: 3px 0 0 0;">
                Projects created by combining plots from one or multiple acquisition batches.
            </p>
        </div>
        <a href="{{ route('projects.create', ['property_id' => $propertyMaster->id]) }}" class="btn-primary-custom" style="padding: 7px 16px; min-height: 36px; font-size: 13px;">
            <i class="fa-solid fa-plus"></i> Create Project
        </a>
    </div>

    <div class="table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Plots Breakdown</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($propertyMaster->projects as $project)
                    @php
                        $projectPlots = $project->properties;
                        $batchBreakdown = $projectPlots->groupBy('acquisition_batch_id');
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
                            <div style="display: flex; flex-direction: column; gap: 3px;">
                                <strong style="color: #FFFFFF;">{{ $projectPlots->count() }} Total Plots</strong>
                                @if($batchBreakdown->count() > 0)
                                    <small style="color: #94A3B8; font-size: 11.5px;">
                                        @foreach($batchBreakdown as $bId => $pGroup)
                                            @php $bName = $pGroup->first()->acquisitionBatch?->batch_name ?? 'Direct Plots'; @endphp
                                            <span style="display: inline-block; background: rgba(255,255,255,0.05); padding: 1px 6px; border-radius: 4px; margin-right: 4px; margin-top: 2px;">
                                                {{ $pGroup->count() }} from {{ $bName }}
                                            </span>
                                        @endforeach
                                    </small>
                                @endif
                            </div>
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
                        <td colspan="6" style="text-align: center; color: #94A3B8; padding: 28px; font-weight: 600;">
                            No Projects created under this Property yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ================================================================
     MODAL 1: ADD ACQUISITION BATCH (WITH INSTANT BULK PLOT GENERATOR)
================================================================ -->
<div class="modal-backdrop-custom" id="addBatchModal">
    <div class="modal-box-custom">
        <div class="modal-header-custom">
            <h3><i class="fa-solid fa-layer-group" style="color: #A78BFA; margin-right: 8px;"></i>Add Acquisition Batch</h3>
            <button type="button" class="modal-close-btn" onclick="closeAddBatchModal()">&times;</button>
        </div>

        <form id="addBatchForm" action="{{ route('acquisition-batches.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return handleBatchSubmit(event, this)">
            @csrf
            <input type="hidden" name="property_master_id" value="{{ $propertyMaster->id }}">
            <input type="hidden" name="firm_id" value="{{ $propertyMaster->firm_id }}">

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Batch Name <span>*</span></label>
                    <input type="text" name="batch_name" class="m-form-control" placeholder="e.g. Acquisition Batch 1" required>
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Batch Number / Code</label>
                    <input type="text" name="batch_number" class="m-form-control" placeholder="Auto-generated if empty">
                </div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Purchase Date <span>*</span></label>
                    <input type="date" name="purchase_date" class="m-form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Purchase Rate (INR) <span>*</span></label>
                    <input type="number" step="0.01" name="purchase_rate" id="modal_purchase_rate" class="m-form-control" placeholder="e.g. 10000" oninput="calculateTotalAmount()" required>
                </div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Rate Unit <span>*</span></label>
                    <select name="rate_unit" class="m-form-control" required>
                        <option value="per_plot" selected>Per Plot / Unit</option>
                        <option value="per_sqft">Per Sq. Ft</option>
                        <option value="per_sqyd">Per Sq. Yard</option>
                    </select>
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Total Purchase Amount (INR)</label>
                    <input type="number" step="0.01" name="total_purchase_amount" id="modal_total_amount" class="m-form-control" placeholder="Auto calculated or custom">
                </div>
            </div>

            <div class="m-form-group">
                <label class="m-form-label">Status <span>*</span></label>
                <select name="status" class="m-form-control" required>
                    <option value="active" selected>Active</option>
                    <option value="completed">Completed</option>
                    <option value="archived">Archived</option>
                </select>
            </div>

            <!-- Instant Bulk Plot Generator Toggle -->
            <div class="generator-box">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <strong style="color: #FFFFFF; font-size: 14px; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-wand-magic-sparkles" style="color: #60A5FA;"></i>
                        Instant Plot Generator for this Batch
                    </strong>
                    <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #93C5FD; cursor: pointer;">
                        <input type="checkbox" id="generate_plots_toggle" checked onchange="toggleGeneratorFields(this.checked)">
                        Generate plots now
                    </label>
                </div>

                <div id="generator_fields">
                    <div class="m-form-row">
                        <div class="m-form-group">
                            <label class="m-form-label">Number of Plots to Create <span>*</span></label>
                            <input type="number" min="1" max="500" name="plot_count" id="modal_plot_count" class="m-form-control" value="14" oninput="calculateTotalAmount()">
                        </div>
                        <div class="m-form-group">
                            <label class="m-form-label">Plot Name Prefix</label>
                            <input type="text" name="plot_prefix" class="m-form-control" value="Plot ">
                        </div>
                    </div>

                    <div class="m-form-row">
                        <div class="m-form-group">
                            <label class="m-form-label">Starting Plot Number (Continuous)</label>
                            <input type="number" min="1" name="start_number" class="m-form-control" value="{{ $propertyMaster->getNextPlotSequenceNumber() }}" placeholder="e.g. {{ $propertyMaster->getNextPlotSequenceNumber() }}">
                        </div>
                        <div class="m-form-group">
                            <label class="m-form-label">Plot Size (Area)</label>
                            <input type="text" name="plot_size" class="m-form-control" placeholder="e.g. 1200">
                        </div>
                    </div>

                    <div class="m-form-row">
                        <div class="m-form-group">
                            <label class="m-form-label">Size Unit</label>
                            <select name="plot_size_unit" class="m-form-control">
                                <option value="sq.ft" selected>sq.ft</option>
                                <option value="sq.yard">sq.yard</option>
                                <option value="sq.meter">sq.meter</option>
                                <option value="acre">acre</option>
                                <option value="bigha">bigha</option>
                            </select>
                        </div>
                        <div class="m-form-group">
                            <label class="m-form-label">Facing Direction</label>
                            <select name="plot_facing" class="m-form-control">
                                <option value="">Select (Optional)</option>
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
                </div>
            </div>

            <div class="m-form-group">
                <label class="m-form-label">Description / Purchase Notes</label>
                <textarea name="description" rows="2" class="m-form-control" placeholder="e.g. Purchased from landowner Survey No. 12/A"></textarea>
            </div>

            <div class="m-form-group">
                <label class="m-form-label">Upload Purchase Deed / Document</label>
                <input type="file" name="document_file" class="m-form-control">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.10);">
                <button type="button" class="btn-secondary-custom" onclick="closeAddBatchModal()">Cancel</button>
                <button type="submit" class="btn-gold">
                    <i class="fa-solid fa-check"></i> Save Acquisition Batch &amp; Plots
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================================================================
     MODAL 2: ADD PLOTS TO EXISTING BATCH
================================================================ -->
<div class="modal-backdrop-custom" id="addPlotsModal">
    <div class="modal-box-custom">
        <div class="modal-header-custom">
            <h3><i class="fa-solid fa-plus-circle" style="color: #34D399; margin-right: 8px;"></i>Add Plots to Batch</h3>
            <button type="button" class="modal-close-btn" onclick="closeAddPlotsModal()">&times;</button>
        </div>

        <form id="addPlotsForm" method="POST" onsubmit="return handleBatchSubmit(event, this)">
            @csrf
            <div style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 12px 16px; margin-bottom: 18px;">
                <div style="font-size: 12px; color: #94A3B8; text-transform: uppercase; font-weight: 700;">Target Batch</div>
                <div id="targetBatchName" style="font-size: 16px; font-weight: 800; color: #FFFFFF; margin-top: 2px;"></div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Number of Plots to Add <span>*</span></label>
                    <input type="number" min="1" max="500" name="plot_count" class="m-form-control" value="5" required>
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Plot Name Prefix</label>
                    <input type="text" name="plot_prefix" class="m-form-control" value="Plot ">
                </div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Starting Number (Continuous)</label>
                    <input type="number" min="1" name="start_number" id="ap_start_number" class="m-form-control" value="{{ $propertyMaster->getNextPlotSequenceNumber() }}" placeholder="e.g. {{ $propertyMaster->getNextPlotSequenceNumber() }}">
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Purchase Rate (INR)</label>
                    <input type="number" step="0.01" name="purchase_rate" id="ap_purchase_rate" class="m-form-control" placeholder="Inherit from batch">
                </div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Plot Size (Area)</label>
                    <input type="text" name="plot_size" class="m-form-control" placeholder="e.g. 1200">
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Size Unit</label>
                    <select name="plot_size_unit" class="m-form-control">
                        <option value="sq.ft" selected>sq.ft</option>
                        <option value="sq.yard">sq.yard</option>
                        <option value="sq.meter">sq.meter</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.10);">
                <button type="button" class="btn-secondary-custom" onclick="closeAddPlotsModal()">Cancel</button>
                <button type="submit" class="btn-gold">
                    <i class="fa-solid fa-plus"></i> Add Plots to Batch
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================================================================
     MODAL 3: QUICK EDIT PLOT
================================================================ -->
<div class="modal-backdrop-custom" id="quickEditPlotModal">
    <div class="modal-box-custom">
        <div class="modal-header-custom">
            <h3><i class="fa-regular fa-pen-to-square" style="color: #60A5FA; margin-right: 8px;"></i>Edit Plot Details</h3>
            <button type="button" class="modal-close-btn" onclick="closeQuickEditPlotModal()">&times;</button>
        </div>

        <form id="quickEditPlotForm" method="POST" onsubmit="return handlePlotSubmit(event, this)">
            @csrf
            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Plot Name <span>*</span></label>
                    <input type="text" name="property_name" id="qe_property_name" class="m-form-control" required>
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Plot Code <span>*</span></label>
                    <input type="text" name="property_code" id="qe_property_code" class="m-form-control" required>
                </div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Size (Area)</label>
                    <input type="text" name="size" id="qe_size" class="m-form-control" placeholder="e.g. 1200">
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Size Unit</label>
                    <select name="size_unit" id="qe_size_unit" class="m-form-control">
                        <option value="sq.ft">sq.ft</option>
                        <option value="sq.yard">sq.yard</option>
                        <option value="sq.meter">sq.meter</option>
                        <option value="acre">acre</option>
                        <option value="bigha">bigha</option>
                    </select>
                </div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Facing Direction</label>
                    <select name="facing" id="qe_facing" class="m-form-control">
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
                <div class="m-form-group">
                    <label class="m-form-label">Status <span>*</span></label>
                    <select name="status" id="qe_status" class="m-form-control" required>
                        <option value="available">Available</option>
                        <option value="booked">Booked</option>
                        <option value="sold">Sold</option>
                        <option value="blocked">Blocked</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Purchase Rate (INR)</label>
                    <input type="number" step="0.01" name="purchase_rate" id="qe_purchase_rate" class="m-form-control">
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Selling / Asking Price (INR)</label>
                    <input type="number" step="0.01" name="price" id="qe_price" class="m-form-control">
                </div>
            </div>

            <div class="m-form-group">
                <label class="m-form-label">Notes / Description</label>
                <textarea name="description" id="qe_description" rows="2" class="m-form-control" placeholder="Optional notes..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.10);">
                <button type="button" class="btn-secondary-custom" onclick="closeQuickEditPlotModal()">Cancel</button>
                <button type="submit" class="btn-gold">
                    <i class="fa-solid fa-check"></i> Update Plot
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddBatchModal() {
    document.getElementById('addBatchModal').classList.add('active');
}
function closeAddBatchModal() {
    document.getElementById('addBatchModal').classList.remove('active');
}

function openAddPlotsModal(batchId, batchName, purchaseRate) {
    document.getElementById('addPlotsForm').action = "/acquisition-batches/" + batchId + "/add-plots";
    document.getElementById('targetBatchName').textContent = batchName;
    document.getElementById('ap_purchase_rate').value = purchaseRate;
    document.getElementById('addPlotsModal').classList.add('active');
}
function closeAddPlotsModal() {
    document.getElementById('addPlotsModal').classList.remove('active');
}

function openQuickEditPlotModal(id, name, code, size, sizeUnit, facing, purchaseRate, price, status, desc) {
    document.getElementById('quickEditPlotForm').action = "/properties/" + id + "/quick-update";
    document.getElementById('qe_property_name').value = name;
    document.getElementById('qe_property_code').value = code;
    document.getElementById('qe_size').value = size || '';
    document.getElementById('qe_size_unit').value = sizeUnit || 'sq.ft';
    document.getElementById('qe_facing').value = facing || '';
    document.getElementById('qe_purchase_rate').value = purchaseRate || '';
    document.getElementById('qe_price').value = price || '';
    document.getElementById('qe_status').value = (status || 'available').toLowerCase();
    document.getElementById('qe_description').value = desc || '';
    document.getElementById('quickEditPlotModal').classList.add('active');
}

function closeQuickEditPlotModal() {
    document.getElementById('quickEditPlotModal').classList.remove('active');
}

function toggleGeneratorFields(checked) {
    const fields = document.getElementById('generator_fields');
    fields.style.display = checked ? 'block' : 'none';
    if (!checked) {
        document.getElementById('modal_plot_count').value = '0';
    } else {
        document.getElementById('modal_plot_count').value = '14';
    }
    calculateTotalAmount();
}

function calculateTotalAmount() {
    const rate = parseFloat(document.getElementById('modal_purchase_rate').value) || 0;
    const count = parseInt(document.getElementById('modal_plot_count').value) || 0;
    if (rate > 0 && count > 0) {
        document.getElementById('modal_total_amount').value = (rate * count).toFixed(2);
    }
}

function toggleBatchPlots(batchId) {
    const wrap = document.getElementById('batch-plots-wrap-' + batchId);
    const btnText = document.getElementById('toggle-text-' + batchId);
    if (wrap.style.display === 'none' || wrap.style.display === '') {
        wrap.style.display = 'block';
        if (btnText) btnText.textContent = 'Hide Plots';
    } else {
        wrap.style.display = 'none';
        if (btnText) btnText.textContent = 'View Plots';
    }
}

let isSubmittingBatch = false;
function handleBatchSubmit(event, form) {
    if (isSubmittingBatch) {
        event.preventDefault();
        return false;
    }
    isSubmittingBatch = true;
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
    }
    return true;
}

let isSubmittingPlot = false;
function handlePlotSubmit(event, form) {
    if (isSubmittingPlot) {
        event.preventDefault();
        return false;
    }
    isSubmittingPlot = true;
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Updating...';
    }
    return true;
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddBatchModal();
        closeAddPlotsModal();
        closeQuickEditPlotModal();
    }
});
</script>
@endsection
