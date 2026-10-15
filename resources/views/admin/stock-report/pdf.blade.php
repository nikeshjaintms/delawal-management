<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Stock & Inventory Valuation Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Stock Valuation Report',
    'orientation' => 'landscape',
    'backUrl' => route('stock-report.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Material Stock & Inventory Valuation Statement',
    'subtitle' => 'Opening Balance, Inwards, Site Outward Consumption & Closing Valuation'
])

@php
    $totalValuation = $materials->sum(fn($m) => (float)($m->computed_stock ?? 0) * (float)($m->unit_price ?? $m->rate ?? 0));
    $totalInwQty    = $materials->sum('total_inward');
    $totalOutwQty   = $materials->sum('total_outward');
    $totalClosing   = $materials->sum('computed_stock');
    $lowStockCount  = $materials->filter(fn($m) => ($m->computed_stock ?? 0) <= ($m->minimum_stock ?? 0) && ($m->minimum_stock ?? 0) > 0)->count();
@endphp

{{-- ── KPI Summary Cards ── --}}
<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Total Closing Stock Valuation</div>
        <div class="s-value">{{ formatIndianCurrency($totalValuation) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Total Materials in Catalog</div>
        <div class="s-value">{{ $materials->count() }} Materials</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Cumulative Inward Received</div>
        <div class="s-value">{{ number_format($totalInwQty, 2) }} Units</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Cumulative Outward Consumed</div>
        <div class="s-value">{{ number_format($totalOutwQty, 2) }} Units</div>
    </div>
    <div class="stat-box {{ $lowStockCount > 0 ? 's-red' : 's-green' }}">
        <div class="s-label">Reorder / Low Stock Items</div>
        <div class="s-value">{{ $lowStockCount }} Alert{{ $lowStockCount != 1 ? 's' : '' }}</div>
    </div>
</div>

<div class="section-label">
    <i class="fa-solid fa-boxes-stacked" style="color:#059669;"></i> Material Inventory Balance &amp; Asset Valuation Register
</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Material / Item Description</th>
            <th>Category</th>
            <th class="c" style="width:60px;">Unit</th>
            <th class="r" style="width:75px;">Opening</th>
            <th class="r" style="width:75px;">Inward</th>
            <th class="r" style="width:75px;">Outward</th>
            <th class="r" style="width:85px;">Closing Stock</th>
            <th class="r" style="width:85px;">Rate (₹)</th>
            <th class="r" style="width:110px;">Valuation (₹)</th>
            <th class="c" style="width:70px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($materials as $i => $m)
        @php
            $cat = $m->category->category_name ?? ($m->category->name ?? 'General');
            $cStock = (float)($m->computed_stock ?? 0);
            $rate = (float)($m->unit_price ?? $m->rate ?? 0);
            $val = $cStock * $rate;
            $isLow = $cStock <= ($m->minimum_stock ?? 0) && ($m->minimum_stock ?? 0) > 0;
            $isOut = $cStock <= 0;
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td>
                <strong>{{ $m->material_name }}</strong>
                @if(!empty($m->material_code))
                    <div style="font-size:7.5px; color:#64748B;">Code: {{ $m->material_code }}</div>
                @endif
            </td>
            <td>{{ $cat }}</td>
            <td class="c">{{ $m->unit ?: 'Units' }}</td>
            <td class="r" style="color:#64748B;">{{ number_format($m->opening_stock ?? 0, 2) }}</td>
            <td class="r" style="color:#059669;">+ {{ number_format($m->total_inward ?? 0, 2) }}</td>
            <td class="r" style="color:#DC2626;">− {{ number_format($m->total_outward ?? 0, 2) }}</td>
            <td class="r" style="font-weight:700; color:{{ $cStock > 0 ? '#0F172A' : '#DC2626' }};">
                {{ number_format($cStock, 2) }}
            </td>
            <td class="r">{{ formatIndianCurrency($rate) }}</td>
            <td class="r" style="font-weight:800; color:#059669;">
                {{ formatIndianCurrency($val) }}
            </td>
            <td class="c">
                @if($isOut)
                    <span class="badge badge-danger">Out of Stock</span>
                @elseif($isLow)
                    <span class="badge badge-warning" style="background:#FFFBEB; color:#B45309; border-color:#FDE68A;">Reorder</span>
                @else
                    <span class="badge badge-success">Adequate</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="11" class="c" style="padding:24px; color:#64748B;">No inventory items recorded in stock catalog.</td>
        </tr>
        @endforelse
    </tbody>
    @if($materials->count() > 0)
    <tfoot>
        <tr class="double-total">
            <td colspan="7" class="r">TOTAL INVENTORY ASSET VALUATION:</td>
            <td class="r" style="font-weight:800;">{{ number_format($totalClosing, 2) }}</td>
            <td></td>
            <td class="r" style="color:#059669; font-size:10px; font-weight:900;">
                {{ formatIndianCurrency($totalValuation) }}
            </td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- ── Signatures Block ── --}}
<div class="auth-block" style="margin-top:20px; page-break-inside:avoid;">
    <div class="auth-col">
        <div class="auth-line">Warehouse / Store Keeper</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Materials &amp; Procurement Manager</div>
    </div>
</div>

@include('admin.components.pdf-footer', ['note' => 'Inventory Valuation Statement • Reconciled Against Physical Stock & Goods Receipt Notes'])
</body>
</html>