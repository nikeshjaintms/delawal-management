<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Specification & Inventory Dossier - {{ $material->material_name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Material Specification: ' . $material->material_name,
    'orientation' => 'portrait',
    'backUrl' => isset($material->id) ? route('materials.show', $material->id) : route('materials.index')
])

@php
    $stk = (float)($material->current_stock ?? 0);
    $rate = (float)($material->unit_price ?? $material->rate ?? 0);
    $totVal = $stk * $rate;
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Material Master Specification Sheet',
    'subtitle' => 'Inventory Rate Card, Specification & Asset Valuation',
    'firm' => $material->firm ?? null,
    'docRef' => 'MAT-' . str_pad($material->id ?? 1, 5, '0', STR_PAD_LEFT)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Material SKU</div>
        <div class="s-value" style="font-size:13px;">{{ $material->material_name }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Current Stock Balance</div>
        <div class="s-value" style="font-size:12px;">{{ number_format($stk, 2) }} {{ $material->unit ?: 'Units' }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Standard Unit Rate</div>
        <div class="s-value" style="font-size:12px;">{{ formatIndianCurrency($rate) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Total Asset Value</div>
        <div class="s-value" style="font-size:12px;">{{ formatIndianCurrency($totVal) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-boxes-stacked"></i> Material Specification</div>
        <div class="info-row"><span class="info-label">Material Description:</span><span class="info-val"><strong>{{ $material->material_name }}</strong></span></div>
        <div class="info-row"><span class="info-label">Category / Classification:</span><span class="info-val">{{ $material->category->category_name ?? ($material->category->name ?? 'General') }}</span></div>
        <div class="info-row"><span class="info-label">Standard Unit of Measure:</span><span class="info-val">{{ $material->unit ?: 'Units' }}</span></div>
        <div class="info-row"><span class="info-label">HSN / SAC Code:</span><span class="info-val">{{ $material->hsn_code ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Material Item Code:</span><span class="info-val">{{ $material->material_code ?: '—' }}</span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-warehouse"></i> Inventory Thresholds &amp; Firm</div>
        <div class="info-row"><span class="info-label">Affiliated Firm:</span><span class="info-val">{{ $material->firm->firm_name ?? 'Delawala Infrastructure' }}</span></div>
        <div class="info-row"><span class="info-label">Reorder Level (Min):</span><span class="info-val">{{ $material->minimum_stock ?? ($material->min_stock ?? 10) }} {{ $material->unit ?: 'Units' }}</span></div>
        <div class="info-row"><span class="info-label">Inventory Health:</span><span class="info-val">
            @if($stk <= 0)
                <span class="badge badge-danger">Out of Stock</span>
            @elseif($stk <= ($material->minimum_stock ?? 5))
                <span class="badge badge-warning">Low Stock</span>
            @else
                <span class="badge badge-success">Adequate</span>
            @endif
        </span></div>
        <div class="info-row"><span class="info-label">Master Status:</span><span class="info-val"><span class="badge badge-success">{{ strtoupper($material->status ?: 'ACTIVE') }}</span></span></div>
    </div>
</div>

<div style="margin-top:14px; font-size:9px; color:#64748B; line-height:1.4;">
    <strong>Material Valuation Note:</strong> Physical stock is valued under Current Assets in the Balance Sheet at standard replacement / weighted cost.
</div>

@include('admin.components.pdf-footer')
</body>
</html>