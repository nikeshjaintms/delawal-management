<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Valuation & Material Stock Ledger - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Inventory Valuation & Material Stock Ledger',
    'orientation' => 'landscape',
    'backUrl' => route('reports.inventory')
])

@php
    $filters = [];
    if(request('from_date') || request('to_date')) {
        $filters['Period'] = (request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'Beginning') . ' to ' . (request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'Current Date');
    }
    if(request('filter_project')) {
        $proj = \App\Models\Project::find(request('filter_project'));
        if($proj) $filters['Site / Project'] = $proj->name ?? $proj->project_name;
    }
    if(request('filter_supplier')) {
        $filters['Supplier'] = request('filter_supplier');
    }
    if(request('filter_status')) {
        $filters['Stock Status'] = ucwords(str_replace('_', ' ', request('filter_status')));
    }

    $totOpening = 0;
    $totInward = 0;
    $totOutward = 0;
    $totClosing = 0;
    $totValuation = 0;

    foreach($materials as $m) {
        $op = (float)($m->computed_opening ?? $m->opening_stock ?? 0);
        $in = (float)($m->computed_inward ?? 0);
        $out = (float)($m->computed_outward ?? 0);
        $avail = (float)($m->computed_available ?? ($op + $in - $out));
        $rate = (float)($m->unit_price ?? $m->rate ?? 0);
        $val = $avail * $rate;

        $totOpening += $op;
        $totInward += $in;
        $totOutward += $out;
        $totClosing += $avail;
        $totValuation += $val;
    }
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Material Stock Valuation & Inventory Movement Register',
    'subtitle' => 'Perpetual Inventory Tracking, Consumption & Material Asset Valuation',
    'financialYear' => (request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('M Y') : 'FY ' . (date('m') >= 4 ? date('Y').'-'.(date('y')+1) : (date('Y')-1).'-'.date('y'))) . (request('to_date') ? ' - ' . \Carbon\Carbon::parse(request('to_date'))->format('M Y') : ''),
    'period' => (request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'All Recorded Batches') . ' to ' . (request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'Present'),
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total SKUs Tracked</div>
        <div class="s-value">{{ $totalMaterials ?? $materials->count() }}</div>
    </div>
    <div class="stat-box s-cyan">
        <div class="s-label">Inward Receipts</div>
        <div class="s-value">{{ number_format($totInward, 2) }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Outward Dispatched</div>
        <div class="s-value">{{ number_format($totOutward, 2) }}</div>
    </div>
    <div class="stat-box s-emerald">
        <div class="s-label">Closing Physical Stock</div>
        <div class="s-value">{{ number_format($totClosing, 2) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Total Stock Asset Value</div>
        <div class="s-value">{{ formatIndianCurrency($totValuation) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-boxes-stacked"></i> Material Inventory Ledger & Valuation Summary</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Item / Material Specification</th>
            <th>Category</th>
            <th>Unit</th>
            <th class="r">Opening</th>
            <th class="r">Inward (+)</th>
            <th class="r">Outward (-)</th>
            <th class="r">Closing Stock</th>
            <th class="r">Unit Rate (₹)</th>
            <th class="r">Valuation (₹)</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($materials as $i => $m)
        @php
            $op = (float)($m->computed_opening ?? $m->opening_stock ?? 0);
            $in = (float)($m->computed_inward ?? 0);
            $out = (float)($m->computed_outward ?? 0);
            $avail = (float)($m->computed_available ?? ($op + $in - $out));
            $rate = (float)($m->unit_price ?? $m->rate ?? 0);
            $val = $avail * $rate;
            $status = $m->stock_status ?? ($avail <= 0 ? 'Out of Stock' : ($avail <= ($m->minimum_stock ?? 5) ? 'Low Stock' : 'In Stock'));
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i + 1 }}</td>
            <td>
                <strong>{{ $m->material_name }}</strong>
                @if(!empty($m->material_code))
                    <div style="font-size:8px; color:#64748B;">CODE: {{ $m->material_code }}</div>
                @endif
            </td>
            <td>{{ $m->category->category_name ?? ($m->category->name ?? 'General') }}</td>
            <td style="color:#475569;">{{ $m->unit ?: 'Units' }}</td>
            <td class="r" style="color:#64748B;">{{ number_format($op, 2) }}</td>
            <td class="r" style="color:#059669; font-weight:600;">{{ $in > 0 ? '+'.number_format($in, 2) : '-' }}</td>
            <td class="r" style="color:#DC2626;">{{ $out > 0 ? '-'.number_format($out, 2) : '-' }}</td>
            <td class="r" style="font-weight:700; color:#1E3A8A;">{{ number_format($avail, 2) }}</td>
            <td class="r">{{ formatIndianCurrency($rate, 2, false) }}</td>
            <td class="r" style="font-weight:700; color:#0F172A;">{{ formatIndianCurrency($val) }}</td>
            <td class="c">
                @if($status === 'Out of Stock')
                    <span class="badge badge-danger">Out of Stock</span>
                @elseif($status === 'Low Stock')
                    <span class="badge badge-warning">Low Stock</span>
                @else
                    <span class="badge badge-success">Adequate</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="11" class="c" style="padding:24px; color:#64748B;">
                <em>No material inventory records found matching the specified parameters.</em>
            </td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="4" class="r"><strong>CONSOLIDATED INVENTORY VALUATION:</strong></td>
            <td class="r"><strong>{{ number_format($totOpening, 2) }}</strong></td>
            <td class="r" style="color:#059669;"><strong>+{{ number_format($totInward, 2) }}</strong></td>
            <td class="r" style="color:#DC2626;"><strong>-{{ number_format($totOutward, 2) }}</strong></td>
            <td class="r" style="color:#1E3A8A;"><strong>{{ number_format($totClosing, 2) }}</strong></td>
            <td class="r" style="color:#64748B;">-</td>
            <td class="r" style="color:#0F172A; font-size:11px;"><strong>{{ formatIndianCurrency($totValuation) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>

<div style="margin-top:12px; font-size:9px; color:#64748B; line-height:1.4;">
    <strong>Accounting Valuation Method:</strong> Material inventory is valued at Standard Weighted Acquisition Cost (FIFO equivalent). Closing inventory valuation is included under Current Assets in the Balance Sheet.
</div>

@include('admin.components.pdf-footer')
</body>
</html>