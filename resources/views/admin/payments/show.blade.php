@extends('admin.layouts.app')

@section('title', 'View Payment')
@section('page-title', 'Payment Management')

@section('content')
<style>
    .crud-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:15px; }
    .crud-title h2 { font-size:22px; font-weight:700; color:var(--text-primary); margin-bottom:4px; }
    .crud-title p  { font-size:13.5px; color:var(--text-secondary); }
    .card-box { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:30px; box-shadow:var(--soft-shadow); max-width:900px; margin:0 auto; }
    .pay-hero { display:flex; align-items:center; gap:20px; padding-bottom:24px; margin-bottom:24px; border-bottom:1px solid var(--border-color); flex-wrap:wrap; }
    .pay-icon { width:64px; height:64px; border-radius:12px; background:var(--gold-light); border:2px solid var(--gold); display:flex; align-items:center; justify-content:center; font-size:26px; color:var(--gold); flex-shrink:0; }
    .pay-hero-info h3 { font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:5px; }
    .pay-hero-info p  { font-size:13.5px; color:var(--text-secondary); margin-bottom:8px; }
    .hero-badges { display:flex; gap:10px; flex-wrap:wrap; }
    .section-title { font-size:12px; font-weight:700; color:var(--gold); text-transform:uppercase; letter-spacing:1px; margin-bottom:14px; margin-top:22px; padding-bottom:8px; border-bottom:1px solid var(--border-color); }
    .detail-grid  { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .detail-grid-3{ display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
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
    .badge-pending { background:rgba(234,179,8,0.12); color:#92710A; }
    .badge-partial { background:rgba(59,130,246,0.1); color:#1D4ED8; }
    .badge-paid    { background:rgba(34,197,94,0.1);  color:#16803D; }
    .amount-big   { font-size:18px; font-weight:800; }
    .pending-red  { color:#B91C1C; }
    .paid-green   { color:#16803D; }
    .mode-chip { display:inline-flex; align-items:center; gap:5px; background:var(--gold-light); color:#92710A; font-size:13px; font-weight:700; padding:5px 12px; border-radius:20px; border:1px solid rgba(212,175,55,0.3); }
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
        <h2>Payment Details</h2>
        <p>Full record for payment <strong>#{{ $payment->id }}</strong></p>
    </div>
</div>

<div class="card-box">
    {{-- Hero --}}
    <div class="pay-hero">
        <div class="pay-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div class="pay-hero-info">
            <h3>₹{{ number_format($payment->payment_amount, 2) }} <span style="font-size:14px;font-weight:400;color:var(--text-secondary);">paid</span></h3>
            <p>
                {{ $payment->customer->name ?? '' }}
                &nbsp;·&nbsp; {{ $payment->property->property_name ?? '' }}
                @if($payment->payment_date) &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }} @endif
            </p>
            <div class="hero-badges">
                <span class="badge badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span>
                @if($payment->payment_mode)
                    <span class="mode-chip"><i class="fa-solid fa-wallet" style="font-size:11px;"></i> {{ $payment->payment_mode }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Booking Reference --}}
    <div class="section-title"><i class="fa-solid fa-file-contract"></i> Booking & Firm Reference</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building-user"></i> Firm</div>
            <div class="detail-value">{{ $payment->firm->firm_name ?? 'Not set' }}</div>
            @if($payment->firm?->city)
                <div style="font-size:12px;color:var(--text-secondary);margin-top:3px;">{{ $payment->firm->city }}</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building"></i> Property</div>
            <div class="detail-value">
                {{ $payment->property->property_name ?? '-' }}
                @if($payment->property?->property_code)
                    <span style="color:var(--gold);font-size:13px;"> ({{ $payment->property->property_code }})</span>
                @endif
                @if($payment->property?->unit_no)
                    <div style="font-size:12px;color:var(--text-secondary);margin-top:3px;">Unit: {{ $payment->property->unit_no }}</div>
                @endif
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user"></i> Customer</div>
            <div class="detail-value">{{ $payment->customer->name ?? '-' }}</div>
            @if($payment->customer?->mobile)
                <div style="font-size:12px;color:var(--text-secondary);margin-top:3px;">{{ $payment->customer->mobile }}</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-link"></i> Booking ID</div>
            <div class="detail-value">
                <a href="{{ route('property-sales.show', $payment->property_sale_id) }}"
                   style="color:var(--gold);text-decoration:none;">#{{ $payment->property_sale_id }}</a>
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar"></i> Payment Date</div>
            @if($payment->payment_date)
                <div class="detail-value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
    </div>

    {{-- Amounts --}}
    <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Amount Details</div>
    <div class="detail-grid-3">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-file-invoice-dollar"></i> Total Sale Amount</div>
            <div class="detail-value amount-big">₹{{ number_format($payment->total_amount, 2) }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-money-bill-wave"></i> This Payment</div>
            <div class="detail-value amount-big" style="color:var(--gold);">₹{{ number_format($payment->payment_amount, 2) }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-circle-check"></i> Total Paid (cumulative)</div>
            <div class="detail-value amount-big paid-green">₹{{ number_format($payment->paid_amount, 2) }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-hourglass-half"></i> Pending Amount</div>
            <div class="detail-value amount-big {{ $payment->pending_amount > 0 ? 'pending-red' : 'paid-green' }}">
                ₹{{ number_format($payment->pending_amount, 2) }}
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-wallet"></i> Payment Mode</div>
            @if($payment->payment_mode)
                <div class="detail-value"><span class="mode-chip">{{ $payment->payment_mode }}</span></div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-receipt"></i> Transaction / Cheque Ref</div>
            @if($payment->transaction_ref)
                <div class="detail-value">{{ $payment->transaction_ref }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
    </div>

    {{-- Status --}}
    <div class="section-title"><i class="fa-solid fa-circle-dot"></i> Payment Status</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-circle-dot"></i> Status</div>
            <div class="detail-value"><span class="badge badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span></div>
        </div>
    </div>

    @if($payment->remarks)
        <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Remarks</div>
        <div class="detail-item">
            <div class="detail-value" style="font-weight:400;font-size:14px;line-height:1.7;">{{ $payment->remarks }}</div>
        </div>
    @endif

    <div class="meta-info">
        <div class="meta-item"><i class="fa-regular fa-calendar-plus"></i><span>Recorded: {{ $payment->created_at->format('d M Y, h:i A') }}</span></div>
        <div class="meta-item"><i class="fa-regular fa-calendar-check"></i><span>Updated: {{ $payment->updated_at->format('d M Y, h:i A') }}</span></div>
    </div>

    <div class="form-actions">
        <a href="{{ route('payments.edit', $payment->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit Payment
        </a>
        <a href="{{ route('payments.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection
