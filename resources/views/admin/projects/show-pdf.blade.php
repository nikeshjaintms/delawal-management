<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Master Dossier - {{ $project->project_name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Project Dossier: ' . $project->project_name,
    'orientation' => 'landscape',
    'backUrl' => route('projects.show', $project->id)
])

@php
    $totVal = (float)($totalValue ?? 0);
    $totExp = (float)($totalExpenses ?? 0);
    $totContr = (float)($contractorPaymentsTotal ?? 0);
    $grandCost = (float)($grandTotalProjectCost ?? ($totExp + $totContr));
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Project Master Dossier & Site Financial Costing Sheet',
    'subtitle' => 'Development Scheme Inventory, Real Estate Valuation & Infrastructure Costing',
    'firm' => $project->firm ?? null,
    'docRef' => $project->project_code ?: ('PROJ-' . $project->id)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Project Scheme</div>
        <div class="s-value" style="font-size:13px; color:#1E3A8A;">{{ $project->project_name }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Inventory Status</div>
        <div class="s-value" style="font-size:12px;">{{ $availablePlots ?? 0 }} Avail / {{ $totalPlots ?? 0 }} Total</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Total Inventory Valuation</div>
        <div class="s-value" style="font-size:12px;">{{ formatIndianCurrency($totVal) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total Site Cost Outlay</div>
        <div class="s-value" style="font-size:12px;">{{ formatIndianCurrency($grandCost) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-city"></i> Scheme Particulars</div>
        <div class="info-row"><span class="info-label">Scheme Name:</span><span class="info-val"><strong>{{ $project->project_name }}</strong></span></div>
        <div class="info-row"><span class="info-label">Scheme Code:</span><span class="info-val">{{ $project->project_code ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Development Type:</span><span class="info-val">{{ ucfirst($project->project_type ?: 'Plotted Development') }}</span></div>
        <div class="info-row"><span class="info-label">Managing Firm:</span><span class="info-val">{{ $project->firm->firm_name ?? 'Delawala Infrastructure' }}</span></div>
        <div class="info-row"><span class="info-label">Development Status:</span><span class="info-val"><span class="badge badge-success">{{ ucfirst($project->status ?: 'Active') }}</span></span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Site Location &amp; Metrics</div>
        <div class="info-row"><span class="info-label">Site Address:</span><span class="info-val">{{ $project->display_address ?: ($project->address ?: 'Dahegam Road, Bharuch') }}</span></div>
        <div class="info-row"><span class="info-label">City / Taluka:</span><span class="info-val">{{ $project->city ?: 'Dahegam, Bharuch' }}</span></div>
        <div class="info-row"><span class="info-label">Total Plotted Area:</span><span class="info-val">{{ number_format($totalArea ?? 0, 2) }} Sq.Yd / Units</span></div>
        <div class="info-row"><span class="info-label">Booked &amp; Sold Units:</span><span class="info-val" style="color:#B45309; font-weight:700;">{{ ($bookedPlots ?? 0) + ($soldPlots ?? 0) }} Units</span></div>
        <div class="info-row"><span class="info-label">Available for Allotment:</span><span class="info-val" style="color:#059669; font-weight:700;">{{ $availablePlots ?? 0 }} Units</span></div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-coins"></i> Project Capital Expenditure &amp; Cost Breakdown</div>
<table>
    <thead>
        <tr>
            <th>Cost Center / Expenditure Head</th>
            <th>Description &amp; Scope</th>
            <th class="r" style="width: 150px;">Total Outflow (₹)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Direct Site Expenses &amp; Petty Cash</strong></td>
            <td>On-site operational expenses, equipment hire, utilities, and daily site maintenance</td>
            <td class="r" style="color:#1E3A8A; font-weight:700;">{{ formatIndianCurrency($directExpensesTotal ?? 0, 2, false) }}</td>
        </tr>
        <tr>
            <td><strong>Material Procurements &amp; PO Deliveries</strong></td>
            <td>Raw materials (Cement, Steel, Bricks, Aggregates) billed via vendor purchase orders</td>
            <td class="r" style="color:#1E3A8A; font-weight:700;">{{ formatIndianCurrency($poExpensesTotal ?? 0, 2, false) }}</td>
        </tr>
        <tr>
            <td><strong>Contractor Labor &amp; Civil Works Settlements</strong></td>
            <td>Contractor milestone payments, civil works contracts, and structural labour</td>
            <td class="r" style="color:#1E3A8A; font-weight:700;">{{ formatIndianCurrency($totContr, 2, false) }}</td>
        </tr>
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="2" class="r"><strong>GRAND TOTAL CAPITAL EXPENDITURE (WIP COST):</strong></td>
            <td class="r" style="color:#DC2626; font-size:11px;"><strong>{{ formatIndianCurrency($grandCost) }}</strong></td>
        </tr>
    </tfoot>
</table>

@if($project->properties && $project->properties->isNotEmpty())
<div class="section-label" style="margin-top: 15px;"><i class="fa-solid fa-border-all"></i> Plotted Units &amp; Real Estate Inventory Schedule</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:85px;">Plot / Unit No</th>
            <th>Property / Unit Name</th>
            <th>Type</th>
            <th class="r" style="width:100px;">Area / Size</th>
            <th class="r" style="width:120px;">List Price (₹)</th>
            <th class="c" style="width:80px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($project->properties as $i => $plot)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong style="color:#1E3A8A;">{{ $plot->unit_no ?: ($plot->property_code ?: '—') }}</strong></td>
            <td>{{ $plot->property_name }}</td>
            <td>{{ ucfirst($plot->propertyType->name ?? ($plot->property_type ?? 'Plot')) }}</td>
            <td class="r" style="color:#475569;">{{ $plot->size ? ($plot->size . ' ' . ($plot->area_unit ?: 'Sq.Yd')) : ($plot->area ? ($plot->area . ' Sq.Ft') : '—') }}</td>
            <td class="r" style="font-weight:700; color:#0F172A;">{{ formatIndianCurrency($plot->price ?? 0, 2, false) }}</td>
            <td class="c">
                @if($plot->status === 'available')
                    <span class="badge badge-success">Available</span>
                @elseif($plot->status === 'booked')
                    <span class="badge badge-warning">Booked</span>
                @elseif($plot->status === 'sold')
                    <span class="badge badge-info">Sold</span>
                @else
                    <span class="badge badge-neutral">{{ ucfirst($plot->status) }}</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="5" class="r"><strong>TOTAL PLOTTED INVENTORY VALUATION:</strong></td>
            <td class="r" style="color:#0F172A; font-size:11px;"><strong>{{ formatIndianCurrency($totVal) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>
@endif

@include('admin.components.pdf-footer')
</body>
</html>