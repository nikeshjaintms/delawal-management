<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materials Directory Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Materials Directory Report',
    'orientation' => 'landscape',
    'backUrl' => route('materials.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Materials Directory Report',
    'subtitle' => 'Construction Inventory & Material Rates'
])

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Materials</div>
        <div class="s-value">{{ $totalMaterialsCount ?? $materials->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Materials</div>
        <div class="s-value">{{ $activeMaterialsCount ?? $materials->where('status', 'active')->count() }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Total Stock Qty</div>
        <div class="s-value">{{ number_format($totalStockQty ?? $materials->sum('current_stock'), 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-boxes-stacked"></i> Materials Inventory</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Material Name</th>
            <th>Category</th>
            <th>Unit</th>
            <th class="r">Standard Rate (₹)</th>
            <th class="r">Current Stock</th>
            <th>Firm / Supplier</th>
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
            <td class="r" style="font-weight:700; color:#2563EB;">{{ number_format($m->current_stock ?? 0, 2) }}</td>
            <td>{{ $m->firm->firm_name ?? 'Delawala Infra Co.' }}</td>
            <td class="c">
                @if(($m->status ?? 'active') === 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-danger">{{ ucfirst($m->status ?: 'Inactive') }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:20px;color:#64748B;">No material records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>