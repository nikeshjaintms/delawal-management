<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Account Statement & Dossier - {{ $tenant->name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Tenant Dossier: ' . $tenant->name,
    'orientation' => 'portrait',
    'backUrl' => isset($tenant->id) ? route('tenants.show', $tenant->id) : route('tenants.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Tenant Account Statement & Lease Dossier',
    'subtitle' => 'Commercial & Residential Lease Agreements & Occupancy Profile',
    'firm' => $tenant->firm ?? null,
    'docRef' => 'TEN-' . str_pad($tenant->id ?? 1, 5, '0', STR_PAD_LEFT)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Tenant Name</div>
        <div class="s-value" style="font-size:13px;">{{ $tenant->name }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Contact / Mobile</div>
        <div class="s-value" style="font-size:12px;">{{ $tenant->mobile ?: '—' }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Identity Proof</div>
        <div class="s-value" style="font-size:12px;">{{ $tenant->id_proof_no ?: ($tenant->identity_number ?: 'Verified') }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Status</div>
        <div class="s-value" style="font-size:12px;">{{ strtoupper($tenant->status ?: 'ACTIVE') }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user"></i> Tenant Particulars</div>
        <div class="info-row"><span class="info-label">Full Name:</span><span class="info-val"><strong>{{ $tenant->name }}</strong></span></div>
        <div class="info-row"><span class="info-label">Mobile Number:</span><span class="info-val">{{ $tenant->mobile ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Email Address:</span><span class="info-val">{{ $tenant->email ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Identity Number:</span><span class="info-val">{{ $tenant->id_proof_no ?: ($tenant->identity_number ?: '—') }}</span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Address & Registration</div>
        <div class="info-row"><span class="info-label">Registered Address:</span><span class="info-val">{{ $tenant->address ?: 'Dahegam, Bharuch' }}</span></div>
        <div class="info-row"><span class="info-label">City:</span><span class="info-val">{{ $tenant->city ?: 'Dahegam' }}</span></div>
        <div class="info-row"><span class="info-label">Affiliated Firm:</span><span class="info-val">{{ $tenant->firm->firm_name ?? 'Delawala Infrastructure' }}</span></div>
        <div class="info-row"><span class="info-label">Enrolled On:</span><span class="info-val">{{ $tenant->created_at ? $tenant->created_at->format('d M Y') : '—' }}</span></div>
    </div>
</div>

@if($tenant->rentals && $tenant->rentals->isNotEmpty())
@php
    $totRentBilled = $tenant->rentals->sum('rent_amount');
    $totDeposit = $tenant->rentals->sum('deposit_amount');
@endphp
<div class="section-label"><i class="fa-solid fa-key"></i> Active Lease Contracts &amp; Tenancy Terms</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Leased Property / Unit</th>
            <th style="width:90px;">Agreement No</th>
            <th class="r" style="width:115px;">Monthly Rent (₹)</th>
            <th class="r" style="width:115px;">Security Deposit (₹)</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tenant->rentals as $i => $r)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $r->property->property_name ?? ($r->property->property_code ?? 'Commercial / Unit') }}</strong></td>
            <td style="font-weight:700; color:#1E3A8A;">{{ $r->agreement_no ?: ('RA-' . $r->id) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($r->rent_amount ?? 0) }}</td>
            <td class="r" style="font-weight:600;">{{ formatIndianCurrency($r->deposit_amount ?? 0) }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($r->status ?? 'Active') }}</span></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="3" class="r"><strong>TOTAL MONTHLY LEASE ACCRUAL:</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($totRentBilled) }}</strong></td>
            <td class="r" style="color:#0F172A; font-size:11px;"><strong>{{ formatIndianCurrency($totDeposit) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>
@endif

@include('admin.components.pdf-footer')
</body>
</html>