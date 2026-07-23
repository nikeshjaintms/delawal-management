@extends('admin.layouts.app')

@section('title', 'Assign Permissions — ' . ($role->role_name ?? $role->name))
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
    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        box-shadow: var(--soft-shadow);
        margin-bottom: 24px;
    }
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
    /* ── Select All bar ── */
    .select-all-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: var(--blue-light);
        border-radius: 8px;
        margin-bottom: 18px;
        border: 1px solid rgba(59,130,246,0.15);
    }
    .select-all-bar label {
        font-size: 13.5px;
        font-weight: 600;
        color: #1E40AF;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .select-all-bar input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: var(--blue);
    }
    .perm-count {
        margin-left: auto;
        font-size: 12.5px;
        color: #3B82F6;
        font-weight: 600;
    }
    /* ── Scrollable table wrapper ── */
    .perm-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }
    .perm-matrix {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 640px;
    }
    /* Sticky header row */
    .perm-matrix thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        padding: 12px 14px;
        background: #F8FAFC;
        color: #475569;
        font-weight: 700;
        border-bottom: 2px solid var(--border-color);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        text-align: center;
        white-space: nowrap;
    }
    .perm-matrix thead th:first-child {
        text-align: left;
        position: sticky;
        left: 0;
        z-index: 6;
        background: #F8FAFC;
        min-width: 200px;
    }
    /* Sticky first column */
    .perm-matrix tbody td:first-child {
        position: sticky;
        left: 0;
        background: #FFFFFF;
        z-index: 2;
        font-weight: 600;
        color: var(--text-primary);
        border-right: 1px solid var(--border-color);
    }
    .perm-matrix tbody tr:hover td:first-child { background: #F0F7FF; }
    .perm-matrix td {
        padding: 11px 14px;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
        text-align: center;
    }
    .perm-matrix tbody tr:last-child td { border-bottom: none; }
    .perm-matrix tbody tr:hover { background: #F0F7FF; }
    /* Column-header checkboxes */
    .col-all-check { cursor: pointer; accent-color: #8B5CF6; width: 14px; height: 14px; }
    /* Row checkboxes */
    .perm-matrix input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: var(--blue);
    }
    /* Row select-all in first cell of each data row */
    .row-select-all {
        width: 14px;
        height: 14px;
        cursor: pointer;
        accent-color: #10B981;
        margin-right: 8px;
        vertical-align: middle;
    }
    /* Action buttons */
    .form-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }
    .btn-gold {
        background-color: var(--gold);
        color: #FFFFFF;
        padding: 11px 28px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: 0 4px 10px rgba(212, 175, 55, 0.2);
    }
    .btn-gold:hover { background-color: #B58D1B; transform: translateY(-1px); }
    .btn-outline {
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-secondary);
        padding: 11px 24px;
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
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--blue-light);
        color: #1E40AF;
        font-size: 13px;
        font-weight: 700;
        border-radius: 8px;
        padding: 5px 14px;
        margin-bottom: 6px;
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
                                        <span style="color:#E2E8F0; font-size:11px;">—</span>
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
