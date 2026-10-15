@extends('admin.layouts.app')
@section('title', 'Expense Report')
@section('page-title', 'Reports')
@section('content')
<style>
/* ── Layout & Typography ── */
.rpt-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.rpt-title-block h2 {
    font-size: 26px;
    font-weight: 800;
    color: #FFFFFF !important;
    letter-spacing: -0.3px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.rpt-title-block p {
    font-size: 14px;
    color: #94A3B8 !important;
    margin: 0;
}
.rpt-action-btns {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
}
.btn-pdf {
    padding: 10px 18px;
    border: 1px solid rgba(239, 68, 68, 0.4);
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #FFFFFF !important;
    background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
    transition: all .25s ease;
}
.btn-pdf:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.5);
    color: #FFFFFF !important;
}
.btn-excel {
    padding: 10px 18px;
    border: 1px solid rgba(16, 185, 129, 0.4);
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #FFFFFF !important;
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
    transition: all .25s ease;
}
.btn-excel:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
    color: #FFFFFF !important;
}
.btn-print {
    padding: 10px 18px;
    border: 1px solid rgba(99, 102, 241, 0.4);
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #FFFFFF !important;
    background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
    cursor: pointer;
    font-family: inherit;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
    transition: all .25s ease;
}
.btn-print:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
    color: #FFFFFF !important;
}

/* ── Modern Premium Stat Cards ── */
.gst-stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media (max-width: 1100px) {
    .gst-stat-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .gst-stat-grid { grid-template-columns: 1fr; }
}

.gst-stat-card {
    background: rgba(15, 23, 42, 0.72) !important;
    backdrop-filter: blur(20px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 22px 24px !important;
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.35) !important;
    transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
    position: relative;
    overflow: hidden;
}
.gst-stat-card:hover {
    transform: translateY(-3px);
    border-color: rgba(255, 255, 255, 0.25) !important;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45) !important;
}
.gst-stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    opacity: 0.9;
}
.stat-card-red::before   { background: linear-gradient(90deg, #EF4444, #F87171); }
.stat-card-green::before { background: linear-gradient(90deg, #10B981, #34D399); }
.stat-card-amber::before { background: linear-gradient(90deg, #F59E0B, #FBBF24); }
.stat-card-blue::before  { background: linear-gradient(90deg, #3B82F6, #60A5FA); }

.stat-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}
.sc-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.sc-red   { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.3); }
.sc-green { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.3); }
.sc-amber { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.3); }
.sc-blue  { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.3); }

.stat-pill {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 20px;
    letter-spacing: 0.4px;
    text-transform: uppercase;
}
.pill-red   { background: rgba(239, 68, 68, 0.12); color: #FCA5A5; border: 1px solid rgba(239, 68, 68, 0.25); }
.pill-green { background: rgba(16, 185, 129, 0.12); color: #6EE7B7; border: 1px solid rgba(16, 185, 129, 0.25); }
.pill-amber { background: rgba(245, 158, 11, 0.12); color: #FDE68A; border: 1px solid rgba(245, 158, 11, 0.25); }
.pill-blue  { background: rgba(59, 130, 246, 0.12); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.25); }

.gst-stat-card .sc-label {
    font-size: 11.5px;
    font-weight: 800;
    color: #94A3B8 !important;
    text-transform: uppercase;
    letter-spacing: .8px;
    margin-bottom: 6px;
}
.gst-stat-card .sc-value {
    font-size: 25px;
    font-weight: 800;
    color: #FFFFFF !important;
    letter-spacing: -0.4px;
    line-height: 1.2;
}

/* ── Modern Premium Filter Card ── */
.card-box {
    background: rgba(15, 23, 42, 0.72) !important;
    backdrop-filter: blur(20px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 24px;
}
.filter-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.filter-card-title {
    font-size: 14.5px;
    font-weight: 800;
    color: #FFFFFF !important;
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: 0.3px;
}
.filter-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}
@media (max-width: 1100px) {
    .filter-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .filter-grid { grid-template-columns: 1fr; }
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.filter-label {
    font-size: 11px;
    font-weight: 800;
    color: #94A3B8 !important;
    text-transform: uppercase;
    letter-spacing: .8px;
}
.filter-ctrl {
    padding: 10px 14px !important;
    background: rgba(16, 22, 34, 0.80) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 10px !important;
    font-size: 13.5px !important;
    color: #FFFFFF !important;
    outline: none;
    transition: all .2s ease;
    width: 100%;
    box-sizing: border-box;
    color-scheme: dark;
}
select.filter-ctrl option {
    background: #0F172A !important;
    color: #FFFFFF !important;
}
.filter-ctrl:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
    background: rgba(16, 22, 34, 0.95) !important;
}

/* Dedicated Action Bar */
.filter-footer-bar {
    grid-column: 1 / -1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 6px;
    padding-top: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    flex-wrap: wrap;
    gap: 12px;
}
.filter-actions-right {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-left: auto;
}
.btn-filter {
    background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%) !important;
    color: #FFFFFF !important;
    padding: 10px 24px !important;
    border-radius: 10px !important;
    border: 1px solid #60A5FA !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    cursor: pointer;
    font-family: inherit;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all .25s ease;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.4);
    height: 42px;
}
.btn-filter:hover {
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 22px rgba(37, 99, 235, 0.55);
}
.btn-reset {
    background: rgba(30, 41, 59, 0.85) !important;
    color: #CBD5E1 !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    border-radius: 10px !important;
    padding: 10px 18px !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: all .2s ease;
    height: 42px;
    box-sizing: border-box;
}
.btn-reset:hover {
    color: #FFFFFF !important;
    background: rgba(51, 65, 85, 0.95) !important;
    border-color: rgba(255, 255, 255, 0.35) !important;
    transform: translateY(-2px);
}

/* ── Modern Table ── */
.table-wrap {
    width: 100%;
    overflow-x: auto;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.10);
    background: rgba(16, 22, 34, 0.60);
}
.r-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}
.r-table thead th {
    padding: 13px 14px !important;
    background: rgba(15, 23, 42, 0.90) !important;
    color: #94A3B8 !important;
    font-weight: 800;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .8px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.12) !important;
    white-space: nowrap !important;
}
.r-table tbody td {
    padding: 13px 14px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    color: #E2E8F0 !important;
    font-size: 13.5px;
    vertical-align: middle;
}
.r-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.04) !important;
}
.r-table tfoot td {
    padding: 14px 16px !important;
    background: rgba(15, 23, 42, 0.90) !important;
    font-weight: 800;
    color: #FFFFFF !important;
    border-top: 2px solid rgba(255, 255, 255, 0.15) !important;
}
.amt {
    text-align: right;
    font-variant-numeric: tabular-nums;
    font-weight: 700;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: center;
}
.sb-approved { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); }
.sb-pending  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35); }
.sb-rejected { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); }

/* Empty state */
.empty-state {
    text-align: center;
    padding: 56px 20px;
    color: #94A3B8;
}
.empty-state i {
    font-size: 42px;
    margin-bottom: 14px;
    display: block;
    color: #64748B;
    opacity: 0.6;
}
.empty-state p {
    font-size: 14.5px;
    margin: 0;
}

.btn-action {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #94A3B8;
    border: 1px solid rgba(255, 255, 255, 0.15);
    background: rgba(30, 41, 59, 0.80);
    transition: all .2s ease;
    text-decoration: none;
}
.btn-action:hover {
    color: #FFFFFF;
    border-color: #3B82F6;
    background: #2563EB;
    transform: translateY(-1px);
}

/* ── Summary Cards ── */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 24px;
}
.summary-card {
    background: rgba(15, 23, 42, 0.72) !important;
    backdrop-filter: blur(20px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 22px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
}
.summary-card-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #60A5FA !important;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    gap: 8px;
}
.summary-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}
.summary-row:last-child { border-bottom: none; }
.summary-row-label {
    font-size: 13.5px;
    color: #E2E8F0;
    font-weight: 600;
    flex: 1;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    padding-right: 10px;
}
.summary-row-bar-wrap {
    flex: 0 0 100px;
    height: 6px;
    background: rgba(255, 255, 255, 0.10);
    border-radius: 4px;
    overflow: hidden;
    margin: 0 12px;
}
.summary-row-bar {
    height: 100%;
    border-radius: 4px;
    background: #3B82F6;
}
.summary-row-amount {
    font-size: 13.5px;
    font-weight: 800;
    color: #F87171;
    white-space: nowrap;
}

/* ── Print header ── */
.print-header {
    display: none;
    border-bottom: 2.5px solid #EF4444;
    padding-bottom: 12px;
    margin-bottom: 20px;
    flex-direction: row;
    justify-content: space-between;
    align-items: flex-start;
}
.print-header .ph-left .ph-company { font-size: 20px; font-weight: 800; color: #0F172A; }
.print-header .ph-left .ph-sub { font-size: 10px; color: #EF4444; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-top: 2px; }
.print-header .ph-right { text-align: right; }
.print-header .ph-right .ph-title { font-size: 15px; font-weight: 700; color: #0F172A; margin-bottom: 3px; }
.print-header .ph-right .ph-meta { font-size: 11px; color: #64748B; }

@media print {
    .sidebar, .topbar, .rpt-action-btns,
    .card-box.filter-card,
    .btn-action, .btn-filter, .btn-reset,
    .empty-state a { display: none !important; }

    .main-content { margin-left: 0 !important; }
    .content-body  { padding: 6px 0 0 !important; }
    body           { background: #fff !important; color: #000 !important; }

    .gst-stat-card, .card-box, .summary-card {
        box-shadow: none !important;
        border: 1px solid #E2E8F0 !important;
        background: #fff !important;
        color: #000 !important;
    }
    .gst-stat-grid {
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 10px !important;
    }
    .summary-grid { grid-template-columns: repeat(2, 1fr) !important; }
    .summary-row-bar { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .table-wrap    { overflow: visible !important; background: #fff !important; }
    .r-table       { font-size: 10.5px !important; }
    thead tr { background: #0F172A !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    thead th { color: #fff !important; }
    .print-header  { display: flex !important; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; }
    @page { margin: 12mm; }
}
</style>

{{-- Print-only Header --}}
<div class="print-header">
    <div class="ph-left">
        <div class="ph-company">Delawala</div>
        <div class="ph-sub">Properties &amp; Management</div>
    </div>
    <div class="ph-right">
        <div class="ph-title">Expense Report</div>
        <div class="ph-meta">Generated: {{ now()->format('d M Y, h:i A') }}</div>
        @if(request()->hasAny(['from_date','to_date','filter_category','filter_vendor','filter_mode','filter_status']))
        <div class="ph-meta" style="margin-top:4px;">
            @if(request('from_date') || request('to_date'))
                Period: {{ request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'All time' }}
                → {{ request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'Today' }}
            @endif
            @if(request('filter_mode')) &nbsp;·&nbsp; Mode: {{ request('filter_mode') }} @endif
            @if(request('filter_status')) &nbsp;·&nbsp; Status: {{ request('filter_status') }} @endif
        </div>
        @endif
    </div>
</div>

{{-- Header --}}
<div class="rpt-header">
    <div class="rpt-title-block">
        <h2><i class="fa-solid fa-receipt" style="color:#EF4444;"></i>Expense Report</h2>
        <p>Detailed expense analysis with category summaries, filters, and export options.</p>
    </div>
    <div class="rpt-action-btns">
        <a href="{{ route('expense-report.pdf', request()->query()) }}" target="_blank" class="btn-pdf">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('expense-report.excel', request()->query()) }}" class="btn-excel">
            <i class="fa-solid fa-file-csv"></i> Export Excel
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-print"></i> Print
        </button>
    </div>
</div>

{{-- Summary Cards (Refined Premium High-Contrast Layout) --}}
<div class="gst-stat-grid">
    <div class="gst-stat-card stat-card-red">
        <div class="stat-card-head">
            <div class="sc-icon sc-red"><i class="fa-solid fa-indian-rupee-sign"></i></div>
            <span class="stat-pill pill-red">Gross Total</span>
        </div>
        <div class="sc-label">Total Expenses</div>
        <div class="sc-value" style="color: #F87171 !important;">₹{{ number_format($totalAmount, 2) }}</div>
    </div>

    <div class="gst-stat-card stat-card-green">
        <div class="stat-card-head">
            <div class="sc-icon sc-green"><i class="fa-solid fa-circle-check"></i></div>
            <span class="stat-pill pill-green">Settled</span>
        </div>
        <div class="sc-label">Paid Expenses</div>
        <div class="sc-value" style="color: #34D399 !important;">₹{{ number_format($paidAmount, 2) }}</div>
    </div>

    <div class="gst-stat-card stat-card-amber">
        <div class="stat-card-head">
            <div class="sc-icon sc-amber"><i class="fa-solid fa-clock"></i></div>
            <span class="stat-pill pill-amber">Outstanding</span>
        </div>
        <div class="sc-label">Pending Expenses</div>
        <div class="sc-value" style="color: #FBBF24 !important;">₹{{ number_format($pendingAmount, 2) }}</div>
    </div>

    <div class="gst-stat-card stat-card-blue">
        <div class="stat-card-head">
            <div class="sc-icon sc-blue"><i class="fa-solid fa-calendar-day"></i></div>
            <span class="stat-pill pill-blue">Today</span>
        </div>
        <div class="sc-label">Today's Expense</div>
        <div class="sc-value" style="color: #60A5FA !important;">₹{{ number_format($todayAmount, 2) }}</div>
    </div>
</div>

{{-- Filters (Balanced 4-Column Grid with Dedicated Bottom Action Bar) --}}
<div class="card-box filter-card">
    <div class="filter-card-header">
        <div class="filter-card-title">
            <i class="fa-solid fa-sliders" style="color: #60A5FA;"></i> Filter &amp; Search Expenses
        </div>
        <span style="font-size: 12px; color: #94A3B8; font-weight: 600;">
            Showing {{ $expenses->count() }} record{{ $expenses->count() != 1 ? 's' : '' }}
        </span>
    </div>

    <form method="GET" action="{{ route('expense-report.index') }}" class="filter-grid">
        @if(auth()->user() && auth()->user()->isAdmin())
        <div class="filter-group">
            <span class="filter-label">Firm</span>
            <select name="firm_id" class="filter-ctrl" onchange="this.form.submit()">
                <option value="">All Firms</option>
                @foreach($firms as $f)
                    <option value="{{ $f->id }}" {{ request('firm_id') == $f->id ? 'selected' : '' }}>{{ $f->firm_name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="filter-group">
            <span class="filter-label">Project</span>
            <select name="filter_project" class="filter-ctrl" onchange="this.form.submit()">
                <option value="">All Projects</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('filter_project') == $p->id ? 'selected' : '' }}>{{ $p->project_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">From Date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-ctrl @error('from_date') is-invalid @enderror">
        </div>

        <div class="filter-group">
            <span class="filter-label">To Date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-ctrl @error('to_date') is-invalid @enderror">
        </div>

        <div class="filter-group">
            <span class="filter-label">Expense Category</span>
            <select name="filter_category" class="filter-ctrl @error('filter_category') is-invalid @enderror">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('filter_category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Vendor / Paid To</span>
            <select name="filter_vendor" class="filter-ctrl @error('filter_vendor') is-invalid @enderror">
                <option value="">All Vendors</option>
                @foreach($vendors as $v)
                    <option value="{{ $v->id }}" {{ request('filter_vendor') == $v->id ? 'selected' : '' }}>
                        {{ $v->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Payment Mode</span>
            <select name="filter_mode" class="filter-ctrl @error('filter_mode') is-invalid @enderror">
                <option value="">All Modes</option>
                @foreach(\App\Models\PaymentMode::where('status', 'active')->orderBy('name')->get() as $pm)
                    <option value="{{ $pm->name }}" {{ request('filter_mode') == $pm->name ? 'selected' : '' }}>
                        {{ $pm->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Expense Status</span>
            <select name="filter_status" class="filter-ctrl @error('filter_status') is-invalid @enderror">
                <option value="">All Statuses</option>
                @foreach(['Pending', 'Approved', 'Rejected'] as $status)
                    <option value="{{ $status }}" {{ request('filter_status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
        </div>

        {{-- Dedicated Full-Width Filter Actions Bar --}}
        <div class="filter-footer-bar">
            <div style="font-size: 12.5px; color: #94A3B8; display: flex; align-items: center; gap: 8px;">
                @if(request()->hasAny(['firm_id', 'filter_project', 'from_date', 'to_date', 'filter_category', 'filter_vendor', 'filter_mode', 'filter_status']))
                    <span style="color: #60A5FA; font-weight: 700;">
                        <i class="fa-solid fa-filter"></i> Filters Active
                    </span>
                @else
                    <span>Showing unfiltered expense ledger</span>
                @endif
            </div>

            <div class="filter-actions-right">
                <a href="{{ route('expense-report.index') }}" class="btn-reset">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
                <button type="submit" class="btn-filter">
                    <i class="fa-solid fa-magnifying-glass"></i> Search Expenses
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Data Table --}}
<div class="card-box">
    <div style="font-size:14.5px; font-weight:800; color:#FFFFFF; margin-bottom:16px; display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-list-check" style="color:#EF4444; font-size: 16px;"></i>
            <span>Expense Records</span>
        </div>
        <span style="font-size:12px; font-weight:600; color:#94A3B8;">
            Total Count: {{ $expenses->count() }}
        </span>
    </div>
    <div class="table-wrap">
        <table class="r-table">
            <thead>
                <tr>
                    <th style="width: 40px;">Sr. No.</th>
                    <th>Expense Date</th>
                    <th>Expense Category</th>
                    <th>Vendor / Paid To</th>
                    <th>Description</th>
                    <th>Payment Mode</th>
                    <th class="amt">Amount</th>
                    <th style="text-align: center;">Expense Status</th>
                    <th style="text-align: center; width: 60px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $index => $e)
                    @php
                        $badgeClass = 'sb-approved';
                        if ($e->approval_status === 'Pending') {
                            $badgeClass = 'sb-pending';
                        } elseif ($e->approval_status === 'Rejected') {
                            $badgeClass = 'sb-rejected';
                        }
                    @endphp
                    <tr>
                        <td style="color:#64748B; font-weight: 700; font-size:12px;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ \Carbon\Carbon::parse($e->expense_date)->format('d M Y') }}</td>
                        <td>
                            @if($e->expense_category)
                                <span style="background: rgba(59,130,246,0.15); color:#60A5FA; padding:3px 10px; border-radius:6px; font-weight:700; font-size:11.5px; border: 1px solid rgba(59,130,246,0.3);">
                                    {{ $e->expense_category }}
                                </span>
                            @else
                                <span style="color:#64748B;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($e->vendor)
                                <strong style="color: #FFFFFF;">{{ $e->vendor->name }}</strong>
                            @else
                                <span style="color: #CBD5E1;">{{ $e->paid_to ?? '—' }}</span>
                            @endif
                        </td>
                        <td style="font-weight: 500; color: #CBD5E1; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $e->expense_title }}</td>
                        <td>
                            <span style="color: #94A3B8; font-size: 12px; font-weight: 600;">{{ $e->payment_mode ?? '—' }}</span>
                        </td>
                        <td class="amt" style="font-weight: 800; color:#F87171; font-size: 14px;">₹{{ number_format($e->amount, 2) }}</td>
                        <td style="text-align: center;">
                            <span class="status-badge {{ $badgeClass }}">{{ $e->approval_status ?? 'Pending' }}</span>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('expenses.show', $e->id) }}" class="btn-action" title="View Expense Details">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <i class="fa-solid fa-receipt"></i>
                                <p>No expense records found matching your filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($expenses->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align: left; font-size: 13px;">Total Summary (Filtered)</td>
                        <td class="amt" style="color:#F87171; font-weight: 800; font-size: 15px;">₹{{ number_format($totalAmount, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- Summary Sections --}}
@if($expenses->count() > 0)
<div class="summary-grid">

    {{-- Monthly Summary --}}
    <div class="summary-card">
        <div class="summary-card-title">
            <i class="fa-solid fa-calendar-days" style="color: #60A5FA;"></i> Monthly Summary
        </div>
        @php $maxMonthly = $monthly ? max($monthly) : 1; @endphp
        @forelse($monthly as $month => $amt)
        <div class="summary-row">
            <div class="summary-row-label">{{ $month }}</div>
            <div class="summary-row-bar-wrap">
                <div class="summary-row-bar" style="width:{{ min(100, round(($amt / $maxMonthly) * 100)) }}%;"></div>
            </div>
            <div class="summary-row-amount">₹{{ number_format($amt, 2) }}</div>
        </div>
        @empty
        <p style="color:#64748B;font-size:13px;">No data.</p>
        @endforelse
    </div>

    {{-- Category-wise Summary --}}
    <div class="summary-card">
        <div class="summary-card-title">
            <i class="fa-solid fa-tags" style="color: #34D399;"></i> Category-wise Summary
        </div>
        @php $maxCat = $byCategory ? max($byCategory) : 1; @endphp
        @forelse($byCategory as $cat => $amt)
        <div class="summary-row">
            <div class="summary-row-label">{{ $cat }}</div>
            <div class="summary-row-bar-wrap">
                <div class="summary-row-bar" style="width:{{ min(100, round(($amt / $maxCat) * 100)) }}%;background:#10B981;"></div>
            </div>
            <div class="summary-row-amount">₹{{ number_format($amt, 2) }}</div>
        </div>
        @empty
        <p style="color:#64748B;font-size:13px;">No data.</p>
        @endforelse
    </div>

    {{-- Property-wise Summary --}}
    <div class="summary-card">
        <div class="summary-card-title">
            <i class="fa-solid fa-building" style="color: #38BDF8;"></i> Property-wise Summary
        </div>
        @php $maxProp = $byProperty ? max($byProperty) : 1; @endphp
        @forelse($byProperty as $prop => $amt)
        <div class="summary-row">
            <div class="summary-row-label">{{ $prop }}</div>
            <div class="summary-row-bar-wrap">
                <div class="summary-row-bar" style="width:{{ min(100, round(($amt / $maxProp) * 100)) }}%;background:#0EA5E9;"></div>
            </div>
            <div class="summary-row-amount">₹{{ number_format($amt, 2) }}</div>
        </div>
        @empty
        <p style="color:#64748B;font-size:13px;">No data.</p>
        @endforelse
    </div>

</div>
@endif
@endsection
