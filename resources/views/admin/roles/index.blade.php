@extends('admin.layouts.app')

@section('title', 'Role & Permission Management')
@section('page-title', 'Role & Permission')

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
    color: #CBD5E1 !important;
    font-weight: 500;
    margin: 0;
}

.btn-gold {
    background: #2563EB !important;
    color: #FFFFFF !important;
    padding: 10px 22px;
    border-radius: 10px;
    text-decoration: none !important;
    font-size: 14px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #3B82F6 !important;
    cursor: pointer;
    transition: all .25s ease;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35);
}
.btn-gold:hover {
    background: #1D4ED8 !important;
    color: #FFFFFF !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 22px rgba(37, 99, 235, 0.50);
}

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important;
    padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 28px;
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
    max-width: 480px;
}
.search-input {
    flex: 1;
    padding: 10px 14px !important;
    background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important;
    font-size: 13.5px;
    color: #FFFFFF !important;
    outline: none;
    transition: all 0.2s ease;
}
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}

.btn-search {
    background: #2563EB !important;
    color: #FFFFFF !important;
    padding: 10px 20px !important;
    border-radius: 10px !important;
    border: 1px solid #3B82F6 !important;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    white-space: nowrap !important;
}
.btn-search:hover {
    background: #1D4ED8 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50);
}

.btn-reset {
    padding: 10px 16px;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    background: rgba(255, 255, 255, 0.06) !important;
    color: #CBD5E1 !important;
    border-radius: 10px !important;
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.btn-reset:hover {
    background: rgba(255, 255, 255, 0.12) !important;
    color: #FFFFFF !important;
}

.table-container {
    width: 100%;
    overflow-x: auto;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.10);
    background: rgba(16, 22, 34, 0.50);
}
.premium-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 13.5px;
}
.premium-table th {
    padding: 16px 18px !important;
    background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important;
    font-weight: 800;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.9px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 16px 18px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px;
    color: #E2E8F0 !important;
    font-weight: 500;
    vertical-align: middle;
}
.premium-table tr:last-child td { border-bottom: none !important; }
.premium-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.04) !important;
}

/* ── Users Count Badge ── */
.users-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(59, 130, 246, 0.18) !important;
    border: 1px solid rgba(59, 130, 246, 0.35) !important;
    color: #60A5FA !important;
    font-size: 12px;
    font-weight: 800;
    border-radius: 20px;
    padding: 3px 12px;
    min-width: 32px;
}

/* ── Status Badges ── */
.badge-status-btn {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    outline: none;
}
.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    font-size: 11px;
    font-weight: 800;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    white-space: nowrap !important;
    transition: all 0.2s ease;
}
.badge-status-btn:hover .badge {
    transform: scale(1.05);
}
.badge-active {
    background: rgba(16, 185, 129, 0.18) !important;
    color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.35) !important;
}
.badge-inactive {
    background: rgba(239, 68, 68, 0.18) !important;
    color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.35) !important;
}

/* ── Action Buttons Container ── */
.table-action-buttons {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: nowrap !important;
    white-space: nowrap !important;
    justify-content: flex-start !important;
}
.table-action-buttons form {
    display: inline-flex !important;
    margin: 0 !important;
    padding: 0 !important;
}

/* Dedicated Permissions Action Button */
.btn-perm, a.btn-perm {
    background: rgba(139, 92, 246, 0.15) !important;
    border: 1px solid rgba(139, 92, 246, 0.35) !important;
    color: #C4B5FD !important;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    white-space: nowrap !important;
    box-shadow: 0 2px 8px rgba(139, 92, 246, 0.20);
    cursor: pointer;
}
.btn-perm:hover, a.btn-perm:hover {
    background: #8B5CF6 !important;
    border-color: #8B5CF6 !important;
    color: #FFFFFF !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(139, 92, 246, 0.45);
}
.btn-perm i {
    font-size: 12px;
    color: inherit;
}

.alert-success {
    background: rgba(16, 185, 129, 0.16) !important;
    border: 1px solid rgba(16, 185, 129, 0.38) !important;
    color: #34D399 !important;
    padding: 14px 18px !important;
    border-radius: 12px !important;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 700 !important;
    display: flex;
    align-items: center;
    gap: 8px;
}
.alert-danger {
    background: rgba(239, 68, 68, 0.16) !important;
    border: 1px solid rgba(239, 68, 68, 0.38) !important;
    color: #F87171 !important;
    padding: 14px 18px !important;
    border-radius: 12px !important;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 700 !important;
    display: flex;
    align-items: center;
    gap: 8px;
}
.pagination-wrapper {
    margin-top: 24px;
    display: flex;
    justify-content: center;
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
                <a href="{{ route('roles.index') }}" class="btn-reset">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Role Name</th>
                    <th>Description</th>
                    <th style="text-align:center;">Users</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width: 320px; text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $key => $role)
                    <tr>
                        <td style="color:#94A3B8; font-weight:700;">{{ method_exists($roles, 'firstItem') ? ($roles->firstItem() + $key) : ($key + 1) }}</td>
                        <td><strong style="color: #FFFFFF !important; font-size:14.5px;">{{ $role->role_name ?? $role->name }}</strong></td>
                        <td style="color: #CBD5E1; max-width: 300px;">{{ $role->description ?? '-' }}</td>
                        <td style="text-align:center;">
                            <span class="users-count">{{ $role->users_count ?? 0 }}</span>
                        </td>
                        <td style="text-align:center;">
                            <form action="{{ route('roles.toggle-status', $role->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="badge-status-btn" title="Click to toggle status">
                                    <span class="badge badge-{{ $role->status ?? 'active' }}">
                                        <i class="fa-solid {{ ($role->status ?? 'active') === 'active' ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                                        {{ ucfirst($role->status ?? 'active') }}
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="table-action-buttons" style="justify-content:center;">
                                <a href="{{ route('roles.show', $role->id) }}" class="btn-view">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn-edit">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="{{ route('roles.permissions', $role->id) }}" class="btn-perm">
                                    <i class="fa-solid fa-shield-halved"></i> Permissions
                                </a>
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this role? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" align="center" style="padding: 36px; color: #94A3B8; font-weight:600;">No roles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($roles, 'links'))
        <div class="pagination-wrapper">
            {{ $roles->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
