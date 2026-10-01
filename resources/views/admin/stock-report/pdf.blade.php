<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock & Inventory Balance Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Stock & Inventory Balance Report',
    'orientation' => 'landscape',
    'backUrl' => route('stock-report.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Stock & Inventory Balance Report',
    'subtitle' => 'Warehouse Inward/Outward Ledger'
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Inventory Items</div>
        <div class="s-value">{{ $totalMaterials ?? ($materials ? $materials->count() : 0) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-boxes-stacked"></i> Stock Valuation &amp; Balance</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Material Name</th>
            <th>Category</th>
            <th>Unit</th>
            <th class="r">Unit Price (₹)</th>
            <th class="r">Available Stock</th>
            <th class="r">Valuation (₹)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($materials ?? [] as $i => $m)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $m->material_name }}</strong></td>
            <td>{{ $m->category->category_name ?? ($m->category->name ?? 'General') }}</td>
            <td>{{ $m->unit ?: 'Units' }}</td>
            <td class="r">₹{{ number_format($m->unit_price ?? $m->rate ?? 0, 2) }}</td>
            <td class="r" style="font-weight:700; color:#2563EB;">{{ number_format($m->current_stock ?? 0, 2) }}</td>
            <td class="r" style="font-weight:700;">₹{{ number_format(($m->current_stock ?? 0) * ($m->unit_price ?? $m->rate ?? 0), 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="c" style="padding:20px;color:#64748B;">No stock records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>