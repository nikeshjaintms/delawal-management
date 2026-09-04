@extends('admin.layouts.app')

@section('title', 'Assign Permissions — ' . ($role->role_name ?? $role->name))
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
.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(139, 92, 246, 0.18) !important;
    border: 1px solid rgba(139, 92, 246, 0.35) !important;
    color: #C4B5FD !important;
    font-size: 13px;
    font-weight: 800;
    border-radius: 8px;
    padding: 5px 14px;
    margin-bottom: 8px;
}

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

/* ── Select All bar ── */
.select-all-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    background: rgba(59, 130, 246, 0.12) !important;
    border-radius: 14px;
    margin-bottom: 20px;
    border: 1px solid rgba(59, 130, 246, 0.28);
}
.select-all-bar label {
    font-size: 14px;
    font-weight: 700;
    color: #93C5FD !important;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
}
.select-all-bar input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #2563EB;
}
.perm-count {
    margin-left: auto;
    font-size: 13px;
    color: #60A5FA;
    font-weight: 800;
    background: rgba(59, 130, 246, 0.15);
    padding: 4px 12px;
    border-radius: 20px;
    border: 1px solid rgba(59, 130, 246, 0.30);
}

/* ── Scrollable table wrapper ── */
.perm-table-wrapper {
    width: 100%;
    overflow-x: auto;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.10);
    background: rgba(16, 22, 34, 0.50);
}
.perm-matrix {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
    min-width: 640px;
}
/* Sticky header row */
.perm-matrix thead th {
    position: sticky;
    top: 0;
    z-index: 5;
    padding: 14px 16px;
    background: #141B29 !important;
    color: #94A3B8 !important;
    font-weight: 800;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.12) !important;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    text-align: center;
    white-space: nowrap;
}
.perm-matrix thead th:first-child {
    text-align: left;
    position: sticky;
    left: 0;
    z-index: 6;
    background: #141B29 !important;
    min-width: 220px;
}
/* Sticky first column */
.perm-matrix tbody td:first-child {
    position: sticky;
    left: 0;
    background: #101622 !important;
    z-index: 2;
    font-weight: 700;
    color: #FFFFFF !important;
    border-right: 1px solid rgba(255, 255, 255, 0.08);
}
.perm-matrix tbody tr:hover td:first-child { background: #182236 !important; }
.perm-matrix td {
    padding: 12px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    vertical-align: middle;
    text-align: center;
    color: #E2E8F0;
}
.perm-matrix tbody tr:last-child td { border-bottom: none !important; }
.perm-matrix tbody tr:hover { background: rgba(255, 255, 255, 0.04); }

/* Column-header checkboxes */
.col-all-check { cursor: pointer; accent-color: #8B5CF6; width: 16px; height: 16px; }
/* Row checkboxes */
.perm-matrix input[type="checkbox"] {
    width: 17px;
    height: 17px;
    cursor: pointer;
    accent-color: #2563EB;
}
/* Row select-all in first cell of each data row */
.row-select-all {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: #10B981;
    margin-right: 10px;
    vertical-align: middle;
}

/* Action buttons */
.form-actions {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-top: 28px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.10);
    flex-wrap: wrap;
}
.btn-gold {
    background: #2563EB !important;
    color: #FFFFFF !important;
    padding: 11px 26px;
    border-radius: 10px;
    text-decoration: none !important;
    font-size: 14px;
    font-weight: 700;
    border: 1px solid #3B82F6 !important;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all .25s ease;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35);
}
.btn-gold:hover {
    background: #1D4ED8 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 22px rgba(37, 99, 235, 0.50);
}
.btn-outline {
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important;
    padding: 10px 22px;
    border-radius: 10px;
    text-decoration: none !important;
    font-size: 13.5px;
    font-weight: 600;
    transition: all .25s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}
.btn-outline:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    color: #FFFFFF !important;
    transform: translateY(-2px);
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <div class="role-badge">
            <i class="fa-solid fa-shield-halved"></i>
            {{ $role->role_name ?? $role->name }}
        </div>
        <h2>Assign Permissions</h2>
        <p>Check the permissions you want to grant to this role. Each column is an action type.</p>
    </div>
    <a href="{{ route('roles.index') }}" class="btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Back to Roles
    </a>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<form method="POST" action="{{ route('roles.permissions.update', $role->id) }}" id="permissionsForm">
    @csrf
    <div class="card-box">

        {{-- Select All bar --}}
        <div class="select-all-bar">
            <label for="selectAll">
                <input type="checkbox" id="selectAll">
                Select / Deselect All Permissions
            </label>
            <span class="perm-count" id="permCount">0 selected</span>
        </div>

        <div class="perm-table-wrapper">
            <table class="perm-matrix" id="permMatrix">
                <thead>
                    <tr>
                        <th>
                            Module
                        </th>
                        @foreach($actions as $action)
                            <th>
                                <div style="display:flex;flex-direction:column;align-items:center;gap:5px;">
                                    {{ ucfirst($action) }}
                                    <input type="checkbox" class="col-all-check" data-action="{{ $action }}" title="Toggle all {{ $action }}">
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $moduleName => $modulePerms)
                        @php
                            $modulePermMap = $modulePerms->keyBy('action');
                        @endphp
                        <tr>
                            <td>
                                <input type="checkbox" class="row-select-all" data-module="{{ $loop->index }}" title="Toggle all for {{ $moduleName }}">
                                {{ $moduleName }}
                            </td>
                            @foreach($actions as $action)
                                @php
                                    $perm = $modulePermMap->get($action);
                                @endphp
                                <td>
                                    @if($perm)
                                        <input type="checkbox"
                                            name="permissions[]"
                                            value="{{ $perm->id }}"
                                            class="perm-cb row-{{ $loop->parent->index }} col-{{ $action }}"
                                            {{ in_array($perm->id, $assignedPermissionIds) ? 'checked' : '' }}>
                                    @else
                                        <span style="color:#64748B; font-size:12px;">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-floppy-disk"></i> Save Permissions
            </button>
            <a href="{{ route('roles.show', $role->id) }}" class="btn-outline">
                <i class="fa-regular fa-eye"></i> View Role
            </a>
            <a href="{{ route('roles.index') }}" class="btn-outline">
                Back
            </a>
        </div>
    </div>
</form>

<script>
(function () {
    const allCheckboxes = () => document.querySelectorAll('.perm-cb');
    const checkedCount  = () => document.querySelectorAll('.perm-cb:checked').length;

    function updateCount() {
        const total   = allCheckboxes().length;
        const checked = checkedCount();
        document.getElementById('permCount').textContent = checked + ' of ' + total + ' selected';

        // Sync master select-all
        const selectAll = document.getElementById('selectAll');
        selectAll.indeterminate = checked > 0 && checked < total;
        selectAll.checked = checked === total;
    }

    // Master select/deselect all
    document.getElementById('selectAll').addEventListener('change', function () {
        allCheckboxes().forEach(cb => cb.checked = this.checked);
        // Sync column headers
        document.querySelectorAll('.col-all-check').forEach(c => c.checked = this.checked);
        // Sync row checkboxes
        document.querySelectorAll('.row-select-all').forEach(r => r.checked = this.checked);
        updateCount();
    });

    // Column all-check (toggle whole column)
    document.querySelectorAll('.col-all-check').forEach(function (colCb) {
        colCb.addEventListener('change', function () {
            const action = this.dataset.action;
            document.querySelectorAll('.col-' + action).forEach(cb => cb.checked = colCb.checked);
            updateCount();
        });
    });

    // Row all-check (toggle whole row)
    document.querySelectorAll('.row-select-all').forEach(function (rowCb) {
        rowCb.addEventListener('change', function () {
            const idx = this.dataset.module;
            document.querySelectorAll('.row-' + idx).forEach(cb => cb.checked = rowCb.checked);
            updateCount();
        });
    });

    // Individual checkbox change — update count
    allCheckboxes().forEach(cb => cb.addEventListener('change', updateCount));

    // Initial count
    updateCount();

    // Sync column headers on load
    @foreach($actions as $action)
    (function () {
        const action = '{{ $action }}';
        const colCbs = document.querySelectorAll('.col-' + action);
        const allChecked = Array.from(colCbs).every(c => c.checked);
        const colHeader = document.querySelector('.col-all-check[data-action="' + action + '"]');
        if (colHeader) colHeader.checked = allChecked && colCbs.length > 0;
    })();
    @endforeach

    // Sync row headers on load
    document.querySelectorAll('.row-select-all').forEach(function (rowCb) {
        const idx = rowCb.dataset.module;
        const rowCbs = document.querySelectorAll('.row-' + idx);
        const allChecked = Array.from(rowCbs).every(c => c.checked);
        rowCb.checked = allChecked && rowCbs.length > 0;
    });
})();
</script>
@endsection
