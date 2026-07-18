@extends('admin.layouts.app')
@section('title', 'Expense Report')
@section('page-title', 'Reports')
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
.gst-stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;margin-bottom:22px;}
.gst-stat-card{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:16px 18px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);transition:transform .2s ease,box-shadow .2s ease;}
.gst-stat-card:hover{transform:translateY(-3px);box-shadow:0 4px 8px rgba(0,0,0,0.07),0 16px 36px rgba(0,0,0,0.09);}
.gst-stat-card .sc-label{font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px;}
.gst-stat-card .sc-value{font-size:18px;font-weight:800;color:#0F172A;}
.gst-stat-card .sc-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:15px;margin-bottom:10px;}
.sc-blue  {background:rgba(59,130,246,0.1); color:#3B82F6;}
.sc-green {background:rgba(16,185,129,0.1); color:#10B981;}
.sc-amber {background:rgba(245,158,11,0.1); color:#F59E0B;}
.sc-red   {background:rgba(239,68,68,0.1);  color:#EF4444;}

/* Filter */
.card-box{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:20px 22px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);margin-bottom:18px;}
.filter-bar{display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:15px;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:5px;}
.filter-label{font-size:11px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.6px;}
.filter-ctrl{padding:9px 12px;border:1.5px solid #E2E8F0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#fff;transition:border-color .18s ease;width:100%;}
.filter-ctrl:focus{border-color:#3B82F6;box-shadow:0 0 0 3px rgba(59,130,246,0.15);}
.filter-actions{display:flex;gap:10px;justify-content:flex-end;}
.btn-filter{background:#0F172A;color:#fff;padding:10px 20px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:6px;transition:background .18s ease;justify-content:center;}
.btn-filter:hover{background:#1E293B;}
.btn-reset{padding:10px 14px;border:1px solid #E2E8F0;background:#fff;color:#64748B;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:5px;transition:all .18s ease;justify-content:center;}
.btn-reset:hover{color:#0F172A;border-color:#94A3B8;background:#F8FAFC;}

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

/* Badges */
.status-badge{display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;text-align:center;}
.sb-approved{background:rgba(16,185,129,0.1);color:#065F46;}
.sb-pending{background:rgba(245,158,11,0.1);color:#92400E;}
.sb-rejected{background:rgba(239,68,68,0.1);color:#991B1B;}

/* Empty state */
.empty-state{text-align:center;padding:52px 20px;color:#94A3B8;}
.empty-state i{font-size:40px;margin-bottom:14px;display:block;opacity:.3;}
.empty-state p{font-size:14px;}

.btn-action{width:28px;height:28px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;color:#64748B;border:1px solid #E2E8F0;background:#fff;transition:all .18s;text-decoration:none;}
.btn-action:hover{color:#3B82F6;border-color:#3B82F6;background:#F0F7FF;}

/* Summary charts/progress bars */
.summary-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;margin-top:24px;}
.summary-card{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);}
.summary-card-title{font-size:12px;font-weight:700;color:#3B82F6;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid #E2E8F0;display:flex;align-items:center;gap:8px;}
.summary-row{display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid #F1F5F9;}
.summary-row:last-child{border-bottom:none;}
.summary-row-label{font-size:13px;color:#0F172A;font-weight:500;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;padding-right:10px;}
.summary-row-bar-wrap{flex:0 0 100px;height:5px;background:#F1F5F9;border-radius:3px;overflow:hidden;margin:0 10px;}
.summary-row-bar{height:100%;border-radius:3px;background:#3B82F6;}
.summary-row-amount{font-size:13px;font-weight:700;color:#EF4444;white-space:nowrap;}

/* ── Print header (screen: hidden, print: visible) ── */
.print-header{display:none;border-bottom:2.5px solid #EF4444;padding-bottom:12px;margin-bottom:20px;flex-direction:row;justify-content:space-between;align-items:flex-start;}
.print-header .ph-left .ph-company{font-size:20px;font-weight:800;color:#0F172A;}
.print-header .ph-left .ph-sub{font-size:10px;color:#EF4444;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-top:2px;}
.print-header .ph-right{text-align:right;}
.print-header .ph-right .ph-title{font-size:15px;font-weight:700;color:#0F172A;margin-bottom:3px;}
.print-header .ph-right .ph-meta{font-size:11px;color:#64748B;}
.print-header .ph-filter-strip{width:100%;margin-top:10px;background:#FEF2F2;border:1px solid #FECACA;border-radius:5px;padding:7px 12px;font-size:11px;color:#991B1B;font-weight:600;}

@media print{
    /* ── Hide all chrome ── */
    .sidebar, .topbar, .rpt-action-btns,
    .card-box.filter-card,
    .btn-action, .btn-filter, .btn-reset,
    .empty-state a { display: none !important; }

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
        <h2><i class="fa-solid fa-receipt" style="color:#EF4444;margin-right:9px;"></i>Expense Report</h2>
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

{{-- Summary Cards --}}
<div class="gst-stat-grid">
    <div class="gst-stat-card">
        <div class="sc-icon sc-red"><i class="fa-solid fa-indian-rupee-sign"></i></div>
        <div class="sc-label">Total Expenses</div>
        <div class="sc-value" style="color:#EF4444;">₹{{ number_format($totalAmount, 2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="sc-label">Paid Expenses</div>
        <div class="sc-value" style="color:#10B981;">₹{{ number_format($paidAmount, 2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-amber"><i class="fa-solid fa-clock"></i></div>
        <div class="sc-label">Pending Expenses</div>
        <div class="sc-value" style="color:#F59E0B;">₹{{ number_format($pendingAmount, 2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-blue"><i class="fa-solid fa-calendar-day"></i></div>
        <div class="sc-label">Today's Expense</div>
        <div class="sc-value" style="color:#3B82F6;">₹{{ number_format($todayAmount, 2) }}</div>
    </div>
</div>

{{-- Filters --}}
<div class="card-box filter-card">
    <form method="GET" action="{{ route('expense-report.index') }}" class="filter-bar">
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
                @foreach(['Cash', 'Bank Transfer', 'UPI', 'Cheque', 'Other'] as $mode)
                    <option value="{{ $mode }}" {{ request('filter_mode') == $mode ? 'selected' : '' }}>{{ $mode }}</option>
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
        <div class="filter-group filter-actions" style="grid-column: span 1; min-width: 180px;">
            <a href="{{ route('expense-report.index') }}" class="btn-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
            <button type="submit" class="btn-filter">
                <i class="fa-solid fa-magnifying-glass"></i> Search
            </button>
        </div>
    </form>
</div>

{{-- Data Table --}}
<div class="card-box">
    <div style="font-size:13.5px;font-weight:700;color:#0F172A;margin-bottom:16px;">
        <i class="fa-solid fa-list" style="color:#EF4444;margin-right:7px;"></i>
        Expense Records <span style="font-size:12px;font-weight:500;color:#64748B;margin-left:8px;">Showing {{ $expenses->count() }} records</span>
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
                        <td style="color:#94A3B8; font-size:12px;">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($e->expense_date)->format('d M Y') }}</td>
                        <td>
                            @if($e->expense_category)
                                <span style="background: rgba(59,130,246,0.1); color:#1D4ED8; padding:3px 8px; border-radius:5px; font-weight:600; font-size:11.5px;">{{ $e->expense_category }}</span>
                            @else
                                <span style="color:#94A3B8;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($e->vendor)
                                <strong>{{ $e->vendor->name }}</strong>
                            @else
                                {{ $e->paid_to ?? '—' }}
                            @endif
                        </td>
                        <td style="font-weight: 500; color: #0F172A;">{{ $e->expense_title }}</td>
                        <td>{{ $e->payment_mode ?? '—' }}</td>
                        <td class="amt" style="font-weight: 700; color:#EF4444;">₹{{ number_format($e->amount, 2) }}</td>
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
                        <td colspan="6" style="text-align: left;">Total Summary</td>
                        <td class="amt" style="color:#EF4444; font-weight: 800;">₹{{ number_format($totalAmount, 2) }}</td>
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
            <i class="fa-solid fa-calendar-days"></i> Monthly Summary
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
            <i class="fa-solid fa-tags"></i> Category-wise Summary
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
            <i class="fa-solid fa-building"></i> Property-wise Summary
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

