<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Accounting Report</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Segoe UI',Arial,sans-serif;font-size:11px;color:#0F172A;background:#fff;padding:24px;}
        .rpt-header{display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:14px;margin-bottom:18px;border-bottom:2.5px solid #10B981;}
        .co-name{font-size:20px;font-weight:800;color:#0F172A;}
        .co-sub{font-size:9.5px;color:#10B981;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-top:2px;}
        .rpt-meta{text-align:right;}
        .rpt-meta .rpt-title{font-size:14px;font-weight:700;color:#0F172A;margin-bottom:2px;}
        .rpt-meta .rpt-date{font-size:10.5px;color:#64748B;}

        .filter-row{background:#F0FDF4;border:1px solid #BBF7D0;border-radius:6px;padding:8px 12px;margin-bottom:14px;font-size:10.5px;color:#166534;display:flex;flex-wrap:wrap;gap:12px;}
        .filter-row strong{color:#14532D;}

        .stat-row{display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap;}
        .stat-box{flex:1;min-width:95px;border:1px solid #E5E7EB;border-radius:6px;padding:9px 10px;}
        .stat-box .s-label{font-size:8.5px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#64748B;}
        .stat-box .s-value{font-size:14.5px;font-weight:800;margin-top:2px;color:#0F172A;}
        .stat-box.s-purple{border-color:rgba(139,92,246,.3);background:rgba(139,92,246,.04);}
        .stat-box.s-purple .s-value{color:#7C3AED;}
        .stat-box.s-amber{border-color:rgba(245,158,11,.3);background:rgba(245,158,11,.04);}
        .stat-box.s-amber .s-value{color:#D97706;}
        .stat-box.s-green{border-color:rgba(16,185,129,.3);background:rgba(16,185,129,.04);}
        .stat-box.s-green .s-value{color:#059669;}
        .stat-box.s-red{border-color:rgba(239,68,68,.3);background:rgba(239,68,68,.04);}
        .stat-box.s-red .s-value{color:#DC2626;}

        .section-label{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#10B981;margin-bottom:8px;margin-top:16px;padding-bottom:4px;border-bottom:1px solid #E5E7EB;}
        table{width:100%;border-collapse:collapse;font-size:10px;}
        thead tr{background:#0F172A;}
        thead th{padding:7px 8px;color:#FFF;font-weight:600;text-align:left;font-size:9px;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap;}
        thead th.r{text-align:right;}
        thead th.c{text-align:center;}
        tbody tr:nth-child(even){background:#F9FAFB;}
        tbody td{padding:6.5px 8px;border-bottom:1px solid #F1F5F9;vertical-align:middle;}
        tbody td.r{text-align:right;font-family:monospace;font-weight:700;}
        tbody td.c{text-align:center;}
        tbody tr:last-child td{border-bottom:none;}
        tfoot tr{background:#F0FDF4;}
        tfoot td{padding:8px 8px;font-weight:800;border-top:2px solid #E5E7EB;}
        tfoot td.r{text-align:right;font-size:11px;font-family:monospace;}

        .badge{display:inline-block;padding:2px 6px;border-radius:8px;font-size:8.5px;font-weight:700;text-transform:uppercase;}
        .b-paid{background:rgba(16,185,129,.12);color:#059669;}
        .b-partial{background:rgba(245,158,11,.12);color:#D97706;}
        .b-pending{background:rgba(239,68,68,.12);color:#DC2626;}

        .rpt-footer{margin-top:20px;padding-top:10px;border-top:1px solid #E5E7EB;display:flex;justify-content:space-between;color:#9CA3AF;font-size:9.5px;}
        @media print{body{padding:8px;}@page{margin:6mm;size:landscape;}}
    </style>
</head>
<body>

<div class="rpt-header">
    <div><div class="co-name">Delawala</div><div class="co-sub">Properties &amp; Management</div></div>
    <div class="rpt-meta">
        <div class="rpt-title">Sales Accounting Report</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

@if(request()->hasAny(['from_date','to_date','filter_property','filter_customer','filter_status']))
<div class="filter-row">
    <span><strong>Filters Applied:</strong></span>
    @if(request('from_date')) <span>From: {{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}</span> @endif
    @if(request('to_date'))   <span>To: {{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}</span> @endif
    @if(request('filter_status')) <span>Status: {{ ucfirst(request('filter_status')) }}</span> @endif
</div>
@endif

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Bookings</div>
        <div class="s-value">{{ $totalBookings }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Turnover (Sale Value)</div>
        <div class="s-value">₹{{ number_format($totalSale, 2) }}</div>
    </div>
    <div class="stat-box s-amber">
        <div class="s-label">Acquisition Cost</div>
        <div class="s-value">₹{{ number_format($totalPurchaseCost, 2) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Broker Commission</div>
        <div class="s-value">₹{{ number_format($totalCommission, 2) }}</div>
    </div>
    <div class="stat-box {{ $totalNetProfit >= 0 ? 's-green' : 's-red' }}">
        <div class="s-label">Realized Net Profit</div>
        <div class="s-value">{{ $totalNetProfit >= 0 ? '' : '−' }}₹{{ number_format(abs($totalNetProfit), 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Total Received</div>
        <div class="s-value">₹{{ number_format($totalReceived, 2) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total Pending</div>
        <div class="s-value">₹{{ number_format($totalPending, 2) }}</div>
    </div>
</div>

<div class="section-label">&#9632; Sales Accounting Ledger</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Property / Unit</th>
            <th>Broker</th>
            <th class="r">Sale Value (₹)</th>
            <th class="r">Purchase Cost (₹)</th>
            <th class="r">Commission (₹)</th>
            <th class="r">Net Profit (₹)</th>
            <th class="r">Margin</th>
            <th class="r">Received (₹)</th>
            <th class="r">Pending (₹)</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $i => $s)
        @php
            $status = strtolower($s->payment_status ?? 'pending');
            $badgeClass = $status === 'paid' ? 'b-paid' : ($status === 'partial' ? 'b-partial' : 'b-pending');
            $pCost = (float)$s->total_purchase_cost;
            $comm  = (float)$s->broker_commission_amount;
            $nProf = (float)$s->net_profit;
            $propName = $s->property?->property_name ?? ($s->properties->pluck('property_name')->implode(', ') ?: '—');
        @endphp
        <tr>
            <td style="color:#9CA3AF;">{{ $i+1 }}</td>
            <td style="white-space:nowrap;">{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '—' }}</td>
            <td><strong>{{ $s->customer?->name ?? '—' }}</strong></td>
            <td>{{ $propName }}</td>
            <td style="color:#64748B;">{{ $s->broker?->name ?? '—' }}</td>
            <td class="r" style="color:#7C3AED;">₹{{ number_format($s->sale_amount ?? 0, 2) }}</td>
            <td class="r" style="color:#D97706;">₹{{ number_format($pCost, 2) }}</td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($comm, 2) }}</td>
            <td class="r" style="color:{{ $nProf >= 0 ? '#059669' : '#DC2626' }};">
                {{ $nProf >= 0 ? '' : '−' }}₹{{ number_format(abs($nProf), 2) }}
            </td>
            <td class="r" style="color:{{ $nProf >= 0 ? '#059669' : '#DC2626' }};font-size:9.5px;">
                {{ $s->profit_margin_percentage }}%
            </td>
            <td class="r" style="color:#059669;">₹{{ number_format($s->received_amount ?? 0, 2) }}</td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($s->remaining_amount ?? 0, 2) }}</td>
            <td class="c"><span class="badge {{ $badgeClass }}">{{ ucfirst($s->payment_status ?? 'Pending') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="13" style="text-align:center;padding:16px;color:#64748B;">No records found.</td></tr>
        @endforelse
    </tbody>
    @if($records->count() > 0)
    <tfoot>
        <tr>
            <td colspan="5" style="font-size:10px;">TOTAL ({{ $totalBookings }} records)</td>
            <td class="r" style="color:#7C3AED;">₹{{ number_format($totalSale, 2) }}</td>
            <td class="r" style="color:#D97706;">₹{{ number_format($totalPurchaseCost, 2) }}</td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($totalCommission, 2) }}</td>
            <td class="r" style="color:{{ $totalNetProfit >= 0 ? '#059669' : '#DC2626' }};">
                {{ $totalNetProfit >= 0 ? '' : '−' }}₹{{ number_format(abs($totalNetProfit), 2) }}
            </td>
            <td class="r" style="color:{{ $totalNetProfit >= 0 ? '#059669' : '#DC2626' }};">{{ $profitMargin }}%</td>
            <td class="r" style="color:#059669;">₹{{ number_format($totalReceived, 2) }}</td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($totalPending, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="rpt-footer">
    <span>Delawala Management System — Sales Accounting Report</span>
    <span>Turnover: ₹{{ number_format($totalSale, 2) }} · Realized Profit: ₹{{ number_format($totalNetProfit, 2) }} ({{ $profitMargin }}%) · {{ now()->format('d M Y') }}</span>
</div>

<script>window.onload=function(){window.print();}</script>
</body>
</html>
