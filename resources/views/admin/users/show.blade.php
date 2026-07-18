@extends('admin.layouts.app')

@section('title', 'View User')
@section('page-title', 'User Management')

@section('content')
<style>
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
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
        padding: 30px;
        box-shadow: var(--soft-shadow);
        max-width: 800px;
        margin: 0 auto;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 30px;
    }

    @media (max-width: 576px) {
        .detail-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
    }

    .detail-item {
        border-bottom: 1px solid #F1F5F9;
        padding-bottom: 12px;
    }

    .detail-label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .detail-value {
        font-size: 15px;
        font-weight: 700;
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

    .badge-active {
        background: rgba(34, 197, 94, 0.1);
        color: #16803D;
    }

    .badge-inactive {
        background: rgba(239, 68, 68, 0.1);
        color: #B91C1C;
    }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    .btn-gold {
        background-color: var(--gold);
        color: #FFFFFF;
        padding: 11px 24px;
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

    .btn-gold:hover {
        background-color: #B58D1B;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(212, 175, 55, 0.3);
    }

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
    }

    .btn-outline:hover {
        background: #F9FAFB;
        color: var(--text-primary);
        border-color: #D1D5DB;
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>User Details</h2>
        <p>Review system settings and profiles for this user account.</p>
    </div>
</div>

<div class="card-box">
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">Full Name</div>
            <div class="detail-value">{{ $user->name }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Email Address</div>
            <div class="detail-value">{{ $user->email }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Mobile Number</div>
            <div class="detail-value">{{ $user->mobile_number ?? '-' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Status</div>
            <div class="detail-value">
                <span class="badge badge-{{ $user->status }}">
                    {{ ucfirst($user->status) }}
                </span>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">User Role</div>
            <div class="detail-value">{{ is_object($user->role) ? ($user->role->role_name ?? $user->role->name) : ucfirst($user->role ?? '-') }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Belongs to Firm</div>
            <div class="detail-value">{{ $user->firm->firm_name ?? '-' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Created At</div>
            <div class="detail-value">{{ $user->created_at ? $user->created_at->format('d M Y, h:i A') : '-' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Last Updated</div>
            <div class="detail-value">{{ $user->updated_at ? $user->updated_at->format('d M Y, h:i A') : '-' }}</div>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('users.edit', $user->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit User
        </a>
        <a href="{{ route('users.index') }}" class="btn-outline">
            Back to User Management
        </a>
    </div>
</div>
@endsection
