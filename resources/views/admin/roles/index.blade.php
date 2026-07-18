@extends('admin.layouts.app')

@section('title', 'Role & Permission Management')
@section('page-title', 'Role & Permission')

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
        max-width: 500px;
    }
    .search-input {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 13.5px;
        font-family: var(--font-primary);
        color: var(--text-primary);
        outline: none;
        transition: var(--transition);
    }
    .search-input:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px var(--blue-glow);
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
    .btn-search:hover { background-color: #1E293B; }
    .btn-reset {
        padding: 10px 14px;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 500;
        transition: var(--transition);
    }
    .btn-reset:hover { color: var(--text-primary); }
    .table-container { width: 100%; overflow-x: auto; }
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
    .premium-table tr:last-child td { border-bottom: none; }
    .premium-table tbody tr:hover { background-color: #F9FAFB; }
    .badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 20px;
        text-transform: uppercase;
    }
    .badge-active { background: rgba(34,197,94,0.1); color: #16803D; }
    .badge-inactive { background: rgba(239,68,68,0.1); color: #B91C1C; }
    .action-links {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }
    .action-link {
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 13px;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .action-link:hover { color: var(--text-primary); }
    .action-link.view:hover { color: #0EA5E9; }
    .action-link.edit:hover { color: #2563EB; }
    .action-link.perm:hover { color: #8B5CF6; }
    .action-link.toggle-active, .action-link.toggle-inactive {
        color: var(--text-secondary);
        background: none;
        border: none;
        cursor: pointer;
        font-family: var(--font-primary);
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 0;
    }
    .action-link.toggle-active:hover  { color: #16803D; }
    .action-link.toggle-inactive:hover { color: #B91C1C; }
    .action-link.delete-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--text-secondary);
        font-family: var(--font-primary);
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 0;
    }
    .action-link.delete-btn:hover { color: #EF4444; }
    .alert-success {
        background: rgba(34,197,94,0.08);
        border: 1px solid rgba(34,197,94,0.2);
        color: #16803D;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .alert-danger {
        background: rgba(239,68,68,0.08);
        border: 1px solid rgba(239,68,68,0.2);
        color: #B91C1C;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }
    .users-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--blue-light);
        color: var(--blue);
        font-size: 12px;
        font-weight: 700;
        border-radius: 20px;
        padding: 2px 10px;
        min-width: 28px;
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Role &amp; Permission Management</h2>
        <p>Manage roles and assign granular permissions to each role.</p>
    </div>
    <a href="{{ route('roles.create') }}" class="btn-gold">
        <i class="fa-solid fa-plus"></i>
        <span>Add Role</span>
    </a>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if(session('error'))
    <div class="alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="card-box">
    <div class="filter-bar">
        <form method="GET" action="{{ route('roles.index') }}" class="search-form">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by role name..." class="search-input @error('search') is-invalid @enderror">
            <button type="submit" class="btn-search">Search</button>
            @if(request('search'))
                <a href="{{ route('roles.index') }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Role Name</th>
                    <th>Description</th>
                    <th>Users</th>
                    <th>Status</th>
                    <th style="width: 280px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $key => $role)
                    <tr>
                        <td>{{ $roles->firstItem() + $key }}</td>
                        <td><strong>{{ $role->role_name ?? $role->name }}</strong></td>
                        <td>{{ $role->description ?? '-' }}</td>
                        <td>
                            <span class="users-count">{{ $role->users_count }}</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $role->status ?? 'active' }}">
                                {{ ucfirst($role->status ?? 'active') }}
                            </span>
                        </td>
                        <td>
                            <div class="table-action-buttons">
                                <a href="{{ route('roles.show', $role->id) }}" class="btn-view">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn-edit">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('roles.permissions', $role->id) }}" class="action-link perm">
                                    <i class="fa-solid fa-shield-halved"></i> Permissions
                                </a>
                                {{-- Status Toggle --}}
                                <form action="{{ route('roles.toggle-status', $role->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    @if(($role->status ?? 'active') === 'active')
                                        <button type="submit" class="action-link toggle-inactive" title="Set Inactive">
                                            <i class="fa-solid fa-toggle-on" style="color:#16803D;"></i> Active
                                        </button>
                                    @else
                                        <button type="submit" class="action-link toggle-active" title="Set Active">
                                            <i class="fa-solid fa-toggle-off" style="color:#94A3B8;"></i> Inactive
                                        </button>
                                    @endif
                                </form>
                                {{-- Delete --}}
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this role? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" align="center" style="padding: 30px; color: var(--text-secondary);">No roles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $roles->appends(request()->query())->links() }}
    </div>
</div>
@endsection

