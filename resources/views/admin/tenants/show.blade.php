@extends('admin.layouts.app')

@section('title', 'View Tenant')
@section('page-title', 'Tenant Master')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
    max-width: 820px; margin-left: auto; margin-right: auto;
}

/* Profile header */
.tenant-profile-header {
    display: flex; align-items: center; gap: 20px; padding-bottom: 24px; margin-bottom: 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); flex-wrap: wrap;
}

.tenant-avatar {
    width: 64px; height: 64px; border-radius: 50%;
    background: rgba(59, 130, 246, 0.20) !important; border: 2px solid #3B82F6 !important;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; font-weight: 800; color: #60A5FA !important; flex-shrink: 0;
}

.tenant-profile-info h3 { font-size: 22px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 4px; }
.tenant-profile-info p { font-size: 14px; color: #CBD5E1 !important; margin: 0; }

.tenant-badges { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; align-items: center; }

/* Details grid */
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 576px) { .detail-grid { grid-template-columns: 1fr; } }

.detail-item {
    padding: 16px 18px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1px solid rgba(255, 255, 255, 0.10) !important; border-radius: 14px; transition: all .2s ease;
}
.detail-item:hover { border-color: rgba(59, 130, 246, 0.35) !important; background: rgba(16, 22, 34, 0.85) !important; }

.detail-label {
    font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase;
    letter-spacing: 0.8px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;
}
.detail-label i { color: #60A5FA !important; font-size: 12px; }

.detail-value { font-size: 15px; font-weight: 700; color: #FFFFFF !important; word-break: break-word; }
.detail-value.empty { color: #64748B !important; font-weight: 400; font-style: italic; }

.detail-item-full { grid-column: 1 / -1; }

/* Badges */
.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.identity-chip { display: inline-flex; align-items: center; gap: 5px; background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; font-size: 12.5px; font-weight: 700; padding: 5px 12px; border-radius: 20px; border: 1px solid rgba(59, 130, 246, 0.30) !important; }

/* Document link */
.doc-link {
    display: inline-flex; align-items: center; gap: 6px; color: #60A5FA !important;
    font-size: 13.5px; font-weight: 700; text-decoration: none !important; padding: 7px 16px;
    border: 1px solid rgba(59, 130, 246, 0.40) !important; border-radius: 10px;
    background: rgba(59, 130, 246, 0.15) !important; transition: all .25s ease;
}
.doc-link:hover { background: rgba(59, 130, 246, 0.28) !important; transform: translateY(-2px); }

/* Meta info */
.meta-info { margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); display: flex; gap: 24px; flex-wrap: wrap; }
.meta-item { font-size: 12.5px; color: #CBD5E1 !important; display: flex; align-items: center; gap: 6px; font-weight: 500; }
.meta-item i { color: #60A5FA !important; }

.form-actions { display: flex; align-items: center; gap: 14px; margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 12px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all .25s ease;
    box-shadow: 0 4px 18px rgba(37,99,235,0.38); text-decoration: none !important;
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37,99,235,0.52); }

.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 22px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-2px); }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Tenant Details</h2>
        <p>Full profile view for firm-wise tenant record.</p>
    </div>
</div>

<div class="card-box">
    <!-- Tenant Profile Header -->
    <div class="tenant-profile-header">
        <div class="tenant-avatar">
            {{ strtoupper(substr($tenant->name, 0, 1)) }}
        </div>
        <div class="tenant-profile-info">
            <h3>{{ $tenant->name }}</h3>
            <p>{{ $tenant->email ?? $tenant->mobile }}</p>
            <div class="tenant-badges">
                <span class="badge badge-{{ $tenant->status }}">{{ ucfirst($tenant->status) }}</span>
                @if($tenant->identity_type)
                    <span class="identity-chip">
                        <i class="fa-solid fa-id-card" style="font-size:10px;"></i>
                        {{ $tenant->identity_type }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Detail Fields Grid -->
    <div class="detail-grid">
        <!-- Firm -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-building-user"></i>
                Firm
            </div>
            <div class="detail-value">{{ $tenant->firm->firm_name ?? 'Not set' }}</div>
        </div>

        <!-- Name -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-user"></i>
                Full Name
            </div>
            <div class="detail-value">{{ $tenant->name }}</div>
        </div>

        <!-- Mobile -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-phone"></i>
                Primary Mobile
            </div>
            <div class="detail-value">{{ $tenant->mobile }}</div>
        </div>

        <!-- Alternate Mobile -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-mobile-screen"></i>
                Alternate Mobile
            </div>
            <div class="detail-value {{ $tenant->alternate_mobile ? '' : 'empty' }}">{{ $tenant->alternate_mobile ?? 'Not provided' }}</div>
        </div>

        <!-- Email -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-envelope"></i>
                Email
            </div>
            @if($tenant->email)
                <div class="detail-value">{{ $tenant->email }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>

        <!-- Occupation -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-briefcase"></i>
                Occupation / Profession
            </div>
            <div class="detail-value {{ $tenant->occupation ? '' : 'empty' }}">{{ $tenant->occupation ?? 'Not provided' }}</div>
        </div>

        <!-- City -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-city"></i>
                City
            </div>
            @if($tenant->city)
                <div class="detail-value">{{ $tenant->city }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>

        <!-- Identity Type & Number -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-id-card"></i>
                Identity Info
            </div>
            @if($tenant->identity_type || $tenant->identity_number)
                <div class="detail-value">{{ $tenant->identity_type ?? 'ID' }}: {{ $tenant->identity_number ?? '—' }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>

        <!-- Emergency Contact -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-phone-volume"></i>
                Emergency Contact
            </div>
            <div class="detail-value {{ ($tenant->emergency_contact_name || $tenant->emergency_contact_mobile) ? '' : 'empty' }}">
                @if($tenant->emergency_contact_name || $tenant->emergency_contact_mobile)
                    {{ $tenant->emergency_contact_name ?? '—' }} ({{ $tenant->emergency_contact_mobile ?? '—' }})
                @else
                    Not provided
                @endif
            </div>
        </div>

        <!-- Document -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-file-lines"></i>
                Document
            </div>
            @if($tenant->document_file)
                <div class="detail-value">
                    <a href="{{ asset('storage/' . $tenant->document_file) }}" target="_blank" class="doc-link">
                        <i class="fa-solid fa-file-arrow-down"></i> View Document
                    </a>
                </div>
            @else
                <div class="detail-value empty">No document uploaded</div>
            @endif
        </div>

        <!-- Current Address -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-location-dot"></i>
                Current Address
            </div>
            <div class="detail-value {{ $tenant->address ? '' : 'empty' }}">{{ $tenant->address ?? 'Not provided' }}</div>
        </div>

        <!-- Permanent Address -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fa-solid fa-house-chimney-user"></i>
                Permanent Address
            </div>
            <div class="detail-value {{ $tenant->permanent_address ? '' : 'empty' }}">{{ $tenant->permanent_address ?? 'Not provided' }}</div>
        </div>
    </div>

    <!-- Meta info -->
    <div class="meta-info">
        <div class="meta-item">
            <i class="fa-regular fa-calendar-plus"></i>
            <span>Created: {{ $tenant->created_at->format('d M Y, h:i A') }}</span>
        </div>
        <div class="meta-item">
            <i class="fa-regular fa-calendar-check"></i>
            <span>Last Updated: {{ $tenant->updated_at->format('d M Y, h:i A') }}</span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('tenants.edit', $tenant->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit Tenant
        </a>
        <a href="{{ route('tenants.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection
