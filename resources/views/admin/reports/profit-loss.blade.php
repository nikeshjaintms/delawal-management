@extends('admin.layouts.app')
@section('title','Profit & Loss Statement')
@section('page-title','Reports')
@section('content')
<style>
.rpt-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:14px;}
.rpt-title-block h2{font-size:22px;font-weight:800;color:#0F172A;margin-bottom:4px;}
.rpt-title-block p{font-size:13.5px;color:#64748B;}
.rpt-action-btns{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
.btn-pdf{padding:9px 16px;border:1px solid #EF4444;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:7px;color:#EF4444;background:rgba(239,68,68,0.05);text-decoration:none;transition:all .2s ease;}
.btn-pdf:hover{background:rgba(239,68,68,0.12);transform:translateY(-1px);}
.btn-excel{padding:9px 16px;border:1px solid #16803D;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:7px;color:#16803D;background:rgba(34,197,94,0.05);text-decoration:none;transition:all .2s ease;}
.btn-excel:hover{background:rgba(34,197,94,0.12);transform:translateY(-1px);}
.btn-print{padding:9px 16px;border:1px solid #6366F1;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:7px;color:#6366F1;background:rgba(99,102,241,0.05);cursor:pointer;font-family:inherit;transition:all .2s ease;}
.btn-print:hover{background:rgba(99,102,241,0.12);transform:translateY(-1px);}
/* Summary cards */
.pl-stat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:24px;}
@media(max-width:768px){.pl-stat-grid{grid-template-columns:1fr;}}
.pl-stat-card{background:#fff;border:1px solid #E2E8F0;border-radius:16px;padding:22px 24px;
    box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);
    transition:transform .2s ease,box-shadow .2s ease;display:flex;align-items:center;gap:18px;}
.pl-stat-card:hover{transform:translateY(-3px);box-shadow:0 4px 8px rgba(0,0,0,0.07),0 16px 36px rgba(0,0,0,0.09);}
.pl-icon{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;}
.pl-icon.income {background:rgba(16,185,129,0.1);color:#10B981;}
.pl-icon.expense{background:rgba(239,68,68,0.1); color:#EF4444;}
.pl-icon.profit {background:rgba(16,185,129,0.12);color:#059669;}
.pl-icon.loss   {background:rgba(239,68,68,0.12); color:#DC2626;}
.pl-card-body .pl-label{font-size:11.5px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.7px;margin-bottom:6px;}
.pl-card-body .pl-value{font-size:26px;font-weight:800;line-height:1.1;}
.pl-card-body .pl-sub{font-size:12px;color:#94A3B8;margin-top:4px;}
/* Filter */
.card-box{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:20px 22px;
    box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);margin-bottom:18px;}
.filter-bar{display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:5px;}
.filter-label{font-size:11px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.6px;}
.filter-ctrl{padding:9px 12px;border:1.5px solid #E2E8F0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#fff;transition:border-color .18s;min-width:160px;}
.filter-ctrl:focus{border-color:#3B82F6;box-shadow:0 0 0 3px rgba(59,130,246,0.12);}
.btn-filter{background:#0F172A;color:#fff;padding:9px 16px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;align-self:flex-end;display:inline-flex;align-items:center;gap:6px;transition:background .18s;}
.btn-filter:hover{background:#1E293B;}
.btn-reset{padding:9px 10px;color:#64748B;text-decoration:none;font-size:13px;align-self:flex-end;display:inline-flex;align-items:center;gap:5px;}
.btn-reset:hover{color:#0F172A;}
/* P&L Table */
.pl-table-wrap{width:100%;overflow-x:auto;}
.pl-table{width:100%;border-collapse:collapse;font-size:13.5px;}
.pl-table thead th{padding:11px 16px;background:#F8FAFC;color:#475569;font-weight:700;
    border-bottom:2px solid #E2E8F0;font-size:11px;text-transform:uppercase;letter-spacing:.7px;}
.pl-table tbody td{padding:13px 16px;border-bottom:1px solid #F1F5F9;vertical-align:middle;}
.pl-table tbody tr:last-child td{border-bottom:none;}
.pl-table tbody tr{transition:background .14s ease;}
.pl-table tbody tr:hover{background:#F8FAFF;}
.pl-section-hdr td{background:#F1F5F9;font-weight:800;font-size:12px;text-transform:uppercase;
    letter-spacing:.8px;padding:9px 16px!important;color:#475569;border-top:1px solid #E2E8F0;}
.pl-subtotal td{background:#F8FAFC;font-weight:700;border-top:1px solid #E2E8F0;
    border-bottom:1px solid #E2E8F0!important;}
.pl-net td{font-weight:800;font-size:15px;border-top:2px solid #0F172A!important;background:#fff;}
.badge-income {display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;
    background:rgba(16,185,129,0.1);color:#065F46;}
.badge-expense{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;
    background:rgba(239,68,68,0.1);color:#991B1B;}
.badge-net-profit{display:inline-block;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;
    background:rgba(16,185,129,0.12);color:#065F46;}
.badge-net-loss  {display:inline-block;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;
    background:rgba(239,68,68,0.12);color:#991B1B;}
/* Category breakdown */
.cat-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:6px;}
@media(max-width:600px){.cat-grid{grid-template-columns:1fr;}}
.cat-row{display:flex;justify-content:space-between;align-items:center;padding:9px 14px;
    background:#F8FAFC;border:1px solid #F1F5F9;border-radius:8px;font-size:13px;transition:all .15s;}
.cat-row:hover{background:#FFF0F0;border-color:#FECACA;}
.cat-row .cat-name{font-weight:600;color:#0F172A;}
.cat-row .cat-amt{font-weight:700;color:#DC2626;}
/* Date badge */
.date-badge{display:inline-flex;align-items:center;gap:6px;background:#EFF6FF;
    border:1px solid #BFDBFE;border-radius:8px;padding:6px 12px;font-size:12.5px;color:#1E40AF;font-weight:600;}
/* Print */
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
        <h2><i class="fa-solid fa-scale-balanced" style="color:#8B5CF6;margin-right:9px;"></i>Profit & Loss Statement</h2>
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
            <div class="pl-value" style="color:#059669;">₹{{ number_format($totalIncome, 2) }}</div>
            <div class="pl-sub">Sales + Rental receipts</div>
        </div>
    </div>
    <div class="pl-stat-card">
        <div class="pl-icon expense"><i class="fa-solid fa-arrow-trend-down"></i></div>
        <div class="pl-card-body">
            <div class="pl-label">Total Expenses</div>
            <div class="pl-value" style="color:#DC2626;">₹{{ number_format($totalExpense, 2) }}</div>
            <div class="pl-sub">Operations + Loan EMIs</div>
        </div>
    </div>
    <div class="pl-stat-card" style="border:2px solid {{ $isProfit ? 'rgba(16,185,129,0.3)' : 'rgba(239,68,68,0.3)' }};">
        <div class="pl-icon {{ $isProfit ? 'profit' : 'loss' }}">
            <i class="fa-solid {{ $isProfit ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
        </div>
        <div class="pl-card-body">
            <div class="pl-label">Net {{ $isProfit ? 'Profit' : 'Loss' }}</div>
            <div class="pl-value" style="color:{{ $isProfit ? '#059669' : '#DC2626' }};">
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
        <div style="font-size:14px;font-weight:800;color:#0F172A;">
            <i class="fa-solid fa-file-invoice" style="color:#8B5CF6;margin-right:8px;"></i>
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
                    <td colspan="3"><i class="fa-solid fa-arrow-trend-up" style="color:#10B981;margin-right:8px;"></i>Income</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:600;">Property Sales Receipts</div>
                        <div style="font-size:12px;color:#64748B;">Actual payment amounts received from property sales</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-income">Income</span></td>
                    <td style="text-align:right;font-weight:700;color:#059669;font-size:14px;">₹{{ number_format($salesIncome, 2) }}</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:600;">Rental Income Received</div>
                        <div style="font-size:12px;color:#64748B;">Actual paid amounts from rental payment records</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-income">Income</span></td>
                    <td style="text-align:right;font-weight:700;color:#059669;font-size:14px;">₹{{ number_format($rentalIncome, 2) }}</td>
                </tr>
                <tr class="pl-subtotal">
                    <td style="font-size:13.5px;"><i class="fa-solid fa-sigma" style="color:#059669;margin-right:7px;"></i>Total Income</td>
                    <td></td>
                    <td style="text-align:right;font-size:16px;color:#059669;">₹{{ number_format($totalIncome, 2) }}</td>
                </tr>

                {{-- ── EXPENSE SECTION ── --}}
                <tr class="pl-section-hdr">
                    <td colspan="3"><i class="fa-solid fa-arrow-trend-down" style="color:#EF4444;margin-right:8px;"></i>Expenses</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:600;">Operating Expenses</div>
                        <div style="font-size:12px;color:#64748B;">All recorded business expenses</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-expense">Expense</span></td>
                    <td style="text-align:right;font-weight:700;color:#DC2626;font-size:14px;">₹{{ number_format($operatingExpense, 2) }}</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:600;">Loan EMI Payments</div>
                        <div style="font-size:12px;color:#64748B;">Paid EMI amounts from loan schedules</div>
                    </td>
                    <td style="text-align:center;"><span class="badge-expense">Expense</span></td>
                    <td style="text-align:right;font-weight:700;color:#DC2626;font-size:14px;">₹{{ number_format($loanEmiPaid, 2) }}</td>
                </tr>
                <tr class="pl-subtotal">
                    <td style="font-size:13.5px;"><i class="fa-solid fa-sigma" style="color:#DC2626;margin-right:7px;"></i>Total Expenses</td>
                    <td></td>
                    <td style="text-align:right;font-size:16px;color:#DC2626;">₹{{ number_format($totalExpense, 2) }}</td>
                </tr>

                {{-- ── NET PROFIT / LOSS ── --}}
                <tr class="pl-net">
                    <td>
                        <i class="fa-solid {{ $isProfit ? 'fa-circle-check' : 'fa-circle-xmark' }}"
                           style="color:{{ $isProfit ? '#059669' : '#DC2626' }};margin-right:9px;font-size:16px;"></i>
                        <span style="color:{{ $isProfit ? '#059669' : '#DC2626' }};">
                            Net {{ $isProfit ? 'Profit' : 'Loss' }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <span class="{{ $isProfit ? 'badge-net-profit' : 'badge-net-loss' }}">
                            {{ $isProfit ? 'PROFIT' : 'LOSS' }}
                        </span>
                    </td>
                    <td style="text-align:right;font-size:20px;color:{{ $isProfit ? '#059669' : '#DC2626' }};">
                        {{ $isProfit ? '' : '−' }}₹{{ number_format(abs($netProfitLoss), 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;
                margin-top:16px;padding-top:14px;border-top:1px solid #F1F5F9;">
        <span style="font-size:12px;color:#64748B;">
            Total Income: <strong style="color:#059669;">₹{{ number_format($totalIncome,2) }}</strong>
            &nbsp;−&nbsp; Total Expense: <strong style="color:#DC2626;">₹{{ number_format($totalExpense,2) }}</strong>
            &nbsp;=&nbsp;
            <strong style="color:{{ $isProfit ? '#059669' : '#DC2626' }};">
                {{ $isProfit ? 'Profit' : 'Loss' }} ₹{{ number_format(abs($netProfitLoss),2) }}
            </strong>
        </span>
        <span style="font-size:12px;color:#64748B;"><i class="fa-regular fa-clock"></i> Generated: {{ now()->format('d M Y, h:i A') }}</span>
    </div>
</div>

{{-- Expense Breakdown by Category --}}
@if($expenseByCategory->count() > 0)
<div class="card-box">
    <div style="font-size:14px;font-weight:800;color:#0F172A;margin-bottom:16px;">
        <i class="fa-solid fa-chart-pie" style="color:#EF4444;margin-right:8px;"></i>
        Expense Breakdown by Category
    </div>
    <div class="cat-grid">
        @foreach($expenseByCategory as $cat)
        <div class="cat-row">
            <span class="cat-name">
                <i class="fa-solid fa-tag" style="color:#94A3B8;margin-right:6px;font-size:11px;"></i>
                {{ $cat->category }}
            </span>
            <span class="cat-amt">₹{{ number_format($cat->total, 2) }}</span>
        </div>
        @endforeach
    </div>
    <div style="margin-top:14px;padding-top:12px;border-top:1px solid #F1F5F9;
                display:flex;justify-content:space-between;font-size:13px;font-weight:700;color:#0F172A;">
        <span>Total Operating Expenses</span>
        <span style="color:#DC2626;">₹{{ number_format($operatingExpense, 2) }}</span>
    </div>
</div>
@endif

@endsection
