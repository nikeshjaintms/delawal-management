<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Master Report - Delawala Management</title>
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

        /* ── Applied Filters ── */
        .filter-row { background:#FFF7ED; border:1px solid #FFEDD5; border-radius:6px; padding:8px 12px; margin-bottom:18px; font-size:11px; color:#9A3412; display:flex; flex-wrap:wrap; gap:12px; }
        .filter-row strong { color:#7C2D12; }

        /* ── Stat Row ── */
        .stat-row { display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; }
        .stat-box { flex:1; min-width:110px; border:1px solid #E5E7EB; border-radius:8px; padding:10px 12px; background:#F8FAFC; }
        .stat-box .s-label { font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#64748B; }
        .stat-box .s-value { font-size:16px; font-weight:800; margin-top:4px; color:#0F1F35; }
        .stat-box.s-orange { border-color:rgba(252,105,0,0.3); background:rgba(252,105,0,0.03); }
        .stat-box.s-orange .s-value { color:#e05c00; }
        .stat-box.s-green { border-color:rgba(16,185,129,0.3); background:rgba(16,185,129,0.03); }
        .stat-box.s-green .s-value { color:#059669; }
        .stat-box.s-amber { border-color:rgba(245,158,11,0.3); background:rgba(245,158,11,0.03); }
        .stat-box.s-amber .s-value { color:#B45309; }

        /* ── Main table ── */
        .section-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#fc6900ff; margin-bottom:8px; margin-top:16px; padding-bottom:4px; border-bottom:1px solid #E5E7EB; }
        table { width:100%; border-collapse:collapse; font-size:11px; }
        thead tr { background:#0F172A; }
        thead th { padding:8px 10px; color:#FFF; font-weight:600; text-align:left; font-size:9.5px; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap; }
        thead th.r { text-align:right; }
        thead th.c { text-align:center; }
        tbody tr:nth-child(even) { background:#F9FAFB; }
        tbody td { padding:8px 10px; border-bottom:1px solid #F1F5F9; vertical-align:middle; }
        tbody td.r { text-align:right; font-weight:700; color:#0F1F35; }
        tbody td.c { text-align:center; }
        tbody tr:last-child td { border-bottom:none; }
        tfoot tr { background:#F1F5F9; }
        tfoot td { padding:9px 10px; font-weight:800; border-top:2px solid #E5E7EB; }
        tfoot td.r { text-align:right; color:#e05c00; font-size:12px; }

        .badge { display:inline-block; padding:2px 7px; border-radius:10px; font-size:9px; font-weight:700; text-transform:uppercase; }
        .badge-success { background:#ECFDF5; color:#059669; border:1px solid #A7F3D0; }
        .badge-warning { background:#FFFBEB; color:#D97706; border:1px solid #FDE68A; }
        .badge-danger  { background:#FEF2F2; color:#DC2626; border:1px solid #FECACA; }
        .badge-info    { background:#EFF6FF; color:#2563EB; border:1px solid #BFDBFE; }

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
        <div class="rpt-title">Property Master Directory</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

@if(request()->hasAny(['search', 'status', 'firm_id']))
<div class="filter-row">
    <span><strong>Active Filters:</strong></span>
    @if(request('search')) <span><strong>Search:</strong> "{{ request('search') }}"</span> @endif
    @if(request('status')) <span><strong>Status:</strong> {{ ucfirst(request('status')) }}</span> @endif
</div>
@endif

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Properties</div>
        <div class="s-value">{{ $propertyMasters->count() }}</div>
    </div>
    <div class="stat-box s-orange">
        <div class="s-label">Total Purchase Value</div>
        <div class="s-value">₹{{ number_format($totalPurchasePrice, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Total Paid</div>
        <div class="s-value">₹{{ number_format($totalPaid, 2) }}</div>
    </div>
    <div class="stat-box s-amber">
        <div class="s-label">Total Due / Balance</div>
        <div class="s-value">₹{{ number_format($totalDue, 2) }}</div>
    </div>
    <div class="stat-box">
        <div class="s-label">Total Plots Generated</div>
        <div class="s-value">{{ $totalPlots }}</div>
    </div>
</div>

<div class="section-label">&#9632; Master Property List</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;">#</th>
            <th>Property Name / Code</th>
            <th>Location</th>
            <th>Firm</th>
            <th>Assigned Project(s)</th>
            <th class="c">Plots</th>
            <th class="r">Purchase Price</th>
            <th class="r">Paid Amount</th>
            <th class="r">Due Amount</th>
            <th class="c">Payment Status</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($propertyMasters as $i => $item)
        <tr>
            <td style="color:#9CA3AF;">{{ $i+1 }}</td>
            <td>
                <strong>{{ $item->property_name }}</strong><br>
                <span style="font-size:9.5px; color:#64748B;">{{ $item->property_code }}</span>
                @if($item->property_type)
                    <br><span style="font-size:9px; color:#7C3AED; font-weight:700;">[{{ $item->property_type }}]</span>
                @endif
            </td>
            <td>
                {{ $item->location ?: '-' }}
                @if($item->city) <br><span style="font-size:9.5px; color:#64748B;">{{ $item->city }}</span> @endif
            </td>
            <td>{{ $item->firm->firm_name ?? '-' }}</td>
            <td>
                @php
                    $allProjs = $item->all_projects ?? collect();
                @endphp
                @if($allProjs->isNotEmpty())
                    @foreach($allProjs as $prj)
                        <span style="display:inline-block; background:#EEF2FF; color:#4338CA; padding:2px 6px; border-radius:4px; font-size:9.5px; font-weight:600; margin:1px;">{{ $prj->project_name }}</span>
                    @endforeach
                @else
                    <span style="color:#9CA3AF;">-</span>
                @endif
            </td>
            <td class="c"><strong>{{ $item->plots_count ?? $item->plots->count() }}</strong></td>
            <td class="r">₹{{ number_format($item->purchase_price, 2) }}</td>
            <td class="r" style="color:#059669;">₹{{ number_format($item->paid_amount, 2) }}</td>
            <td class="r" style="color:#B45309;">₹{{ number_format($item->due_amount, 2) }}</td>
            <td class="c">
                @if($item->payment_status === 'paid')
                    <span class="badge badge-success">Paid</span>
                @elseif($item->payment_status === 'partial')
                    <span class="badge badge-warning">Partial</span>
                @else
                    <span class="badge badge-danger">Unpaid</span>
                @endif
            </td>
            <td class="c">
                @if($item->status === 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-danger">{{ ucfirst($item->status) }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="11" style="text-align:center;padding:20px;color:#64748B;">No property master records found.</td></tr>
        @endforelse
    </tbody>
    @if($propertyMasters->count() > 0)
    <tfoot>
        <tr>
            <td colspan="6" style="font-size:11px;">Total ({{ $propertyMasters->count() }} properties)</td>
            <td class="r">₹{{ number_format($totalPurchasePrice, 2) }}</td>
            <td class="r" style="color:#059669;">₹{{ number_format($totalPaid, 2) }}</td>
            <td class="r" style="color:#e05c00;">₹{{ number_format($totalDue, 2) }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; Property Master Directory</span>
    <span>{{ $propertyMasters->count() }} properties &nbsp;|&nbsp; Total Purchase: ₹{{ number_format($totalPurchasePrice, 2) }} &nbsp;|&nbsp; {{ now()->format('d M Y') }}</span>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
