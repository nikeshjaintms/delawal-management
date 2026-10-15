<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materials Directory & Standard Pricing Ledger - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Materials Directory & Standard Pricing Ledger',
    'orientation' => 'landscape',
    'backUrl' => route('materials.index')
])

@php
    $totQty = 0;
    $totVal = 0;
    foreach($materials as $m) {
        $stk = (float)($m->current_stock ?? 0);
        $rate = (float)($m->unit_price ?? $m->rate ?? 0);
        $totQty += $stk;
        $totVal += ($stk * $rate);
    }
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Materials Directory & Standard Pricing Register',
    'subtitle' => 'Construction Materials, Standard Rates & Physical Stock Valuation'
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Material SKUs</div>
        <div class="s-value">{{ $totalMaterialsCount ?? $materials->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Approved Items</div>
        <div class="s-value">{{ $activeMaterialsCount ?? $materials->where('status', 'active')->count() }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Total Stock Quantity</div>
        <div class="s-value">{{ number_format($totQty, 2) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Total Stock Asset Value</div>
        <div class="s-value">{{ formatIndianCurrency($totVal) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-boxes-stacked"></i> Materials Master Schedule &amp; Valuation</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Item / Material Description</th>
            <th>Material Category</th>
            <th style="width:75px;">Unit</th>
            <th class="r" style="width:115px;">Standard Rate (₹)</th>
            <th class="r" style="width:110px;">Current Stock</th>
            <th class="r" style="width:125px;">Stock Asset Value (₹)</th>
            <th>Firm / Warehouse Link</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($materials as $i => $m)
        @php
            $stk = (float)($m->current_stock ?? 0);
            $rate = (float)($m->unit_price ?? $m->rate ?? 0);
            $val = $stk * $rate;
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td>
                <strong>{{ $m->material_name }}</strong>
                @if(!empty($m->material_code))
                    <div style="font-size:8px; color:#64748B;">Code: {{ $m->material_code }}</div>
                @endif
            </td>
            <td>{{ $m->category->category_name ?? ($m->category->name ?? 'General') }}</td>
            <td style="color:#475569;">{{ $m->unit ?: 'Units' }}</td>
            <td class="r" style="color:#1E3A8A; font-weight:600;">{{ formatIndianCurrency($rate, 2, false) }}</td>
            <td class="r" style="font-weight:700; color:#059669;">{{ number_format($stk, 2) }}</td>
            <td class="r" style="font-weight:700; color:#0F172A;">{{ formatIndianCurrency($val, 2, false) }}</td>
            <td>{{ $m->firm->firm_name ?? 'Delawala Infrastructure' }}</td>
            <td class="c">
                @if(($m->status ?? 'active') === 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-neutral">{{ ucfirst($m->status ?: 'Inactive') }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:24px; color:#64748B;"><em>No material records found.</em></td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="5" class="r"><strong>CONSOLIDATED INVENTORY TOTALS:</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ number_format($totQty, 2) }}</strong></td>
            <td class="r" style="color:#0F172A; font-size:11px;"><strong>{{ formatIndianCurrency($totVal) }}</strong></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

@include('admin.components.pdf-footer')
</body>
</html>