@extends('admin.layouts.app')
@section('title', 'Inventory Report')
@section('page-title', 'Reports')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.rpt-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 14px; }
.rpt-title-block h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.rpt-title-block p { font-size: 14px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }
.rpt-action-btns { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.btn-pdf {
    padding: 10px 18px; border: 1px solid #EF4444 !important; border-radius: 10px;
    font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 7px;
    color: #FFFFFF !important; background: #DC2626 !important; text-decoration: none !important;
    transition: all .2s ease; box-shadow: 0 4px 14px rgba(220, 38, 38, 0.40);
}
.btn-pdf:hover { background: #B91C1C !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220, 38, 38, 0.60); }

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

/* Stat cards */
.gst-stat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px; }
.gst-stat-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 20px 22px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.30);
    transition: transform .25s ease, box-shadow .25s ease;
}
.gst-stat-card:hover { transform: translateY(-3px); border-color: rgba(59, 130, 246, 0.40) !important; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45); }
.gst-stat-card .sc-label { font-size: 11.5px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 6px; }
.gst-stat-card .sc-value { font-size: 24px; font-weight: 800; color: #FFFFFF !important; }
.gst-stat-card .sc-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 19px; margin-bottom: 12px; }
.sc-blue  { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35); }
.sc-green { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); }
.sc-amber { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35); }
.sc-red   { background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); }

/* Filter */
.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 24px;
}
.filter-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: flex-end; }
.filter-group { display: flex; flex-direction: column; gap: 6px; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; }
.filter-ctrl {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease; width: 100%; box-sizing: border-box;
}
select.filter-ctrl option { background: #101622 !important; color: #FFFFFF !important; }
.filter-ctrl:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.filter-actions { display: flex; gap: 10px; justify-content: flex-end; }
.btn-filter {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; gap: 6px;
    transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35); height: 42px; justify-content: center;
}
.btn-filter:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset {
    padding: 10px 14px !important; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    background: rgba(255, 255, 255, 0.06) !important; color: #CBD5E1 !important; border-radius: 10px !important;
    font-size: 13.5px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
    transition: all .2s ease; justify-content: center; height: 42px; box-sizing: border-box;
}
.btn-reset:hover { background: rgba(255, 255, 255, 0.12) !important; color: #FFFFFF !important; }

/* Table */
.table-wrap { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); background: rgba(16, 22, 34, 0.70); }
.r-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.r-table thead th {
    padding: 13px 14px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.r-table tbody td {
    padding: 13px 14px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #FFFFFF !important; font-weight: 600; vertical-align: middle;
}
.r-table tbody tr:hover { background: rgba(255, 255, 255, 0.04) !important; }
.r-table tfoot td {
    padding: 14px 16px !important; background: rgba(255, 255, 255, 0.08) !important;
    font-weight: 800; border-top: 2px solid rgba(255, 255, 255, 0.15) !important;
    color: #FFFFFF !important; white-space: nowrap !important;
}
.amt { text-align: right; font-variant-numeric: tabular-nums; font-family: monospace; }

/* Badges */
.status-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; text-align: center; letter-spacing: .3px; }
.sb-approved { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); }
.sb-pending  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35); }
.sb-rejected { background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); }

/* Empty state */
.empty-state { text-align: center; padding: 52px 20px; color: #94A3B8; }
.empty-state i { font-size: 40px; margin-bottom: 14px; display: block; opacity: .4; color: #60A5FA; }
.empty-state p { font-size: 14.5px; color: #CBD5E1; }

.btn-action {
    width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;
    color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.30) !important; background: rgba(59, 130, 246, 0.15) !important;
    transition: all .2s ease; text-decoration: none;
}
.btn-action:hover { background: #2563EB !important; color: #FFFFFF !important; transform: translateY(-1px); }

/* Print */
@media print{
    .sidebar, .topbar, .rpt-action-btns, .card-box.filter-card, .btn-action, .btn-filter, .btn-reset, .empty-state a { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .content-body  { padding: 6px 0 0 !important; }
    body           { background: #fff !important; }
    .gst-stat-card, .card-box { box-shadow: none !important; border: 1px solid #E2E8F0 !important; }
    .gst-stat-grid { grid-template-columns: repeat(4, 1fr) !important; gap: 10px !important; }
    .table-wrap    { overflow: visible !important; }
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
        <div class="ph-title">Inventory Report</div>
        <div class="ph-meta">Generated: {{ now()->format('d M Y, h:i A') }}</div>
        @if(request()->hasAny(['from_date','to_date','filter_material','filter_category','filter_status','filter_supplier']))
        <div class="ph-meta" style="margin-top:4px;">
            @if(request('from_date') || request('to_date'))
                Period: {{ request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'All time' }}
                → {{ request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'Today' }}
            @endif
            @if(request('filter_material')) &nbsp;·&nbsp; Item: {{ request('filter_material') }} @endif
            @if(request('filter_status')) &nbsp;·&nbsp; Status: {{ ucfirst(str_replace('_',' ',request('filter_status'))) }} @endif
            @if(request('filter_supplier')) &nbsp;·&nbsp; Supplier: {{ request('filter_supplier') }} @endif
        </div>
        @endif
    </div>
</div>

{{-- Header --}}
<div class="rpt-header">
    <div class="rpt-title-block">
        <h2><i class="fa-solid fa-boxes-stacked" style="color:#60A5FA;margin-right:9px;"></i>Inventory Report</h2>
        <p>Comprehensive overview of material inventory stock levels, category metrics, and suppliers.</p>
    </div>
    <div class="rpt-action-btns">
        <a href="{{ route('reports.inventory.pdf', request()->query()) }}" target="_blank" class="btn-pdf">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('reports.inventory.excel', request()->query()) }}" class="btn-excel">
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
        <div class="sc-icon sc-blue"><i class="fa-solid fa-cubes"></i></div>
        <div class="sc-label">Total Materials</div>
        <div class="sc-value" style="color:#FFFFFF !important;">{{ $totalMaterials }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-green"><i class="fa-solid fa-warehouse"></i></div>
        <div class="sc-label">Total Stock Quantity</div>
        <div class="sc-value" style="color:#34D399 !important;">{{ number_format($totalStockQty, 2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-amber"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="sc-label">Low Stock Items</div>
        <div class="sc-value" style="color:#FBBF24 !important;">{{ $lowStockItems }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-red"><i class="fa-solid fa-circle-xmark"></i></div>
        <div class="sc-label">Out of Stock Items</div>
        <div class="sc-value" style="color:#F87171 !important;">{{ $outOfStockItems }}</div>
    </div>
</div>

{{-- Filters --}}
<div class="card-box filter-card">
    <form method="GET" action="{{ route('reports.inventory') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">From Date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-ctrl @error('from_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">To Date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-ctrl @error('to_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">Material / Item Name</span>
            <input type="text" name="filter_material" value="{{ request('filter_material') }}" placeholder="Search Material..." class="filter-ctrl @error('filter_material') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">Category</span>
            <select name="filter_category" class="filter-ctrl @error('filter_category') is-invalid @enderror">
                <option value="">All Categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('filter_category') == $c->id ? 'selected' : '' }}>
                        {{ $c->category_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Stock Status</span>
            <select name="filter_status" class="filter-ctrl @error('filter_status') is-invalid @enderror">
                <option value="">All Statuses</option>
                <option value="in_stock" {{ request('filter_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="low_stock" {{ request('filter_status') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                <option value="out_of_stock" {{ request('filter_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Supplier / Vendor Name</span>
            <select name="filter_supplier" class="filter-ctrl @error('filter_supplier') is-invalid @enderror">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier }}" {{ request('filter_supplier') === $supplier ? 'selected' : '' }}>
                        {{ $supplier }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group filter-actions" style="grid-column: span 1; min-width: 180px;">
            <a href="{{ route('reports.inventory') }}" class="btn-reset">
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
    <div style="font-size:16px;font-weight:800;color:#FFFFFF !important;margin-bottom:18px;">
        <i class="fa-solid fa-list" style="color:#60A5FA;margin-right:7px;"></i>
        Inventory Stock Records <span style="font-size:13px;font-weight:700;color:#94A3B8;margin-left:8px;">Showing {{ $materials->count() }} items</span>
    </div>
    <div class="table-wrap">
        <table class="r-table">
            <thead>
                <tr>
                    <th style="width: 40px;">Sr. No.</th>
                    <th>Date</th>
                    <th>Material / Item Name</th>
                    <th>Category</th>
                    <th class="amt">Opening Stock</th>
                    <th class="amt">Stock In</th>
                    <th class="amt">Stock Out</th>
                    <th class="amt">Available Stock</th>
                    <th style="text-align: center;">Stock Status</th>
                    <th style="text-align: center; width: 60px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $index => $m)
                    @php
                        $badgeClass = 'sb-approved';
                        if ($m->stock_status === 'Low Stock') {
                            $badgeClass = 'sb-pending';
                        } elseif ($m->stock_status === 'Out of Stock') {
                            $badgeClass = 'sb-rejected';
                        }
                    @endphp
                    <tr>
                        <td style="color:#94A3B8; font-size:12.5px; font-weight:700;">{{ $index + 1 }}</td>
                        <td style="color:#CBD5E1; font-weight:600;">{{ $m->latest_date }}</td>
                        <td>
                            <strong style="color: #FFFFFF !important; font-size:13.5px;">{{ $m->material_name }}</strong>
                        </td>
                        <td style="color:#CBD5E1; font-weight:600;">{{ $m->materialCategory?->category_name ?? '—' }}</td>
                        <td class="amt" style="color:#CBD5E1; font-weight:600;">{{ number_format($m->computed_opening, 2) }} <small style="color:#94A3B8;">{{ $m->unit }}</small></td>
                        <td class="amt" style="color:#38BDF8 !important; font-weight:700;">+{{ number_format($m->computed_inward, 2) }}</td>
                        <td class="amt" style="color:#F87171 !important; font-weight:700;">-{{ number_format($m->computed_outward, 2) }}</td>
                        <td class="amt" style="font-weight: 800; font-size:14px; color:#FFFFFF !important;">{{ number_format($m->computed_available, 2) }} <small style="color:#94A3B8;">{{ $m->unit }}</small></td>
                        <td style="text-align: center;">
                            <span class="status-badge {{ $badgeClass }}">{{ $m->stock_status }}</span>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('materials.show', $m->id) }}" class="btn-action" title="View Material Details">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <i class="fa-solid fa-boxes-stacked"></i>
                                <p>No inventory records found matching your filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($materials->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: left; color: #FFFFFF !important; font-size: 13.5px; font-weight:800;">
                            <i class="fa-solid fa-calculator" style="color:#60A5FA;margin-right:6px;"></i> Total Summary
                        </td>
                        <td class="amt" style="color: #CBD5E1 !important; font-weight:700;">{{ number_format($materials->sum('computed_opening'), 2) }}</td>
                        <td class="amt" style="color: #38BDF8 !important; font-weight:800;">+{{ number_format($materials->sum('computed_inward'), 2) }}</td>
                        <td class="amt" style="color: #F87171 !important; font-weight:800;">-{{ number_format($materials->sum('computed_outward'), 2) }}</td>
                        <td class="amt" style="color: #34D399 !important; font-weight: 800; font-size:14.5px;">{{ number_format($materials->sum('computed_available'), 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
