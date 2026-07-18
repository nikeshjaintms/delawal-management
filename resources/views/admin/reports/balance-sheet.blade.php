@extends('admin.layouts.app')
@section('title','Balance Sheet')
@section('page-title','Reports')
@section('content')
<style>
/* ── Header ── */
.rpt-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:14px;}
.rpt-title-block h2{font-size:22px;font-weight:800;color:#0F172A;margin-bottom:4px;}
.rpt-title-block p{font-size:13.5px;color:#64748B;}
.rpt-action-btns{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
.btn-excel{padding:9px 16px;border:1px solid #16803D;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:7px;color:#16803D;background:rgba(34,197,94,0.05);text-decoration:none;transition:all .2s ease;}
.btn-excel:hover{background:rgba(34,197,94,0.12);transform:translateY(-1px);}
.btn-print{padding:9px 16px;border:1px solid #6366F1;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:7px;color:#6366F1;background:rgba(99,102,241,0.05);cursor:pointer;font-family:inherit;transition:all .2s ease;}
.btn-print:hover{background:rgba(99,102,241,0.12);transform:translateY(-1px);}
/* ── Summary Cards ── */
.bs-card-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:24px;}
@media(max-width:700px){.bs-card-grid{grid-template-columns:1fr;}}
.bs-sum-card{background:#fff;border:1px solid #E2E8F0;border-radius:16px;padding:22px 24px;
    box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);
    transition:transform .22s ease,box-shadow .22s ease;display:flex;align-items:center;gap:18px;}
.bs-sum-card:hover{transform:translateY(-3px);box-shadow:0 4px 8px rgba(0,0,0,0.07),0 16px 36px rgba(0,0,0,0.09);}
.bs-sum-icon{width:54px;height:54px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;}
.bs-sum-icon.asset  {background:rgba(16,185,129,0.1);color:#10B981;}
.bs-sum-icon.liab   {background:rgba(239,68,68,0.1); color:#EF4444;}
.bs-sum-icon.profit {background:rgba(16,185,129,0.12);color:#059669;}
.bs-sum-icon.loss   {background:rgba(239,68,68,0.12); color:#DC2626;}
.bs-sum-body .bs-sum-label{font-size:11.5px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.7px;margin-bottom:6px;}
.bs-sum-body .bs-sum-value{font-size:26px;font-weight:800;line-height:1.1;}
.bs-sum-body .bs-sum-sub{font-size:12px;color:#94A3B8;margin-top:4px;}
/* ── Filter ── */
.card-box{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:20px 22px;
    box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);margin-bottom:18px;}
.filter-bar{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:5px;}
.filter-label{font-size:11px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.6px;}
.filter-ctrl{padding:9px 14px;border:1.5px solid #E2E8F0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#fff;transition:border-color .18s;min-width:180px;}
.filter-ctrl:focus{border-color:#3B82F6;box-shadow:0 0 0 3px rgba(59,130,246,0.12);}
.btn-filter{background:#0F172A;color:#fff;padding:9px 18px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;align-self:flex-end;display:inline-flex;align-items:center;gap:6px;transition:background .18s;}
.btn-filter:hover{background:#1E293B;}
.btn-reset{padding:9px 10px;color:#64748B;text-decoration:none;font-size:13px;align-self:flex-end;display:inline-flex;align-items:center;gap:5px;transition:color .15s;}
.btn-reset:hover{color:#0F172A;}
.date-badge{display:inline-flex;align-items:center;gap:6px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:6px 12px;font-size:12.5px;color:#1E40AF;font-weight:600;margin-top:8px;}
/* ── Balance Sheet Table ── */
.bs-wrap{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;}
@media(max-width:900px){.bs-wrap{grid-template-columns:1fr;}}
.bs-panel{background:#fff;border:1px solid #E2E8F0;border-radius:14px;overflow:hidden;
    box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);}
.bs-panel-hdr{padding:14px 20px;font-size:13px;font-weight:800;display:flex;align-items:center;gap:9px;letter-spacing:.3px;}
.bs-panel-hdr.asset{background:linear-gradient(135deg,rgba(16,185,129,0.08),rgba(16,185,129,0.03));color:#065F46;border-bottom:1px solid rgba(16,185,129,0.15);}
.bs-panel-hdr.liab {background:linear-gradient(135deg,rgba(239,68,68,0.08),rgba(239,68,68,0.03));color:#991B1B;border-bottom:1px solid rgba(239,68,68,0.15);}
.bs-item{display:flex;justify-content:space-between;align-items:center;padding:12px 20px;border-bottom:1px solid #F8FAFC;transition:background .14s ease;}
.bs-item:last-of-type{border-bottom:none;}
.bs-item:hover{background:#F8FAFF;}
.bs-item .bs-item-name{font-size:13.5px;font-weight:500;color:#374151;}
.bs-item .bs-item-note{font-size:11px;color:#94A3B8;margin-top:2px;}
.bs-item .bs-item-amt{font-size:14px;font-weight:700;}
.bs-subtotal{display:flex;justify-content:space-between;align-items:center;padding:13px 20px;font-weight:800;font-size:14px;}
.bs-subtotal.asset{background:rgba(16,185,129,0.06);border-top:1.5px solid rgba(16,185,129,0.2);color:#065F46;}
.bs-subtotal.liab {background:rgba(239,68,68,0.06); border-top:1.5px solid rgba(239,68,68,0.2); color:#991B1B;}
/* ── Net Worth Row ── */
.bs-net-row{padding:18px 22px;border-radius:12px;font-weight:800;font-size:16px;
    display:flex;justify-content:space-between;align-items:center;border:2px solid;margin-bottom:16px;}
.bs-net-row.profit{background:rgba(16,185,129,0.05);border-color:rgba(16,185,129,0.3);color:#059669;}
.bs-net-row.loss  {background:rgba(239,68,68,0.05); border-color:rgba(239,68,68,0.3); color:#DC2626;}
/* ── Stat badges ── */
.bs-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;}
.bs-badge.asset{background:rgba(16,185,129,0.1);color:#065F46;}
.bs-badge.liab {background:rgba(239,68,68,0.1); color:#991B1B;}
.bs-badge.equity.profit{background:rgba(16,185,129,0.1);color:#065F46;}
.bs-badge.equity.loss  {background:rgba(239,68,68,0.1); color:#991B1B;}
/* ── Detail table below ── */
.bs-full-table{width:100%;border-collapse:collapse;font-size:13.5px;}
.bs-full-table thead th{padding:10px 16px;background:#F8FAFC;color:#475569;font-weight:700;border-bottom:2px solid #E2E8F0;font-size:11px;text-transform:uppercase;letter-spacing:.7px;}
.bs-full-table tbody td{padding:12px 16px;border-bottom:1px solid #F1F5F9;vertical-align:middle;}
.bs-full-table tbody tr:hover{background:#F8FAFF;}
.bs-full-table tfoot td{padding:12px 16px;font-weight:800;border-top:2px solid #E2E8F0;background:#F8FAFC;}
.amt{text-align:right;font-variant-numeric:tabular-nums;}
.section-divider td{background:#F1F5F9;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#475569;padding:8px 16px!important;}
/* Print */
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
        <h2><i class="fa-solid fa-sheet-plastic" style="color:#3B82F6;margin-right:9px;"></i>Balance Sheet</h2>
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
            <div class="bs-sum-value" style="color:#059669;">₹{{ number_format($totalAssets,2) }}</div>
            <div class="bs-sum-sub">Cash + Receivables + Property</div>
        </div>
    </div>
    <div class="bs-sum-card">
        <div class="bs-sum-icon liab"><i class="fa-solid fa-arrow-trend-down"></i></div>
        <div class="bs-sum-body">
            <div class="bs-sum-label">Total Liabilities</div>
            <div class="bs-sum-value" style="color:#DC2626;">₹{{ number_format($totalLiabilities,2) }}</div>
            <div class="bs-sum-sub">Loans + Payables + Credit Notes</div>
        </div>
    </div>
    <div class="bs-sum-card" style="border:2px solid {{ $isPositive ? 'rgba(16,185,129,0.3)' : 'rgba(239,68,68,0.3)' }};">
        <div class="bs-sum-icon {{ $isPositive ? 'profit' : 'loss' }}">
            <i class="fa-solid fa-scale-balanced"></i>
        </div>
        <div class="bs-sum-body">
            <div class="bs-sum-label">Net Worth / Equity</div>
            <div class="bs-sum-value" style="color:{{ $isPositive ? '#059669' : '#DC2626' }};">
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
            <div class="bs-item-amt" style="color:#059669;">₹{{ number_format($cashReceived,2) }}</div>
        </div>

        <div class="bs-item">
            <div>
                <div class="bs-item-name">Cash / Bank — Rental Collections</div>
                <div class="bs-item-note">Rental payment amounts actually collected</div>
            </div>
            <div class="bs-item-amt" style="color:#059669;">₹{{ number_format($rentalCashReceived,2) }}</div>
        </div>

        <div class="bs-item">
            <div>
                <div class="bs-item-name">Receivables — Pending Customer Dues</div>
                <div class="bs-item-note">Remaining amounts on pending/partial sales</div>
            </div>
            <div class="bs-item-amt" style="color:#0EA5E9;">₹{{ number_format($receivables,2) }}</div>
        </div>

        <div class="bs-item">
            <div>
                <div class="bs-item-name">Property Value — Unsold / Booked</div>
                <div class="bs-item-note">Listed price of available & booked properties</div>
            </div>
            <div class="bs-item-amt" style="color:#8B5CF6;">₹{{ number_format($propertyValue,2) }}</div>
        </div>

        <div class="bs-item">
            <div>
                <div class="bs-item-name">Security Deposits Held</div>
                <div class="bs-item-note">Deposits from active rental agreements</div>
            </div>
            <div class="bs-item-amt" style="color:#F59E0B;">₹{{ number_format($securityDeposits,2) }}</div>
        </div>

        <div class="bs-subtotal asset">
            <span><i class="fa-solid fa-sigma" style="margin-right:7px;"></i>Total Assets</span>
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
            <div class="bs-item-amt" style="color:#DC2626;">₹{{ number_format($loanOutstanding,2) }}</div>
        </div>

        <div class="bs-item">
            <div>
                <div class="bs-item-name">Unpaid / Pending Expenses</div>
                <div class="bs-item-note">Expenses with approval status: Pending</div>
            </div>
            <div class="bs-item-amt" style="color:#DC2626;">₹{{ number_format($unpaidExpenses,2) }}</div>
        </div>

        <div class="bs-item">
            <div>
                <div class="bs-item-name">Credit Notes Payable</div>
                <div class="bs-item-note">Pending/Approved credit notes owed to customers</div>
            </div>
            <div class="bs-item-amt" style="color:#DC2626;">₹{{ number_format($creditNotePayable,2) }}</div>
        </div>

        {{-- Spacer rows to match height visually --}}
        <div class="bs-item" style="opacity:0.35;">
            <div><div class="bs-item-name">Other Liabilities</div><div class="bs-item-note">—</div></div>
            <div class="bs-item-amt">₹0.00</div>
        </div>

        <div class="bs-item" style="opacity:0.35;">
            <div><div class="bs-item-name">Other Payables</div><div class="bs-item-note">—</div></div>
            <div class="bs-item-amt">₹0.00</div>
        </div>

        <div class="bs-subtotal liab">
            <span><i class="fa-solid fa-sigma" style="margin-right:7px;"></i>Total Liabilities</span>
            <span>₹{{ number_format($totalLiabilities,2) }}</span>
        </div>
    </div>

</div>

{{-- ── Net Worth / Equity Row ── --}}
<div class="bs-net-row {{ $isPositive ? 'profit' : 'loss' }}">
    <span>
        <i class="fa-solid {{ $isPositive ? 'fa-circle-check' : 'fa-circle-xmark' }}" style="margin-right:10px;font-size:18px;"></i>
        C. Net Worth / Equity &nbsp;
        <span style="font-size:12px;font-weight:600;opacity:.7;">(Total Assets − Total Liabilities)</span>
    </span>
    <span style="font-size:22px;">
        {{ $isPositive ? '' : '−' }}₹{{ number_format(abs($netWorth),2) }}
    </span>
</div>

{{-- ── Full Detail Table ── --}}
<div class="card-box">
    <div style="font-size:14px;font-weight:800;color:#0F172A;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <span><i class="fa-solid fa-table-list" style="color:#3B82F6;margin-right:8px;"></i>Detailed Balance Sheet</span>
        <span class="bs-badge equity {{ $isPositive ? 'profit' : 'loss' }}" style="font-size:13px;padding:6px 14px;">
            Net Worth: {{ $isPositive ? '' : '−' }}₹{{ number_format(abs($netWorth),2) }}
        </span>
    </div>

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
                <td colspan="3"><i class="fa-solid fa-arrow-trend-up" style="color:#10B981;margin-right:8px;"></i>A. Assets</td>
            </tr>
            <tr>
                <td>
                    <div style="font-weight:600;">Cash / Bank — Sales Receipts</div>
                    <div style="font-size:12px;color:#64748B;">Payment amounts received from property sales</div>
                </td>
                <td style="text-align:center;"><span class="bs-badge asset">Asset</span></td>
                <td class="amt" style="color:#059669;font-weight:700;">₹{{ number_format($cashReceived,2) }}</td>
            </tr>
            <tr>
                <td>
                    <div style="font-weight:600;">Cash / Bank — Rental Collections</div>
                    <div style="font-size:12px;color:#64748B;">Rental payment amounts actually collected</div>
                </td>
                <td style="text-align:center;"><span class="bs-badge asset">Asset</span></td>
                <td class="amt" style="color:#059669;font-weight:700;">₹{{ number_format($rentalCashReceived,2) }}</td>
            </tr>
            <tr>
                <td>
                    <div style="font-weight:600;">Receivables — Pending Customer Dues</div>
                    <div style="font-size:12px;color:#64748B;">Remaining amounts on pending/partial property sales</div>
                </td>
                <td style="text-align:center;"><span class="bs-badge asset">Asset</span></td>
                <td class="amt" style="color:#0EA5E9;font-weight:700;">₹{{ number_format($receivables,2) }}</td>
            </tr>
            <tr>
                <td>
                    <div style="font-weight:600;">Property Value — Unsold / Booked</div>
                    <div style="font-size:12px;color:#64748B;">Listed price of available &amp; booked properties</div>
                </td>
                <td style="text-align:center;"><span class="bs-badge asset">Asset</span></td>
                <td class="amt" style="color:#8B5CF6;font-weight:700;">₹{{ number_format($propertyValue,2) }}</td>
            </tr>
            <tr>
                <td>
                    <div style="font-weight:600;">Security Deposits Held</div>
                    <div style="font-size:12px;color:#64748B;">Deposits from active rental agreements</div>
                </td>
                <td style="text-align:center;"><span class="bs-badge asset">Asset</span></td>
                <td class="amt" style="color:#F59E0B;font-weight:700;">₹{{ number_format($securityDeposits,2) }}</td>
            </tr>
            <tr style="background:#F0FDF4;">
                <td style="font-weight:800;font-size:14px;padding:13px 16px;">
                    <i class="fa-solid fa-sigma" style="color:#059669;margin-right:8px;"></i>Total Assets
                </td>
                <td></td>
                <td class="amt" style="font-weight:800;font-size:16px;color:#059669;padding:13px 16px;">₹{{ number_format($totalAssets,2) }}</td>
            </tr>

            {{-- LIABILITIES SECTION --}}
            <tr class="section-divider">
                <td colspan="3"><i class="fa-solid fa-arrow-trend-down" style="color:#EF4444;margin-right:8px;"></i>B. Liabilities</td>
            </tr>
            <tr>
                <td>
                    <div style="font-weight:600;">Outstanding Loan Balance</div>
                    <div style="font-size:12px;color:#64748B;">
                        Pending EMI principal &nbsp;·&nbsp;
                        Total: ₹{{ number_format($loanTotal,2) }} &nbsp;|&nbsp;
                        Paid: ₹{{ number_format($loanPaid,2) }}
                    </div>
                </td>
                <td style="text-align:center;"><span class="bs-badge liab">Liability</span></td>
                <td class="amt" style="color:#DC2626;font-weight:700;">₹{{ number_format($loanOutstanding,2) }}</td>
            </tr>
            <tr>
                <td>
                    <div style="font-weight:600;">Unpaid / Pending Expenses</div>
                    <div style="font-size:12px;color:#64748B;">Expenses with approval status: Pending</div>
                </td>
                <td style="text-align:center;"><span class="bs-badge liab">Liability</span></td>
                <td class="amt" style="color:#DC2626;font-weight:700;">₹{{ number_format($unpaidExpenses,2) }}</td>
            </tr>
            <tr>
                <td>
                    <div style="font-weight:600;">Credit Notes Payable</div>
                    <div style="font-size:12px;color:#64748B;">Pending/Approved credit notes owed to customers</div>
                </td>
                <td style="text-align:center;"><span class="bs-badge liab">Liability</span></td>
                <td class="amt" style="color:#DC2626;font-weight:700;">₹{{ number_format($creditNotePayable,2) }}</td>
            </tr>
            <tr style="background:#FFF5F5;">
                <td style="font-weight:800;font-size:14px;padding:13px 16px;">
                    <i class="fa-solid fa-sigma" style="color:#DC2626;margin-right:8px;"></i>Total Liabilities
                </td>
                <td></td>
                <td class="amt" style="font-weight:800;font-size:16px;color:#DC2626;padding:13px 16px;">₹{{ number_format($totalLiabilities,2) }}</td>
            </tr>

            {{-- EQUITY SECTION --}}
            <tr class="section-divider">
                <td colspan="3"><i class="fa-solid fa-scale-balanced" style="color:#3B82F6;margin-right:8px;"></i>C. Equity / Net Worth</td>
            </tr>
            <tr style="background:{{ $isPositive ? 'rgba(16,185,129,0.04)' : 'rgba(239,68,68,0.04)' }};">
                <td style="font-weight:800;font-size:15px;padding:15px 16px;color:{{ $isPositive ? '#059669' : '#DC2626' }};">
                    <i class="fa-solid {{ $isPositive ? 'fa-circle-check' : 'fa-circle-xmark' }}" style="margin-right:9px;"></i>
                    Net Worth — {{ $isPositive ? 'Positive Equity' : 'Negative Equity' }}
                </td>
                <td style="text-align:center;">
                    <span class="bs-badge equity {{ $isPositive ? 'profit' : 'loss' }}">Equity</span>
                </td>
                <td class="amt" style="font-weight:800;font-size:18px;color:{{ $isPositive ? '#059669' : '#DC2626' }};padding:15px 16px;">
                    {{ $isPositive ? '' : '−' }}₹{{ number_format(abs($netWorth),2) }}
                </td>
            </tr>

        </tbody>
    </table>

    {{-- Footer bar --}}
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;
                margin-top:16px;padding-top:14px;border-top:1px solid #F1F5F9;">
        <span style="font-size:12px;color:#64748B;">
            Total Assets: <strong style="color:#059669;">₹{{ number_format($totalAssets,2) }}</strong>
            &nbsp;−&nbsp;
            Total Liabilities: <strong style="color:#DC2626;">₹{{ number_format($totalLiabilities,2) }}</strong>
            &nbsp;=&nbsp;
            <strong style="color:{{ $isPositive ? '#059669' : '#DC2626' }};">
                Net Worth {{ $isPositive ? '' : '(Loss)' }} ₹{{ number_format(abs($netWorth),2) }}
            </strong>
        </span>
        <span style="font-size:12px;color:#64748B;">
            <i class="fa-regular fa-clock"></i> Generated: {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>
</div>

@endsection
