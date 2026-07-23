@extends('admin.layouts.app')
@section('title','Sales Report')
@section('page-title','Reports')
@section('content')
<style>
/* ── Header ── */
.rpt-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:14px;}
.rpt-title-block h2{font-size:22px;font-weight:800;color:#0F172A;margin-bottom:4px;}
.rpt-title-block p{font-size:13.5px;color:#64748B;}
.rpt-action-btns{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
/* ── Buttons ── */
.btn-pdf{padding:9px 16px;border:1px solid #EF4444;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:7px;color:#EF4444;background:rgba(239,68,68,0.05);text-decoration:none;transition:all .2s ease;}
.btn-pdf:hover{background:rgba(239,68,68,0.12);transform:translateY(-1px);}
.btn-excel{padding:9px 16px;border:1px solid #16803D;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:7px;color:#16803D;background:rgba(34,197,94,0.05);text-decoration:none;transition:all .2s ease;}
.btn-excel:hover{background:rgba(34,197,94,0.12);transform:translateY(-1px);}
.btn-print{padding:9px 16px;border:1px solid #6366F1;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:7px;color:#6366F1;background:rgba(99,102,241,0.05);cursor:pointer;font-family:inherit;transition:all .2s ease;}
.btn-print:hover{background:rgba(99,102,241,0.12);transform:translateY(-1px);}
/* ── Summary Cards ── */
.stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:16px;margin-bottom:24px;}
.stat-card{background:#fff;border:1px solid #E2E8F0;border-radius:16px;padding:20px 22px;
    box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);
    transition:transform .22s ease,box-shadow .22s ease;}
.stat-card:hover{transform:translateY(-3px);box-shadow:0 4px 8px rgba(0,0,0,0.07),0 16px 36px rgba(0,0,0,0.09);}
.stat-card .sc-icon{width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:18px;margin-bottom:12px;}
.sc-blue  {background:rgba(59,130,246,0.1);color:#3B82F6;}
.sc-green {background:rgba(16,185,129,0.1);color:#10B981;}
.sc-amber {background:rgba(245,158,11,0.1);color:#F59E0B;}
.sc-red   {background:rgba(239,68,68,0.1); color:#EF4444;}
.sc-purple{background:rgba(139,92,246,0.1);color:#8B5CF6;}
.stat-card .sc-label{font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.6px;margin-bottom:5px;}
.stat-card .sc-value{font-size:20px;font-weight:800;}
/* ── Filter ── */
.card-box{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:20px 22px;
    box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);margin-bottom:18px;}
.filter-bar{display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:5px;}
.filter-label{font-size:11px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.6px;}
.filter-ctrl{padding:9px 12px;border:1.5px solid #E2E8F0;border-radius:8px;font-size:13px;
    font-family:inherit;outline:none;background:#fff;transition:border-color .18s;min-width:150px;}
.filter-ctrl:focus{border-color:#3B82F6;box-shadow:0 0 0 3px rgba(59,130,246,0.12);}
.btn-filter{background:#0F172A;color:#fff;padding:9px 16px;border-radius:8px;border:none;font-size:13px;
    font-weight:600;cursor:pointer;font-family:inherit;align-self:flex-end;display:inline-flex;align-items:center;gap:6px;transition:background .18s;}
.btn-filter:hover{background:#1E293B;}
.btn-reset{padding:9px 10px;color:#64748B;text-decoration:none;font-size:13px;align-self:flex-end;
    display:inline-flex;align-items:center;gap:5px;transition:color .15s;}
.btn-reset:hover{color:#0F172A;}
/* ── Table ── */
.table-wrap{width:100%;overflow-x:auto;}
.sales-table{width:100%;border-collapse:collapse;font-size:13px;}
.sales-table thead th{padding:11px 14px;background:#F8FAFC;color:#475569;font-weight:700;
    border-bottom:2px solid #E2E8F0;font-size:11px;text-transform:uppercase;letter-spacing:.7px;white-space:nowrap;}
.sales-table tbody td{padding:12px 14px;border-bottom:1px solid #F1F5F9;vertical-align:middle;}
.sales-table tbody tr{transition:background .14s ease;}
.sales-table tbody tr:hover{background:#F0FDF4;}
.sales-table tfoot td{padding:12px 14px;background:#F8FAFC;font-weight:800;border-top:2px solid #E2E8F0;}
.amt{text-align:right;font-variant-numeric:tabular-nums;}
/* ── Status Badges ── */
.pay-badge{display:inline-block;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;}
.pb-paid     {background:rgba(16,185,129,0.1);color:#065F46;}
.pb-pending  {background:rgba(245,158,11,0.1);color:#92400E;}
.pb-partial  {background:rgba(59,130,246,0.1);color:#1E40AF;}
.pb-cancelled{background:rgba(239,68,68,0.1);color:#991B1B;}
/* ── Action link ── */
.tbl-action{color:#3B82F6;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:4px;transition:color .15s;}
.tbl-action:hover{color:#1D4ED8;}
/* ── Empty state ── */
.empty-state{text-align:center;padding:52px 20px;color:#94A3B8;}
.empty-state i{font-size:40px;margin-bottom:14px;display:block;opacity:.3;}
/* ── Date badge ── */
.date-badge{display:inline-flex;align-items:center;gap:6px;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;padding:6px 12px;font-size:12.5px;color:#065F46;font-weight:600;margin-top:8px;}
/* ── Print header (screen: hidden, print: visible) ── */
.print-header{display:none;border-bottom:2.5px solid #10B981;padding-bottom:12px;margin-bottom:20px;flex-direction:row;justify-content:space-between;align-items:flex-start;}
.print-header .ph-left .ph-company{font-size:20px;font-weight:800;color:#0F172A;}
.print-header .ph-left .ph-sub{font-size:10px;color:#10B981;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-top:2px;}
.print-header .ph-right{text-align:right;}
.print-header .ph-right .ph-title{font-size:15px;font-weight:700;color:#0F172A;margin-bottom:3px;}
.print-header .ph-right .ph-meta{font-size:11px;color:#64748B;}
.print-header .ph-filter-strip{width:100%;margin-top:10px;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:5px;padding:7px 12px;font-size:11px;color:#166534;font-weight:600;}
@media print{
    /* ── Hide all chrome ── */
    .sidebar, .topbar, .rpt-action-btns,
    .card-box.filter-card,
    .btn-action, .tbl-action, .btn-filter, .btn-reset,
    .empty-state a { display: none !important; }

    /* ── Full-width layout ── */
    .main-content { margin-left: 0 !important; }
    .content-body  { padding: 6px 0 0 !important; }
    body           { background: #fff !important; }

    /* ── Strip decorative chrome from cards/boxes ── */
    .stat-card, .gst-stat-card, .card-box, .section-card {
        box-shadow: none !important;
        border: 1px solid #E2E8F0 !important;
    }

    /* ── Force 4-column stat grid ── */
    .stat-grid, .gst-stat-grid {
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 10px !important;
    }

    /* ── Keep summary charts for context ── */
    .summary-grid  { grid-template-columns: repeat(2, 1fr) !important; }

    /* ── Table fixes ── */
    .table-wrap    { overflow: visible !important; }
    .sales-table, .pay-table, .rent-table, .r-table {
        font-size: 10.5px !important;
    }
    thead tr { background: #0F172A !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    thead th { color: #fff !important; }

    /* ── Show print-only header, hide screen-only date badge ── */
    .print-header  { display: flex !important; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; }
    .date-badge    { display: none !important; }

    /* ── Page settings ── */
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
        <div class="sc-value" style="color:#3B82F6;">{{ $totalBookings }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon sc-purple"><i class="fa-solid fa-building"></i></div>
        <div class="sc-label">Total Sale Value</div>
        <div class="sc-value" style="color:#8B5CF6;">₹{{ number_format($totalSale,2) }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon sc-green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="sc-label">Total Received</div>
        <div class="sc-value" style="color:#059669;">₹{{ number_format($totalReceived,2) }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon sc-red"><i class="fa-solid fa-clock"></i></div>
        <div class="sc-label">Total Pending</div>
        <div class="sc-value" style="color:#DC2626;">₹{{ number_format($totalPending,2) }}</div>
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
        <div style="font-size:13.5px;font-weight:700;color:#0F172A;">
            <i class="fa-solid fa-table-list" style="color:#10B981;margin-right:7px;"></i>
            Sales Records
            <span style="font-size:12px;font-weight:500;color:#64748B;margin-left:8px;">
                {{ $totalBookings }} record{{ $totalBookings!=1?'s':'' }}
            </span>
        </div>
        @if(request()->hasAny(['from_date','to_date','filter_customer','filter_property','filter_status']))
            <span style="font-size:12px;color:#64748B;display:flex;align-items:center;gap:5px;">
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
                    <td style="white-space:nowrap;font-size:13px;font-weight:600;">
                        {{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '—' }}
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $s->customer?->name ?? '—' }}</div>
                        @if($s->customer?->mobile)
                            <div style="font-size:11px;color:#64748B;">{{ $s->customer->mobile }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $s->property?->property_name ?? '—' }}</div>
                        @if($s->invoice_no)
                            <div style="font-size:11px;color:#64748B;">INV: {{ $s->invoice_no }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px;color:#64748B;">{{ $s->broker?->name ?? '—' }}</td>
                    <td class="amt" style="color:#8B5CF6;font-weight:700;">
                        ₹{{ number_format($s->booking_amount ?? 0, 2) }}
                    </td>
                    <td class="amt" style="color:#059669;font-weight:700;">
                        ₹{{ number_format($received, 2) }}
                    </td>
                    <td class="amt" style="color:#DC2626;font-weight:700;">
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
                                <a href="{{ route('reports.sales') }}" style="color:#3B82F6;font-size:13px;margin-top:8px;display:inline-block;">
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
                    <td colspan="5" style="font-size:13px;color:#0F172A;">
                        <i class="fa-solid fa-sigma" style="color:#10B981;margin-right:6px;"></i>
                        Total ({{ $totalBookings }} sale{{ $totalBookings!=1?'s':'' }})
                    </td>
                    <td class="amt" style="color:#8B5CF6;font-size:14px;">₹{{ number_format($records->sum('booking_amount'),2) }}</td>
                    <td class="amt" style="color:#059669;font-size:14px;">₹{{ number_format($totalReceived,2) }}</td>
                    <td class="amt" style="color:#DC2626;font-size:14px;">₹{{ number_format($totalPending,2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if($records->count() > 0)
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;
                margin-top:16px;padding-top:14px;border-top:1px solid #F1F5F9;">
        <span style="font-size:12px;color:#64748B;">
            <strong>{{ $totalBookings }}</strong> record{{ $totalBookings!=1?'s':'' }}
            &nbsp;·&nbsp; Received: <strong style="color:#059669;">₹{{ number_format($totalReceived,2) }}</strong>
            &nbsp;·&nbsp; Pending: <strong style="color:#DC2626;">₹{{ number_format($totalPending,2) }}</strong>
        </span>
        <span style="font-size:12px;color:#64748B;">
            <i class="fa-regular fa-clock"></i> Generated: {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>
    @endif
</div>

@endsection
