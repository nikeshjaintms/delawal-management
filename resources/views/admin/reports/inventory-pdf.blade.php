<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Valuation & Stock Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Inventory Valuation & Stock Report',
    'orientation' => 'landscape',
    'backUrl' => route('reports.inventory')
])

@include('admin.components.pdf-header', [
    'title' => 'Inventory Valuation & Stock Report',
    'subtitle' => 'Material Stocks & Site Consumption Ledger'
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Materials</div>
        <div class="s-value">{{ $totalMaterials ?? $materials->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Total Stock Quantity</div>
        <div class="s-value">{{ number_format($totalStockQty ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Low Stock Items</div>
        <div class="s-value">{{ $lowStockItems ?? 0 }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Out of Stock Items</div>
        <div class="s-value">{{ $outOfStockItems ?? 0 }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-warehouse"></i> Warehouse Material Stock Valuation</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Material Name</th>
            <th>Category</th>
            <th>Unit</th>
            <th class="r">Standard Rate (₹)</th>
            <th class="r">Available Stock</th>
            <th class="r">Stock Valuation (₹)</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($materials as $i => $m)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $m->material_name }}</strong></td>
            <td>{{ $m->category->category_name ?? ($m->category->name ?? 'General') }}</td>
            <td>{{ $m->unit ?: 'Units' }}</td>
            <td class="r">₹{{ number_format($m->unit_price ?? $m->rate ?? 0, 2) }}</td>
            <td class="r" style="color:#2563EB; font-weight:700;">{{ number_format($m->current_stock ?? 0, 2) }}</td>
            <td class="r" style="font-weight:700;">₹{{ number_format(($m->current_stock ?? 0) * ($m->unit_price ?? $m->rate ?? 0), 2) }}</td>
            <td class="c">
                @if(($m->current_stock ?? 0) <= 0)
                    <span class="badge badge-danger">Out of Stock</span>
                @elseif(($m->current_stock ?? 0) <= ($m->min_stock ?? 10))
                    <span class="badge badge-warning">Low Stock</span>
                @else
                    <span class="badge badge-success">Adequate</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:20px;color:#64748B;">No materials found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>