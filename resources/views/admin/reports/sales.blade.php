@extends('admin.layouts.app')
@section('title','Sales Report (Accounting Method)')
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

/* ── Summary Cards (Accounting Grid) ── */
.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 24px; }
.stat-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 18px 20px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.30); transition: all .25s ease;
}
.stat-card:hover { transform: translateY(-3px); border-color: rgba(59, 130, 246, 0.40) !important; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45); }
.stat-card .sc-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 10px; }
.sc-blue   { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.sc-amber  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.sc-green  { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.sc-red    { background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.sc-purple { background: rgba(139, 92, 246, 0.18) !important; color: #C084FC !important; border: 1px solid rgba(139, 92, 246, 0.35) !important; }
.sc-teal   { background: rgba(20, 184, 166, 0.18) !important; color: #2DD4BF !important; border: 1px solid rgba(20, 184, 166, 0.35) !important; }

.stat-card .sc-label { font-size: 10.5px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 4px; }
.stat-card .sc-value { font-size: 19px; font-weight: 800; color: #FFFFFF !important; font-family: monospace; }
.stat-card .sc-sub { font-size: 11px; color: #94A3B8; margin-top: 3px; font-weight: 600; }

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
.sales-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.sales-table thead th {
    padding: 14px 16px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: .9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.sales-table tbody td {
    padding: 14px 16px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle; white-space: nowrap !important;
}
.sales-table tbody tr { transition: background .14s ease; }
.sales-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.sales-table tfoot td {
    padding: 16px 16px !important; background: rgba(255, 255, 255, 0.08) !important;
    font-weight: 800; border-top: 2px solid rgba(255, 255, 255, 0.15) !important;
    color: #FFFFFF !important; white-space: nowrap !important;
}
.amt { text-align: right; font-variant-numeric: tabular-nums; font-family: monospace; }

/* ── Status Badges ── */
.pay-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; white-space: nowrap !important; }
.pb-paid      { background: rgba(34, 197, 94, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.pb-pending   { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.pb-partial   { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.pb-cancelled { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

/* ── Action link ── */
.tbl-action { color: #60A5FA !important; font-size: 12.5px; font-weight: 600; text-decoration: none !important; display: inline-flex; align-items: center; gap: 5px; transition: color .15s; }
.tbl-action:hover { color: #93C5FD !important; }

/* ── Empty state ── */
.empty-state { text-align: center; padding: 52px 20px; color: #CBD5E1; }
.empty-state i { font-size: 40px; margin-bottom: 14px; display: block; opacity: .3; }

/* ── Date badge ── */
.date-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; border-radius: 8px; padding: 6px 12px; font-size: 12.5px; color: #34D399 !important; font-weight: 600; margin-top: 8px; }

@media print {
    .sidebar, .topbar, .rpt-action-btns, .card-box.filter-card, .btn-action, .tbl-action, .btn-filter, .btn-reset, .empty-state a { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .content-body  { padding: 6px 0 0 !important; }
    body           { background: #fff !important; }
    .stat-card, .card-box { box-shadow: none !important; border: 1px solid #E2E8F0 !important; background: #FFF !important; }
    .stat-grid { grid-template-columns: repeat(4, 1fr) !important; gap: 8px !important; }
    .table-wrap    { overflow: visible !important; }
    .sales-table   { font-size: 9.5px !important; color: #000 !important; }
    thead tr { background: #0F172A !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    thead th { color: #fff !important; }
    .date-badge    { display: none !important; }
    @page { margin: 8mm; }
}
</style>

{{-- ── Header ── --}}
<div class="rpt-header">
    <div class="rpt-title-block">
        <h2><i class="fa-solid fa-handshake" style="color:#10B981;margin-right:9px;"></i>Sales Accounting Report</h2>
        <p>Complete Sales Ledger with Selling Price, Purchase Cost (Lidhi Price), Brokerage & Realized Net Profit.</p>
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

{{-- ── Accounting Summary Cards ── --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="sc-icon sc-blue"><i class="fa-solid fa-file-contract"></i></div>
        <div class="sc-label">Total Bookings</div>
        <div class="sc-value" style="color:#60A5FA !important;">{{ $totalBookings }}</div>
        <div class="sc-sub">Units / properties sold</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon sc-purple"><i class="fa-solid fa-tag"></i></div>
        <div class="sc-label">Turnover (Sale Value)</div>
        <div class="sc-value" style="color:#C084FC !important;">₹{{ number_format($totalSale, 2) }}</div>
        <div class="sc-sub">Total revenue recognized</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon sc-amber"><i class="fa-solid fa-boxes-packing"></i></div>
        <div class="sc-label">Acquisition Cost</div>
        <div class="sc-value" style="color:#FBBF24 !important;">₹{{ number_format($totalPurchaseCost, 2) }}</div>
        <div class="sc-sub">Lidhi price of sold units</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon sc-red"><i class="fa-solid fa-hand-holding-dollar"></i></div>
        <div class="sc-label">Broker Commission</div>
        <div class="sc-value" style="color:#F87171 !important;">₹{{ number_format($totalCommission, 2) }}</div>
        <div class="sc-sub">Committed brokerage</div>
    </div>
    <div class="stat-card" style="border:1.5px solid {{ $totalNetProfit >= 0 ? 'rgba(16,185,129,0.35)' : 'rgba(239,68,68,0.35)' }} !important;">
        <div class="sc-icon sc-green"><i class="fa-solid fa-sack-dollar"></i></div>
        <div class="sc-label">Realized Net Profit</div>
        <div class="sc-value" style="color:{{ $totalNetProfit >= 0 ? '#34D399' : '#F87171' }} !important;">
            {{ $totalNetProfit >= 0 ? '' : '−' }}₹{{ number_format(abs($totalNetProfit), 2) }}
        </div>
        <div class="sc-sub">Margin: {{ $profitMargin }}%</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon sc-teal"><i class="fa-solid fa-circle-check"></i></div>
        <div class="sc-label">Total Received</div>
        <div class="sc-value" style="color:#2DD4BF !important;">₹{{ number_format($totalReceived, 2) }}</div>
        <div class="sc-sub">Cash / bank inflow</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon sc-red"><i class="fa-solid fa-clock"></i></div>
        <div class="sc-label">Total Pending</div>
        <div class="sc-value" style="color:#F87171 !important;">₹{{ number_format($totalPending, 2) }}</div>
        <div class="sc-sub">Outstanding dues</div>
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
        @if(isset($firms) && $firms->count() > 1 && Auth::user()?->isAdmin())
        <div class="filter-group">
            <span class="filter-label">Firm</span>
            <select name="firm_id" class="filter-ctrl">
                <option value="">All Firms</option>
                @foreach($firms as $f)
                    <option value="{{ $f->id }}" {{ request('firm_id')==$f->id?'selected':'' }}>{{ $f->firm_name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <button type="submit" class="btn-filter">
            <i class="fa-solid fa-magnifying-glass"></i> Search
        </button>
        @if(request()->hasAny(['from_date','to_date','filter_customer','filter_property','filter_status','firm_id']))
            <a href="{{ route('reports.sales') }}" class="btn-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        @endif
    </form>
</div>

{{-- ── Data Table ── --}}
<div class="card-box">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <div style="font-size:15px;font-weight:800;color:#FFFFFF !important;">
            <i class="fa-solid fa-table-list" style="color:#10B981;margin-right:7px;"></i>
            Sales Accounting Ledger
            <span style="font-size:12.5px;font-weight:600;color:#94A3B8;margin-left:8px;">
                {{ $totalBookings }} record{{ $totalBookings!=1?'s':'' }}
            </span>
        </div>
        @if(request()->hasAny(['from_date','to_date','filter_customer','filter_property','filter_status','firm_id']))
            <span style="font-size:12px;color:#94A3B8;display:flex;align-items:center;gap:5px;">
                <i class="fa-solid fa-filter" style="color:#10B981;"></i> Filtered results
            </span>
        @endif
    </div>

    <div class="table-wrap">
        <table class="sales-table">
            <thead>
                <tr>
                    <th style="width:30px;">#</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Property / Unit</th>
                    <th>Broker</th>
                    <th class="amt">Sale Value (₹)</th>
                    <th class="amt">Purchase Cost (₹)</th>
                    <th class="amt">Commission (₹)</th>
                    <th class="amt">Net Profit (₹)</th>
                    <th class="amt">Margin %</th>
                    <th class="amt">Received (₹)</th>
                    <th class="amt">Pending (₹)</th>
                    <th style="text-align:center;">Payment Status</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $s)
                @php
                    $badge = match(strtolower($s->payment_status ?? 'pending')) {
                        'paid'      => 'pb-paid',
                        'partial'   => 'pb-partial',
                        'cancelled' => 'pb-cancelled',
                        default     => 'pb-pending',
                    };
                    $received = (float)($s->received_amount ?? 0);
                    $pending  = (float)($s->remaining_amount ?? max(0, ($s->sale_amount ?? 0) - $received));
                    $pCost    = (float)$s->total_purchase_cost;
                    $comm     = (float)$s->broker_commission_amount;
                    $nProfit  = (float)$s->net_profit;
                    $isProf   = $nProfit >= 0;
                    $propName = $s->property?->property_name ?? ($s->properties->pluck('property_name')->implode(', ') ?: '—');
                @endphp
                <tr>
                    <td style="color:#94A3B8;font-size:12px;">{{ $i + 1 }}</td>
                    <td style="white-space:nowrap;font-size:12.5px;font-weight:600;color:#FFFFFF !important;">
                        {{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '—' }}
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF !important;">{{ $s->customer?->name ?? '—' }}</div>
                        @if($s->customer?->mobile)
                            <div style="font-size:11.5px;color:#94A3B8;">{{ $s->customer->mobile }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF !important;">{{ $propName }}</div>
                        @if($s->invoice_no)
                            <div style="font-size:11.5px;color:#94A3B8;">INV: {{ $s->invoice_no }}</div>
                        @endif
                    </td>
                    <td style="font-size:12.5px;color:#CBD5E1;">{{ $s->broker?->name ?? '—' }}</td>
                    {{-- Sale Value --}}
                    <td class="amt" style="color:#C084FC !important;font-weight:700;">
                        ₹{{ number_format($s->sale_amount ?? 0, 2) }}
                    </td>
                    {{-- Purchase Cost (Lidhi Price) --}}
                    <td class="amt" style="color:#FBBF24 !important;font-weight:700;">
                        ₹{{ number_format($pCost, 2) }}
                    </td>
                    {{-- Commission --}}
                    <td class="amt" style="color:#F87171 !important;font-weight:700;">
                        ₹{{ number_format($comm, 2) }}
                    </td>
                    {{-- Net Profit --}}
                    <td class="amt" style="color:{{ $isProf ? '#34D399' : '#F87171' }} !important;font-weight:800;">
                        {{ $isProf ? '' : '−' }}₹{{ number_format(abs($nProfit), 2) }}
                    </td>
                    {{-- Margin % --}}
                    <td class="amt" style="color:{{ $isProf ? '#34D399' : '#F87171' }} !important;font-weight:700;font-size:12px;">
                        {{ $s->profit_margin_percentage }}%
                    </td>
                    {{-- Received --}}
                    <td class="amt" style="color:#2DD4BF !important;font-weight:700;">
                        ₹{{ number_format($received, 2) }}
                    </td>
                    {{-- Pending --}}
                    <td class="amt" style="color:#F87171 !important;font-weight:700;">
                        ₹{{ number_format($pending, 2) }}
                    </td>
                    <td style="text-align:center;">
                        <span class="pay-badge {{ $badge }}">
                            {{ ucfirst($s->payment_status ?? 'pending') }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <a href="{{ route('property-sales.show', $s->id) }}" class="tbl-action" title="View Sale Details">
                            <i class="fa-regular fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14">
                        <div class="empty-state">
                            <i class="fa-solid fa-handshake"></i>
                            <p>No sales records found for the selected filters.</p>
                            @if(request()->hasAny(['from_date','to_date','filter_customer','filter_property','filter_status','firm_id']))
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
                    <td colspan="5" style="font-size:13px;color:#FFFFFF !important;font-weight:800;">
                        <i class="fa-solid fa-calculator" style="color:#34D399;margin-right:6px;"></i>
                        ACCOUNTING TOTALS ({{ $totalBookings }} sale{{ $totalBookings!=1?'s':'' }})
                    </td>
                    <td class="amt" style="color:#C084FC !important;font-size:13.5px;font-weight:800;">₹{{ number_format($totalSale, 2) }}</td>
                    <td class="amt" style="color:#FBBF24 !important;font-size:13.5px;font-weight:800;">₹{{ number_format($totalPurchaseCost, 2) }}</td>
                    <td class="amt" style="color:#F87171 !important;font-size:13.5px;font-weight:800;">₹{{ number_format($totalCommission, 2) }}</td>
                    <td class="amt" style="color:{{ $totalNetProfit >= 0 ? '#34D399' : '#F87171' }} !important;font-size:14px;font-weight:800;">
                        {{ $totalNetProfit >= 0 ? '' : '−' }}₹{{ number_format(abs($totalNetProfit), 2) }}
                    </td>
                    <td class="amt" style="color:{{ $totalNetProfit >= 0 ? '#34D399' : '#F87171' }} !important;font-size:12.5px;font-weight:800;">{{ $profitMargin }}%</td>
                    <td class="amt" style="color:#2DD4BF !important;font-size:13.5px;font-weight:800;">₹{{ number_format($totalReceived, 2) }}</td>
                    <td class="amt" style="color:#F87171 !important;font-size:13.5px;font-weight:800;">₹{{ number_format($totalPending, 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if($records->count() > 0)
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;
                margin-top:16px;padding-top:14px;border-top:1px solid rgba(255,255,255,0.10);">
        <span style="font-size:12.5px;color:#CBD5E1;">
            Turnover: <strong style="color:#C084FC !important;">₹{{ number_format($totalSale, 2) }}</strong>
            &nbsp;·&nbsp; Purchase Cost: <strong style="color:#FBBF24 !important;">₹{{ number_format($totalPurchaseCost, 2) }}</strong>
            &nbsp;·&nbsp; Commission: <strong style="color:#F87171 !important;">₹{{ number_format($totalCommission, 2) }}</strong>
            &nbsp;·&nbsp; Net Profit: <strong style="color:{{ $totalNetProfit >= 0 ? '#34D399' : '#F87171' }} !important;">₹{{ number_format($totalNetProfit, 2) }} ({{ $profitMargin }}%)</strong>
        </span>
        <span style="font-size:12px;color:#94A3B8;">
            <i class="fa-regular fa-clock"></i> Generated: {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>
    @endif
</div>

@endsection
