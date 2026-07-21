<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rental Report</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Segoe UI',Arial,sans-serif;font-size:11.5px;color:#0F172A;background:#fff;padding:26px;}
        .rpt-header{display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:16px;margin-bottom:20px;border-bottom:2.5px solid#14B8A6;}
        .co-name{font-size:22px;font-weight:800;color:#0F172A;}
        .co-sub{font-size:10px;color:#14B8A6;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-top:3px;}
        .rpt-meta{text-align:right;}
        .rpt-meta .rpt-title{font-size:15px;font-weight:700;color:#0F172A;margin-bottom:3px;}
        .rpt-meta .rpt-date{font-size:11px;color:#64748B;}

        .filter-row{background:#F0FDFA;border:1px solid#99F6E4;border-radius:6px;padding:9px 14px;margin-bottom:16px;font-size:11px;color:#115E59;display:flex;flex-wrap:wrap;gap:12px;}
        .filter-row strong{color:#134E4A;}

        .stat-row{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
        .stat-box{flex:1;min-width:110px;border:1px solid#E5E7EB;border-radius:7px;padding:11px 13px;}
        .stat-box .s-label{font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#64748B;}
        .stat-box .s-value{font-size:17px;font-weight:800;margin-top:3px;color:#0F172A;}
        .stat-box.s-teal{border-color:rgba(20,184,166,.3);background:rgba(20,184,166,.04);}
        .stat-box.s-teal .s-value{color:#0F766E;}
        .stat-box.s-green{border-color:rgba(16,185,129,.3);background:rgba(16,185,129,.04);}
        .stat-box.s-green .s-value{color:#059669;}
        .stat-box.s-amber{border-color:rgba(245,158,11,.3);background:rgba(245,158,11,.04);}
        .stat-box.s-amber .s-value{color:#D97706;}

        .section-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#14B8A6;margin-bottom:9px;margin-top:20px;padding-bottom:5px;border-bottom:1px solid#E5E7EB;}
        table{width:100%;border-collapse:collapse;font-size:10.5px;}
        thead tr{background:#0F172A;}
        thead th{padding:8px 9px;color:#FFF;font-weight:600;text-align:left;font-size:9.5px;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap;}
        thead th.r{text-align:right;}
        thead th.c{text-align:center;}
        tbody tr:nth-child(even){background:#F9FAFB;}
        tbody td{padding:7.5px 9px;border-bottom:1px solid#F1F5F9;vertical-align:middle;}
        tbody td.r{text-align:right;}
        tbody td.c{text-align:center;}
        tbody tr:last-child td{border-bottom:none;}
        tfoot tr{background:#F0FDFA;}
        tfoot td{padding:9px 9px;font-weight:800;border-top:2px solid#E5E7EB;}
        tfoot td.r{text-align:right;font-size:12px;}

        .badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:9.5px;font-weight:700;text-transform:uppercase;}
        .b-paid{background:rgba(16,185,129,.12);color:#059669;}
        .b-partial{background:rgba(245,158,11,.12);color:#D97706;}
        .b-pending{background:rgba(239,68,68,.12);color:#DC2626;}

        .rpt-footer{margin-top:22px;padding-top:10px;border-top:1px solid#E5E7EB;display:flex;justify-content:space-between;color:#9CA3AF;font-size:10px;}
        @media print{body{padding:10px;}@page{margin:8mm;}}
    </style>
</head>
<body>

<div class="rpt-header">
    <div><div class="co-name">Delawala</div><div class="co-sub">Properties &amp; Management</div></div>
    <div class="rpt-meta">
        <div class="rpt-title">Rental Report</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

@if(request()->hasAny(['from_date','to_date','filter_status','filter_mode','filter_tenant','filter_property']))
<div class="filter-row">
    <span><strong>Filters Applied:</strong></span>
    @if(request('from_date')) <span>From: {{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}</span> @endif
    @if(request('to_date'))   <span>To: {{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}</span> @endif
    @if(request('filter_mode')) <span>Mode: {{ request('filter_mode') }}</span> @endif
    @if(request('filter_status')) <span>Status: {{ ucfirst(request('filter_status')) }}</span> @endif
</div>
@endif

<div class="stat-row">
    <div class="stat-box s-teal">
        <div class="s-label">Total Rent Amount</div>
        <div class="s-value">₹{{ number_format($totalRentAmt, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Total Received</div>
        <div class="s-value">₹{{ number_format($totalReceived, 2) }}</div>
    </div>
    <div class="stat-box s-amber">
        <div class="s-label">Total Pending</div>
        <div class="s-value">₹{{ number_format($totalPending, 2) }}</div>
    </div>
</div>

<div class="section-label">&#9632; Rental Payment Records</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Firm</th>
            <th>Payment Date</th>
            <th>Month/Year</th>
            <th>Tenant</th>
            <th>Property</th>
            <th>Payment Mode</th>
            <th class="r">Rent Amount</th>
            <th class="r">Paid Amount</th>
            <th class="r">Pending</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $i => $r)
        @php
            $status = strtolower($r->payment_status ?? 'pending');
            $badgeClass = $status === 'paid' ? 'b-paid' : ($status === 'partial' ? 'b-partial' : 'b-pending');
            $monthName = \DateTime::createFromFormat('!m', $r->payment_month)->format('M');
        @endphp
        <tr>
            <td style="color:#9CA3AF;">{{ $i+1 }}</td>
            <td><strong>{{ $r->firm->firm_name ?? $r->rental?->firm?->firm_name ?? '—' }}</strong></td>
            <td style="white-space:nowrap;">{{ $r->payment_date ? \Carbon\Carbon::parse($r->payment_date)->format('d M Y') : '—' }}</td>
            <td><span style="background:rgba(20,184,166,.1);color:#0F766E;padding:2px 7px;border-radius:4px;font-weight:700;font-size:10px;">{{ $monthName }} {{ $r->payment_year }}</span></td>
            <td><strong>{{ $r->rental?->tenant_name ?? '—' }}</strong></td>
            <td>{{ $r->rental?->property?->property_name ?? '—' }}</td>
            <td><span style="background:#F1F5F9;color:#475569;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:600;">{{ $r->payment_mode ?? '—' }}</span></td>
            <td class="r" style="color:#0F766E;font-weight:700;">₹{{ number_format($r->rent_amount, 2) }}</td>
            <td class="r" style="color:#059669;font-weight:700;">₹{{ number_format($r->paid_amount, 2) }}</td>
            <td class="r" style="color:#D97706;font-weight:700;">₹{{ number_format($r->pending_amount, 2) }}</td>
            <td class="c"><span class="badge {{ $badgeClass }}">{{ ucfirst($r->payment_status ?? 'Pending') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="11" style="text-align:center;padding:20px;color:#64748B;">No records found.</td></tr>
        @endforelse
    </tbody>
    @if($records->count() > 0)
    <tfoot>
        <tr>
            <td colspan="7" style="font-size:11px;">Total ({{ $records->count() }} payments)</td>
            <td class="r" style="color:#0F766E;">₹{{ number_format($totalRentAmt, 2) }}</td>
            <td class="r" style="color:#059669;">₹{{ number_format($totalReceived, 2) }}</td>
            <td class="r" style="color:#D97706;">₹{{ number_format($totalPending, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="rpt-footer">
    <span>Delawala Management System — Rental Report</span>
    <span>{{ $records->count() }} payments · Total Received ₹{{ number_format($totalReceived, 2) }} · {{ now()->format('d M Y') }}</span>
</div>

<script>window.onload=function(){window.print();}</script>
</body>
</html>
