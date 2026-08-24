@extends('admin.layouts.app')

@section('title', 'Property Master Details')
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
.breadcrumb-nav a:hover {
    color: #93C5FD;
}
.breadcrumb-nav .separator {
    font-size: 10px;
    color: #64748B;
}
.breadcrumb-nav .active {
    color: #FFFFFF;
    font-weight: 700;
}

.crud-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 15px;
}

.crud-title h2 {
    font-size: 28px;
    font-weight: 800;
    color: #FFFFFF !important;
    margin-bottom: 6px;
    letter-spacing: -0.3px;
}

.crud-title p {
    font-size: 14px;
    color: #FFFFFF !important;
    font-weight: 700 !important;
    margin: 0;
}

.details-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 24px;
    margin-bottom: 30px;
}

@media (max-width: 992px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
}

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important;
    padding: 26px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
}

/* Property Banner Showcase */
.property-banner-box {
    width: 100%;
    height: 200px;
    border-radius: 18px;
    overflow: hidden;
    position: relative;
    border: 1px solid rgba(255, 255, 255, 0.14);
    margin-bottom: 22px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.30);
}
.property-banner-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.property-banner-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.30) 0%, rgba(139, 92, 246, 0.25) 50%, rgba(16, 22, 34, 0.85) 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    position: relative;
}
.property-avatar-icon {
    width: 68px;
    height: 68px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    color: #60A5FA;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
}
.property-badge-floating {
    position: absolute;
    bottom: 14px;
    right: 14px;
    background: rgba(16, 22, 34, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(12px);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 800;
    color: #60A5FA;
    letter-spacing: 0.4px;
}

.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    font-size: 14px;
}

.info-item:last-child {
    border-bottom: none;
    padding-bottom: 4px;
}

.info-label {
    font-weight: 800;
    color: #94A3B8 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

.info-value {
    color: #FFFFFF !important;
    font-weight: 700;
    text-align: right;
    font-size: 14.5px;
}

.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom, .btn-gold {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-primary-custom:hover, .btn-gold:hover {
    background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50);
}

.btn-secondary-custom, a.btn-secondary-custom, button.btn-secondary-custom, .btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: rgba(255, 255, 255, 0.06) !important;
    color: #CBD5E1 !important; font-size: 13.5px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-secondary-custom:hover, .btn-outline:hover {
    background: rgba(255, 255, 255, 0.12) !important; color: #FFFFFF !important; transform: translateY(-2px);
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    font-size: 11.5px;
    font-weight: 800;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.section-title {
    font-size: 17px;
    font-weight: 800;
    color: #FFFFFF !important;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    letter-spacing: -0.2px;
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.10);
    background: rgba(16, 22, 34, 0.70);
}

.projects-table {
    width: 100%;
    border-collapse: collapse;
    background: transparent;
    font-size: 13.5px;
}

.projects-table thead {
    background: rgba(255, 255, 255, 0.05) !important;
}

.projects-table th {
    padding: 14px 16px;
    background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important;
    font-weight: 800 !important;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    text-align: left;
    white-space: nowrap;
}

.projects-table td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    font-size: 13.5px;
    font-weight: 600;
    color: #FFFFFF !important;
    vertical-align: middle;
    white-space: nowrap;
}

.projects-table tbody tr {
    transition: background 0.2s ease;
}

.projects-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.04) !important;
}

.projects-table tbody tr:last-child td {
    border-bottom: none;
}
</style>

{{-- Luxury Breadcrumb --}}
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
        <p>Property profile, location details, and associated Projects.</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        @if(Auth::user() && Auth::user()->hasPermission('property_edit'))
            <a href="{{ route('property-masters.edit', $propertyMaster->id) }}" class="btn-primary-custom">
                <i class="fa-regular fa-pen-to-square"></i> Edit Property
            </a>
        @endif
        <a href="{{ route('property-masters.index') }}" class="btn-secondary-custom">
            <i class="fa-solid fa-arrow-left"></i> Back to Properties
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.18); border: 1px solid rgba(16, 185, 129, 0.35); color: #34D399; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

<div class="details-grid">
    <!-- Left Panel: Property Profile -->
    <div class="card-box" style="height: fit-content;">
        <div class="property-banner-box">
            @if($propertyMaster->main_image)
                <img src="{{ asset('storage/' . $propertyMaster->main_image) }}" alt="{{ $propertyMaster->property_name }}" class="property-banner-img">
            @else
                <div class="property-banner-placeholder">
                    <div class="property-avatar-icon">
                        <i class="fa-solid fa-city"></i>
                    </div>
                    <span style="font-size: 15px; font-weight: 800; color: #FFFFFF; letter-spacing: -0.2px;">{{ $propertyMaster->property_name }}</span>
                    <div class="property-badge-floating">
                        <i class="fa-solid fa-tag" style="margin-right: 4px;"></i> {{ $propertyMaster->property_code }}
                    </div>
                </div>
            @endif
        </div>

        <ul class="info-list">
            <li class="info-item">
                <span class="info-label">Property Code</span>
                <span class="info-value"><code style="background: rgba(59, 130, 246, 0.15); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.30); padding: 4px 10px; border-radius: 8px; font-size: 13px; font-weight: 800;">{{ $propertyMaster->property_code }}</code></span>
            </li>
            <li class="info-item">
                <span class="info-label">Firm</span>
                <span class="info-value" style="color: #60A5FA !important;">{{ $propertyMaster->firm->firm_name ?? '-' }}</span>
            </li>
            <li class="info-item">
                <span class="info-label">Status</span>
                <span class="info-value">
                    <span class="badge {{ $propertyMaster->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        <i class="fa-solid {{ $propertyMaster->status === 'active' ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                        {{ ucfirst($propertyMaster->status) }}
                    </span>
                </span>
            </li>
            <li class="info-item">
                <span class="info-label">City</span>
                <span class="info-value">{{ $propertyMaster->city ?? '-' }}</span>
            </li>
        </ul>
    </div>

    <!-- Right Panel: Address & Managed Projects -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <div class="card-box">
            <h3 style="font-size: 16px; font-weight: 800; color: #FFFFFF; margin-bottom: 14px; letter-spacing: -0.2px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-location-dot" style="color: #F87171;"></i>
                Location &amp; Address
            </h3>
            <div style="font-size: 14.5px; color: #FFFFFF; line-height: 1.6; font-weight: 600;">
                @if($propertyMaster->address) {{ $propertyMaster->address }}, @endif
                @if($propertyMaster->location) {{ $propertyMaster->location }}, @endif
                @if($propertyMaster->city) {{ $propertyMaster->city }}, @endif
                @if($propertyMaster->state) {{ $propertyMaster->state }} @endif
                @if($propertyMaster->pincode) - {{ $propertyMaster->pincode }} @endif
                @if($propertyMaster->country) ({{ $propertyMaster->country }}) @endif
                @if(!$propertyMaster->address && !$propertyMaster->location && !$propertyMaster->city)
                    <span style="color: #94A3B8; font-weight: 500;">No location details provided.</span>
                @endif
            </div>

            @if($propertyMaster->description)
                <div style="margin-top: 16px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.08); font-size: 13.5px; color: #CBD5E1; line-height: 1.6;">
                    <strong style="color: #FFFFFF; font-weight: 700;">Description:</strong> {{ $propertyMaster->description }}
                </div>
            @endif
        </div>

        <div class="card-box">
            <div class="section-title">
                <span style="display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-diagram-project" style="color: #38BDF8;"></i>
                    Managed Projects ({{ $propertyMaster->projects->count() }})
                </span>
                @if(Auth::user() && Auth::user()->hasPermission('project_add'))
                    <a href="{{ route('projects.create', ['property_id' => $propertyMaster->id]) }}" class="btn-primary-custom" style="padding: 7px 16px; min-height: 36px; font-size: 13px;">
                        <i class="fa-solid fa-plus"></i> Add Project
                    </a>
                @endif
            </div>

            <div class="table-wrapper">
                <table class="projects-table">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Bulk Records</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($propertyMaster->projects as $project)
                            <tr>
                                <td>
                                    <a href="{{ route('projects.show', $project->id) }}" style="color: #FFFFFF; font-weight: 700; text-decoration: none;">
                                        {{ $project->project_name }}
                                    </a>
                                </td>
                                <td><code style="background: rgba(255, 255, 255, 0.08); color: #60A5FA; padding: 2px 6px; border-radius: 4px; font-size: 12.5px;">{{ $project->project_code }}</code></td>
                                <td><span style="color: #CBD5E1; text-transform: capitalize;">{{ $project->project_type }}</span></td>
                                <td>
                                    <a href="{{ route('projects.show', $project->id) }}" style="color: #FFFFFF; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-boxes-stacked" style="color: #60A5FA;"></i> {{ $project->bulks->count() }} Records
                                    </a>
                                </td>
                                <td>
                                    <span class="badge {{ $project->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                        <i class="fa-solid {{ $project->status === 'active' ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('projects.show', $project->id) }}" class="btn-secondary-custom" style="padding: 6px 12px; min-height: 32px; font-size: 12.5px;">
                                        Open Project <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #94A3B8; padding: 32px; font-weight: 600;">
                                    No Projects added under this Property yet. Click <strong style="color: #60A5FA;">+ Add Project</strong> above to add one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
