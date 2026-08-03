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
        box-shadow: 0 4px 10px rgba(212, 175, 55, 0.2);
    }

    .btn-gold:hover {
        background-color: #B58D1B;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(212, 175, 55, 0.3);
    }

    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        box-shadow: var(--soft-shadow);
    }

    .filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .search-form {
        display: flex;
        gap: 10px;
        flex: 1;
        max-width: 600px;
    }

    .search-input, .filter-select {
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 13.5px;
        font-family: var(--font-primary);
        color: var(--text-primary);
        outline: none;
        transition: var(--transition);
        background-color: #FFFFFF;
    }

    .search-input {
        flex: 1;
    }

    .search-input:focus, .filter-select:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px var(--gold-light);
    }

    .btn-search {
        background-color: var(--text-primary);
        color: #FFFFFF;
        padding: 10px 18px;
        border-radius: 8px;
        border: none;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-search:hover {
        background-color: #1E293B;
    }

    .btn-reset {
        padding: 10px 14px;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 500;
        transition: var(--transition);
    }

    .btn-reset:hover {
        color: var(--text-primary);
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .premium-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }

    .premium-table th {
        padding: 14px 16px;
        background: #F9FAFB;
        color: var(--text-secondary);
        font-weight: 600;
        border-bottom: 1px solid var(--border-color);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .premium-table td {
        padding: 16px;
        border-bottom: 1px solid #F1F5F9;
        color: var(--text-primary);
        vertical-align: middle;
    }

    .premium-table tr:last-child td {
        border-bottom: none;
    }

    .premium-table tbody tr:hover {
        background-color: #F9FAFB;
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

    .action-links {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .action-link {
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: var(--transition);
    }

    .action-link:hover {
        color: var(--text-primary);
    }

    .action-link.delete:hover {
        color: #EF4444;
    }

    .project-img-thumb {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid var(--border-color);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 40px;
        color: var(--border-color);
        margin-bottom: 12px;
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Project Management</h2>
        <p>Create and manage projects, firms, and location details.</p>
    </div>
    @if($authUser && $authUser->hasPermission('project_add'))
        <a href="{{ route('projects.create') }}" class="btn-gold">
            <i class="fa-solid fa-plus"></i> Add Project
        </a>
    @endif
</div>

<div class="card-box">
    <div class="filter-bar">
        <form method="GET" action="{{ route('projects.index') }}" class="search-form">
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

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Project Name</th>
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
                                <div style="width: 48px; height: 48px; background: #F3F4F6; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #9CA3AF; border: 1px solid var(--border-color);">
                                    <i class="fa-solid fa-building" style="font-size: 18px;"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('projects.show', $project->id) }}" style="color: var(--gold); font-weight: 600; text-decoration: none;">
                                {{ $project->project_name }}
                            </a>
                        </td>
                        <td><code style="background: #F1F5F9; padding: 2px 6px; border-radius: 4px; font-weight: 600;">{{ $project->project_code }}</code></td>
                        @if($authUser && $authUser->isAdmin())
                            <td>{{ $project->firm->firm_name ?? '-' }}</td>
                        @endif
                        <td>{{ $project->project_type }}</td>
                        <td>{{ $project->city ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $project->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                {{ $project->status }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div class="action-links" style="justify-content: flex-end;">
                                @if($authUser && $authUser->hasPermission('project_view'))
                                    <a href="{{ route('projects.show', $project->id) }}" class="action-link">
                                        <i class="fa-regular fa-eye"></i> View
                                    </a>
                                @endif
                                @if($authUser && $authUser->hasPermission('project_edit'))
                                    <a href="{{ route('projects.edit', $project->id) }}" class="action-link">
                                        <i class="fa-regular fa-pen-to-square"></i> Edit
                                    </a>
                                @endif
                                @if($authUser && $authUser->hasPermission('project_delete'))
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project?')" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-link delete" style="background: none; border: none; cursor: pointer; padding: 0;">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $authUser && $authUser->isAdmin() ? 8 : 7 }}">
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
