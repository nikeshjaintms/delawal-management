<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contractor Dossier - {{ $contractor->name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Contractor Dossier: ' . $contractor->name,
    'orientation' => 'portrait',
    'backUrl' => isset($contractor->id) ? route('contractors.show', $contractor->id) : route('contractors.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Contractor Dossier & Work Ledger',
    'subtitle' => 'Civil Contracts & Labor Accounts',
    'firm' => $contractor->firm ?? null,
    'docRef' => 'CONT-' . ($contractor->id ?? 1)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Contractor Name</div>
        <div class="s-value" style="font-size:14px;">{{ $contractor->name }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Mobile</div>
        <div class="s-value" style="font-size:13px;">{{ $contractor->mobile ?: '—' }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Status</div>
        <div class="s-value" style="font-size:13px;">{{ strtoupper($contractor->status ?: 'ACTIVE') }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-helmet-safety"></i> Contractor Particulars</div>
        <div class="info-row"><span class="info-label">Name:</span><span class="info-val">{{ $contractor->name }}</span></div>
        <div class="info-row"><span class="info-label">Mobile:</span><span class="info-val">{{ $contractor->mobile ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Email:</span><span class="info-val">{{ $contractor->email ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Specialization:</span><span class="info-val">{{ $contractor->specialization ?: 'Civil Works' }}</span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Location &amp; Firm</div>
        <div class="info-row"><span class="info-label">Address:</span><span class="info-val">{{ $contractor->address ?: 'Dahegam, Bharuch' }}</span></div>
        <div class="info-row"><span class="info-label">City:</span><span class="info-val">{{ $contractor->city ?: 'Dahegam' }}</span></div>
        <div class="info-row"><span class="info-label">Firm:</span><span class="info-val">{{ $contractor->firm->firm_name ?? 'Delawala Infra Co.' }}</span></div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>