<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Broker Dossier - {{ $broker->name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Broker Dossier: ' . $broker->name,
    'orientation' => 'portrait',
    'backUrl' => isset($broker->id) ? route('brokers.show', $broker->id) : route('brokers.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Broker Dossier & Commission Profile',
    'subtitle' => 'Channel Partner Ledger & Deal History',
    'firm' => $broker->firm ?? null,
    'docRef' => 'BROKER-' . ($broker->id ?? 1)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Broker Name</div>
        <div class="s-value" style="font-size:14px;">{{ $broker->name }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Mobile</div>
        <div class="s-value" style="font-size:13px;">{{ $broker->mobile ?: '—' }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Status</div>
        <div class="s-value" style="font-size:13px;">{{ strtoupper($broker->status ?: 'ACTIVE') }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user-tie"></i> Channel Partner Details</div>
        <div class="info-row"><span class="info-label">Full Name:</span><span class="info-val">{{ $broker->name }}</span></div>
        <div class="info-row"><span class="info-label">Mobile:</span><span class="info-val">{{ $broker->mobile ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Email:</span><span class="info-val">{{ $broker->email ?: '—' }}</span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Location &amp; Affiliation</div>
        <div class="info-row"><span class="info-label">Address:</span><span class="info-val">{{ $broker->address ?: 'Dahegam, Bharuch' }}</span></div>
        <div class="info-row"><span class="info-label">City:</span><span class="info-val">{{ $broker->city ?: 'Dahegam' }}</span></div>
        <div class="info-row"><span class="info-label">Firm:</span><span class="info-val">{{ $broker->firm->firm_name ?? 'Delawala Infra Co.' }}</span></div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>