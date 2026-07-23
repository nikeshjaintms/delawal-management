<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Broker Commission Report</title>
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Segoe UI',Arial,sans-serif; font-size:12px; color:#0F1F35; background:#fff; padding:28px; }

        /* ── Header ── */
        .rpt-header { display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:18px; margin-bottom:22px; border-bottom:2.5px solid #fc6900ff; }
        .co-name  { font-size:22px; font-weight:800; color:#0F1F35; letter-spacing:0.4px; }
        .co-sub   { font-size:10px; color:#fc6900ff; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-top:3px; }
        .rpt-meta { text-align:right; }
        .rpt-meta .rpt-title { font-size:15px; font-weight:700; color:#0F1F35; margin-bottom:4px; }
        .rpt-meta .rpt-date  { font-size:11px; color:#64748B; }

        /* ── Applied Filters ── */
        .filter-row { background:#FFF7ED; border:1px solid #FFEDD5; border-radius:6px; padding:10px 14px; margin-bottom:20px; font-size:11px; color:#9A3412; display:flex; flex-wrap:wrap; gap:12px; }
        .filter-row strong { color:#7C2D12; }

        /* ── Stat Row ── */
        .stat-row { display:flex; gap:10px; margin-bottom:22px; flex-wrap:wrap; }
        .stat-box { flex:1; min-width:120px; border:1px solid #E5E7EB; border-radius:8px; padding:12px 14px; }
        .stat-box .s-label { font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#64748B; }
        .stat-box .s-value { font-size:18px; font-weight:800; margin-top:4px; color:#0F1F35; }
        .stat-box.s-orange { border-color:rgba(252,105,0,0.3); background:rgba(252,105,0,0.03); }
        .stat-box.s-orange .s-value { color:#e05c00; }
        .stat-box.s-green { border-color:rgba(16,185,129,0.3); background:rgba(16,185,129,0.03); }
        .stat-box.s-green .s-value { color:#059669; }
        .stat-box.s-amber { border-color:rgba(245,158,11,0.3); background:rgba(245,158,11,0.03); }
        .stat-box.s-amber .s-value { color:#B45309; }

        /* ── Main table ── */
        .section-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#fc6900ff; margin-bottom:10px; margin-top:22px; padding-bottom:6px; border-bottom:1px solid #E5E7EB; display:flex; align-items:center; gap:6px; }
        table { width:100%; border-collapse:collapse; font-size:11px; }
        thead tr { background:#0F172A; }
        thead th { padding:9px 10px; color:#FFF; font-weight:600; text-align:left; font-size:9.5px; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap; }
        thead th.r { text-align:right; }
        thead th.c { text-align:center; }
        tbody tr:nth-child(even) { background:#F9FAFB; }
        tbody td { padding:8px 10px; border-bottom:1px solid #F1F5F9; vertical-align:middle; }
        tbody td.r { text-align:right; font-weight:700; color:#0F1F35; }
        tbody td.c { text-align:center; }
        tbody tr:last-child td { border-bottom:none; }
        tfoot tr { background:#F1F5F9; }
        tfoot td { padding:9px 10px; font-weight:800; border-top:2px solid #E5E7EB; }
        tfoot td.r { text-align:right; color:#e05c00; font-size:13px; }
        .badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:9.5px; font-weight:700; text-transform:uppercase; }
        .b-pending  { background:rgba(245,158,11,0.12); color:#B45309; }
        .b-partial  { background:rgba(59,130,246,0.12); color:#1D4ED8; }
        .b-paid     { background:rgba(16,185,129,0.12); color:#059669; }
        .commission-chip { background:rgba(252, 105, 0, 0.08); color:#e05c00; padding:2px 7px; border-radius:4px; font-size:10px; font-weight:700; display:inline-block; }

        /* ── Footer ── */
        .rpt-footer { margin-top:26px; padding-top:12px; border-top:1px solid #E5E7EB; display:flex; justify-content:space-between; color:#9CA3AF; font-size:10px; }

        @media print { body { padding:12px; } @page { margin:8mm; } }
    </style>
</head>
<body>

{{-- ── Report Header ── --}}
<div class="rpt-header">
    <div>
        <div class="co-name">Delawala</div>
        <div class="co-sub">Properties &amp; Management</div>
    </div>
    <div class="rpt-meta">
        <div class="rpt-title">Broker Commission Report</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

{{-- ── Active Filters ── --}}
@php
    $hasFilters = request()->hasAny(['search', 'filter_broker', 'filter_property', 'filter_payment_status', 'from_date', 'to_date']);
@endphp
@if($hasFilters)
<div class="filter-row">
    <span><strong>Filters Applied:</strong></span>
    @if(request('search'))                 <span><strong>Search:</strong> "{{ request('search') }}"</span> @endif
    @if(request('from_date'))              <span><strong>From:</strong> {{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}</span> @endif
    @if(request('to_date'))                <span><strong>To:</strong> {{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}</span> @endif
    @if(request('filter_payment_status'))  <span><strong>Payment Status:</strong> {{ ucfirst(request('filter_payment_status')) }}</span> @endif
</div>
@endif

{{-- ── Stat Boxes ── --}}
<div class="stat-row">
    <div class="stat-box s-orange">
        <div class="s-label">Total Commissions</div>
        <div class="s-value">₹{{ number_format($totalCommission, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Paid Commissions</div>
        <div class="s-value">₹{{ number_format($paidCommission, 2) }}</div>
    </div>
    <div class="stat-box s-amber">
        <div class="s-label">Pending Commissions</div>
        <div class="s-value">₹{{ number_format($pendingCommission, 2) }}</div>
    </div>
    <div class="stat-box">
        <div class="s-label">Total Records</div>
        <div class="s-value">{{ $commissions->count() }}</div>
    </div>
</div>

{{-- ── Records Table ── --}}
<div class="section-label">&#9632; Commission Records</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;">#</th>
            <th>Broker Name</th>
            <th>Property</th>
            <th>Customer</th>
            <th>Commission Rate</th>
            <th>Payment Date</th>
            <th class="r">Calculated Amount</th>
            <th class="c">Payment Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($commissions as $i => $c)
        <tr>
            <td style="color:#9CA3AF;">{{ $i+1 }}</td>
            <td><strong>{{ $c->broker->name ?? '-' }}</strong></td>
            <td>{{ $c->property->property_name ?? '-' }}</td>
            <td>{{ $c->customer->name ?? '-' }}</td>
            <td>
                <span class="commission-chip">
                    @if($c->commission_type == 'percentage')
                        {{ number_format($c->commission_value, 2) }}%
                    @else
                        ₹{{ number_format($c->commission_value, 2) }}
                    @endif
                </span>
            </td>
            <td>{{ $c->payment_date ? \Carbon\Carbon::parse($c->payment_date)->format('d M Y') : '-' }}</td>
            <td class="r">₹{{ number_format($c->commission_amount, 2) }}</td>
            <td class="c">
                <span class="badge b-{{ $c->payment_status }}">{{ ucfirst($c->payment_status) }}</span>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:20px;color:#64748B;">No records found.</td></tr>
        @endforelse
    </tbody>
    @if($commissions->count() > 0)
    <tfoot>
        <tr>
            <td colspan="6" style="font-size:12px;">Total ({{ $commissions->count() }} records)</td>
            <td class="r">₹{{ number_format($totalCommission, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- ── Footer ── --}}
<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; Broker Commission Report</span>
    <span>{{ $commissions->count() }} records &nbsp;|&nbsp; Total ₹{{ number_format($totalCommission, 2) }} &nbsp;|&nbsp; {{ now()->format('d M Y') }}</span>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
