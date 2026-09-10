<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Profile - {{ $vendor->name }}</title>
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

        /* ── Associated Properties Table ── */
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
        <div class="rpt-title">Vendor / Supplier Dossier</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

<div class="banner">
    <div>
        <h2>{{ $vendor->name }}</h2>
        <div style="font-size:11px; color:#94A3B8;">
            Firm: {{ $vendor->firm->firm_name ?? 'Delawala Management' }} &nbsp;|&nbsp; GST: {{ $vendor->gst_no ?: 'Unregistered' }}
        </div>
    </div>
    <div>
        <span class="badge-pill">STATUS: {{ strtoupper($vendor->status ?: 'ACTIVE') }}</span>
    </div>
</div>

<div class="grid-2">
    <!-- Contact Info -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Vendor Details</div>
        <div class="info-row">
            <span class="info-label">Vendor Name:</span>
            <span class="info-value">{{ $vendor->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mobile Number:</span>
            <span class="info-value">{{ $vendor->mobile ?: '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email Address:</span>
            <span class="info-value">{{ $vendor->email ?: '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">GST Number:</span>
            <span class="info-value">{{ $vendor->gst_no ?: '-' }}</span>
        </div>
    </div>

    <!-- Address & Commercials -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Commercial &amp; Location</div>
        <div class="info-row">
            <span class="info-label">City:</span>
            <span class="info-value">{{ $vendor->city ?: '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Full Address:</span>
            <span class="info-value">{{ $vendor->address ?: '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Terms:</span>
            <span class="info-value">{{ $vendor->payment_terms ?: '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Created At:</span>
            <span class="info-value">{{ $vendor->created_at->format('d M Y, h:i A') }}</span>
        </div>
    </div>
</div>

@if($vendor->propertyMasters && $vendor->propertyMasters->isNotEmpty())
<div class="section-label">&#9632; Supplied / Landlord Property Master Records</div>
<table>
    <thead>
        <tr>
            <th style="width:20px;">#</th>
            <th>Property Name</th>
            <th>Code</th>
            <th>Purchase Date</th>
            <th class="r">Purchase Price</th>
            <th class="r">Paid</th>
            <th class="r">Due Balance</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vendor->propertyMasters as $i => $pm)
        <tr>
            <td style="color:#9CA3AF;">{{ $i+1 }}</td>
            <td><strong>{{ $pm->property_name }}</strong></td>
            <td>{{ $pm->property_code }}</td>
            <td>{{ $pm->purchase_date ? \Carbon\Carbon::parse($pm->purchase_date)->format('d M Y') : '-' }}</td>
            <td class="r">₹{{ number_format($pm->purchase_price, 2) }}</td>
            <td class="r" style="color:#059669;">₹{{ number_format($pm->paid_amount, 2) }}</td>
            <td class="r" style="color:#B45309;">₹{{ number_format($pm->due_amount, 2) }}</td>
            <td>{{ ucfirst($pm->status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; Vendor Dossier</span>
    <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
