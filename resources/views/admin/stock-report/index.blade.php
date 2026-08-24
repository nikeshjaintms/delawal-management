@extends('admin.layouts.app')
@section('title','Current Stock Report')
@section('page-title','Inventory Management')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }

.header-actions { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }

.btn-export-pdf {
    background: rgba(239, 68, 68, 0.15) !important; color: #F87171 !important;
    padding: 9px 16px; border-radius: 10px; border: 1px solid rgba(239, 68, 68, 0.30) !important;
    font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none !important;
    display: inline-flex; align-items: center; gap: 8px; flex-shrink: 0; white-space: nowrap !important;
    transition: all .25s ease;
}
.btn-export-pdf:hover { background: #DC2626 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(220,38,38,0.40); }

.btn-excel {
    background: rgba(16, 185, 129, 0.15) !important; color: #34D399 !important;
    padding: 9px 16px; border-radius: 10px; border: 1px solid rgba(52, 211, 153, 0.30) !important;
    font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none !important;
    display: inline-flex; align-items: center; gap: 8px; flex-shrink: 0; white-space: nowrap !important;
    transition: all .25s ease;
}
.btn-excel:hover { background: #059669 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(5,150,105,0.40); }

.stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
.stat-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 20px !important;
    display: flex; flex-direction: column; gap: 6px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
}
.stat-card .stat-label { font-size: 11.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #94A3B8 !important; }
.stat-card .stat-value { font-size: 28px; font-weight: 800; color: #FFFFFF !important; }
.stat-card.stat-warning .stat-value { color: #F87171 !important; }
.stat-card.stat-success .stat-value { color: #34D399 !important; }
.stat-card .stat-icon { font-size: 22px; margin-bottom: 2px; }
.stat-card.stat-warning .stat-icon { color: #F87171 !important; }
.stat-card.stat-success .stat-icon { color: #34D399 !important; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
}

.filter-bar {
    display: flex; gap: 12px; align-items: flex-end; margin-bottom: 22px;
    background: rgba(255, 255, 255, 0.04); padding: 14px 18px;
    border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10);
    flex-wrap: wrap;
}
.filter-group { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 140px; }
.filter-group.search-group { flex: 1.4; min-width: 180px; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; }

.filter-control, .search-input {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box;
}
.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus, .filter-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 18px;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    white-space: nowrap; height: 42px; display: inline-flex; align-items: center; gap: 6px;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset {
    color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 700;
    padding: 10px 14px; white-space: nowrap; height: 42px; display: inline-flex; align-items: center; gap: 6px;
    transition: color .2s ease;
}
.btn-reset:hover { color: #FFFFFF !important; }

.table-responsive-wrapper {
    width: 100%; overflow-x: auto; border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.10); background: rgba(16, 22, 34, 0.70);
}

.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 13px 14px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap;
}
.premium-table td {
    padding: 13px 14px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #FFFFFF !important; font-weight: 600; vertical-align: middle;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.04) !important; }

.badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; font-size: 11px; font-weight: 800; border-radius: 20px; text-transform: uppercase; white-space: nowrap; }
.badge-available { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-lowstock { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-unit { background: rgba(255, 255, 255, 0.08); color: #CBD5E1; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.12); }
.cat-pill { display: inline-flex; align-items: center; gap: 4px; background: rgba(59, 130, 246, 0.15); color: #60A5FA; font-size: 12px; font-weight: 700; border-radius: 6px; padding: 3px 8px; border: 1px solid rgba(59, 130, 246, 0.30); }

.qty-col { font-weight: 800; font-size: 14px; }
.num-col { color: #CBD5E1 !important; font-size: 13.5px; font-weight: 600; font-family: monospace; }

.empty-state { padding: 48px 20px; text-align: center; color: #94A3B8; font-weight: 600; }
.empty-state i { font-size: 36px; margin-bottom: 12px; opacity: 0.40; color: #60A5FA; }
.empty-state p { font-size: 14.5px; color: #CBD5E1; margin: 0; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Current Stock Report</h2>
        <p>Live stock levels with low stock alerts across all materials.</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('stock-report.pdf', request()->query()) }}" class="btn-export-pdf" target="_blank">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('stock-report.excel', request()->query()) }}" class="btn-excel">
            <i class="fa-solid fa-file-csv"></i> Export Excel
        </a>
    </div>
</div>

{{-- Summary Cards --}}
@php
    $totalMaterials = $materials->count();
    $lowStockItems  = $materials->filter(fn($m) => $m->computed_stock <= $m->minimum_stock && $m->minimum_stock > 0)->count();
    $availableItems = $totalMaterials - $lowStockItems;
@endphp
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon" style="color:#60A5FA;"><i class="fa-solid fa-boxes-stacked"></i></div>
        <div class="stat-label">Total Materials</div>
        <div class="stat-value">{{ $totalMaterials }}</div>
    </div>
    <div class="stat-card stat-success">
        <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
        <div class="stat-label">Available</div>
        <div class="stat-value">{{ $availableItems }}</div>
    </div>
    <div class="stat-card stat-warning">
        <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="stat-label">Low Stock Alerts</div>
        <div class="stat-value">{{ $lowStockItems }}</div>
    </div>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('stock-report.index') }}" class="filter-bar" id="stockFilterForm">
        @if(auth()->user() && auth()->user()->isAdmin())
            <div class="filter-group">
                <span class="filter-label">Firm</span>
                <select name="firm_id" class="filter-control" onchange="this.form.submit()">
                    <option value="">All Firms</option>
                    @foreach(\App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get() as $firm)
                        <option value="{{ $firm->id }}" {{ request('firm_id') == $firm->id ? 'selected' : '' }}>
                            {{ $firm->firm_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="filter-group search-group">
            <span class="filter-label">Search Material</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Type material name...">
        </div>

        <div class="filter-group">
            <span class="filter-label">Category</span>
            <select name="filter_category" class="filter-control" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('filter_category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->category_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Project</span>
            <select name="filter_project" class="filter-control" onchange="this.form.submit()">
                <option value="">All Projects</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('filter_project') == $p->id ? 'selected' : '' }}>
                        {{ $p->project_name }} ({{ $p->propertyMaster->property_name ?? 'Property' }})
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','filter_category','filter_project','firm_id']))
            <a href="{{ route('stock-report.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>
        @endif
    </form>

    <div class="table-responsive-wrapper">
        <table class="premium-table">
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <th>Firm</th>
                    @endif
                    <th>Material Name</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th style="text-align:right;">Opening</th>
                    <th style="text-align:right;">Total Inward</th>
                    <th style="text-align:right;">Total Outward</th>
                    <th style="text-align:right;">Current Stock</th>
                    <th style="text-align:right;">Min Stock</th>
                    <th style="text-align:center;">Stock Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $index => $m)
                @php
                    $isLow = $m->computed_stock <= $m->minimum_stock && $m->minimum_stock > 0;
                @endphp
                <tr @if($isLow) style="background:rgba(239,68,68,0.06);" @endif>
                    <td>{{ $index + 1 }}</td>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <td><strong style="color:#FFFFFF;">{{ $m->firm->firm_name ?? 'N/A' }}</strong></td>
                    @endif
                    <td>
                        <strong style="color:#FFFFFF !important; font-weight:700; font-size:14px;">{{ $m->material_name }}</strong>
                        @if($isLow)
                            <i class="fa-solid fa-triangle-exclamation" style="color:#F87171;font-size:11px;margin-left:5px;" title="Low Stock"></i>
                        @endif
                    </td>
                    <td>
                        @if($m->materialCategory)
                            <span class="cat-pill">{{ $m->materialCategory->category_name }}</span>
                        @else
                            <span style="color:#94A3B8">—</span>
                        @endif
                    </td>
                    <td><span class="badge-unit">{{ $m->unit ?? '—' }}</span></td>
                    <td class="num-col" style="text-align:right;">{{ number_format($m->opening_stock, 2) }}</td>
                    <td class="num-col" style="text-align:right;color:#34D399 !important;">+{{ number_format($m->total_inward, 2) }}</td>
                    <td class="num-col" style="text-align:right;color:#F87171 !important;">-{{ number_format($m->total_outward, 2) }}</td>
                    <td style="text-align:right;">
                        <span class="qty-col" style="@if($isLow) color:#F87171; @else color:#34D399; @endif">
                            {{ number_format($m->computed_stock, 2) }}
                        </span>
                    </td>
                    <td class="num-col" style="text-align:right;">{{ number_format($m->minimum_stock, 2) }}</td>
                    <td style="text-align:center;">
                        @if($isLow)
                            <span class="badge badge-lowstock"><i class="fa-solid fa-triangle-exclamation" style="font-size:9px;"></i> Low Stock</span>
                        @else
                            <span class="badge badge-available"><i class="fa-solid fa-circle-check" style="font-size:9px;"></i> Available</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ (auth()->user() && auth()->user()->isAdmin()) ? 11 : 10 }}">
                        <div class="empty-state">
                            <i class="fa-solid fa-boxes-stacked"></i>
                            <p>No materials found for this filter. Try selecting 'All Categories' or resetting.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
