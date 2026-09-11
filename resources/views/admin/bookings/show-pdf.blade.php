<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation #BK-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }} - Delawala Management</title>
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Segoe UI',Arial,sans-serif; font-size:11.5px; color:#0F1F35; background:#fff; padding:28px; line-height:1.45; }

        /* ── Header ── */
        .rpt-header { display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:16px; margin-bottom:20px; border-bottom:2.5px solid #fc6900ff; }
        .co-name  { font-size:24px; font-weight:800; color:#0F1F35; letter-spacing:0.4px; }
        .co-sub   { font-size:10px; color:#fc6900ff; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-top:3px; }
        .rpt-meta { text-align:right; }
        .rpt-meta .rpt-title { font-size:17px; font-weight:800; color:#0F1F35; margin-bottom:4px; }
        .rpt-meta .rpt-date  { font-size:11px; color:#64748B; }

        /* ── Header Banner ── */
        .banner { background:#0F172A; color:#fff; border-radius:8px; padding:16px 20px; margin-bottom:22px; display:flex; justify-content:space-between; align-items:center; }
        .banner h2 { font-size:17px; font-weight:700; color:#F8FAFC; margin-bottom:4px; }
        .token-badge { background:rgba(16,185,129,0.25); color:#34D399; font-size:11px; font-weight:700; padding:4px 12px; border-radius:4px; border:1px solid rgba(16,185,129,0.4); display:inline-block; }

        /* ── 2 Column Grid ── */
        .grid-2 { display:flex; gap:18px; margin-bottom:20px; }
        .grid-col { flex:1; border:1px solid #E2E8F0; border-radius:8px; padding:14px; background:#F8FAFC; }
        .col-heading { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#fc6900ff; margin-bottom:10px; border-bottom:1px solid #E2E8F0; padding-bottom:4px; }

        .info-row { display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px dashed #E2E8F0; font-size:11px; }
        .info-row:last-child { border-bottom:none; }
        .info-label { color:#64748B; font-weight:500; }
        .info-value { color:#0F1F35; font-weight:700; text-align:right; }

        /* ── Financial Summary Box ── */
        .finance-card { border:1.5px solid #CBD5E1; border-radius:8px; overflow:hidden; margin-bottom:22px; }
        .finance-header { background:#1E293B; color:#fff; padding:10px 16px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; }
        .finance-body { padding:14px 16px; background:#fff; }
        .finance-table { width:100%; border-collapse:collapse; }
        .finance-table td { padding:6px 0; font-size:11.5px; }
        .finance-table td.label { color:#475569; font-weight:500; }
        .finance-table td.value { text-align:right; font-weight:700; color:#0F1F35; }
        .finance-table tr.total td { font-size:13.5px; font-weight:800; border-top:2px solid #E2E8F0; padding-top:8px; }
        .finance-table tr.total td.value { color:#e05c00; }

        /* ── Notes / Terms ── */
        .terms-box { background:#F8FAFC; border:1px solid #E2E8F0; border-radius:6px; padding:12px 16px; margin-bottom:24px; font-size:10.5px; color:#475569; }
        .terms-box strong { color:#0F1F35; }

        /* ── Signatures ── */
        .auth-block { margin-top:36px; padding-top:14px; display:flex; justify-content:space-between; page-break-inside:avoid; }
        .auth-col { width:200px; text-align:center; }
        .auth-line { border-top:1.5px solid #0F1F35; margin-top:44px; padding-top:5px; font-size:10px; font-weight:700; color:#0F1F35; }

        /* ── Footer ── */
        .rpt-footer { margin-top:26px; padding-top:10px; border-top:1px solid #E5E7EB; display:flex; justify-content:space-between; color:#9CA3AF; font-size:9.5px; }

        @media print { body { padding:14px; } @page { margin:10mm; } }
    </style>
</head>
<body>

<div class="rpt-header">
    <div>
        <div class="co-name">Delawala</div>
        <div class="co-sub">Properties &amp; Management</div>
    </div>
    <div class="rpt-meta">
        <div class="rpt-title">Booking Confirmation Slip / Token Receipt</div>
        <div class="rpt-date">Booking Date: {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') : now()->format('d M Y') }}</div>
    </div>
</div>

<div class="banner">
    <div>
        <h2>Booking Ref #BK-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</h2>
        <div style="font-size:11px; color:#94A3B8;">
            Firm: {{ $booking->firm->firm_name ?? 'Delawala Management' }} &nbsp;|&nbsp; Type: <strong style="color:#FBBF24; text-transform:uppercase;">{{ $booking->booking_type ?? 'booking' }}</strong> &nbsp;|&nbsp; Status: {{ ucfirst($booking->status) }}
        </div>
    </div>
    <div>
        <span class="token-badge">TOKEN ADVANCE: ₹{{ number_format($booking->booking_amount, 2) }}</span>
    </div>
</div>

<div class="grid-2">
    <!-- Property Details -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Booked Unit / Property Details</div>
        <div class="info-row">
            <span class="info-label">Property / Unit:</span>
            <span class="info-value">{{ $booking->property->property_name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Property Code:</span>
            <span class="info-value">{{ $booking->property->property_code ?? '-' }}</span>
        </div>
        @if($booking->property && $booking->property->project)
        <div class="info-row">
            <span class="info-label">Project:</span>
            <span class="info-value">{{ $booking->property->project->project_name }}</span>
        </div>
        @endif
        @if($booking->property && $booking->property->propertyMaster)
        <div class="info-row">
            <span class="info-label">Property Master:</span>
            <span class="info-value">{{ $booking->property->propertyMaster->property_name }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Property Type:</span>
            <span class="info-value">{{ $booking->property->propertyType->name ?? 'Residential / Commercial' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Size / Area:</span>
            <span class="info-value">{{ $booking->property && $booking->property->size ? $booking->property->size . ' ' . ($booking->property->size_unit ?? 'sq.ft') : '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Location / City:</span>
            <span class="info-value">{{ $booking->property->location ?? ($booking->property->city ?? '-') }}</span>
        </div>
    </div>

    <!-- Customer Details -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Customer &amp; Intermediary Details</div>
        <div class="info-row">
            <span class="info-label">Customer Name:</span>
            <span class="info-value">{{ $booking->customer->name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Customer Phone:</span>
            <span class="info-value">{{ $booking->customer->phone ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Customer Email:</span>
            <span class="info-value">{{ $booking->customer->email ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Address:</span>
            <span class="info-value">{{ $booking->customer->address ?? ($booking->customer->city ?? '-') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Transaction Type:</span>
            <span class="info-value" style="text-transform:uppercase; color:#e05c00;">{{ $booking->booking_type ?? 'booking' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Broker / Agent:</span>
            <span class="info-value">{{ $booking->broker->name ?? 'Direct (No Broker)' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Mode:</span>
            <span class="info-value">{{ $booking->payment_mode ?: ($booking->paymentMode->name ?? 'Cash') }}</span>
        </div>
        @if($booking->transaction_ref)
        <div class="info-row">
            <span class="info-label">Transaction Ref:</span>
            <span class="info-value">{{ $booking->transaction_ref }}</span>
        </div>
        @endif
    </div>
</div>

<div class="finance-card">
    <div class="finance-header">Commercial Summary &amp; Booking Token Details</div>
    <div class="finance-body">
        <table class="finance-table">
            <tr>
                <td class="label">Gross Property Consideration Price</td>
                <td class="value">₹{{ number_format($booking->total_amount, 2) }}</td>
            </tr>
            @if($booking->discount_amount > 0)
            <tr>
                <td class="label">Applicable Discount / Concession ({{ $booking->discount_value }}{{ $booking->discount_type === 'percentage' ? '%' : ' Fixed' }})</td>
                <td class="value" style="color:#DC2626;">- ₹{{ number_format($booking->discount_amount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Net Agreed Final Selling Price</td>
                <td class="value">₹{{ number_format($booking->final_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Advance Booking / Token Amount Received (Paid)</td>
                <td class="value" style="color:#059669;">- ₹{{ number_format($booking->booking_amount, 2) }}</td>
            </tr>
            <tr class="total">
                <td class="label">Remaining Balance Consideration to be Paid</td>
                <td class="value">₹{{ number_format($booking->remaining_amount, 2) }}</td>
            </tr>
        </table>
    </div>
</div>

@if($booking->remarks)
<div class="terms-box">
    <strong>Remarks / Booking Notes:</strong><br>
    {{ $booking->remarks }}
</div>
@endif

<div class="terms-box" style="font-size:10px; line-height:1.4;">
    <strong>Terms &amp; Conditions:</strong>
    1. The token amount confirms the provisional reservation of the property unit.
    2. Final sale agreement deed shall be prepared by the agreed date: <strong>{{ $booking->agreement_date ? \Carbon\Carbon::parse($booking->agreement_date)->format('d M Y') : 'Prior to handover' }}</strong>.
    3. Failure to pay the balance installment as per schedule may result in cancellation per company policies.
</div>

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Customer / Applicant Signature</div>
    </div>
    @if($booking->broker)
    <div class="auth-col">
        <div class="auth-line">Broker / Witness Signature</div>
    </div>
    @endif
    <div class="auth-col">
        <div class="auth-line">Authorized Signatory (Delawala)</div>
    </div>
</div>

<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; Booking Slip #BK-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</span>
    <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
