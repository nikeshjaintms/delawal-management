@extends('admin.layouts.app')

@section('title', 'Project Details')
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
.crud-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 4px;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 15px;
}

.crud-title h2 {
    font-size: 26px;
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
    border-radius: 20px !important;
    padding: 26px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
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
    padding: 13px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    font-size: 14px;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 700;
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

.project-large-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    margin-bottom: 20px;
}

.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom, .btn-gold {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-primary-custom:hover, .btn-gold:hover {
    background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50);
}

.btn-secondary-custom, a.btn-secondary-custom, button.btn-secondary-custom, .btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: rgba(255, 255, 255, 0.06) !important;
    color: #CBD5E1 !important; font-size: 14px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-secondary-custom:hover, .btn-outline:hover {
    background: rgba(255, 255, 255, 0.12) !important; color: #FFFFFF !important; transform: translateY(-2px);
}

.btn-excel-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 7px;
    padding: 9px 16px; min-height: 38px; background: rgba(16, 185, 129, 0.18) !important;
    color: #34D399 !important; font-size: 13px; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.35) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-excel-custom:hover {
    background: #10B981 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(16, 185, 129, 0.40);
}

.btn-view {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 6px 12px; min-height: 32px; background: rgba(59, 130, 246, 0.15) !important;
    color: #60A5FA !important; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(59, 130, 246, 0.30) !important;
    border-radius: 8px; text-decoration: none !important; transition: all .2s ease; cursor: pointer;
}
.btn-view:hover {
    background: #2563EB !important; color: #FFFFFF !important; transform: translateY(-2px);
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 12px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 20px;
    text-transform: uppercase;
}

.badge-active, .badge-available { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive, .badge-sold { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-booked { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }

.section-title {
    font-size: 17px;
    font-weight: 800;
    color: #FFFFFF !important;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    letter-spacing: -0.2px;
    flex-wrap: wrap;
    gap: 10px;
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.10);
}

.properties-table {
    width: 100%;
    border-collapse: collapse;
    background: transparent;
}

.properties-table thead {
    background: rgba(255, 255, 255, 0.05) !important;
}

.properties-table th {
    padding: 13px 16px;
    background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important;
    font-weight: 800 !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10) !important;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    text-align: left;
    white-space: nowrap;
}

.properties-table td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    font-size: 14px;
    font-weight: 600;
    color: #FFFFFF !important;
}

.properties-table tbody tr {
    transition: background 0.2s ease;
}

.properties-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.04) !important;
}

.properties-table tbody tr:last-child td {
    border-bottom: none;
}

code.code-chip, code {
    background: rgba(59, 130, 246, 0.15) !important;
    color: #60A5FA !important;
    border: 1px solid rgba(59, 130, 246, 0.30) !important;
    padding: 3px 8px !important;
    border-radius: 6px !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    font-family: monospace !important;
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
        <p>Project details and Bulk management.</p>
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

<div class="details-grid">
    <!-- Left Panel: Profile & Details -->
    <div class="card-box" style="height: fit-content;">
        @if($project->project_image)
            <img src="{{ asset('storage/' . $project->project_image) }}" alt="{{ $project->project_name }}" class="project-large-img">
        @else
            <div style="width: 100%; height: 180px; background: rgba(59, 130, 246, 0.12); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.30); margin-bottom: 20px;">
                <i class="fa-solid fa-city" style="font-size: 48px;"></i>
            </div>
        @endif

        <ul class="info-list">
            <li class="info-item">
                <span class="info-label">Property Master</span>
                <span class="info-value">
                    @if($project->propertyMaster)
                        <a href="{{ route('property-masters.show', $project->propertyMaster->id) }}" style="color: #60A5FA; font-weight: 700; text-decoration: none;">
                            {{ $project->propertyMaster->property_name }}
                        </a>
                    @else
                        -
                    @endif
                </span>
            </li>
            <li class="info-item">
                <span class="info-label">Project Code</span>
                <span class="info-value"><code class="code-chip">{{ $project->project_code }}</code></span>
            </li>
            <li class="info-item">
                <span class="info-label">Firm</span>
                <span class="info-value">{{ $project->firm->firm_name ?? '-' }}</span>
            </li>
            <li class="info-item">
                <span class="info-label">Project Type</span>
                <span class="info-value" style="text-transform: capitalize;">{{ $project->project_type }}</span>
            </li>
            <li class="info-item">
                <span class="info-label">Status</span>
                <span class="info-value">
                    <span class="badge badge-{{ $project->status === 'active' ? 'active' : 'inactive' }}">
                        <i class="fa-solid fa-circle-dot"></i> {{ ucfirst($project->status) }}
                    </span>
                </span>
            </li>
        </ul>
    </div>

    <!-- Right Panel: Address & Bulk Management -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <div class="card-box">
            <h3 style="font-size: 16px; font-weight: 800; color: #FFFFFF; margin-bottom: 12px; letter-spacing: -0.2px;">Location &amp; Description</h3>
            
            <div style="margin-bottom: 16px; font-size: 14.5px; color: #FFFFFF; line-height: 1.6; font-weight: 500;">
                <strong style="color: #60A5FA;">Location/Address:</strong><br>
                @if($project->address) {{ $project->address }}, @endif
                @if($project->city) {{ $project->city }}, @endif
                @if($project->state) {{ $project->state }} @endif
                @if($project->pincode) - {{ $project->pincode }} @endif
                @if($project->country) ({{ $project->country }}) @endif
                @if(!$project->address && !$project->city)
                    <span style="color: #94A3B8;">No address specified.</span>
                @endif
            </div>

            @if($project->description)
                <div style="font-size: 13.5px; color: #CBD5E1; line-height: 1.6; border-top: 1px solid rgba(255, 255, 255, 0.08); padding-top: 14px;">
                    <strong style="color: #FFFFFF;">Description:</strong><br>
                    {{ $project->description }}
                </div>
            @endif
        </div>

        <div class="card-box">
            <div class="section-title">
                <span>Bulk Management ({{ $project->properties->count() }} Records)</span>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="{{ route('properties.index', ['project_id' => $project->id]) }}" class="btn-excel-custom">
                        <i class="fa-solid fa-file-excel"></i> Manage Bulk Excel
                    </a>
                    @if($authUser && $authUser->hasPermission('property_add'))
                        <a href="{{ route('properties.create', ['project_id' => $project->id]) }}" class="btn-primary-custom" style="padding: 8px 16px; min-height: 38px; font-size: 13px;">
                            <i class="fa-solid fa-plus"></i> Add Bulk Record
                        </a>
                    @endif
                </div>
            </div>

            <div class="table-wrapper">
                <table class="properties-table">
                    <thead>
                        <tr>
                            <th>Bulk Record / Unit</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th>Price</th>
                            <th>Size</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->properties as $property)
                            <tr>
                                <td>
                                    <a href="{{ route('properties.show', $property->id) }}" style="color: #FFFFFF; font-weight: 700; text-decoration: none;">
                                        {{ $property->property_name }}
                                    </a>
                                </td>
                                <td><code class="code-chip">{{ $property->property_code }}</code></td>
                                <td>
                                    <span class="badge badge-{{ strtolower($property->status ?? 'available') }}">
                                        <i class="fa-solid fa-circle-dot"></i> {{ ucfirst($property->status ?? 'Available') }}
                                    </span>
                                </td>
                                <td>{{ $property->price ? '₹' . number_format($property->price, 2) : '-' }}</td>
                                <td>{{ $property->size }} {{ $property->size_unit }}</td>
                                <td style="text-align: right;">
                                    <a href="{{ route('properties.show', $property->id) }}" class="btn-view">
                                        <i class="fa-regular fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #94A3B8; padding: 30px 0;">
                                    No bulk records registered under this project yet. Use <strong>Manage Bulk Excel</strong> to upload excel.
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
