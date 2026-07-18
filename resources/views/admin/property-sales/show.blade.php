@extends('admin.layouts.app')

@section('title', 'View Property Sale')
@section('page-title', 'Property Sales')

@section('content')
<style>
    .crud-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:15px; }
    .crud-title h2 { font-size:22px; font-weight:700; color:var(--text-primary); margin-bottom:4px; }
    .crud-title p  { font-size:13.5px; color:var(--text-secondary); }
    .card-box { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:30px; box-shadow:var(--soft-shadow); max-width:900px; margin:0 auto; }
    .sale-hero { display:flex; align-items:center; gap:20px; padding-bottom:24px; margin-bottom:24px; border-bottom:1px solid var(--border-color); flex-wrap:wrap; }
    .sale-icon { width:64px; height:64px; border-radius:12px; background:var(--gold-light); border:2px solid var(--gold); display:flex; align-items:center; justify-content:center; font-size:26px; color:var(--gold); flex-shrink:0; }
    .sale-hero-info h3 { font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:5px; }
    .sale-hero-info p  { font-size:13.5px; color:var(--text-secondary); margin-bottom:8px; }
    .hero-badges { display:flex; gap:10px; flex-wrap:wrap; }
    .section-title { font-size:12px; font-weight:700; color:var(--gold); text-transform:uppercase; letter-spacing:1px; margin-bottom:14px; margin-top:22px; padding-bottom:8px; border-bottom:1px solid var(--border-color); }
    .detail-grid  { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .detail-grid-3{ display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
    @media(max-width:768px){ .detail-grid-3{ grid-template-columns:1fr 1fr; } }
    @media(max-width:576px){ .detail-grid,.detail-grid-3{ grid-template-columns:1fr; } }
    .detail-item { padding:14px 16px; background:#F9FAFB; border:1px solid var(--border-color); border-radius:10px; transition:var(--transition); }
    .detail-item:hover { border-color:rgba(212,175,55,0.2); background:#FFFFFF; box-shadow:0 4px 12px rgba(15,31,53,0.04); }
    .detail-label { font-size:11px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.8px; margin-bottom:7px; display:flex; align-items:center; gap:6px; }
    .detail-label i { color:var(--gold); font-size:12px; }
    .detail-value { font-size:14.5px; font-weight:600; color:var(--text-primary); word-break:break-word; }
    .detail-value.empty { color:#9CA3AF; font-weight:400; font-style:italic; }
    .detail-item-full { grid-column:1/-1; }
    .badge { display:inline-block; padding:4px 12px; font-size:11px; font-weight:600; border-radius:20px; text-transform:uppercase; }
    .badge-pending  { background:rgba(234,179,8,0.12); color:#92710A; }
    .badge-partial  { background:rgba(59,130,246,0.1); color:#1D4ED8; }
    .badge-paid     { background:rgba(34,197,94,0.1); color:#16803D; }
    .badge-booked   { background:rgba(234,179,8,0.12); color:#92710A; }
    .badge-sold     { background:rgba(34,197,94,0.1); color:#16803D; }
    .badge-cancelled{ background:rgba(239,68,68,0.1); color:#B91C1C; }
    .amount-highlight { font-size:16px; font-weight:800; color:var(--text-primary); }
    .doc-link { display:inline-flex; align-items:center; gap:6px; color:var(--gold); font-size:13.5px; font-weight:600; text-decoration:none; padding:6px 14px; border:1px solid rgba(212,175,55,0.35); border-radius:7px; background:var(--gold-light); transition:var(--transition); }
    .doc-link:hover { background:rgba(212,175,55,0.2); color:#B58D1B; }
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
        <h2>Property Sale Details</h2>
        <p>Full record of this firm-wise property sale or booking.</p>
    </div>
</div>

<div class="card-box">
    {{-- Hero --}}
    <div class="sale-hero">
        <div class="sale-icon"><i class="fa-solid fa-file-contract"></i></div>
        <div class="sale-hero-info">
            <h3>{{ $propertySale->property->property_name ?? 'Property Sale' }}</h3>
            <p>
                @if($propertySale->property?->property_code)
                    <span style="color:var(--gold); font-weight:600;">{{ $propertySale->property->property_code }}</span> &nbsp;·&nbsp;
                @endif
                {{ $propertySale->customer->name ?? '' }}
                @if($propertySale->sale_date)
                    &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($propertySale->sale_date)->format('d M Y') }}
                @endif
            </p>
            <div class="hero-badges">
                <span class="badge badge-{{ $propertySale->sale_status }}">{{ ucfirst($propertySale->sale_status) }}</span>
                <span class="badge badge-{{ $propertySale->payment_status }}">{{ ucfirst($propertySale->payment_status) }}</span>
                @if($propertySale->sale_amount)
                    <span style="font-size:14px; font-weight:700; color:var(--text-primary);">
                        ₹{{ number_format($propertySale->sale_amount, 0) }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Parties --}}
    <div class="section-title"><i class="fa-solid fa-handshake"></i> Sale Parties</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building"></i> Property</div>
            @if($propertySale->property)
                <div class="detail-value">
                    {{ $propertySale->property->property_name }}
                    @if($propertySale->property->property_code)
                        <span style="color:var(--gold); font-size:13px;"> ({{ $propertySale->property->property_code }})</span>
                    @endif
                </div>
                @if($propertySale->property->propertyType)
                    <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">{{ $propertySale->property->propertyType->name }}</div>
                @endif
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user"></i> Customer</div>
            @if($propertySale->customer)
                <div class="detail-value">{{ $propertySale->customer->name }}</div>
                <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">{{ $propertySale->customer->mobile }}</div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user-tie"></i> Broker</div>
            @if($propertySale->broker)
                <div class="detail-value">{{ $propertySale->broker->name }}</div>
                <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">{{ $propertySale->broker->mobile }}</div>
            @else
                <div class="detail-value empty">No broker assigned</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar"></i> Sale Date</div>
            @if($propertySale->sale_date)
                <div class="detail-value">{{ \Carbon\Carbon::parse($propertySale->sale_date)->format('d M Y') }}</div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
    </div>

    {{-- Amounts --}}
    <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Amount Details</div>
    <div class="detail-grid-3">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-indian-rupee-sign"></i> Sale Amount</div>
            @if($propertySale->sale_amount !== null)
                <div class="detail-value amount-highlight">₹{{ number_format($propertySale->sale_amount, 2) }}</div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-money-bill-wave"></i> Booking Amount</div>
            @if($propertySale->booking_amount !== null)
                <div class="detail-value">₹{{ number_format($propertySale->booking_amount, 2) }}</div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-hourglass-half"></i> Remaining Amount</div>
            @if($propertySale->remaining_amount !== null)
                <div class="detail-value" style="color:{{ $propertySale->remaining_amount > 0 ? '#B91C1C' : '#16803D' }};">
                    ₹{{ number_format($propertySale->remaining_amount, 2) }}
                </div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
    </div>

    {{-- Status & Documents --}}
    <div class="section-title"><i class="fa-solid fa-circle-dot"></i> Status & Documents</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-credit-card"></i> Payment Status</div>
            <div class="detail-value"><span class="badge badge-{{ $propertySale->payment_status }}">{{ ucfirst($propertySale->payment_status) }}</span></div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-circle-dot"></i> Sale Status</div>
            <div class="detail-value"><span class="badge badge-{{ $propertySale->sale_status }}">{{ ucfirst($propertySale->sale_status) }}</span></div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-file-signature"></i> Agreement</div>
            @if($propertySale->agreement_file)
                <div class="detail-value">
                    <a href="{{ asset('storage/' . $propertySale->agreement_file) }}" target="_blank" class="doc-link">
                        <i class="fa-solid fa-file-arrow-down"></i> View Agreement
                    </a>
                </div>
            @else
                <div class="detail-value empty">No agreement uploaded</div>
            @endif
        </div>
    </div>

    @if($propertySale->note)
        <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Note</div>
        <div class="detail-item">
            <div class="detail-value" style="font-weight:400; font-size:14px; line-height:1.7;">{{ $propertySale->note }}</div>
        </div>
    @endif

    {{-- Meta --}}
    <div class="meta-info">
        <div class="meta-item"><i class="fa-regular fa-calendar-plus"></i> <span>Created: {{ $propertySale->created_at->format('d M Y, h:i A') }}</span></div>
        <div class="meta-item"><i class="fa-regular fa-calendar-check"></i> <span>Updated: {{ $propertySale->updated_at->format('d M Y, h:i A') }}</span></div>
    </div>

    {{-- Actions --}}
    <div class="form-actions">
        <a href="{{ route('property-sales.edit', $propertySale->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit Sale
        </a>
        <a href="{{ route('property-sales.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection
