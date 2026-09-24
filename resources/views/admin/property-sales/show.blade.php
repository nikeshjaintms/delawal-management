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
    max-width: 1040px; margin-left: auto; margin-right: auto;
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
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
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

.btn-pay-record {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important;
    color: #FFFFFF !important; border: 1px solid #34D399 !important;
    padding: 8px 18px; border-radius: 10px; font-size: 13px; font-weight: 700;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35); transition: all .2s ease;
}
.btn-pay-record:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.50);
}

.table-container { width: 100%; overflow-x: auto; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.10); }
.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 12px 16px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: 0.9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 12px 16px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.04) !important; }

/* Modal Styling */
.modal-overlay {
    position: fixed; inset: 0; background: rgba(5, 10, 20, 0.85); backdrop-filter: blur(8px);
    display: none; align-items: center; justify-content: center; z-index: 9999; padding: 16px;
}
.modal-overlay.active { display: flex; }
.modal-content-card {
    background: #131B2E; border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 20px;
    width: 100%; max-width: 520px; padding: 28px; box-shadow: 0 24px 60px rgba(0,0,0,0.6);
    position: relative; animation: modalFadeIn 0.25s ease-out;
}
@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.modal-header h3 { font-size: 18px; font-weight: 800; color: #FFFFFF; margin: 0; display: flex; align-items: center; gap: 8px; }
.modal-close-btn { background: transparent; border: none; color: #94A3B8; font-size: 20px; cursor: pointer; padding: 4px; border-radius: 6px; transition: color .2s ease; }
.modal-close-btn:hover { color: #F87171; }
.m-form-group { margin-bottom: 16px; }
.m-form-label { display: block; font-size: 12.5px; font-weight: 700; color: #CBD5E1; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
.m-form-control {
    width: 100%; padding: 10px 14px; background: rgba(10, 15, 26, 0.85);
    border: 1.5px solid rgba(255, 255, 255, 0.15); border-radius: 10px;
    color: #FFFFFF; font-size: 14px; outline: none; transition: border-color .2s; box-sizing: border-box;
}
.m-form-control:focus { border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25); }
.m-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
</style>

@php
    $assignedPlots = $propertySale->all_properties;
    $plotCount = $assignedPlots->count();
    $paymentsList = $propertySale->payments;
@endphp

<div class="crud-header">
    <div class="crud-title">
        <h2>Property Sale Details</h2>
        <p>Full record of this firm-wise property sale agreement and installment payment breakdown.</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        @if(($propertySale->remaining_amount ?? 0) > 0)
            <button type="button" class="btn-pay-record" onclick="openRecordPaymentModal()">
                <i class="fa-solid fa-circle-plus"></i> + Record Installment Payment
            </button>
        @endif
        <a href="{{ route('property-sales.receipt-pdf', $propertySale->id) }}" target="_blank" class="btn-gold" style="background: rgba(252,105,0,0.18) !important; border-color: rgba(252,105,0,0.45) !important; color: #FF8A3D !important; box-shadow: 0 4px 14px rgba(252,105,0,0.25);">
            <i class="fa-solid fa-file-pdf"></i> Print / PDF
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.30); color: #34D399; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600;">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="card-box">
    {{-- Hero --}}
    <div class="sale-hero">
        <div class="sale-icon"><i class="fa-solid fa-file-contract"></i></div>
        <div class="sale-hero-info">
            <h3>{{ $propertySale->property_names }}</h3>
            <p>
                <span style="color:#60A5FA; font-weight:700;">
                    {{ $assignedPlots->pluck('property_code')->filter()->implode(' · ') ?: 'Sale #'.$propertySale->id }}
                </span>
                &nbsp;·&nbsp;
                {{ $propertySale->customer->name ?? '' }}
                @if($propertySale->sale_date)
                    &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($propertySale->sale_date)->format('d M Y') }}
                @endif
            </p>
            <div class="hero-badges">
                <span class="badge badge-{{ $propertySale->sale_status }}">{{ ucfirst($propertySale->sale_status) }}</span>
                <span class="badge badge-{{ $propertySale->payment_status }}">{{ ucfirst($propertySale->payment_status) }}</span>
                @if($plotCount > 1)
                    <span class="badge" style="background: rgba(59, 130, 246, 0.20) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.40) !important;">
                        <i class="fa-solid fa-layer-group"></i> {{ $plotCount }} Assigned Plots / Units
                    </span>
                @endif
                @if($propertySale->sale_amount)
                    <span style="font-size:16px; font-weight:800; color:#34D399; margin-left: 6px;">
                        ₹{{ number_format($propertySale->sale_amount, 2) }}
                    </span>
                @endif
                @if($propertySale->net_profit > 0)
                    <span class="badge" style="background: rgba(16, 185, 129, 0.20) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.45) !important;">
                        <i class="fa-solid fa-arrow-trend-up"></i> +₹{{ number_format($propertySale->net_profit, 2) }} Profit ({{ $propertySale->profit_margin_percentage }}%)
                    </span>
                @elseif($propertySale->net_profit < 0)
                    <span class="badge" style="background: rgba(239, 68, 68, 0.20) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.45) !important;">
                        <i class="fa-solid fa-arrow-trend-down"></i> -₹{{ number_format(abs($propertySale->net_profit), 2) }} Loss
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
            <div class="detail-label"><i class="fa-solid fa-user"></i> Customer</div>
            @if($propertySale->customer)
                <div class="detail-value">{{ $propertySale->customer->name }}</div>
                <div style="font-size:12px; color:#CBD5E1; margin-top:4px;"><i class="fa-solid fa-phone" style="font-size:11px;"></i> {{ $propertySale->customer->mobile }}</div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user-tie"></i> Broker &amp; Commission</div>
            @if($propertySale->broker)
                <div class="detail-value" style="color: #C4B5FD;">{{ $propertySale->broker->name }}</div>
                <div style="font-size:12px; color:#CBD5E1; margin-top:4px;">{{ $propertySale->broker->mobile }}</div>
                @if($propertySale->broker_commission_amount > 0)
                    <div style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed rgba(255,255,255,0.15); font-size: 12px;">
                        <span style="color: #A78BFA; font-weight: 700;">Comm: ₹{{ number_format($propertySale->broker_commission_amount, 2) }}</span>
                        @if($propertySale->broker_commission_type === 'percentage' && $propertySale->broker_commission_rate > 0)
                            <small>({{ $propertySale->broker_commission_rate }}%)</small>
                        @endif
                        <div style="font-size: 11px; margin-top: 2px;">
                            <span style="color: #34D399;">Paid: ₹{{ number_format($propertySale->broker_commission_paid ?? 0, 2) }}</span> | 
                            <span style="color: #FBBF24;">Due: ₹{{ number_format($propertySale->broker_commission_due ?? 0, 2) }}</span>
                        </div>
                    </div>
                @endif
            @else
                <div class="detail-value empty">No broker assigned (Direct)</div>
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

    {{-- Assigned Properties / Multiple Plots Grid --}}
    <div class="section-title">
        <span><i class="fa-solid fa-layer-group"></i> Assigned Plot(s) / Unit(s) ({{ $plotCount }})</span>
    </div>
    @if($plotCount > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 14px; margin-bottom: 24px;">
            @foreach($assignedPlots as $p)
                @php
                    $unitCost = (float)($p->effective_purchase_cost ?? 0);
                    $unitSell = (float)($p->price ?? 0);
                    $unitProfit = $unitSell - $unitCost;
                    $unitMargin = $unitSell > 0 ? round(($unitProfit / $unitSell) * 100, 1) : 0;
                @endphp
                <div style="background: rgba(16, 22, 34, 0.75); border: 1.5px solid rgba(59, 130, 246, 0.25); border-radius: 14px; padding: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <strong style="color: #FFFFFF; font-size: 15px;">{{ $p->property_name }}</strong>
                        @if($p->property_code)
                            <span style="font-size: 11px; font-weight: 700; color: #60A5FA; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.35); padding: 2px 8px; border-radius: 6px;">
                                {{ $p->property_code }}
                            </span>
                        @endif
                    </div>
                    <div style="font-size: 12px; color: #CBD5E1; margin-bottom: 10px;">
                        @if($p->unit_no) <span>Unit: <strong>{{ $p->unit_no }}</strong> &nbsp;·&nbsp; </span> @endif
                        @if($p->size) <span>Size: <strong>{{ $p->size }} {{ $p->size_unit ?? 'Sq.Ft' }}</strong></span> @endif
                    </div>

                    <!-- Cost vs Selling Price Comparison -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; background: rgba(10, 15, 26, 0.70); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 10px 12px; margin-bottom: 10px;">
                        <div>
                            <div style="font-size: 10.5px; font-weight: 700; color: #F59E0B; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-cart-shopping" style="font-size: 10px;"></i> Purchase Cost
                            </div>
                            <div style="font-size: 13.5px; font-weight: 800; color: #FBBF24; margin-top: 2px;">
                                ₹{{ number_format($unitCost, 2) }}
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 10.5px; font-weight: 700; color: #60A5FA; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-tag" style="font-size: 10px;"></i> Selling / Price
                            </div>
                            <div style="font-size: 13.5px; font-weight: 800; color: #93C5FD; margin-top: 2px;">
                                ₹{{ number_format($unitSell, 2) }}
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 8px; border-top: 1px dashed rgba(255,255,255,0.12);">
                        <span style="font-size: 12px; font-weight: 800; color: {{ $unitProfit >= 0 ? '#34D399' : '#F87171' }}; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fa-solid {{ $unitProfit >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                            {{ $unitProfit >= 0 ? '+' : '' }}₹{{ number_format($unitProfit, 2) }} ({{ $unitMargin }}%)
                        </span>
                        <span class="badge badge-{{ $p->status }}" style="font-size: 9.5px; padding: 2px 8px;">{{ ucfirst($p->status) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="detail-item" style="margin-bottom: 24px;">
            <div class="detail-value empty">No specific plot assigned.</div>
        </div>
    @endif

    {{-- Amounts, Cost, Profit & Financial Breakdown --}}
    <div class="section-title">
        <span><i class="fa-solid fa-chart-pie"></i> Purchase, Sale &amp; Profit Analysis (ખરીદી, વેચાણ અને નફો)</span>
    </div>
    
    <div style="background: linear-gradient(135deg, rgba(20, 27, 41, 0.90) 0%, rgba(15, 23, 42, 0.95) 100%); border: 1.5px solid rgba(255, 255, 255, 0.14); border-radius: 20px; padding: 24px; margin-bottom: 24px; box-shadow: 0 12px 32px rgba(0,0,0,0.35);">
        
        <!-- 4 Key Financial Metrics Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 20px;">
            <!-- 1. Total Purchase Cost -->
            <div class="detail-item" style="background: rgba(245, 158, 11, 0.08) !important; border: 1.5px solid rgba(245, 158, 11, 0.35) !important;">
                <div class="detail-label" style="color: #FBBF24 !important;">
                    <i class="fa-solid fa-cart-shopping" style="color: #FBBF24 !important;"></i> Total Purchase Cost
                </div>
                <div class="detail-value" style="font-size: 21px; font-weight: 800; color: #FBBF24 !important;">
                    ₹{{ number_format($propertySale->total_purchase_cost, 2) }}
                </div>
                <div style="font-size: 11px; color: #94A3B8; margin-top: 4px;">
                    {{ $plotCount }} unit(s) acquisition cost
                </div>
            </div>

            <!-- 2. Total Sale Value -->
            <div class="detail-item" style="background: rgba(59, 130, 246, 0.08) !important; border: 1.5px solid rgba(59, 130, 246, 0.35) !important;">
                <div class="detail-label" style="color: #60A5FA !important;">
                    <i class="fa-solid fa-money-bill-wave" style="color: #60A5FA !important;"></i> Total Sale Value
                </div>
                <div class="detail-value" style="font-size: 21px; font-weight: 800; color: #60A5FA !important;">
                    ₹{{ number_format($propertySale->sale_amount ?? 0, 2) }}
                </div>
                <div style="font-size: 11px; color: #94A3B8; margin-top: 4px;">
                    Agreed customer contract price
                </div>
            </div>

            <!-- 3. Net Profit / Gain -->
            @php
                $isProfit = $propertySale->net_profit >= 0;
            @endphp
            <div class="detail-item" style="background: {{ $isProfit ? 'rgba(16, 185, 129, 0.12)' : 'rgba(239, 68, 68, 0.12)' }} !important; border: 1.5px solid {{ $isProfit ? 'rgba(16, 185, 129, 0.45)' : 'rgba(239, 68, 68, 0.45)' }} !important; box-shadow: 0 4px 20px {{ $isProfit ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' }};">
                <div class="detail-label" style="color: {{ $isProfit ? '#34D399' : '#F87171' }} !important;">
                    <i class="fa-solid {{ $isProfit ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}" style="color: {{ $isProfit ? '#34D399' : '#F87171' }} !important;"></i> Net {{ $isProfit ? 'Profit (ચોખ્ખો નફો)' : 'Loss (ખોટ)' }}
                </div>
                <div class="detail-value" style="font-size: 21px; font-weight: 800; color: {{ $isProfit ? '#34D399' : '#F87171' }} !important;">
                    {{ $isProfit ? '+' : '' }}₹{{ number_format($propertySale->net_profit, 2) }}
                </div>
                <div style="font-size: 11px; color: {{ $isProfit ? '#A7F3D0' : '#FECACA' }}; margin-top: 4px; font-weight: 700;">
                    {{ $propertySale->profit_margin_percentage }}% Margin &nbsp;·&nbsp; {{ $propertySale->roi_percentage }}% ROI
                </div>
            </div>

            <!-- 4. Total Received & Due -->
            <div class="detail-item" style="background: rgba(139, 92, 246, 0.08) !important; border: 1.5px solid rgba(139, 92, 246, 0.35) !important;">
                <div class="detail-label" style="color: #C084FC !important;">
                    <i class="fa-solid fa-hand-holding-dollar" style="color: #C084FC !important;"></i> Total Received / Paid
                </div>
                <div class="detail-value" style="font-size: 21px; font-weight: 800; color: #C084FC !important;">
                    ₹{{ number_format($propertySale->booking_amount ?? 0, 2) }}
                </div>
                <div style="font-size: 11px; color: #F87171; margin-top: 4px; font-weight: 600;">
                    Outstanding Due: ₹{{ number_format($propertySale->remaining_amount ?? 0, 2) }}
                </div>
            </div>
        </div>

        <!-- Profit Formula & Financial Breakdown Strip -->
        <div style="background: rgba(10, 15, 26, 0.75); border: 1px solid rgba(255, 255, 255, 0.10); border-radius: 14px; padding: 14px 18px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; font-size: 13px;">
            <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
                <span style="color: #94A3B8; font-weight: 600;">Financial Formula:</span>
                <span style="color: #60A5FA; font-weight: 700;">
                    <i class="fa-solid fa-plus" style="font-size: 10px;"></i> Sale Price: ₹{{ number_format($propertySale->sale_amount ?? 0, 2) }}
                </span>
                <span style="color: #64748B;">—</span>
                <span style="color: #F59E0B; font-weight: 700;">
                    <i class="fa-solid fa-minus" style="font-size: 10px;"></i> Purchase Cost: ₹{{ number_format($propertySale->total_purchase_cost, 2) }}
                </span>
                @if(($propertySale->broker_commission_amount ?? 0) > 0)
                    <span style="color: #64748B;">—</span>
                    <span style="color: #A78BFA; font-weight: 700;">
                        <i class="fa-solid fa-minus" style="font-size: 10px;"></i> Broker Comm: ₹{{ number_format($propertySale->broker_commission_amount, 2) }}
                    </span>
                @endif
                <span style="color: #64748B;">=</span>
                <span style="color: {{ $isProfit ? '#34D399' : '#F87171' }}; font-weight: 800; font-size: 14px;">
                    Net {{ $isProfit ? 'Profit' : 'Loss' }}: {{ $isProfit ? '+' : '' }}₹{{ number_format($propertySale->net_profit, 2) }}
                </span>
            </div>
            <div>
                <span class="badge" style="background: {{ $isProfit ? 'rgba(16, 185, 129, 0.18)' : 'rgba(239, 68, 68, 0.18)' }} !important; color: {{ $isProfit ? '#34D399' : '#F87171' }} !important; border: 1px solid {{ $isProfit ? 'rgba(16, 185, 129, 0.4)' : 'rgba(239, 68, 68, 0.4)' }} !important; padding: 4px 10px; font-size: 11.5px;">
                    {{ $propertySale->profit_margin_percentage }}% Profit Margin
                </span>
            </div>
        </div>

        <!-- Progress Bar -->
        <div style="padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <span style="font-size: 12px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Payment Progress ({{ $propertySale->paid_percentage }}% Cleared)</span>
                <span style="font-size: 12px; color: #CBD5E1; font-weight: 700;">₹{{ number_format($propertySale->booking_amount ?? 0, 2) }} of ₹{{ number_format($propertySale->sale_amount ?? 0, 2) }}</span>
            </div>
            <div style="width: 100%; height: 8px; background: rgba(255,255,255,0.08); border-radius: 999px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
                <div style="width: {{ $propertySale->paid_percentage }}%; height: 100%; background: linear-gradient(90deg, #10B981, #34D399); border-radius: 999px; transition: width 0.3s ease;"></div>
            </div>
        </div>
    </div>

    {{-- Installment Payments History --}}
    <div class="section-title">
        <span><i class="fa-solid fa-receipt"></i> Payment Installments History ({{ $paymentsList->count() }})</span>
        @if(($propertySale->remaining_amount ?? 0) > 0)
            <button type="button" class="btn-pay-record" style="padding: 5px 12px; font-size: 12px;" onclick="openRecordPaymentModal()">
                <i class="fa-solid fa-plus"></i> Record Next Installment
            </button>
        @endif
    </div>

    <div class="table-container" style="margin-bottom: 24px;">
        <table class="premium-table">
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th>Payment Date</th>
                    <th>Amount Paid</th>
                    <th>Payment Mode</th>
                    <th>Reference / Cheque</th>
                    <th>Remarks</th>
                    <th>Recorded At</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paymentsList as $i => $pay)
                    <tr>
                        <td style="color: #94A3B8;">{{ $i + 1 }}</td>
                        <td>
                            <strong style="color: #FFFFFF;">
                                {{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') : '—' }}
                            </strong>
                        </td>
                        <td>
                            <strong style="color: #34D399; font-size: 14px;">
                                ₹{{ number_format($pay->payment_amount, 2) }}
                            </strong>
                        </td>
                        <td>
                            <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.35);">
                                {{ $pay->payment_mode ?? 'Cash' }}
                            </span>
                        </td>
                        <td style="color: #CBD5E1;">{{ $pay->transaction_ref ?: '—' }}</td>
                        <td style="color: #94A3B8; font-size: 12px;">{{ $pay->remarks ?: '—' }}</td>
                        <td style="color: #64748B; font-size: 11.5px;">{{ $pay->created_at ? $pay->created_at->format('d M Y, h:i A') : '—' }}</td>
                        <td style="text-align: right;">
                            <form action="{{ route('property-sales.payments.destroy', [$propertySale->id, $pay->id]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this payment installment? Balance will be updated automatically.')" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.35); color: #F87171; border-radius: 6px; padding: 4px 10px; font-size: 11.5px; cursor: pointer;">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" align="center" style="padding: 24px; color: #94A3B8;">
                            No installment payments recorded in table yet. Initial booking amount: ₹{{ number_format($propertySale->booking_amount ?? 0, 2) }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
        <a href="{{ route('property-sales.receipt-pdf', $propertySale->id) }}" target="_blank" class="btn-gold" style="background: rgba(252,105,0,0.18) !important; border-color: rgba(252,105,0,0.45) !important; color: #FF8A3D !important; box-shadow: 0 4px 14px rgba(252,105,0,0.25);">
            <i class="fa-solid fa-file-pdf"></i> Print / PDF Agreement
        </a>
        <a href="{{ route('property-sales.edit', $propertySale->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit Sale
        </a>
        <a href="{{ route('property-sales.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

{{-- ── Record Installment Payment Modal on Show Page ── --}}
<div class="modal-overlay" id="recordPaymentModal">
    <div class="modal-content-card">
        <div class="modal-header">
            <h3><i class="fa-solid fa-hand-holding-dollar" style="color: #34D399;"></i> Record Installment Payment</h3>
            <button type="button" class="modal-close-btn" onclick="closeRecordPaymentModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('property-sales.payments.store', $propertySale->id) }}">
            @csrf
            <div style="background: rgba(37, 99, 235, 0.10); border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 12px; padding: 14px; margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                    <span style="color: #94A3B8;">Customer:</span>
                    <strong style="color: #FFFFFF;">{{ $propertySale->customer->name ?? '—' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                    <span style="color: #94A3B8;">Total Sale Value:</span>
                    <strong style="color: #60A5FA;">₹ {{ number_format($propertySale->sale_amount ?? 0, 2) }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                    <span style="color: #94A3B8;">Paid So Far:</span>
                    <strong style="color: #34D399;">₹ {{ number_format($propertySale->booking_amount ?? 0, 2) }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 14px; margin-top: 6px; padding-top: 6px; border-top: 1px dashed rgba(255,255,255,0.15);">
                    <span style="color: #F87171; font-weight: 700;">Remaining Due Balance:</span>
                    <strong style="color: #F87171; font-size: 15px;">₹ {{ number_format($propertySale->remaining_amount ?? 0, 2) }}</strong>
                </div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Payment Amount (₹) <span style="color:#EF4444;">*</span></label>
                    <input type="number" step="0.01" min="0.01" max="{{ $propertySale->remaining_amount ?? 0 }}" name="payment_amount" class="m-form-control" value="{{ $propertySale->remaining_amount ?? '' }}" required placeholder="0.00">
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Payment Date <span style="color:#EF4444;">*</span></label>
                    <input type="date" name="payment_date" class="m-form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Payment Mode <span style="color:#EF4444;">*</span></label>
                    <select name="payment_mode" class="m-form-control" required>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer / NEFT / RTGS</option>
                        <option value="Cheque">Cheque</option>
                        <option value="UPI">UPI / GPay / PhonePe</option>
                        @foreach($paymentModes as $pm)
                            @if(!in_array($pm->name, ['Cash', 'Bank Transfer', 'Cheque', 'UPI']))
                                <option value="{{ $pm->name }}">{{ $pm->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Ref No. / Cheque No.</label>
                    <input type="text" name="transaction_ref" class="m-form-control" placeholder="e.g. TXN987654 / CHQ-1002">
                </div>
            </div>

            <div class="m-form-group">
                <label class="m-form-label">Remarks / Notes</label>
                <input type="text" name="remarks" class="m-form-control" placeholder="Optional installment notes">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.1);">
                <button type="button" class="btn-outline" onclick="closeRecordPaymentModal()">Cancel</button>
                <button type="submit" class="btn-pay-record">
                    <i class="fa-solid fa-check"></i> Save Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRecordPaymentModal() {
    document.getElementById('recordPaymentModal').classList.add('active');
}
function closeRecordPaymentModal() {
    document.getElementById('recordPaymentModal').classList.remove('active');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeRecordPaymentModal();
});
document.getElementById('recordPaymentModal').addEventListener('click', function(e) {
    if (e.target === this) closeRecordPaymentModal();
});
</script>
@endsection
