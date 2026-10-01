<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Income Report</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Segoe UI',Arial,sans-serif;font-size:11.5px;color:#0F172A;background:#fff;padding:26px;}
        .rpt-header{display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:16px;margin-bottom:20px;border-bottom:2.5px solid #D4AF37;}
        .co-name{font-size:22px;font-weight:800;color:#0F172A;}
        .co-sub{font-size:10px;color:#D4AF37;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-top:3px;}
        .rpt-meta{text-align:right;}
        .rpt-meta .rpt-title{font-size:15px;font-weight:700;color:#0F172A;margin-bottom:3px;}
        .rpt-meta .rpt-date{font-size:11px;color:#64748B;}

        .filter-row{background:#FFFDF5;border:1px solid #F3E8C4;border-radius:6px;padding:9px 14px;margin-bottom:16px;font-size:11px;color:#856404;display:flex;flex-wrap:wrap;gap:12px;}
        .filter-row strong{color:#533F03;}

        .stat-row{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
        .stat-box{flex:1;min-width:110px;border:1px solid #E5E7EB;border-radius:7px;padding:11px 13px;}
        .stat-box .s-label{font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#64748B;}
        .stat-box .s-value{font-size:17px;font-weight:800;margin-top:3px;color:#0F172A;}
        .stat-box.s-gold{border-color:rgba(212,175,55,.3);background:rgba(212,175,55,.04);}
        .stat-box.s-gold .s-value{color:#B58D1B;}

        .section-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#B58D1B;margin-bottom:9px;margin-top:20px;padding-bottom:5px;border-bottom:1px solid #E5E7EB;}
        table{width:100%;border-collapse:collapse;font-size:10.5px;}
        thead tr{background:#0F172A;}
        thead th{padding:8px 9px;color:#FFF;font-weight:600;text-align:left;font-size:9.5px;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap;}
        thead th.r{text-align:right;}
        thead th.c{text-align:center;}
        tbody tr:nth-child(even){background:#F9FAFB;}
        tbody td{padding:7.5px 9px;border-bottom:1px solid #F1F5F9;vertical-align:middle;}
        tbody td.r{text-align:right;}
        tbody td.c{text-align:center;}
        tbody tr:last-child td{border-bottom:none;}
        tfoot tr{background:#FFFDF5;}
        tfoot td{padding:9px 9px;font-weight:800;border-top:2px solid #E5E7EB;}
        tfoot td.r{text-align:right;font-size:12px;}

        .badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:9.5px;font-weight:700;text-transform:uppercase;}
        .b-active{background:rgba(16,185,129,.12);color:#059669;}
        .b-inactive{background:rgba(239,68,68,.12);color:#DC2626;}

        .rpt-footer{margin-top:22px;padding-top:10px;border-top:1px solid #E5E7EB;display:flex;justify-content:space-between;color:#9CA3AF;font-size:10px;}
        @media print{body{padding:10px;}@page{margin:8mm;} button, .no-print{display:none !important;}}
        .print-btn{background:#B58D1B;color:#FFF;padding:6px 14px;border:none;border-radius:4px;cursor:pointer;font-weight:600;font-size:11px;margin-bottom:15px;}
    </style>
</head>
<body>
@include('admin.components.pdf-action-bar', ['title' => 'Income Accounting Report', 'orientation' => 'landscape', 'backUrl' => route('incomes.index')])

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
        <div class="rpt-title">Income Report</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

@if(request()->hasAny(['from_date','to_date','filter_type','filter_status','filter_property','filter_property_type','search']))
<div class="filter-row">
    <span><strong>Filters Applied:</strong></span>
    @if(request('from_date')) <span>From: {{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}</span> @endif
    @if(request('to_date'))   <span>To: {{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}</span> @endif
    @if(request('filter_type')) <span>Type: {{ request('filter_type') }}</span> @endif
    @if(request('filter_status')) <span>Status: {{ ucfirst(request('filter_status')) }}</span> @endif
    @if(request('search')) <span>Search: "{{ request('search') }}"</span> @endif
</div>
@endif

<div class="stat-row">
    <div class="stat-box s-gold">
        <div class="s-label">Total Income Amount</div>
        <div class="s-value">₹{{ number_format($totalAmount, 2) }}</div>
    </div>
</div>

<div class="section-label">&#9632; Income Records</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Firm</th>
            <th>Property Name</th>
            <th>Property Type</th>
            <th>Date</th>
            <th>Income Type</th>
            <th>Received From</th>
            <th class="r">Amount</th>
            <th>Payment Mode</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($allRecords as $i => $r)
        @php
            $status = strtolower($r->status ?? 'active');
            $badgeClass = $status === 'active' ? 'b-active' : 'b-inactive';
        @endphp
        <tr>
            <td style="color:#9CA3AF;">{{ $i+1 }}</td>
            <td><strong>{{ $r->firm_names ?? $r->firm->firm_name ?? '—' }}</strong></td>
            <td><strong>{{ $r->property->property_name ?? '—' }}</strong></td>
            <td>{{ $r->property->propertyType->name ?? '—' }}</td>
            <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($r->income_date)->format('d M Y') }}</td>
            <td>{{ $r->income_type ?? '—' }}</td>
            <td>{{ $r->received_from ?? '—' }}</td>
            <td class="r" style="color:#059669;font-weight:700;">₹{{ number_format($r->amount, 2) }}</td>
            <td>{{ $r->paymentMode->name ?? '—' }}</td>
            <td class="c"><span class="badge {{ $badgeClass }}">{{ ucfirst($r->status ?? 'active') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="10" style="text-align:center;padding:20px;color:#64748B;">No records found.</td></tr>
        @endforelse
    </tbody>
    @if($allRecords->count() > 0)
    <tfoot>
        <tr>
            <td colspan="7" style="font-size:11px;">Total ({{ $allRecords->count() }} payments)</td>
            <td class="r" style="color:#059669;">₹{{ number_format($totalAmount, 2) }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="rpt-footer">
    <span>Delawala Properties &amp; Management</span>
    <span>Page 1 of 1</span>
</div>

</body>
</html>
