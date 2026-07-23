<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Credit Note Report</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Segoe UI',Arial,sans-serif;font-size:11.5px;color:#0F172A;background:#fff;padding:26px;}
        .hdr{display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:16px;margin-bottom:18px;border-bottom:2.5px solid #10B981;}
        .co-name{font-size:21px;font-weight:800;}
        .co-sub{font-size:10px;color:#10B981;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-top:3px;}
        .rpt-meta{text-align:right;}
        .rpt-meta .rpt-title{font-size:15px;font-weight:700;margin-bottom:3px;}
        .rpt-meta .rpt-date{font-size:11px;color:#64748B;}
        .filter-row{background:#ECFDF5;border:1px solid #A7F3D0;border-radius:6px;padding:9px 14px;margin-bottom:16px;font-size:11px;color:#065F46;display:flex;flex-wrap:wrap;gap:12px;}
        .stat-row{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;}
        .sbox{flex:1;min-width:100px;border:1px solid #E2E8F0;border-radius:7px;padding:11px 13px;}
        .sbox .sl{font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#64748B;}
        .sbox .sv{font-size:16px;font-weight:800;margin-top:3px;}
        .sbox.s-amber .sv{color:#D97706;}
        .sbox.s-red .sv{color:#DC2626;}
        .sbox.s-green .sv{color:#059669;}
        table{width:100%;border-collapse:collapse;font-size:10.5px;}
        thead tr{background:#0F172A;}
        thead th{padding:8px 9px;color:#fff;font-weight:600;text-align:left;font-size:9.5px;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap;}
        thead th.r{text-align:right;}
        thead th.c{text-align:center;}
        tbody tr:nth-child(even){background:#F8FAFC;}
        tbody td{padding:7.5px 9px;border-bottom:1px solid #F1F5F9;vertical-align:middle;}
        tbody td.r{text-align:right;}
        tbody td.c{text-align:center;}
        tfoot tr{background:#F1F5F9;}
        tfoot td{padding:8px 9px;font-weight:800;border-top:2px solid #E2E8F0;}
        tfoot td.r{text-align:right;}
        .badge{display:inline-block;padding:2px 7px;border-radius:10px;font-size:9px;font-weight:700;}
        .cn-approved{background:rgba(16,185,129,.12);color:#065F46;}
        .cn-pending{background:rgba(245,158,11,.12);color:#92400E;}
        .cn-rejected{background:rgba(239,68,68,.12);color:#991B1B;}
        .rpt-foot{margin-top:20px;padding-top:10px;border-top:1px solid #E2E8F0;display:flex;justify-content:space-between;color:#94A3B8;font-size:10px;}
        @media print{body{padding:10px;}@page{margin:8mm;}}
    </style>
</head>
<body>

<div class="hdr">
    <div>
        <div class="co-name">Delawala</div>
        <div class="co-sub">Properties &amp; Management</div>
    </div>
    <div class="rpt-meta">
        <div class="rpt-title">Credit Note Report</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

@if(request()->hasAny(['from_date','to_date','filter_customer','filter_status']))
<div class="filter-row">
    <span><strong>Filters:</strong></span>
    @if(request('from_date'))<span>From: {{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}</span>@endif
    @if(request('to_date'))<span>To: {{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}</span>@endif
    @if(request('filter_status'))<span>Status: {{ request('filter_status') }}</span>@endif
</div>
@endif

<div class="stat-row">
    <div class="sbox"><div class="sl">Total Notes</div><div class="sv">{{ $notes->count() }}</div></div>
    <div class="sbox s-amber"><div class="sl">Taxable Amt</div><div class="sv">₹{{ number_format($totalTaxable,2) }}</div></div>
    <div class="sbox"><div class="sl">CGST</div><div class="sv" style="color:#0EA5E9;">₹{{ number_format($totalCgst,2) }}</div></div>
    <div class="sbox"><div class="sl">SGST</div><div class="sv" style="color:#14B8A6;">₹{{ number_format($totalSgst,2) }}</div></div>
    <div class="sbox"><div class="sl">IGST</div><div class="sv" style="color:#8B5CF6;">₹{{ number_format($totalIgst,2) }}</div></div>
    <div class="sbox s-red"><div class="sl">Total GST</div><div class="sv">₹{{ number_format($totalGst,2) }}</div></div>
    <div class="sbox s-green"><div class="sl">Credit Amt</div><div class="sv">₹{{ number_format($totalCredit,2) }}</div></div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:22px;">#</th>
            <th>Credit Note No</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Rel. Invoice</th>
            <th>Reason</th>
            <th class="r">Taxable</th>
            <th class="r">CGST</th>
            <th class="r">SGST</th>
            <th class="r">IGST</th>
            <th class="r">Total GST</th>
            <th class="r">Credit Amt</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($notes as $i => $cn)
        @php $badge = match($cn->status){'Approved'=>'cn-approved','Rejected'=>'cn-rejected',default=>'cn-pending'}; @endphp
        <tr>
            <td style="color:#94A3B8;">{{ $i+1 }}</td>
            <td style="font-weight:600;">{{ $cn->credit_note_no ?? '—' }}</td>
            <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($cn->credit_note_date)->format('d M Y') }}</td>
            <td>{{ $cn->customer?->name ?? '—' }}</td>
            <td>{{ $cn->related_invoice_no ?? '—' }}</td>
            <td style="font-size:10px;max-width:100px;">{{ $cn->reason ? \Illuminate\Support\Str::limit($cn->reason,30) : '—' }}</td>
            <td class="r">₹{{ number_format($cn->taxable_amount,2) }}</td>
            <td class="r" style="color:#0EA5E9;">₹{{ number_format($cn->cgst_amount,2) }}</td>
            <td class="r" style="color:#14B8A6;">₹{{ number_format($cn->sgst_amount,2) }}</td>
            <td class="r" style="color:#8B5CF6;">₹{{ number_format($cn->igst_amount,2) }}</td>
            <td class="r" style="color:#DC2626;font-weight:700;">₹{{ number_format($cn->total_gst,2) }}</td>
            <td class="r" style="color:#059669;font-weight:800;">₹{{ number_format($cn->credit_amount,2) }}</td>
            <td class="c"><span class="badge {{ $badge }}">{{ $cn->status }}</span></td>
        </tr>
        @empty
        <tr><td colspan="13" style="text-align:center;padding:20px;color:#94A3B8;">No records.</td></tr>
        @endforelse
    </tbody>
    @if($notes->count() > 0)
    <tfoot>
        <tr>
            <td colspan="6" style="font-size:11px;">Total ({{ $notes->count() }} records)</td>
            <td class="r" style="color:#D97706;">₹{{ number_format($totalTaxable,2) }}</td>
            <td class="r" style="color:#0EA5E9;">₹{{ number_format($totalCgst,2) }}</td>
            <td class="r" style="color:#14B8A6;">₹{{ number_format($totalSgst,2) }}</td>
            <td class="r" style="color:#8B5CF6;">₹{{ number_format($totalIgst,2) }}</td>
            <td class="r" style="color:#DC2626;font-size:12px;">₹{{ number_format($totalGst,2) }}</td>
            <td class="r" style="color:#059669;font-size:13px;">₹{{ number_format($totalCredit,2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="rpt-foot">
    <span>Delawala Management System — Credit Note Report</span>
    <span>{{ $notes->count() }} records · Total ₹{{ number_format($totalCredit,2) }} · {{ now()->format('d M Y') }}</span>
</div>

<script>window.onload=function(){window.print();}</script>
</body>
</html>
