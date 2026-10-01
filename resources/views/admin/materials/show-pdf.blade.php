<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Dossier - {{ $material->material_name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Material Specification: ' . $material->material_name,
    'orientation' => 'portrait',
    'backUrl' => isset($material->id) ? route('materials.show', $material->id) : route('materials.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Material Master Specification Sheet',
    'subtitle' => 'Inventory Rate Card & Stock Ledger',
    'firm' => $material->firm ?? null,
    'docRef' => 'MAT-' . ($material->id ?? 1)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Material Name</div>
        <div class="s-value" style="font-size:14px;">{{ $material->material_name }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Current Stock</div>
        <div class="s-value" style="font-size:13px;">{{ number_format($material->current_stock ?? 0, 2) }} {{ $material->unit ?: 'Units' }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Unit Price</div>
        <div class="s-value" style="font-size:13px;">₹{{ number_format($material->unit_price ?? $material->rate ?? 0, 2) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-boxes-stacked"></i> Material Details</div>
        <div class="info-row"><span class="info-label">Name:</span><span class="info-val">{{ $material->material_name }}</span></div>
        <div class="info-row"><span class="info-label">Category:</span><span class="info-val">{{ $material->category->category_name ?? ($material->category->name ?? 'General') }}</span></div>
        <div class="info-row"><span class="info-label">Unit of Measure:</span><span class="info-val">{{ $material->unit ?: 'Units' }}</span></div>
        <div class="info-row"><span class="info-label">HSN/SAC Code:</span><span class="info-val">{{ $material->hsn_code ?: '—' }}</span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building"></i> Stock &amp; Firm Info</div>
        <div class="info-row"><span class="info-label">Firm:</span><span class="info-val">{{ $material->firm->firm_name ?? 'Delawala Infra Co.' }}</span></div>
        <div class="info-row"><span class="info-label">Minimum Reorder:</span><span class="info-val">{{ $material->min_stock ?? 10 }} {{ $material->unit ?: 'Units' }}</span></div>
        <div class="info-row"><span class="info-label">Status:</span><span class="info-val"><span class="badge badge-success">{{ ucfirst($material->status ?? 'Active') }}</span></span></div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>