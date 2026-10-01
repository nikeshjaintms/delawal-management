<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenants Directory Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Tenants Directory Report',
    'orientation' => 'landscape',
    'backUrl' => route('tenants.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Tenants Directory Report',
    'subtitle' => 'Rental Agreements & Tenant Registry'
])

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Tenants</div>
        <div class="s-value">{{ $totalTenants ?? $tenants->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Tenants</div>
        <div class="s-value">{{ $activeTenants ?? $tenants->where('status', 'active')->count() }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-users"></i> Tenants Master Directory</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Tenant Name</th>
            <th>Mobile Number</th>
            <th>Email</th>
            <th>ID Proof</th>
            <th>Firm</th>
            <th>City</th>
            <th>Address</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($tenants as $i => $t)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $t->name }}</strong></td>
            <td>{{ $t->mobile ?: '—' }}</td>
            <td>{{ $t->email ?: '—' }}</td>
            <td>{{ $t->id_proof_no ?: '—' }}</td>
            <td>{{ $t->firm->firm_name ?? 'Delawala Infra Co.' }}</td>
            <td>{{ $t->city ?: 'Dahegam' }}</td>
            <td>{{ $t->address ?: '—' }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($t->status ?? 'Active') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:20px;color:#64748B;">No tenant records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>