<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Broker Profile - {{ $broker->name }}</title>
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

        /* ── Commission Table ── */
        .section-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#fc6900ff; margin-bottom:8px; margin-top:16px; padding-bottom:4px; border-bottom:1px solid #E5E7EB; }
        table { width:100%; border-collapse:collapse; font-size:10.5px; margin-bottom:20px; }
        thead tr { background:#0F172A; }
        thead th { padding:7px 8px; color:#FFF; font-weight:600; text-align:left; font-size:9px; text-transform:uppercase; letter-spacing:0.5px; }
        thead th.r { text-align:right; }
        tbody tr:nth-child(even) { background:#F9FAFB; }
        tbody td { padding:6px 8px; border-bottom:1px solid #F1F5F9; vertical-align:middle; }
        tbody td.r { text-align:right; font-weight:700; color:#0F1F35; }

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
        <div class="rpt-title">Broker Dossier &amp; Profile</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

<div class="banner">
    <div>
        <h2>{{ $broker->name }}</h2>
        <div style="font-size:11px; color:#94A3B8;">
            Firm: {{ $broker->firm->firm_name ?? 'Delawala Management' }} &nbsp;|&nbsp; Rate: {{ $broker->commission_percentage ? $broker->commission_percentage.'%' : 'Standard' }}
        </div>
    </div>
    <div>
        <span class="badge-pill">STATUS: {{ strtoupper($broker->status ?: 'ACTIVE') }}</span>
    </div>
</div>

<div class="grid-2">
    <!-- Contact Info -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Broker Contact</div>
        <div class="info-row">
            <span class="info-label">Full Name:</span>
            <span class="info-value">{{ $broker->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mobile Number:</span>
            <span class="info-value">{{ $broker->mobile ?: '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email Address:</span>
            <span class="info-value">{{ $broker->email ?: '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Standard Commission Rate:</span>
            <span class="info-value">{{ $broker->commission_percentage ? $broker->commission_percentage.'%' : 'Standard' }}</span>
        </div>
    </div>

    <!-- Location & Assignment -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Location &amp; Project Assignment</div>
        <div class="info-row">
            <span class="info-label">Assigned Project:</span>
            <span class="info-value">{{ $broker->project->project_name ?? 'General / All' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">City:</span>
            <span class="info-value">{{ $broker->city ?: '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Full Address:</span>
            <span class="info-value">{{ $broker->address ?: '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Created At:</span>
            <span class="info-value">{{ $broker->created_at->format('d M Y, h:i A') }}</span>
        </div>
    </div>
</div>

@if($broker->commissions && $broker->commissions->isNotEmpty())
<div class="section-label">&#9632; Facilitated Deals &amp; Commissions</div>
<table>
    <thead>
        <tr>
            <th style="width:20px;">#</th>
            <th>Property</th>
            <th>Customer</th>
            <th>Commission Rate</th>
            <th class="r">Commission Amount</th>
            <th>Payment Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($broker->commissions as $i => $c)
        <tr>
            <td style="color:#9CA3AF;">{{ $i+1 }}</td>
            <td><strong>{{ $c->property->property_name ?? '-' }}</strong></td>
            <td>{{ $c->customer->name ?? '-' }}</td>
            <td>{{ $c->commission_type == 'percentage' ? $c->commission_value.'%' : '₹'.number_format($c->commission_value, 2) }}</td>
            <td class="r">₹{{ number_format($c->commission_amount, 2) }}</td>
            <td>{{ ucfirst($c->payment_status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; Broker Dossier</span>
    <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
