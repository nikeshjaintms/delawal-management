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

@php
    $rRent = (float)($rental->rent_amount ?? ($rental->monthly_rent ?? 0));
    $rDep = (float)($rental->security_deposit ?? ($rental->deposit_amount ?? 0));
    $sDate = $rental->rent_start_date ?: ($rental->start_date ?: null);
    $eDate = $rental->rent_end_date ?: ($rental->end_date ?: null);
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Tenancy & Lease Agreement Deed',
    'subtitle' => 'Official Contract of Tenancy, Monthly Rent Terms & Deposit Deed',
    'firm' => $rental->firm ?? null,
    'docRef' => $rental->agreement_no ?? ('RA-' . $rental->id)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Agreement Reference</div>
        <div class="s-value" style="font-size:13px; color:#1E3A8A;">{{ $rental->agreement_no ?: ('RA-' . $rental->id) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Agreed Monthly Rent</div>
        <div class="s-value" style="font-size:13px;">{{ formatIndianCurrency($rRent) }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Security Deposit</div>
        <div class="s-value" style="font-size:13px;">{{ formatIndianCurrency($rDep) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Lease Status</div>
        <div class="s-value" style="font-size:13px;">{{ strtoupper($rental->rental_status ?: ($rental->status ?: 'ACTIVE')) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user"></i> Tenant Particulars</div>
        <div class="info-row">
            <span class="info-label">Tenant Full Name:</span>
            <span class="info-val"><strong>{{ $rental->tenant_name ?? ($rental->tenant?->name ?? '—') }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Mobile Number:</span>
            <span class="info-val">{{ $rental->tenant_mobile ?? ($rental->tenant?->mobile ?? '—') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email Address:</span>
            <span class="info-val">{{ $rental->tenant_email ?? ($rental->tenant?->email ?? '—') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">ID / Aadhar Proof:</span>
            <span class="info-val">{{ $rental->tenant_id_proof ?: ($rental->tenant?->id_proof_no ?: '—') }}</span>
        </div>
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building"></i> Tenancy Property & Terms</div>
        <div class="info-row">
            <span class="info-label">Leased Property:</span>
            <span class="info-val">{{ $rental->property->property_name ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Commencement Date:</span>
            <span class="info-val">{{ $sDate ? \Carbon\Carbon::parse($sDate)->format('d M Y') : '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Expiry / Renewal Date:</span>
            <span class="info-val">{{ $eDate ? \Carbon\Carbon::parse($eDate)->format('d M Y') : '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Monthly Due Date:</span>
            <span class="info-val">{{ $rental->rent_due_date ? ($rental->rent_due_date . 'th of every month') : '1st of every month' }}</span>
        </div>
    </div>
</div>

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Tenant Signature</div>
    </div>
    <div class="auth-col">
        @php
            $rntFirm = $rental->firm ?: ($rental->property?->project?->firm);
            $rntSig = $rntFirm?->digital_signature ?? null;
            $hasRntSig = !empty($rntSig) && (
                \Illuminate\Support\Facades\Storage::disk('public')->exists($rntSig) ||
                file_exists(public_path('storage/' . $rntSig)) ||
                file_exists(public_path($rntSig))
            );
            $rntSigUrl = $hasRntSig ? (file_exists(public_path($rntSig)) ? asset($rntSig) : asset('storage/' . $rntSig)) : null;
        @endphp
        @if($rntSigUrl)
            <div style="min-height: 40px; margin-bottom: 2px;">
                <img src="{{ $rntSigUrl }}" style="max-height: 40px; max-width: 140px; object-fit: contain;" alt="Authorized Signature">
            </div>
        @endif
        <div class="auth-line">Authorized Signatory / {{ $rntFirm->firm_name ?? 'Delawala Infrastructure' }}</div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>