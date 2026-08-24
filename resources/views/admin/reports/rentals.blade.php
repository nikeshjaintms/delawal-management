@extends('admin.layouts.app')
@section('title','Rental Report')
@section('page-title','Reports')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.rpt-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 14px; }
.rpt-title-block h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.rpt-title-block p { font-size: 14px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }
.rpt-action-btns { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

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
.sc-teal   { background: rgba(20, 184, 166, 0.18) !important; color: #2DD4BF !important; border: 1px solid rgba(20, 184, 166, 0.35) !important; }
.sc-green  { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.sc-amber  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.sc-red    { background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.sc-blue   { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.stat-card .sc-label { font-size: 11.5px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 5px; }
.stat-card .sc-value { font-size: 24px; font-weight: 800; color: #FFFFFF !important; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 24px;
}

.filter-bar {
    display: flex; gap: 12px; align-items: flex-end;
    background: rgba(255, 255, 255, 0.04); padding: 16px 20px;
    border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10);
    width: 100%; flex-wrap: wrap;
}
.filter-group { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 130px; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; }
.filter-ctrl, .search-input {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease; width: 100%; box-sizing: border-box;
}
select.filter-ctrl option { background: #101622 !important; color: #FFFFFF !important; }
.filter-ctrl:focus, .search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

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

.table-wrap { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); background: rgba(16, 22, 34, 0.70); }
.rent-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.rent-table thead th {
    padding: 13px 14px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.rent-table tbody td {
    padding: 13px 14px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #FFFFFF !important; font-weight: 600; vertical-align: middle;
}
.rent-table tbody tr:hover { background: rgba(255, 255, 255, 0.04) !important; }

/* ── TFOOT LUXURY DARK GLASS STYLING ── */
.rent-table tfoot td {
    padding: 14px 16px !important; background: rgba(255, 255, 255, 0.08) !important;
    font-weight: 800; border-top: 2px solid rgba(255, 255, 255, 0.15) !important;
    color: #FFFFFF !important; white-space: nowrap !important;
}
.amt { text-align: right; font-variant-numeric: tabular-nums; font-family: monospace; }

.pay-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .3px; white-space: nowrap !important; }
.pb-paid      { background: rgba(34, 197, 94, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.pb-pending   { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.pb-partial   { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.pb-cancelled { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.month-badge { display: inline-flex; align-items: center; background: rgba(45, 212, 191, 0.15) !important; color: #2DD4BF !important; border: 1px solid rgba(45, 212, 191, 0.30) !important; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; white-space: nowrap; }
.mode-chip { background: rgba(255, 255, 255, 0.08) !important; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; color: #CBD5E1 !important; border: 1px solid rgba(255, 255, 255, 0.12); }
.tbl-action { color: #60A5FA !important; font-size: 12.5px; font-weight: 700; text-decoration: none !important; display: inline-flex; align-items: center; gap: 5px; transition: all .2s; padding: 5px 10px; border-radius: 6px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.30); }
.tbl-action:hover { background: #2563EB !important; color: #FFFFFF !important; transform: translateY(-1px); }
.empty-state { text-align: center; padding: 52px 20px; color: #94A3B8; }
.empty-state i { font-size: 40px; margin-bottom: 14px; display: block; opacity: .4; color: #2DD4BF; }
.date-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(20, 184, 166, 0.15) !important; border: 1px solid rgba(20, 184, 166, 0.30) !important; border-radius: 8px; padding: 6px 12px; font-size: 12.5px; color: #2DD4BF !important; font-weight: 700; margin-top: 8px; }

/* ── Print header (screen: hidden, print: visible) ── */
.print-header{display:none;border-bottom:2.5px solid #14B8A6;padding-bottom:12px;margin-bottom:20px;flex-direction:row;justify-content:space-between;align-items:flex-start;}
.print-header .ph-left .ph-company{font-size:20px;font-weight:800;color:#0F172A;}
.print-header .ph-sub{font-size:10px;color:#14B8A6;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-top:2px;}
.print-header .ph-right{text-align:right;}
.print-header .ph-right .ph-title{font-size:15px;font-weight:700;color:#0F172A;margin-bottom:3px;}
.print-header .ph-right .ph-meta{font-size:11px;color:#64748B;}
.print-header .ph-filter-strip{width:100%;margin-top:10px;background:#F0FDFA;border:1px solid #99F6E4;border-radius:5px;padding:7px 12px;font-size:11px;color:#0D9488;font-weight:600;}

@media print{
    .sidebar, .topbar, .rpt-action-btns, .card-box.filter-card, .btn-action, .tbl-action, .btn-filter, .btn-reset, .empty-state a { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .content-body  { padding: 6px 0 0 !important; }
    body           { background: #fff !important; }
    .stat-card, .gst-stat-card, .card-box { box-shadow: none !important; border: 1px solid #E2E8F0 !important; }
    .stat-grid, .gst-stat-grid { grid-template-columns: repeat(4, 1fr) !important; gap: 10px !important; }
    .table-wrap  { overflow: visible !important; }
    .rent-table  { font-size: 10.5px !important; }
    thead tr { background: #0F172A !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    thead th { color: #fff !important; }
    .print-header { display: flex !important; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; }
    .date-badge   { display: none !important; }
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
        <div class="ph-title">Rental Report</div>
        <div class="ph-meta">Generated: {{ now()->format('d M Y, h:i A') }}</div>
        @if(request()->hasAny(['from_date','to_date','filter_tenant','filter_property','filter_status','filter_mode']))
        <div class="ph-meta" style="margin-top:4px;">
            @if(request('from_date') || request('to_date'))
                Period: {{ request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'All time' }}
                → {{ request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'Today' }}
            @endif
            @if(request('filter_tenant')) &nbsp;·&nbsp; Tenant: {{ request('filter_tenant') }} @endif
            @if(request('filter_status')) &nbsp;·&nbsp; Status: {{ ucfirst(request('filter_status')) }} @endif
            @if(request('filter_mode')) &nbsp;·&nbsp; Mode: {{ request('filter_mode') }} @endif
        </div>
        @endif
    </div>
</div>

{{-- ── Header ── --}}
<div class="rpt-header">
    <div class="rpt-title-block">
        <h2><i class="fa-solid fa-house-circle-check" style="color:#2DD4BF;margin-right:9px;"></i>Rental Report</h2>
        <p>Tenant-wise rent payments with received amount, pending balance, and payment status.</p>
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
        <a href="{{ route('reports.rentals.pdf', request()->query()) }}" target="_blank" class="btn-pdf">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('reports.rentals.excel', request()->query()) }}" class="btn-excel">
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
        <div class="sc-icon sc-teal"><i class="fa-solid fa-indian-rupee-sign"></i></div>
        <div class="sc-label">Total Rent Amount</div>
        <div class="sc-value" style="color:#2DD4BF;">₹{{ number_format($totalRentAmt,2) }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon sc-green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="sc-label">Total Rent Received</div>
        <div class="sc-value" style="color:#34D399;">₹{{ number_format($totalReceived,2) }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon sc-red"><i class="fa-solid fa-clock"></i></div>
        <div class="sc-label">Total Pending Rent</div>
        <div class="sc-value" style="color:#F87171;">₹{{ number_format($totalPending,2) }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon sc-blue"><i class="fa-solid fa-house-user"></i></div>
        <div class="sc-label">Total Active Rentals</div>
        <div class="sc-value" style="color:#60A5FA;">{{ $totalActive }}</div>
    </div>
</div>

{{-- ── Filter Bar ── --}}
<div class="card-box filter-card">
    <form method="GET" action="{{ route('reports.rentals') }}" class="filter-bar">
        @if(auth()->user() && auth()->user()->isAdmin())
        <div class="filter-group">
            <span class="filter-label">Firm</span>
            <select name="firm_id" class="filter-ctrl" onchange="this.form.submit()">
                <option value="">All Firms</option>
                @foreach($firms as $f)
                    <option value="{{ $f->id }}" {{ request('firm_id')==$f->id?'selected':'' }}>{{ $f->firm_name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="filter-group">
            <span class="filter-label">From Date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-ctrl @error('from_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">To Date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-ctrl @error('to_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">Tenant Name</span>
            <input type="text" name="filter_tenant" value="{{ request('filter_tenant') }}"
                   class="search-input @error('filter_tenant') is-invalid @enderror" placeholder="Search tenant...">
        </div>
        <div class="filter-group">
            <span class="filter-label">Property / Unit</span>
            <select name="filter_property" class="filter-ctrl @error('filter_property') is-invalid @enderror">
                <option value="">All Properties</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}" {{ request('filter_property')==$p->id?'selected':'' }}>
                        {{ $p->property_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Property Type</span>
            <select name="filter_property_type" class="filter-ctrl @error('filter_property_type') is-invalid @enderror">
                <option value="">All Types</option>
                @foreach($propertyTypes as $pt)
                    <option value="{{ $pt->id }}" {{ request('filter_property_type')==$pt->id?'selected':'' }}>
                        {{ $pt->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Rent Status</span>
            <select name="filter_status" class="filter-ctrl @error('filter_status') is-invalid @enderror">
                <option value="">All Status</option>
                @foreach(['paid','pending','partial','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('filter_status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Payment Mode</span>
            <select name="filter_mode" class="filter-ctrl @error('filter_mode') is-invalid @enderror">
                <option value="">All Modes</option>
                @foreach(\App\Models\PaymentMode::whereHas('firms', function($q) { $q->where('firms.id', Auth::user()?->firm_id ?? session('firm_id')); })->where('status', 'active')->orderBy('name')->get() as $pm)
                    <option value="{{ $pm->name }}" {{ request('filter_mode')==$pm->name?'selected':'' }}>{{ $pm->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-filter">
            <i class="fa-solid fa-magnifying-glass"></i> Search
        </button>
        @if(request()->hasAny(['from_date','to_date','firm_id','filter_tenant','filter_property','filter_property_type','filter_status','filter_mode']))
            <a href="{{ route('reports.rentals') }}" class="btn-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        @endif
    </form>
</div>

{{-- ── Data Table ── --}}
<div class="card-box">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
        <div style="font-size:16px;font-weight:800;color:#FFFFFF !important;">
            <i class="fa-solid fa-table-list" style="color:#2DD4BF;margin-right:7px;"></i>
            Rental Payment Records
            <span style="font-size:13px;font-weight:700;color:#94A3B8;margin-left:8px;">
                {{ $records->count() }} record{{ $records->count()!=1?'s':'' }}
            </span>
        </div>
        @if(request()->hasAny(['from_date','to_date','firm_id','filter_tenant','filter_property','filter_status','filter_mode']))
            <span style="font-size:12.5px;color:#94A3B8;font-weight:700;display:flex;align-items:center;gap:5px;">
                <i class="fa-solid fa-filter" style="color:#2DD4BF;"></i> Filtered results
            </span>
        @endif
    </div>

    <div class="table-wrap">
        <table class="rent-table">
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <th>Firm</th>
                    @endif
                    <th>Rent Date</th>
                    <th>Month / Year</th>
                    <th>Tenant Name</th>
                    <th>Property Name</th>
                    <th>Property Type</th>
                    <th>Property Code</th>
                    <th class="amt">Monthly Rent</th>
                    <th class="amt">Received Amt</th>
                    <th class="amt">Pending Amt</th>
                    <th style="text-align:center;">Payment Mode</th>
                    <th style="text-align:center;">Rent Status</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $rp)
                @php
                    $badge = match(strtolower($rp->payment_status ?? 'pending')) {
                        'paid'      => 'pb-paid',
                        'partial'   => 'pb-partial',
                        'cancelled' => 'pb-cancelled',
                        default     => 'pb-pending',
                    };
                @endphp
                <tr>
                    <td style="color:#94A3B8;font-size:12.5px;font-weight:700;">{{ $i + 1 }}</td>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <td style="font-weight:700;font-size:13.5px;color:#FFFFFF;">{{ $rp->firm->firm_name ?? $rp->rental?->firm?->firm_name ?? '—' }}</td>
                    @endif
                    <td style="white-space:nowrap;font-weight:700;font-size:13px;color:#CBD5E1;">
                        {{ $rp->payment_date
                            ? \Carbon\Carbon::parse($rp->payment_date)->format('d M Y')
                            : '—' }}
                    </td>
                    <td>
                        <span class="month-badge">
                            {{ $rp->payment_month }} {{ $rp->payment_year }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">{{ $rp->rental?->tenant_name ?? '—' }}</div>
                        @if($rp->rental?->tenant_mobile)
                            <div style="font-size:11.5px;color:#94A3B8;font-weight:600;">{{ $rp->rental->tenant_mobile }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">
                            {{ $rp->property->property_name ?? $rp->rental?->property?->property_name ?? '—' }}
                        </div>
                    </td>
                    <td>
                        <div style="font-size:13px;color:#CBD5E1;font-weight:600;">
                            {{ $rp->property->propertyType->name ?? $rp->rental?->property?->propertyType->name ?? '—' }}
                        </div>
                    </td>
                    <td>
                        <code class="code-chip">{{ $rp->property->property_code ?? $rp->rental?->property?->property_code ?? '—' }}</code>
                    </td>
                    <td class="amt" style="color:#2DD4BF !important;font-weight:800;font-size:14px;">
                        ₹{{ number_format($rp->rent_amount, 2) }}
                    </td>
                    <td class="amt" style="color:#34D399 !important;font-weight:800;font-size:14px;">
                        ₹{{ number_format($rp->paid_amount, 2) }}
                    </td>
                    <td class="amt" style="color:#F87171 !important;font-weight:800;font-size:14px;">
                        ₹{{ number_format($rp->pending_amount, 2) }}
                    </td>
                    <td style="text-align:center;">
                        @if($rp->payment_mode)
                            <span class="mode-chip">{{ $rp->payment_mode }}</span>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="pay-badge {{ $badge }}">
                            {{ ucfirst($rp->payment_status ?? 'pending') }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <a href="{{ route('rentals.show', $rp->rental_id) }}"
                           class="tbl-action" title="View Rental">
                            <i class="fa-regular fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ (auth()->user() && auth()->user()->isAdmin()) ? 14 : 13 }}">
                        <div class="empty-state">
                            <i class="fa-solid fa-house-circle-check"></i>
                            <p>No rental payment records found for the selected filters.</p>
                            @if(request()->hasAny(['from_date','to_date','filter_tenant','filter_property','filter_status','filter_mode']))
                                <a href="{{ route('reports.rentals') }}"
                                   style="color:#2DD4BF;font-weight:700;font-size:13px;margin-top:8px;display:inline-block;text-decoration:none;">
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
                    <td colspan="{{ (auth()->user() && auth()->user()->isAdmin()) ? 8 : 7 }}" style="font-size:13.5px;color:#FFFFFF !important;font-weight:800;">
                        <i class="fa-solid fa-calculator" style="color:#2DD4BF;margin-right:6px;"></i>
                        Total ({{ $records->count() }} record{{ $records->count()!=1?'s':'' }})
                    </td>
                    <td class="amt" style="color:#2DD4BF !important;font-size:14px;font-weight:800;">₹{{ number_format($totalRentAmt,2) }}</td>
                    <td class="amt" style="color:#34D399 !important;font-size:14px;font-weight:800;">₹{{ number_format($totalReceived,2) }}</td>
                    <td class="amt" style="color:#F87171 !important;font-size:14px;font-weight:800;">₹{{ number_format($totalPending,2) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if($records->count() > 0)
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;
                margin-top:18px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.12);">
        <span style="font-size:13.5px;color:#FFFFFF !important;font-weight:700 !important;">
            <strong>{{ $records->count() }}</strong> record{{ $records->count()!=1?'s':'' }}
            &nbsp;·&nbsp; Received: <strong style="color:#34D399 !important;">₹{{ number_format($totalReceived,2) }}</strong>
            &nbsp;·&nbsp; Pending: <strong style="color:#F87171 !important;">₹{{ number_format($totalPending,2) }}</strong>
            &nbsp;·&nbsp; Active Rentals: <strong style="color:#60A5FA !important;">{{ $totalActive }}</strong>
        </span>
        <span style="font-size:13px;color:#FFFFFF !important;font-weight:700 !important;">
            <i class="fa-regular fa-clock" style="color:#60A5FA;"></i> Generated: {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>
    @endif
</div>

@endsection
