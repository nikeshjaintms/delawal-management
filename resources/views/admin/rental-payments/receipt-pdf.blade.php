@php
    $pdAmt = (float)($rentalPayment->paid_amount ?? 0);
    $rtAmt = (float)($rentalPayment->rent_amount ?? 0);
    $mtAmt = (float)($rentalPayment->maintenance_amount ?? 0);
    $totDue = $rtAmt + $mtAmt;
    $balAmt = (float)($rentalPayment->pending_amount ?? max(0, $totDue - $pdAmt));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Receipt #RCP-{{ str_pad($rentalPayment->id ?? 1, 5, '0', STR_PAD_LEFT) }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Official Rent Receipt #RCP-' . str_pad($rentalPayment->id ?? 1, 5, '0', STR_PAD_LEFT),
    'orientation' => 'portrait',
    'backUrl' => isset($rental->id) ? route('rental-payments.index', $rental->id) : route('rentals.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Official Rent Payment Receipt',
    'subtitle' => 'Monthly Rent Settlement Slip & Collection Voucher',
    'docRef' => 'RCP-' . str_pad($rentalPayment->id ?? 1, 5, '0', STR_PAD_LEFT)
])

<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Amount Realized</div>
        <div class="s-value" style="font-size:14px; color:#059669;">{{ formatIndianCurrency($pdAmt) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Billing Period</div>
        <div class="s-value" style="font-size:13px;">{{ ($rentalPayment->payment_month ?? '') . ' ' . ($rentalPayment->payment_year ?? '') }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Payment Status</div>
        <div class="s-value" style="font-size:13px;">{{ strtoupper($rentalPayment->payment_status ?? 'PAID') }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user"></i> Tenant Particulars</div>
        <div class="info-row">
            <span class="info-label">Tenant Full Name:</span>
            <span class="info-val"><strong>{{ $rental->tenant_name ?? ($rental->tenant?->name ?? 'Tenant') }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Mobile Number:</span>
            <span class="info-val">{{ $rental->tenant_mobile ?? ($rental->tenant?->mobile ?? '—') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Agreement Reference:</span>
            <span class="info-val" style="color:#1E3A8A; font-weight:700;">{{ $rental->agreement_no ?: 'RA-'.str_pad($rental->id ?? 1, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building"></i> Property &amp; Transaction Details</div>
        <div class="info-row">
            <span class="info-label">Leased Property:</span>
            <span class="info-val"><strong>{{ $rental->property->property_name ?? 'Delawala Property' }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Settlement Mode:</span>
            <span class="info-val">{{ ucfirst(str_replace('_', ' ', $rentalPayment->payment_mode ?: 'Cash')) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Date:</span>
            <span class="info-val">{{ isset($rentalPayment->payment_date) ? \Carbon\Carbon::parse($rentalPayment->payment_date)->format('d M Y') : now()->format('d M Y') }}</span>
        </div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-calculator"></i> Monthly Rent Accrual &amp; Settlement Statement</div>
<table>
    <thead>
        <tr>
            <th>Item Particulars</th>
            <th class="r" style="width: 150px;">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Monthly Base Rent Amount ({{ $rentalPayment->payment_month ?? '' }} {{ $rentalPayment->payment_year ?? '' }})</td>
            <td class="r" style="font-weight:600; color:#1E3A8A;">{{ formatIndianCurrency($rtAmt) }}</td>
        </tr>
        @if($mtAmt > 0)
        <tr>
            <td>Society Maintenance &amp; Common Utility Charges</td>
            <td class="r" style="font-weight:600;">{{ formatIndianCurrency($mtAmt) }}</td>
        </tr>
        @endif
        <tr style="background:#F8FAFC; font-weight:700;">
            <td>Total Billed Demand</td>
            <td class="r">{{ formatIndianCurrency($totDue) }}</td>
        </tr>
        <tr style="background:#F0FDF4; font-weight:800; color:#059669;">
            <td>Amount Realized / Paid (Credit)</td>
            <td class="r" style="color:#059669;">{{ formatIndianCurrency($pdAmt) }}</td>
        </tr>
        <tr class="double-total">
            <td><strong>Remaining Balance Due for this Billing Period</strong></td>
            <td class="r" style="color:{{ $balAmt > 0 ? '#DC2626' : '#10B981' }}; font-size:11px;"><strong>{{ formatIndianCurrency($balAmt) }}</strong></td>
        </tr>
    </tbody>
</table>

<div style="margin-top:10px; font-size:8.5px; color:#64748B;">
    <strong>Amount in Words:</strong> {{ numberToWordsIndian($pdAmt) }}
</div>

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Tenant Acknowledgment</div>
    </div>
    <div class="auth-col">
        @php
            $rpFirm = $rentalPayment->rental?->firm ?: ($rentalPayment->rental?->property?->project?->firm);
            $rpSig = $rpFirm?->digital_signature ?? null;
            $hasRpSig = !empty($rpSig) && (
                \Illuminate\Support\Facades\Storage::disk('public')->exists($rpSig) ||
                file_exists(public_path('storage/' . $rpSig)) ||
                file_exists(public_path($rpSig))
            );
            $rpSigUrl = $hasRpSig ? (file_exists(public_path($rpSig)) ? asset($rpSig) : asset('storage/' . $rpSig)) : null;
        @endphp
        @if($rpSigUrl)
            <div style="min-height: 40px; margin-bottom: 2px;">
                <img src="{{ $rpSigUrl }}" style="max-height: 40px; max-width: 140px; object-fit: contain;" alt="Authorized Signature">
            </div>
        @endif
        <div class="auth-line">Authorized Signatory &amp; Stamp</div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>