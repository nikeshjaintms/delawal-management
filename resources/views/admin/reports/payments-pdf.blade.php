<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Report</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Segoe UI',Arial,sans-serif;font-size:11.5px;color:#0F172A;background:#fff;padding:26px;}
        .rpt-header{display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:16px;margin-bottom:20px;border-bottom:2.5px solid#059669;}
        .co-name{font-size:22px;font-weight:800;color:#0F172A;}
        .co-sub{font-size:10px;color:#059669;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-top:3px;}
        .rpt-meta{text-align:right;}
        .rpt-meta .rpt-title{font-size:15px;font-weight:700;color:#0F172A;margin-bottom:3px;}
        .rpt-meta .rpt-date{font-size:11px;color:#64748B;}

        .filter-row{background:#ECFDF5;border:1px solid#A7F3D0;border-radius:6px;padding:9px 14px;margin-bottom:16px;font-size:11px;color:#047857;display:flex;flex-wrap:wrap;gap:12px;}
        .filter-row strong{color:#065F46;}

        .stat-row{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
        .stat-box{flex:1;min-width:110px;border:1px solid#E5E7EB;border-radius:7px;padding:11px 13px;}
        .stat-box .s-label{font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#64748B;}
        .stat-box .s-value{font-size:17px;font-weight:800;margin-top:3px;color:#0F172A;}
        .stat-box.s-green{border-color:rgba(16,185,129,.3);background:rgba(16,185,129,.04);}
        .stat-box.s-green .s-value{color:#059669;}
        .stat-box.s-amber{border-color:rgba(245,158,11,.3);background:rgba(245,158,11,.04);}
        .stat-box.s-amber .s-value{color:#D97706;}
        .stat-box.s-blue{border-color:rgba(59,130,246,.3);background:rgba(59,130,246,.04);}
        .stat-box.s-blue .s-value{color:#2563EB;}

        .section-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#059669;margin-bottom:9px;margin-top:20px;padding-bottom:5px;border-bottom:1px solid#E5E7EB;}
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
        tfoot tr{background:#ECFDF5;}
        tfoot td{padding:9px 9px;font-weight:800;border-top:2px solid#E5E7EB;}
        tfoot td.r{text-align:right;font-size:12px;}

        .badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:9.5px;font-weight:700;text-transform:uppercase;}
        .b-completed{background:rgba(16,185,129,.12);color:#059669;}
        .b-pending{background:rgba(245,158,11,.12);color:#D97706;}
        .b-failed{background:rgba(239,68,68,.12);color:#DC2626;}

        .rpt-footer{margin-top:22px;padding-top:10px;border-top:1px solid#E5E7EB;display:flex;justify-content:space-between;color:#9CA3AF;font-size:10px;}
        @media print{body{padding:10px;}@page{margin:8mm;}}
    </style>
</head>
<body>
@include('admin.components.pdf-action-bar', ['title' => 'Payment Ledger Report', 'orientation' => 'landscape', 'backUrl' => route('reports.payments')])

<div class="rpt-header" style="display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:14px; margin-bottom:18px; border-bottom:2.5px solid #D97706; gap:16px;">
    <div style="display:flex; align-items:center; gap:14px;">
        <img src="{{ asset('images/logo.png') }}" alt="Delawala Properties" style="height:52px; width:auto; object-fit:contain;" onerror="this.style.display='none';">
        <div style="display:flex; flex-direction:column; gap:2px;">
            <div class="co-name" style="font-size:20px; font-weight:800; color:#0F172A; text-transform:uppercase; letter-spacing:0.3px; line-height:1.1;">Delawala Properties</div>
            <div class="co-sub" style="font-size:9.5px; color:#D97706; font-weight:700; letter-spacing:1.2px; text-transform:uppercase;">Delawala Infra Co. &bull; Real Estate &amp; Management</div>
            <div style="font-size:10px; color:#475569; line-height:1.3; margin-top:1px;">
                <i class="fa-solid fa-location-dot" style="color:#D97706; font-size:9px; margin-right:3px;"></i>Ground Floor, F F SH No. 116, Aman Plazza, Dahegam Road, Dahegam, Bharuch - 392012
            </div>
            <div style="display:flex; align-items:center; gap:6px; margin-top:3px; flex-wrap:wrap;">
                <span style="display:inline-flex; align-items:center; background:#FEF3C7; color:#92400E; border:1px solid #FCD34D; padding:1px 6px; border-radius:4px; font-size:9.5px; font-weight:700; letter-spacing:0.3px;"><strong>GSTIN:</strong> 24CUBPD0770R1ZI</span>
                <span style="display:inline-flex; align-items:center; background:#F1F5F9; color:#334155; border:1px solid #CBD5E1; padding:1px 6px; border-radius:4px; font-size:9px;"><strong>Proprietor:</strong> Delawala Zafar</span>
            </div>
        </div>
    </div>
    <div class="rpt-meta" style="text-align:right; flex-shrink:0;">
        <div class="rpt-title" style="font-size:16px; font-weight:800; color:#0F172A; letter-spacing:0.2px; line-height:1.2;">Official Document</div>
        <div class="rpt-date" style="font-size:10px; color:#64748B; margin-top:3px;">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>
    </div>
    <div class="rpt-meta">
        <div class="rpt-title">Payment Report</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

@if(request()->hasAny(['from_date','to_date','filter_mode','filter_status','filter_customer','filter_property']))
<div class="filter-row">
    <span><strong>Filters Applied:</strong></span>
    @if(request('from_date')) <span>From: {{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}</span> @endif
    @if(request('to_date'))   <span>To: {{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}</span> @endif
    @if(request('filter_mode')) <span>Mode: {{ request('filter_mode') }}</span> @endif
    @if(request('filter_status')) <span>Status: {{ ucfirst(request('filter_status')) }}</span> @endif
</div>
@endif

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Transactions</div>
        <div class="s-value">{{ $totalTransactions }}</div>
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

<div class="section-label">&#9632; Payment Records</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Payment Date</th>
            <th>Customer</th>
            <th>Property</th>
            <th>Invoice No</th>
            <th>Payment Mode</th>
            <th>Transaction Ref</th>
            <th class="r">Paid Amount</th>
            <th class="r">Pending</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $i => $p)
        @php
            $status = strtolower($p->status ?? 'pending');
            $badgeClass = $status === 'completed' ? 'b-completed' : ($status === 'failed' ? 'b-failed' : 'b-pending');
        @endphp
        <tr>
            <td style="color:#9CA3AF;">{{ $i+1 }}</td>
            <td style="white-space:nowrap;">{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d M Y') : '—' }}</td>
            <td><strong>{{ $p->customer?->name ?? '—' }}</strong></td>
            <td>{{ $p->propertySale?->property?->property_name ?? '—' }}</td>
            <td style="font-family:monospace;font-size:10px;">{{ $p->propertySale?->invoice_no ?? '—' }}</td>
            <td><span style="background:#F1F5F9;color:#475569;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:600;">{{ $p->payment_mode ?? '—' }}</span></td>
            <td style="font-family:monospace;font-size:10px;color:#64748B;">{{ $p->transaction_ref ?? '—' }}</td>
            <td class="r" style="color:#059669;font-weight:700;">₹{{ number_format($p->payment_amount, 2) }}</td>
            <td class="r" style="color:#D97706;font-weight:700;">₹{{ number_format($p->pending_amount, 2) }}</td>
            <td class="c"><span class="badge {{ $badgeClass }}">{{ ucfirst($p->status ?? 'Pending') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="10" style="text-align:center;padding:20px;color:#64748B;">No records found.</td></tr>
        @endforelse
    </tbody>
    @if($records->count() > 0)
    <tfoot>
        <tr>
            <td colspan="7" style="font-size:11px;">Total ({{ $totalTransactions }} transactions)</td>
            <td class="r" style="color:#059669;">₹{{ number_format($totalReceived, 2) }}</td>
            <td class="r" style="color:#D97706;">₹{{ number_format($totalPending, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="rpt-footer">
    <span>Delawala Management System — Payment Report</span>
    <span>{{ $totalTransactions }} transactions · Total Received ₹{{ number_format($totalReceived, 2) }} · {{ now()->format('d M Y') }}</span>
</div>

</body>
</html>
