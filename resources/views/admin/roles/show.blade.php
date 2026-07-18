@extends('admin.layouts.app')

@section('title', 'View Role — ' . ($role->role_name ?? $role->name))
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
    .header-actions { display: flex; gap: 10px; }
    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 28px;
        box-shadow: var(--soft-shadow);
        margin-bottom: 24px;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }
    @media(max-width: 768px) { .detail-grid { grid-template-columns: 1fr 1fr; } }
    @media(max-width: 480px) { .detail-grid { grid-template-columns: 1fr; } }
    .detail-item {}
    .detail-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }
    .detail-value {
        font-size: 14.5px;
        font-weight: 600;
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
    .badge-active   { background: rgba(34,197,94,0.1); color: #16803D; }
    .badge-inactive { background: rgba(239,68,68,0.1); color: #B91C1C; }
    .section-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-title i { color: var(--blue); }
    .table-container { width: 100%; overflow-x: auto; }
    .perm-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .perm-table th {
        padding: 10px 14px;
        background: #F8FAFC;
        color: #475569;
        font-weight: 700;
        border-bottom: 2px solid var(--border-color);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        text-align: center;
    }
    .perm-table th:first-child { text-align: left; min-width: 180px; }
    .perm-table td {
        padding: 10px 14px;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
        text-align: center;
    }
    .perm-table td:first-child { text-align: left; font-weight: 500; color: var(--text-primary); }
    .perm-table tbody tr:hover { background: #F0F7FF; }
    .perm-check { color: #10B981; font-size: 15px; }
    .perm-dash  { color: #CBD5E1; font-size: 13px; }
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
    .btn-gold:hover { background-color: #B58D1B; transform: translateY(-1px); }
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
    .btn-outline:hover { background: #F9FAFB; color: var(--text-primary); border-color: #D1D5DB; }
    .btn-purple {
        background: linear-gradient(135deg, #8B5CF6, #7C3AED);
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: 0 2px 8px rgba(139,92,246,0.3);
    }
    .btn-purple:hover { background: linear-gradient(135deg, #7C3AED, #6D28D9); transform: translateY(-1px); }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>{{ $role->role_name ?? $role->name }}</h2>
        <p>Role details and assigned permissions overview.</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('roles.permissions', $role->id) }}" class="btn-purple">
            <i class="fa-solid fa-shield-halved"></i> Assign Permissions
        </a>
        <a href="{{ route('roles.edit', $role->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit Role
        </a>
        <a href="{{ route('roles.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>
</div>

{{-- Details --}}
<div class="card-box">
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">Role Name</div>
            <div class="detail-value">{{ $role->role_name ?? $role->name }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Status</div>
            <div class="detail-value">
                <span class="badge badge-{{ $role->status ?? 'active' }}">{{ ucfirst($role->status ?? 'active') }}</span>
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Users Assigned</div>
            <div class="detail-value">{{ $role->users_count }} user(s)</div>
        </div>
        <div class="detail-item" style="grid-column: span 2;">
            <div class="detail-label">Description</div>
            <div class="detail-value" style="font-weight: 400; color: var(--text-secondary);">
                {{ $role->description ?? '—' }}
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Created At</div>
            <div class="detail-value" style="font-weight: 400; font-size: 13.5px;">{{ $role->created_at ? $role->created_at->format('d M Y, h:i A') : '—' }}</div>
        </div>
    </div>
</div>

{{-- Permissions Matrix (read-only) --}}
<div class="card-box">
    <div class="section-title">
        <i class="fa-solid fa-lock"></i> Assigned Permissions
    </div>

    @if($permissions->isEmpty())
        <p style="color: var(--text-secondary); font-size: 14px;">No permissions have been configured yet. <a href="{{ route('roles.permissions', $role->id) }}" style="color: var(--blue);">Assign permissions</a>.</p>
    @else
        <div class="table-container">
            <table class="perm-table">
                <thead>
                    <tr>
                        <th>Module</th>
                        @foreach($actions as $action)
                            <th>{{ ucfirst($action) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $moduleName => $modulePerms)
                        @php
                            $modulePermMap = $modulePerms->keyBy('action');
                        @endphp
                        <tr>
                            <td>{{ $moduleName }}</td>
                            @foreach($actions as $action)
                                @php
                                    $perm = $modulePermMap->get($action);
                                    $has = $perm && in_array($perm->permission_key, $assignedPermissions);
                                @endphp
                                <td>
                                    @if($has)
                                        <i class="fa-solid fa-circle-check perm-check"></i>
                                    @else
                                        <i class="fa-solid fa-minus perm-dash"></i>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
