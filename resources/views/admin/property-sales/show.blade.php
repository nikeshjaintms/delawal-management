@extends('admin.layouts.app')

@section('title', 'View Property Sale')
@section('page-title', 'Property Sales')

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
    max-width: 960px; margin-left: auto; margin-right: auto;
}

.sale-hero {
    display: flex; align-items: center; gap: 20px; padding-bottom: 24px; margin-bottom: 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); flex-wrap: wrap;
}
.sale-icon {
    width: 64px; height: 64px; border-radius: 16px;
    background: rgba(59, 130, 246, 0.15) !important; border: 1.5px solid rgba(59, 130, 246, 0.35) !important;
    display: flex; align-items: center; justify-content: center; font-size: 26px; color: #60A5FA !important; flex-shrink: 0;
}
.sale-hero-info h3 { font-size: 22px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; }
.sale-hero-info p { font-size: 13.5px; color: #CBD5E1 !important; margin-bottom: 10px; }
.hero-badges { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.section-title {
    font-size: 12px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 16px; margin-top: 26px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}

.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.detail-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
@media(max-width:768px){ .detail-grid-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .detail-grid, .detail-grid-3 { grid-template-columns: 1fr; } }

.detail-item {
    padding: 16px 18px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.12) !important; border-radius: 14px !important;
    transition: all 0.2s ease !important;
}
.detail-item:hover { border-color: rgba(59, 130, 246, 0.40) !important; background: rgba(22, 30, 46, 0.85) !important; }

.detail-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
.detail-label i { color: #60A5FA !important; font-size: 12px; }
.detail-value { font-size: 14.5px; font-weight: 700; color: #FFFFFF !important; word-break: break-word; }
.detail-value.empty { color: #64748B !important; font-weight: 500; font-style: italic; }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-pending { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-partial { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.badge-paid { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-booked { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-sold { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-cancelled { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.amount-highlight { font-size: 18px; font-weight: 800; color: #34D399 !important; }
.doc-link { display: inline-flex; align-items: center; gap: 8px; color: #60A5FA !important; font-size: 13.5px; font-weight: 700; text-decoration: none !important; padding: 8px 16px; border: 1px solid rgba(59, 130, 246, 0.35); border-radius: 10px; background: rgba(59, 130, 246, 0.15); transition: all 0.2s ease; }
.doc-link:hover { background: #2563EB !important; color: #FFFFFF !important; }

.meta-info { margin-top: 24px; padding-top: 18px; border-top: 1px solid rgba(255, 255, 255, 0.10); display: flex; gap: 24px; flex-wrap: wrap; }
.meta-item { font-size: 12.5px; color: #94A3B8 !important; display: flex; align-items: center; gap: 6px; }
.meta-item i { color: #60A5FA !important; }

.form-actions { display: flex; align-items: center; justify-content: flex-end; gap: 14px; margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 12px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 18px rgba(37,99,235,0.38);
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
                    <span style="color:#60A5FA; font-weight:700;">{{ $propertySale->property->property_code }}</span> &nbsp;·&nbsp;
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
                    <span style="font-size:16px; font-weight:800; color:#34D399;">
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
            <div class="detail-label"><i class="fa-solid fa-building-user"></i> Firm</div>
            <div class="detail-value">{{ $propertySale->firm->firm_name ?? 'Not set' }}</div>
            @if($propertySale->firm?->city)
                <div style="font-size:12px; color:#CBD5E1; margin-top:4px;">{{ $propertySale->firm->city }}</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building"></i> Property</div>
            @if($propertySale->property)
                <div class="detail-value">
                    {{ $propertySale->property->property_name }}
                    @if($propertySale->property->property_code)
                        <span style="color:#60A5FA; font-size:13px;"> ({{ $propertySale->property->property_code }})</span>
                    @endif
                </div>
                @if($propertySale->property->propertyType)
                    <div style="font-size:12px; color:#CBD5E1; margin-top:4px;">{{ $propertySale->property->propertyType->name }}</div>
                @endif
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user"></i> Customer</div>
            @if($propertySale->customer)
                <div class="detail-value">{{ $propertySale->customer->name }}</div>
                <div style="font-size:12px; color:#CBD5E1; margin-top:4px;">{{ $propertySale->customer->mobile }}</div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user-tie"></i> Broker</div>
            @if($propertySale->broker)
                <div class="detail-value">{{ $propertySale->broker->name }}</div>
                <div style="font-size:12px; color:#CBD5E1; margin-top:4px;">{{ $propertySale->broker->mobile }}</div>
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
                <div class="detail-value" style="color:{{ $propertySale->remaining_amount > 0 ? '#F87171' : '#34D399' }};">
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
