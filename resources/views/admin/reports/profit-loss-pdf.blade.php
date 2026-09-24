<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profit & Loss Statement (Accounting Method)</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Segoe UI',Arial,sans-serif;font-size:12px;color:#0F172A;background:#fff;padding:26px;}
        .rpt-header{display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:16px;margin-bottom:20px;border-bottom:2.5px solid #3B82F6;}
        .co-name{font-size:22px;font-weight:800;color:#0F172A;}
        .co-sub{font-size:10px;color:#3B82F6;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-top:3px;}
        .rpt-meta{text-align:right;}
        .rpt-meta .rpt-title{font-size:15px;font-weight:700;color:#0F172A;margin-bottom:3px;}
        .rpt-meta .rpt-date{font-size:11px;color:#64748B;}

        @if(request()->hasAny(['from_date','to_date']))
        .filter-row{background:#EFF6FF;border:1px solid #BFDBFE;border-radius:6px;padding:9px 14px;margin-bottom:16px;font-size:11px;color:#1E40AF;display:flex;gap:12px;}
        .filter-row strong{color:#1D4ED8;}
        @endif

        .stat-row{display:flex;gap:10px;margin-bottom:20px;}
        .stat-box{flex:1;border:1px solid #E5E7EB;border-radius:7px;padding:10px 12px;}
        .stat-box .s-label{font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#64748B;}
        .stat-box .s-value{font-size:16px;font-weight:800;margin-top:3px;color:#0F172A;}
        .stat-box.s-blue{border-color:rgba(59,130,246,.3);background:rgba(59,130,246,.04);}
        .stat-box.s-blue .s-value{color:#2563EB;}
        .stat-box.s-amber{border-color:rgba(245,158,11,.3);background:rgba(245,158,11,.04);}
        .stat-box.s-amber .s-value{color:#D97706;}
        .stat-box.s-green{border-color:rgba(16,185,129,.3);background:rgba(16,185,129,.04);}
        .stat-box.s-green .s-value{color:#059669;}
        .stat-box.s-red{border-color:rgba(239,68,68,.3);background:rgba(239,68,68,.04);}
        .stat-box.s-red .s-value{color:#DC2626;}

        table{width:100%;border-collapse:collapse;font-size:11px;margin-bottom:20px;}
        thead tr{background:#0F172A;}
        thead th{padding:9px 10px;color:#FFF;font-weight:600;text-align:left;font-size:9.5px;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap;}
        thead th.r{text-align:right;}
        thead th.c{text-align:center;}
        tbody tr:nth-child(even){background:#F9FAFB;}
        tbody td{padding:8px 10px;border-bottom:1px solid #F1F5F9;vertical-align:middle;}
        tbody td.r{text-align:right;font-weight:700;}
        tbody td.c{text-align:center;}
        
        .sec-hdr td{background:#F1F5F9;font-weight:800;font-size:10.5px;text-transform:uppercase;color:#1E293B;padding:8px 10px;}
        .subtotal-row td{background:#F8FAFC;font-weight:800;border-top:1.5px solid #E2E8F0;border-bottom:1.5px solid #E2E8F0;}
        .gross-row td{background:#ECFDF5;font-weight:800;border-top:2px solid #10B981;border-bottom:2px solid #10B981;font-size:12px;color:#065F46;}
        .gross-row.gross-loss td{background:#FEF2F2;border-top:2px solid #EF4444;border-bottom:2px solid #EF4444;color:#991B1B;}
        .net-row td{background:#0F172A;color:#FFF;font-weight:800;font-size:13px;padding:10px;}
        .net-row td.r{color:#34D399;font-size:14px;}
        .net-row.net-loss td.r{color:#F87171;}

        .badge{display:inline-block;padding:2px 7px;border-radius:4px;font-size:9.5px;font-weight:700;text-transform:uppercase;}
        .b-rev{background:rgba(59,130,246,.1);color:#2563EB;}
        .b-cogs{background:rgba(245,158,11,.1);color:#D97706;}
        .b-exp{background:rgba(239,68,68,.1);color:#DC2626;}

        .summary-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-top:16px;}
        .sum-table{width:100%;border-collapse:collapse;font-size:10.5px;}
        .sum-table th{padding:7px 10px;background:#F9FAFB;color:#64748B;font-weight:600;border-bottom:1px solid #E5E7EB;font-size:10px;text-transform:uppercase;}
        .sum-table td{padding:7px 10px;border-bottom:1px solid #F1F5F9;}
        .sum-table td.r{text-align:right;font-weight:700;}
        .sum-table tfoot td{font-weight:800;border-top:1.5px solid #E5E7EB;}

        .rpt-footer{margin-top:24px;padding-top:10px;border-top:1px solid #E5E7EB;display:flex;justify-content:space-between;color:#9CA3AF;font-size:10px;}
        @media print{body{padding:12px;}@page{margin:8mm;}}
    </style>
</head>
<body>

<div class="rpt-header">
    <div><div class="co-name">Delawala</div><div class="co-sub">Properties &amp; Management</div></div>
    <div class="rpt-meta">
        <div class="rpt-title">Profit &amp; Loss Accounting Statement</div>
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
    <div class="stat-box s-blue">
        <div class="s-label">Total Revenue</div>
        <div class="s-value">₹{{ number_format($totalRevenue, 2) }}</div>
    </div>
    <div class="stat-box s-amber">
        <div class="s-label">Cost of Sales (COGS)</div>
        <div class="s-value">₹{{ number_format($totalCostOfSales, 2) }}</div>
    </div>
    <div class="stat-box {{ $grossProfit >= 0 ? 's-green' : 's-red' }}">
        <div class="s-label">Gross Profit ({{ $grossProfitMargin }}%)</div>
        <div class="s-value">₹{{ number_format(abs($grossProfit), 2) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Operating Expenses</div>
        <div class="s-value">₹{{ number_format($totalOperatingExpenses, 2) }}</div>
    </div>
    <div class="stat-box {{ $netProfitLoss >= 0 ? 's-green' : 's-red' }}">
        <div class="s-label">Net {{ $netProfitLoss >= 0 ? 'Profit' : 'Loss' }} ({{ $netProfitMargin }}%)</div>
        <div class="s-value">₹{{ number_format(abs($netProfitLoss), 2) }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:55%;">Particulars / Account Head</th>
            <th style="width:20%;text-align:center;">Nature</th>
            <th class="r" style="width:25%;">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        {{-- Part I: Operating Revenue --}}
        <tr class="sec-hdr">
            <td colspan="3">Part I: Operating Revenue (Turnover)</td>
        </tr>
        <tr>
            <td>Property Sales Revenue</td>
            <td class="c"><span class="badge b-rev">Revenue</span></td>
            <td class="r" style="color:#2563EB;">₹{{ number_format($propertySalesRevenue, 2) }}</td>
        </tr>
        <tr>
            <td>Rental Incomes Received</td>
            <td class="c"><span class="badge b-rev">Revenue</span></td>
            <td class="r" style="color:#2563EB;">₹{{ number_format($rentalRevenue, 2) }}</td>
        </tr>
        @if($otherIncome > 0)
        <tr>
            <td>Other Business Incomes</td>
            <td class="c"><span class="badge b-rev">Revenue</span></td>
            <td class="r" style="color:#2563EB;">₹{{ number_format($otherIncome, 2) }}</td>
        </tr>
        @endif
        <tr class="subtotal-row">
            <td><strong>TOTAL OPERATING REVENUE (A)</strong></td>
            <td></td>
            <td class="r" style="color:#2563EB;">₹{{ number_format($totalRevenue, 2) }}</td>
        </tr>

        {{-- Part II: Cost of Sales --}}
        <tr class="sec-hdr">
            <td colspan="3">Part II: Cost of Sales / Direct Costs (COGS)</td>
        </tr>
        <tr>
            <td>Property Acquisition Cost (Lidhi Price of Sold Units)</td>
            <td class="c"><span class="badge b-cogs">Direct Cost</span></td>
            <td class="r" style="color:#D97706;">₹{{ number_format($propertyPurchaseCost, 2) }}</td>
        </tr>
        <tr>
            <td>Material Stock Purchases &amp; Direct Site Costs</td>
            <td class="c"><span class="badge b-cogs">Direct Cost</span></td>
            <td class="r" style="color:#D97706;">₹{{ number_format($materialPurchaseCost, 2) }}</td>
        </tr>
        <tr class="subtotal-row">
            <td><strong>TOTAL COST OF SALES (B)</strong></td>
            <td></td>
            <td class="r" style="color:#D97706;">₹{{ number_format($totalCostOfSales, 2) }}</td>
        </tr>

        {{-- Part III: Gross Profit --}}
        <tr class="gross-row {{ $grossProfit >= 0 ? '' : 'gross-loss' }}">
            <td><strong>GROSS PROFIT / (LOSS) (A − B)</strong></td>
            <td class="c"><strong>Margin: {{ $grossProfitMargin }}%</strong></td>
            <td class="r">₹{{ number_format($grossProfit, 2) }}</td>
        </tr>

        {{-- Part IV: Operating Expenses --}}
        <tr class="sec-hdr">
            <td colspan="3">Part III: Operating &amp; Indirect Expenses</td>
        </tr>
        <tr>
            <td>Broker Commissions / Sales Incentives</td>
            <td class="c"><span class="badge b-exp">Selling Expense</span></td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($brokerCommissions, 2) }}</td>
        </tr>
        <tr>
            <td>Operating &amp; Administrative Expenses</td>
            <td class="c"><span class="badge b-exp">Admin Expense</span></td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($operatingExpenses, 2) }}</td>
        </tr>
        <tr>
            <td>Finance Charges &amp; Loan EMI Outflows</td>
            <td class="c"><span class="badge b-exp">Finance Expense</span></td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($loanEmiPaid, 2) }}</td>
        </tr>
        <tr class="subtotal-row">
            <td><strong>TOTAL OPERATING EXPENSES (C)</strong></td>
            <td></td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($totalOperatingExpenses, 2) }}</td>
        </tr>

        {{-- Part V: Net Profit --}}
        <tr class="net-row {{ $netProfitLoss >= 0 ? '' : 'net-loss' }}">
            <td>NET {{ $netProfitLoss >= 0 ? 'PROFIT' : 'LOSS' }} BEFORE TAX (Gross Profit − OpEx)</td>
            <td class="c" style="color:#E2E8F0;font-size:11px;">Net Margin: {{ $netProfitMargin }}%</td>
            <td class="r">₹{{ number_format($netProfitLoss, 2) }}</td>
        </tr>
    </tbody>
</table>

@if($expenseByCategory->count() > 0)
<div class="summary-grid">
    <div>
        <table class="sum-table">
            <thead><tr><th>Revenue Summary</th><th class="r">Amount (₹)</th></tr></thead>
            <tbody>
                <tr><td>Property Sales</td><td class="r" style="color:#2563EB;">₹{{ number_format($propertySalesRevenue, 2) }}</td></tr>
                <tr><td>Rental Income</td><td class="r" style="color:#2563EB;">₹{{ number_format($rentalRevenue, 2) }}</td></tr>
                @if($otherIncome > 0)
                <tr><td>Other Incomes</td><td class="r" style="color:#2563EB;">₹{{ number_format($otherIncome, 2) }}</td></tr>
                @endif
            </tbody>
            <tfoot><tr><td><strong>Total Revenue</strong></td><td class="r" style="color:#2563EB;"><strong>₹{{ number_format($totalRevenue, 2) }}</strong></td></tr></tfoot>
        </table>
    </div>
    <div>
        <table class="sum-table">
            <thead><tr><th>Operating Expense by Category</th><th class="r">Amount (₹)</th></tr></thead>
            <tbody>
                @foreach($expenseByCategory as $c)
                <tr><td>{{ $c->category }}</td><td class="r" style="color:#DC2626;">₹{{ number_format($c->total, 2) }}</td></tr>
                @endforeach
            </tbody>
            <tfoot><tr><td><strong>Total Admin Expenses</strong></td><td class="r" style="color:#DC2626;"><strong>₹{{ number_format($operatingExpenses, 2) }}</strong></td></tr></tfoot>
        </table>
    </div>
</div>
@endif

<div class="rpt-footer">
    <span>Delawala Management System — Accounting Profit &amp; Loss Statement</span>
    <span>Net {{ $netProfitLoss >= 0 ? 'Profit' : 'Loss' }}: ₹{{ number_format(abs($netProfitLoss), 2) }} · {{ now()->format('d M Y') }}</span>
</div>

<script>window.onload=function(){window.print();}</script>
</body>
</html>
