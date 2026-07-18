<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Report</title>
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Segoe UI',Arial,sans-serif; font-size:12px; color:#0F1F35; background:#fff; padding:28px; }

        /* ── Header ── */
        .rpt-header { display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:18px; margin-bottom:22px; border-bottom:2.5px solid #EF4444; }
        .co-name  { font-size:22px; font-weight:800; color:#0F1F35; letter-spacing:0.4px; }
        .co-sub   { font-size:10px; color:#EF4444; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-top:3px; }
        .rpt-meta { text-align:right; }
        .rpt-meta .rpt-title { font-size:15px; font-weight:700; color:#0F1F35; margin-bottom:4px; }
        .rpt-meta .rpt-date  { font-size:11px; color:#64748B; }

        /* ── Applied Filters ── */
        .filter-row { background:#FEF2F2; border:1px solid #FECACA; border-radius:6px; padding:10px 14px; margin-bottom:20px; font-size:11px; color:#991B1B; display:flex; flex-wrap:wrap; gap:12px; }
        .filter-row strong { color:#7F1D1D; }

        /* ── Stat Row ── */
        .stat-row { display:flex; gap:10px; margin-bottom:22px; flex-wrap:wrap; }
        .stat-box { flex:1; min-width:120px; border:1px solid #E5E7EB; border-radius:8px; padding:12px 14px; }
        .stat-box .s-label { font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#64748B; }
        .stat-box .s-value { font-size:18px; font-weight:800; margin-top:4px; color:#0F1F35; }
        .stat-box.s-red   { border-color:rgba(239,68,68,0.3);  background:rgba(239,68,68,0.04); }
        .stat-box.s-red   .s-value { color:#DC2626; }
        .stat-box.s-green { border-color:rgba(16,185,129,0.3); background:rgba(16,185,129,0.03); }
        .stat-box.s-green .s-value { color:#059669; }
        .stat-box.s-amber { border-color:rgba(245,158,11,0.3); background:rgba(245,158,11,0.03); }
        .stat-box.s-amber .s-value { color:#B45309; }
        .stat-box.s-blue  { border-color:rgba(59,130,246,0.3);  background:rgba(59,130,246,0.04); }
        .stat-box.s-blue  .s-value { color:#2563EB; }

        /* ── Main table ── */
        .section-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#EF4444; margin-bottom:10px; margin-top:22px; padding-bottom:6px; border-bottom:1px solid #E5E7EB; display:flex; align-items:center; gap:6px; }
        table { width:100%; border-collapse:collapse; font-size:11px; }
        thead tr { background:#0F172A; }
        thead th { padding:9px 10px; color:#FFF; font-weight:600; text-align:left; font-size:9.5px; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap; }
        thead th.r { text-align:right; }
        thead th.c { text-align:center; }
        tbody tr:nth-child(even) { background:#F9FAFB; }
        tbody td { padding:8px 10px; border-bottom:1px solid #F1F5F9; vertical-align:middle; }
        tbody td.r { text-align:right; font-weight:700; color:#DC2626; }
        tbody td.c { text-align:center; }
        tbody tr:last-child td { border-bottom:none; }
        tfoot tr { background:#F1F5F9; }
        tfoot td { padding:9px 10px; font-weight:800; border-top:2px solid #E5E7EB; }
        tfoot td.r { text-align:right; color:#DC2626; font-size:13px; }
        .badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:9.5px; font-weight:700; text-transform:uppercase; }
        .b-pending  { background:rgba(245,158,11,0.12); color:#B45309; }
        .b-approved { background:rgba(16,185,129,0.12); color:#059669; }
        .b-rejected { background:rgba(239,68,68,0.12); color:#DC2626; }
        .cat-chip { background:rgba(59,130,246,0.1); color:#1D4ED8; padding:2px 7px; border-radius:4px; font-size:10px; font-weight:700; display:inline-block; }
        .mode-chip { background:#F1F5F9; color:#475569; padding:2px 6px; border-radius:4px; font-size:10px; font-weight:600; display:inline-block; }

        /* ── Summary tables ── */
        .summary-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-top:22px; }
        .sum-table { width:100%; border-collapse:collapse; font-size:11px; }
        .sum-table th { padding:7px 10px; background:#F9FAFB; color:#64748B; font-weight:600; border-bottom:1px solid #E5E7EB; font-size:10px; text-transform:uppercase; }
        .sum-table td { padding:7px 10px; border-bottom:1px solid #F1F5F9; }
        .sum-table td.r { text-align:right; font-weight:700; color:#DC2626; }
        .sum-table tfoot td { font-weight:800; border-top:1.5px solid #E5E7EB; }
        .sum-table tfoot td.r { color:#DC2626; }

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
        <div class="rpt-title">Expense Report</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

{{-- ── Active Filters ── --}}
@php
    $hasFilters = request()->hasAny(['from_date','to_date','filter_property','filter_category','filter_vendor','filter_mode','filter_status']);
@endphp
@if($hasFilters)
<div class="filter-row">
    <span><strong>Filters Applied:</strong></span>
    @if(request('from_date'))    <span><strong>From:</strong> {{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}</span> @endif
    @if(request('to_date'))      <span><strong>To:</strong> {{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}</span> @endif
    @if(request('filter_category'))
        @php $fc = $categories->find(request('filter_category')); @endphp
        @if($fc) <span><strong>Category:</strong> {{ $fc->name }}</span> @endif
    @endif
    @if(request('filter_vendor'))
        @php $fv = $vendors->find(request('filter_vendor')); @endphp
        @if($fv) <span><strong>Vendor:</strong> {{ $fv->name }}</span> @endif
    @endif
    @if(request('filter_mode'))   <span><strong>Mode:</strong> {{ request('filter_mode') }}</span> @endif
    @if(request('filter_status')) <span><strong>Status:</strong> {{ request('filter_status') }}</span> @endif
</div>
@endif

{{-- ── Stat Boxes ── --}}
<div class="stat-row">
    <div class="stat-box s-red">
        <div class="s-label">Total Expenses</div>
        <div class="s-value">₹{{ number_format($totalAmount, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Paid / Approved</div>
        <div class="s-value">₹{{ number_format($paidAmount, 2) }}</div>
    </div>
    <div class="stat-box s-amber">
        <div class="s-label">Pending</div>
        <div class="s-value">₹{{ number_format($pendingAmount, 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Today's Expense</div>
        <div class="s-value">₹{{ number_format($todayAmount, 2) }}</div>
    </div>
    <div class="stat-box">
        <div class="s-label">Total Records</div>
        <div class="s-value">{{ $expenses->count() }}</div>
    </div>
</div>

{{-- ── Expense Records Table ── --}}
<div class="section-label">&#9632; Expense Records</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;">#</th>
            <th>Expense Date</th>
            <th>Expense Category</th>
            <th>Vendor / Paid To</th>
            <th>Description</th>
            <th>Payment Mode</th>
            <th class="r">Amount</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $i => $e)
        <tr>
            <td style="color:#9CA3AF;">{{ $i+1 }}</td>
            <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($e->expense_date)->format('d M Y') }}</td>
            <td>
                @if($e->expense_category)
                    <span class="cat-chip">{{ $e->expense_category }}</span>
                @else —
                @endif
            </td>
            <td>
                @if($e->vendor)
                    <strong>{{ $e->vendor->name }}</strong>
                @else
                    {{ $e->paid_to ?? '—' }}
                @endif
            </td>
            <td style="font-weight:500;">{{ $e->expense_title }}</td>
            <td>
                @if($e->payment_mode)
                    <span class="mode-chip">{{ $e->payment_mode }}</span>
                @else —
                @endif
            </td>
            <td class="r">₹{{ number_format($e->amount, 2) }}</td>
            <td class="c">
                @php $st = strtolower($e->approval_status ?? 'pending'); @endphp
                <span class="badge b-{{ $st }}">{{ ucfirst($st) }}</span>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:20px;color:#64748B;">No records found.</td></tr>
        @endforelse
    </tbody>
    @if($expenses->count() > 0)
    <tfoot>
        <tr>
            <td colspan="6" style="font-size:12px;">Total ({{ $expenses->count() }} records)</td>
            <td class="r">₹{{ number_format($totalAmount, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- ── Summary Tables ── --}}
@if($expenses->count() > 0)
<div class="summary-grid">

    {{-- Monthly --}}
    <div>
        <div class="section-label" style="margin-top:16px;">&#9632; Monthly Summary</div>
        <table class="sum-table">
            <thead><tr><th>Month</th><th class="r">Amount</th></tr></thead>
            <tbody>
                @foreach($monthly as $month => $amt)
                <tr><td>{{ $month }}</td><td class="r">₹{{ number_format($amt, 2) }}</td></tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td><strong>Total</strong></td><td class="r"><strong>₹{{ number_format($totalAmount, 2) }}</strong></td></tr>
            </tfoot>
        </table>
    </div>

    {{-- Category-wise --}}
    <div>
        <div class="section-label" style="margin-top:16px;">&#9632; Category Summary</div>
        <table class="sum-table">
            <thead><tr><th>Category</th><th class="r">Amount</th></tr></thead>
            <tbody>
                @foreach($byCategory as $cat => $amt)
                <tr><td>{{ $cat }}</td><td class="r">₹{{ number_format($amt, 2) }}</td></tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td><strong>Total</strong></td><td class="r"><strong>₹{{ number_format($totalAmount, 2) }}</strong></td></tr>
            </tfoot>
        </table>
    </div>

    {{-- Property-wise --}}
    <div>
        <div class="section-label" style="margin-top:16px;">&#9632; Property Summary</div>
        <table class="sum-table">
            <thead><tr><th>Property</th><th class="r">Amount</th></tr></thead>
            <tbody>
                @foreach($byProperty as $prop => $amt)
                <tr><td>{{ $prop }}</td><td class="r">₹{{ number_format($amt, 2) }}</td></tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td><strong>Total</strong></td><td class="r"><strong>₹{{ number_format($totalAmount, 2) }}</strong></td></tr>
            </tfoot>
        </table>
    </div>

</div>
@endif

{{-- ── Footer ── --}}
<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; Expense Report</span>
    <span>{{ $expenses->count() }} records &nbsp;|&nbsp; Total ₹{{ number_format($totalAmount, 2) }} &nbsp;|&nbsp; {{ now()->format('d M Y') }}</span>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
