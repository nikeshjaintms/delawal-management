@extends('admin.layouts.app')
@section('title','Cash Flow Report')
@section('page-title','Reports')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.rpt-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 14px; }
.rpt-title-block h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.rpt-title-block p { font-size: 14px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }
.rpt-action-btns { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

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

/* ── Summary Cards ── */
.cf-card-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; margin-bottom: 24px; }
@media(max-width:768px){ .cf-card-grid { grid-template-columns: 1fr; } }
.cf-sum-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 22px 24px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.30);
    transition: transform .25s ease, box-shadow .25s ease;
    display: flex; align-items: center; gap: 18px;
}
.cf-sum-card:hover { transform: translateY(-3px); border-color: rgba(59, 130, 246, 0.40) !important; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45); }
.cf-sum-icon { width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
.cf-sum-icon.inflow  { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); }
.cf-sum-icon.outflow { background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); }
.cf-sum-icon.pos     { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); }
.cf-sum-icon.neg     { background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); }

.cf-sum-body .cf-sum-label { font-size: 11.5px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 6px; }
.cf-sum-body .cf-sum-value { font-size: 26px; font-weight: 800; line-height: 1.1; color: #FFFFFF !important; }
.cf-sum-body .cf-sum-sub { font-size: 12px; color: #CBD5E1 !important; font-weight: 600; margin-top: 4px; }

/* ── Section breakdown cards ── */
.cf-section-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 24px; }
@media(max-width:768px){ .cf-section-grid { grid-template-columns: 1fr; } }
.cf-section-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; overflow: hidden;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.30);
}
.cf-section-hdr { padding: 14px 20px; font-size: 14px; font-weight: 800; display: flex; align-items: center; gap: 9px; }
.cf-section-hdr.in  { background: rgba(16, 185, 129, 0.16) !important; color: #34D399 !important; border-bottom: 1px solid rgba(16, 185, 129, 0.28); }
.cf-section-hdr.out { background: rgba(239, 68, 68, 0.16) !important;  color: #F87171 !important; border-bottom: 1px solid rgba(239, 68, 68, 0.28); }

.cf-section-row { display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); transition: background .15s ease; }
.cf-section-row:last-of-type { border-bottom: none; }
.cf-section-row:hover { background: rgba(255, 255, 255, 0.04); }
.cf-section-row .sr-name { font-size: 14px; font-weight: 700; color: #FFFFFF !important; }
.cf-section-row .sr-note { font-size: 12px; color: #94A3B8 !important; font-weight: 500; margin-top: 3px; }
.cf-section-row .sr-amt { font-size: 15px; font-weight: 800; font-family: monospace; }

.cf-section-total { display: flex; justify-content: space-between; padding: 14px 20px; font-weight: 800; font-size: 15px; font-family: monospace; }
.cf-section-total.in  { background: rgba(16, 185, 129, 0.12) !important; border-top: 1.5px solid rgba(16, 185, 129, 0.30); color: #34D399 !important; }
.cf-section-total.out { background: rgba(239, 68, 68, 0.12) !important;  border-top: 1.5px solid rgba(239, 68, 68, 0.30); color: #F87171 !important; }

/* ── Net row ── */
.cf-net-row {
    padding: 18px 24px; border-radius: 16px; font-weight: 800; font-size: 16px;
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;
    background: rgba(20, 27, 41, 0.70) !important; backdrop-filter: blur(20px);
}
.cf-net-row.pos { border: 1.5px solid rgba(16, 185, 129, 0.40) !important; color: #34D399 !important; box-shadow: 0 6px 22px rgba(16, 185, 129, 0.20); }
.cf-net-row.neg { border: 1.5px solid rgba(239, 68, 68, 0.40) !important; color: #F87171 !important; box-shadow: 0 6px 22px rgba(239, 68, 68, 0.20); }

/* ── Filter & Cards ── */
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

.date-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(56, 189, 248, 0.15) !important; border: 1px solid rgba(56, 189, 248, 0.30) !important; border-radius: 8px; padding: 6px 12px; font-size: 12.5px; color: #38BDF8 !important; font-weight: 700; margin-top: 8px; }

/* ── Transaction Table ── */
.table-wrap { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); background: rgba(16, 22, 34, 0.70); }
.cf-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.cf-table thead th {
    padding: 13px 14px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.cf-table tbody td {
    padding: 13px 14px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #FFFFFF !important; font-weight: 600; vertical-align: middle;
}
.cf-table tbody tr:hover { background: rgba(255, 255, 255, 0.04) !important; }
.cf-table tfoot td {
    padding: 14px 16px !important; background: rgba(255, 255, 255, 0.08) !important;
    font-weight: 800; border-top: 2px solid rgba(255, 255, 255, 0.15) !important;
    color: #FFFFFF !important; white-space: nowrap !important;
}

.amt { text-align: right; font-variant-numeric: tabular-nums; font-family: monospace; }
.inflow-badge  { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); text-transform: uppercase; }
.outflow-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); text-transform: uppercase; }
.mode-chip { background: rgba(255, 255, 255, 0.08) !important; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; color: #CBD5E1 !important; border: 1px solid rgba(255, 255, 255, 0.12); }
.empty-state { text-align: center; padding: 52px 20px; color: #94A3B8; }
.empty-state i { font-size: 40px; margin-bottom: 14px; display: block; opacity: .4; color: #38BDF8; }

/* ── Monthly summary ── */
.cf-monthly-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.cf-monthly-table thead th {
    padding: 13px 14px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
}
.cf-monthly-table tbody td {
    padding: 13px 14px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    color: #FFFFFF !important; font-weight: 600; vertical-align: middle;
}
.cf-monthly-table tbody tr:hover { background: rgba(255, 255, 255, 0.04) !important; }
.cf-monthly-table tfoot td {
    padding: 14px 16px !important; background: rgba(255, 255, 255, 0.08) !important;
    font-weight: 800; border-top: 2px solid rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important;
}

@media print{
    .sidebar,.topbar,.rpt-action-btns,.card-box:has(.filter-bar){display:none!important;}
    .main-content{margin-left:0!important;}
    .content-body{padding:10px!important;}
    body{background:#fff!important;}
    .cf-card-grid,.cf-section-grid{grid-template-columns:repeat(3,1fr)!important;}
}
</style>

@php $isPositive = $netCashFlow >= 0; @endphp

{{-- ── Header ── --}}
<div class="rpt-header">
    <div class="rpt-title-block">
        <h2><i class="fa-solid fa-water" style="color:#38BDF8;margin-right:9px;"></i>Cash Flow Report</h2>
        <p>Transaction-level cash inflow and outflow with net balance.</p>
        @if(request('from_date') || request('to_date'))
            <div>
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
        <a href="{{ route('reports.cash-flow.excel', request()->query()) }}" class="btn-excel">
            <i class="fa-solid fa-file-csv"></i> Export Excel
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-print"></i> Print / PDF
        </button>
    </div>
</div>

{{-- ── 3 Summary Cards ── --}}
<div class="cf-card-grid">
    <div class="cf-sum-card">
        <div class="cf-sum-icon inflow"><i class="fa-solid fa-arrow-trend-up"></i></div>
        <div class="cf-sum-body">
            <div class="cf-sum-label">Total Cash Inflow</div>
            <div class="cf-sum-value" style="color:#34D399 !important;">₹{{ number_format($totalInflow,2) }}</div>
            <div class="cf-sum-sub">Sales + Rental receipts</div>
        </div>
    </div>
    <div class="cf-sum-card">
        <div class="cf-sum-icon outflow"><i class="fa-solid fa-arrow-trend-down"></i></div>
        <div class="cf-sum-body">
            <div class="cf-sum-label">Total Cash Outflow</div>
            <div class="cf-sum-value" style="color:#F87171 !important;">₹{{ number_format($totalOutflow,2) }}</div>
            <div class="cf-sum-sub">Expenses + Loan repayments</div>
        </div>
    </div>
    <div class="cf-sum-card" style="border:1.5px solid {{ $isPositive ? 'rgba(16,185,129,0.35)' : 'rgba(239,68,68,0.35)' }} !important;">
        <div class="cf-sum-icon {{ $isPositive ? 'pos' : 'neg' }}">
            <i class="fa-solid fa-scale-balanced"></i>
        </div>
        <div class="cf-sum-body">
            <div class="cf-sum-label">Net Cash Flow</div>
            <div class="cf-sum-value" style="color:{{ $isPositive ? '#34D399' : '#F87171' }} !important;">
                {{ $isPositive ? '' : '−' }}₹{{ number_format(abs($netCashFlow),2) }}
            </div>
            <div class="cf-sum-sub">Inflow − Outflow</div>
        </div>
    </div>
</div>

{{-- ── Filter ── --}}
<div class="card-box">
    <form method="GET" action="{{ route('reports.cash-flow') }}" class="filter-bar">
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
            <a href="{{ route('reports.cash-flow') }}" class="btn-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        @endif
    </form>
</div>

{{-- ── Section Breakdown ── --}}
<div class="cf-section-grid">
    {{-- INFLOW --}}
    <div class="cf-section-card">
        <div class="cf-section-hdr in">
            <i class="fa-solid fa-arrow-trend-up"></i> A. Cash Inflow
        </div>
        <div class="cf-section-row">
            <div>
                <div class="sr-name">Sales Payment Received</div>
                <div class="sr-note">Property payment receipts from customers</div>
            </div>
            <div class="sr-amt" style="color:#34D399 !important;">₹{{ number_format($totalSalesInflow,2) }}</div>
        </div>
        <div class="cf-section-row">
            <div>
                <div class="sr-name">Rental Payment Received</div>
                <div class="sr-note">Rent collections from tenants</div>
            </div>
            <div class="sr-amt" style="color:#34D399 !important;">₹{{ number_format($totalRentalInflow,2) }}</div>
        </div>
        <div class="cf-section-total in">
            <span><i class="fa-solid fa-calculator" style="margin-right:7px;"></i>Total Inflow</span>
            <span>₹{{ number_format($totalInflow,2) }}</span>
        </div>
    </div>

    {{-- OUTFLOW --}}
    <div class="cf-section-card">
        <div class="cf-section-hdr out">
            <i class="fa-solid fa-arrow-trend-down"></i> B. Cash Outflow
        </div>
        <div class="cf-section-row">
            <div>
                <div class="sr-name">Expenses Paid</div>
                <div class="sr-note">All recorded business expenses</div>
            </div>
            <div class="sr-amt" style="color:#F87171 !important;">₹{{ number_format($totalExpenseOutflow,2) }}</div>
        </div>
        <div class="cf-section-row">
            <div>
                <div class="sr-name">Loan Repayment (EMI Paid)</div>
                <div class="sr-note">EMI payments made on active loans</div>
            </div>
            <div class="sr-amt" style="color:#F87171 !important;">₹{{ number_format($totalLoanOutflow,2) }}</div>
        </div>
        <div class="cf-section-total out">
            <span><i class="fa-solid fa-calculator" style="margin-right:7px;"></i>Total Outflow</span>
            <span>₹{{ number_format($totalOutflow,2) }}</span>
        </div>
    </div>
</div>

{{-- ── Net Cash Flow Row ── --}}
<div class="cf-net-row {{ $isPositive ? 'pos' : 'neg' }}">
    <span>
        <i class="fa-solid {{ $isPositive ? 'fa-circle-check' : 'fa-circle-xmark' }}" style="margin-right:10px;font-size:18px;"></i>
        C. Net Cash Flow &nbsp;
        <span style="font-size:12.5px;font-weight:700;opacity:.85;color:#FFFFFF;">(Total Inflow − Total Outflow)</span>
    </span>
    <span style="font-size:24px;font-family:monospace;">
        {{ $isPositive ? '' : '−' }}₹{{ number_format(abs($netCashFlow),2) }}
    </span>
</div>

{{-- ── Monthly Summary Table ── --}}
@if($monthlyRows->count() > 0)
<div class="card-box">
    <div style="font-size:16px;font-weight:800;color:#FFFFFF !important;margin-bottom:18px;">
        <i class="fa-solid fa-calendar-days" style="color:#38BDF8;margin-right:8px;"></i>
        Monthly Cash Flow Summary
        <span style="font-size:13px;font-weight:700;color:#94A3B8;margin-left:8px;">{{ $monthlyRows->count() }} months</span>
    </div>
    <div class="table-wrap">
        <table class="cf-monthly-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="amt">Cash Inflow (₹)</th>
                    <th class="amt">Cash Outflow (₹)</th>
                    <th class="amt">Net (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyRows as $row)
                @php $rowPos = $row['net'] >= 0; @endphp
                <tr>
                    <td style="font-weight:700;color:#FFFFFF;">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $row['month'])->format('F Y') }}
                    </td>
                    <td class="amt" style="color:#34D399 !important;font-weight:700;">₹{{ number_format($row['inflow'],2) }}</td>
                    <td class="amt" style="color:#F87171 !important;font-weight:700;">₹{{ number_format($row['outflow'],2) }}</td>
                    <td class="amt" style="font-weight:800;color:{{ $rowPos ? '#34D399' : '#F87171' }} !important;">
                        {{ $rowPos ? '' : '−' }}₹{{ number_format(abs($row['net']),2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td style="color:#FFFFFF !important;font-weight:800;">Total</td>
                    <td class="amt" style="color:#34D399 !important;font-size:14px;font-weight:800;">₹{{ number_format($totalInflow,2) }}</td>
                    <td class="amt" style="color:#F87171 !important;font-size:14px;font-weight:800;">₹{{ number_format($totalOutflow,2) }}</td>
                    <td class="amt" style="font-size:15px;font-weight:800;color:{{ $isPositive ? '#34D399' : '#F87171' }} !important;">
                        {{ $isPositive ? '' : '−' }}₹{{ number_format(abs($netCashFlow),2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

{{-- ── Transaction-Level Detail Table ── --}}
<div class="card-box">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
        <div style="font-size:16px;font-weight:800;color:#FFFFFF !important;">
            <i class="fa-solid fa-table-list" style="color:#38BDF8;margin-right:8px;"></i>
            All Cash Transactions
            <span style="font-size:13px;font-weight:700;color:#94A3B8;margin-left:8px;">{{ $allTransactions->count() }} records</span>
        </div>
        <div style="display:flex;gap:12px;font-size:13px;">
            <span style="display:flex;align-items:center;gap:6px;">
                <span class="inflow-badge">Inflow</span>
                <span style="color:#FFFFFF;font-weight:700;">{{ $allTransactions->where('type','inflow')->count() }} txns</span>
            </span>
            <span style="display:flex;align-items:center;gap:6px;">
                <span class="outflow-badge">Outflow</span>
                <span style="color:#FFFFFF;font-weight:700;">{{ $allTransactions->where('type','outflow')->count() }} txns</span>
            </span>
        </div>
    </div>

    @if($allTransactions->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-water"></i>
            <p style="color:#CBD5E1;font-weight:600;">No cash transactions found for the selected period.</p>
            @if(request('from_date') || request('to_date'))
                <a href="{{ route('reports.cash-flow') }}" style="color:#38BDF8;font-weight:700;font-size:13px;margin-top:8px;display:inline-block;text-decoration:none;">
                    Clear filters to see all records
                </a>
            @endif
        </div>
    @else
    <div class="table-wrap">
        <table class="cf-table">
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th>Date</th>
                    <th>Particular</th>
                    <th>Section</th>
                    <th style="text-align:center;">Type</th>
                    <th>Payment Mode</th>
                    <th class="amt">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allTransactions as $i => $txn)
                <tr>
                    <td style="color:#94A3B8;font-size:12.5px;font-weight:700;">{{ $i + 1 }}</td>
                    <td style="white-space:nowrap;font-weight:700;font-size:13px;color:#CBD5E1;">
                        {{ \Carbon\Carbon::parse($txn['date'])->format('d M Y') }}
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;max-width:320px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $txn['particular'] }}
                        </div>
                    </td>
                    <td style="font-size:13px;color:#CBD5E1;font-weight:600;">{{ $txn['section'] }}</td>
                    <td style="text-align:center;">
                        @if($txn['type'] === 'inflow')
                            <span class="inflow-badge">Inflow</span>
                        @else
                            <span class="outflow-badge">Outflow</span>
                        @endif
                    </td>
                    <td>
                        @if($txn['payment_mode'] !== '—' && $txn['payment_mode'] !== '-')
                            <span class="mode-chip">{{ $txn['payment_mode'] }}</span>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td class="amt" style="font-weight:800;font-size:14px;color:{{ $txn['type']==='inflow' ? '#34D399' : '#F87171' }} !important;">
                        {{ $txn['type'] === 'outflow' ? '−' : '+' }}₹{{ number_format($txn['amount'],2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="font-size:13.5px;color:#FFFFFF !important;font-weight:800;">
                        <i class="fa-solid fa-calculator" style="color:#38BDF8;margin-right:6px;"></i>
                        Total ({{ $allTransactions->count() }} transactions)
                    </td>
                    <td></td>
                    <td class="amt">
                        <div style="font-size:13.5px;color:#34D399 !important;font-weight:800;">+₹{{ number_format($totalInflow,2) }}</div>
                        <div style="font-size:13.5px;color:#F87171 !important;font-weight:800;">−₹{{ number_format($totalOutflow,2) }}</div>
                        <div style="font-size:15px;font-weight:800;color:{{ $isPositive ? '#34D399' : '#F87171' }} !important;border-top:1px solid rgba(255,255,255,0.18);margin-top:5px;padding-top:5px;">
                            {{ $isPositive ? '' : '−' }}₹{{ number_format(abs($netCashFlow),2) }}
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Footer --}}
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;
                margin-top:18px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.12);">
        <span style="font-size:13.5px;color:#FFFFFF !important;font-weight:700 !important;">
            Inflow: <strong style="color:#34D399 !important;">₹{{ number_format($totalInflow,2) }}</strong>
            &nbsp;−&nbsp;
            Outflow: <strong style="color:#F87171 !important;">₹{{ number_format($totalOutflow,2) }}</strong>
            &nbsp;=&nbsp;
            <strong style="color:{{ $isPositive ? '#34D399' : '#F87171' }} !important;">
                Net {{ $isPositive ? 'Surplus' : 'Deficit' }} ₹{{ number_format(abs($netCashFlow),2) }}
            </strong>
        </span>
        <span style="font-size:13px;color:#FFFFFF !important;font-weight:700 !important;">
            <i class="fa-regular fa-clock" style="color:#38BDF8;"></i> Generated: {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>
    @endif
</div>

@endsection
