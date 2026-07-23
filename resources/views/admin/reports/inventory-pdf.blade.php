<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Report</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Segoe UI',Arial,sans-serif;font-size:11.5px;color:#0F172A;background:#fff;padding:26px;}
        .hdr{display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:16px;margin-bottom:18px;border-bottom:2.5px solid #3B82F6;}
        .co-name{font-size:21px;font-weight:800;color:#0F172A;}
        .co-sub{font-size:10px;color:#3B82F6;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-top:3px;}
        .rpt-meta{text-align:right;}
        .rpt-meta .rpt-title{font-size:15px;font-weight:700;margin-bottom:3px;}
        .rpt-meta .rpt-date{font-size:11px;color:#64748B;}
        .filter-row{background:#EFF6FF;border:1px solid #BFDBFE;border-radius:6px;padding:9px 14px;margin-bottom:16px;font-size:11px;color:#1E40AF;display:flex;flex-wrap:wrap;gap:12px;}
        .stat-row{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;}
        .sbox{flex:1;min-width:100px;border:1px solid #E2E8F0;border-radius:7px;padding:11px 13px;}
        .sbox .sl{font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#64748B;}
        .sbox .sv{font-size:16px;font-weight:800;margin-top:3px;}
        .sbox.s-blue{border-color:rgba(59,130,246,.3);background:rgba(59,130,246,.04);}
        .sbox.s-blue .sv{color:#2563EB;}
        .sbox.s-green{border-color:rgba(16,185,129,.3);background:rgba(16,185,129,.04);}
        .sbox.s-green .sv{color:#059669;}
        .sbox.s-amber{border-color:rgba(245,158,11,.3);background:rgba(245,158,11,.04);}
        .sbox.s-amber .sv{color:#D97706;}
        .sbox.s-red{border-color:rgba(239,68,68,.3);background:rgba(239,68,68,.04);}
        .sbox.s-red .sv{color:#DC2626;}

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

        .status-badge{display:inline-block;padding:2px 7px;border-radius:10px;font-size:9px;font-weight:700;text-transform:uppercase;text-align:center;}
        .sb-approved{background:rgba(16,185,129,.12);color:#065F46;}
        .sb-pending{background:rgba(245,158,11,.12);color:#92400E;}
        .sb-rejected{background:rgba(239,68,68,.12);color:#991B1B;}

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
        <div class="rpt-title">Inventory Report</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

@if(request()->hasAny(['from_date','to_date','filter_material','filter_category','filter_status','filter_supplier']))
<div class="filter-row">
    <span><strong>Filters:</strong></span>
    @if(request('from_date'))<span>From: {{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}</span>@endif
    @if(request('to_date'))<span>To: {{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}</span>@endif
    @if(request('filter_material'))<span>Material: "{{ request('filter_material') }}"</span>@endif
    @if(request('filter_category'))<span>Category ID: {{ request('filter_category') }}</span>@endif
    @if(request('filter_status'))<span>Status: {{ request('filter_status') }}</span>@endif
    @if(request('filter_supplier'))<span>Supplier: "{{ request('filter_supplier') }}"</span>@endif
</div>
@endif

<div class="stat-row">
    <div class="sbox s-blue"><div class="sl">Total Materials</div><div class="sv">{{ $totalMaterials }}</div></div>
    <div class="sbox s-green"><div class="sl">Total Stock Qty</div><div class="sv">{{ number_format($totalStockQty,2) }}</div></div>
    <div class="sbox s-amber"><div class="sl">Low Stock Items</div><div class="sv">{{ $lowStockItems }}</div></div>
    <div class="sbox s-red"><div class="sl">Out of Stock Items</div><div class="sv">{{ $outOfStockItems }}</div></div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:25px;">#</th>
            <th>Last Activity Date</th>
            <th>Material / Item Name</th>
            <th>Category</th>
            <th class="r">Opening Stock</th>
            <th class="r">Stock In</th>
            <th class="r">Stock Out</th>
            <th class="r">Available Stock</th>
            <th class="c">Stock Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($materials as $i => $m)
        @php
            $badgeClass = 'sb-approved';
            if ($m->stock_status === 'Low Stock') {
                $badgeClass = 'sb-pending';
            } elseif ($m->stock_status === 'Out of Stock') {
                $badgeClass = 'sb-rejected';
            }
        @endphp
        <tr>
            <td style="color:#94A3B8;">{{ $i+1 }}</td>
            <td>{{ $m->latest_date }}</td>
            <td style="font-weight:600; color:#0F172A;">{{ $m->material_name }}</td>
            <td>{{ $m->materialCategory?->category_name ?? '—' }}</td>
            <td class="r" style="color:#64748B;">{{ number_format($m->computed_opening, 2) }} <small>{{ $m->unit }}</small></td>
            <td class="r" style="color:#0EA5E9;">+{{ number_format($m->computed_inward, 2) }}</td>
            <td class="r" style="color:#EF4444;">-{{ number_format($m->computed_outward, 2) }}</td>
            <td class="r" style="font-weight:700; color:#0F172A;">{{ number_format($m->computed_available, 2) }} <small>{{ $m->unit }}</small></td>
            <td class="c"><span class="status-badge {{ $badgeClass }}">{{ $m->stock_status }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;padding:20px;color:#94A3B8;">No inventory records found.</td></tr>
        @endforelse
    </tbody>
    @if($materials->count() > 0)
    <tfoot>
        <tr>
            <td colspan="4" style="font-size:11px;">Total Summary ({{ $materials->count() }} records)</td>
            <td class="r">{{ number_format($materials->sum('computed_opening'), 2) }}</td>
            <td class="r" style="color:#0EA5E9;">+{{ number_format($materials->sum('computed_inward'), 2) }}</td>
            <td class="r" style="color:#EF4444;">-{{ number_format($materials->sum('computed_outward'), 2) }}</td>
            <td class="r" style="color:#059669; font-size:12px;">{{ number_format($materials->sum('computed_available'), 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="rpt-foot">
    <span>Delawala Management System — Inventory Report</span>
    <span>{{ $materials->count() }} records · Total Stock {{ number_format($totalStockQty,2) }} · {{ now()->format('d M Y') }}</span>
</div>

<script>window.onload=function(){window.print();}</script>
</body>
</html>
