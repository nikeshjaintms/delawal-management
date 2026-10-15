<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenants Master Registry - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Tenants Master Registry',
    'orientation' => 'landscape',
    'backUrl' => route('tenants.index')
])

@php
    $filters = [];
    if(request('search')) {
        $filters['Search Keyword'] = request('search');
    }
    if(request('status')) {
        $filters['Status'] = ucfirst(request('status'));
    }
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Tenants Master Registry & Lease Directory',
    'subtitle' => 'Commercial & Residential Tenancy Account Directory',
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Registered Tenants</div>
        <div class="s-value">{{ $totalTenants ?? $tenants->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Leases</div>
        <div class="s-value">{{ $activeTenants ?? $tenants->where('status', 'active')->count() }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-users"></i> Tenants Master Directory</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Tenant Full Name</th>
            <th style="width:95px;">Mobile Number</th>
            <th>Email</th>
            <th style="width:95px;">ID Proof / Aadhar</th>
            <th>Firm Affiliation</th>
            <th>City / State</th>
            <th>Registered Address</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($tenants as $i => $t)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $t->name }}</strong></td>
            <td>{{ $t->mobile ?: '—' }}</td>
            <td>{{ $t->email ?: '—' }}</td>
            <td style="color:#1E3A8A; font-weight:600;">{{ $t->id_proof_no ?: '—' }}</td>
            <td>{{ $t->firm->firm_name ?? 'Delawala Infrastructure' }}</td>
            <td>{{ $t->city ?: 'Dahegam' }}</td>
            <td>{{ $t->address ?: '—' }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($t->status ?? 'Active') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:24px; color:#64748B;"><em>No tenant records found matching the filter parameters.</em></td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="8" class="r"><strong>TOTAL ACTIVE TENANT REGISTRATIONS:</strong></td>
            <td class="c"><strong>{{ $tenants->count() }}</strong></td>
        </tr>
    </tfoot>
</table>

@include('admin.components.pdf-footer')
</body>
</html>