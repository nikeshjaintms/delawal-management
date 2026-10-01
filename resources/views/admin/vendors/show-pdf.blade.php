<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dossier - {{ $vendor->name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Vendor Dossier: ' . $vendor->name,
    'orientation' => 'portrait',
    'backUrl' => isset($vendor->id) ? route('vendors.show', $vendor->id) : route('vendors.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Vendor Dossier & Profile',
    'subtitle' => 'Material Supplier Ledger & Accounts',
    'firm' => $vendor->firm ?? null,
    'docRef' => 'VENDOR-' . ($vendor->id ?? 1)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Vendor Name</div>
        <div class="s-value" style="font-size:14px;">{{ $vendor->name }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">GSTIN</div>
        <div class="s-value" style="font-size:12px;">{{ $vendor->gst_no ?: 'Unregistered' }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Status</div>
        <div class="s-value" style="font-size:13px;">{{ strtoupper($vendor->status ?: 'ACTIVE') }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-truck"></i> Vendor Particulars</div>
        <div class="info-row"><span class="info-label">Name:</span><span class="info-val">{{ $vendor->name }}</span></div>
        <div class="info-row"><span class="info-label">Mobile:</span><span class="info-val">{{ $vendor->mobile ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Email:</span><span class="info-val">{{ $vendor->email ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">GSTIN:</span><span class="info-val">{{ $vendor->gst_no ?: 'Unregistered' }}</span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Address &amp; Location</div>
        <div class="info-row"><span class="info-label">Address:</span><span class="info-val">{{ $vendor->address ?: 'Dahegam Road, Bharuch' }}</span></div>
        <div class="info-row"><span class="info-label">City:</span><span class="info-val">{{ $vendor->city ?: 'Dahegam' }}</span></div>
        <div class="info-row"><span class="info-label">State / PIN:</span><span class="info-val">{{ $vendor->state ?: 'Gujarat' }} - {{ $vendor->pincode ?: '392012' }}</span></div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>