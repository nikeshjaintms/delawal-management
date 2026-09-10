<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Agreement #{{ $propertySale->id }} - Delawala Management</title>
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

        /* ── Agreement Header Banner ── */
        .agreement-banner { background:#0F172A; color:#fff; border-radius:8px; padding:16px 20px; margin-bottom:22px; display:flex; justify-content:space-between; align-items:center; }
        .agreement-banner h2 { font-size:17px; font-weight:700; color:#F8FAFC; margin-bottom:4px; }
        .agreement-badge { background:rgba(252,105,0,0.25); color:#FF8A3D; font-size:11px; font-weight:700; padding:4px 12px; border-radius:4px; border:1px solid rgba(252,105,0,0.4); display:inline-block; }

        /* ── 2 Column Detail Grids ── */
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
        .finance-table td { padding:7px 0; font-size:12px; }
        .finance-table td.label { color:#475569; font-weight:500; }
        .finance-table td.value { text-align:right; font-weight:700; color:#0F1F35; }
        .finance-table tr.total td { font-size:14px; font-weight:800; border-top:2px solid #E2E8F0; padding-top:10px; }
        .finance-table tr.total td.value { color:#e05c00; }

        /* ── Terms / Notes Box ── */
        .terms-box { background:#F8FAFC; border:1px solid #E2E8F0; border-radius:6px; padding:12px 16px; margin-bottom:26px; font-size:10.5px; color:#475569; }
        .terms-box strong { color:#0F1F35; }

        /* ── Signatures ── */
        .auth-block { margin-top:36px; padding-top:14px; display:flex; justify-content:space-between; page-break-inside:avoid; }
        .auth-col { width:200px; text-align:center; }
        .auth-line { border-top:1.5px solid #0F1F35; margin-top:48px; padding-top:5px; font-size:10px; font-weight:700; color:#0F1F35; }

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
        <div class="rpt-title">Official Sales Agreement / Deed</div>
        <div class="rpt-date">Date: {{ $propertySale->sale_date ? \Carbon\Carbon::parse($propertySale->sale_date)->format('d M Y') : now()->format('d M Y') }}</div>
    </div>
</div>

<div class="agreement-banner">
    <div>
        <h2>Sales Agreement #SA-{{ str_pad($propertySale->id, 5, '0', STR_PAD_LEFT) }}</h2>
        <div style="font-size:11px; color:#94A3B8;">
            Firm: {{ $propertySale->firm->firm_name ?? 'Delawala Management' }} &nbsp;|&nbsp; Status: {{ ucfirst($propertySale->sale_status) }}
        </div>
    </div>
    <div>
        <span class="agreement-badge">Payment: {{ strtoupper($propertySale->payment_status ?: 'Pending') }}</span>
    </div>
</div>

<div class="grid-2">
    <!-- Property Details -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Property / Unit Information</div>
        <div class="info-row">
            <span class="info-label">Property Name:</span>
            <span class="info-value">{{ $propertySale->property->property_name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Property Code:</span>
            <span class="info-value">{{ $propertySale->property->property_code ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Property Type:</span>
            <span class="info-value">{{ $propertySale->property->propertyType->name ?? 'General Property' }}</span>
        </div>
        @if($propertySale->property && $propertySale->property->project)
        <div class="info-row">
            <span class="info-label">Project:</span>
            <span class="info-value">{{ $propertySale->property->project->project_name }}</span>
        </div>
        @endif
        @if($propertySale->property && $propertySale->property->propertyMaster)
        <div class="info-row">
            <span class="info-label">Property Master:</span>
            <span class="info-value">{{ $propertySale->property->propertyMaster->property_name }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Size / Area:</span>
            <span class="info-value">{{ $propertySale->property && $propertySale->property->size ? $propertySale->property->size . ' ' . ($propertySale->property->size_unit ?? 'sq.ft') : '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Location / City:</span>
            <span class="info-value">{{ $propertySale->property->location ?? ($propertySale->property->city ?? '-') }}</span>
        </div>
    </div>

    <!-- Customer & Broker Details -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Buyer &amp; Intermediary Details</div>
        <div class="info-row">
            <span class="info-label">Buyer / Customer:</span>
            <span class="info-value">{{ $propertySale->customer->name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Customer Phone:</span>
            <span class="info-value">{{ $propertySale->customer->phone ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Customer Email:</span>
            <span class="info-value">{{ $propertySale->customer->email ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Customer Address:</span>
            <span class="info-value">{{ $propertySale->customer->address ?? ($propertySale->customer->city ?? '-') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Facilitating Broker:</span>
            <span class="info-value">{{ $propertySale->broker->name ?? 'Direct Sale (No Broker)' }}</span>
        </div>
        @if($propertySale->broker && $propertySale->broker->phone)
        <div class="info-row">
            <span class="info-label">Broker Contact:</span>
            <span class="info-value">{{ $propertySale->broker->phone }}</span>
        </div>
        @endif
    </div>
</div>

<div class="finance-card">
    <div class="finance-header">Financial Consideration &amp; Payment Breakdown</div>
    <div class="finance-body">
        <table class="finance-table">
            <tr>
                <td class="label">Agreed Total Sale Consideration Amount</td>
                <td class="value">₹{{ number_format($propertySale->sale_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Initial Booking / Advance Token Amount Received</td>
                <td class="value" style="color:#059669;">- ₹{{ number_format($propertySale->booking_amount, 2) }}</td>
            </tr>
            <tr class="total">
                <td class="label">Net Outstanding Balance Consideration Due</td>
                <td class="value">₹{{ number_format($propertySale->remaining_amount, 2) }}</td>
            </tr>
        </table>
    </div>
</div>

@if($propertySale->note)
<div class="terms-box">
    <strong>Terms &amp; Special Conditions / Remarks:</strong><br>
    {{ $propertySale->note }}
</div>
@endif

<div class="terms-box" style="font-size:10px; line-height:1.4;">
    <strong>Declaration:</strong> This official sales agreement represents the binding commercial terms agreed between Delawala Management and the Purchaser named herein. Full conveyance and deed handover shall be executed upon complete clearance of all outstanding balances.
</div>

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Purchaser / Buyer Signature</div>
    </div>
    @if($propertySale->broker)
    <div class="auth-col">
        <div class="auth-line">Broker / Witness Signature</div>
    </div>
    @endif
    <div class="auth-col">
        <div class="auth-line">Authorized Signatory (Delawala)</div>
    </div>
</div>

<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; Agreement #SA-{{ str_pad($propertySale->id, 5, '0', STR_PAD_LEFT) }}</span>
    <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
