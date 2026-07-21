@extends('admin.layouts.app')

@section('title', 'View Rental')
@section('page-title', 'Rental Management')

@section('content')
<style>
    .crud-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:15px; }
    .crud-title h2 { font-size:22px; font-weight:700; color:var(--text-primary); margin-bottom:4px; }
    .crud-title p  { font-size:13.5px; color:var(--text-secondary); }
    .card-box { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:30px; box-shadow:var(--soft-shadow); max-width:900px; margin:0 auto; }
    .rental-hero { display:flex; align-items:center; gap:20px; padding-bottom:24px; margin-bottom:24px; border-bottom:1px solid var(--border-color); flex-wrap:wrap; }
    .rental-icon { width:64px; height:64px; border-radius:12px; background:var(--gold-light); border:2px solid var(--gold); display:flex; align-items:center; justify-content:center; font-size:26px; color:var(--gold); flex-shrink:0; }
    .rental-hero-info h3 { font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:5px; }
    .rental-hero-info p  { font-size:13.5px; color:var(--text-secondary); margin-bottom:8px; }
    .hero-badges { display:flex; gap:10px; flex-wrap:wrap; }
    .section-title { font-size:12px; font-weight:700; color:var(--gold); text-transform:uppercase; letter-spacing:1px; margin-bottom:14px; margin-top:22px; padding-bottom:8px; border-bottom:1px solid var(--border-color); }
    .detail-grid   { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .detail-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
    @media(max-width:768px){ .detail-grid-3{ grid-template-columns:1fr 1fr; } }
    @media(max-width:576px){ .detail-grid,.detail-grid-3{ grid-template-columns:1fr; } }
    .detail-item { padding:14px 16px; background:#F9FAFB; border:1px solid var(--border-color); border-radius:10px; transition:var(--transition); }
    .detail-item:hover { border-color:rgba(212,175,55,0.2); background:#FFF; box-shadow:0 4px 12px rgba(15,31,53,0.04); }
    .detail-label { font-size:11px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.8px; margin-bottom:7px; display:flex; align-items:center; gap:6px; }
    .detail-label i { color:var(--gold); font-size:12px; }
    .detail-value { font-size:14.5px; font-weight:600; color:var(--text-primary); word-break:break-word; }
    .detail-value.empty { color:#9CA3AF; font-weight:400; font-style:italic; }
    .detail-item-full { grid-column:1/-1; }
    .badge { display:inline-block; padding:4px 12px; font-size:11px; font-weight:600; border-radius:20px; text-transform:uppercase; }
    .badge-pending   { background:rgba(234,179,8,0.12);  color:#92710A; }
    .badge-partial   { background:rgba(59,130,246,0.1);  color:#1D4ED8; }
    .badge-paid      { background:rgba(34,197,94,0.1);   color:#16803D; }
    .badge-active    { background:rgba(34,197,94,0.1);   color:#16803D; }
    .badge-completed { background:rgba(100,116,139,0.1); color:#475569; }
    .badge-cancelled { background:rgba(239,68,68,0.1);   color:#B91C1C; }
    .amount-big { font-size:17px; font-weight:800; }
    .due-chip { background:var(--gold-light); color:#92710A; padding:4px 12px; border-radius:20px; font-size:13px; font-weight:700; border:1px solid rgba(212,175,55,0.3); display:inline-block; }
    .meta-info { margin-top:22px; padding-top:18px; border-top:1px solid var(--border-color); display:flex; gap:24px; flex-wrap:wrap; }
    .meta-item { font-size:12px; color:var(--text-secondary); display:flex; align-items:center; gap:6px; }
    .meta-item i { color:var(--gold); }
    .form-actions { display:flex; align-items:center; gap:15px; margin-top:28px; padding-top:20px; border-top:1px solid var(--border-color); }
    .btn-gold { background-color:var(--gold); color:#FFF; padding:11px 24px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; display:inline-flex; align-items:center; gap:8px; border:none; cursor:pointer; transition:var(--transition); box-shadow:0 4px 10px rgba(212,175,55,0.2); }
    .btn-gold:hover { background-color:#B58D1B; transform:translateY(-1px); }
    .btn-outline { border:1px solid var(--border-color); background:transparent; color:var(--text-secondary); padding:11px 24px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; display:inline-flex; align-items:center; gap:8px; transition:var(--transition); }
    .btn-outline:hover { background:#F9FAFB; color:var(--text-primary); border-color:#D1D5DB; }
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
