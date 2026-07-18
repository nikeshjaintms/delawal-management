@extends('admin.layouts.app')

@section('title', 'Audit Logs')
@section('page-title', 'System Logs')

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
        margin-bottom: 20px;
    }

    /* Filters section */
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-label {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-control {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid var(--border-color);
        border-radius: 8px;
        font-size: 13.5px;
        font-family: var(--font-primary);
        color: var(--text-primary);
        outline: none;
        background-color: #FFF;
        transition: var(--transition);
    }

    .filter-control:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px var(--blue-glow);
    }

    .filter-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 15px;
        flex-wrap: wrap;
    }

    .btn-search {
        background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
        color: #FFFFFF;
        padding: 10px 22px;
        border-radius: 8px;
        border: none;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 8px rgba(59,130,246,0.3);
        transition: var(--transition);
    }

    .btn-search:hover {
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59,130,246,0.4);
    }

    .btn-reset {
        padding: 10px 18px;
        border: 1px solid var(--border-color);
        background: #FFF;
        color: var(--text-secondary);
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: var(--transition);
    }

    .btn-reset:hover {
        color: var(--text-primary);
        border-color: #94A3B8;
        background: #F8FAFC;
    }

    /* Table styles */
    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .premium-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 13.5px;
    }

    .premium-table th {
        padding: 14px 16px;
        background: #F8FAFC;
        color: #475569;
        font-weight: 700;
        border-bottom: 2px solid var(--border-color);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        white-space: nowrap;
    }

    .premium-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #F1F5F9;
        color: var(--text-primary);
        vertical-align: middle;
    }

    .premium-table tr:last-child td {
        border-bottom: none;
    }

    .premium-table tbody tr {
        transition: background 0.15s ease;
    }

    .premium-table tbody tr:hover {
        background-color: #F0F7FF;
    }

    /* Badge actions */
    .badge-action {
        display: inline-block;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 700;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .badge-login {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .badge-logout {
        background: rgba(239, 68, 68, 0.1);
        color: #DC2626;
    }

    .badge-create {
        background: rgba(59, 130, 246, 0.1);
        color: #2563EB;
    }

    .badge-update {
        background: rgba(245, 158, 11, 0.1);
        color: #D97706;
    }

    .badge-delete {
        background: rgba(156, 163, 175, 0.1);
        color: #4B5563;
    }

    .badge-export {
        background: rgba(139, 92, 246, 0.1);
        color: #7C3AED;
    }

    .badge-print {
        background: rgba(14, 165, 233, 0.1);
        color: #0284C7;
    }

    .badge-download {
        background: rgba(20, 184, 166, 0.1);
        color: #0F766E;
    }

    .badge-backup {
        background: rgba(249, 115, 22, 0.1);
        color: #C2410C;
    }

    .badge-other {
        background: rgba(100, 116, 139, 0.1);
        color: #475569;
    }

    .ip-badge {
        font-family: monospace;
        background: #F1F5F9;
        color: #475569;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
    }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 40px;
        margin-bottom: 12px;
        opacity: 0.4;
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2><i class="fa-solid fa-clock-rotate-left" style="color: var(--blue); margin-right: 9px;"></i>Audit Logs</h2>
        <p>Monitor system usage, data changes, exports, and administrator logins.</p>
    </div>
</div>

{{-- Filters Card --}}
<div class="card-box">
    <form method="GET" action="{{ route('audit-logs.index') }}">
        <div class="filter-grid">
            <div class="filter-group">
                <span class="filter-label">Search Keywords</span>
                <input type="text" name="search" class="filter-control @error('search') is-invalid @enderror" placeholder="Search description, IP..." value="{{ request('search') }}">
            </div>

            <div class="filter-group">
                <span class="filter-label">From Date</span>
                <input type="date" name="from_date" class="filter-control @error('from_date') is-invalid @enderror" value="{{ request('from_date') }}">
            </div>

            <div class="filter-group">
                <span class="filter-label">To Date</span>
                <input type="date" name="to_date" class="filter-control @error('to_date') is-invalid @enderror" value="{{ request('to_date') }}">
            </div>

            <div class="filter-group">
                <span class="filter-label">User Name</span>
                <select name="user_name" class="filter-control @error('user_name') is-invalid @enderror">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user }}" {{ request('user_name') == $user ? 'selected' : '' }}>{{ $user }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <span class="filter-label">Module Name</span>
                <select name="module_name" class="filter-control @error('module_name') is-invalid @enderror">
                    <option value="">All Modules</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" {{ request('module_name') == $module ? 'selected' : '' }}>{{ $module }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <span class="filter-label">Action Type</span>
                <select name="action_type" class="filter-control @error('action_type') is-invalid @enderror">
                    <option value="">All Actions</option>
                    @foreach($actionTypes as $type)
                        <option value="{{ $type }}" {{ request('action_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="filter-actions">
            <a href="{{ route('audit-logs.index') }}" class="btn-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
            <button type="submit" class="btn-search">
                <i class="fa-solid fa-magnifying-glass"></i> Search Logs
            </button>
        </div>
    </form>
</div>

{{-- Table Card --}}
<div class="card-box">
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Sr. No.</th>
                    <th style="width: 170px;">Date &amp; Time</th>
                    <th style="width: 150px;">User Name</th>
                    <th style="width: 150px;">Module Name</th>
                    <th style="width: 140px;">Action Type</th>
                    <th>Description</th>
                    <th style="width: 130px;">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $startNo = $logs->firstItem() ?? 1;
                @endphp
                @forelse($logs as $index => $log)
                    <tr>
                        <td>{{ $startNo + $index }}</td>
                        <td style="white-space: nowrap;">
                            {{ $log->created_at->format('d M Y, h:i A') }}
                        </td>
                        <td>
                            <strong>{{ $log->user_name ?? 'System/Guest' }}</strong>
                        </td>
                        <td>
                            <span style="font-weight: 500;">{{ $log->module_name }}</span>
                        </td>
                        <td>
                            @php
                                $badgeClass = 'badge-other';
                                if ($log->action_type === 'Login')           $badgeClass = 'badge-login';
                                elseif ($log->action_type === 'Logout')          $badgeClass = 'badge-logout';
                                elseif ($log->action_type === 'Create Record')   $badgeClass = 'badge-create';
                                elseif ($log->action_type === 'Update Record')   $badgeClass = 'badge-update';
                                elseif ($log->action_type === 'Delete Record')   $badgeClass = 'badge-delete';
                                elseif ($log->action_type === 'Export PDF')      $badgeClass = 'badge-export';
                                elseif ($log->action_type === 'Export Excel')    $badgeClass = 'badge-export';
                                elseif ($log->action_type === 'Print')           $badgeClass = 'badge-print';
                                elseif ($log->action_type === 'Download Action') $badgeClass = 'badge-download';
                                elseif ($log->action_type === 'Backup Generate' || str_contains($log->action_type, 'Backup') || $log->module_name === 'Backup System') $badgeClass = 'badge-backup';
                            @endphp
                            <span class="badge-action {{ $badgeClass }}">{{ $log->action_type }}</span>
                        </td>
                        <td>
                            <span style="color: var(--text-secondary); word-break: break-all;">{{ $log->description }}</span>
                        </td>
                        <td>
                            <span class="ip-badge">{{ $log->ip_address ?? '-' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fa-solid fa-circle-info"></i>
                                <p>No audit logs found.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div style="margin-top: 20px;">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection

