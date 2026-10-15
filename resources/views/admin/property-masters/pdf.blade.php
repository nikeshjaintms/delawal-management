<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Master Asset Register - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Property Master Asset Register',
    'orientation' => 'landscape',
    'backUrl' => route('property-masters.index')
])

@php
    $allProps = $propertyMasters ?? ($properties ?? collect([]));
    $totInventoryVal = 0;
    foreach($allProps as $p) {
        $totInventoryVal += (float)($p->price ?? ($p->final_price ?? 0));
    }

    $filters = [];
    if(request('project_id')) {
        $pr = \App\Models\Project::find(request('project_id'));
        if($pr) $filters['Site / Scheme'] = $pr->project_name ?? $pr->name;
    }
    if(request('property_type_id') || request('property_type')) {
        $filters['Type'] = request('property_type') ?: 'Specified Type';
    }
    if(request('status')) {
        $filters['Status'] = ucfirst(request('status'));
    }
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Property Master Asset Register & Inventory Valuation',
    'subtitle' => 'Schemes, Plots, Commercial Units & Real Estate Asset Stock',
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Real Estate Units</div>
        <div class="s-value">{{ $totalProperties ?? $allProps->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Available for Sale</div>
        <div class="s-value">{{ $availableProperties ?? $allProps->where('status', 'available')->count() }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Under Active Booking</div>
        <div class="s-value">{{ $bookedProperties ?? $allProps->where('status', 'booked')->count() }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Sold & Conveyed</div>
        <div class="s-value">{{ $soldProperties ?? $allProps->where('status', 'sold')->count() }}</div>
    </div>
    <div class="stat-box s-emerald">
        <div class="s-label">Total Asset Valuation</div>
        <div class="s-value">{{ formatIndianCurrency($totInventoryVal) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-building"></i> Real Estate Property Assets &amp; Valuation Ledger</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Property / Unit Particulars</th>
            <th style="width:85px;">Unit / Plot No</th>
            <th>Project / Development Scheme</th>
            <th>Category / Type</th>
            <th class="r" style="width:100px;">Super Built / Area</th>
            <th class="r" style="width:115px;">List Price / Val. (₹)</th>
            <th class="c" style="width:80px;">Inventory Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($allProps as $i => $p)
        @php
            $val = (float)($p->price ?? ($p->final_price ?? 0));
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td>
                <strong>{{ $p->property_name }}</strong>
                @if(!empty($p->property_code))
                    <div style="font-size:8px; color:#64748B;">CODE: {{ $p->property_code }}</div>
                @endif
            </td>
            <td><span class="badge badge-info">{{ $p->unit_no ?: ($p->property_code ?: '—') }}</span></td>
            <td>{{ $p->project->project_name ?? ($p->project->name ?? 'Standalone Property') }}</td>
            <td>{{ ucfirst($p->property_type ?? ($p->propertyType->name ?? 'Plot / Unit')) }}</td>
            <td class="r" style="color:#475569;">{{ $p->area ? ($p->area . ' ' . ($p->area_unit ?: 'Sq.Ft')) : '—' }}</td>
            <td class="r" style="font-weight:700; color:#0F172A;">{{ formatIndianCurrency($val, 2, false) }}</td>
            <td class="c">
                @if($p->status === 'available')
                    <span class="badge badge-success">Available</span>
                @elseif($p->status === 'booked')
                    <span class="badge badge-warning">Booked</span>
                @elseif($p->status === 'sold')
                    <span class="badge badge-info">Sold</span>
                @else
                    <span class="badge badge-neutral">{{ ucfirst($p->status) }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="c" style="padding:24px; color:#64748B;">
                <em>No property asset records found matching the filter parameters.</em>
            </td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="6" class="r"><strong>TOTAL PROPERTY INVENTORY VALUATION:</strong></td>
            <td class="r" style="color:#0F172A; font-size:11px;"><strong>{{ formatIndianCurrency($totInventoryVal) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>

<div style="margin-top:12px; font-size:9px; color:#64748B; line-height:1.4;">
    <strong>Balance Sheet Asset Note:</strong> Unsold and booked property units represent Work-in-Progress (WIP) and Finished Goods Inventory under Current Assets at cost/realizable valuation.
</div>

@include('admin.components.pdf-footer')
</body>
</html>