<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profit & Loss Statement</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Segoe UI',Arial,sans-serif;font-size:12px;color:#0F172A;background:#fff;padding:26px;}
        .rpt-header{display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:16px;margin-bottom:22px;border-bottom:2.5px solid #3B82F6;}
        .co-name{font-size:22px;font-weight:800;color:#0F172A;}
        .co-sub{font-size:10px;color:#3B82F6;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-top:3px;}
        .rpt-meta{text-align:right;}
        .rpt-meta .rpt-title{font-size:15px;font-weight:700;color:#0F172A;margin-bottom:3px;}
        .rpt-meta .rpt-date{font-size:11px;color:#64748B;}

        @if(request()->hasAny(['from_date','to_date']))
        .filter-row{background:#EFF6FF;border:1px solid #BFDBFE;border-radius:6px;padding:9px 14px;margin-bottom:16px;font-size:11px;color:#1E40AF;display:flex;gap:12px;}
        .filter-row strong{color:#1D4ED8;}
        @endif

        .stat-row{display:flex;gap:12px;margin-bottom:22px;}
        .stat-box{flex:1;border:1px solid #E5E7EB;border-radius:7px;padding:12px 14px;}
        .stat-box .s-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#64748B;}
        .stat-box .s-value{font-size:18px;font-weight:800;margin-top:3px;color:#0F172A;}
        .stat-box.s-green{border-color:rgba(16,185,129,.3);background:rgba(16,185,129,.04);}
        .stat-box.s-green .s-value{color:#059669;}
        .stat-box.s-red{border-color:rgba(239,68,68,.3);background:rgba(239,68,68,.04);}
        .stat-box.s-red .s-value{color:#DC2626;}
        .stat-box.s-blue{border-color:rgba(59,130,246,.3);background:rgba(59,130,246,.04);}
        .stat-box.s-blue .s-value{color:#2563EB;}

        .section-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#3B82F6;margin-bottom:10px;margin-top:20px;padding-bottom:5px;border-bottom:1px solid #E5E7EB;}
        table{width:100%;border-collapse:collapse;font-size:11px;}
        thead tr{background:#0F172A;}
        thead th{padding:9px 10px;color:#FFF;font-weight:600;text-align:left;font-size:9.5px;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap;}
        thead th.r{text-align:right;}
        tbody tr:nth-child(even){background:#F9FAFB;}
        tbody td{padding:8px 10px;border-bottom:1px solid #F1F5F9;vertical-align:middle;}
        tbody td.r{text-align:right;font-weight:700;}
        tbody tr:last-child td{border-bottom:none;}
        tfoot tr{background:#F1F5F9;}
        tfoot td{padding:9px 10px;font-weight:800;border-top:2px solid #E5E7EB;}
        tfoot td.r{text-align:right;font-size:13px;}

        .net-profit{color:#059669;}
        .net-loss{color:#DC2626;}

        .summary-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-top:22px;}
        .sum-table{width:100%;border-collapse:collapse;font-size:10.5px;}
        .sum-table th{padding:7px 10px;background:#F9FAFB;color:#64748B;font-weight:600;border-bottom:1px solid #E5E7EB;font-size:10px;text-transform:uppercase;}
        .sum-table td{padding:7px 10px;border-bottom:1px solid #F1F5F9;}
        .sum-table td.r{text-align:right;font-weight:700;color:#DC2626;}
        .sum-table tfoot td{font-weight:800;border-top:1.5px solid #E5E7EB;}
        .sum-table tfoot td.r{color:#DC2626;}

        .rpt-footer{margin-top:24px;padding-top:10px;border-top:1px solid #E5E7EB;display:flex;justify-content:space-between;color:#9CA3AF;font-size:10px;}
        @media print{body{padding:12px;}@page{margin:8mm;}}
    </style>
</head>
<body>

<div class="rpt-header">
    <div><div class="co-name">Delawala</div><div class="co-sub">Properties &amp; Management</div></div>
    <div class="rpt-meta">
        <div class="rpt-title">Profit &amp; Loss Statement</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

@if(request()->hasAny(['from_date','to_date']))
<div class="filter-row">
    <span><strong>Period:</strong></span>
    @if(request('from_date')) <span>From: {{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}</span> @endif
    @if(request('to_date'))   <span>To: {{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}</span> @endif
</div>
@endif

<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Total Income</div>
        <div class="s-value">₹{{ number_format($totalIncome, 2) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total Expenses</div>
        <div class="s-value">₹{{ number_format($totalExpense, 2) }}</div>
    </div>
    <div class="stat-box {{ $netProfitLoss >= 0 ? 's-green' : 's-red' }}">
        <div class="s-label">Net {{ $netProfitLoss >= 0 ? 'Profit' : 'Loss' }}</div>
        <div class="s-value">₹{{ number_format(abs($netProfitLoss), 2) }}</div>
    </div>
</div>

<div class="section-label">&#9632; Income & Expenses Breakdown</div>
<table>
    <thead>
        <tr>
            <th>Particular</th>
            <th>Type</th>
            <th class="r">Amount</th>
        </tr>
    </thead>
    <tbody>
        {{-- Income Section --}}
        <tr>
            <td><strong>Property Sales Receipts</strong></td>
            <td><span style="background:rgba(16,185,129,.1);color:#059669;padding:2px 7px;border-radius:4px;font-size:10px;font-weight:700;">INCOME</span></td>
            <td class="r" style="color:#059669;">₹{{ number_format($salesIncome, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Rental Income Received</strong></td>
            <td><span style="background:rgba(16,185,129,.1);color:#059669;padding:2px 7px;border-radius:4px;font-size:10px;font-weight:700;">INCOME</span></td>
            <td class="r" style="color:#059669;">₹{{ number_format($rentalIncome, 2) }}</td>
        </tr>
        {{-- Expense Section --}}
        <tr>
            <td><strong>Operating Expenses</strong></td>
            <td><span style="background:rgba(239,68,68,.1);color:#DC2626;padding:2px 7px;border-radius:4px;font-size:10px;font-weight:700;">EXPENSE</span></td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($operatingExpense, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Loan EMI Payments</strong></td>
            <td><span style="background:rgba(239,68,68,.1);color:#DC2626;padding:2px 7px;border-radius:4px;font-size:10px;font-weight:700;">EXPENSE</span></td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($loanEmiPaid, 2) }}</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td><strong>Net {{ $netProfitLoss >= 0 ? 'Profit' : 'Loss' }}</strong></td>
            <td></td>
            <td class="r {{ $netProfitLoss >= 0 ? 'net-profit' : 'net-loss' }}">₹{{ number_format(abs($netProfitLoss), 2) }}</td>
        </tr>
    </tfoot>
</table>

@if($expenseByCategory->count() > 0)
<div class="summary-grid">
    <div>
        <div class="section-label" style="margin-top:16px;">&#9632; Income Summary</div>
        <table class="sum-table">
            <thead><tr><th>Source</th><th class="r">Amount</th></tr></thead>
            <tbody>
                <tr><td>Property Sales</td><td class="r" style="color:#059669;">₹{{ number_format($salesIncome, 2) }}</td></tr>
                <tr><td>Rental Income</td><td class="r" style="color:#059669;">₹{{ number_format($rentalIncome, 2) }}</td></tr>
            </tbody>
            <tfoot><tr><td><strong>Total Income</strong></td><td class="r" style="color:#059669;"><strong>₹{{ number_format($totalIncome, 2) }}</strong></td></tr></tfoot>
        </table>
    </div>
    <div>
        <div class="section-label" style="margin-top:16px;">&#9632; Expense by Category</div>
        <table class="sum-table">
            <thead><tr><th>Category</th><th class="r">Amount</th></tr></thead>
            <tbody>
                @foreach($expenseByCategory as $c)
                <tr><td>{{ $c->category }}</td><td class="r">₹{{ number_format($c->total, 2) }}</td></tr>
                @endforeach
            </tbody>
            <tfoot><tr><td><strong>Total Expenses</strong></td><td class="r"><strong>₹{{ number_format($totalExpense, 2) }}</strong></td></tr></tfoot>
        </table>
    </div>
</div>
@endif

<div class="rpt-footer">
    <span>Delawala Management System — Profit &amp; Loss Statement</span>
    <span>Net {{ $netProfitLoss >= 0 ? 'Profit' : 'Loss' }}: ₹{{ number_format(abs($netProfitLoss), 2) }} · {{ now()->format('d M Y') }}</span>
</div>

<script>window.onload=function(){window.print();}</script>
</body>
</html>
