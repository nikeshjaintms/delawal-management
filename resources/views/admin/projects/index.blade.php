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

.table-action-buttons { display: flex !important; flex-direction: row !important; align-items: center !important; gap: 10px !important; flex-wrap: nowrap !important; white-space: nowrap !important; justify-content: flex-end; }
.table-action-buttons form { display: inline-flex !important; margin: 0 !important; padding: 0 !important; }

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
</style>

{{-- Breadcrumb --}}
<div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 15px;">
    Property Management &nbsp;&gt;&nbsp; 
    @if(isset($propertyMaster) && $propertyMaster)
        <a href="{{ route('property-masters.show', $propertyMaster->id) }}" style="color: var(--gold); text-decoration: none; font-weight: 600;">{{ $propertyMaster->property_name }}</a> &nbsp;&gt;&nbsp; 
    @endif
    <span style="color: var(--text-primary); font-weight: 600;">Projects</span>
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
        <p>Manage projects associated with Property Masters.</p>
    </div>
    @if($authUser && $authUser->hasPermission('project_add'))
        <a href="{{ route('projects.create', isset($propertyMaster) && $propertyMaster ? ['property_id' => $propertyMaster->id] : []) }}" class="btn-gold">
            <i class="fa-solid fa-plus"></i> Add Project
        </a>
    @endif
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
                                @if($authUser && $authUser->hasPermission('project_view'))
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
@endsection
