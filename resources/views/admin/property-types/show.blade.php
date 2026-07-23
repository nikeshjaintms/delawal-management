@extends('admin.layouts.app')

@section('title', 'View Property Type')
@section('page-title', 'Property Type Master')

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
        padding: 30px;
        box-shadow: var(--soft-shadow);
        max-width: 800px;
        margin: 0 auto;
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 20px;
        padding-bottom: 24px;
        margin-bottom: 24px;
        border-bottom: 1px solid var(--border-color);
        flex-wrap: wrap;
    }

    .type-avatar {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        background: var(--gold-light);
        border: 2px solid var(--gold);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 700;
        color: var(--gold);
        flex-shrink: 0;
    }

    .profile-info h3 {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .profile-info p {
        font-size: 13.5px;
        color: var(--text-secondary);
    }

    .profile-badges {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 576px) {
        .detail-grid { grid-template-columns: 1fr; }
    }

    .detail-item {
        padding: 16px;
        background: #F9FAFB;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        transition: var(--transition);
    }

    .detail-item:hover {
        border-color: rgba(212, 175, 55, 0.2);
        box-shadow: 0 4px 12px rgba(15, 31, 53, 0.04);
        background: #FFFFFF;
    }

    .detail-label {
        font-size: 11px;
        font-weight: 700;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .detail-label i {
        color: var(--gold);
        font-size: 12px;
    }

    .detail-value {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-primary);
        word-break: break-word;
    }

    .detail-value.empty {
        color: #9CA3AF;
        font-weight: 400;
        font-style: italic;
    }

    .detail-item-full {
        grid-column: 1 / -1;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .badge-active   { background: rgba(34, 197, 94, 0.1); color: #16803D; }
    .badge-inactive { background: rgba(239, 68, 68, 0.1);  color: #B91C1C; }

    .meta-info {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
    }

    .meta-item {
        font-size: 12px;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .meta-item i { color: var(--gold); }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 28px;
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

    .btn-outline {
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-secondary);
        padding: 11px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
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
        <h2>Property Type Details</h2>
        <p>Full details of this firm-wise property category.</p>
    </div>
</div>

<div class="card-box">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="type-avatar">
            {{ strtoupper(substr($propertyType->name, 0, 1)) }}
        </div>
        <div class="profile-info">
            <h3>{{ $propertyType->name }}</h3>
            <p>Property Category</p>
            <div class="profile-badges">
                <span class="badge badge-{{ $propertyType->status }}">{{ ucfirst($propertyType->status) }}</span>
            </div>
        </div>
    </div>

    <!-- Detail Grid -->
    <div class="detail-grid">

        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-building"></i>
                Assigned Firms
            </div>
            <div class="detail-value">{{ $propertyType->firm_names }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-tag"></i>
                Name
            </div>
            <div class="detail-value">{{ $propertyType->name }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-circle-dot"></i>
                Status
            </div>
            <div class="detail-value">
                <span class="badge badge-{{ $propertyType->status }}">{{ ucfirst($propertyType->status) }}</span>
            </div>
        </div>

        <div class="detail-item detail-item-full">
            <div class="detail-label">
                <i class="fa-solid fa-align-left"></i>
                Description
            </div>
            @if($propertyType->description)
                <div class="detail-value" style="font-weight: 400; font-size: 14px; line-height: 1.6;">
                    {{ $propertyType->description }}
                </div>
            @else
                <div class="detail-value empty">No description provided</div>
            @endif
        </div>

    </div>

    <!-- Meta Info -->
    <div class="meta-info">
        <div class="meta-item">
            <i class="fa-regular fa-calendar-plus"></i>
            <span>Created: {{ $propertyType->created_at->format('d M Y, h:i A') }}</span>
        </div>
        <div class="meta-item">
            <i class="fa-regular fa-calendar-check"></i>
            <span>Last Updated: {{ $propertyType->updated_at->format('d M Y, h:i A') }}</span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('property-types.edit', $propertyType->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit Property Type
        </a>
        <a href="{{ route('property-types.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection
