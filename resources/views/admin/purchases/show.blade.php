@extends('admin.layouts.app')
@section('title', 'View Property Buy')
@section('page-title', 'Property Buy')
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

.section-head {
    font-size: 13px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 18px; padding-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}

.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 24px; }
.detail-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; margin-bottom: 24px; }
@media(max-width:768px){ .detail-grid-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .detail-grid, .detail-grid-3 { grid-template-columns: 1fr; } }

.detail-item {
    padding: 16px 18px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.12) !important; border-radius: 16px !important;
    transition: all .25s ease;
}
.detail-item:hover { border-color: rgba(59, 130, 246, 0.40) !important; transform: translateY(-2px); }
.detail-item-full { grid-column: 1 / -1; }

.detail-label {
    font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase;
    letter-spacing: 0.8px; margin-bottom: 7px; display: flex; align-items: center; gap: 6px;
}
.detail-label i { color: #60A5FA !important; font-size: 12px; }

.detail-value { font-size: 14.5px; font-weight: 700; color: #FFFFFF !important; word-break: break-word; }
.detail-value.empty { color: #94A3B8 !important; font-weight: 400; font-style: italic; }

.chip { background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; padding: 4px 10px; border-radius: 8px; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(59, 130, 246, 0.30); display: inline-block; }

.form-actions { display: flex; align-items: center; gap: 14px; margin-top: 10px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); }

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
        <h2>Property Buy Details</h2>
        <p>Overview of: <strong>{{ $purchase->display_name }}</strong></p>
    </div>
</div>

<div class="card-box">
    {{-- ── Property Details ── --}}
    <div class="section-head">
        <i class="fa-solid fa-building"></i> Property Details
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building"></i> Firm</div>
            <div class="detail-value">{{ $purchase->firm_names }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-hotel"></i> Property / Plot Name</div>
            <div class="detail-value">{{ $purchase->display_name }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-shapes"></i> Property Type</div>
            <div class="detail-value">
                @if($purchase->property_type)
                    <span class="chip">{{ $purchase->property_type }}</span>
                @else
                    <span class="empty">Not specified</span>
                @endif
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-hashtag"></i> Property Number / Code</div>
            <div class="detail-value">{{ $purchase->property_code ?: '—' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-location-dot"></i> Location</div>
            <div class="detail-value">{{ $purchase->location ?: '—' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-ruler-combined"></i> Area</div>
            <div class="detail-value">
                @if($purchase->area)
                    <span style="color: #34D399; font-size: 16px;">{{ number_format($purchase->area, 2) }}</span>
                    <span style="color: #94A3B8; font-size: 13px; margin-left: 4px;">{{ $purchase->area_unit ?? 'Sq.Ft' }}</span>
                @else
                    <span class="empty">—</span>
                @endif
            </div>
        </div>

        <div class="detail-item detail-item-full">
            <div class="detail-label"><i class="fa-solid fa-map-pin"></i> Address</div>
            <div class="detail-value {{ $purchase->address ? '' : 'empty' }}">
                {{ $purchase->address ?: 'No address specified' }}
            </div>
        </div>
    </div>

    <div class="detail-grid-3">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-file-contract"></i> Survey No.</div>
            <div class="detail-value">{{ $purchase->survey_no ?: '—' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-map"></i> TP No.</div>
            <div class="detail-value">{{ $purchase->tp_no ?: '—' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-draw-polygon"></i> FP No.</div>
            <div class="detail-value">{{ $purchase->fp_no ?: '—' }}</div>
        </div>
    </div>

    {{-- ── Financial & Purchase Price Details ── --}}
    <div class="section-head" style="margin-top: 30px; color: #34D399 !important;">
        <i class="fa-solid fa-indian-rupee-sign"></i> Purchase Price & Financial Details
    </div>

    <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.12) 0%, rgba(5, 150, 105, 0.08) 100%); border: 1.5px solid rgba(16, 185, 129, 0.35); border-radius: 16px; padding: 20px; margin-bottom: 24px;">
        <div class="detail-grid">
            <div class="detail-item" style="background: rgba(16, 22, 34, 0.85) !important; border-color: rgba(16, 185, 129, 0.4) !important;">
                <div class="detail-label" style="color: #34D399 !important;"><i class="fa-solid fa-money-bill-wave" style="color:#34D399 !important;"></i> Total Purchase Price / Buy Amount</div>
                <div class="detail-value" style="font-size: 22px; font-weight: 800; color: #34D399 !important;">
                    ₹{{ number_format($purchase->purchase_amount ?? 0, 2) }}
                </div>
            </div>

            <div class="detail-item" style="background: rgba(16, 22, 34, 0.85) !important;">
                <div class="detail-label"><i class="fa-solid fa-calendar-day"></i> Purchase Date</div>
                <div class="detail-value">
                    {{ $purchase->purchase_date ? date('d M, Y', strtotime($purchase->purchase_date)) : '—' }}
                </div>
            </div>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user-tie"></i> Seller / Vendor / Owner</div>
            <div class="detail-value">
                @if($purchase->vendor)
                    <strong style="color: #FFFFFF;">{{ $purchase->vendor->name }}</strong>
                    @if($purchase->vendor->phone)
                        <div style="font-size: 12px; color: #94A3B8; margin-top: 3px;">
                            <i class="fa-solid fa-phone" style="font-size: 10px;"></i> {{ $purchase->vendor->phone }}
                        </div>
                    @endif
                @else
                    <span class="empty">Not specified</span>
                @endif
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-credit-card"></i> Payment Mode</div>
            <div class="detail-value">
                @if($purchase->payment_mode)
                    <span class="chip" style="background: rgba(139, 92, 246, 0.15) !important; color: #A78BFA !important; border-color: rgba(139, 92, 246, 0.35) !important;">
                        {{ $purchase->payment_mode }}
                    </span>
                @else
                    <span class="empty">—</span>
                @endif
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-receipt"></i> Payment Status</div>
            <div class="detail-value">
                @if(strtolower($purchase->payment_status) === 'paid')
                    <span class="chip" style="background: rgba(16, 185, 129, 0.15) !important; color: #34D399 !important; border-color: rgba(16, 185, 129, 0.35) !important;">
                        <i class="fa-solid fa-circle-check"></i> Paid (Full)
                    </span>
                @elseif(strtolower($purchase->payment_status) === 'partial')
                    <span class="chip" style="background: rgba(245, 158, 11, 0.15) !important; color: #FBBF24 !important; border-color: rgba(245, 158, 11, 0.35) !important;">
                        <i class="fa-solid fa-clock"></i> Partial Payment
                    </span>
                @else
                    <span class="chip" style="background: rgba(239, 68, 68, 0.15) !important; color: #F87171 !important; border-color: rgba(239, 68, 68, 0.35) !important;">
                        <i class="fa-solid fa-circle-xmark"></i> Unpaid / Due
                    </span>
                @endif
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-barcode"></i> Cheque / Ref No.</div>
            <div class="detail-value" style="font-family: monospace;">{{ $purchase->reference_no ?: '—' }}</div>
        </div>

        <div class="detail-item detail-item-full">
            <div class="detail-label"><i class="fa-solid fa-note-sticky"></i> Remarks / Notes</div>
            <div class="detail-value {{ $purchase->remarks ? '' : 'empty' }}">
                {{ $purchase->remarks ?: 'No remarks specified' }}
            </div>
        </div>
    </div>

    <div class="form-actions">
        @if($purchase->property_id)
            <a href="{{ route('property-sales.create', ['property_id' => $purchase->property_id]) }}" class="btn-gold" style="background:#10B981 !important; border-color:#059669 !important;">
                <i class="fa-solid fa-handshake"></i> Sell Property Directly
            </a>
            <a href="{{ route('bookings.create', ['property_id' => $purchase->property_id]) }}" class="btn-gold" style="background:#F59E0B !important; border-color:#D97706 !important;">
                <i class="fa-solid fa-bookmark"></i> Book Property
            </a>
        @endif
        <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit Property Buy
        </a>
        <a href="{{ route('purchases.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection
