@php
    $sale = $sale ?? $propertySale;
    $propDisplay = $sale->property_names ?: ($sale->property->property_name ?? '—');
    $projDisplay = $sale->project_name ?: ($sale->property?->project?->project_name ?? 'Direct Property');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Agreement - {{ $sale->agreement_no ?? ('SALE-' . $sale->id) }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Sale Agreement: ' . ($sale->agreement_no ?? ('SALE-' . $sale->id)),
    'orientation' => 'portrait',
    'backUrl' => route('property-sales.show', $sale->id)
])

@include('admin.components.pdf-header', [
    'title' => 'Property Sale Agreement & Deed',
    'subtitle' => 'Official Conveyance & Payment Terms',
    'firm' => $sale->firm ?? null,
    'docRef' => $sale->agreement_no ?? ('SALE-' . $sale->id)
])

<div class="stat-row">
    <div class="stat-box s-gold">
        <div class="s-label">Agreement No</div>
        <div class="s-value" style="font-size:14px;">{{ $sale->agreement_no ?: ('SALE-' . $sale->id) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Total Sale Value</div>
        <div class="s-value">₹{{ number_format($sale->sale_amount, 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Amount Received</div>
        <div class="s-value">₹{{ number_format($sale->booking_amount, 2) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user"></i> Purchaser / Customer Details</div>
        <div class="info-row">
            <span class="info-label">Customer Name:</span>
            <span class="info-val"><strong>{{ $sale->customer->name ?? '—' }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Mobile Number:</span>
            <span class="info-val">{{ $sale->customer->mobile ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-val">{{ $sale->customer->email ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Address:</span>
            <span class="info-val">{{ $sale->customer->address ?? 'Dahegam, Bharuch' }}</span>
        </div>
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building"></i> Property &amp; Sale Specifications</div>
        <div class="info-row">
            <span class="info-label">Property / Unit(s):</span>
            <span class="info-val"><strong>{{ $propDisplay }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Project:</span>
            <span class="info-val">{{ $projDisplay }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Sale Date:</span>
            <span class="info-val">{{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-val"><span class="badge badge-success">{{ ucfirst($sale->sale_status ?: 'Sold') }}</span></span>
        </div>
        @if($sale->broker || $sale->broker_name)
        <div class="info-row">
            <span class="info-label">Broker / Agent:</span>
            <span class="info-val">{{ $sale->broker->name ?? $sale->broker_name }} @if(($sale->broker_commission_amount ?? $sale->broker_commission) > 0)(Commission: ₹{{ number_format($sale->broker_commission_amount ?? $sale->broker_commission, 2) }})@endif</span>
        </div>
        @endif
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-receipt"></i> Financial Settlement Statement</div>
<table>
    <thead>
        <tr>
            <th>Description</th>
            <th class="r">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Agreed Consideration / Sale Price</td>
            <td class="r"><strong>₹{{ number_format($sale->sale_amount, 2) }}</strong></td>
        </tr>
        <tr>
            <td>Total Amount Received So Far</td>
            <td class="r" style="color:#059669; font-weight:700;">₹{{ number_format($sale->booking_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Remaining Balance Outstanding</td>
            <td class="r" style="color:#B45309; font-weight:700;">₹{{ number_format($sale->remaining_amount, 2) }}</td>
        </tr>
    </tbody>
</table>

@if($sale->payments && $sale->payments->count() > 0)
<div class="section-label" style="margin-top: 15px;"><i class="fa-solid fa-clock-rotate-left"></i> Payment Installments History</div>
<table>
    <thead>
        <tr>
            <th style="width: 30px;">#</th>
            <th>Date</th>
            <th>Mode</th>
            <th>Reference No</th>
            <th class="r">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sale->payments as $pIdx => $pmt)
        <tr>
            <td>{{ $pIdx + 1 }}</td>
            <td>{{ $pmt->payment_date ? \Carbon\Carbon::parse($pmt->payment_date)->format('d M Y') : '—' }}</td>
            <td>{{ $pmt->payment_mode ?: 'Cash' }}</td>
            <td>{{ $pmt->transaction_ref ?: '—' }}</td>
            <td class="r" style="font-weight: 700; color: #059669;">₹{{ number_format($pmt->payment_amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Purchaser Signature</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">For {{ $sale->firm->firm_name ?? 'Delawala Infra Co.' }}<br><small style="color:#64748B;">(Authorized Signatory)</small></div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>