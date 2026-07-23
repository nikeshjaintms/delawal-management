@extends('admin.layouts.app')

@section('title', 'Backup System')
@section('page-title', 'System Settings')

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
        background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
        color: #FFF;
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
        box-shadow: 0 2px 8px rgba(59,130,246,0.3);
    }

    .btn-gold:hover {
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59,130,246,0.4);
    }

    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        box-shadow: var(--soft-shadow);
        margin-bottom: 20px;
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

    /* Actions buttons */
    .action-links {
        display: flex;
        gap: 12px;
    }

    .btn-action-download {
        color: var(--blue);
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-action-download:hover {
        text-decoration: underline;
    }

    .btn-action-delete {
        color: #EF4444;
        background: none;
        border: none;
        cursor: pointer;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 0;
        font-family: inherit;
    }

    .btn-action-delete:hover {
        text-decoration: underline;
    }

    /* Alerts */
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13.5px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background-color: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: #065F46;
    }

    .alert-danger {
        background-color: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #991B1B;
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
        <h2><i class="fa-solid fa-database" style="color: var(--blue); margin-right: 9px;"></i>Database Backups</h2>
        <p>Generate, download, and manage system database backups safely.</p>
    </div>
    <div>
        <form method="POST" action="{{ route('backups.generate') }}">
            @csrf
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-plus-circle"></i> Generate Backup
            </button>
        </form>
    </div>
</div>

{{-- Messages --}}
@if(session('success'))
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-xmark"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

{{-- Table Card --}}
<div class="card-box">
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Sr. No.</th>
                    <th>Backup Date &amp; Time</th>
                    <th>Backup File Name</th>
                    <th>File Size</th>
                    <th>Created By</th>
                    <th style="width: 180px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $startNo = $backups->firstItem() ?? 1;
                @endphp
                @forelse($backups as $index => $backup)
                    <tr>
                        <td>{{ $startNo + $index }}</td>
                        <td>
                            {{ $backup->created_at->format('d M Y, h:i A') }}
                        </td>
                        <td>
                            <strong>{{ $backup->file_name }}</strong>
                        </td>
                        <td>
                            {{ $backup->file_size }}
                        </td>
                        <td>
                            {{ $backup->user_name ?? 'System/Guest' }}
                        </td>
                        <td>
                            <div class="table-action-buttons">
                                <a href="{{ route('backups.download', $backup->id) }}" class="btn-action-download">
                                    <i class="fa-solid fa-download"></i> Download
                                </a>
                                
                                <form action="{{ route('backups.destroy', $backup->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this backup file? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-delete">
                                        <i class="fa-solid fa-trash-can"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fa-solid fa-folder-open"></i>
                                <p>No backup records found. Click the "Generate Backup" button to create one.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($backups->hasPages())
        <div style="margin-top: 20px;">
            {{ $backups->links() }}
        </div>
    @endif
</div>
@endsection

