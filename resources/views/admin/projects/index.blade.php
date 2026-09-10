@extends('admin.layouts.app')

@section('title', 'Projects')
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
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 28px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 22px;
    border-radius: 12px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 18px rgba(37,99,235,0.38);
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37,99,235,0.52); }

/* Perfectly RED PDF Button */
.btn-pdf-red {
    background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%) !important;
    color: #FFFFFF !important; padding: 11px 22px;
    border-radius: 12px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #F87171 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 18px rgba(239, 68, 68, 0.45);
}
.btn-pdf-red:hover {
    background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%) !important;
    color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(239, 68, 68, 0.65);
}

/* Vibrant Emerald Excel Button */
.btn-excel {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important;
    color: #FFFFFF !important; padding: 11px 22px;
    border-radius: 12px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #34D399 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 18px rgba(16, 185, 129, 0.40);
}
.btn-excel:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(16, 185, 129, 0.60);
}

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
}

.filter-bar {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;
    background: rgba(255, 255, 255, 0.04) !important; padding: 16px 20px !important;
    border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
    width: 100%; overflow-x: auto;
}
.search-form { display: flex; gap: 12px; flex: 1; width: 100%; align-items: center; flex-wrap: nowrap !important; max-width: 100% !important; }
.filter-select { flex-shrink: 0; min-width: 140px; }
.search-input, .filter-select {
    padding: 11px 16px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
}
.filter-select option { background: #101622 !important; color: #FFFFFF !important; }
.search-input { flex: 1; min-width: 200px; }
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus, .filter-select:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 22px;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    flex-shrink: 0; white-space: nowrap !important;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 14px; transition: color .2s ease; flex-shrink: 0; white-space: nowrap !important; }
.btn-reset:hover { color: #FFFFFF !important; }

.table-responsive-wrapper { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); }

.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
.premium-table th {
    padding: 16px 22px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: 0.9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 18px 22px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 14px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
    white-space: nowrap !important;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.badge { display: inline-block; padding: 5px 14px; font-size: 11.5px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.table-action-buttons { display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; flex-wrap: nowrap !important; white-space: nowrap !important; justify-content: flex-end; }
.table-action-buttons form { display: inline-flex !important; margin: 0 !important; padding: 0 !important; }

.action-link-pdf-red {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.35) !important; border-radius: 10px; text-decoration: none !important;
    font-size: 13px !important; font-weight: 700 !important; transition: all .2s ease; white-space: nowrap !important;
}
.action-link-pdf-red:hover { background: #DC2626 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(220, 38, 38, 0.40); }

.action-link-excel {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(16, 185, 129, 0.15) !important; color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.35) !important; border-radius: 10px; text-decoration: none !important;
    font-size: 13px !important; font-weight: 700 !important; transition: all .2s ease; cursor: pointer; white-space: nowrap !important;
}
.action-link-excel:hover { background: #10B981 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(16, 185, 129, 0.40); }

.action-link-view {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important;
    border: 1px solid rgba(96, 165, 250, 0.30) !important; border-radius: 10px; text-decoration: none !important;
    font-size: 13px !important; font-weight: 700 !important; transition: all .2s ease; white-space: nowrap !important;
}
.action-link-view:hover { background: #2563EB !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(37, 99, 235, 0.40); }

.action-link-edit {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(245, 158, 11, 0.15) !important; color: #FBBF24 !important;
    border: 1px solid rgba(245, 158, 11, 0.30) !important; border-radius: 10px; text-decoration: none !important;
    font-size: 13px !important; font-weight: 700 !important; transition: all .2s ease; white-space: nowrap !important;
}
.action-link-edit:hover { background: #D97706 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(217, 119, 6, 0.40); }

.action-link-delete {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(239, 68, 68, 0.15) !important; color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.30) !important; border-radius: 10px; text-decoration: none !important;
    font-size: 13px !important; font-weight: 700 !important; transition: all .2s ease; cursor: pointer; white-space: nowrap !important;
}
.action-link-delete:hover { background: #DC2626 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(220, 38, 38, 0.40); }

.project-img-thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.15); }
.empty-state { text-align: center; padding: 40px 20px; color: #CBD5E1; }
.empty-state i { font-size: 40px; color: #94A3B8; margin-bottom: 12px; }

/* Modal Styles */
.modal-overlay {
    position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(10px);
    display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px;
}
.modal-card {
    background: #101622; border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 20px; width: 100%; max-width: 820px; max-height: 90vh;
    display: flex; flex-direction: column; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6); overflow: hidden;
}
.modal-header {
    padding: 20px 26px; border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex; justify-content: space-between; align-items: flex-start;
    background: rgba(255, 255, 255, 0.03);
}
.modal-title { font-size: 19px; font-weight: 800; color: #FFFFFF !important; margin: 0; }
.modal-subtitle { font-size: 13.5px; color: #94A3B8 !important; margin-top: 4px; margin-bottom: 0; font-weight: 500; }
.modal-close {
    background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 8px; font-size: 20px; color: #CBD5E1; cursor: pointer;
    line-height: 1; padding: 6px 12px; transition: all 0.2s ease;
}
.modal-close:hover { color: #FFFFFF; background: rgba(239, 68, 68, 0.30); border-color: #EF4444; }
.modal-body { padding: 26px; overflow-y: auto; flex: 1; }

.btn-template-download {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(59, 130, 246, 0.15); color: #60A5FA;
    border: 1px solid rgba(59, 130, 246, 0.30); padding: 8px 16px;
    border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; transition: all 0.2s ease;
}
.btn-template-download:hover { background: #2563EB; color: #FFFFFF; transform: translateY(-1px); }

.upload-dropzone {
    border: 2px dashed rgba(255, 255, 255, 0.20); border-radius: 16px;
    padding: 36px 20px; text-align: center; background: rgba(255, 255, 255, 0.03);
    transition: all 0.2s ease; cursor: pointer;
}
.upload-dropzone:hover, .upload-dropzone.dragover { border-color: #3B82F6; background: rgba(59, 130, 246, 0.08); }
.btn-browse {
    background: #2563EB; color: #FFFFFF; border: 1px solid #3B82F6;
    padding: 9px 20px; border-radius: 10px; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.btn-browse:hover { background: #1D4ED8; transform: translateY(-1px); }
.btn-secondary-custom {
    display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 8px !important;
    padding: 10px 22px !important; background: rgba(255, 255, 255, 0.08) !important;
    border: 1px solid rgba(255, 255, 255, 0.20) !important; color: #FFFFFF !important;
    font-size: 13.5px !important; font-weight: 700 !important; border-radius: 10px !important; cursor: pointer !important;
    transition: all 0.2s ease !important; text-decoration: none !important;
}
.btn-secondary-custom:hover { background: rgba(255, 255, 255, 0.18) !important; color: #FFFFFF !important; border-color: rgba(255, 255, 255, 0.35) !important; }
.btn-primary-custom {
    display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 8px !important;
    padding: 10px 24px !important; background: #2563EB !important; border: 1px solid #3B82F6 !important;
    color: #FFFFFF !important; font-size: 13.5px !important; font-weight: 700 !important;
    border-radius: 10px !important; cursor: pointer !important; box-shadow: 0 4px 16px rgba(37, 99, 235, 0.40) !important;
    transition: all 0.2s ease !important; text-decoration: none !important;
}
.btn-primary-custom:hover { background: #1D4ED8 !important; transform: translateY(-1px) !important; box-shadow: 0 6px 22px rgba(37, 99, 235, 0.55) !important; }
.badge-valid { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; }
.badge-invalid { background: rgba(239, 68, 68, 0.18); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.35); display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; }
</style>

{{-- Breadcrumb --}}
<div class="breadcrumb-nav">
    <span><i class="fa-solid fa-city" style="color: #60A5FA; margin-right: 6px;"></i>Property Management</span>
    <i class="fa-solid fa-chevron-right separator"></i>
    @if(isset($propertyMaster) && $propertyMaster)
        <a href="{{ route('property-masters.show', $propertyMaster->id) }}">{{ $propertyMaster->property_name }}</a>
        <i class="fa-solid fa-chevron-right separator"></i>
    @endif
    <span class="active">Projects</span>
</div>

<div class="crud-header">
    <div class="crud-title">
        <h2>
            @if(isset($propertyMaster) && $propertyMaster)
                Projects under {{ $propertyMaster->property_name }}
            @else
                Project Management
            @endif
        </h2>
        <p>Manage projects associated with Property Masters and upload properties via Excel.</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        {{-- Perfectly Vibrant RED PDF Button --}}
        <a href="{{ route('projects.pdf', request()->query()) }}" target="_blank" class="btn-pdf-red" title="Export PDF Directory">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>

        {{-- Direct Prominent Excel Upload Button --}}
        <button type="button" class="btn-excel" onclick="openProjectImportModal()" title="Upload & Import Properties from Excel">
            <i class="fa-solid fa-file-excel"></i> <span>Import from Excel</span>
        </button>

        @if($authUser && $authUser->hasPermission('project_add'))
            <a href="{{ route('projects.create', isset($propertyMaster) && $propertyMaster ? ['property_id' => $propertyMaster->id] : []) }}" class="btn-gold">
                <i class="fa-solid fa-plus"></i> Add Project
            </a>
        @endif
    </div>
</div>

<div class="card-box">
    <div class="filter-bar">
        <form method="GET" action="{{ route('projects.index') }}" class="search-form">
            @if(request('property_id'))
                <input type="hidden" name="property_id" value="{{ request('property_id') }}">
            @endif

            @if($authUser && $authUser->isAdmin())
                <select name="firm_id" class="filter-select">
                    <option value="">All Firms</option>
                    @foreach(\App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get() as $firm)
                        <option value="{{ $firm->id }}" {{ request('firm_id') == $firm->id ? 'selected' : '' }}>
                            {{ $firm->firm_name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <select name="status" class="filter-select">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Search by name, code, type or city...">
            <button type="submit" class="btn-search">Search</button>
            <a href="{{ route('projects.index') }}" class="btn-reset">Reset</a>
        </form>
    </div>

    @if(session('success'))
        <div style="background: rgba(34, 197, 94, 0.1); color: #16803D; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive-wrapper">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Project Name</th>
                    <th>Property Master</th>
                    <th>Project Code</th>
                    @if($authUser && $authUser->isAdmin())
                        <th>Firm</th>
                    @endif
                    <th>Type</th>
                    <th>City</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                    <tr>
                        <td>
                            @if($project->project_image)
                                <img src="{{ asset('storage/' . $project->project_image) }}" alt="Project" class="project-img-thumb">
                            @else
                                <div style="width: 44px; height: 44px; background: rgba(59, 130, 246, 0.14); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #60A5FA; border: 1px solid rgba(96, 165, 250, 0.35);">
                                    <i class="fa-solid fa-building" style="font-size: 18px;"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('projects.show', $project->id) }}" style="color: #60A5FA !important; font-weight: 700; text-decoration: none; white-space: nowrap !important;">
                                {{ $project->project_name }}
                            </a>
                        </td>
                        <td>
                            @if($project->propertyMaster)
                                <a href="{{ route('property-masters.show', $project->propertyMaster->id) }}" style="color: #FFFFFF !important; font-weight: 700; text-decoration: none; white-space: nowrap !important;">
                                    <i class="fa-solid fa-building" style="color: #FBBF24;"></i> {{ $project->propertyMaster->property_name }}
                                </a>
                            @else
                                <span style="color: rgba(255, 255, 255, 0.5);">-</span>
                            @endif
                        </td>
                        <td><code style="background: rgba(59, 130, 246, 0.15); color: #93C5FD; border: 1px solid rgba(96, 165, 250, 0.35); padding: 5px 10px; border-radius: 6px; font-weight: 700; font-family: monospace; white-space: nowrap !important; display: inline-block;">{{ $project->project_code }}</code></td>
                        @if($authUser && $authUser->isAdmin())
                            <td style="white-space: nowrap !important; font-weight: 600; color: #E2E8F0;">{{ $project->firm->firm_name ?? '-' }}</td>
                        @endif
                        <td style="white-space: nowrap !important;">{{ ucfirst($project->project_type) }}</td>
                        <td style="white-space: nowrap !important;">{{ $project->city ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $project->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div class="table-action-buttons">
                                {{-- Direct Import Excel Button per Project --}}
                                <button type="button" class="action-link-excel" onclick="openProjectImportModal({{ $project->id }}, '{{ addslashes($project->project_name) }}', {{ $project->firm_id ?? 'null' }})" title="Import Properties into {{ $project->project_name }}">
                                    <i class="fa-solid fa-file-excel"></i> Import
                                </button>

                                @if($authUser && $authUser->hasPermission('project_view'))
                                    {{-- Crisp Red PDF Link --}}
                                    <a href="{{ route('projects.detail-pdf', $project->id) }}" target="_blank" class="action-link-pdf-red" title="Print / PDF Dossier">
                                        <i class="fa-solid fa-file-pdf"></i> PDF
                                    </a>
                                    <a href="{{ route('projects.show', $project->id) }}" class="action-link-view">
                                        <i class="fa-regular fa-eye"></i> View
                                    </a>
                                @endif
                                @if($authUser && $authUser->hasPermission('project_edit'))
                                    <a href="{{ route('projects.edit', $project->id) }}" class="action-link-edit">
                                        <i class="fa-regular fa-pen-to-square"></i> Edit
                                    </a>
                                @endif
                                @if($authUser && $authUser->hasPermission('project_delete'))
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project?')" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-link-delete">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $authUser && $authUser->isAdmin() ? 9 : 8 }}">
                            <div class="empty-state">
                                <i class="fa-solid fa-city"></i>
                                <p>No projects found. Add your first project to get started!</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $projects->links() }}
    </div>
</div>

<!-- Project Excel Import Modal -->
<div id="projectImportModal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h3 class="modal-title"><i class="fa-solid fa-file-excel" style="color: #10B981; margin-right: 8px;"></i> Import Properties from Excel</h3>
                <p class="modal-subtitle">Upload property Excel data (.xlsx, .xls, .csv) directly into your selected Project.</p>
            </div>
            <button type="button" class="modal-close" onclick="closeProjectImportModal()">&times;</button>
        </div>
        
        <div class="modal-body">
            <!-- Step 1: Upload Form -->
            <form id="projectImportForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="context_firm_id" id="modal_firm_id_input" value="">

                <!-- Project Selector Dropdown -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13.5px; font-weight: 700; color: #FFFFFF; margin-bottom: 8px;">
                        <i class="fa-solid fa-building-columns" style="color: #60A5FA; margin-right: 6px;"></i> Select Target Project:
                    </label>
                    <select name="context_project_id" id="modal_project_selector" class="search-input" style="width: 100%;" onchange="updateModalTemplateLink()">
                        <option value="">-- Auto-Detect Project from Excel File --</option>
                        @foreach(\App\Models\Project::orderBy('project_name')->get() as $p)
                            <option value="{{ $p->id }}" data-firm="{{ $p->firm_id }}">
                                {{ $p->project_name }} ({{ $p->project_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); padding: 14px 18px; border-radius: 12px; flex-wrap: wrap; gap: 10px;">
                    <div style="font-size: 13.5px; color: #FFFFFF; font-weight: 600;">
                        <i class="fa-solid fa-circle-info" style="color: #60A5FA; margin-right: 6px;"></i> Download prefilled template for chosen project:
                    </div>
                    <a href="{{ route('properties.import.template') }}" id="modal_download_template_btn" class="btn-template-download" target="_blank">
                        <i class="fa-solid fa-download"></i> Download Excel Template
                    </a>
                </div>

                <div class="upload-dropzone" id="proj-dropzone-area">
                    <i class="fa-solid fa-cloud-arrow-up" style="font-size: 36px; color: #60A5FA; margin-bottom: 12px;"></i>
                    <p style="font-weight: 700; color: #FFFFFF; margin-bottom: 4px; font-size: 15px;">Choose an Excel file or drag & drop</p>
                    <p style="font-size: 13px; color: #94A3B8; margin-bottom: 16px;">Supported formats: <strong style="color: #FFFFFF;">.xlsx, .xls, .csv</strong> (Max size: 10MB)</p>
                    <input type="file" name="excel_file" id="proj_excel_file_input" accept=".xlsx,.xls,.csv" required style="display: none;">
                    <button type="button" class="btn-browse" onclick="document.getElementById('proj_excel_file_input').click()">Select Excel File</button>
                    <div id="proj-selected-file-name" style="margin-top: 14px; font-weight: 700; font-size: 14px; color: #60A5FA; display: none;"></div>
                </div>

                <div style="margin-top: 20px; padding-top: 16px; border-top: 1px dashed rgba(255, 255, 255, 0.12);">
                    <label style="font-size: 13px; font-weight: 700; color: #FFFFFF; margin-bottom: 6px; display: block;">Optional Property Images Archive (ZIP file):</label>
                    <input type="file" name="image_archive" id="proj_image_archive_input" accept=".zip" class="search-input" style="width: 100%; max-width: 100%; box-sizing: border-box;">
                    <div style="font-size: 12.5px; color: #94A3B8; margin-top: 6px;">
                        If your Excel file contains an <strong style="color: #FFFFFF;">Image Filename</strong> column (e.g. <code>plot-001.jpg</code>), upload a ZIP archive containing those image files.
                    </div>
                </div>

                <div style="margin-top: 26px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" class="btn-secondary-custom" onclick="closeProjectImportModal()">Cancel</button>
                    <button type="submit" class="btn-primary-custom" id="proj-btn-validate-upload">
                        <i class="fa-solid fa-check-circle"></i> Upload & Validate
                    </button>
                </div>
            </form>

            <!-- Loading Spinner -->
            <div id="proj-import-loader" style="display: none; text-align: center; padding: 40px 20px;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size: 40px; color: #60A5FA; margin-bottom: 16px;"></i>
                <h4 style="font-size: 17px; font-weight: 800; color: #FFFFFF; margin-bottom: 6px;">Reading and Validating Excel Data...</h4>
                <p style="font-size: 13.5px; color: #94A3B8;">Verifying property codes, duplicate protection, project and type mappings.</p>
            </div>

            <!-- Step 2: Validation Preview -->
            <div id="proj-validation-preview-section" style="display: none;">
                <div id="proj-preview-summary-bar" style="margin-bottom: 16px;"></div>

                <div class="table-container" style="max-height: 320px; overflow-y: auto; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 12px;">
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Row</th>
                                <th>Action</th>
                                <th>Code</th>
                                <th>Property Name</th>
                                <th>Project</th>
                                <th>Property Type</th>
                                <th>City</th>
                                <th>Status</th>
                                <th>Validation Status</th>
                            </tr>
                        </thead>
                        <tbody id="proj-preview-table-body">
                        </tbody>
                    </table>
                </div>

                <div id="proj-error-summary-box" style="display: none; margin-top: 16px; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.30); border-radius: 12px; padding: 14px 18px;">
                    <h5 style="color: #F87171; font-size: 14px; font-weight: 800; margin-top: 0; margin-bottom: 8px;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Validation Error Details (<span id="proj-failed-rows-count">0</span> invalid rows)
                    </h5>
                    <ul id="proj-error-list" style="margin: 0; padding-left: 20px; font-size: 13px; color: #FCA5A5; line-height: 1.6; max-height: 160px; overflow-y: auto;">
                    </ul>
                </div>

                <div style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center;">
                    <button type="button" class="btn-secondary-custom" onclick="resetProjectImportModal()">
                        <i class="fa-solid fa-arrow-left"></i> Re-upload File
                    </button>
                    <button type="button" class="btn-primary-custom" id="proj-btn-final-import" onclick="submitProjectFinalImport()" style="background: #10B981 !important; border-color: #10B981 !important; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.40);">
                        <i class="fa-solid fa-file-import"></i> <span id="proj-import-btn-text">Import Properties</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let projBatchId = null;
    let projValidCount = 0;

    function openProjectImportModal(projectId = null, projectName = '', firmId = null) {
        resetProjectImportModal();
        if (projectId) {
            const selector = document.getElementById('modal_project_selector');
            if (selector) {
                selector.value = projectId;
            }
            if (firmId) {
                document.getElementById('modal_firm_id_input').value = firmId;
            }
        }
        updateModalTemplateLink();
        document.getElementById('projectImportModal').style.display = 'flex';
    }

    function closeProjectImportModal() {
        document.getElementById('projectImportModal').style.display = 'none';
        resetProjectImportModal();
    }

    function resetProjectImportModal() {
        document.getElementById('projectImportForm').reset();
        document.getElementById('projectImportForm').style.display = 'block';
        document.getElementById('proj-import-loader').style.display = 'none';
        document.getElementById('proj-validation-preview-section').style.display = 'none';
        document.getElementById('proj-selected-file-name').style.display = 'none';
        document.getElementById('proj-selected-file-name').innerText = '';
        projBatchId = null;
        projValidCount = 0;
    }

    function updateModalTemplateLink() {
        const selector = document.getElementById('modal_project_selector');
        const projId = selector ? selector.value : '';
        const selectedOpt = selector ? selector.options[selector.selectedIndex] : null;
        const firmId = selectedOpt ? selectedOpt.getAttribute('data-firm') : '';

        if (firmId) {
            document.getElementById('modal_firm_id_input').value = firmId;
        }

        let baseUrl = '{{ route("properties.import.template") }}';
        let params = [];
        if (projId) params.push('project_id=' + encodeURIComponent(projId));
        if (firmId) params.push('firm_id=' + encodeURIComponent(firmId));

        if (params.length > 0) {
            baseUrl += '?' + params.join('&');
        }

        const btn = document.getElementById('modal_download_template_btn');
        if (btn) btn.href = baseUrl;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('proj_excel_file_input');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const fileName = e.target.files[0].name;
                    const fileSize = (e.target.files[0].size / (1024 * 1024)).toFixed(2);
                    const disp = document.getElementById('proj-selected-file-name');
                    disp.innerText = `📄 Selected File: ${fileName} (${fileSize} MB)`;
                    disp.style.display = 'block';
                }
            });
        }

        const dropzone = document.getElementById('proj-dropzone-area');
        if (dropzone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => { e.preventDefault(); dropzone.classList.add('dragover'); }, false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => { e.preventDefault(); dropzone.classList.remove('dragover'); }, false);
            });
            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files.length > 0) {
                    document.getElementById('proj_excel_file_input').files = files;
                    const fileName = files[0].name;
                    const fileSize = (files[0].size / (1024 * 1024)).toFixed(2);
                    const disp = document.getElementById('proj-selected-file-name');
                    disp.innerText = `📄 Selected File: ${fileName} (${fileSize} MB)`;
                    disp.style.display = 'block';
                }
            });
        }

        // Validate Form Submit
        const form = document.getElementById('projectImportForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const fileInput = document.getElementById('proj_excel_file_input');
                if (!fileInput.files || !fileInput.files[0]) {
                    alert('Please select an Excel file (.xlsx, .xls, .csv) to import.');
                    return;
                }

                const formData = new FormData(this);
                document.getElementById('projectImportForm').style.display = 'none';
                document.getElementById('proj-import-loader').style.display = 'block';

                fetch('{{ route("properties.import.validate") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('proj-import-loader').style.display = 'none';

                    if (!data.success) {
                        alert(data.message || 'Validation failed. Please check your Excel file.');
                        document.getElementById('projectImportForm').style.display = 'block';
                        return;
                    }

                    projBatchId = data.batch_id;
                    projValidCount = data.valid_count;

                    // Summary Bar
                    const summaryHtml = `
                        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; justify-content: space-between; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.10); border-radius: 12px; padding: 12px 18px;">
                            <div style="display: flex; gap: 14px; align-items: center;">
                                <span style="font-size: 13.5px; font-weight: 700; color: #FFFFFF;">Total Rows: <strong>${data.total_rows}</strong></span>
                                <span style="font-size: 13.5px; font-weight: 700; color: #34D399;"><i class="fa-solid fa-circle-check"></i> Valid: <strong>${data.valid_count}</strong></span>
                                ${data.invalid_count > 0 ? `<span style="font-size: 13.5px; font-weight: 700; color: #F87171;"><i class="fa-solid fa-triangle-exclamation"></i> Invalid: <strong>${data.invalid_count}</strong></span>` : ''}
                            </div>
                            <div style="font-size: 12.5px; color: #94A3B8;">
                                <span style="color: #60A5FA; font-weight: 700;">${data.new_count} New</span> | <span style="color: #FBBF24; font-weight: 700;">${data.update_count} Updates</span>
                            </div>
                        </div>
                    `;
                    document.getElementById('proj-preview-summary-bar').innerHTML = summaryHtml;

                    // Populate Table Body
                    const tbody = document.getElementById('proj-preview-table-body');
                    tbody.innerHTML = '';

                    const errors = [];
                    data.preview.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.style.background = row.is_valid ? 'transparent' : 'rgba(239, 68, 68, 0.08)';

                        const actionBadge = row.action === 'update' 
                            ? `<span style="background: rgba(245, 158, 11, 0.18); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); padding: 2px 8px; border-radius: 12px; font-size: 10.5px; font-weight: 800;">UPDATE</span>`
                            : `<span style="background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); padding: 2px 8px; border-radius: 12px; font-size: 10.5px; font-weight: 800;">+ NEW</span>`;

                        const statusBadge = row.is_valid
                            ? `<span class="badge-valid"><i class="fa-solid fa-check"></i> VALID</span>`
                            : `<span class="badge-invalid"><i class="fa-solid fa-times"></i> INVALID (${row.errors.length})</span>`;

                        tr.innerHTML = `
                            <td>${row.row_index}</td>
                            <td>${actionBadge}</td>
                            <td><strong>${row.property_code || '-'}</strong></td>
                            <td>${row.property_name || '-'}</td>
                            <td>${row.project_name || '-'}</td>
                            <td>${row.property_type_name || '-'}</td>
                            <td>${row.city || '-'}</td>
                            <td><span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #34D399;">${row.status}</span></td>
                            <td>${statusBadge}</td>
                        `;
                        tbody.appendChild(tr);

                        if (!row.is_valid && row.errors && row.errors.length > 0) {
                            row.errors.forEach(err => {
                                errors.push(`<strong>Row ${row.row_index} (${row.property_code || 'No Code'}):</strong> ${err}`);
                            });
                        }
                    });

                    // Error Box
                    const errorBox = document.getElementById('proj-error-summary-box');
                    const errorList = document.getElementById('proj-error-list');
                    if (errors.length > 0) {
                        document.getElementById('proj-failed-rows-count').innerText = data.invalid_count;
                        errorList.innerHTML = errors.map(e => `<li>${e}</li>`).join('');
                        errorBox.style.display = 'block';
                    } else {
                        errorBox.style.display = 'none';
                    }

                    // Import Button State
                    const finalBtn = document.getElementById('proj-btn-final-import');
                    const btnText = document.getElementById('proj-import-btn-text');
                    if (data.valid_count > 0) {
                        finalBtn.disabled = false;
                        finalBtn.style.opacity = '1';
                        finalBtn.style.cursor = 'pointer';
                        btnText.innerText = `Import ${data.valid_count} Valid Properties`;
                    } else {
                        finalBtn.disabled = true;
                        finalBtn.style.opacity = '0.5';
                        finalBtn.style.cursor = 'not-allowed';
                        btnText.innerText = `No Valid Records to Import`;
                    }

                    document.getElementById('proj-validation-preview-section').style.display = 'block';
                })
                .catch(err => {
                    document.getElementById('proj-import-loader').style.display = 'none';
                    document.getElementById('projectImportForm').style.display = 'block';
                    alert('Error connecting to server for Excel validation.');
                });
            });
        }
    });

    function submitProjectFinalImport() {
        if (!projBatchId || projValidCount <= 0) {
            alert('No valid properties available to import.');
            return;
        }

        const finalBtn = document.getElementById('proj-btn-final-import');
        finalBtn.disabled = true;
        finalBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Importing Properties...';

        fetch('{{ route("properties.import.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ batch_id: projBatchId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                window.location.reload();
            } else {
                alert('❌ ' + (data.message || 'Import process failed.'));
                finalBtn.disabled = false;
                finalBtn.innerHTML = '<i class="fa-solid fa-file-import"></i> <span>Try Again</span>';
            }
        })
        .catch(err => {
            alert('Error executing property import.');
            finalBtn.disabled = false;
            finalBtn.innerHTML = '<i class="fa-solid fa-file-import"></i> <span>Try Again</span>';
        });
    }
</script>
@endsection
