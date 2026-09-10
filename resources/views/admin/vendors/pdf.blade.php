<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendors / Suppliers Directory Report - Delawala Management</title>
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Segoe UI',Arial,sans-serif; font-size:11.5px; color:#0F1F35; background:#fff; padding:24px; }

        /* ── Header ── */
        .rpt-header { display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:16px; margin-bottom:20px; border-bottom:2.5px solid #fc6900ff; }
        .co-name  { font-size:22px; font-weight:800; color:#0F1F35; letter-spacing:0.4px; }
        .co-sub   { font-size:10px; color:#fc6900ff; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-top:3px; }
        .rpt-meta { text-align:right; }
        .rpt-meta .rpt-title { font-size:16px; font-weight:700; color:#0F1F35; margin-bottom:4px; }
        .rpt-meta .rpt-date  { font-size:11px; color:#64748B; }

        /* ── Stat Row ── */
        .stat-row { display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; }
        .stat-box { flex:1; min-width:110px; border:1px solid #E5E7EB; border-radius:8px; padding:10px 12px; background:#F8FAFC; }
        .stat-box .s-label { font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#64748B; }
        .stat-box .s-value { font-size:16px; font-weight:800; margin-top:4px; color:#0F1F35; }
        .stat-box.s-green { border-color:rgba(16,185,129,0.3); background:rgba(16,185,129,0.03); }
        .stat-box.s-green .s-value { color:#059669; }

        /* ── Main table ── */
        .section-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#fc6900ff; margin-bottom:8px; margin-top:16px; padding-bottom:4px; border-bottom:1px solid #E5E7EB; }
        table { width:100%; border-collapse:collapse; font-size:11px; }
        thead tr { background:#0F172A; }
        thead th { padding:8px 10px; color:#FFF; font-weight:600; text-align:left; font-size:9.5px; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap; }
        thead th.c { text-align:center; }
        tbody tr:nth-child(even) { background:#F9FAFB; }
        tbody td { padding:8px 10px; border-bottom:1px solid #F1F5F9; vertical-align:middle; }
        tbody td.c { text-align:center; }
        tbody tr:last-child td { border-bottom:none; }

        .badge { display:inline-block; padding:2px 7px; border-radius:10px; font-size:9px; font-weight:700; text-transform:uppercase; }
        .badge-success { background:#ECFDF5; color:#059669; border:1px solid #A7F3D0; }
        .badge-danger  { background:#FEF2F2; color:#DC2626; border:1px solid #FECACA; }

        /* ── Footer ── */
        .rpt-footer { margin-top:24px; padding-top:10px; border-top:1px solid #E5E7EB; display:flex; justify-content:space-between; color:#9CA3AF; font-size:9.5px; }

        @media print { body { padding:10px; } @page { margin:8mm; size:landscape; } }
    </style>
</head>
<body>

<div class="rpt-header">
    <div>
        <div class="co-name">Delawala</div>
        <div class="co-sub">Properties &amp; Management</div>
    </div>
    <div class="rpt-meta">
        <div class="rpt-title">Vendors &amp; Suppliers Directory</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Vendors</div>
        <div class="s-value">{{ $totalVendors }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Vendors</div>
        <div class="s-value">{{ $activeVendors }}</div>
    </div>
</div>

<div class="section-label">&#9632; Vendor Records</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;">#</th>
            <th>Vendor Name</th>
            <th>Mobile</th>
            <th>Email</th>
            <th>Firm</th>
            <th>GST Number</th>
            <th>City</th>
            <th>Payment Terms</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($vendors as $i => $item)
        <tr>
            <td style="color:#9CA3AF;">{{ $i+1 }}</td>
            <td><strong>{{ $item->name }}</strong></td>
            <td>{{ $item->mobile ?: '-' }}</td>
            <td>{{ $item->email ?: '-' }}</td>
            <td>{{ $item->firm->firm_name ?? '-' }}</td>
            <td>{{ $item->gst_no ?: '-' }}</td>
            <td>{{ $item->city ?: '-' }}</td>
            <td>{{ $item->payment_terms ?: '-' }}</td>
            <td class="c">
                @if($item->status === 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-danger">{{ ucfirst($item->status) }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;padding:20px;color:#64748B;">No vendor records found.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; Vendors Directory</span>
    <span>{{ $vendors->count() }} vendors &nbsp;|&nbsp; {{ now()->format('d M Y') }}</span>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
