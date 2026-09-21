@extends('admin.layouts.app')
@section('title', 'Income Details - Agriculture')
@section('page-title', 'Agriculture Management')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-back {
    background: rgba(255, 255, 255, 0.08) !important; color: #E2E8F0 !important; padding: 10px 20px;
    border-radius: 10px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    transition: all .25s ease;
}
.btn-back:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; }

.btn-edit-head {
    background: #10B981 !important; color: #FFFFFF !important; padding: 10px 20px;
    border-radius: 10px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #34D399 !important;
    transition: all .25s ease;
}

.details-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 28px; }
@media (max-width: 900px) { .details-grid { grid-template-columns: 1fr; } }

.glass-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 28px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
}

.section-title { font-size: 14px; font-weight: 800; color: #34D399; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }

.info-table { width: 100%; border-collapse: collapse; }
.info-table tr { border-bottom: 1px solid rgba(255, 255, 255, 0.07); }
.info-table tr:last-child { border-bottom: none; }
.info-table th { padding: 12px 14px; color: #94A3B8; font-size: 12px; text-transform: uppercase; letter-spacing: 0.6px; text-align: left; width: 38%; font-weight: 700; }
.info-table td { padding: 12px 14px; color: #FFFFFF; font-size: 14px; font-weight: 600; }

.amount-hero {
    background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.25);
    border-radius: 18px; padding: 24px; text-align: center; margin-bottom: 20px;
}
.amount-hero span { font-size: 12px; font-weight: 800; color: #34D399; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 6px; }
.amount-hero h2 { font-size: 32px; font-weight: 800; color: #FFFFFF; margin: 0; }

.status-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.st-paid { background: rgba(34, 197, 94, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.st-partial { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.st-pending { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2><i class="fa-solid fa-wheat-awn" style="color: #34D399;"></i> Crop Sale / Income Details</h2>
        <p>Sale voucher for {{ $income->crop_product }} &bull; {{ $income->farm?->farm_name ?: 'Farm' }}</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('agriculture.incomes.index') }}" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back</a>
        <a href="{{ route('agriculture.incomes.edit', $income->id) }}" class="btn-edit-head"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
    </div>
</div>

<div class="details-grid">
    <div class="glass-card">
        <div class="section-title"><i class="fa-solid fa-circle-info"></i> Sale Transaction Information</div>
        <table class="info-table">
            <tr>
                <th>Crop / Produce Sold</th>
                <td><strong style="color:#FFFFFF;font-size:16px;">{{ $income->crop_product }}</strong></td>
            </tr>
            <tr>
                <th>Income Type</th>
                <td><span style="color:#60A5FA;">{{ $income->income_type }}</span></td>
            </tr>
            <tr>
                <th>Farm / Origin Land</th>
                <td>
                    @if($income->farm)
                        <a href="{{ route('agriculture.farms.show', $income->farm_id) }}" style="color:#34D399;text-decoration:none;font-weight:700;">
                            <i class="fa-solid fa-tractor"></i> {{ $income->farm->farm_name }}
                        </a>
                        <div style="font-size:12px;color:#94A3B8;">{{ $income->farm->village }}</div>
                    @else
                        <span style="color:#94A3B8;">General Farm</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Sale Date</th>
                <td>{{ $income->income_date ? \Carbon\Carbon::parse($income->income_date)->format('d F Y') : '—' }}</td>
            </tr>
            <tr>
                <th>Buyer / Mandi Trader</th>
                <td>
                    @if($income->customer)
                        <span style="color:#60A5FA;"><i class="fa-solid fa-user-tie"></i> {{ $income->customer->name }} ({{ $income->customer->phone }})</span>
                    @elseif($income->buyer_name)
                        {{ $income->buyer_name }}
                    @else
                        <span style="color:#94A3B8;">Direct Buyer</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Quantity & Rate</th>
                <td>
                    <span style="color:#FBBF24;font-weight:700;">{{ number_format($income->quantity, 2) }} {{ $income->unit }}</span>
                    &nbsp;@ ₹{{ number_format($income->rate, 2) }} / {{ $income->unit }}
                </td>
            </tr>
            <tr>
                <th>Invoice / Bill No</th>
                <td>{{ $income->invoice_no ?: '—' }}</td>
            </tr>
            <tr>
                <th>Notes / Remarks</th>
                <td>{{ $income->notes ?: '—' }}</td>
            </tr>
            <tr>
                <th>Created By</th>
                <td>{{ $income->creator?->name ?: 'System' }} &bull; <small style="color:#94A3B8;">{{ $income->created_at->format('d M Y, h:i A') }}</small></td>
            </tr>
        </table>
    </div>

    <div style="display: flex; flex-direction: column; gap: 20px;">
        <div class="glass-card">
            <div class="amount-hero">
                <span>Total Sale Value</span>
                <h2>₹{{ number_format($income->total_amount, 2) }}</h2>
                <div style="margin-top: 10px;">
                    @php
                        $stCls = 'st-pending';
                        if ($income->payment_status === 'Paid') $stCls = 'st-paid';
                        elseif ($income->payment_status === 'Partial') $stCls = 'st-partial';
                    @endphp
                    <span class="status-badge {{ $stCls }}">{{ $income->payment_status }}</span>
                </div>
            </div>

            <table class="info-table">
                <tr>
                    <th>Payment Received</th>
                    <td><strong style="color:#34D399;">₹{{ number_format($income->payment_received, 2) }}</strong></td>
                </tr>
                <tr>
                    <th>Pending Balance</th>
                    <td>
                        @if($income->pending_amount > 0)
                            <strong style="color:#F87171;">₹{{ number_format($income->pending_amount, 2) }}</strong>
                        @else
                            <span style="color:#34D399;">₹0.00 (Fully Paid)</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Payment Method</th>
                    <td>{{ $income->payment_method ?: 'Cash' }}</td>
                </tr>
                <tr>
                    <th>Payment Mode</th>
                    <td>{{ $income->paymentMode?->name ?: '—' }}</td>
                </tr>
                <tr>
                    <th>Firm Name</th>
                    <td>{{ $income->firm_names }}</td>
                </tr>
            </table>
        </div>

        @if($income->attachment)
        <div class="glass-card" style="text-align: center;">
            <div class="section-title" style="justify-content: center;"><i class="fa-solid fa-paperclip"></i> Attached Document</div>
            <a href="{{ asset('storage/' . $income->attachment) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(52, 211, 153, 0.20); color: #34D399; padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: 700; border: 1px solid rgba(52, 211, 153, 0.35);">
                <i class="fa-solid fa-file-arrow-down"></i> Open Attachment / Slip
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
