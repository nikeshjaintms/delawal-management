@extends('admin.layouts.app')

@section('title', 'View Rental')
@section('page-title', 'Rental Management')

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
    max-width: 920px; margin-left: auto; margin-right: auto;
}

.rental-hero {
    display: flex; align-items: center; gap: 20px; padding-bottom: 24px; margin-bottom: 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); flex-wrap: wrap;
}
.rental-icon {
    width: 64px; height: 64px; border-radius: 16px;
    background: rgba(59, 130, 246, 0.20) !important; border: 2px solid #3B82F6 !important;
    display: flex; align-items: center; justify-content: center; font-size: 26px; color: #60A5FA !important; flex-shrink: 0;
}
.rental-hero-info h3 { font-size: 22px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 5px; }
.rental-hero-info p { font-size: 14px; color: #CBD5E1 !important; margin-bottom: 8px; }
.hero-badges { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.section-title {
    font-size: 12px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 16px; margin-top: 24px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.detail-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
@media(max-width:768px){ .detail-grid-3{ grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .detail-grid, .detail-grid-3{ grid-template-columns: 1fr; } }

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

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-pending   { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-partial   { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.badge-paid      { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-active    { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-completed { background: rgba(148, 163, 184, 0.18) !important; color: #CBD5E1 !important; border: 1px solid rgba(148, 163, 184, 0.35) !important; }
.badge-cancelled { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.amount-big { font-size: 18px; font-weight: 800; }
.due-chip { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.35) !important; display: inline-block; }

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
        <h2>Rental Details</h2>
        <p>Full record of this firm-wise rental agreement.</p>
    </div>
</div>

<div class="card-box">
    {{-- Hero --}}
    <div class="rental-hero">
        <div class="rental-icon"><i class="fa-solid fa-key"></i></div>
        <div class="rental-hero-info">
            <h3>{{ $rental->tenant_name }}</h3>
            <p>
                {{ $rental->property->property_name ?? '' }}
                @if($rental->property?->property_code)
                    <span style="color:var(--gold);font-weight:600;"> ({{ $rental->property->property_code }})</span>
                @endif
                @if($rental->property?->unit_no)
                    &nbsp;·&nbsp; Unit {{ $rental->property->unit_no }}
                @endif
            </p>
            <div class="hero-badges">
                <span class="badge badge-{{ $rental->rental_status }}">{{ ucfirst($rental->rental_status) }}</span>
                <span class="badge badge-{{ $rental->payment_status }}">{{ ucfirst($rental->payment_status) }}</span>
                <span style="font-size:15px;font-weight:800;color:var(--text-primary);">
                    ₹{{ number_format($rental->rent_amount, 0) }}<span style="font-size:12px;font-weight:400;color:var(--text-secondary);">/mo</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Property --}}
    <div class="section-title"><i class="fa-solid fa-building"></i> Property & Firm Details</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building-user"></i> Firm</div>
            <div class="detail-value">{{ $rental->firm->firm_name ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building"></i> Property Name</div>
            <div class="detail-value">
                {{ $rental->property->property_name ?? '-' }}
                @if($rental->property?->property_code)
                    <span style="color:var(--gold);font-size:13px;"> ({{ $rental->property->property_code }})</span>
                @endif
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-layer-group"></i> Property Type</div>
            @if($rental->property?->propertyType)
                <div class="detail-value">{{ $rental->property->propertyType->name }}</div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-door-open"></i> Unit No</div>
            @if($rental->property?->unit_no)
                <div class="detail-value">{{ $rental->property->unit_no }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-city"></i> City</div>
            @if($rental->property?->city)
                <div class="detail-value">{{ $rental->property->city }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
    </div>

    {{-- Tenant --}}
    <div class="section-title"><i class="fa-solid fa-user"></i> Tenant Information</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user"></i> Tenant Name</div>
            <div class="detail-value">{{ $rental->tenant_name }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-phone"></i> Mobile</div>
            <div class="detail-value">{{ $rental->tenant_mobile }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-envelope"></i> Email</div>
            @if($rental->tenant_email)
                <div class="detail-value">{{ $rental->tenant_email }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
    </div>

    {{-- Amounts --}}
    <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Rent & Deposit</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-indian-rupee-sign"></i> Monthly Rent</div>
            <div class="detail-value amount-big" style="color:var(--gold);">₹{{ number_format($rental->rent_amount, 2) }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-shield-halved"></i> Security Deposit</div>
            @if($rental->security_deposit)
                <div class="detail-value amount-big">₹{{ number_format($rental->security_deposit, 2) }}</div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
    </div>

    {{-- Dates --}}
    <div class="section-title"><i class="fa-solid fa-calendar-days"></i> Rental Period</div>
    <div class="detail-grid-3">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar-plus"></i> Start Date</div>
            <div class="detail-value">{{ \Carbon\Carbon::parse($rental->rent_start_date)->format('d M Y') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar-minus"></i> End Date</div>
            @if($rental->rent_end_date)
                <div class="detail-value">{{ \Carbon\Carbon::parse($rental->rent_end_date)->format('d M Y') }}</div>
            @else
                <div class="detail-value empty">Open-ended</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-clock"></i> Rent Due Day</div>
            @if($rental->rent_due_date)
                <div class="detail-value"><span class="due-chip">Day {{ $rental->rent_due_date }} of month</span></div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
    </div>

    {{-- Status --}}
    <div class="section-title"><i class="fa-solid fa-circle-dot"></i> Status</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-credit-card"></i> Payment Status</div>
            <div class="detail-value"><span class="badge badge-{{ $rental->payment_status }}">{{ ucfirst($rental->payment_status) }}</span></div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-key"></i> Rental Status</div>
            <div class="detail-value"><span class="badge badge-{{ $rental->rental_status }}">{{ ucfirst($rental->rental_status) }}</span></div>
        </div>
    </div>

    @if($rental->remarks)
        <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Remarks</div>
        <div class="detail-item">
            <div class="detail-value" style="font-weight:400;font-size:14px;line-height:1.7;">{{ $rental->remarks }}</div>
        </div>
    @endif

    <div class="meta-info">
        <div class="meta-item"><i class="fa-regular fa-calendar-plus"></i><span>Created: {{ $rental->created_at->format('d M Y, h:i A') }}</span></div>
        <div class="meta-item"><i class="fa-regular fa-calendar-check"></i><span>Updated: {{ $rental->updated_at->format('d M Y, h:i A') }}</span></div>
    </div>

    <div class="form-actions">
        <a href="{{ route('rentals.edit', $rental->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit Rental
        </a>
        <a href="{{ route('rentals.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection
