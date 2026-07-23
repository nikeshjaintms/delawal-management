@extends('admin.layouts.app')
@section('title','Cash Flow Report')
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
.cf-card-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:24px;}
@media(max-width:700px){.cf-card-grid{grid-template-columns:1fr;}}
.cf-sum-card{background:#fff;border:1px solid #E2E8F0;border-radius:16px;padding:22px 24px;
    box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);
    transition:transform .22s ease,box-shadow .22s ease;display:flex;align-items:center;gap:18px;}
.cf-sum-card:hover{transform:translateY(-3px);box-shadow:0 4px 8px rgba(0,0,0,0.07),0 16px 36px rgba(0,0,0,0.09);}
.cf-sum-icon{width:54px;height:54px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;}
.cf-sum-icon.inflow {background:rgba(16,185,129,0.1);color:#10B981;}
.cf-sum-icon.outflow{background:rgba(239,68,68,0.1); color:#EF4444;}
.cf-sum-icon.pos    {background:rgba(16,185,129,0.12);color:#059669;}
.cf-sum-icon.neg    {background:rgba(239,68,68,0.12); color:#DC2626;}
.cf-sum-body .cf-sum-label{font-size:11.5px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.7px;margin-bottom:6px;}
.cf-sum-body .cf-sum-value{font-size:26px;font-weight:800;line-height:1.1;}
.cf-sum-body .cf-sum-sub{font-size:12px;color:#94A3B8;margin-top:4px;}
/* ── Section breakdown cards ── */
.cf-section-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:22px;}
@media(max-width:700px){.cf-section-grid{grid-template-columns:1fr;}}
.cf-section-card{background:#fff;border:1px solid #E2E8F0;border-radius:14px;overflow:hidden;
    box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);}
.cf-section-hdr{padding:13px 18px;font-size:13px;font-weight:800;display:flex;align-items:center;gap:9px;}
.cf-section-hdr.in {background:linear-gradient(135deg,rgba(16,185,129,0.08),rgba(16,185,129,0.03));color:#065F46;border-bottom:1px solid rgba(16,185,129,0.15);}
.cf-section-hdr.out{background:linear-gradient(135deg,rgba(239,68,68,0.08),rgba(239,68,68,0.03));color:#991B1B;border-bottom:1px solid rgba(239,68,68,0.15);}
.cf-section-row{display:flex;justify-content:space-between;align-items:center;padding:12px 18px;border-bottom:1px solid #F8FAFC;}
.cf-section-row:last-of-type{border-bottom:none;}
.cf-section-row:hover{background:#F8FAFF;}
.cf-section-row .sr-name{font-size:13.5px;font-weight:500;color:#374151;}
.cf-section-row .sr-note{font-size:11px;color:#94A3B8;margin-top:2px;}
.cf-section-row .sr-amt{font-size:14px;font-weight:700;}
.cf-section-total{display:flex;justify-content:space-between;padding:12px 18px;font-weight:800;font-size:14px;}
.cf-section-total.in {background:rgba(16,185,129,0.06);border-top:1.5px solid rgba(16,185,129,0.2);color:#065F46;}
.cf-section-total.out{background:rgba(239,68,68,0.06); border-top:1.5px solid rgba(239,68,68,0.2); color:#991B1B;}
/* ── Net row ── */
.cf-net-row{padding:18px 22px;border-radius:12px;font-weight:800;font-size:16px;
    display:flex;justify-content:space-between;align-items:center;border:2px solid;margin-bottom:20px;}
.cf-net-row.pos{background:rgba(16,185,129,0.05);border-color:rgba(16,185,129,0.3);color:#059669;}
.cf-net-row.neg{background:rgba(239,68,68,0.05); border-color:rgba(239,68,68,0.3); color:#DC2626;}
/* ── Filter ── */
.card-box{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:20px 22px;
    box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);margin-bottom:18px;}
.filter-bar{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:5px;}
.filter-label{font-size:11px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.6px;}
.filter-ctrl{padding:9px 12px;border:1.5px solid #E2E8F0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#fff;transition:border-color .18s;min-width:160px;}
.filter-ctrl:focus{border-color:#3B82F6;box-shadow:0 0 0 3px rgba(59,130,246,0.12);}
.btn-filter{background:#0F172A;color:#fff;padding:9px 18px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;align-self:flex-end;display:inline-flex;align-items:center;gap:6px;transition:background .18s;}
.btn-filter:hover{background:#1E293B;}
.btn-reset{padding:9px 10px;color:#64748B;text-decoration:none;font-size:13px;align-self:flex-end;display:inline-flex;align-items:center;gap:5px;}
.btn-reset:hover{color:#0F172A;}
.date-badge{display:inline-flex;align-items:center;gap:6px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:6px 12px;font-size:12.5px;color:#1E40AF;font-weight:600;margin-top:8px;}
/* ── Transaction Table ── */
.table-wrap{width:100%;overflow-x:auto;}
.cf-table{width:100%;border-collapse:collapse;font-size:13px;}
.cf-table thead th{padding:11px 14px;background:#F8FAFC;color:#475569;font-weight:700;border-bottom:2px solid #E2E8F0;font-size:11px;text-transform:uppercase;letter-spacing:.7px;white-space:nowrap;}
.cf-table tbody td{padding:12px 14px;border-bottom:1px solid #F1F5F9;vertical-align:middle;}
.cf-table tbody tr.inflow-row:hover{background:#F0FDF4;}
.cf-table tbody tr.outflow-row:hover{background:#FFF5F5;}
.cf-table tfoot td{padding:12px 14px;background:#F8FAFC;font-weight:800;border-top:2px solid #E2E8F0;}
.amt{text-align:right;font-variant-numeric:tabular-nums;}
.inflow-badge {display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:rgba(16,185,129,0.1);color:#065F46;}
.outflow-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:rgba(239,68,68,0.1);color:#991B1B;}
.mode-chip{background:#F1F5F9;padding:2px 8px;border-radius:6px;font-size:11.5px;font-weight:600;color:#475569;}
.empty-state{text-align:center;padding:52px 20px;color:#94A3B8;}
.empty-state i{font-size:40px;margin-bottom:14px;display:block;opacity:.3;}
/* ── Monthly summary ── */
.cf-monthly-table{width:100%;border-collapse:collapse;font-size:13.5px;}
.cf-monthly-table thead th{padding:10px 14px;background:#F8FAFC;color:#475569;font-weight:700;border-bottom:2px solid #E2E8F0;font-size:11px;text-transform:uppercase;letter-spacing:.7px;}
.cf-monthly-table tbody td{padding:11px 14px;border-bottom:1px solid #F1F5F9;vertical-align:middle;}
.cf-monthly-table tbody tr:hover{background:#F8FAFF;}
.cf-monthly-table tfoot td{padding:11px 14px;background:#F1F5F9;font-weight:800;border-top:2px solid #E2E8F0;}
/* Print */
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
        <h2><i class="fa-solid fa-water" style="color:#0EA5E9;margin-right:9px;"></i>Cash Flow Report</h2>
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
        <div class="cf-sum-icon inflow"><i class="fa-solid fa-arrow-down-to-bracket"></i></div>
        <div class="cf-sum-body">
            <div class="cf-sum-label">Total Cash Inflow</div>
            <div class="cf-sum-value" style="color:#059669;">₹{{ number_format($totalInflow,2) }}</div>
            <div class="cf-sum-sub">Sales + Rental receipts</div>
        </div>
    </div>
    <div class="cf-sum-card">
        <div class="cf-sum-icon outflow"><i class="fa-solid fa-arrow-up-from-bracket"></i></div>
        <div class="cf-sum-body">
            <div class="cf-sum-label">Total Cash Outflow</div>
            <div class="cf-sum-value" style="color:#DC2626;">₹{{ number_format($totalOutflow,2) }}</div>
            <div class="cf-sum-sub">Expenses + Loan repayments</div>
        </div>
    </div>
    <div class="cf-sum-card" style="border:2px solid {{ $isPositive ? 'rgba(16,185,129,0.3)' : 'rgba(239,68,68,0.3)' }};">
        <div class="cf-sum-icon {{ $isPositive ? 'pos' : 'neg' }}">
            <i class="fa-solid fa-scale-balanced"></i>
        </div>
        <div class="cf-sum-body">
            <div class="cf-sum-label">Net Cash Flow</div>
            <div class="cf-sum-value" style="color:{{ $isPositive ? '#059669' : '#DC2626' }};">
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
            <i class="fa-solid fa-arrow-down-to-bracket"></i> A. Cash Inflow
        </div>
        <div class="cf-section-row">
            <div>
                <div class="sr-name">Sales Payment Received</div>
                <div class="sr-note">Property payment receipts from customers</div>
            </div>
            <div class="sr-amt" style="color:#059669;">₹{{ number_format($totalSalesInflow,2) }}</div>
        </div>
        <div class="cf-section-row">
            <div>
                <div class="sr-name">Rental Payment Received</div>
                <div class="sr-note">Rent collections from tenants</div>
            </div>
            <div class="sr-amt" style="color:#059669;">₹{{ number_format($totalRentalInflow,2) }}</div>
        </div>
        <div class="cf-section-total in">
            <span><i class="fa-solid fa-sigma" style="margin-right:7px;"></i>Total Inflow</span>
            <span>₹{{ number_format($totalInflow,2) }}</span>
        </div>
    </div>

    {{-- OUTFLOW --}}
    <div class="cf-section-card">
        <div class="cf-section-hdr out">
            <i class="fa-solid fa-arrow-up-from-bracket"></i> B. Cash Outflow
        </div>
        <div class="cf-section-row">
            <div>
                <div class="sr-name">Expenses Paid</div>
                <div class="sr-note">All recorded business expenses</div>
            </div>
            <div class="sr-amt" style="color:#DC2626;">₹{{ number_format($totalExpenseOutflow,2) }}</div>
        </div>
        <div class="cf-section-row">
            <div>
                <div class="sr-name">Loan Repayment (EMI Paid)</div>
                <div class="sr-note">EMI payments made on active loans</div>
            </div>
            <div class="sr-amt" style="color:#DC2626;">₹{{ number_format($totalLoanOutflow,2) }}</div>
        </div>
        <div class="cf-section-total out">
            <span><i class="fa-solid fa-sigma" style="margin-right:7px;"></i>Total Outflow</span>
            <span>₹{{ number_format($totalOutflow,2) }}</span>
        </div>
    </div>
</div>

{{-- ── Net Cash Flow Row ── --}}
<div class="cf-net-row {{ $isPositive ? 'pos' : 'neg' }}">
    <span>
        <i class="fa-solid {{ $isPositive ? 'fa-circle-check' : 'fa-circle-xmark' }}" style="margin-right:10px;font-size:18px;"></i>
        C. Net Cash Flow &nbsp;
        <span style="font-size:12px;font-weight:600;opacity:.7;">(Total Inflow − Total Outflow)</span>
    </span>
    <span style="font-size:22px;">
        {{ $isPositive ? '' : '−' }}₹{{ number_format(abs($netCashFlow),2) }}
    </span>
</div>

{{-- ── Monthly Summary Table ── --}}
@if($monthlyRows->count() > 0)
<div class="card-box">
    <div style="font-size:14px;font-weight:800;color:#0F172A;margin-bottom:16px;">
        <i class="fa-solid fa-calendar-days" style="color:#0EA5E9;margin-right:8px;"></i>
        Monthly Cash Flow Summary
        <span style="font-size:12px;font-weight:500;color:#64748B;margin-left:8px;">{{ $monthlyRows->count() }} months</span>
    </div>
    <div style="overflow-x:auto;">
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
                    <td style="font-weight:600;">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $row['month'])->format('F Y') }}
                    </td>
                    <td class="amt" style="color:#059669;font-weight:700;">₹{{ number_format($row['inflow'],2) }}</td>
                    <td class="amt" style="color:#DC2626;font-weight:700;">₹{{ number_format($row['outflow'],2) }}</td>
                    <td class="amt" style="font-weight:800;color:{{ $rowPos ? '#059669' : '#DC2626' }};">
                        {{ $rowPos ? '' : '−' }}₹{{ number_format(abs($row['net']),2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td class="amt" style="color:#059669;">₹{{ number_format($totalInflow,2) }}</td>
                    <td class="amt" style="color:#DC2626;">₹{{ number_format($totalOutflow,2) }}</td>
                    <td class="amt" style="font-size:15px;color:{{ $isPositive ? '#059669' : '#DC2626' }};">
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
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <div style="font-size:14px;font-weight:800;color:#0F172A;">
            <i class="fa-solid fa-table-list" style="color:#0EA5E9;margin-right:8px;"></i>
            All Cash Transactions
            <span style="font-size:12px;font-weight:500;color:#64748B;margin-left:8px;">{{ $allTransactions->count() }} records</span>
        </div>
        <div style="display:flex;gap:10px;font-size:12px;">
            <span style="display:flex;align-items:center;gap:5px;">
                <span class="inflow-badge">Inflow</span>
                <span style="color:#64748B;">{{ $allTransactions->where('type','inflow')->count() }} txns</span>
            </span>
            <span style="display:flex;align-items:center;gap:5px;">
                <span class="outflow-badge">Outflow</span>
                <span style="color:#64748B;">{{ $allTransactions->where('type','outflow')->count() }} txns</span>
            </span>
        </div>
    </div>

    @if($allTransactions->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-water"></i>
            <p>No cash transactions found for the selected period.</p>
            @if(request('from_date') || request('to_date'))
                <a href="{{ route('reports.cash-flow') }}" style="color:#3B82F6;font-size:13px;margin-top:8px;display:inline-block;">
                    Clear filters to see all records
                </a>
            @endif
        </div>
    @else
    <div class="table-wrap">
        <table class="cf-table">
            <thead>
                <tr>
                    <th style="width:30px;">#</th>
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
                <tr class="{{ $txn['type'] === 'inflow' ? 'inflow-row' : 'outflow-row' }}">
                    <td style="color:#94A3B8;font-size:12px;">{{ $i + 1 }}</td>
                    <td style="white-space:nowrap;font-weight:600;font-size:13px;">
                        {{ \Carbon\Carbon::parse($txn['date'])->format('d M Y') }}
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13px;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $txn['particular'] }}
                        </div>
                    </td>
                    <td style="font-size:12px;color:#64748B;">{{ $txn['section'] }}</td>
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
                            <span style="color:#CBD5E1;font-size:12px;">—</span>
                        @endif
                    </td>
                    <td class="amt" style="font-weight:700;font-size:14px;color:{{ $txn['type']==='inflow' ? '#059669' : '#DC2626' }};">
                        {{ $txn['type'] === 'outflow' ? '−' : '' }}₹{{ number_format($txn['amount'],2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="font-size:13px;color:#0F172A;">
                        <i class="fa-solid fa-sigma" style="color:#0EA5E9;margin-right:6px;"></i>
                        Total ({{ $allTransactions->count() }} transactions)
                    </td>
                    <td></td>
                    <td class="amt">
                        <div style="font-size:13px;color:#059669;font-weight:700;">+₹{{ number_format($totalInflow,2) }}</div>
                        <div style="font-size:13px;color:#DC2626;font-weight:700;">−₹{{ number_format($totalOutflow,2) }}</div>
                        <div style="font-size:15px;font-weight:800;color:{{ $isPositive ? '#059669' : '#DC2626' }};border-top:1px solid #E2E8F0;margin-top:4px;padding-top:4px;">
                            {{ $isPositive ? '' : '−' }}₹{{ number_format(abs($netCashFlow),2) }}
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Footer --}}
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;
                margin-top:16px;padding-top:14px;border-top:1px solid #F1F5F9;">
        <span style="font-size:12px;color:#64748B;">
            Inflow: <strong style="color:#059669;">₹{{ number_format($totalInflow,2) }}</strong>
            &nbsp;−&nbsp;
            Outflow: <strong style="color:#DC2626;">₹{{ number_format($totalOutflow,2) }}</strong>
            &nbsp;=&nbsp;
            <strong style="color:{{ $isPositive ? '#059669' : '#DC2626' }};">
                Net {{ $isPositive ? 'Surplus' : 'Deficit' }} ₹{{ number_format(abs($netCashFlow),2) }}
            </strong>
        </span>
        <span style="font-size:12px;color:#64748B;">
            <i class="fa-regular fa-clock"></i> Generated: {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>
    @endif
</div>

@endsection
