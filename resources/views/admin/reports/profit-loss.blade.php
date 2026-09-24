@extends('admin.layouts.app')
@section('title','Profit & Loss Statement (Accounting Method)')
@section('page-title','Reports')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.rpt-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 14px; }
.rpt-title-block h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.rpt-title-block p { font-size: 14px; color: #CBD5E1 !important; font-weight: 600 !important; margin: 0; }
.rpt-action-btns { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.btn-pdf {
    padding: 10px 18px; border: 1px solid #EF4444 !important; border-radius: 10px;
    font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 7px;
    color: #FFFFFF !important; background: #DC2626 !important; text-decoration: none !important;
    transition: all .2s ease; box-shadow: 0 4px 14px rgba(220, 38, 38, 0.40);
}
.btn-pdf:hover { background: #B91C1C !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220, 38, 38, 0.60); }

.btn-excel {
    padding: 10px 18px; border: 1px solid #10B981 !important; border-radius: 10px;
    font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 7px;
    color: #FFFFFF !important; background: #059669 !important; text-decoration: none !important;
    transition: all .2s ease; box-shadow: 0 4px 14px rgba(5, 150, 105, 0.40);
}
.btn-excel:hover { background: #047857 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(5, 150, 105, 0.60); }

.btn-print {
    padding: 10px 18px; border: 1px solid #6366F1 !important; border-radius: 10px;
    font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 7px;
    color: #FFFFFF !important; background: #4F46E5 !important; cursor: pointer;
    font-family: inherit; transition: all .2s ease; box-shadow: 0 4px 14px rgba(79, 70, 229, 0.40);
}
.btn-print:hover { background: #4338CA !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(79, 70, 229, 0.60); }

/* 5-Column Accounting Summary Grid */
.pl-stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 24px; }
.pl-stat-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 20px 22px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.30);
    transition: transform .25s ease, box-shadow .25s ease;
    display: flex; align-items: center; gap: 16px;
}
.pl-stat-card:hover { transform: translateY(-3px); border-color: rgba(59, 130, 246, 0.40) !important; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45); }
.pl-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.pl-icon.revenue  { background: rgba(59, 130, 246, 0.18) !important;  color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35); }
.pl-icon.cogs     { background: rgba(245, 158, 11, 0.18) !important;  color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35); }
.pl-icon.gross    { background: rgba(16, 185, 129, 0.18) !important;  color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); }
.pl-icon.expense  { background: rgba(239, 68, 68, 0.18) !important;   color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); }
.pl-icon.net-p    { background: rgba(16, 185, 129, 0.22) !important;  color: #34D399 !important; border: 1.5px solid rgba(16, 185, 129, 0.50); }
.pl-icon.net-l    { background: rgba(239, 68, 68, 0.22) !important;   color: #F87171 !important; border: 1.5px solid rgba(239, 68, 68, 0.50); }

.pl-card-body .pl-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 4px; }
.pl-card-body .pl-value { font-size: 21px; font-weight: 800; line-height: 1.1; color: #FFFFFF !important; font-family: monospace; }
.pl-card-body .pl-sub { font-size: 11.5px; color: #CBD5E1 !important; font-weight: 600; margin-top: 4px; }

/* Filter */
.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 24px;
}
.filter-bar { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
.filter-group { display: flex; flex-direction: column; gap: 6px; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; }
.filter-ctrl {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease; min-width: 160px;
    box-sizing: border-box;
}
.filter-ctrl:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-filter {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; font-family: inherit; align-self: flex-end; display: inline-flex; align-items: center;
    gap: 6px; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35); height: 42px; white-space: nowrap !important;
}
.btn-filter:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset {
    color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 700; padding: 10px 12px;
    align-self: flex-end; display: inline-flex; align-items: center; gap: 5px; transition: color .15s; height: 42px; white-space: nowrap !important;
}
.btn-reset:hover { color: #FFFFFF !important; }

/* Accounting P&L Table */
.pl-table-wrap { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); background: rgba(16, 22, 34, 0.70); }
.pl-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.pl-table thead th {
    padding: 14px 18px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.pl-table tbody td {
    padding: 13px 18px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #FFFFFF !important; font-weight: 600; vertical-align: middle;
}
.pl-table tbody tr:hover { background: rgba(255, 255, 255, 0.04) !important; }

.pl-section-hdr td {
    background: rgba(255, 255, 255, 0.07) !important; font-weight: 800; font-size: 12px;
    text-transform: uppercase; letter-spacing: .9px; padding: 12px 18px !important;
    color: #38BDF8 !important; border-top: 1.5px solid rgba(255, 255, 255, 0.12) !important;
}
.pl-subtotal td {
    background: rgba(255, 255, 255, 0.09) !important; font-weight: 800;
    border-top: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    color: #FFFFFF !important; font-family: monospace; font-size: 14px;
}
.pl-gross-row td {
    background: rgba(16, 185, 129, 0.14) !important; font-weight: 800; font-size: 15px;
    border-top: 2px solid rgba(16, 185, 129, 0.35) !important;
    border-bottom: 2px solid rgba(16, 185, 129, 0.35) !important;
    color: #34D399 !important; font-family: monospace;
}
.pl-gross-row.gross-loss td {
    background: rgba(239, 68, 68, 0.14) !important;
    border-top: 2px solid rgba(239, 68, 68, 0.35) !important;
    border-bottom: 2px solid rgba(239, 68, 68, 0.35) !important;
    color: #F87171 !important;
}
.pl-net td {
    font-weight: 800; font-size: 17px; border-top: 2px solid rgba(255, 255, 255, 0.25) !important;
    background: rgba(20, 27, 41, 0.90) !important; color: #FFFFFF !important; font-family: monospace;
}

.badge-rev     { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; background: rgba(59, 130, 246, 0.18); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.35); text-transform: uppercase; }
.badge-cogs    { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; background: rgba(245, 158, 11, 0.18); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); text-transform: uppercase; }
.badge-expense { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; background: rgba(239, 68, 68, 0.18); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.35); text-transform: uppercase; }
.badge-net-profit { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 800; background: rgba(16, 185, 129, 0.22); color: #34D399; border: 1.5px solid rgba(16, 185, 129, 0.45); }
.badge-net-loss   { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 800; background: rgba(239, 68, 68, 0.22); color: #F87171; border: 1.5px solid rgba(239, 68, 68, 0.45); }

/* Category breakdown */
.cat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 8px; }
@media(max-width:600px){ .cat-grid { grid-template-columns: 1fr; } }
.cat-row {
    display: flex; justify-content: space-between; align-items: center; padding: 14px 18px;
    background: rgba(16, 22, 34, 0.65) !important;
    border: 1px solid rgba(255, 255, 255, 0.10) !important;
    border-radius: 14px !important; font-size: 13.5px; transition: all .2s ease;
}
.cat-row:hover {
    background: rgba(239, 68, 68, 0.12) !important;
    border-color: rgba(239, 68, 68, 0.35) !important;
    transform: translateY(-2px);
}
.cat-row .cat-name { font-weight: 700; color: #FFFFFF !important; display: flex; align-items: center; gap: 8px; font-size: 14px; }
.cat-row .cat-amt { font-weight: 800; font-family: monospace; color: #F87171 !important; font-size: 15px; }

/* Date badge */
.date-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(139, 92, 246, 0.15); border: 1px solid rgba(139, 92, 246, 0.30); border-radius: 8px; padding: 6px 12px; font-size: 12.5px; color: #C084FC; font-weight: 700; }

@media print{
    .sidebar,.topbar,.rpt-action-btns,.filter-bar,.btn-filter,.btn-reset{display:none!important;}
    .main-content{margin-left:0!important;}
    .content-body{padding:10px!important;}
    body{background:#fff!important;}
    .pl-stat-card{box-shadow:none!important;border:1px solid #E2E8F0!important;}
}
</style>

{{-- Header --}}
<div class="rpt-header">
    <div class="rpt-title-block">
        <h2><i class="fa-solid fa-scale-balanced" style="color:#C084FC;margin-right:9px;"></i>Profit & Loss Statement</h2>
        <p>Standard Double-Entry Accounting Method — Revenue, Cost of Sales (COGS), Gross Profit, Operating Expenses & Net Profit.</p>
        @if(request('from_date') || request('to_date'))
        <div style="margin-top:10px;">
            <span class="date-badge">
                <i class="fa-regular fa-calendar"></i>
                {{ request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'All time' }}
                &nbsp;→&nbsp;
                {{ request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'Today' }}
            </span>
        </div>
        @endif
    </div>
    <div class="rpt-action-btns">
        <a href="{{ route('reports.profit-loss.pdf', request()->query()) }}" target="_blank" class="btn-pdf">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('reports.profit-loss.excel', request()->query()) }}" class="btn-excel">
            <i class="fa-solid fa-file-csv"></i> Export Excel
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-print"></i> Print
        </button>
    </div>
</div>

{{-- Summary Cards (5-Metric Accounting Row) --}}
@php 
    $isGrossProfit = $grossProfit >= 0;
    $isNetProfit = $netProfitLoss >= 0;
@endphp
<div class="pl-stat-grid">
    {{-- 1. Total Revenue --}}
    <div class="pl-stat-card">
        <div class="pl-icon revenue"><i class="fa-solid fa-chart-line"></i></div>
        <div class="pl-card-body">
            <div class="pl-label">Operating Revenue</div>
            <div class="pl-value" style="color:#60A5FA !important;">₹{{ number_format($totalRevenue, 2) }}</div>
            <div class="pl-sub">Sales + Rent + Incomes</div>
        </div>
    </div>
    {{-- 2. Cost of Sales --}}
    <div class="pl-stat-card">
        <div class="pl-icon cogs"><i class="fa-solid fa-boxes-packing"></i></div>
        <div class="pl-card-body">
            <div class="pl-label">Cost of Sales (COGS)</div>
            <div class="pl-value" style="color:#FBBF24 !important;">₹{{ number_format($totalCostOfSales, 2) }}</div>
            <div class="pl-sub">Property Purchase + Materials</div>
        </div>
    </div>
    {{-- 3. Gross Profit --}}
    <div class="pl-stat-card" style="border:1.5px solid {{ $isGrossProfit ? 'rgba(16,185,129,0.35)' : 'rgba(239,68,68,0.35)' }} !important;">
        <div class="pl-icon gross">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <div class="pl-card-body">
            <div class="pl-label">Gross Profit</div>
            <div class="pl-value" style="color:{{ $isGrossProfit ? '#34D399' : '#F87171' }} !important;">
                {{ $isGrossProfit ? '' : '−' }}₹{{ number_format(abs($grossProfit), 2) }}
            </div>
            <div class="pl-sub">Gross Margin: {{ $grossProfitMargin }}%</div>
        </div>
    </div>
    {{-- 4. Operating Expenses --}}
    <div class="pl-stat-card">
        <div class="pl-icon expense"><i class="fa-solid fa-receipt"></i></div>
        <div class="pl-card-body">
            <div class="pl-label">Operating Expenses</div>
            <div class="pl-value" style="color:#F87171 !important;">₹{{ number_format($totalOperatingExpenses, 2) }}</div>
            <div class="pl-sub">Brokerage + Admin + Loans</div>
        </div>
    </div>
    {{-- 5. Net Profit / Loss --}}
    <div class="pl-stat-card" style="border:1.5px solid {{ $isNetProfit ? 'rgba(16,185,129,0.50)' : 'rgba(239,68,68,0.50)' }} !important;">
        <div class="pl-icon {{ $isNetProfit ? 'net-p' : 'net-l' }}">
            <i class="fa-solid {{ $isNetProfit ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
        </div>
        <div class="pl-card-body">
            <div class="pl-label">Net {{ $isNetProfit ? 'Profit' : 'Loss' }}</div>
            <div class="pl-value" style="color:{{ $isNetProfit ? '#34D399' : '#F87171' }} !important;">
                {{ $isNetProfit ? '' : '−' }}₹{{ number_format(abs($netProfitLoss), 2) }}
            </div>
            <div class="pl-sub">Net Margin: {{ $netProfitMargin }}%</div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="card-box">
    <form method="GET" action="{{ route('reports.profit-loss') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">From Date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-ctrl @error('from_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">To Date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-ctrl @error('to_date') is-invalid @enderror">
        </div>
        @if(isset($firms) && $firms->count() > 1 && Auth::user()?->isAdmin())
        <div class="filter-group">
            <span class="filter-label">Firm</span>
            <select name="firm_id" class="filter-ctrl">
                <option value="">All Firms</option>
                @foreach($firms as $f)
                    <option value="{{ $f->id }}" {{ request('firm_id') == $f->id ? 'selected' : '' }}>{{ $f->firm_name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <button type="submit" class="btn-filter">
            <i class="fa-solid fa-magnifying-glass"></i> Apply Filter
        </button>
        @if(request('from_date') || request('to_date') || request('firm_id'))
            <a href="{{ route('reports.profit-loss') }}" class="btn-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        @endif
    </form>
</div>

{{-- P&L Statement Table --}}
<div class="card-box">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
        <div style="font-size:16px;font-weight:800;color:#FFFFFF !important;">
            <i class="fa-solid fa-file-invoice" style="color:#C084FC;margin-right:8px;"></i>
            Profit & Loss Accounting Statement
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <span class="badge-net-profit" style="font-size:12px;background:rgba(59,130,246,0.2);color:#60A5FA;border-color:rgba(59,130,246,0.4);">
                Gross Margin: {{ $grossProfitMargin }}%
            </span>
            <span class="{{ $isNetProfit ? 'badge-net-profit' : 'badge-net-loss' }}" style="font-size:12px;padding:6px 14px;">
                Net {{ $isNetProfit ? 'Profit' : 'Loss' }}: {{ $isNetProfit ? '' : '−' }}₹{{ number_format(abs($netProfitLoss), 2) }} ({{ $netProfitMargin }}%)
            </span>
        </div>
    </div>

    <div class="pl-table-wrap">
        <table class="pl-table">
            <thead>
                <tr>
                    <th style="width:50%;">Particulars / Account Head</th>
                    <th style="width:20%;text-align:center;">Accounting Nature</th>
                    <th style="width:30%;text-align:right;">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                {{-- ── SECTION I: OPERATING REVENUE ── --}}
                <tr class="pl-section-hdr">
                    <td colspan="3"><i class="fa-solid fa-arrow-trend-up" style="color:#60A5FA;margin-right:8px;"></i>Part I: Operating Revenue (Turnover)</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Property Sales Revenue</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Total contract value of sold properties & units</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-rev">Operating Revenue</span></td>
                    <td style="text-align:right;font-weight:800;color:#60A5FA !important;font-size:14.5px;font-family:monospace;">₹{{ number_format($propertySalesRevenue, 2) }}</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Rental Incomes Received</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Rental revenue from property leasing agreements</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-rev">Operating Revenue</span></td>
                    <td style="text-align:right;font-weight:800;color:#60A5FA !important;font-size:14.5px;font-family:monospace;">₹{{ number_format($rentalRevenue, 2) }}</td>
                </tr>
                @if($otherIncome > 0)
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Other Business Incomes</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Miscellaneous business earnings & receipts</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-rev">Other Income</span></td>
                    <td style="text-align:right;font-weight:800;color:#60A5FA !important;font-size:14.5px;font-family:monospace;">₹{{ number_format($otherIncome, 2) }}</td>
                </tr>
                @endif
                <tr class="pl-subtotal">
                    <td style="font-size:13.5px;color:#FFFFFF !important;"><i class="fa-solid fa-calculator" style="color:#60A5FA;margin-right:7px;"></i>TOTAL OPERATING REVENUE (A)</td>
                    <td></td>
                    <td style="text-align:right;font-size:15px;color:#60A5FA !important;font-weight:800;">₹{{ number_format($totalRevenue, 2) }}</td>
                </tr>

                {{-- ── SECTION II: COST OF SALES (COGS) ── --}}
                <tr class="pl-section-hdr">
                    <td colspan="3"><i class="fa-solid fa-boxes-packing" style="color:#FBBF24;margin-right:8px;"></i>Part II: Cost of Sales / Direct Costs (COGS)</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Property Acquisition Cost (Lidhi Price)</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Total purchase/acquisition cost of properties sold to customers</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-cogs">Direct Cost (COGS)</span></td>
                    <td style="text-align:right;font-weight:800;color:#FBBF24 !important;font-size:14.5px;font-family:monospace;">₹{{ number_format($propertyPurchaseCost, 2) }}</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Material Stock Purchases & Construction Costs</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Stock inward purchases and direct site material costs</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-cogs">Direct Cost (COGS)</span></td>
                    <td style="text-align:right;font-weight:800;color:#FBBF24 !important;font-size:14.5px;font-family:monospace;">₹{{ number_format($materialPurchaseCost, 2) }}</td>
                </tr>
                <tr class="pl-subtotal">
                    <td style="font-size:13.5px;color:#FFFFFF !important;"><i class="fa-solid fa-calculator" style="color:#FBBF24;margin-right:7px;"></i>TOTAL COST OF SALES (B)</td>
                    <td></td>
                    <td style="text-align:right;font-size:15px;color:#FBBF24 !important;font-weight:800;">₹{{ number_format($totalCostOfSales, 2) }}</td>
                </tr>

                {{-- ── SECTION III: GROSS PROFIT ── --}}
                <tr class="pl-gross-row {{ $isGrossProfit ? '' : 'gross-loss' }}">
                    <td>
                        <i class="fa-solid fa-layer-group" style="margin-right:8px;"></i>
                        GROSS PROFIT / (LOSS) (A − B)
                    </td>
                    <td style="text-align:center;">
                        <span style="font-size:12px;font-weight:800;text-transform:uppercase;">Margin: {{ $grossProfitMargin }}%</span>
                    </td>
                    <td style="text-align:right;font-size:16px;font-weight:800;">
                        {{ $isGrossProfit ? '' : '−' }}₹{{ number_format(abs($grossProfit), 2) }}
                    </td>
                </tr>

                {{-- ── SECTION IV: OPERATING & INDIRECT EXPENSES ── --}}
                <tr class="pl-section-hdr">
                    <td colspan="3"><i class="fa-solid fa-arrow-trend-down" style="color:#F87171;margin-right:8px;"></i>Part III: Operating & Indirect Expenses</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Broker Commissions / Sales Incentives</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Commissions committed to brokers/agents on property sales</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-expense">Selling Expense</span></td>
                    <td style="text-align:right;font-weight:800;color:#F87171 !important;font-size:14.5px;font-family:monospace;">₹{{ number_format($brokerCommissions, 2) }}</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Operating & Administrative Expenses</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Utilities, office, maintenance and general operational outflows</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-expense">Admin Expense</span></td>
                    <td style="text-align:right;font-weight:800;color:#F87171 !important;font-size:14.5px;font-family:monospace;">₹{{ number_format($operatingExpenses, 2) }}</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Finance Charges & Loan EMI Outflows</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Paid EMI and interest obligations from active loan accounts</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-expense">Finance Expense</span></td>
                    <td style="text-align:right;font-weight:800;color:#F87171 !important;font-size:14.5px;font-family:monospace;">₹{{ number_format($loanEmiPaid, 2) }}</td>
                </tr>
                <tr class="pl-subtotal">
                    <td style="font-size:13.5px;color:#FFFFFF !important;"><i class="fa-solid fa-calculator" style="color:#F87171;margin-right:7px;"></i>TOTAL OPERATING EXPENSES (C)</td>
                    <td></td>
                    <td style="text-align:right;font-size:15px;color:#F87171 !important;font-weight:800;">₹{{ number_format($totalOperatingExpenses, 2) }}</td>
                </tr>

                {{-- ── SECTION V: NET PROFIT / LOSS ── --}}
                <tr class="pl-net">
                    <td>
                        <i class="fa-solid {{ $isNetProfit ? 'fa-circle-check' : 'fa-circle-xmark' }}"
                           style="color:{{ $isNetProfit ? '#34D399' : '#F87171' }} !important;margin-right:9px;font-size:18px;"></i>
                        <span style="color:{{ $isNetProfit ? '#34D399' : '#F87171' }} !important;font-size:16px;">
                            NET {{ $isNetProfit ? 'PROFIT' : 'LOSS' }} BEFORE TAX (Gross Profit − Expenses)
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <span class="{{ $isNetProfit ? 'badge-net-profit' : 'badge-net-loss' }}">
                            {{ $isNetProfit ? 'NET PROFIT' : 'NET LOSS' }} ({{ $netProfitMargin }}%)
                        </span>
                    </td>
                    <td style="text-align:right;font-size:21px;color:{{ $isNetProfit ? '#34D399' : '#F87171' }} !important;">
                        {{ $isNetProfit ? '' : '−' }}₹{{ number_format(abs($netProfitLoss), 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;
                margin-top:18px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.12);">
        <span style="font-size:13.5px;color:#FFFFFF !important;font-weight:700 !important;">
            Revenue: <strong style="color:#60A5FA !important;">₹{{ number_format($totalRevenue,2) }}</strong>
            &nbsp;−&nbsp; COGS: <strong style="color:#FBBF24 !important;">₹{{ number_format($totalCostOfSales,2) }}</strong>
            &nbsp;−&nbsp; OpEx: <strong style="color:#F87171 !important;">₹{{ number_format($totalOperatingExpenses,2) }}</strong>
            &nbsp;=&nbsp;
            <strong style="color:{{ $isNetProfit ? '#34D399' : '#F87171' }} !important;">
                {{ $isNetProfit ? 'Net Profit' : 'Net Loss' }} ₹{{ number_format(abs($netProfitLoss),2) }}
            </strong>
        </span>
        <span style="font-size:13px;color:#CBD5E1 !important;font-weight:700 !important;"><i class="fa-regular fa-clock" style="color:#C084FC;"></i> Generated: {{ now()->format('d M Y, h:i A') }}</span>
    </div>
</div>

{{-- Expense Breakdown by Category --}}
@if($expenseByCategory->count() > 0)
<div class="card-box">
    <div style="font-size:16px;font-weight:800;color:#FFFFFF !important;margin-bottom:18px;">
        <i class="fa-solid fa-chart-pie" style="color:#F87171;margin-right:8px;"></i>
        Operating Expense Breakdown by Category
    </div>
    <div class="cat-grid">
        @foreach($expenseByCategory as $cat)
        <div class="cat-row">
            <span class="cat-name">
                <i class="fa-solid fa-tag" style="color:#F87171;margin-right:6px;font-size:12px;"></i>
                {{ $cat->category }}
            </span>
            <span class="cat-amt">₹{{ number_format($cat->total, 2) }}</span>
        </div>
        @endforeach
    </div>
    <div style="margin-top:18px;padding-top:14px;border-top:1px solid rgba(255,255,255,0.12);
                display:flex;justify-content:space-between;align-items:center;font-size:14px;font-weight:800;color:#FFFFFF !important;">
        <span><i class="fa-solid fa-calculator" style="color:#F87171;margin-right:7px;"></i>Total Operating Expenses</span>
        <span style="color:#F87171 !important;font-size:16px;font-family:monospace;">₹{{ number_format($operatingExpenses, 2) }}</span>
    </div>
</div>
@endif

@endsection
