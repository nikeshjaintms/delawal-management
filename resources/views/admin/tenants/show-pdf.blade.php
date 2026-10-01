<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Dossier - {{ $tenant->name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Tenant Dossier: ' . $tenant->name,
    'orientation' => 'portrait',
    'backUrl' => isset($tenant->id) ? route('tenants.show', $tenant->id) : route('tenants.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Tenant Dossier & Profile',
    'subtitle' => 'Tenancy Ledger & Rent History',
    'firm' => $tenant->firm ?? null,
    'docRef' => 'TENANT-' . ($tenant->id ?? 1)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Tenant Name</div>
        <div class="s-value" style="font-size:14px;">{{ $tenant->name }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Mobile</div>
        <div class="s-value" style="font-size:13px;">{{ $tenant->mobile ?: '—' }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Status</div>
        <div class="s-value" style="font-size:13px;">{{ strtoupper($tenant->status ?: 'ACTIVE') }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user"></i> Tenant Particulars</div>
        <div class="info-row"><span class="info-label">Name:</span><span class="info-val">{{ $tenant->name }}</span></div>
        <div class="info-row"><span class="info-label">Mobile:</span><span class="info-val">{{ $tenant->mobile ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Email:</span><span class="info-val">{{ $tenant->email ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">ID Proof:</span><span class="info-val">{{ $tenant->id_proof_no ?: '—' }}</span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Address &amp; Registration</div>
        <div class="info-row"><span class="info-label">Address:</span><span class="info-val">{{ $tenant->address ?: 'Dahegam, Bharuch' }}</span></div>
        <div class="info-row"><span class="info-label">City:</span><span class="info-val">{{ $tenant->city ?: 'Dahegam' }}</span></div>
        <div class="info-row"><span class="info-label">Firm:</span><span class="info-val">{{ $tenant->firm->firm_name ?? 'Delawala Infra Co.' }}</span></div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>