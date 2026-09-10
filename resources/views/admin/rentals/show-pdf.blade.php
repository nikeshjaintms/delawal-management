<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenancy Agreement #{{ $rental->agreement_no ?: $rental->id }} - Delawala Management</title>
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

        /* ── Banner ── */
        .banner { background:#0F172A; color:#fff; border-radius:8px; padding:16px 20px; margin-bottom:22px; display:flex; justify-content:space-between; align-items:center; }
        .banner h2 { font-size:17px; font-weight:700; color:#F8FAFC; margin-bottom:4px; }
        .badge-pill { background:rgba(37,99,235,0.3); color:#60A5FA; font-size:11px; font-weight:700; padding:4px 12px; border-radius:4px; border:1px solid rgba(59,130,246,0.4); display:inline-block; }

        /* ── 2 Column Grid ── */
        .grid-2 { display:flex; gap:18px; margin-bottom:20px; }
        .grid-col { flex:1; border:1px solid #E2E8F0; border-radius:8px; padding:14px; background:#F8FAFC; }
        .col-heading { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#fc6900ff; margin-bottom:10px; border-bottom:1px solid #E2E8F0; padding-bottom:4px; }

        .info-row { display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px dashed #E2E8F0; font-size:11px; }
        .info-row:last-child { border-bottom:none; }
        .info-label { color:#64748B; font-weight:500; }
        .info-value { color:#0F1F35; font-weight:700; text-align:right; }

        /* ── Commercials Box ── */
        .finance-card { border:1.5px solid #CBD5E1; border-radius:8px; overflow:hidden; margin-bottom:22px; }
        .finance-header { background:#1E293B; color:#fff; padding:10px 16px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; }
        .finance-body { padding:14px 16px; background:#fff; }
        .finance-table { width:100%; border-collapse:collapse; }
        .finance-table td { padding:6px 0; font-size:11.5px; }
        .finance-table td.label { color:#475569; font-weight:500; }
        .finance-table td.value { text-align:right; font-weight:700; color:#0F1F35; }
        .finance-table tr.total td { font-size:13.5px; font-weight:800; border-top:2px solid #E2E8F0; padding-top:8px; }
        .finance-table tr.total td.value { color:#e05c00; }

        /* ── Remarks / Terms ── */
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
        <div class="rpt-title">Tenancy &amp; Lease Agreement Deed</div>
        <div class="rpt-date">Effective: {{ $rental->rent_start_date ? \Carbon\Carbon::parse($rental->rent_start_date)->format('d M Y') : now()->format('d M Y') }}</div>
    </div>
</div>

<div class="banner">
    <div>
        <h2>Lease Agreement #{{ $rental->agreement_no ?: ('RA-' . str_pad($rental->id, 5, '0', STR_PAD_LEFT)) }}</h2>
        <div style="font-size:11px; color:#94A3B8;">
            Lessor: {{ $rental->firm->firm_name ?? 'Delawala Management' }} &nbsp;|&nbsp; Status: {{ ucfirst($rental->rental_status) }}
        </div>
    </div>
    <div>
        <span class="badge-pill">MONTHLY: ₹{{ number_format($rental->rent_amount, 2) }}</span>
    </div>
</div>

<div class="grid-2">
    <!-- Property Info -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Demised Premises / Property</div>
        <div class="info-row">
            <span class="info-label">Property Name:</span>
            <span class="info-value">{{ $rental->property->property_name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Property Code:</span>
            <span class="info-value">{{ $rental->property->property_code ?? '-' }}</span>
        </div>
        @if($rental->property && $rental->property->project)
        <div class="info-row">
            <span class="info-label">Project:</span>
            <span class="info-value">{{ $rental->property->project->project_name }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Property Type:</span>
            <span class="info-value">{{ $rental->property->propertyType->name ?? 'Residential / Commercial' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Location / Address:</span>
            <span class="info-value">{{ $rental->property->location ?? ($rental->property->address ?? '-') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Size / Area:</span>
            <span class="info-value">{{ $rental->property && $rental->property->size ? $rental->property->size . ' ' . ($rental->property->size_unit ?? 'sq.ft') : '-' }}</span>
        </div>
    </div>

    <!-- Tenant Info -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Lessee / Tenant Details</div>
        <div class="info-row">
            <span class="info-label">Tenant Name:</span>
            <span class="info-value">{{ $rental->tenant_name ?: ($rental->tenant->name ?? '-') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mobile Number:</span>
            <span class="info-value">{{ $rental->tenant_mobile ?: ($rental->tenant->phone ?? '-') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email Address:</span>
            <span class="info-value">{{ $rental->tenant_email ?: ($rental->tenant->email ?? '-') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Lock-in Period:</span>
            <span class="info-value">{{ $rental->lock_in_period ?: 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Notice Period:</span>
            <span class="info-value">{{ $rental->notice_period ?: '1 Month' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Rent Due Day:</span>
            <span class="info-value">{{ $rental->rent_due_date ? 'Day ' . $rental->rent_due_date . ' of month' : '5th of month' }}</span>
        </div>
    </div>
</div>

<div class="finance-card">
    <div class="finance-header">Rental Commercials &amp; Deposit Schedule</div>
    <div class="finance-body">
        <table class="finance-table">
            <tr>
                <td class="label">Monthly Agreed Net Rent</td>
                <td class="value">₹{{ number_format($rental->rent_amount, 2) }}</td>
            </tr>
            @if($rental->maintenance_amount > 0)
            <tr>
                <td class="label">Monthly Maintenance Contribution</td>
                <td class="value">₹{{ number_format($rental->maintenance_amount, 2) }}</td>
            </tr>
            @endif
            @if($rental->escalation_percent > 0)
            <tr>
                <td class="label">Annual Escalation Rate</td>
                <td class="value">{{ $rental->escalation_percent }}%</td>
            </tr>
            @endif
            <tr class="total">
                <td class="label">Security Deposit (Refundable upon vacating)</td>
                <td class="value">₹{{ number_format($rental->security_deposit, 2) }}</td>
            </tr>
        </table>
    </div>
</div>

@if($rental->remarks)
<div class="terms-box">
    <strong>Special Terms &amp; Inventory Notes:</strong><br>
    {{ $rental->remarks }}
</div>
@endif

<div class="terms-box" style="font-size:10px; line-height:1.4;">
    <strong>Standard Clauses:</strong>
    1. The Tenant shall pay the monthly rental on or before the due date each month.
    2. The premises shall be used strictly in accordance with approved tenancy laws.
    3. The security deposit is refundable upon termination subject to inspection and utility clearance.
</div>

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Tenant / Lessee Signature</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Lessor / Delawala Authorized Signatory</div>
    </div>
</div>

<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; Tenancy Agreement #{{ $rental->agreement_no ?: $rental->id }}</span>
    <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
