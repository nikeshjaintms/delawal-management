@extends('admin.layouts.app')

@section('title', $project->project_name . ' - Project Details')
@section('page-title', 'Project Master')
@php
    $user = Auth::user();
    if (!$user && session('login_type') === 'firm' && session('firm_id')) {
        $authUser = new class {
            public function isAdmin()        { return true; }
            public function hasPermission($p){ return true; }
            public $role = null;
            public $name = '';
            public $firm_id = null;
        };
        $authUser->name = session('firm_name', 'Firm');
        $authUser->firm_id = session('firm_id');
    } else {
        $authUser = $user;
    }
@endphp

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
.project-hero-grid {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 24px;
    align-items: center;
}
@media (max-width: 992px) {
    .project-hero-grid {
        grid-template-columns: 1fr;
        gap: 18px;
    }
}

.project-hero-avatar {
    width: 120px;
    height: 120px;
    border-radius: 16px;
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
}
.project-hero-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.project-meta-grid {
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

/* ── Quick KPI Strip ── */
.project-kpi-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-top: 20px;
    padding-top: 18px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}
@media (max-width: 768px) {
    .project-kpi-strip {
        grid-template-columns: repeat(2, 1fr);
    }
}
.pk-card {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.pk-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.pk-blue   { background: rgba(59, 130, 246, 0.20); color: #60A5FA; }
.pk-purple { background: rgba(167, 139, 250, 0.20); color: #C4B5FD; }
.pk-green  { background: rgba(16, 185, 129, 0.20); color: #34D399; }
.pk-amber  { background: rgba(245, 158, 11, 0.20); color: #FBBF24; }

.pk-info { display: flex; flex-direction: column; }
.pk-label { font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; }
.pk-val { font-size: 16px; font-weight: 800; color: #FFFFFF; line-height: 1.2; margin-top: 2px; }

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
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.18) !important;
    border-radius: 10px; text-decoration: none !important; cursor: pointer;
}
.btn-secondary-custom:hover, .btn-outline:hover {
    background: rgba(255, 255, 255, 0.16) !important; color: #FFFFFF !important; border-color: rgba(255, 255, 255, 0.30) !important;
}

.btn-excel-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 7px;
    padding: 10px 18px; min-height: 40px; background: rgba(16, 185, 129, 0.18) !important;
    color: #34D399 !important; font-size: 13.5px; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.35) !important;
    border-radius: 10px; text-decoration: none !important; cursor: pointer;
}
.btn-excel-custom:hover {
    background: #10B981 !important; color: #FFFFFF !important;
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
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 20px;
    text-transform: uppercase;
}
.badge-active, .badge-available { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive, .badge-sold { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-booked { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }

.section-title {
    font-size: 18px;
    font-weight: 800;
    color: #FFFFFF !important;
    margin-bottom: 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

/* ── Full Width Table Styling ── */
.table-wrapper {
    width: 100%;
    overflow-x: auto;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.10);
    background: rgba(10, 14, 23, 0.65);
}

.properties-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}

.properties-table th {
    padding: 13px 18px;
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

.properties-table td {
    padding: 13px 18px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    color: #E2E8F0 !important;
    font-weight: 600;
    vertical-align: middle;
    white-space: nowrap;
}

.properties-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.04);
}

.properties-table tbody tr:last-child td {
    border-bottom: none;
}

.code-chip {
    background: rgba(59, 130, 246, 0.15) !important;
    color: #60A5FA !important;
    border: 1px solid rgba(59, 130, 246, 0.30) !important;
    padding: 3px 8px !important;
    border-radius: 6px !important;
    font-weight: 700 !important;
    font-size: 12.5px !important;
    font-family: monospace !important;
}

/* ── Modal Styling ── */
.modal-backdrop-custom {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background: rgba(0, 0, 0, 0.75) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    z-index: 9999 !important;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    box-sizing: border-box;
}
.modal-backdrop-custom.active {
    display: flex !important;
}
.modal-box-custom {
    background: #101622 !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    border-radius: 20px !important;
    width: 100% !important;
    max-width: 680px !important;
    max-height: 90vh !important;
    overflow-y: auto !important;
    padding: 28px !important;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.60) !important;
    position: relative !important;
    box-sizing: border-box;
}
.modal-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
}
.modal-header-custom h3 {
    font-size: 20px;
    font-weight: 800;
    color: #FFFFFF !important;
    margin: 0;
}
.modal-close-btn {
    background: transparent;
    border: none;
    font-size: 24px;
    color: #94A3B8;
    cursor: pointer;
    line-height: 1;
    transition: color .2s;
}
.modal-close-btn:hover { color: #FFFFFF; }

.m-form-group { margin-bottom: 16px; }
.m-form-label { display: block; font-size: 13px; font-weight: 700; color: #E2E8F0; margin-bottom: 6px; }
.m-form-label span { color: #EF4444; }
.m-form-control {
    width: 100%;
    padding: 10px 14px;
    background: rgba(255, 255, 255, 0.06) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 10px !important;
    color: #FFFFFF !important;
    font-size: 13.5px;
    outline: none;
    box-sizing: border-box;
    transition: border-color .2s;
}
.m-form-control:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}
select.m-form-control option { background: #101622; color: #FFFFFF; }
.m-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
@media (max-width: 580px) {
    .m-form-row { grid-template-columns: 1fr; gap: 0; }
}
</style>

{{-- Breadcrumb --}}
<div class="breadcrumb-nav">
    <span><i class="fa-solid fa-city" style="color: #60A5FA; margin-right: 6px;"></i>Property Management</span>
    <i class="fa-solid fa-chevron-right separator"></i>
    @if($project->propertyMaster)
        <a href="{{ route('property-masters.show', $project->propertyMaster->id) }}">{{ $project->propertyMaster->property_name }}</a>
        <i class="fa-solid fa-chevron-right separator"></i>
    @endif
    <a href="{{ route('projects.index', $project->property_id ? ['property_id' => $project->property_id] : []) }}">Projects</a>
    <i class="fa-solid fa-chevron-right separator"></i>
    <span class="active">{{ $project->project_name }}</span>
</div>

<div class="crud-header">
    <div class="crud-title">
        <h2>{{ $project->project_name }}</h2>
        <p>Project details, Acquisition Batch breakdown &amp; Plot Inventory</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        @if($authUser && $authUser->hasPermission('project_edit'))
            <a href="{{ route('projects.edit', $project->id) }}" class="btn-primary-custom">
                <i class="fa-regular fa-pen-to-square"></i> Edit Project
            </a>
        @endif
        @if($project->propertyMaster)
            <a href="{{ route('property-masters.show', $project->propertyMaster->id) }}" class="btn-secondary-custom">
                <i class="fa-solid fa-arrow-left"></i> Back to {{ $project->propertyMaster->property_name }}
            </a>
        @else
            <a href="{{ route('projects.index') }}" class="btn-secondary-custom">
                <i class="fa-solid fa-arrow-left"></i> Back to Projects
            </a>
        @endif
    </div>
</div>

@php
    $totalPlots = $project->properties->count();
    $availPlots = $project->properties->where('status', 'available')->count();
    $bookedPlots = $project->properties->whereIn('status', ['booked', 'sold'])->count();
    $batchGroups = $project->properties->groupBy('acquisition_batch_id');
@endphp

<!-- ================================================================
     TOP SECTION: FULL WIDTH PROJECT OVERVIEW & METADATA CARD
================================================================ -->
<div class="card-box">
    <div class="project-hero-grid">
        <!-- Hero Avatar / Image -->
        <div class="project-hero-avatar">
            @if($project->project_image)
                <img src="{{ asset('storage/' . $project->project_image) }}" alt="{{ $project->project_name }}">
            @else
                <i class="fa-solid fa-city"></i>
            @endif
        </div>

        <!-- Meta Grid -->
        <div class="project-meta-grid">
            <div class="pm-item">
                <span class="pm-label">Property Master</span>
                <span class="pm-value">
                    @if($project->propertyMaster)
                        <a href="{{ route('property-masters.show', $project->propertyMaster->id) }}" style="color: #60A5FA; text-decoration: none; font-weight: 800;">
                            {{ $project->propertyMaster->property_name }}
                        </a>
                    @else
                        -
                    @endif
                </span>
            </div>
            <div class="pm-item">
                <span class="pm-label">Project Code</span>
                <span class="pm-value"><code class="code-chip">{{ $project->project_code }}</code></span>
            </div>
            <div class="pm-item">
                <span class="pm-label">Firm</span>
                <span class="pm-value" style="color: #93C5FD;">{{ $project->firm->firm_name ?? '-' }}</span>
            </div>
            <div class="pm-item">
                <span class="pm-label">Project Type</span>
                <span class="pm-value" style="text-transform: capitalize;">{{ $project->project_type ?: 'Plotted Development' }}</span>
            </div>
            <div class="pm-item">
                <span class="pm-label">Status</span>
                <span class="pm-value">
                    <span class="badge badge-{{ $project->status === 'active' ? 'active' : 'inactive' }}">
                        <i class="fa-solid fa-circle-dot"></i> {{ ucfirst($project->status) }}
                    </span>
                </span>
            </div>
            <div class="pm-item" style="grid-column: span 2;">
                <span class="pm-label">Location / Address (Property Master)</span>
                <span class="pm-value" style="font-size: 13.5px; font-weight: 600; color: #CBD5E1;">
                    <i class="fa-solid fa-location-dot" style="color: #60A5FA; margin-right: 5px;"></i>
                    {{ $project->display_address }}
                </span>
            </div>
        </div>

        <!-- Right Quick Actions -->
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <a href="{{ route('projects.edit', $project->id) }}" class="btn-primary-custom" style="padding: 8px 16px; min-height: 38px; font-size: 13px;">
                <i class="fa-solid fa-layer-group"></i> Manage Plots / Batches
            </a>
            <a href="{{ route('properties.index', ['project_id' => $project->id]) }}" class="btn-excel-custom" style="padding: 8px 16px; min-height: 38px; font-size: 13px;">
                <i class="fa-solid fa-file-excel"></i> Manage Bulk Excel
            </a>
        </div>
    </div>

    <!-- KPI Summary Strip -->
    <div class="project-kpi-strip">
        <div class="pk-card">
            <div class="pk-icon pk-blue"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div class="pk-info">
                <span class="pk-label">Total Plots</span>
                <span class="pk-val">{{ $totalPlots }} Plots</span>
            </div>
        </div>
        <div class="pk-card">
            <div class="pk-icon pk-purple"><i class="fa-solid fa-layer-group"></i></div>
            <div class="pk-info">
                <span class="pk-label">Batches Used</span>
                <span class="pk-val">{{ $batchGroups->count() }} Batches</span>
            </div>
        </div>
        <div class="pk-card">
            <div class="pk-icon pk-green"><i class="fa-solid fa-circle-check"></i></div>
            <div class="pk-info">
                <span class="pk-label">Available Plots</span>
                <span class="pk-val">{{ $availPlots }} Plots</span>
            </div>
        </div>
        <div class="pk-card">
            <div class="pk-icon pk-amber"><i class="fa-solid fa-handshake"></i></div>
            <div class="pk-info">
                <span class="pk-label">Booked / Sold</span>
                <span class="pk-val">{{ $bookedPlots }} Plots</span>
            </div>
        </div>
    </div>

    @if($project->description)
        <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.08); font-size: 13.5px; color: #CBD5E1;">
            <strong style="color: #94A3B8; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 3px;">Project Description:</strong>
            {{ $project->description }}
        </div>
    @endif
</div>

<!-- ================================================================
     BOTTOM SECTION: FULL WIDTH PROJECT PLOTS & UNITS INVENTORY TABLE
================================================================ -->
<div class="card-box">
    <div class="section-title">
        <div>
            <span><i class="fa-solid fa-list-check" style="color: #60A5FA; margin-right: 8px;"></i>Project Plots &amp; Units Inventory ({{ $totalPlots }} Plots)</span>
            @if($batchGroups->count() > 0)
                <div style="font-size: 12.5px; color: #94A3B8; margin-top: 6px; font-weight: 500; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                    <span>Source Batches:</span>
                    @foreach($batchGroups as $bId => $pGroup)
                        @php $bName = $pGroup->first()->acquisitionBatch?->batch_name ?? 'Direct Plots'; @endphp
                        <span style="background: rgba(167, 139, 250, 0.18); color: #C4B5FD; border: 1px solid rgba(167, 139, 250, 0.35); padding: 2px 8px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                            <i class="fa-solid fa-layer-group" style="font-size: 10px; margin-right: 4px;"></i> {{ $pGroup->count() }} from {{ $bName }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
        <div style="font-size: 12.5px; color: #94A3B8;">
            <span style="color: #34D399; font-weight: 700;">● Continuous Line-wise Sequence</span>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="properties-table">
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Plot Name</th>
                    <th>Code</th>
                    <th>Acquisition Batch</th>
                    <th>Original Purchase Rate</th>
                    <th>Size (Area)</th>
                    <th>Facing</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($project->properties as $index => $property)
                    <tr>
                        <td style="color: #94A3B8; font-weight: 700; font-size: 12px;">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <a href="{{ route('properties.show', $property->id) }}" style="color: #FFFFFF; font-weight: 800; text-decoration: none; font-size: 14px;">
                                {{ $property->property_name }}
                            </a>
                        </td>
                        <td><code class="code-chip">{{ $property->property_code }}</code></td>
                        <td>
                            @if($property->acquisitionBatch)
                                <span style="background: rgba(167, 139, 250, 0.15); color: #C4B5FD; border: 1px solid rgba(167, 139, 250, 0.30); padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                                    <i class="fa-solid fa-layer-group" style="margin-right: 3px;"></i> {{ $property->acquisitionBatch->batch_name }}
                                </span>
                            @else
                                <span style="color: #94A3B8; font-size: 12px;">Direct Plot</span>
                            @endif
                        </td>
                        <td>
                            <strong style="color: #FBBF24; font-size: 14px;">₹{{ number_format($property->purchase_rate ?: ($property->acquisitionBatch?->purchase_rate ?? 0), 2) }}</strong>
                            @if($property->purchase_date)
                                <small style="color: #94A3B8; display: block; font-size: 11px;">{{ \Carbon\Carbon::parse($property->purchase_date)->format('d M Y') }}</small>
                            @endif
                        </td>
                        <td>{{ $property->size ? $property->size . ' ' . ($property->size_unit ?? 'sq.ft') : '-' }}</td>
                        <td>{{ $property->facing ?: '-' }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($property->status ?? 'available') }}">
                                <i class="fa-solid fa-circle-dot"></i> {{ ucfirst($property->status ?? 'Available') }}
                            </span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <div class="tbl-actions-wrap">
                                <button type="button" class="btn-tbl-edit" onclick="openQuickEditPlotModal({{ $property->id }}, '{{ addslashes($property->property_name) }}', '{{ addslashes($property->property_code) }}', '{{ $property->size }}', '{{ $property->size_unit }}', '{{ $property->facing }}', '{{ $property->purchase_rate }}', '{{ $property->price }}', '{{ $property->status }}', '{{ addslashes($property->description ?? '') }}')">
                                    <i class="fa-regular fa-pen-to-square"></i> Edit
                                </button>
                                <a href="{{ route('properties.show', $property->id) }}" class="btn-tbl-view">
                                    <i class="fa-regular fa-eye"></i> View
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; color: #94A3B8; padding: 36px 0;">
                            <i class="fa-solid fa-boxes-stacked" style="font-size: 32px; color: #60A5FA; margin-bottom: 8px; display: block;"></i>
                            No plots assigned to this project yet. Click <strong>Manage Plots / Batches</strong> to allocate plots from acquisition batches.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ================================================================
     PROJECT CONTRACTORS SECTION
================================================================ -->
<div class="card-box" style="margin-top: 24px;">
    <div class="section-title" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10); padding-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-helmet-safety" style="font-size: 18px; color: #FBBF24;"></i>
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: #FFFFFF; margin: 0;">Assigned Project Contractors</h3>
                <p style="font-size: 12.5px; color: #94A3B8; margin: 2px 0 0 0;">Specialist agencies and contractors engaged on this project ({{ $project->contractors->count() }} active/registered).</p>
            </div>
        </div>
        <a href="{{ route('contractors.create', ['project_id' => $project->id]) }}" class="btn-primary-custom" style="padding: 7px 16px; min-height: 36px; font-size: 13px;">
            <i class="fa-solid fa-plus"></i> Add Contractor
        </a>
    </div>

    @if($project->contractors->count() > 0)
        <div class="table-responsive-wrapper">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Contractor Name</th>
                        <th>Mobile</th>
                        <th>Aadhar Card</th>
                        <th>PAN Card</th>
                        <th>Bank Details</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project->contractors as $con)
                        <tr>
                            <td>
                                <strong style="color: #FFFFFF; font-weight: 700;">{{ $con->contractor_name }}</strong>
                                @if($con->address)
                                    <div style="font-size: 11.5px; color: #94A3B8; margin-top: 2px;">{{ $con->address }}</div>
                                @endif
                            </td>
                            <td>{{ $con->mobile ?: '—' }}</td>
                            <td>
                                @if($con->aadhar_no)
                                    <span style="font-family: monospace; font-size: 12px; color: #FBBF24;">{{ $con->aadhar_no }}</span>
                                @else
                                    <span style="color: #94A3B8;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($con->pan_no)
                                    <span style="font-family: monospace; font-size: 12px; color: #34D399;">{{ $con->pan_no }}</span>
                                @else
                                    <span style="color: #94A3B8;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($con->bank_name || $con->account_number)
                                    <div style="color: #FFFFFF; font-weight: 600;">{{ $con->bank_name ?: 'Bank' }}</div>
                                    @if($con->account_number)
                                        <div style="font-size: 11.5px; color: #94A3B8; font-family: monospace;">A/C: {{ $con->account_number }}</div>
                                    @endif
                                @else
                                    <span style="color: #94A3B8;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $con->status }}" style="padding: 4px 10px; font-size: 11px; font-weight: 800; border-radius: 20px; text-transform: uppercase;">
                                    <i class="fa-solid fa-circle" style="font-size: 6px;"></i> {{ ucfirst($con->status) }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="{{ route('contractors.show', $con) }}" class="btn-tbl-view" style="font-size: 12px; padding: 4px 10px;">
                                        <i class="fa-regular fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('contractors.edit', $con) }}" class="btn-tbl-edit" style="font-size: 12px; padding: 4px 10px;">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; color: #94A3B8; padding: 28px 0; background: rgba(255, 255, 255, 0.02); border-radius: 12px; border: 1px dashed rgba(255, 255, 255, 0.12);">
            <i class="fa-solid fa-helmet-safety" style="font-size: 28px; color: #FBBF24; margin-bottom: 8px; display: block;"></i>
            No contractors assigned to this project yet.
            <div style="margin-top: 10px;">
                <a href="{{ route('contractors.create', ['project_id' => $project->id]) }}" class="btn-primary-custom" style="padding: 6px 14px; font-size: 12.5px; display: inline-flex;">
                    <i class="fa-solid fa-plus"></i> Assign Contractor
                </a>
            </div>
        </div>
    @endif
</div>

<!-- ================================================================
     MODAL: QUICK EDIT PLOT
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

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeQuickEditPlotModal();
    }
});
</script>
@endsection
