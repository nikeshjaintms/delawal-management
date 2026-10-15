<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Land Seller Account Statement & Dossier - {{ $seller->name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Seller Statement: ' . $seller->name,
    'orientation' => 'landscape',
    'backUrl' => isset($seller->id) ? route('sellers.show', $seller->id) : route('sellers.index')
])

@php
    $totPurchaseCost = 0;
    $totPaid = 0;
    $totPayable = 0;

    foreach($seller->propertyMasters as $pm) {
        $cost = (float)($pm->purchase_price ?? 0);
        $paid = (float)$pm->payments->sum('amount');
        $due = max(0, $cost - $paid);

        $totPurchaseCost += $cost;
        $totPaid += $paid;
        $totPayable += $due;
    }
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Land Seller Statement of Account & Acquisition Ledger',
    'subtitle' => 'Land Purchases, Acquisition Deeds, Disbursed Payments & Balance Payable',
    'firm' => $seller->firm ?? null,
    'docRef' => 'SELLER-' . str_pad($seller->id ?? 1, 5, '0', STR_PAD_LEFT)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Seller / Land Owner</div>
        <div class="s-value" style="font-size:13px;">{{ $seller->name }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Total Land Consideration</div>
        <div class="s-value">{{ formatIndianCurrency($totPurchaseCost) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Disbursed Purchase Outflow</div>
        <div class="s-value">{{ formatIndianCurrency($totPaid) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Outstanding Land Payable</div>
        <div class="s-value">{{ formatIndianCurrency($totPayable) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user"></i> Seller Particulars</div>
        <div class="info-row"><span class="info-label">Full Name:</span><span class="info-val"><strong>{{ $seller->name }}</strong></span></div>
        <div class="info-row"><span class="info-label">Mobile / Phone:</span><span class="info-val">{{ $seller->mobile ?: ($seller->phone ?: '—') }}</span></div>
        <div class="info-row"><span class="info-label">PAN Number:</span><span class="info-val"><strong>{{ $seller->pan_no ?: '—' }}</strong></span></div>
        <div class="info-row"><span class="info-label">Bank Details:</span><span class="info-val">{{ $seller->bank_name ?: '—' }} @if($seller->account_number)(A/C: ...{{ substr($seller->account_number, -4) }})@endif</span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Address & Registration</div>
        <div class="info-row"><span class="info-label">Address:</span><span class="info-val">{{ $seller->address ?: 'Dahegam, Bharuch' }}</span></div>
        <div class="info-row"><span class="info-label">City / State:</span><span class="info-val">{{ $seller->city ?: 'Dahegam' }}, {{ $seller->state ?: 'Gujarat' }}</span></div>
        <div class="info-row"><span class="info-label">Acquiring Firm:</span><span class="info-val">{{ $seller->firm->firm_name ?? 'Delawala Infrastructure' }}</span></div>
        <div class="info-row"><span class="info-label">Status:</span><span class="info-val"><span class="badge badge-success">{{ strtoupper($seller->status ?: 'ACTIVE') }}</span></span></div>
    </div>
</div>

@if($seller->propertyMasters && $seller->propertyMasters->isNotEmpty())
<div class="section-label"><i class="fa-solid fa-map-location-dot"></i> Acquired Land Properties &amp; Consideration Schedule</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Property / Survey Particulars</th>
            <th>Total Land Area</th>
            <th class="r" style="width:120px;">Acquisition Cost (₹)</th>
            <th class="r" style="width:120px;">Paid to Date (₹)</th>
            <th class="r" style="width:120px;">Balance Due (₹)</th>
            <th class="c" style="width:80px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($seller->propertyMasters as $i => $pm)
        @php
            $cost = (float)($pm->purchase_price ?? 0);
            $paid = (float)$pm->payments->sum('amount');
            $due = max(0, $cost - $paid);
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td>
                <strong>{{ $pm->property_name }}</strong>
                @if(!empty($pm->survey_number))
                    <div style="font-size:8px; color:#64748B;">Survey/Khasra: {{ $pm->survey_number }}</div>
                @endif
            </td>
            <td>{{ $pm->total_area ? ($pm->total_area . ' ' . ($pm->area_unit ?: 'Vigha/Sq.Yd')) : '—' }}</td>
            <td class="r" style="font-weight:700; color:#1E3A8A;">{{ formatIndianCurrency($cost, 2, false) }}</td>
            <td class="r" style="font-weight:700; color:#059669;">{{ formatIndianCurrency($paid, 2, false) }}</td>
            <td class="r" style="font-weight:700; color:{{ $due > 0 ? '#DC2626' : '#10B981' }};">{{ formatIndianCurrency($due, 2, false) }}</td>
            <td class="c">
                @if($due <= 0 && $cost > 0)
                    <span class="badge badge-success">Settled</span>
                @elseif($paid > 0)
                    <span class="badge badge-warning">Partial</span>
                @else
                    <span class="badge badge-danger">Unpaid</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="3" class="r"><strong>TOTAL LAND ACQUISITION COMMITMENTS:</strong></td>
            <td class="r" style="color:#1E3A8A; font-size:11px;"><strong>{{ formatIndianCurrency($totPurchaseCost) }}</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($totPaid) }}</strong></td>
            <td class="r" style="color:#DC2626; font-size:11px;"><strong>{{ formatIndianCurrency($totPayable) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>
@endif

<div style="margin-top:14px; font-size:9px; color:#64748B; line-height:1.4;">
    <strong>Accounting Ledger Note:</strong> Outstanding balance payable to land owners is classified under Current Liabilities as Land Creditors / Sundry Payables in the Balance Sheet.
</div>

@include('admin.components.pdf-footer')
</body>
</html>