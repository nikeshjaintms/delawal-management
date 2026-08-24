@extends('admin.layouts.app')
@section('title','Balance Sheet')
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
.bs-card-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 24px; }
@media(max-width:768px){ .bs-card-grid { grid-template-columns: 1fr; } }
.bs-sum-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 22px 24px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.30);
    transition: transform .25s ease, box-shadow .25s ease;
    display: flex; align-items: center; gap: 18px;
}
.bs-sum-card:hover { transform: translateY(-3px); border-color: rgba(59, 130, 246, 0.40) !important; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45); }
.bs-sum-icon { width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
.bs-sum-icon.asset  { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); }
.bs-sum-icon.liab   { background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); }
.bs-sum-icon.profit { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); }
.bs-sum-icon.loss   { background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); }

.bs-sum-body .bs-sum-label { font-size: 11.5px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 6px; }
.bs-sum-body .bs-sum-value { font-size: 26px; font-weight: 800; line-height: 1.1; color: #FFFFFF !important; }
.bs-sum-body .bs-sum-sub { font-size: 12px; color: #CBD5E1 !important; font-weight: 600; margin-top: 4px; }

/* ── Filter ── */
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
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease; min-width: 180px;
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

.date-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.30); border-radius: 8px; padding: 6px 12px; font-size: 12.5px; color: #38BDF8; font-weight: 700; margin-top: 8px; }

/* ── Balance Sheet Panels ── */
.bs-wrap { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
@media(max-width:900px){ .bs-wrap { grid-template-columns: 1fr; } }
.bs-panel {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; overflow: hidden;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.30);
}
.bs-panel-hdr { padding: 14px 20px; font-size: 14px; font-weight: 800; display: flex; align-items: center; gap: 9px; letter-spacing: .3px; }
.bs-panel-hdr.asset { background: rgba(16, 185, 129, 0.16) !important; color: #34D399 !important; border-bottom: 1px solid rgba(16, 185, 129, 0.28); }
.bs-panel-hdr.liab  { background: rgba(239, 68, 68, 0.16) !important;  color: #F87171 !important; border-bottom: 1px solid rgba(239, 68, 68, 0.28); }

.bs-item { display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); transition: background .15s ease; }
.bs-item:last-of-type { border-bottom: none; }
.bs-item:hover { background: rgba(255, 255, 255, 0.04); }
.bs-item .bs-item-name { font-size: 14px; font-weight: 700; color: #FFFFFF !important; }
.bs-item .bs-item-note { font-size: 12px; color: #94A3B8 !important; font-weight: 500; margin-top: 3px; }
.bs-item .bs-item-amt { font-size: 15px; font-weight: 800; font-family: monospace; }

.bs-subtotal { display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; font-weight: 800; font-size: 15px; font-family: monospace; }
.bs-subtotal.asset { background: rgba(16, 185, 129, 0.12) !important; border-top: 1.5px solid rgba(16, 185, 129, 0.30); color: #34D399 !important; }
.bs-subtotal.liab  { background: rgba(239, 68, 68, 0.12) !important;  border-top: 1.5px solid rgba(239, 68, 68, 0.30); color: #F87171 !important; }

/* ── Net Worth Row ── */
.bs-net-row {
    padding: 18px 24px; border-radius: 16px; font-weight: 800; font-size: 16px;
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;
    background: rgba(20, 27, 41, 0.70) !important; backdrop-filter: blur(20px);
}
.bs-net-row.profit { border: 1.5px solid rgba(16, 185, 129, 0.40) !important; color: #34D399 !important; box-shadow: 0 6px 22px rgba(16, 185, 129, 0.20); }
.bs-net-row.loss   { border: 1.5px solid rgba(239, 68, 68, 0.40) !important; color: #F87171 !important; box-shadow: 0 6px 22px rgba(239, 68, 68, 0.20); }

/* ── Stat badges ── */
.bs-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .3px; }
.bs-badge.asset { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); }
.bs-badge.liab  { background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); }
.bs-badge.equity.profit { background: rgba(16, 185, 129, 0.22) !important; color: #34D399 !important; border: 1.5px solid rgba(16, 185, 129, 0.45); }
.bs-badge.equity.loss   { background: rgba(239, 68, 68, 0.22) !important;  color: #F87171 !important; border: 1.5px solid rgba(239, 68, 68, 0.45); }

/* ── Detail table ── */
.table-wrap { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); background: rgba(16, 22, 34, 0.70); }
.bs-full-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.bs-full-table thead th {
    padding: 13px 14px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
}
.bs-full-table tbody td {
    padding: 14px 16px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #FFFFFF !important; font-weight: 600; vertical-align: middle;
}
.bs-full-table tbody tr:hover { background: rgba(255, 255, 255, 0.04) !important; }
.amt { text-align: right; font-variant-numeric: tabular-nums; font-family: monospace; }
.section-divider td {
    background: rgba(255, 255, 255, 0.06) !important; font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .8px; color: #94A3B8 !important; padding: 12px 16px !important;
    border-top: 1px solid rgba(255, 255, 255, 0.10);
}

@media print{
    .sidebar,.topbar,.rpt-action-btns,.filter-bar,.btn-filter,.btn-reset{display:none!important;}
    .main-content{margin-left:0!important;}
    .content-body{padding:10px!important;}
    body{background:#fff!important;}
    .bs-wrap{grid-template-columns:1fr 1fr!important;}
    .bs-sum-card,.bs-panel{box-shadow:none!important;border:1px solid #E2E8F0!important;}
}
</style>

@php $isPositive = $netWorth >= 0; @endphp

{{-- ── Header ── --}}
<div class="rpt-header">
    <div class="rpt-title-block">
        <h2><i class="fa-solid fa-sheet-plastic" style="color:#38BDF8;margin-right:9px;"></i>Balance Sheet</h2>
        <p>Assets, liabilities, and net worth snapshot of the firm.</p>
        @if(request('as_on_date'))
            <div>
                <span class="date-badge">
                    <i class="fa-regular fa-calendar-check"></i>
                    As on: {{ \Carbon\Carbon::parse(request('as_on_date'))->format('d M Y') }}
                </span>
            </div>
        @endif
    </div>
    <div class="rpt-action-btns">
        <a href="{{ route('reports.balance-sheet.excel', request()->query()) }}" class="btn-excel">
            <i class="fa-solid fa-file-csv"></i> Export Excel
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-print"></i> Print / PDF
        </button>
    </div>
</div>

{{-- ── 3 Summary Cards ── --}}
<div class="bs-card-grid">
    <div class="bs-sum-card">
        <div class="bs-sum-icon asset"><i class="fa-solid fa-arrow-trend-up"></i></div>
        <div class="bs-sum-body">
            <div class="bs-sum-label">Total Assets</div>
            <div class="bs-sum-value" style="color:#34D399 !important;">₹{{ number_format($totalAssets,2) }}</div>
            <div class="bs-sum-sub">Cash + Receivables + Property</div>
        </div>
    </div>
    <div class="bs-sum-card">
        <div class="bs-sum-icon liab"><i class="fa-solid fa-arrow-trend-down"></i></div>
        <div class="bs-sum-body">
            <div class="bs-sum-label">Total Liabilities</div>
            <div class="bs-sum-value" style="color:#F87171 !important;">₹{{ number_format($totalLiabilities,2) }}</div>
            <div class="bs-sum-sub">Loans + Payables + Credit Notes</div>
        </div>
    </div>
    <div class="bs-sum-card" style="border:1.5px solid {{ $isPositive ? 'rgba(16,185,129,0.35)' : 'rgba(239,68,68,0.35)' }} !important;">
        <div class="bs-sum-icon {{ $isPositive ? 'profit' : 'loss' }}">
            <i class="fa-solid fa-scale-balanced"></i>
        </div>
        <div class="bs-sum-body">
            <div class="bs-sum-label">Net Worth / Equity</div>
            <div class="bs-sum-value" style="color:{{ $isPositive ? '#34D399' : '#F87171' }} !important;">
                {{ $isPositive ? '' : '−' }}₹{{ number_format(abs($netWorth),2) }}
            </div>
            <div class="bs-sum-sub">Assets − Liabilities</div>
        </div>
    </div>
</div>

{{-- ── Filter ── --}}
<div class="card-box">
    <form method="GET" action="{{ route('reports.balance-sheet') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">As On Date</span>
            <input type="date" name="as_on_date" value="{{ request('as_on_date') }}" class="filter-ctrl @error('as_on_date') is-invalid @enderror">
        </div>
        <button type="submit" class="btn-filter">
            <i class="fa-solid fa-magnifying-glass"></i> Apply Filter
        </button>
        @if(request('as_on_date'))
            <a href="{{ route('reports.balance-sheet') }}" class="btn-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        @endif
    </form>
</div>

{{-- ── Side-by-side Assets / Liabilities panels ── --}}
<div class="bs-wrap">

    {{-- ASSETS PANEL --}}
    <div class="bs-panel">
        <div class="bs-panel-hdr asset">
            <i class="fa-solid fa-arrow-trend-up"></i>
            A. Assets
        </div>

        <div class="bs-item">
            <div>
                <div class="bs-item-name">Cash / Bank — Sales Receipts</div>
                <div class="bs-item-note">Payment amounts received from property sales</div>
            </div>
            <div class="bs-item-amt" style="color:#34D399 !important;">₹{{ number_format($cashReceived,2) }}</div>
        </div>

        <div class="bs-item">
            <div>
                <div class="bs-item-name">Cash / Bank — Rental Collections</div>
                <div class="bs-item-note">Rental payment amounts actually collected</div>
            </div>
            <div class="bs-item-amt" style="color:#34D399 !important;">₹{{ number_format($rentalCashReceived,2) }}</div>
        </div>

        <div class="bs-item">
            <div>
                <div class="bs-item-name">Receivables — Pending Customer Dues</div>
                <div class="bs-item-note">Remaining amounts on pending/partial sales</div>
            </div>
            <div class="bs-item-amt" style="color:#38BDF8 !important;">₹{{ number_format($receivables,2) }}</div>
        </div>

        <div class="bs-item">
            <div>
                <div class="bs-item-name">Property Value — Unsold / Booked</div>
                <div class="bs-item-note">Listed price of available & booked properties</div>
            </div>
            <div class="bs-item-amt" style="color:#C084FC !important;">₹{{ number_format($propertyValue,2) }}</div>
        </div>

        <div class="bs-item">
            <div>
                <div class="bs-item-name">Security Deposits Held</div>
                <div class="bs-item-note">Deposits from active rental agreements</div>
            </div>
            <div class="bs-item-amt" style="color:#FBBF24 !important;">₹{{ number_format($securityDeposits,2) }}</div>
        </div>

        <div class="bs-subtotal asset">
            <span><i class="fa-solid fa-calculator" style="margin-right:7px;"></i>Total Assets</span>
            <span>₹{{ number_format($totalAssets,2) }}</span>
        </div>
    </div>

    {{-- LIABILITIES PANEL --}}
    <div class="bs-panel">
        <div class="bs-panel-hdr liab">
            <i class="fa-solid fa-arrow-trend-down"></i>
            B. Liabilities
        </div>

        <div class="bs-item">
            <div>
                <div class="bs-item-name">Outstanding Loan Balance</div>
                <div class="bs-item-note">
                    Pending EMI principal across all loans
                    @if($loanTotal > 0)
                        &nbsp;·&nbsp; ₹{{ number_format($loanPaid,2) }} paid of ₹{{ number_format($loanTotal,2) }}
                    @endif
                </div>
            </div>
            <div class="bs-item-amt" style="color:#F87171 !important;">₹{{ number_format($loanOutstanding,2) }}</div>
        </div>

        <div class="bs-item">
            <div>
                <div class="bs-item-name">Unpaid / Pending Expenses</div>
                <div class="bs-item-note">Expenses with approval status: Pending</div>
            </div>
            <div class="bs-item-amt" style="color:#F87171 !important;">₹{{ number_format($unpaidExpenses,2) }}</div>
        </div>

        <div class="bs-item">
            <div>
                <div class="bs-item-name">Credit Notes Payable</div>
                <div class="bs-item-note">Pending/Approved credit notes owed to customers</div>
            </div>
            <div class="bs-item-amt" style="color:#F87171 !important;">₹{{ number_format($creditNotePayable,2) }}</div>
        </div>

        {{-- Spacer rows to match height visually --}}
        <div class="bs-item" style="opacity:0.35;">
            <div><div class="bs-item-name" style="color:#94A3B8 !important;">Other Liabilities</div><div class="bs-item-note">—</div></div>
            <div class="bs-item-amt" style="color:#94A3B8 !important;">₹0.00</div>
        </div>

        <div class="bs-item" style="opacity:0.35;">
            <div><div class="bs-item-name" style="color:#94A3B8 !important;">Other Payables</div><div class="bs-item-note">—</div></div>
            <div class="bs-item-amt" style="color:#94A3B8 !important;">₹0.00</div>
        </div>

        <div class="bs-subtotal liab">
            <span><i class="fa-solid fa-calculator" style="margin-right:7px;"></i>Total Liabilities</span>
            <span>₹{{ number_format($totalLiabilities,2) }}</span>
        </div>
    </div>

</div>

{{-- ── Net Worth / Equity Row ── --}}
<div class="bs-net-row {{ $isPositive ? 'profit' : 'loss' }}">
    <span>
        <i class="fa-solid {{ $isPositive ? 'fa-circle-check' : 'fa-circle-xmark' }}" style="margin-right:10px;font-size:18px;"></i>
        C. Net Worth / Equity &nbsp;
        <span style="font-size:12.5px;font-weight:700;opacity:.85;color:#FFFFFF;">(Total Assets − Total Liabilities)</span>
    </span>
    <span style="font-size:24px;font-family:monospace;">
        {{ $isPositive ? '' : '−' }}₹{{ number_format(abs($netWorth),2) }}
    </span>
</div>

{{-- ── Full Detail Table ── --}}
<div class="card-box">
    <div style="font-size:16px;font-weight:800;color:#FFFFFF !important;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <span><i class="fa-solid fa-table-list" style="color:#38BDF8;margin-right:8px;"></i>Detailed Balance Sheet</span>
        <span class="bs-badge equity {{ $isPositive ? 'profit' : 'loss' }}" style="font-size:13px;padding:6px 14px;">
            Net Worth: {{ $isPositive ? '' : '−' }}₹{{ number_format(abs($netWorth),2) }}
        </span>
    </div>

    <div class="table-wrap">
        <table class="bs-full-table">
            <thead>
                <tr>
                    <th style="width:45%;">Particular</th>
                    <th style="width:15%;text-align:center;">Category</th>
                    <th style="width:40%;" class="amt">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>

                {{-- ASSETS SECTION --}}
                <tr class="section-divider">
                    <td colspan="3"><i class="fa-solid fa-arrow-trend-up" style="color:#34D399;margin-right:8px;"></i>A. Assets</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Cash / Bank — Sales Receipts</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Payment amounts received from property sales</div>
                    </td>
                    <td style="text-align:center;"><span class="bs-badge asset">Asset</span></td>
                    <td class="amt" style="color:#34D399 !important;font-weight:800;font-size:14.5px;">₹{{ number_format($cashReceived,2) }}</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Cash / Bank — Rental Collections</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Rental payment amounts actually collected</div>
                    </td>
                    <td style="text-align:center;"><span class="bs-badge asset">Asset</span></td>
                    <td class="amt" style="color:#34D399 !important;font-weight:800;font-size:14.5px;">₹{{ number_format($rentalCashReceived,2) }}</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Receivables — Pending Customer Dues</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Remaining amounts on pending/partial property sales</div>
                    </td>
                    <td style="text-align:center;"><span class="bs-badge asset">Asset</span></td>
                    <td class="amt" style="color:#38BDF8 !important;font-weight:800;font-size:14.5px;">₹{{ number_format($receivables,2) }}</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Property Value — Unsold / Booked</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Listed price of available &amp; booked properties</div>
                    </td>
                    <td style="text-align:center;"><span class="bs-badge asset">Asset</span></td>
                    <td class="amt" style="color:#C084FC !important;font-weight:800;font-size:14.5px;">₹{{ number_format($propertyValue,2) }}</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Security Deposits Held</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Deposits from active rental agreements</div>
                    </td>
                    <td style="text-align:center;"><span class="bs-badge asset">Asset</span></td>
                    <td class="amt" style="color:#FBBF24 !important;font-weight:800;font-size:14.5px;">₹{{ number_format($securityDeposits,2) }}</td>
                </tr>
                <tr style="background:rgba(16, 185, 129, 0.12);border-top:1.5px solid rgba(16, 185, 129, 0.30);">
                    <td style="font-weight:800;font-size:14px;padding:14px 16px;color:#FFFFFF !important;">
                        <i class="fa-solid fa-calculator" style="color:#34D399;margin-right:8px;"></i>Total Assets
                    </td>
                    <td></td>
                    <td class="amt" style="font-weight:800;font-size:16px;color:#34D399 !important;padding:14px 16px;">₹{{ number_format($totalAssets,2) }}</td>
                </tr>

                {{-- LIABILITIES SECTION --}}
                <tr class="section-divider">
                    <td colspan="3"><i class="fa-solid fa-arrow-trend-down" style="color:#F87171;margin-right:8px;"></i>B. Liabilities</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Outstanding Loan Balance</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">
                            Pending EMI principal &nbsp;·&nbsp;
                            Total: ₹{{ number_format($loanTotal,2) }} &nbsp;|&nbsp;
                            Paid: ₹{{ number_format($loanPaid,2) }}
                        </div>
                    </td>
                    <td style="text-align:center;"><span class="bs-badge liab">Liability</span></td>
                    <td class="amt" style="color:#F87171 !important;font-weight:800;font-size:14.5px;">₹{{ number_format($loanOutstanding,2) }}</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Unpaid / Pending Expenses</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Expenses with approval status: Pending</div>
                    </td>
                    <td style="text-align:center;"><span class="bs-badge liab">Liability</span></td>
                    <td class="amt" style="color:#F87171 !important;font-weight:800;font-size:14.5px;">₹{{ number_format($unpaidExpenses,2) }}</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">Credit Notes Payable</div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:500;margin-top:2px;">Pending/Approved credit notes owed to customers</div>
                    </td>
                    <td style="text-align:center;"><span class="bs-badge liab">Liability</span></td>
                    <td class="amt" style="color:#F87171 !important;font-weight:800;font-size:14.5px;">₹{{ number_format($creditNotePayable,2) }}</td>
                </tr>
                <tr style="background:rgba(239, 68, 68, 0.12);border-top:1.5px solid rgba(239, 68, 68, 0.30);">
                    <td style="font-weight:800;font-size:14px;padding:14px 16px;color:#FFFFFF !important;">
                        <i class="fa-solid fa-calculator" style="color:#F87171;margin-right:8px;"></i>Total Liabilities
                    </td>
                    <td></td>
                    <td class="amt" style="font-weight:800;font-size:16px;color:#F87171 !important;padding:14px 16px;">₹{{ number_format($totalLiabilities,2) }}</td>
                </tr>

                {{-- EQUITY SECTION --}}
                <tr class="section-divider">
                    <td colspan="3"><i class="fa-solid fa-scale-balanced" style="color:#38BDF8;margin-right:8px;"></i>C. Equity / Net Worth</td>
                </tr>
                <tr style="background:rgba(20, 27, 41, 0.85);border-top:2px solid rgba(255, 255, 255, 0.20);">
                    <td style="font-weight:800;font-size:15px;padding:16px;color:{{ $isPositive ? '#34D399' : '#F87171' }} !important;">
                        <i class="fa-solid {{ $isPositive ? 'fa-circle-check' : 'fa-circle-xmark' }}" style="margin-right:9px;"></i>
                        Net Worth — {{ $isPositive ? 'Positive Equity' : 'Negative Equity' }}
                    </td>
                    <td style="text-align:center;">
                        <span class="bs-badge equity {{ $isPositive ? 'profit' : 'loss' }}">Equity</span>
                    </td>
                    <td class="amt" style="font-weight:800;font-size:18px;color:{{ $isPositive ? '#34D399' : '#F87171' }} !important;padding:16px;">
                        {{ $isPositive ? '' : '−' }}₹{{ number_format(abs($netWorth),2) }}
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

    {{-- Footer bar --}}
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;
                margin-top:18px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.12);">
        <span style="font-size:13.5px;color:#FFFFFF !important;font-weight:700 !important;">
            Total Assets: <strong style="color:#34D399 !important;">₹{{ number_format($totalAssets,2) }}</strong>
            &nbsp;−&nbsp;
            Total Liabilities: <strong style="color:#F87171 !important;">₹{{ number_format($totalLiabilities,2) }}</strong>
            &nbsp;=&nbsp;
            <strong style="color:{{ $isPositive ? '#34D399' : '#F87171' }} !important;">
                Net Worth {{ $isPositive ? '' : '(Loss)' }} ₹{{ number_format(abs($netWorth),2) }}
            </strong>
        </span>
        <span style="font-size:13px;color:#FFFFFF !important;font-weight:700 !important;">
            <i class="fa-regular fa-clock" style="color:#38BDF8;"></i> Generated: {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>
</div>

@endsection
