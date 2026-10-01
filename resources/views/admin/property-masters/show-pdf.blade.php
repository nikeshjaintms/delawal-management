<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Deed - {{ ($property ?? $propertyMaster)->property_name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@php $activeProp = $property ?? ($propertyMaster ?? null); @endphp
@include('admin.components.pdf-action-bar', [
    'title' => 'Property Specification: ' . ($activeProp->property_name ?? 'Property'),
    'orientation' => 'portrait',
    'backUrl' => isset($activeProp->id) ? route('property-masters.show', $activeProp->id) : route('property-masters.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Property Specification & Unit Profile',
    'subtitle' => 'Asset Valuation & Ownership Ledger',
    'firm' => $activeProp->firm ?? null,
    'docRef' => $activeProp->property_code ?: ('PROP-' . ($activeProp->id ?? 1))
])

<div class="stat-row">
    <div class="stat-box s-gold">
        <div class="s-label">Unit / Plot Code</div>
        <div class="s-value" style="font-size:14px;">{{ $activeProp->unit_no ?: ($activeProp->property_code ?: 'PROP-'.$activeProp->id) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Property Valuation</div>
        <div class="s-value" style="font-size:13px;">₹{{ number_format($activeProp->price ?? $activeProp->final_price ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Current Status</div>
        <div class="s-value" style="font-size:13px;">{{ strtoupper($activeProp->status ?: 'AVAILABLE') }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building"></i> Property Particulars</div>
        <div class="info-row"><span class="info-label">Name:</span><span class="info-val">{{ $activeProp->property_name }}</span></div>
        <div class="info-row"><span class="info-label">Type:</span><span class="info-val">{{ ucfirst($activeProp->property_type ?? 'Plot') }}</span></div>
        <div class="info-row"><span class="info-label">Project:</span><span class="info-val">{{ $activeProp->project->project_name ?? 'Standalone' }}</span></div>
        <div class="info-row"><span class="info-label">Total Area:</span><span class="info-val">{{ $activeProp->area ? ($activeProp->area . ' ' . ($activeProp->area_unit ?: 'Sq.Ft')) : '—' }}</span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Location &amp; Legal</div>
        <div class="info-row"><span class="info-label">Address:</span><span class="info-val">{{ $activeProp->address ?: 'Dahegam Road, Bharuch' }}</span></div>
        <div class="info-row"><span class="info-label">City:</span><span class="info-val">{{ $activeProp->city ?: 'Dahegam' }}</span></div>
        <div class="info-row"><span class="info-label">Firm:</span><span class="info-val">{{ $activeProp->firm->firm_name ?? 'Delawala Infra Co.' }}</span></div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>