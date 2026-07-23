@extends('admin.layouts.app')
@section('title', 'Inventory Report')
@section('page-title', 'Reports')
@section('content')
<style>
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
    .btn-action, .btn-filter, .btn-reset,
    .empty-state a { display: none !important; }

    /* ── Full-width layout ── */
    .main-content { margin-left: 0 !important; }
    .content-body  { padding: 6px 0 0 !important; }
    body           { background: #fff !important; }

    /* ── Strip decorative chrome ── */
    .gst-stat-card, .card-box {
        box-shadow: none !important;
        border: 1px solid #E2E8F0 !important;
    }

    /* ── Force 4-column stat grid ── */
    .gst-stat-grid {
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 10px !important;
    }

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
        <h2><i class="fa-solid fa-boxes-stacked" style="color:#3B82F6;margin-right:9px;"></i>Inventory Report</h2>
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
        <div class="sc-value">{{ $totalMaterials }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-green"><i class="fa-solid fa-warehouse"></i></div>
        <div class="sc-label">Total Stock Quantity</div>
        <div class="sc-value" style="color:#10B981;">{{ number_format($totalStockQty, 2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-amber"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="sc-label">Low Stock Items</div>
        <div class="sc-value" style="color:#F59E0B;">{{ $lowStockItems }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-red"><i class="fa-solid fa-circle-xmark"></i></div>
        <div class="sc-label">Out of Stock Items</div>
        <div class="sc-value" style="color:#EF4444;">{{ $outOfStockItems }}</div>
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
    <div style="font-size:13.5px;font-weight:700;color:#0F172A;margin-bottom:16px;">
        <i class="fa-solid fa-list" style="color:#3B82F6;margin-right:7px;"></i>
        Inventory Stock Records <span style="font-size:12px;font-weight:500;color:#64748B;margin-left:8px;">Showing {{ $materials->count() }} items</span>
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
                        <td style="color:#94A3B8; font-size:12px;">{{ $index + 1 }}</td>
                        <td>{{ $m->latest_date }}</td>
                        <td style="font-weight: 600; color: #0F172A;">
                            {{ $m->material_name }}
                        </td>
                        <td>{{ $m->materialCategory?->category_name ?? '—' }}</td>
                        <td class="amt" style="color:#64748B;">{{ number_format($m->computed_opening, 2) }} <small>{{ $m->unit }}</small></td>
                        <td class="amt" style="color:#0EA5E9;">+{{ number_format($m->computed_inward, 2) }}</td>
                        <td class="amt" style="color:#EF4444;">-{{ number_format($m->computed_outward, 2) }}</td>
                        <td class="amt" style="font-weight: 700; color:#0F172A;">{{ number_format($m->computed_available, 2) }} <small>{{ $m->unit }}</small></td>
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
                        <td colspan="4" style="text-align: left;">Total Summary</td>
                        <td class="amt">{{ number_format($materials->sum('computed_opening'), 2) }}</td>
                        <td class="amt" style="color:#0EA5E9;">+{{ number_format($materials->sum('computed_inward'), 2) }}</td>
                        <td class="amt" style="color:#EF4444;">-{{ number_format($materials->sum('computed_outward'), 2) }}</td>
                        <td class="amt" style="color:#10B981; font-weight: 800;">{{ number_format($materials->sum('computed_available'), 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
