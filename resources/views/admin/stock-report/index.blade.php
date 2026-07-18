@extends('admin.layouts.app')
@section('title','Current Stock Report')
@section('page-title','Inventory Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:10px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{padding:10px 18px;border:1px solid var(--border-color);border-radius:8px;text-decoration:none;font-size:13.5px;font-weight:600;display:inline-flex;align-items:center;gap:8px;color:var(--text-secondary);background:#FFF;cursor:pointer;font-family:var(--font-primary);transition:var(--transition);}
    .btn-outline:hover{border-color:var(--gold);color:var(--gold);}
    .btn-excel{padding:10px 18px;border:1px solid #16803D;border-radius:8px;text-decoration:none;font-size:13.5px;font-weight:600;display:inline-flex;align-items:center;gap:8px;color:#16803D;background:rgba(34,197,94,0.05);cursor:pointer;font-family:var(--font-primary);transition:var(--transition);}
    .btn-excel:hover{background:rgba(34,197,94,0.12);color:#14532D;}
    .header-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:24px;box-shadow:var(--soft-shadow);}
    .filter-bar{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;align-items:flex-end;}
    .filter-group{display:flex;flex-direction:column;gap:5px;}
    .filter-label{font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.6px;}
    .filter-control{padding:9px 12px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);outline:none;background:#FFF;transition:var(--transition);min-width:170px;}
    .filter-control:focus{border-color:var(--gold);}
    .search-input{padding:9px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);outline:none;transition:var(--transition);min-width:220px;}
    .search-input:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .btn-search{background-color:var(--text-primary);color:#FFF;padding:9px 16px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:var(--font-primary);align-self:flex-end;}
    .btn-reset{padding:9px 12px;color:var(--text-secondary);text-decoration:none;font-size:13px;align-self:flex-end;}
    .table-container{width:100%;overflow-x:auto;}
    .premium-table{width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;}
    .premium-table th{padding:13px 14px;background:#F9FAFB;color:var(--text-secondary);font-weight:600;border-bottom:1px solid var(--border-color);font-size:11.5px;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;}
    .premium-table td{padding:14px;border-bottom:1px solid #F1F5F9;color:var(--text-primary);vertical-align:middle;}
    .premium-table tr:last-child td{border-bottom:none;}
    .premium-table tbody tr:hover{background-color:#F9FAFB;}
    .badge{display:inline-block;padding:4px 10px;border-radius:6px;font-size:11.5px;font-weight:700;letter-spacing:0.3px;}
    .badge-available{background:rgba(34,197,94,0.1);color:#16803D;}
    .badge-lowstock{background:rgba(239,68,68,0.1);color:#DC2626;}
    .stat-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;}
    .stat-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:20px;display:flex;flex-direction:column;gap:6px;box-shadow:var(--soft-shadow);}
    .stat-card .stat-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-secondary);}
    .stat-card .stat-value{font-size:28px;font-weight:800;color:var(--text-primary);}
    .stat-card.stat-warning .stat-value{color:#DC2626;}
    .stat-card .stat-icon{font-size:22px;margin-bottom:2px;}
    .stat-card.stat-warning .stat-icon{color:#DC2626;}
    .stat-card.stat-success .stat-icon{color:#16803D;}
    .qty-col{font-weight:700;}
    .num-col{color:var(--text-secondary);font-size:13px;}
    .empty-state{padding:48px 20px;text-align:center;color:var(--text-secondary);}
    .empty-state i{font-size:36px;margin-bottom:12px;opacity:0.35;}
    .empty-state p{font-size:14px;}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Current Stock Report</h2>
        <p>Live stock levels with low stock alerts across all materials.</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('stock-report.pdf', request()->query()) }}" class="btn-outline" target="_blank">
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
        <div class="stat-icon" style="color:var(--gold);"><i class="fa-solid fa-boxes-stacked"></i></div>
        <div class="stat-label">Total Materials</div>
        <div class="stat-value">{{ $totalMaterials }}</div>
    </div>
    <div class="stat-card stat-success">
        <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
        <div class="stat-label">Available</div>
        <div class="stat-value" style="color:#16803D;">{{ $availableItems }}</div>
    </div>
    <div class="stat-card stat-warning">
        <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="stat-label">Low Stock Alerts</div>
        <div class="stat-value">{{ $lowStockItems }}</div>
    </div>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('stock-report.index') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Search Material</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input @error('search') is-invalid @enderror" placeholder="Type material name...">
        </div>
        <div class="filter-group">
            <span class="filter-label">Category</span>
            <select name="filter_category" class="filter-control @error('filter_category') is-invalid @enderror">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('filter_category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->category_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','filter_category']))
            <a href="{{ route('stock-report.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Material Name</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th style="text-align:right;">Opening Stock</th>
                    <th style="text-align:right;">Total Inward</th>
                    <th style="text-align:right;">Total Outward</th>
                    <th style="text-align:right;">Current Stock</th>
                    <th style="text-align:right;">Minimum Stock</th>
                    <th style="text-align:center;">Stock Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $index => $m)
                @php
                    $isLow = $m->computed_stock <= $m->minimum_stock && $m->minimum_stock > 0;
                @endphp
                <tr @if($isLow) style="background:rgba(239,68,68,0.025);" @endif>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $m->material_name }}</strong>
                        @if($isLow)
                            <i class="fa-solid fa-triangle-exclamation" style="color:#DC2626;font-size:11px;margin-left:5px;" title="Low Stock"></i>
                        @endif
                    </td>
                    <td>{{ $m->materialCategory->category_name ?? '—' }}</td>
                    <td><span style="background:#F1F5F9;padding:2px 8px;border-radius:5px;font-size:12px;font-weight:600;color:var(--text-secondary);">{{ $m->unit ?? '—' }}</span></td>
                    <td class="num-col" style="text-align:right;">{{ number_format($m->opening_stock, 3) }}</td>
                    <td class="num-col" style="text-align:right;color:#16803D;">+{{ number_format($m->total_inward, 3) }}</td>
                    <td class="num-col" style="text-align:right;color:#DC2626;">-{{ number_format($m->total_outward, 3) }}</td>
                    <td style="text-align:right;">
                        <span class="qty-col" style="@if($isLow) color:#DC2626; @else color:var(--text-primary); @endif">
                            {{ number_format($m->computed_stock, 3) }}
                        </span>
                    </td>
                    <td class="num-col" style="text-align:right;">{{ number_format($m->minimum_stock, 3) }}</td>
                    <td style="text-align:center;">
                        @if($isLow)
                            <span class="badge badge-lowstock"><i class="fa-solid fa-triangle-exclamation" style="font-size:10px;"></i> Low Stock</span>
                        @else
                            <span class="badge badge-available"><i class="fa-solid fa-circle-check" style="font-size:10px;"></i> Available</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="empty-state">
                            <i class="fa-solid fa-boxes-stacked"></i>
                            <p>No materials found. Try adjusting your filters.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($materials->count() > 0)
    <div style="margin-top:18px;padding-top:16px;border-top:1px solid var(--border-color);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
        <span style="font-size:12.5px;color:var(--text-secondary);">
            Showing <strong>{{ $materials->count() }}</strong> material{{ $materials->count() != 1 ? 's' : '' }}
            @if($lowStockCount > 0)
                &nbsp;•&nbsp;<span style="color:#DC2626;font-weight:600;"><i class="fa-solid fa-triangle-exclamation"></i> {{ $lowStockCount }} low stock alert{{ $lowStockCount != 1 ? 's' : '' }}</span>
            @endif
        </span>
        <span style="font-size:12px;color:var(--text-secondary);">
            <i class="fa-regular fa-clock"></i> Generated on {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>
    @endif
</div>
@endsection

