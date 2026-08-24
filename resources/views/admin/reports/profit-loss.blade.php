@extends('admin.layouts.app')
@section('title','Profit & Loss Statement')
@section('page-title','Reports')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.rpt-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 14px; }
.rpt-title-block h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.rpt-title-block p { font-size: 14px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }
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

/* Summary cards */
.pl-stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 24px; }
@media(max-width:768px){ .pl-stat-grid { grid-template-columns: 1fr; } }
.pl-stat-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 22px 24px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.30);
    transition: transform .25s ease, box-shadow .25s ease;
    display: flex; align-items: center; gap: 18px;
}
.pl-stat-card:hover { transform: translateY(-3px); border-color: rgba(59, 130, 246, 0.40) !important; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45); }
.pl-icon { width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
.pl-icon.income  { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); }
.pl-icon.expense { background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); }
.pl-icon.profit  { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); }
.pl-icon.loss    { background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); }

.pl-card-body .pl-label { font-size: 11.5px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 6px; }
.pl-card-body .pl-value { font-size: 26px; font-weight: 800; line-height: 1.1; color: #FFFFFF !important; }
.pl-card-body .pl-sub { font-size: 12px; color: #CBD5E1 !important; font-weight: 600; margin-top: 4px; }

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

/* P&L Table */
.pl-table-wrap { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); background: rgba(16, 22, 34, 0.70); }
.pl-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.pl-table thead th {
    padding: 13px 14px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.pl-table tbody td {
    padding: 14px 16px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #FFFFFF !important; font-weight: 600; vertical-align: middle;
}
.pl-table tbody tr:hover { background: rgba(255, 255, 255, 0.04) !important; }

.pl-section-hdr td {
    background: rgba(255, 255, 255, 0.06) !important; font-weight: 800; font-size: 12px;
    text-transform: uppercase; letter-spacing: .8px; padding: 12px 18px !important;
    color: #94A3B8 !important; border-top: 1px solid rgba(255, 255, 255, 0.10) !important;
}
.pl-subtotal td {
    background: rgba(255, 255, 255, 0.08) !important; font-weight: 800;
    border-top: 1.5px solid rgba(255, 255, 255, 0.12) !important;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.12) !important;
    color: #FFFFFF !important; font-family: monospace;
}
.pl-net td {
    font-weight: 800; font-size: 16px; border-top: 2px solid rgba(255, 255, 255, 0.20) !important;
    background: rgba(20, 27, 41, 0.80) !important; color: #FFFFFF !important; font-family: monospace;
}

.badge-income  { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); text-transform: uppercase; }
.badge-expense { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; background: rgba(239, 68, 68, 0.18); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.35); text-transform: uppercase; }
.badge-net-profit { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 20px; font-size: 11.5px; font-weight: 800; background: rgba(16, 185, 129, 0.22); color: #34D399; border: 1.5px solid rgba(16, 185, 129, 0.45); }
.badge-net-loss   { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 20px; font-size: 11.5px; font-weight: 800; background: rgba(239, 68, 68, 0.22); color: #F87171; border: 1.5px solid rgba(239, 68, 68, 0.45); }

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
        <p>Income vs expense summary — net profit or net loss for the selected period.</p>
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

{{-- Summary Cards --}}
@php $isProfit = $netProfitLoss >= 0; @endphp
<div class="pl-stat-grid">
    <div class="pl-stat-card">
        <div class="pl-icon income"><i class="fa-solid fa-arrow-trend-up"></i></div>
        <div class="pl-card-body">
            <div class="pl-label">Total Income</div>
            <div class="pl-value" style="color:#34D399 !important;">₹{{ number_format($totalIncome, 2) }}</div>
            <div class="pl-sub">Sales + Rental receipts</div>
        </div>
    </div>
    <div class="pl-stat-card">
        <div class="pl-icon expense"><i class="fa-solid fa-arrow-trend-down"></i></div>
        <div class="pl-card-body">
            <div class="pl-label">Total Expenses</div>
            <div class="pl-value" style="color:#F87171 !important;">₹{{ number_format($totalExpense, 2) }}</div>
            <div class="pl-sub">Operations + Loan EMIs</div>
        </div>
    </div>
    <div class="pl-stat-card" style="border:1.5px solid {{ $isProfit ? 'rgba(16,185,129,0.35)' : 'rgba(239,68,68,0.35)' }} !important;">
        <div class="pl-icon {{ $isProfit ? 'profit' : 'loss' }}">
            <i class="fa-solid {{ $isProfit ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
        </div>
        <div class="pl-card-body">
            <div class="pl-label">Net {{ $isProfit ? 'Profit' : 'Loss' }}</div>
            <div class="pl-value" style="color:{{ $isProfit ? '#34D399' : '#F87171' }} !important;">
                {{ $isProfit ? '' : '−' }}₹{{ number_format(abs($netProfitLoss), 2) }}
            </div>
            <div class="pl-sub">Income − Expenses</div>
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
        <button type="submit" class="btn-filter">
            <i class="fa-solid fa-magnifying-glass"></i> Apply Filter
        </button>
        @if(request('from_date') || request('to_date'))
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
            Profit & Loss Statement
        </div>
        <span class="{{ $isProfit ? 'badge-net-profit' : 'badge-net-loss' }}" style="font-size:13px;padding:6px 14px;">
            Net {{ $isProfit ? 'Profit' : 'Loss' }}: {{ $isProfit ? '' : '−' }}₹{{ number_format(abs($netProfitLoss), 2) }}
        </span>
    </div>

    <div class="pl-table-wrap">
        <table class="pl-table">
            <thead>
                <tr>
                    <th style="width:40%;">Particular</th>
                    <th style="width:20%;text-align:center;">Type</th>
                    <th style="width:40%;text-align:right;">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                {{-- ── INCOME SECTION ── --}}
                <tr class="pl-section-hdr">
                    <td colspan="3"><i class="fa-solid fa-arrow-trend-up" style="color:#34D399;margin-right:8px;"></i>Income</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Property Sales Receipts</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Actual payment amounts received from property sales</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-income">Income</span></td>
                    <td style="text-align:right;font-weight:800;color:#34D399 !important;font-size:14.5px;font-family:monospace;">₹{{ number_format($salesIncome, 2) }}</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Rental Income Received</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Actual paid amounts from rental payment records</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-income">Income</span></td>
                    <td style="text-align:right;font-weight:800;color:#34D399 !important;font-size:14.5px;font-family:monospace;">₹{{ number_format($rentalIncome, 2) }}</td>
                </tr>
                <tr class="pl-subtotal">
                    <td style="font-size:13.5px;color:#FFFFFF !important;"><i class="fa-solid fa-calculator" style="color:#34D399;margin-right:7px;"></i>Total Income</td>
                    <td></td>
                    <td style="text-align:right;font-size:15px;color:#34D399 !important;font-weight:800;">₹{{ number_format($totalIncome, 2) }}</td>
                </tr>

                {{-- ── EXPENSE SECTION ── --}}
                <tr class="pl-section-hdr">
                    <td colspan="3"><i class="fa-solid fa-arrow-trend-down" style="color:#F87171;margin-right:8px;"></i>Expenses</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Operating Expenses</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">All recorded business expenses</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-expense">Expense</span></td>
                    <td style="text-align:right;font-weight:800;color:#F87171 !important;font-size:14.5px;font-family:monospace;">₹{{ number_format($operatingExpense, 2) }}</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Loan EMI Payments</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Paid EMI amounts from loan schedules</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-expense">Expense</span></td>
                    <td style="text-align:right;font-weight:800;color:#F87171 !important;font-size:14.5px;font-family:monospace;">₹{{ number_format($loanEmiPaid, 2) }}</td>
                </tr>
                <tr class="pl-subtotal">
                    <td style="font-size:13.5px;color:#FFFFFF !important;"><i class="fa-solid fa-calculator" style="color:#F87171;margin-right:7px;"></i>Total Expenses</td>
                    <td></td>
                    <td style="text-align:right;font-size:15px;color:#F87171 !important;font-weight:800;">₹{{ number_format($totalExpense, 2) }}</td>
                </tr>

                {{-- ── NET PROFIT / LOSS ── --}}
                <tr class="pl-net">
                    <td>
                        <i class="fa-solid {{ $isProfit ? 'fa-circle-check' : 'fa-circle-xmark' }}"
                           style="color:{{ $isProfit ? '#34D399' : '#F87171' }} !important;margin-right:9px;font-size:16px;"></i>
                        <span style="color:{{ $isProfit ? '#34D399' : '#F87171' }} !important;font-size:15px;">
                            Net {{ $isProfit ? 'Profit' : 'Loss' }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <span class="{{ $isProfit ? 'badge-net-profit' : 'badge-net-loss' }}">
                            {{ $isProfit ? 'PROFIT' : 'LOSS' }}
                        </span>
                    </td>
                    <td style="text-align:right;font-size:20px;color:{{ $isProfit ? '#34D399' : '#F87171' }} !important;">
                        {{ $isProfit ? '' : '−' }}₹{{ number_format(abs($netProfitLoss), 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;
                margin-top:18px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.12);">
        <span style="font-size:13.5px;color:#FFFFFF !important;font-weight:700 !important;">
            Total Income: <strong style="color:#34D399 !important;">₹{{ number_format($totalIncome,2) }}</strong>
            &nbsp;−&nbsp; Total Expense: <strong style="color:#F87171 !important;">₹{{ number_format($totalExpense,2) }}</strong>
            &nbsp;=&nbsp;
            <strong style="color:{{ $isProfit ? '#34D399' : '#F87171' }} !important;">
                {{ $isProfit ? 'Profit' : 'Loss' }} ₹{{ number_format(abs($netProfitLoss),2) }}
            </strong>
        </span>
        <span style="font-size:13px;color:#FFFFFF !important;font-weight:700 !important;"><i class="fa-regular fa-clock" style="color:#C084FC;"></i> Generated: {{ now()->format('d M Y, h:i A') }}</span>
    </div>
</div>

{{-- Expense Breakdown by Category --}}
@if($expenseByCategory->count() > 0)
<div class="card-box">
    <div style="font-size:16px;font-weight:800;color:#FFFFFF !important;margin-bottom:18px;">
        <i class="fa-solid fa-chart-pie" style="color:#F87171;margin-right:8px;"></i>
        Expense Breakdown by Category
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
        <span style="color:#F87171 !important;font-size:16px;font-family:monospace;">₹{{ number_format($operatingExpense, 2) }}</span>
    </div>
</div>
@endif

@endsection
