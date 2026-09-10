@extends('admin.layouts.app')
@section('title', 'Booking Details')
@section('page-title', 'Booking Management')
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
    max-width: 900px; margin-left: auto; margin-right: auto;
}

.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; margin-bottom: 24px; }
@media(max-width:576px){ .detail-grid { grid-template-columns: 1fr; } }
.detail-item { border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 12px; }
.detail-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 5px; }
.detail-value { font-size: 15px; font-weight: 700; color: #FFFFFF !important; }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; }
.badge-pending { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-confirmed { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-cancelled { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.badge-unpaid { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-partial { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-paid { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }

/* Financial Summary Dark Glass */
.financial-summary {
    background: rgba(16, 22, 34, 0.55) !important;
    border: 1.5px solid rgba(245, 158, 11, 0.35) !important;
    border-radius: 16px !important;
    padding: 20px !important;
    margin-bottom: 26px !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
}
.financial-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 14px; }
.fin-box {
    background: rgba(16, 22, 34, 0.75) !important;
    backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 12px !important;
    padding: 14px 12px !important;
    text-align: center;
}
.fin-title { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; }
.fin-amount { font-size: 17px; font-weight: 800; letter-spacing: -0.2px; }
.fin-amount.gold { color: #FBBF24 !important; }
.fin-amount.green { color: #34D399 !important; }
.fin-amount.red { color: #F87171 !important; }
.fin-amount.blue { color: #60A5FA !important; }

.form-actions { display: flex; gap: 12px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }
.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 10px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    display: inline-flex; align-items: center; gap: 8px; text-decoration: none !important;
}
.btn-gold:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }
.btn-outline {
    border: 1px solid rgba(255, 255, 255, 0.15) !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #CBD5E1 !important; padding: 11px 24px; border-radius: 10px; text-decoration: none !important;
    font-size: 14px; font-weight: 600; transition: all .2s ease;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.10) !important; color: #FFFFFF !important; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Booking Details</h2>
        <p>Complete booking information, discount calculation and payment tracking.</p>
    </div>
</div>

<div class="card-box">
    <!-- Financial Overview Summary -->
    <div class="financial-summary">
        <div class="financial-grid">
            <div class="fin-box">
                <div class="fin-title">Total Price</div>
                <div class="fin-amount blue">{{ $booking->total_amount ? '₹'.number_format($booking->total_amount, 2) : ($booking->property?->price ? '₹'.number_format($booking->property->price, 2) : '-') }}</div>
            </div>
            <div class="fin-box">
                <div class="fin-title">Discount (-)</div>
                <div class="fin-amount gold">
                    @if($booking->discount_amount > 0)
                        ₹{{ number_format($booking->discount_amount, 2) }}
                        @if($booking->discount_type === 'percentage') ({{ $booking->discount_value }}%) @endif
                    @else
                        ₹0.00
                    @endif
                </div>
            </div>
            <div class="fin-box">
                <div class="fin-title">Net Payable</div>
                <div class="fin-amount blue">{{ $booking->final_amount ? '₹'.number_format($booking->final_amount, 2) : ($booking->booking_amount ? '₹'.number_format($booking->booking_amount, 2) : '-') }}</div>
            </div>
            <div class="fin-box">
                <div class="fin-title">Paid / Booking</div>
                <div class="fin-amount green">{{ $booking->booking_amount ? '₹'.number_format($booking->booking_amount, 2) : '₹0.00' }}</div>
            </div>
            <div class="fin-box">
                <div class="fin-title">Balance Due</div>
                <div class="fin-amount red">{{ $booking->remaining_amount !== null ? '₹'.number_format($booking->remaining_amount, 2) : '₹0.00' }}</div>
            </div>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">Firm</div>
            <div class="detail-value">{{ $booking->firm->firm_name ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Property</div>
            <div class="detail-value">
                {{ $booking->property->property_name ?? '-' }}
                @if($booking->property?->unit_no) <span style="font-size:12px;color:#94A3B8;">(Unit: {{ $booking->property->unit_no }})</span> @endif
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Customer</div>
            <div class="detail-value">{{ $booking->customer->name ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Broker</div>
            <div class="detail-value">{{ $booking->broker->name ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Booking Date</div>
            <div class="detail-value">{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') : '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Agreement Date</div>
            <div class="detail-value">{{ $booking->agreement_date ? \Carbon\Carbon::parse($booking->agreement_date)->format('d M Y') : '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Payment Mode</div>
            <div class="detail-value">{{ $booking->paymentMode->name ?? ($booking->payment_mode ?: '-') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Transaction Reference</div>
            <div class="detail-value">{{ $booking->transaction_ref ?: '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Booking Status</div>
            <div class="detail-value"><span class="badge badge-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Payment Status</div>
            <div class="detail-value"><span class="badge badge-{{ $booking->payment_status }}">{{ ucfirst($booking->payment_status) }}</span></div>
        </div>
        <div class="detail-item" style="grid-column:span 2;">
            <div class="detail-label">Remarks</div>
            <div class="detail-value" style="font-weight:500;color:#CBD5E1;">{{ $booking->remarks ?: '-' }}</div>
        </div>
    </div>
    <div class="form-actions">
        <a href="{{ route('bookings.receipt-pdf', $booking->id) }}" target="_blank" class="btn-gold" style="background: rgba(252,105,0,0.18) !important; border-color: rgba(252,105,0,0.45) !important; color: #FF8A3D !important; box-shadow: 0 4px 14px rgba(252,105,0,0.25);">
            <i class="fa-solid fa-file-pdf"></i> Print / PDF Slip
        </a>
        <a href="{{ route('bookings.edit', $booking->id) }}" class="btn-gold"><i class="fa-regular fa-pen-to-square"></i> Edit Booking</a>
        <a href="{{ route('bookings.index') }}" class="btn-outline">Back to List</a>
    </div>
</div>
@endsection
