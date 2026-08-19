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
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .crud-title h2 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .crud-title p {
        font-size: 13.5px;
        color: var(--text-secondary);
    }

    .details-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    @media (max-width: 768px) {
        .details-grid {
            grid-template-columns: 1fr;
        }
    }

    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        box-shadow: var(--soft-shadow);
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #F1F5F9;
        font-size: 14px;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: var(--text-secondary);
    }

    .info-value {
        color: var(--text-primary);
        font-weight: 500;
        text-align: right;
    }

    .project-large-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        margin-bottom: 20px;
    }

    .btn-gold {
        background-color: var(--gold);
        color: #FFFFFF;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-gold:hover {
        background-color: #B58D1B;
    }

    .btn-outline {
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-secondary);
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-outline:hover {
        background: #F9FAFB;
        color: var(--text-primary);
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .badge-active {
        background: rgba(34, 197, 94, 0.1);
        color: #16803D;
    }

    .badge-inactive {
        background: rgba(239, 68, 68, 0.1);
        color: #B91C1C;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .properties-table {
        width: 100%;
        border-collapse: collapse;
    }

    .properties-table th {
        padding: 12px 16px;
        background: #F9FAFB;
        color: var(--text-secondary);
        font-weight: 600;
        border-bottom: 1px solid var(--border-color);
        font-size: 11px;
        text-transform: uppercase;
    }

    .properties-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #F1F5F9;
        font-size: 13.5px;
    }

    .properties-table tr:last-child td {
        border-bottom: none;
    }
</style>

{{-- Breadcrumb --}}
<div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 15px;">
    Property Management &nbsp;&gt;&nbsp; 
    @if($project->propertyMaster)
        <a href="{{ route('property-masters.show', $project->propertyMaster->id) }}" style="color: var(--gold); text-decoration: none; font-weight: 600;">{{ $project->propertyMaster->property_name }}</a> &nbsp;&gt;&nbsp; 
    @endif
    <a href="{{ route('projects.index', $project->property_id ? ['property_id' => $project->property_id] : []) }}" style="color: var(--gold); text-decoration: none; font-weight: 600;">Projects</a> &nbsp;&gt;&nbsp; 
    <span style="color: var(--text-primary); font-weight: 600;">{{ $project->project_name }}</span>
</div>

<div class="crud-header">
    <div class="crud-title">
        <h2>{{ $project->project_name }}</h2>
        <p>Project details and Bulk management.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        @if($authUser && $authUser->hasPermission('project_edit'))
            <a href="{{ route('projects.edit', $project->id) }}" class="btn-gold">
                <i class="fa-regular fa-pen-to-square"></i> Edit Project
            </a>
        @endif
        @if($project->propertyMaster)
            <a href="{{ route('property-masters.show', $project->propertyMaster->id) }}" class="btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to {{ $project->propertyMaster->property_name }}
            </a>
        @else
            <a href="{{ route('projects.index') }}" class="btn-outline">
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
            <div style="width: 100%; height: 180px; background: rgba(59, 130, 246, 0.14); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #60A5FA; border: 1px solid rgba(96, 165, 250, 0.35); margin-bottom: 20px;">
                <i class="fa-solid fa-city" style="font-size: 48px;"></i>
            </div>
        @endif

        <ul class="info-list">
            <li class="info-item">
                <span class="info-label">Property Master</span>
                <span class="info-value">
                    @if($project->propertyMaster)
                        <a href="{{ route('property-masters.show', $project->propertyMaster->id) }}" style="color: var(--gold); font-weight: 600; text-decoration: none;">
                            {{ $project->propertyMaster->property_name }}
                        </a>
                    @else
                        -
                    @endif
                </span>
            </li>
            <li class="info-item">
                <span class="info-label">Project Code</span>
                <span class="info-value"><code style="background: #F1F5F9; padding: 2px 6px; border-radius: 4px; font-weight: 600;">{{ $project->project_code }}</code></span>
            </li>
            <li class="info-item">
                <span class="info-label">Firm</span>
                <span class="info-value">{{ $project->firm->firm_name ?? '-' }}</span>
            </li>
            <li class="info-item">
                <span class="info-label">Project Type</span>
                <span class="info-value">{{ $project->project_type }}</span>
            </li>
            <li class="info-item">
                <span class="info-label">Status</span>
                <span class="info-value">
                    <span class="badge {{ $project->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ $project->status }}
                    </span>
                </span>
            </li>
        </ul>
    </div>

    <!-- Right Panel: Address & Bulk Management -->
    <div style="display: flex; flex-direction: column; gap: 30px;">
        <div class="card-box">
            <h3 class="section-title" style="margin-bottom: 12px;">Location & Description</h3>
            
            <div style="margin-bottom: 20px; font-size: 14px; color: var(--text-primary); line-height: 1.6;">
                <strong>Location/Address:</strong><br>
                @if($project->address) {{ $project->address }}, @endif
                @if($project->city) {{ $project->city }}, @endif
                @if($project->state) {{ $project->state }} @endif
                @if($project->pincode) - {{ $project->pincode }} @endif
                @if($project->country) ({{ $project->country }}) @endif
                @if(!$project->address && !$project->city)
                    <span style="color: var(--text-secondary);">No address specified.</span>
                @endif
            </div>

            @if($project->description)
                <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; border-top: 1px solid #F1F5F9; padding-top: 15px;">
                    <strong>Description:</strong><br>
                    {{ $project->description }}
                </div>
            @endif
        </div>

        <div class="card-box">
            <div class="section-title">
                <span>Bulk Management ({{ $project->properties->count() }} Records)</span>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('properties.index', ['project_id' => $project->id]) }}" class="btn-excel-custom" style="padding: 7px 14px; font-size: 12.5px;">
                        <i class="fa-solid fa-file-excel"></i> Manage Bulk Excel
                    </a>
                    @if($authUser && $authUser->hasPermission('property_add'))
                        <a href="{{ route('properties.create', ['project_id' => $project->id]) }}" class="btn-gold" style="padding: 7px 14px; font-size: 12.5px;">
                            <i class="fa-solid fa-plus"></i> Add Bulk Record
                        </a>
                    @endif
                </div>
            </div>

            <div style="width: 100%; overflow-x: auto;">
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
                                    <a href="{{ route('properties.show', $property->id) }}" style="color: var(--gold); font-weight: 600; text-decoration: none;">
                                        {{ $property->property_name }}
                                    </a>
                                </td>
                                <td><code>{{ $property->property_code }}</code></td>
                                <td>
                                    <span class="badge badge-{{ strtolower($property->status ?? 'available') }}">
                                        {{ ucfirst($property->status ?? 'Available') }}
                                    </span>
                                </td>
                                <td>{{ $property->price ? '₹' . number_format($property->price, 2) : '-' }}</td>
                                <td>{{ $property->size }} {{ $property->size_unit }}</td>
                                <td style="text-align: right;">
                                    <a href="{{ route('properties.show', $property->id) }}" class="btn-view" style="padding: 6px 12px; font-size: 12px;">
                                        <i class="fa-regular fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 30px 0;">
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
