<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Agreement - {{ $rental->agreement_no ?? ('RA-' . $rental->id) }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Rental Agreement: ' . ($rental->agreement_no ?? ('RA-' . $rental->id)),
    'orientation' => 'portrait',
    'backUrl' => route('rentals.show', $rental->id)
])

@include('admin.components.pdf-header', [
    'title' => 'Tenancy & Lease Agreement',
    'subtitle' => 'Terms of Tenancy & Monthly Rent Deed',
    'docRef' => $rental->agreement_no ?? ('RA-' . $rental->id)
])

<div class="stat-row">
    <div class="stat-box s-gold">
        <div class="s-label">Agreement No</div>
        <div class="s-value" style="font-size:14px;">{{ $rental->agreement_no ?: ('RA-' . $rental->id) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Monthly Rent</div>
        <div class="s-value">₹{{ number_format($rental->monthly_rent, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Security Deposit</div>
        <div class="s-value">₹{{ number_format($rental->security_deposit, 2) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user"></i> Tenant Particulars</div>
        <div class="info-row">
            <span class="info-label">Tenant Name:</span>
            <span class="info-val">{{ $rental->tenant_name ?? ($rental->tenant?->name ?? '—') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mobile Number:</span>
            <span class="info-val">{{ $rental->tenant_mobile ?? ($rental->tenant?->mobile ?? '—') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">ID / Aadhar:</span>
            <span class="info-val">{{ $rental->tenant_id_proof ?: '—' }}</span>
        </div>
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building"></i> Tenancy Property Terms</div>
        <div class="info-row">
            <span class="info-label">Property:</span>
            <span class="info-val">{{ $rental->property->property_name ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Start Date:</span>
            <span class="info-val">{{ $rental->start_date ? \Carbon\Carbon::parse($rental->start_date)->format('d M Y') : '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">End Date:</span>
            <span class="info-val">{{ $rental->end_date ? \Carbon\Carbon::parse($rental->end_date)->format('d M Y') : '—' }}</span>
        </div>
    </div>
</div>

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Tenant Signature</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Landlord / Delawala Infra Co.</div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>