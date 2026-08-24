@extends('admin.layouts.app')
@section('title', 'View Purchase')
@section('page-title', 'Purchase Management')
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
    max-width: 860px; margin-left: auto; margin-right: auto;
}

.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
@media(max-width:576px){ .detail-grid { grid-template-columns: 1fr; } }

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

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-unpaid   { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-partial  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-paid     { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-active   { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

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
        <h2>Purchase Details</h2>
        <p>Full details for: <strong>{{ $purchase->item_name }}</strong></p>
    </div>
</div>

<div class="card-box">
    <div class="detail-grid">

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building"></i> Firm</div>
            <div class="detail-value">{{ $purchase->firm_names }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-box-open"></i> Item Name</div>
            <div class="detail-value">{{ $purchase->item_name }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-truck-field"></i> Vendor</div>
            <div class="detail-value">
                {{ $purchase->vendor ? $purchase->vendor->name : '—' }}
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar"></i> Purchase Date</div>
            <div class="detail-value">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-hashtag"></i> Quantity</div>
            <div class="detail-value">{{ $purchase->quantity ?? '—' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-indian-rupee-sign"></i> Purchase Amount</div>
            <div class="detail-value" style="color:#FBBF24 !important;">₹{{ number_format($purchase->purchase_amount, 2) }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-wallet"></i> Payment Mode</div>
            <div class="detail-value">
                @if($purchase->payment_mode)
                    <span style="background:rgba(255,255,255,0.08);color:#E2E8F0;padding:4px 10px;border-radius:6px;font-size:13px;font-weight:600;border:1px solid rgba(255,255,255,0.10);display:inline-block;">{{ $purchase->payment_mode }}</span>
                @else
                    <span style="color:#94A3B8;">—</span>
                @endif
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-circle-half-stroke"></i> Payment Status</div>
            <div class="detail-value">
                <span class="badge badge-{{ $purchase->payment_status ?? 'unpaid' }}">
                    {{ ucfirst($purchase->payment_status ?? 'unpaid') }}
                </span>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-file-invoice"></i> Reference No</div>
            <div class="detail-value">{{ $purchase->reference_no ?? '—' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-circle-dot"></i> Status</div>
            <div class="detail-value">
                <span class="badge badge-{{ $purchase->status ?? 'active' }}">
                    {{ ucfirst($purchase->status ?? 'active') }}
                </span>
            </div>
        </div>

        <div class="detail-item detail-item-full">
            <div class="detail-label"><i class="fa-solid fa-note-sticky"></i> Remarks</div>
            @if($purchase->remarks)
                <div class="detail-value" style="font-weight:400;font-size:14px;line-height:1.6;color:#CBD5E1 !important;">{{ $purchase->remarks }}</div>
            @else
                <div class="detail-value empty">No remarks provided</div>
            @endif
        </div>

    </div>

    <div class="form-actions">
        <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit Purchase
        </a>
        <a href="{{ route('purchases.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection
