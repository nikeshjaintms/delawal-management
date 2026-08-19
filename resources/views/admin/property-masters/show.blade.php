@extends('admin.layouts.app')

@section('title', 'Property Master Details')
@section('page-title', 'Property Management')

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
        gap: 24px;
        margin-bottom: 30px;
    }

    @media (max-width: 992px) {
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
        color: #FFFFFF;
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
        font-size: 11.5px;
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

    .projects-table {
        width: 100%;
        border-collapse: collapse;
    }

    .projects-table th {
        padding: 12px 16px;
        background: #F9FAFB;
        color: var(--text-secondary);
        font-weight: 600;
        border-bottom: 1px solid var(--border-color);
        font-size: 11.5px;
        text-transform: uppercase;
    }

    .projects-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #F1F5F9;
        font-size: 13.5px;
    }

    .projects-table tr:hover {
        background: #F8FAFC;
    }
</style>

{{-- Breadcrumb --}}
<div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 15px;">
    Property Management &nbsp;&gt;&nbsp; 
    <a href="{{ route('property-masters.index') }}" style="color: var(--gold); text-decoration: none; font-weight: 600;">Property Master</a> &nbsp;&gt;&nbsp; 
    <span style="color: var(--text-primary); font-weight: 600;">{{ $propertyMaster->property_name }}</span>
</div>

<div class="crud-header">
    <div class="crud-title">
        <h2>{{ $propertyMaster->property_name }}</h2>
        <p>Property profile, location details, and associated Projects.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        @if(Auth::user() && Auth::user()->hasPermission('property_edit'))
            <a href="{{ route('property-masters.edit', $propertyMaster->id) }}" class="btn-gold">
                <i class="fa-regular fa-pen-to-square"></i> Edit Property
            </a>
        @endif
        <a href="{{ route('property-masters.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to Properties
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid #22C55E; color: #16803D; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

<div class="details-grid">
    <!-- Left Panel: Property Profile -->
    <div class="card-box" style="height: fit-content;">
        @if($propertyMaster->main_image)
            <img src="{{ asset('storage/' . $propertyMaster->main_image) }}" alt="{{ $propertyMaster->property_name }}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 20px;">
        @else
            <div style="width: 100%; height: 180px; background: rgba(59, 130, 246, 0.14); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #60A5FA; border: 1px solid rgba(96, 165, 250, 0.35); margin-bottom: 20px;">
                <i class="fa-solid fa-building" style="font-size: 48px;"></i>
            </div>
        @endif

        <ul class="info-list">
            <li class="info-item">
                <span class="info-label">Property Code</span>
                <span class="info-value"><code>{{ $propertyMaster->property_code }}</code></span>
            </li>
            <li class="info-item">
                <span class="info-label">Firm</span>
                <span class="info-value">{{ $propertyMaster->firm->firm_name ?? '-' }}</span>
            </li>
            <li class="info-item">
                <span class="info-label">Status</span>
                <span class="info-value">
                    <span class="badge {{ $propertyMaster->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ $propertyMaster->status }}
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
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px;">Location & Address</h3>
            <div style="font-size: 14px; color: var(--text-primary); line-height: 1.6;">
                @if($propertyMaster->address) {{ $propertyMaster->address }}, @endif
                @if($propertyMaster->location) {{ $propertyMaster->location }}, @endif
                @if($propertyMaster->city) {{ $propertyMaster->city }}, @endif
                @if($propertyMaster->state) {{ $propertyMaster->state }} @endif
                @if($propertyMaster->pincode) - {{ $propertyMaster->pincode }} @endif
                @if($propertyMaster->country) ({{ $propertyMaster->country }}) @endif
                @if(!$propertyMaster->address && !$propertyMaster->location && !$propertyMaster->city)
                    <span style="color: var(--text-secondary);">No location details provided.</span>
                @endif
            </div>

            @if($propertyMaster->description)
                <div style="margin-top: 15px; pt-3; border-top: 1px solid #F1F5F9; font-size: 13.5px; color: var(--text-secondary);">
                    <strong>Description:</strong> {{ $propertyMaster->description }}
                </div>
            @endif
        </div>

        <div class="card-box">
            <div class="section-title">
                <span>Managed Projects ({{ $propertyMaster->projects->count() }})</span>
                @if(Auth::user() && Auth::user()->hasPermission('project_add'))
                    <a href="{{ route('projects.create', ['property_id' => $propertyMaster->id]) }}" class="btn-gold" style="padding: 7px 14px; font-size: 13px;">
                        <i class="fa-solid fa-plus"></i> Add Project
                    </a>
                @endif
            </div>

            <div style="width: 100%; overflow-x: auto;">
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
                                    <a href="{{ route('projects.show', $project->id) }}" style="color: var(--gold); font-weight: 600; text-decoration: none;">
                                        {{ $project->project_name }}
                                    </a>
                                </td>
                                <td><code>{{ $project->project_code }}</code></td>
                                <td>{{ $project->project_type }}</td>
                                <td>
                                    <a href="{{ route('projects.show', $project->id) }}" style="color: var(--text-primary); font-weight: 600; text-decoration: none;">
                                        <i class="fa-solid fa-boxes-stacked" style="color: var(--gold);"></i> {{ $project->bulks->count() }} Records
                                    </a>
                                </td>
                                <td>
                                    <span class="badge {{ $project->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $project->status }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('projects.show', $project->id) }}" class="btn-outline" style="padding: 4px 10px; font-size: 12px;">
                                        Open Project <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 24px;">
                                    No Projects added under this Property yet. Click <strong>+ Add Project</strong> above to add one.
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
