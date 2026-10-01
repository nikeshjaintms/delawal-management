<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties Directory Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Properties Directory Report',
    'orientation' => 'landscape',
    'backUrl' => route('property-masters.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Properties Directory Report',
    'subtitle' => 'Units, Plots & Asset Inventory Registry'
])

@php
    $allProps = $propertyMasters ?? ($properties ?? collect([]));
@endphp

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Properties</div>
        <div class="s-value">{{ $totalProperties ?? $allProps->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Available</div>
        <div class="s-value">{{ $availableProperties ?? $allProps->where('status', 'available')->count() }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Booked</div>
        <div class="s-value">{{ $bookedProperties ?? $allProps->where('status', 'booked')->count() }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Sold</div>
        <div class="s-value">{{ $soldProperties ?? $allProps->where('status', 'sold')->count() }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-building"></i> Property Asset Ledger</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Property / Unit Name</th>
            <th>Unit / Plot No</th>
            <th>Project</th>
            <th>Property Type</th>
            <th class="r">Area</th>
            <th class="r">Price (₹)</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($allProps as $i => $p)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $p->property_name }}</strong></td>
            <td><span class="badge badge-info">{{ $p->unit_no ?: ($p->property_code ?: '—') }}</span></td>
            <td>{{ $p->project->project_name ?? 'Standalone' }}</td>
            <td>{{ ucfirst($p->property_type ?? 'Plot') }}</td>
            <td class="r">{{ $p->area ? ($p->area . ' ' . ($p->area_unit ?: 'Sq.Ft')) : '—' }}</td>
            <td class="r">₹{{ number_format($p->price ?? $p->final_price ?? 0, 2) }}</td>
            <td class="c">
                @if($p->status === 'available')
                    <span class="badge badge-success">Available</span>
                @elseif($p->status === 'booked')
                    <span class="badge badge-warning">Booked</span>
                @elseif($p->status === 'sold')
                    <span class="badge badge-info">Sold</span>
                @else
                    <span class="badge badge-danger">{{ ucfirst($p->status) }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:20px;color:#64748B;">No properties found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>