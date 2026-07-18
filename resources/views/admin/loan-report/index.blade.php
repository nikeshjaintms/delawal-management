@extends('admin.layouts.app')
@section('title','Loan Report')
@section('page-title','Loan Management')
@section('content')
<style>
/* ── Layout ── */
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

/* Stat cards */
.gst-stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-bottom:22px;}
.gst-stat-card{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:16px 18px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);transition:transform .2s ease,box-shadow .2s ease;}
.gst-stat-card:hover{transform:translateY(-3px);box-shadow:0 4px 8px rgba(0,0,0,0.07),0 16px 36px rgba(0,0,0,0.09);}
.gst-stat-card .sc-label{font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px;}
.gst-stat-card .sc-value{font-size:18px;font-weight:800;color:#0F172A;}
.gst-stat-card .sc-icon{width:38px;height:38px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:15px;margin-bottom:10px;}
.sc-blue  {background:rgba(59,130,246,0.1);  color:#3B82F6;}
.sc-green {background:rgba(16,185,129,0.1);  color:#10B981;}
.sc-amber {background:rgba(245,158,11,0.1);  color:#F59E0B;}
.sc-red   {background:rgba(239,68,68,0.1);   color:#EF4444;}
.sc-purple{background:rgba(139,92,246,0.1);  color:#8B5CF6;}

/* Filter card */
.card-box{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:20px 22px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);margin-bottom:18px;}
.filter-bar{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:5px;}
.filter-label{font-size:11px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.6px;}
.filter-ctrl{padding:9px 12px;border:1.5px solid #E2E8F0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#fff;transition:border-color .18s ease;width:100%;}
.filter-ctrl:focus{border-color:#3B82F6;box-shadow:0 0 0 3px rgba(59,130,246,0.15);}
.filter-actions{display:flex;gap:10px;justify-content:flex-end;align-items:flex-end;}
.btn-filter{background:#0F172A;color:#fff;padding:10px 20px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:6px;transition:background .18s ease;}
.btn-filter:hover{background:#1E293B;}
.btn-reset-link{padding:10px 14px;border:1px solid #E2E8F0;background:#fff;color:#64748B;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:5px;transition:all .18s ease;}
.btn-reset-link:hover{color:#0F172A;border-color:#94A3B8;background:#F8FAFC;}

/* Table */
.table-wrap{width:100%;overflow-x:auto;}
.r-table{width:100%;border-collapse:collapse;font-size:13px;}
.r-table thead th{padding:11px 13px;background:#F8FAFC;color:#475569;font-weight:700;border-bottom:2px solid #E2E8F0;font-size:11px;text-transform:uppercase;letter-spacing:.7px;white-space:nowrap;}
.r-table tbody td{padding:12px 13px;border-bottom:1px solid #F1F5F9;color:#0F172A;vertical-align:middle;}
.r-table tbody tr:last-child td{border-bottom:none;}
.r-table tbody tr{transition:background .14s ease;}
.r-table tbody tr:hover{background:#F0F7FF;}
.r-table tfoot td{padding:11px 13px;background:#F8FAFC;font-weight:800;border-top:2px solid #E2E8F0;}
.amt{text-align:right;font-variant-numeric:tabular-nums;}

/* Status badges */
.status-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;text-align:center;}
.sb-approved     {background:rgba(16,185,129,0.1);  color:#065F46;}
.sb-pending      {background:rgba(245,158,11,0.1);  color:#92400E;}
.sb-rejected     {background:rgba(239,68,68,0.1);   color:#991B1B;}
.sb-under-process{background:rgba(99,102,241,0.1);  color:#3730A3;}
.sb-active       {background:rgba(16,185,129,0.1);  color:#065F46;}
.sb-completed    {background:rgba(59,130,246,0.1);  color:#1E40AF;}
.sb-closed       {background:rgba(100,116,139,0.1); color:#374151;}
.sb-cancelled    {background:rgba(239,68,68,0.1);   color:#991B1B;}

/* Action button */
.btn-action{width:28px;height:28px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;color:#64748B;border:1px solid #E2E8F0;background:#fff;transition:all .18s;text-decoration:none;}
.btn-action:hover{color:#3B82F6;border-color:#3B82F6;background:#F0F7FF;}
.btn-action.view:hover{color:#3B82F6;border-color:#3B82F6;background:#EFF6FF;}

/* Empty state */
.empty-state{text-align:center;padding:52px 20px;color:#94A3B8;}
.empty-state i{font-size:40px;margin-bottom:14px;display:block;opacity:.3;}
.empty-state p{font-size:14px;}

/* Summary charts */
.summary-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;margin-top:24px;}
.summary-card{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);}
.summary-card-title{font-size:12px;font-weight:700;color:#3B82F6;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid #E2E8F0;display:flex;align-items:center;gap:8px;}
.summary-row{display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid #F1F5F9;}
.summary-row:last-child{border-bottom:none;}
.summary-row-label{font-size:13px;color:#0F172A;font-weight:500;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;padding-right:10px;}
.summary-row-bar-wrap{flex:0 0 90px;height:5px;background:#F1F5F9;border-radius:3px;overflow:hidden;margin:0 10px;}
.summary-row-bar{height:100%;border-radius:3px;}
.summary-row-amount{font-size:13px;font-weight:700;white-space:nowrap;}

/* ── Print header (screen: hidden, print: visible) ── */
.print-header{display:none;border-bottom:2.5px solid #3B82F6;padding-bottom:12px;margin-bottom:20px;flex-direction:row;justify-content:space-between;align-items:flex-start;}
.print-header .ph-left .ph-company{font-size:20px;font-weight:800;color:#0F172A;}
.print-header .ph-left .ph-sub{font-size:10px;color:#3B82F6;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-top:2px;}
.print-header .ph-right{text-align:right;}
.print-header .ph-right .ph-title{font-size:15px;font-weight:700;color:#0F172A;margin-bottom:3px;}
.print-header .ph-right .ph-meta{font-size:11px;color:#64748B;}
.print-header .ph-filter-strip{width:100%;margin-top:10px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:5px;padding:7px 12px;font-size:11px;color:#1E40AF;font-weight:600;}

@media print{
    /* ── Hide all chrome ── */
    .sidebar, .topbar, .rpt-action-btns,
    .card-box.filter-card,
    .btn-action, .btn-filter, .btn-reset-link,
    .empty-state p + a { display: none !important; }

    /* ── Full-width layout ── */
    .main-content { margin-left: 0 !important; }
    .content-body  { padding: 6px 0 0 !important; }
    body           { background: #fff !important; }

    /* ── Strip decorative chrome ── */
    .gst-stat-card, .card-box, .summary-card {
        box-shadow: none !important;
        border: 1px solid #E2E8F0 !important;
    }

    /* ── Force 4-column stat grid ── */
    .gst-stat-grid {
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 10px !important;
    }

    /* ── Keep summary charts (2-col on print) ── */
    .summary-grid { grid-template-columns: repeat(2, 1fr) !important; }

    /* ── Progress bars — force color printing ── */
    .summary-row-bar { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    /* ── Table fixes ── */
    .table-wrap    { overflow: visible !important; }
    .r-table       { font-size: 10.5px !important; }
    thead tr { background: #0F172A !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    thead th { color: #fff !important; }

    /* ── Show print-only header ── */
    .print-header  { display: flex !important; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; }

    @page { margin: 12mm; }
}
</style>

{{-- ── Print-only Header (hidden on screen) ── --}}
<div class="print-header">
    <div class="ph-left">
        <div class="ph-company">Delawala</div>
        <div class="ph-sub">Properties &amp; Management</div>
    </div>
    <div class="ph-right">
        <div class="ph-title">Loan Report</div>
        <div class="ph-meta">Generated: {{ now()->format('d M Y, h:i A') }}</div>
        @if(request()->hasAny(['from_date','to_date','filter_customer','filter_property','filter_status','filter_bank']))
        <div class="ph-meta" style="margin-top:4px;">
            @if(request('from_date') || request('to_date'))
                Period: {{ request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'All time' }}
                → {{ request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'Today' }}
            @endif
            @if(request('filter_bank')) &nbsp;·&nbsp; Bank: {{ request('filter_bank') }} @endif
            @if(request('filter_status')) &nbsp;·&nbsp; Status: {{ request('filter_status') }} @endif
        </div>
        @endif
    </div>
</div>

{{-- ── Header ── --}}
<div class="rpt-header">
    <div class="rpt-title-block">
        <h2><i class="fa-solid fa-landmark" style="color:#3B82F6;margin-right:9px;"></i>Loan Report</h2>
        <p>Comprehensive loan analysis with bank, customer, and property summaries.</p>
    </div>
    <div class="rpt-action-btns">
        <a href="{{ route('loan-report.pdf', request()->query()) }}" target="_blank" class="btn-pdf">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('loan-report.excel', request()->query()) }}" class="btn-excel">
            <i class="fa-solid fa-file-csv"></i> Export Excel
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-print"></i> Print
        </button>
    </div>
</div>

{{-- ── Summary Cards ── --}}
@php
    $totalLoanAmt    = $loans->sum('loan_amount');
    $totalApproved   = $loans->where('loan_status', 'Approved')->sum('loan_amount')
                     + $loans->where('loan_status', 'Active')->sum('loan_amount')
                     + $loans->where('loan_status', 'Completed')->sum('loan_amount');
    $totalPendingAmt = $loans->where('loan_status', 'Pending')->sum('loan_amount')
                     + $loans->where('loan_status', 'Under Process')->sum('loan_amount');
    $totalApps       = $loans->count();
@endphp

<div class="gst-stat-grid">
    <div class="gst-stat-card">
        <div class="sc-icon sc-blue"><i class="fa-solid fa-indian-rupee-sign"></i></div>
        <div class="sc-label">Total Loan Amount</div>
        <div class="sc-value" style="color:#3B82F6;">₹{{ number_format($totalLoanAmt, 2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="sc-label">Total Approved Loan</div>
        <div class="sc-value" style="color:#10B981;">₹{{ number_format($totalApproved, 2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-amber"><i class="fa-solid fa-hourglass-half"></i></div>
        <div class="sc-label">Total Pending Loan</div>
        <div class="sc-value" style="color:#F59E0B;">₹{{ number_format($totalPendingAmt, 2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-purple"><i class="fa-solid fa-file-invoice"></i></div>
        <div class="sc-label">Total Loan Applications</div>
        <div class="sc-value" style="color:#8B5CF6;">{{ $totalApps }}</div>
    </div>
</div>

{{-- ── Filters ── --}}
<div class="card-box filter-card">
    <form method="GET" action="{{ route('loan-report.index') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">From Date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-ctrl @error('from_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">To Date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-ctrl @error('to_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">Customer / Borrower</span>
            <select name="filter_customer" class="filter-ctrl @error('filter_customer') is-invalid @enderror">
                <option value="">All Customers</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ request('filter_customer') == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Property / Project</span>
            <select name="filter_property" class="filter-ctrl @error('filter_property') is-invalid @enderror">
                <option value="">All Properties</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}" {{ request('filter_property') == $p->id ? 'selected' : '' }}>
                        {{ $p->property_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Loan Status</span>
            <select name="filter_status" class="filter-ctrl @error('filter_status') is-invalid @enderror">
                <option value="">All Statuses</option>
                @foreach(['Approved','Pending','Rejected','Under Process','Active','Completed','Closed','Cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('filter_status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Bank / Finance Company</span>
            <input type="text" name="filter_bank" value="{{ request('filter_bank') }}" placeholder="e.g. SBI, HDFC..." class="filter-ctrl @error('filter_bank') is-invalid @enderror">
        </div>
        <div class="filter-group filter-actions">
            <a href="{{ route('loan-report.index') }}" class="btn-reset-link">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
            <button type="submit" class="btn-filter">
                <i class="fa-solid fa-magnifying-glass"></i> Search
            </button>
        </div>
    </form>
</div>

{{-- ── Data Table ── --}}
<div class="card-box">
    <div style="font-size:13.5px;font-weight:700;color:#0F172A;margin-bottom:16px;">
        <i class="fa-solid fa-table-list" style="color:#3B82F6;margin-right:7px;"></i>
        Loan Records <span style="font-size:12px;font-weight:500;color:#64748B;margin-left:8px;">Showing {{ $totalApps }} record{{ $totalApps != 1 ? 's' : '' }}</span>
    </div>

    <div class="table-wrap">
        <table class="r-table">
            <thead>
                <tr>
                    <th style="width:44px;">Sr. No.</th>
                    <th>Date</th>
                    <th>Customer / Borrower</th>
                    <th>Property / Project</th>
                    <th>Bank / Finance Company</th>
                    <th class="amt">Loan Amount</th>
                    <th class="amt">Approved Amount</th>
                    <th class="amt">Pending Amount</th>
                    <th style="text-align:center;">Loan Status</th>
                    <th style="text-align:center;width:60px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $index => $loan)
                    @php
                        $statusKey = strtolower(str_replace(' ', '-', $loan->loan_status ?? 'pending'));
                        $approvedAmt = in_array($loan->loan_status, ['Approved','Active','Completed'])
                                        ? $loan->loan_amount : $loan->paid_amount;
                        $pendingAmt  = in_array($loan->loan_status, ['Pending','Under Process','Rejected'])
                                        ? $loan->loan_amount : $loan->pending_amount;
                    @endphp
                    <tr>
                        <td style="color:#94A3B8;font-size:12px;">{{ $index + 1 }}</td>
                        <td style="white-space:nowrap;font-size:13px;">
                            {{ \Carbon\Carbon::parse($loan->loan_start_date)->format('d M Y') }}
                        </td>
                        <td>
                            @if($loan->customer)
                                <div style="font-weight:600;color:#0F172A;">{{ $loan->customer->name }}</div>
                            @else
                                <span style="color:#94A3B8;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($loan->property)
                                <div style="font-weight:500;">{{ $loan->property->property_name }}</div>
                            @else
                                <span style="color:#94A3B8;">—</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600;">{{ $loan->bank_name ?? '—' }}</div>
                            @if($loan->loan_type)
                                <div style="font-size:11.5px;color:#64748B;">{{ $loan->loan_type }}</div>
                            @endif
                        </td>
                        <td class="amt" style="font-weight:700;color:#3B82F6;">
                            ₹{{ number_format($loan->loan_amount, 2) }}
                        </td>
                        <td class="amt" style="font-weight:700;color:#10B981;">
                            ₹{{ number_format($approvedAmt, 2) }}
                        </td>
                        <td class="amt" style="font-weight:700;color:#EF4444;">
                            ₹{{ number_format($pendingAmt, 2) }}
                        </td>
                        <td style="text-align:center;">
                            <span class="status-badge sb-{{ $statusKey }}">
                                {{ $loan->loan_status ?? 'Pending' }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <a href="{{ route('loans.show', $loan->id) }}" class="btn-action view" title="View Loan Details">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <i class="fa-solid fa-landmark"></i>
                                <p>No loan records found matching your filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($loans->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align:left;font-size:13px;">
                            <i class="fa-solid fa-sigma" style="color:#3B82F6;margin-right:5px;"></i> Totals
                        </td>
                        <td class="amt" style="color:#3B82F6;">₹{{ number_format($totalLoanAmt, 2) }}</td>
                        <td class="amt" style="color:#10B981;">₹{{ number_format($totalApproved, 2) }}</td>
                        <td class="amt" style="color:#EF4444;">₹{{ number_format($totalPendingAmt, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    @if($loans->count() > 0)
        <div style="margin-top:16px;padding-top:14px;border-top:1px solid #E2E8F0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
            <span style="font-size:12.5px;color:#64748B;">
                <strong>{{ $totalApps }}</strong> loan{{ $totalApps != 1 ? 's' : '' }} &middot;
                Total: <strong style="color:#3B82F6;">₹{{ number_format($totalLoanAmt, 2) }}</strong>
            </span>
            <span style="font-size:12px;color:#94A3B8;">
                <i class="fa-regular fa-clock"></i> Generated: {{ now()->format('d M Y, h:i A') }}
            </span>
        </div>
    @endif
</div>

{{-- ── Summary Sections ── --}}
@if($loans->count() > 0)
<div class="summary-grid">

    {{-- Bank-wise --}}
    <div class="summary-card">
        <div class="summary-card-title">
            <i class="fa-solid fa-landmark"></i> Bank / Finance Company
        </div>
        @php $maxBank = $byBank ? max($byBank) : 1; @endphp
        @forelse($byBank as $bank => $amt)
        <div class="summary-row">
            <div class="summary-row-label">{{ $bank ?: '—' }}</div>
            <div class="summary-row-bar-wrap">
                <div class="summary-row-bar" style="width:{{ min(100, round(($amt/$maxBank)*100)) }}%;background:#3B82F6;"></div>
            </div>
            <div class="summary-row-amount" style="color:#3B82F6;">₹{{ number_format($amt, 2) }}</div>
        </div>
        @empty
        <p style="color:#64748B;font-size:13px;">No data available.</p>
        @endforelse
    </div>

    {{-- Customer-wise --}}
    <div class="summary-card">
        <div class="summary-card-title">
            <i class="fa-solid fa-users"></i> Customer / Borrower
        </div>
        @php $maxCust = $byCustomer ? max($byCustomer) : 1; @endphp
        @forelse($byCustomer as $cust => $amt)
        <div class="summary-row">
            <div class="summary-row-label">{{ $cust }}</div>
            <div class="summary-row-bar-wrap">
                <div class="summary-row-bar" style="width:{{ min(100, round(($amt/$maxCust)*100)) }}%;background:#0EA5E9;"></div>
            </div>
            <div class="summary-row-amount" style="color:#0EA5E9;">₹{{ number_format($amt, 2) }}</div>
        </div>
        @empty
        <p style="color:#64748B;font-size:13px;">No data available.</p>
        @endforelse
    </div>

    {{-- Type-wise --}}
    <div class="summary-card">
        <div class="summary-card-title">
            <i class="fa-solid fa-chart-pie"></i> Loan Type Breakdown
        </div>
        @php $maxType = $byType ? max($byType) : 1; @endphp
        @forelse($byType as $type => $amt)
        <div class="summary-row">
            <div class="summary-row-label">{{ $type }}</div>
            <div class="summary-row-bar-wrap">
                <div class="summary-row-bar" style="width:{{ min(100, round(($amt/$maxType)*100)) }}%;background:#8B5CF6;"></div>
            </div>
            <div class="summary-row-amount" style="color:#8B5CF6;">₹{{ number_format($amt, 2) }}</div>
        </div>
        @empty
        <p style="color:#64748B;font-size:13px;">No data available.</p>
        @endforelse
    </div>

    {{-- Status-wise count --}}
    <div class="summary-card">
        <div class="summary-card-title">
            <i class="fa-solid fa-list-check"></i> Status Summary
        </div>
        @php
            $statusCounts = $loans->groupBy('loan_status')->map->count();
            $statusColors = [
                'Approved'      => '#10B981',
                'Active'        => '#10B981',
                'Pending'       => '#F59E0B',
                'Under Process' => '#8B5CF6',
                'Rejected'      => '#EF4444',
                'Completed'     => '#3B82F6',
                'Closed'        => '#64748B',
                'Cancelled'     => '#EF4444',
            ];
            $maxCount = $statusCounts->max() ?: 1;
        @endphp
        @forelse($statusCounts as $status => $count)
        <div class="summary-row">
            <div class="summary-row-label">{{ $status }}</div>
            <div class="summary-row-bar-wrap">
                <div class="summary-row-bar" style="width:{{ min(100, round(($count/$maxCount)*100)) }}%;background:{{ $statusColors[$status] ?? '#64748B' }};"></div>
            </div>
            <div class="summary-row-amount" style="color:{{ $statusColors[$status] ?? '#64748B' }};">{{ $count }} loan{{ $count != 1 ? 's' : '' }}</div>
        </div>
        @empty
        <p style="color:#64748B;font-size:13px;">No data available.</p>
        @endforelse
    </div>

</div>
@endif
@endsection

