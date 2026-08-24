@extends('admin.layouts.app')
@section('title','Sales Report')
@section('page-title','Reports')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.rpt-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 14px; }
.rpt-title-block h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.rpt-title-block p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }
.rpt-action-btns { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

/* ── Action Buttons ── */
.btn-pdf {
    padding: 10px 18px; border: 1px solid rgba(239, 68, 68, 0.40) !important; border-radius: 10px;
    font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 7px;
    color: #F87171 !important; background: rgba(239, 68, 68, 0.15) !important; text-decoration: none !important;
    transition: all .2s ease; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.20);
}
.btn-pdf:hover { background: rgba(239, 68, 68, 0.28) !important; color: #FFFFFF !important; transform: translateY(-1px); }

.btn-excel {
    padding: 10px 18px; border: 1px solid rgba(16, 185, 129, 0.40) !important; border-radius: 10px;
    font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 7px;
    color: #34D399 !important; background: rgba(16, 185, 129, 0.15) !important; text-decoration: none !important;
    transition: all .2s ease; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.20);
}
.btn-excel:hover { background: rgba(16, 185, 129, 0.28) !important; color: #FFFFFF !important; transform: translateY(-1px); }

.btn-print {
    padding: 10px 18px; border: 1px solid rgba(99, 102, 241, 0.40) !important; border-radius: 10px;
    font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 7px;
    color: #A5B4FC !important; background: rgba(99, 102, 241, 0.15) !important; cursor: pointer;
    font-family: inherit; transition: all .2s ease; box-shadow: 0 4px 14px rgba(99, 102, 241, 0.20);
}
.btn-print:hover { background: rgba(99, 102, 241, 0.28) !important; color: #FFFFFF !important; transform: translateY(-1px); }

/* ── Summary Cards ── */
.stat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
.stat-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 20px 22px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.30); transition: all .25s ease;
}
.stat-card:hover { transform: translateY(-3px); border-color: rgba(59, 130, 246, 0.40) !important; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45); }
.stat-card .sc-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 19px; margin-bottom: 12px; }
.sc-blue   { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.sc-green  { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.sc-amber  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.sc-red    { background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.sc-purple { background: rgba(139, 92, 246, 0.18) !important; color: #C084FC !important; border: 1px solid rgba(139, 92, 246, 0.35) !important; }
.stat-card .sc-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 5px; }
.stat-card .sc-value { font-size: 20px; font-weight: 800; color: #FFFFFF !important; }

/* ── Card Container & Filter ── */
.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 24px;
}

.filter-bar {
    display: flex !important; gap: 12px !important; align-items: flex-end !important;
    background: rgba(255, 255, 255, 0.04) !important; padding: 16px 20px !important;
    border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
    width: 100% !important; flex-wrap: nowrap !important; overflow-x: auto !important;
}
.filter-group { display: flex; flex-direction: column; gap: 6px; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; }
.filter-ctrl {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease; min-width: 140px;
}
select.filter-ctrl option { background: #101622 !important; color: #FFFFFF !important; }
.filter-ctrl:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-filter {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; font-family: inherit; align-self: flex-end; display: inline-flex; align-items: center;
    gap: 6px; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35); height: 42px; white-space: nowrap !important;
}
.btn-filter:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset {
    color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px;
    align-self: flex-end; display: inline-flex; align-items: center; gap: 5px; transition: color .15s; height: 42px; white-space: nowrap !important;
}
.btn-reset:hover { color: #FFFFFF !important; }

/* ── Table & Footer Total Row ── */
.table-wrap { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); }
.sales-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.sales-table thead th {
    padding: 16px 18px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: .9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.sales-table tbody td {
    padding: 16px 18px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle; white-space: nowrap !important;
}
.sales-table tbody tr { transition: background .14s ease; }
.sales-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

/* ── TFOOT LUXURY DARK GLASS STYLING ── */
.sales-table tfoot td {
    padding: 16px 18px !important; background: rgba(255, 255, 255, 0.08) !important;
    font-weight: 800; border-top: 2px solid rgba(255, 255, 255, 0.15) !important;
    color: #FFFFFF !important; white-space: nowrap !important;
}
.amt { text-align: right; font-variant-numeric: tabular-nums; }

/* ── Status Badges ── */
.pay-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; white-space: nowrap !important; }
.pb-paid      { background: rgba(34, 197, 94, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.pb-pending   { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.pb-partial   { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.pb-cancelled { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

/* ── Action link ── */
.tbl-action { color: #60A5FA !important; font-size: 13px; font-weight: 600; text-decoration: none !important; display: inline-flex; align-items: center; gap: 5px; transition: color .15s; }
.tbl-action:hover { color: #93C5FD !important; }

/* ── Empty state ── */
.empty-state { text-align: center; padding: 52px 20px; color: #CBD5E1; }
.empty-state i { font-size: 40px; margin-bottom: 14px; display: block; opacity: .3; }

/* ── Date badge ── */
.date-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; border-radius: 8px; padding: 6px 12px; font-size: 12.5px; color: #34D399 !important; font-weight: 600; margin-top: 8px; }

/* ── Print header ── */
.print-header { display: none; border-bottom: 2.5px solid #10B981; padding-bottom: 12px; margin-bottom: 20px; flex-direction: row; justify-content: space-between; align-items: flex-start; }
.print-header .ph-left .ph-company { font-size: 20px; font-weight: 800; color: #0F172A; }
.print-header .ph-left .ph-sub { font-size: 10px; color: #10B981; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-top: 2px; }
.print-header .ph-right { text-align: right; }
.print-header .ph-right .ph-title { font-size: 15px; font-weight: 700; color: #0F172A; margin-bottom: 3px; }
.print-header .ph-right .ph-meta { font-size: 11px; color: #64748B; }
.print-header .ph-filter-strip { width: 100%; margin-top: 10px; background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 5px; padding: 7px 12px; font-size: 11px; color: #166534; font-weight: 600; }

@media print {
    .sidebar, .topbar, .rpt-action-btns, .card-box.filter-card, .btn-action, .tbl-action, .btn-filter, .btn-reset, .empty-state a { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .content-body  { padding: 6px 0 0 !important; }
    body           { background: #fff !important; }
    .stat-card, .gst-stat-card, .card-box, .section-card { box-shadow: none !important; border: 1px solid #E2E8F0 !important; background: #FFF !important; }
    .stat-grid, .gst-stat-grid { grid-template-columns: repeat(4, 1fr) !important; gap: 10px !important; }
    .summary-grid  { grid-template-columns: repeat(2, 1fr) !important; }
    .table-wrap    { overflow: visible !important; }
    .sales-table, .pay-table, .rent-table, .r-table { font-size: 10.5px !important; color: #000 !important; }
    thead tr { background: #0F172A !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    thead th { color: #fff !important; }
    .print-header  { display: flex !important; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; }
    .date-badge    { display: none !important; }
    @page { margin: 12mm; }
}
</style>

{{-- ── Print-only Header ── --}}
<div class="print-header">
    <div class="ph-left">
        <div class="ph-company">Delawala</div>
        <div class="ph-sub">Properties &amp; Management</div>
    </div>
    <div class="ph-right">
        <div class="ph-title">Sales Report</div>
        <div class="ph-meta">Generated: {{ now()->format('d M Y, h:i A') }}</div>
        @if(request()->hasAny(['from_date','to_date','filter_customer','filter_property','filter_status']))
        <div class="ph-meta" style="margin-top:4px;">
            @if(request('from_date') || request('to_date'))
                Period: {{ request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'All time' }}
                → {{ request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'Today' }}
            @endif
            @if(request('filter_status')) &nbsp;·&nbsp; Status: {{ ucfirst(request('filter_status')) }} @endif
        </div>
        @endif
    </div>
</div>

{{-- ── Header ── --}}
<div class="rpt-header">
    <div class="rpt-title-block">
        <h2><i class="fa-solid fa-handshake" style="color:#10B981;margin-right:9px;"></i>Sales Report</h2>
        <p>Property-wise sales with customer, broker, received and pending amount details.</p>
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
        <a href="{{ route('reports.sales.pdf', request()->query()) }}" target="_blank" class="btn-pdf">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('reports.sales.excel', request()->query()) }}" class="btn-excel">
            <i class="fa-solid fa-file-csv"></i> Export Excel
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-print"></i> Print
        </button>
    </div>
</div>

{{-- ── Summary Cards ── --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="sc-icon sc-blue"><i class="fa-solid fa-file-contract"></i></div>
        <div class="sc-label">Total Bookings</div>
        <div class="sc-value" style="color:#60A5FA !important;">{{ $totalBookings }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon sc-purple"><i class="fa-solid fa-building"></i></div>
        <div class="sc-label">Total Sale Value</div>
        <div class="sc-value" style="color:#C084FC !important;">₹{{ number_format($totalSale,2) }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon sc-green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="sc-label">Total Received</div>
        <div class="sc-value" style="color:#34D399 !important;">₹{{ number_format($totalReceived,2) }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon sc-red"><i class="fa-solid fa-clock"></i></div>
        <div class="sc-label">Total Pending</div>
        <div class="sc-value" style="color:#F87171 !important;">₹{{ number_format($totalPending,2) }}</div>
    </div>
</div>

{{-- ── Filter Bar ── --}}
<div class="card-box filter-card">
    <form method="GET" action="{{ route('reports.sales') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">From Date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-ctrl @error('from_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">To Date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-ctrl @error('to_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">Customer</span>
            <select name="filter_customer" class="filter-ctrl @error('filter_customer') is-invalid @enderror">
                <option value="">All Customers</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ request('filter_customer')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Property / Project</span>
            <select name="filter_property" class="filter-ctrl @error('filter_property') is-invalid @enderror">
                <option value="">All Properties</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}" {{ request('filter_property')==$p->id?'selected':'' }}>{{ $p->property_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Payment Status</span>
            <select name="filter_status" class="filter-ctrl @error('filter_status') is-invalid @enderror">
                <option value="">All Status</option>
                @foreach(['paid','pending','partial','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('filter_status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-filter">
            <i class="fa-solid fa-magnifying-glass"></i> Search
        </button>
        @if(request()->hasAny(['from_date','to_date','filter_customer','filter_property','filter_status']))
            <a href="{{ route('reports.sales') }}" class="btn-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        @endif
    </form>
</div>

{{-- ── Data Table ── --}}
<div class="card-box">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <div style="font-size:14px;font-weight:700;color:#FFFFFF !important;">
            <i class="fa-solid fa-table-list" style="color:#10B981;margin-right:7px;"></i>
            Sales Records
            <span style="font-size:12.5px;font-weight:600;color:#94A3B8;margin-left:8px;">
                {{ $totalBookings }} record{{ $totalBookings!=1?'s':'' }}
            </span>
        </div>
        @if(request()->hasAny(['from_date','to_date','filter_customer','filter_property','filter_status']))
            <span style="font-size:12px;color:#94A3B8;display:flex;align-items:center;gap:5px;">
                <i class="fa-solid fa-filter" style="color:#10B981;"></i> Filtered results
            </span>
        @endif
    </div>

    <div class="table-wrap">
        <table class="sales-table">
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Property / Project</th>
                    <th>Broker</th>
                    <th class="amt">Booking Amt</th>
                    <th class="amt">Received Amt</th>
                    <th class="amt">Pending Amt</th>
                    <th style="text-align:center;">Payment Status</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $s)
                @php
                    $badge    = match(strtolower($s->payment_status ?? 'pending')) {
                        'paid'      => 'pb-paid',
                        'partial'   => 'pb-partial',
                        'cancelled' => 'pb-cancelled',
                        default     => 'pb-pending',
                    };
                    $received = $s->received_amount ?? 0;
                    $pending  = $s->remaining_amount ?? max(0, ($s->sale_amount ?? 0) - $received);
                @endphp
                <tr>
                    <td style="color:#94A3B8;font-size:12px;">{{ $i + 1 }}</td>
                    <td style="white-space:nowrap;font-size:13px;font-weight:600;color:#FFFFFF !important;">
                        {{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '—' }}
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF !important;">{{ $s->customer?->name ?? '—' }}</div>
                        @if($s->customer?->mobile)
                            <div style="font-size:11.5px;color:#94A3B8;">{{ $s->customer->mobile }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF !important;">{{ $s->property?->property_name ?? '—' }}</div>
                        @if($s->invoice_no)
                            <div style="font-size:11.5px;color:#94A3B8;">INV: {{ $s->invoice_no }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px;color:#CBD5E1;">{{ $s->broker?->name ?? '—' }}</td>
                    <td class="amt" style="color:#C084FC !important;font-weight:700;">
                        ₹{{ number_format($s->booking_amount ?? 0, 2) }}
                    </td>
                    <td class="amt" style="color:#34D399 !important;font-weight:700;">
                        ₹{{ number_format($received, 2) }}
                    </td>
                    <td class="amt" style="color:#F87171 !important;font-weight:700;">
                        ₹{{ number_format($pending, 2) }}
                    </td>
                    <td style="text-align:center;">
                        <span class="pay-badge {{ $badge }}">
                            {{ ucfirst($s->payment_status ?? 'pending') }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <a href="{{ route('property-sales.show', $s->id) }}" class="tbl-action" title="View Sale">
                            <i class="fa-regular fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="empty-state">
                            <i class="fa-solid fa-handshake"></i>
                            <p>No sales records found for the selected filters.</p>
                            @if(request()->hasAny(['from_date','to_date','filter_customer','filter_property','filter_status']))
                                <a href="{{ route('reports.sales') }}" style="color:#60A5FA;font-size:13px;margin-top:8px;display:inline-block;">
                                    Clear all filters
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($records->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="5" style="font-size:13.5px;color:#FFFFFF !important;font-weight:800;">
                        <i class="fa-solid fa-sigma" style="color:#34D399;margin-right:6px;"></i>
                        Total ({{ $totalBookings }} sale{{ $totalBookings!=1?'s':'' }})
                    </td>
                    <td class="amt" style="color:#C084FC !important;font-size:14px;font-weight:800;">₹{{ number_format($records->sum('booking_amount'),2) }}</td>
                    <td class="amt" style="color:#34D399 !important;font-size:14px;font-weight:800;">₹{{ number_format($totalReceived,2) }}</td>
                    <td class="amt" style="color:#F87171 !important;font-size:14px;font-weight:800;">₹{{ number_format($totalPending,2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if($records->count() > 0)
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;
                margin-top:16px;padding-top:14px;border-top:1px solid rgba(255,255,255,0.10);">
        <span style="font-size:12.5px;color:#94A3B8;">
            <strong>{{ $totalBookings }}</strong> record{{ $totalBookings!=1?'s':'' }}
            &nbsp;·&nbsp; Received: <strong style="color:#34D399 !important;">₹{{ number_format($totalReceived,2) }}</strong>
            &nbsp;·&nbsp; Pending: <strong style="color:#F87171 !important;">₹{{ number_format($totalPending,2) }}</strong>
        </span>
        <span style="font-size:12.5px;color:#94A3B8;">
            <i class="fa-regular fa-clock"></i> Generated: {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>
    @endif
</div>

@endsection

