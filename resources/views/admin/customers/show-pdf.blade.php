<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Account Statement & Dossier - {{ $customer->name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Customer Statement: ' . $customer->name,
    'orientation' => 'portrait',
    'backUrl' => isset($customer->id) ? route('customers.show', $customer->id) : route('customers.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Customer Account Statement & Dossier',
    'subtitle' => 'Comprehensive Purchaser Ledger, Property Holdings & Outstanding Dues',
    'firm' => $customer->firm ?? null,
    'docRef' => 'CUST-' . str_pad($customer->id ?? 1, 5, '0', STR_PAD_LEFT)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Customer / Purchaser</div>
        <div class="s-value" style="font-size:13px;">{{ $customer->name }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Primary Contact</div>
        <div class="s-value" style="font-size:12px;">{{ $customer->mobile ?: '—' }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Associated Firm</div>
        <div class="s-value" style="font-size:12px;">{{ $customer->firm->firm_name ?? 'Delawala Management' }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">KYC Status</div>
        <div class="s-value" style="font-size:12px;">{{ strtoupper($customer->status ?: 'ACTIVE') }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-address-card"></i> Customer Particulars</div>
        <div class="info-row"><span class="info-label">Customer Name:</span><span class="info-val"><strong>{{ $customer->name }}</strong></span></div>
        <div class="info-row"><span class="info-label">Primary Mobile:</span><span class="info-val">{{ $customer->mobile ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Alternate Mobile:</span><span class="info-val">{{ $customer->alternate_mobile ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Email Address:</span><span class="info-val">{{ $customer->email ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Entity Type:</span><span class="info-val">{{ ucfirst($customer->customer_type ?: 'Individual') }}</span></div>
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Billing Address &amp; Registration</div>
        <div class="info-row"><span class="info-label">Full Address:</span><span class="info-val">{{ $customer->address ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">City:</span><span class="info-val">{{ $customer->city ?: 'Dahegam, Gujarat' }}</span></div>
        <div class="info-row"><span class="info-label">Registered Firm:</span><span class="info-val">{{ $customer->firm->firm_name ?? 'Delawala Management' }}</span></div>
        <div class="info-row"><span class="info-label">Account Enrolled:</span><span class="info-val">{{ $customer->created_at ? $customer->created_at->format('d M Y') : '—' }}</span></div>
    </div>
</div>

@if($customer->propertySales && $customer->propertySales->isNotEmpty())
@php
    $totSalesVal = $customer->propertySales->sum('sale_amount');
    $totSalesPaid = $customer->propertySales->sum('booking_amount');
    $totSalesDue = $customer->propertySales->sum('remaining_amount');
@endphp
<div class="section-label"><i class="fa-solid fa-file-contract"></i> Executed Conveyance Deeds &amp; Sales Purchases</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Property / Unit Particulars</th>
            <th style="width:75px;">Sale Date</th>
            <th class="r" style="width:105px;">Agreed Value (₹)</th>
            <th class="r" style="width:105px;">Paid to Date (₹)</th>
            <th class="r" style="width:105px;">Due Balance (₹)</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customer->propertySales as $i => $s)
        @php
            $sAmt = (float)($s->sale_amount ?? 0);
            $pAmt = (float)($s->booking_amount ?? 0);
            $dAmt = (float)($s->remaining_amount ?? max(0, $sAmt - $pAmt));
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td>
                <strong>{{ $s->property->property_name ?? '—' }}</strong>
                @if(!empty($s->agreement_no))
                    <div style="font-size:8px; color:#64748B;">Deed No: {{ $s->agreement_no }}</div>
                @endif
            </td>
            <td style="white-space:nowrap; color:#475569;">{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d-M-Y') : '—' }}</td>
            <td class="r" style="font-weight:600; color:#1E3A8A;">{{ formatIndianCurrency($sAmt, 2, false) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($pAmt, 2, false) }}</td>
            <td class="r" style="color:{{ $dAmt > 0 ? '#DC2626' : '#10B981' }}; font-weight:700;">{{ formatIndianCurrency($dAmt, 2, false) }}</td>
            <td class="c"><span class="badge badge-info">{{ ucfirst($s->sale_status) }}</span></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="3" class="r"><strong>SUBTOTAL CONVEYANCE DEEDS:</strong></td>
            <td class="r" style="color:#1E3A8A; font-size:11px;"><strong>{{ formatIndianCurrency($totSalesVal) }}</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($totSalesPaid) }}</strong></td>
            <td class="r" style="color:#DC2626; font-size:11px;"><strong>{{ formatIndianCurrency($totSalesDue) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>
@endif

@if($customer->bookings && $customer->bookings->isNotEmpty())
@php
    $totBkVal = $customer->bookings->sum('final_amount');
    $totBkAdv = $customer->bookings->sum('booking_amount');
    $totBkDue = $customer->bookings->sum('remaining_amount');
@endphp
<div class="section-label"><i class="fa-solid fa-bookmark"></i> Property Bookings &amp; Advance Tokens</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Booking Reference &amp; Units</th>
            <th style="width:75px;">Booking Date</th>
            <th class="r" style="width:105px;">Agreed Price (₹)</th>
            <th class="r" style="width:105px;">Token Advance (₹)</th>
            <th class="r" style="width:105px;">Pending Due (₹)</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customer->bookings as $i => $b)
        @php
            $fAmt = (float)($b->final_amount ?? 0);
            $aAmt = (float)($b->booking_amount ?? 0);
            $pAmt = (float)($b->remaining_amount ?? max(0, $fAmt - $aAmt));
            $allP = $b->properties->isNotEmpty() ? $b->properties : ($b->property ? collect([$b->property]) : collect([]));
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td>
                <strong>{{ $b->booking_code ?: ('BK-' . $b->id) }}</strong>
                @foreach($allP as $p)
                    <div style="font-size:8.5px; color:#475569;">• {{ $p->property_name }} @if($p->unit_no)({{ $p->unit_no }})@endif</div>
                @endforeach
            </td>
            <td style="white-space:nowrap; color:#475569;">{{ $b->booking_date ? \Carbon\Carbon::parse($b->booking_date)->format('d-M-Y') : '—' }}</td>
            <td class="r" style="font-weight:600; color:#1E3A8A;">{{ formatIndianCurrency($fAmt, 2, false) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($aAmt, 2, false) }}</td>
            <td class="r" style="color:{{ $pAmt > 0 ? '#DC2626' : '#10B981' }}; font-weight:700;">{{ formatIndianCurrency($pAmt, 2, false) }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($b->status) }}</span></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="3" class="r"><strong>SUBTOTAL PROPERTY BOOKINGS:</strong></td>
            <td class="r" style="color:#1E3A8A; font-size:11px;"><strong>{{ formatIndianCurrency($totBkVal) }}</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($totBkAdv) }}</strong></td>
            <td class="r" style="color:#DC2626; font-size:11px;"><strong>{{ formatIndianCurrency($totBkDue) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>
@endif

<div style="margin-top:14px; font-size:9px; color:#64748B; line-height:1.4;">
    <strong>Customer Ledger Note:</strong> This statement reflects all finalized property conveyances and active allotment bookings for the account. Outstanding balances represent Sundry Debtors dues.
</div>

@include('admin.components.pdf-footer')
</body>
</html>
