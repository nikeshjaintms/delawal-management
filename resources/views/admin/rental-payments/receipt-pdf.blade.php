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
    'subtitle' => 'Monthly Rent Settlement Slip',
    'docRef' => 'RCP-' . str_pad($rentalPayment->id ?? 1, 5, '0', STR_PAD_LEFT)
])

<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Amount Received</div>
        <div class="s-value">₹{{ number_format($rentalPayment->paid_amount ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Period / Month</div>
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
            <span class="info-label">Tenant Name:</span>
            <span class="info-val">{{ $rental->tenant_name ?? ($rental->tenant?->name ?? 'Tenant') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mobile Number:</span>
            <span class="info-val">{{ $rental->tenant_mobile ?? ($rental->tenant?->mobile ?? '—') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Agreement No:</span>
            <span class="info-val">{{ $rental->agreement_no ?: 'RA-'.str_pad($rental->id ?? 1, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building"></i> Property Details</div>
        <div class="info-row">
            <span class="info-label">Property Name:</span>
            <span class="info-val">{{ $rental->property->property_name ?? 'Delawala Property' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Mode:</span>
            <span class="info-val">{{ ucfirst($rentalPayment->payment_mode ?? 'Cash') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Date:</span>
            <span class="info-val">{{ isset($rentalPayment->payment_date) ? \Carbon\Carbon::parse($rentalPayment->payment_date)->format('d M Y') : now()->format('d M Y') }}</span>
        </div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-calculator"></i> Rent Breakdown</div>
<table>
    <thead>
        <tr>
            <th>Item Description</th>
            <th class="r">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Monthly Base Rent Amount ({{ $rentalPayment->payment_month ?? '' }} {{ $rentalPayment->payment_year ?? '' }})</td>
            <td class="r">₹{{ number_format($rentalPayment->rent_amount ?? 0, 2) }}</td>
        </tr>
        @if(($rentalPayment->maintenance_amount ?? 0) > 0)
        <tr>
            <td>Society / Maintenance Charges</td>
            <td class="r">₹{{ number_format($rentalPayment->maintenance_amount, 2) }}</td>
        </tr>
        @endif
        <tr style="background:#F1F5F9; font-weight:800;">
            <td>Total Billed / Due</td>
            <td class="r">₹{{ number_format(($rentalPayment->rent_amount ?? 0) + ($rentalPayment->maintenance_amount ?? 0), 2) }}</td>
        </tr>
        <tr style="background:#ECFDF5; font-weight:800; color:#059669;">
            <td>Amount Received / Paid</td>
            <td class="r" style="color:#059669;">₹{{ number_format($rentalPayment->paid_amount ?? 0, 2) }}</td>
        </tr>
        @if(($rentalPayment->pending_amount ?? 0) > 0)
        <tr style="color:#DC2626; font-weight:800;">
            <td>Balance Pending for this Month</td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($rentalPayment->pending_amount, 2) }}</td>
        </tr>
        @endif
    </tbody>
</table>

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Tenant Signature</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Authorized Signatory &amp; Stamp</div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>