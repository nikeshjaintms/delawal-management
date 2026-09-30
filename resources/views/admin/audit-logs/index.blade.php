@extends('admin.layouts.app')

@section('title', 'Audit Logs')
@section('page-title', 'System Logs')

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
    display: flex;
    align-items: center;
    gap: 10px;
}

.crud-title p {
    font-size: 14px;
    color: #CBD5E1 !important;
    font-weight: 500;
    margin: 0;
}

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 24px;
}

/* ── Filters Grid ── */
.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 18px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.filter-label {
    font-size: 11px;
    font-weight: 800;
    color: #94A3B8 !important;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

.filter-control {
    width: 100%;
    padding: 11px 16px;
    background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important;
    font-size: 13.5px;
    color: #FFFFFF !important;
    outline: none;
    transition: all .2s ease;
    box-sizing: border-box;
}

.filter-control::placeholder {
    color: #94A3B8 !important;
}

.filter-control option {
    background: #101622 !important;
    color: #FFFFFF !important;
}

.filter-control:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}

.filter-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.btn-search {
    background: #2563EB !important;
    color: #FFFFFF !important;
    padding: 11px 22px;
    border-radius: 10px;
    border: 1px solid #3B82F6 !important;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all .25s ease;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}

.btn-search:hover {
    background: #1D4ED8 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50);
}

.btn-reset {
    padding: 10px 18px;
    border: 1px solid rgba(255, 255, 255, 0.20);
    background: rgba(255, 255, 255, 0.08);
    color: #CBD5E1 !important;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all .2s ease;
}

.btn-reset:hover {
    background: rgba(255, 255, 255, 0.16);
    color: #FFFFFF !important;
    border-color: rgba(255, 255, 255, 0.35);
    transform: translateY(-1px);
}

/* ── Table Styling ── */
.table-container {
    width: 100%;
    overflow-x: auto;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.10);
    background: rgba(10, 15, 26, 0.40);
}

.premium-table {
    width: 100%;
    min-width: 980px;
    border-collapse: collapse;
    text-align: left;
    font-size: 13.5px;
}

.premium-table th {
    padding: 16px 20px !important;
    background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important;
    font-weight: 800;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.9px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}

.premium-table td {
    padding: 16px 20px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px;
    color: #E2E8F0 !important;
    vertical-align: middle;
}

.premium-table tbody tr {
    transition: background 0.18s ease;
}

.premium-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.04) !important;
}

.premium-table tr:last-child td {
    border-bottom: none;
}

/* ── Action Badges ── */
.badge-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 4px 12px;
    font-size: 11px;
    font-weight: 800;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    white-space: nowrap;
}

.badge-login {
    background: rgba(16, 185, 129, 0.18) !important;
    color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.40) !important;
}

.badge-logout {
    background: rgba(239, 68, 68, 0.18) !important;
    color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.40) !important;
}

.badge-create {
    background: rgba(59, 130, 246, 0.18) !important;
    color: #60A5FA !important;
    border: 1px solid rgba(59, 130, 246, 0.40) !important;
}

.badge-update {
    background: rgba(245, 158, 11, 0.18) !important;
    color: #FBBF24 !important;
    border: 1px solid rgba(245, 158, 11, 0.40) !important;
}

.badge-delete {
    background: rgba(239, 68, 68, 0.18) !important;
    color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.40) !important;
}

.badge-export {
    background: rgba(168, 85, 247, 0.18) !important;
    color: #D8B4FE !important;
    border: 1px solid rgba(168, 85, 247, 0.40) !important;
}

.badge-print {
    background: rgba(14, 165, 233, 0.18) !important;
    color: #38BDF8 !important;
    border: 1px solid rgba(14, 165, 233, 0.40) !important;
}

.badge-download {
    background: rgba(20, 184, 166, 0.18) !important;
    color: #2DD4BF !important;
    border: 1px solid rgba(20, 184, 166, 0.40) !important;
}

.badge-backup {
    background: rgba(249, 115, 22, 0.18) !important;
    color: #FB923C !important;
    border: 1px solid rgba(249, 115, 22, 0.40) !important;
}

.badge-other {
    background: rgba(148, 163, 184, 0.15) !important;
    color: #CBD5E1 !important;
    border: 1px solid rgba(148, 163, 184, 0.30) !important;
}

/* ── Module & IP Badges ── */
.module-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #FFFFFF !important;
    font-weight: 600;
    font-size: 13px;
    white-space: nowrap;
}

.ip-badge {
    font-family: 'JetBrains Mono', 'Fira Code', 'Courier New', monospace;
    background: rgba(30, 41, 59, 0.85) !important;
    color: #93C5FD !important;
    border: 1px solid rgba(96, 165, 250, 0.35) !important;
    padding: 4px 11px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
    letter-spacing: 0.3px;
    white-space: nowrap;
}

.desc-box {
    color: #CBD5E1;
    font-size: 13px;
    line-height: 1.5;
    word-break: break-word;
    max-width: 480px;
}

.empty-state {
    text-align: center;
    padding: 48px 24px;
    color: #94A3B8;
}

.empty-state i {
    font-size: 38px;
    margin-bottom: 12px;
    opacity: 0.35;
    color: #60A5FA;
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2><i class="fa-solid fa-clock-rotate-left" style="color: #60A5FA;"></i> Audit Logs</h2>
        <p>Monitor system usage, data changes, exports, and administrator activity.</p>
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
                    <th style="width: 70px; text-align: center;">Sr. No.</th>
                    <th style="width: 175px;">Date &amp; Time</th>
                    <th style="width: 155px;">User Name</th>
                    <th style="width: 155px;">Module Name</th>
                    <th style="width: 140px; text-align: center;">Action Type</th>
                    <th>Description</th>
                    <th style="width: 150px; text-align: center;">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $startNo = method_exists($logs, 'firstItem') ? ($logs->firstItem() ?? 1) : 1;
                @endphp
                @forelse($logs as $index => $log)
                    <tr>
                        <td style="text-align: center; color: #94A3B8; font-weight: 700;">
                            {{ $startNo + $index }}
                        </td>
                        <td style="white-space: nowrap;">
                            <span style="display: inline-flex; align-items: center; gap: 6px; color: #CBD5E1;">
                                <i class="fa-regular fa-clock" style="color: #60A5FA; font-size: 12px;"></i>
                                {{ $log->created_at->format('d M Y, h:i A') }}
                            </span>
                        </td>
                        <td>
                            <div style="display: inline-flex; align-items: center; gap: 7px;">
                                <i class="fa-solid fa-circle-user" style="color: #60A5FA; font-size: 14px;"></i>
                                <strong style="color: #FFFFFF !important; font-weight: 700;">{{ $log->user_name ?? 'System/Guest' }}</strong>
                            </div>
                        </td>
                        <td>
                            <span class="module-chip">
                                <i class="fa-solid fa-layer-group" style="font-size: 11px; color: #A78BFA;"></i>
                                {{ $log->module_name }}
                            </span>
                        </td>
                        <td style="text-align: center;">
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
                            <div class="desc-box">{{ $log->description }}</div>
                        </td>
                        <td style="text-align: center;">
                            <span class="ip-badge">
                                <i class="fa-solid fa-network-wired"></i>
                                {{ $log->ip_address ?: '—' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fa-solid fa-circle-info"></i>
                                <p style="font-size: 14px; font-weight: 600; color: #CBD5E1; margin-top: 6px;">No audit logs found.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($logs, 'hasPages') && $logs->hasPages())
        <div style="margin-top: 24px; display: flex; justify-content: center;">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
