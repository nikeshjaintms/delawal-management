<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dossier - {{ $seller->name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Seller Dossier: ' . $seller->name,
    'orientation' => 'portrait',
    'backUrl' => isset($seller->id) ? route('sellers.show', $seller->id) : route('sellers.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Seller Dossier & Property History',
    'subtitle' => 'Land Acquisition & Payment Ledger',
    'firm' => $seller->firm ?? null,
    'docRef' => 'SELLER-' . ($seller->id ?? 1)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Seller Name</div>
        <div class="s-value" style="font-size:14px;">{{ $seller->name }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Mobile</div>
        <div class="s-value" style="font-size:13px;">{{ $seller->mobile ?: '—' }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Status</div>
        <div class="s-value" style="font-size:13px;">{{ strtoupper($seller->status ?: 'ACTIVE') }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user"></i> Seller Particulars</div>
        <div class="info-row"><span class="info-label">Full Name:</span><span class="info-val">{{ $seller->name }}</span></div>
        <div class="info-row"><span class="info-label">Mobile:</span><span class="info-val">{{ $seller->mobile ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Email:</span><span class="info-val">{{ $seller->email ?: '—' }}</span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Location &amp; Firm</div>
        <div class="info-row"><span class="info-label">Address:</span><span class="info-val">{{ $seller->address ?: 'Dahegam, Bharuch' }}</span></div>
        <div class="info-row"><span class="info-label">City:</span><span class="info-val">{{ $seller->city ?: 'Dahegam' }}</span></div>
        <div class="info-row"><span class="info-label">Firm:</span><span class="info-val">{{ $seller->firm->firm_name ?? 'Delawala Infra Co.' }}</span></div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>