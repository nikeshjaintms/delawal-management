@extends('admin.layouts.app')

@section('title', 'View Role — ' . ($role->role_name ?? $role->name))
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
.header-actions { display: flex; gap: 10px; flex-wrap: wrap; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important;
    padding: 28px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 28px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
}
@media(max-width: 768px) { .detail-grid { grid-template-columns: 1fr 1fr; } }
@media(max-width: 480px) { .detail-grid { grid-template-columns: 1fr; } }

.detail-item {
    padding: 16px 18px;
    background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 16px !important;
    transition: all .25s ease;
}
.detail-item:hover {
    border-color: rgba(59, 130, 246, 0.40) !important;
    transform: translateY(-2px);
}

.detail-label {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #94A3B8 !important;
    margin-bottom: 7px;
}
.detail-value {
    font-size: 15px;
    font-weight: 700;
    color: #FFFFFF !important;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    font-size: 11px;
    font-weight: 800;
    border-radius: 20px;
    text-transform: uppercase;
}
.badge-active   { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); }

.section-title {
    font-size: 13px;
    font-weight: 800;
    color: #60A5FA !important;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex;
    align-items: center;
    gap: 10px;
}

.table-container {
    width: 100%;
    overflow-x: auto;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.10);
    background: rgba(16, 22, 34, 0.50);
}
.perm-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}
.perm-table th {
    padding: 14px 16px;
    background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important;
    font-weight: 800;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    text-align: center;
    white-space: nowrap !important;
}
.perm-table th:first-child { text-align: left; min-width: 180px; }
.perm-table td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    vertical-align: middle;
    text-align: center;
    color: #E2E8F0;
}
.perm-table tr:last-child td { border-bottom: none !important; }
.perm-table td:first-child { text-align: left; font-weight: 700; color: #FFFFFF; }
.perm-table tbody tr:hover { background: rgba(255, 255, 255, 0.04); }
.perm-check { color: #34D399; font-size: 16px; }
.perm-dash  { color: #64748B; font-size: 14px; }

.btn-gold {
    background: #2563EB !important;
    color: #FFFFFF !important;
    padding: 10px 22px;
    border-radius: 10px;
    text-decoration: none !important;
    font-size: 13.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #3B82F6 !important;
    cursor: pointer;
    transition: all .25s ease;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.btn-gold:hover {
    background: #1D4ED8 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50);
}

.btn-outline {
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important;
    padding: 10px 20px;
    border-radius: 10px;
    text-decoration: none !important;
    font-size: 13.5px;
    font-weight: 600;
    transition: all .25s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-outline:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    color: #FFFFFF !important;
    transform: translateY(-2px);
}

.btn-purple {
    background: #8B5CF6 !important;
    color: #FFFFFF !important;
    padding: 10px 22px;
    border-radius: 10px;
    text-decoration: none !important;
    font-size: 13.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #A78BFA !important;
    transition: all .25s ease;
    box-shadow: 0 4px 14px rgba(139, 92, 246, 0.35);
}
.btn-purple:hover {
    background: #7C3AED !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(139, 92, 246, 0.50);
}
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
            <i class="fa-solid fa-pen-to-square"></i> Edit Role
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
            <div class="detail-value" style="font-weight: 500; color: #CBD5E1;">
                {{ $role->description ?? '—' }}
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Created At</div>
            <div class="detail-value" style="font-weight: 500; font-size: 13.5px; color: #CBD5E1;">{{ $role->created_at ? $role->created_at->format('d M Y, h:i A') : '—' }}</div>
        </div>
    </div>
</div>

{{-- Permissions Matrix (read-only) --}}
<div class="card-box">
    <div class="section-title">
        <i class="fa-solid fa-lock"></i> Assigned Permissions
    </div>

    @if($permissions->isEmpty())
        <p style="color: #94A3B8; font-size: 14px;">No permissions have been configured yet. <a href="{{ route('roles.permissions', $role->id) }}" style="color: #60A5FA; font-weight:700;">Assign permissions</a>.</p>
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
