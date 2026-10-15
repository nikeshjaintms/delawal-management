<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Deed - {{ ($property ?? $propertyMaster)->property_name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@php 
    $activeProp = $property ?? ($propertyMaster ?? null);
    $propVal = (float)($activeProp->price ?? ($activeProp->final_price ?? 0));
@endphp
@include('admin.components.pdf-action-bar', [
    'title' => 'Property Specification: ' . ($activeProp->property_name ?? 'Property'),
    'orientation' => 'portrait',
    'backUrl' => isset($activeProp->id) ? route('property-masters.show', $activeProp->id) : route('property-masters.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Property Master Specification & Unit Dossier',
    'subtitle' => 'Real Estate Asset Valuation, Legal Survey & Ownership Profile',
    'firm' => $activeProp->firm ?? null,
    'docRef' => $activeProp->property_code ?: ('PROP-' . ($activeProp->id ?? 1))
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Unit / Plot Ref</div>
        <div class="s-value" style="font-size:13px; color:#1E3A8A;">{{ $activeProp->unit_no ?: ($activeProp->property_code ?: 'PROP-'.$activeProp->id) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Asset Valuation</div>
        <div class="s-value" style="font-size:13px;">{{ formatIndianCurrency($propVal) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Inventory Status</div>
        <div class="s-value" style="font-size:13px;">{{ strtoupper($activeProp->status ?: 'AVAILABLE') }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building"></i> Property Particulars</div>
        <div class="info-row"><span class="info-label">Property Name:</span><span class="info-val"><strong>{{ $activeProp->property_name }}</strong></span></div>
        <div class="info-row"><span class="info-label">Property Type:</span><span class="info-val">{{ ucfirst($activeProp->property_type ?? 'Plot') }}</span></div>
        <div class="info-row"><span class="info-label">Scheme / Project:</span><span class="info-val">{{ $activeProp->project->project_name ?? ($activeProp->project->name ?? 'Standalone Property') }}</span></div>
        <div class="info-row"><span class="info-label">Super Built / Area:</span><span class="info-val">{{ $activeProp->area ? ($activeProp->area . ' ' . ($activeProp->area_unit ?: 'Sq.Ft')) : '—' }}</span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Location &amp; Legal</div>
        <div class="info-row"><span class="info-label">Survey / Khasra No:</span><span class="info-val">{{ $activeProp->survey_number ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Registered Address:</span><span class="info-val">{{ $activeProp->address ?: 'Dahegam Road, Bharuch' }}</span></div>
        <div class="info-row"><span class="info-label">City / State:</span><span class="info-val">{{ $activeProp->city ?: 'Dahegam' }}, Gujarat</span></div>
        <div class="info-row"><span class="info-label">Affiliated Firm:</span><span class="info-val">{{ $activeProp->firm->firm_name ?? 'Delawala Infrastructure' }}</span></div>
    </div>
</div>

<div style="margin-top:14px; font-size:9px; color:#64748B; line-height:1.4;">
    <strong>Asset Valuation Note:</strong> Property unit asset valuation is reflected under Inventory (Finished Goods / WIP) in the Balance Sheet at list valuation or acquisition cost.
</div>

@include('admin.components.pdf-footer')
</body>
</html>