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
    max-width: 1060px; margin-left: auto; margin-right: auto;
}

.section-head {
    font-size: 13px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 18px; padding-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap;
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

.form-actions { display: flex; align-items: center; gap: 14px; margin-top: 10px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); flex-wrap: wrap; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 12px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all .25s ease;
    box-shadow: 0 4px 18px rgba(37,99,235,0.38); text-decoration: none !important;
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37,99,235,0.52); }

.btn-emerald {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important; color: #FFFFFF !important; padding: 10px 20px;
    border-radius: 12px; font-size: 13.5px; font-weight: 700; border: 1px solid #34D399 !important;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all .25s ease;
    box-shadow: 0 4px 16px rgba(16,185,129,0.35); text-decoration: none !important;
}
.btn-emerald:hover { background: #059669 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(16,185,129,0.5); }

.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 22px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-2px); }

.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 18px; border-radius: 12px; margin-bottom: 22px; font-size: 14px; display: flex; align-items: center; gap: 10px; font-weight: 600; }

/* ── Payments History Table ── */
.payment-table {
    width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 12px;
    border: 1px solid rgba(255, 255, 255, 0.10); border-radius: 14px; overflow: hidden;
}
.payment-table th {
    background: rgba(15, 23, 42, 0.85); color: #94A3B8; font-size: 12px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.6px; padding: 12px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.10); text-align: left;
}
.payment-table td {
    padding: 14px 16px; font-size: 13.5px; color: #E2E8F0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06); background: rgba(16, 22, 34, 0.50); vertical-align: middle;
}
.payment-table tr:last-child td { border-bottom: none; }
.payment-table tr:hover td { background: rgba(30, 41, 59, 0.70); }

/* Mode badge */
.mode-badge {
    display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 8px;
    font-size: 12px; font-weight: 700; border: 1px solid;
}
.mode-cash { background: rgba(16, 185, 129, 0.15); color: #34D399; border-color: rgba(16, 185, 129, 0.35); }
.mode-cheque { background: rgba(245, 158, 11, 0.15); color: #FBBF24; border-color: rgba(245, 158, 11, 0.35); }
.mode-bank { background: rgba(59, 130, 246, 0.15); color: #60A5FA; border-color: rgba(59, 130, 246, 0.35); }
.mode-upi { background: rgba(168, 85, 247, 0.15); color: #C084FC; border-color: rgba(168, 85, 247, 0.35); }
.mode-other { background: rgba(148, 163, 184, 0.15); color: #CBD5E1; border-color: rgba(148, 163, 184, 0.35); }

/* Modal styles */
.modal-overlay {
    position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px);
    display: none; align-items: center; justify-content: center; z-index: 99999; padding: 20px;
}
.modal-overlay.active { display: flex; }
.modal-box {
    background: #0F172A; border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 20px;
    width: 100%; max-width: 580px; box-shadow: 0 24px 60px rgba(0,0,0,0.6); overflow: hidden;
    animation: modalIn .25s ease-out;
}
@keyframes modalIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
.modal-header {
    padding: 20px 24px; background: rgba(30, 41, 59, 0.70); border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex; justify-content: space-between; align-items: center;
}
.modal-header h3 { margin: 0; font-size: 18px; font-weight: 800; color: #FFFFFF; display: flex; align-items: center; gap: 8px; }
.modal-close-btn { background: none; border: none; color: #94A3B8; font-size: 20px; cursor: pointer; transition: color .2s; }
.modal-close-btn:hover { color: #FFFFFF; }
.modal-body { padding: 24px; }
.form-ctrl {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.85); border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 10px; color: #FFFFFF; font-size: 13.5px; box-sizing: border-box; outline: none; transition: border-color .2s;
}
.form-ctrl:focus { border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2); }
.form-lbl { display: block; font-size: 12px; font-weight: 700; color: #CBD5E1; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Property Buy Details</h2>
        <p>Overview & Payment History: <strong>{{ $purchase->display_name }}</strong></p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        @if((float)($purchase->due_amount ?? 0) > 0)
            <button type="button" class="btn-emerald" onclick="openPaymentModal()">
                <i class="fa-solid fa-hand-holding-dollar"></i> + Record Payment / Installment
            </button>
        @else
            <button type="button" class="btn-emerald" onclick="openPaymentModal()" style="opacity: 0.85;">
                <i class="fa-solid fa-plus"></i> + Add Payment Entry
            </button>
        @endif
        <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="card-box">
    {{-- ── Property Details ── --}}
    <div class="section-head">
        <span><i class="fa-solid fa-building"></i> Property Details</span>
        <span style="font-size: 12px; color: #94A3B8; font-weight: 600;">Code: <strong style="color: #60A5FA;">{{ $purchase->property_code ?: '—' }}</strong></span>
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
            <div class="detail-label"><i class="fa-solid fa-calendar-day"></i> Purchase Date</div>
            <div class="detail-value">{{ $purchase->purchase_date ? $purchase->purchase_date->format('d/m/Y') : '—' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-location-dot"></i> Location</div>
            <div class="detail-value">{{ $purchase->location ?: '—' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-ruler-combined"></i> Area</div>
            <div class="detail-value">
                @if($purchase->area)
                    <span style="color: #34D399; font-size: 16px; font-weight: 800;">{{ number_format($purchase->area, 2) }}</span>
                    <span style="color: #94A3B8; font-size: 13px; margin-left: 4px;">{{ $purchase->area_unit ?? 'Sq.Ft' }}</span>
                @else
                    <span class="empty">—</span>
                @endif
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user-tie"></i> Vendor</div>
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
            <div class="detail-label"><i class="fa-solid fa-receipt"></i> Overall Payment Status</div>
            <div class="detail-value">
                @if(strtolower($purchase->payment_status) === 'paid')
                    <span class="chip" style="background: rgba(16, 185, 129, 0.15) !important; color: #34D399 !important; border-color: rgba(16, 185, 129, 0.35) !important;">
                        <i class="fa-solid fa-circle-check"></i> Paid (Full 100%)
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
    <div class="section-head" style="margin-top: 24px; color: #60A5FA !important;">
        <span><i class="fa-solid fa-indian-rupee-sign"></i> Purchase Price & Financial Breakdown</span>
    </div>

    <div style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.12) 0%, rgba(16, 185, 129, 0.08) 100%); border: 1.5px solid rgba(255, 255, 255, 0.12); border-radius: 18px; padding: 22px; margin-bottom: 24px;">
        <div class="detail-grid-3">
            <!-- 1. Total Purchase Price -->
            <div class="detail-item" style="background: rgba(16, 22, 34, 0.85) !important; border-color: rgba(96, 165, 250, 0.4) !important;">
                <div class="detail-label" style="color: #60A5FA !important;"><i class="fa-solid fa-money-bill-wave" style="color:#60A5FA !important;"></i> Total Purchase Deal Price</div>
                <div class="detail-value" style="font-size: 22px; font-weight: 800; color: #60A5FA !important;">
                    ₹{{ number_format($purchase->purchase_amount ?? 0, 2) }}
                </div>
            </div>

            <!-- 2. Paid Amount -->
            <div class="detail-item" style="background: rgba(16, 22, 34, 0.85) !important; border-color: rgba(52, 211, 153, 0.4) !important;">
                <div class="detail-label" style="color: #34D399 !important;"><i class="fa-solid fa-circle-check" style="color:#34D399 !important;"></i> Total Amount Paid So Far</div>
                <div class="detail-value" style="font-size: 22px; font-weight: 800; color: #34D399 !important;">
                    ₹{{ number_format($purchase->paid_amount ?? 0, 2) }}
                </div>
            </div>

            <!-- 3. Due Balance -->
            <div class="detail-item" style="background: rgba(16, 22, 34, 0.85) !important; border-color: rgba(248, 113, 113, 0.4) !important;">
                <div class="detail-label" style="color: #F87171 !important;"><i class="fa-solid fa-clock-rotate-left" style="color:#F87171 !important;"></i> Remaining Due Balance</div>
                <div class="detail-value" style="font-size: 22px; font-weight: 800; color: #F87171 !important;">
                    ₹{{ number_format($purchase->due_amount ?? 0, 2) }}
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div style="margin-top: 6px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <span style="font-size: 12px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Payment Progress ({{ $purchase->paid_percentage }}% Cleared)</span>
                <span style="font-size: 12px; color: #CBD5E1; font-weight: 700;">₹{{ number_format($purchase->paid_amount ?? 0, 2) }} of ₹{{ number_format($purchase->purchase_amount ?? 0, 2) }}</span>
            </div>
            <div style="width: 100%; height: 9px; background: rgba(255,255,255,0.08); border-radius: 999px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
                <div style="width: {{ $purchase->paid_percentage }}%; height: 100%; background: linear-gradient(90deg, #10B981, #34D399); border-radius: 999px; transition: width 0.3s ease;"></div>
            </div>
        </div>
    </div>

    {{-- ── Payment History / Installments ── --}}
    <div class="section-head" style="margin-top: 30px; color: #34D399 !important;">
        <span><i class="fa-solid fa-receipt"></i> Payment Installments & Transaction History ({{ $purchase->payments->count() }})</span>
        <button type="button" class="btn-emerald" onclick="openPaymentModal()" style="font-size: 12.5px; padding: 6px 14px;">
            <i class="fa-solid fa-plus"></i> + Record Next Payment
        </button>
    </div>

    @if($purchase->payments && $purchase->payments->count() > 0)
        <div style="overflow-x: auto;">
            <table class="payment-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Payment Date</th>
                        <th>Amount (₹)</th>
                        <th>Payment Mode</th>
                        <th>Cheque / Ref No.</th>
                        <th>Bank / Branch</th>
                        <th>Remarks / Note</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->payments as $idx => $pmt)
                        @php
                            $m = strtolower($pmt->payment_mode);
                            $badgeClass = 'mode-other';
                            $icon = 'fa-solid fa-money-bill';
                            if (str_contains($m, 'cash')) {
                                $badgeClass = 'mode-cash';
                                $icon = 'fa-solid fa-money-bill-wave';
                            } elseif (str_contains($m, 'cheque') || str_contains($m, 'check')) {
                                $badgeClass = 'mode-cheque';
                                $icon = 'fa-solid fa-money-check-dollar';
                            } elseif (str_contains($m, 'bank') || str_contains($m, 'rtgs') || str_contains($m, 'neft') || str_contains($m, 'transfer')) {
                                $badgeClass = 'mode-bank';
                                $icon = 'fa-solid fa-building-columns';
                            } elseif (str_contains($m, 'upi') || str_contains($m, 'gpay') || str_contains($m, 'phonepe')) {
                                $badgeClass = 'mode-upi';
                                $icon = 'fa-solid fa-mobile-screen-button';
                            }
                        @endphp
                        <tr>
                            <td>
                                <span style="font-weight: 800; color: #94A3B8;">
                                    {{ $idx === 0 ? '1st (Advance)' : ($idx + 1) . 'th Installment' }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: #FFFFFF;">{{ $pmt->payment_date ? $pmt->payment_date->format('d/m/Y') : '—' }}</strong>
                            </td>
                            <td>
                                <span style="font-size: 15px; font-weight: 800; color: #34D399;">
                                    ₹{{ number_format($pmt->amount, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="mode-badge {{ $badgeClass }}">
                                    <i class="{{ $icon }}"></i> {{ $pmt->payment_mode ?: 'Cash' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-family: monospace; color: #E2E8F0;">{{ $pmt->reference_no ?: '—' }}</span>
                            </td>
                            <td>
                                <span style="color: #CBD5E1;">{{ $pmt->bank_name ?: '—' }}</span>
                            </td>
                            <td>
                                <span style="color: #94A3B8; font-size: 13px;">{{ $pmt->remarks ?: '—' }}</span>
                            </td>
                            <td style="text-align: right;">
                                <form action="{{ route('purchases.payments.destroy', [$purchase->id, $pmt->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment record of ₹{{ number_format($pmt->amount, 2) }}?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.35); color: #F87171; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all .2s;" title="Delete Payment Record">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="background: rgba(15, 23, 42, 0.6); border: 1px dashed rgba(255, 255, 255, 0.15); border-radius: 14px; padding: 28px; text-align: center;">
            <i class="fa-solid fa-receipt" style="font-size: 32px; color: #64748B; margin-bottom: 10px;"></i>
            <h4 style="margin: 0 0 6px 0; color: #FFFFFF; font-size: 15px;">No installment payments recorded yet</h4>
            <p style="margin: 0 0 16px 0; color: #94A3B8; font-size: 13px;">Click below to record your initial or subsequent payment for this property.</p>
            <button type="button" class="btn-emerald" onclick="openPaymentModal()">
                <i class="fa-solid fa-plus"></i> + Record 1st Payment / Advance
            </button>
        </div>
    @endif

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

{{-- ── RECORD PAYMENT MODAL ── --}}
<div id="paymentModal" class="modal-overlay" onclick="handleModalBackdropClick(event)">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-hand-holding-dollar" style="color: #34D399;"></i> Record Payment Installment</h3>
            <button type="button" class="modal-close-btn" onclick="closePaymentModal()">&times;</button>
        </div>

        <form action="{{ route('purchases.payments.store', $purchase->id) }}" method="POST">
            @csrf
            <div class="modal-body">
                <!-- Info banner -->
                <div style="background: rgba(30, 41, 59, 0.6); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 12px 16px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; color: #94A3B8; font-weight: 700;">Property</div>
                        <div style="font-size: 14px; font-weight: 800; color: #FFFFFF;">{{ $purchase->display_name }}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 11px; text-transform: uppercase; color: #F87171; font-weight: 700;">Remaining Due</div>
                        <div style="font-size: 16px; font-weight: 800; color: #F87171;">₹{{ number_format($purchase->due_amount ?? 0, 2) }}</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <!-- Payment Date -->
                    <div>
                        <label class="form-lbl">Payment Date <span style="color: #F87171;">*</span></label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-ctrl" required>
                    </div>

                    <!-- Payment Amount -->
                    <div>
                        <label class="form-lbl">Payment Amount (₹) <span style="color: #F87171;">*</span></label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 9px; color: #34D399; font-weight: 800;">₹</span>
                            <input type="number" step="0.01" min="0.01" name="amount" id="modalPaymentAmount"
                                   value="{{ ($purchase->due_amount ?? 0) > 0 ? $purchase->due_amount : '' }}"
                                   class="form-ctrl" style="padding-left: 28px; font-weight: 800; color: #34D399;" required placeholder="0.00">
                        </div>
                    </div>
                </div>

                @if((float)($purchase->due_amount ?? 0) > 0)
                    <div style="margin-top: -6px; margin-bottom: 14px; display: flex; gap: 8px;">
                        <button type="button" onclick="fillExactDue({{ (float)$purchase->due_amount }})" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); color: #34D399; font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 6px; cursor: pointer;">
                            <i class="fa-solid fa-circle-check"></i> Fill Remaining Due (₹{{ number_format($purchase->due_amount, 2) }})
                        </button>
                    </div>
                @endif

                <!-- Payment Mode -->
                <div style="margin-bottom: 14px;">
                    <label class="form-lbl">Payment Mode <span style="color: #F87171;">*</span></label>
                    <select name="payment_mode" id="modalPaymentMode" class="form-ctrl" onchange="togglePaymentModeFields(this.value)" required>
                        <option value="Cash">💵 Cash</option>
                        <option value="Cheque">📝 Cheque / Check</option>
                        <option value="Bank Transfer / RTGS / NEFT">🏦 Bank Transfer / RTGS / NEFT</option>
                        <option value="UPI">📱 UPI (GPay / PhonePe / Paytm)</option>
                        <option value="Demand Draft">📜 Demand Draft (DD)</option>
                        <option value="Other">✨ Other</option>
                        @if(isset($paymentModes))
                            @foreach($paymentModes as $pm)
                                @if(!in_array($pm->name, ['Cash', 'Cheque', 'Bank Transfer / RTGS / NEFT', 'UPI', 'Demand Draft', 'Other']))
                                    <option value="{{ $pm->name }}">{{ $pm->name }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Cheque / Ref No & Bank Name -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div>
                        <label class="form-lbl" id="modalRefLabel">Cheque / Ref / UTR No.</label>
                        <input type="text" name="reference_no" id="modalRefNo" class="form-ctrl" placeholder="e.g. CHQ-482910">
                    </div>
                    <div>
                        <label class="form-lbl">Bank Name / Branch</label>
                        <input type="text" name="bank_name" class="form-ctrl" placeholder="e.g. HDFC Bank, Surat">
                    </div>
                </div>

                <!-- Remarks -->
                <div style="margin-bottom: 18px;">
                    <label class="form-lbl">Remarks / Payment Notes</label>
                    <textarea name="remarks" rows="2" class="form-ctrl" placeholder="e.g. 2nd installment paid via Cheque"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" class="btn-outline" onclick="closePaymentModal()">Cancel</button>
                    <button type="submit" class="btn-emerald" style="padding: 10px 24px;">
                        <i class="fa-solid fa-check"></i> Save Payment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openPaymentModal() {
    document.getElementById('paymentModal').classList.add('active');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.remove('active');
}

function handleModalBackdropClick(event) {
    if (event.target === document.getElementById('paymentModal')) {
        closePaymentModal();
    }
}

function fillExactDue(amount) {
    document.getElementById('modalPaymentAmount').value = amount.toFixed(2);
}

function togglePaymentModeFields(mode) {
    const refLabel = document.getElementById('modalRefLabel');
    const refNo = document.getElementById('modalRefNo');
    if (mode === 'Cheque') {
        refLabel.innerText = 'Cheque Number *';
        refNo.placeholder = 'e.g. 6 Digit Cheque #';
    } else if (mode.includes('UPI')) {
        refLabel.innerText = 'UPI Transaction / Ref ID';
        refNo.placeholder = 'e.g. UPI Ref # 3291823901';
    } else if (mode.includes('Bank') || mode.includes('RTGS') || mode.includes('NEFT')) {
        refLabel.innerText = 'UTR / Transfer Ref No.';
        refNo.placeholder = 'e.g. UTR / Ref No.';
    } else {
        refLabel.innerText = 'Reference / Receipt No.';
        refNo.placeholder = 'Optional Reference';
    }
}
</script>
@endsection
