@php
    $sale = $sale ?? $propertySale;
    $propDisplay = $sale->property_names ?: ($sale->property->property_name ?? '—');
    $projDisplay = $sale->project_name ?: ($sale->property?->project?->project_name ?? 'Direct Property');
    $sAmt = (float)($sale->sale_amount ?? 0);
    $bAmt = (float)($sale->booking_amount ?? 0);
    $rAmt = (float)($sale->remaining_amount ?? max(0, $sAmt - $bAmt));
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
    'title' => 'Property Sale Agreement & Conveyance Deed',
    'subtitle' => 'Official Conveyance, Consideration Terms & Payment Schedule',
    'firm' => $sale->firm ?? null,
    'docRef' => $sale->agreement_no ?? ('SALE-' . $sale->id)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Agreement Reference</div>
        <div class="s-value" style="font-size:13px; color:#1E3A8A;">{{ $sale->agreement_no ?: ('SALE-' . $sale->id) }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Total Consideration</div>
        <div class="s-value" style="font-size:13px;">{{ formatIndianCurrency($sAmt) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Realized Amount</div>
        <div class="s-value" style="font-size:13px;">{{ formatIndianCurrency($bAmt) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Outstanding Balance</div>
        <div class="s-value" style="font-size:13px;">{{ formatIndianCurrency($rAmt) }}</div>
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
            <span class="info-label">Email Address:</span>
            <span class="info-val">{{ $sale->customer->email ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Billing Address:</span>
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
            <span class="info-label">Project / Scheme:</span>
            <span class="info-val">{{ $projDisplay }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Execution Date:</span>
            <span class="info-val">{{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Sale Status:</span>
            <span class="info-val"><span class="badge badge-success">{{ ucfirst($sale->sale_status ?: 'Sold') }}</span></span>
        </div>
        @if($sale->broker || $sale->broker_name)
        <div class="info-row">
            <span class="info-label">Sourcing Broker:</span>
            <span class="info-val">{{ $sale->broker->name ?? $sale->broker_name }} @if(($sale->broker_commission_amount ?? $sale->broker_commission) > 0)({{ formatIndianCurrency($sale->broker_commission_amount ?? $sale->broker_commission) }})@endif</span>
        </div>
        @endif
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-receipt"></i> Consideration &amp; Settlement Statement</div>
<table>
    <thead>
        <tr>
            <th>Transaction Description</th>
            <th class="r" style="width:160px;">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Agreed Consideration / Sale Price</td>
            <td class="r" style="font-weight:700; color:#1E3A8A;">{{ formatIndianCurrency($sAmt) }}</td>
        </tr>
        <tr>
            <td>Total Amount Realized So Far (Credits)</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($bAmt) }}</td>
        </tr>
        <tr class="double-total">
            <td><strong>Remaining Balance Outstanding Due (Sundry Debtors)</strong></td>
            <td class="r" style="color:{{ $rAmt > 0 ? '#DC2626' : '#10B981' }}; font-size:11px;"><strong>{{ formatIndianCurrency($rAmt) }}</strong></td>
        </tr>
    </tbody>
</table>

@if($sale->payments && $sale->payments->count() > 0)
<div class="section-label" style="margin-top: 15px;"><i class="fa-solid fa-clock-rotate-left"></i> Realized Payment Installments History</div>
<table>
    <thead>
        <tr>
            <th style="width: 28px;" class="c">#</th>
            <th style="width: 85px;">Payment Date</th>
            <th style="width: 85px;">Payment Mode</th>
            <th>Transaction Reference / Instrument</th>
            <th class="r" style="width: 120px;">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sale->payments as $pIdx => $pmt)
        <tr>
            <td class="c" style="color:#64748B;">{{ $pIdx + 1 }}</td>
            <td style="white-space:nowrap; color:#475569;">{{ $pmt->payment_date ? \Carbon\Carbon::parse($pmt->payment_date)->format('d-M-Y') : '—' }}</td>
            <td style="text-transform:capitalize; color:#475569;">{{ str_replace('_', ' ', $pmt->payment_mode ?: 'Cash') }}</td>
            <td>{{ $pmt->transaction_ref ?: 'Direct Settlement' }}</td>
            <td class="r" style="font-weight: 700; color: #059669;">{{ formatIndianCurrency($pmt->payment_amount ?? 0, 2, false) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="4" class="r"><strong>TOTAL INSTALLMENTS REALIZED:</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($sale->payments->sum('payment_amount')) }}</strong></td>
        </tr>
    </tfoot>
</table>
@endif

@php $hasCustomAuthBlock = true; @endphp
@include('admin.components.pdf-signature', [
    'leftRole' => 'Purchaser Signature',
    'rightRole' => 'Authorized Signatory / ' . ($sale->firm->firm_name ?? 'Delawala Infrastructure'),
    'firm' => $sale->firm ?: ($sale->property?->project?->firm)
])

@include('admin.components.pdf-footer')
</body>
</html>