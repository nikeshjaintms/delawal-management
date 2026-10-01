<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dossier - {{ $customer->name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Customer Dossier: ' . $customer->name,
    'orientation' => 'portrait',
    'backUrl' => isset($customer->id) ? route('customers.show', $customer->id) : route('customers.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Customer Dossier & Profile',
    'subtitle' => 'Account Ledger & Transaction History',
    'firm' => $customer->firm ?? null,
    'docRef' => 'CUST-' . ($customer->id ?? 1)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Customer Name</div>
        <div class="s-value" style="font-size:14px;">{{ $customer->name }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Associated Firm</div>
        <div class="s-value" style="font-size:13px;">{{ $customer->firm->firm_name ?? 'Delawala Management' }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Account Status</div>
        <div class="s-value" style="font-size:13px;">{{ strtoupper($customer->status ?: 'ACTIVE') }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-address-card"></i> Contact Information</div>
        <div class="info-row"><span class="info-label">Customer Name:</span><span class="info-val">{{ $customer->name }}</span></div>
        <div class="info-row"><span class="info-label">Primary Mobile:</span><span class="info-val">{{ $customer->mobile ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Alternate Mobile:</span><span class="info-val">{{ $customer->alternate_mobile ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Email Address:</span><span class="info-val">{{ $customer->email ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Customer Type:</span><span class="info-val">{{ ucfirst($customer->customer_type ?: 'Individual') }}</span></div>
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Location &amp; Registration</div>
        <div class="info-row"><span class="info-label">Full Address:</span><span class="info-val">{{ $customer->address ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">City:</span><span class="info-val">{{ $customer->city ?: 'Dahegam' }}</span></div>
        <div class="info-row"><span class="info-label">Firm Link:</span><span class="info-val">{{ $customer->firm->firm_name ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Created At:</span><span class="info-val">{{ $customer->created_at ? $customer->created_at->format('d M Y, h:i A') : '—' }}</span></div>
    </div>
</div>

@if($customer->propertySales && $customer->propertySales->isNotEmpty())
<div class="section-label"><i class="fa-solid fa-file-contract"></i> Property Purchases &amp; Sales Deeds</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Property / Unit</th>
            <th>Sale Date</th>
            <th class="r">Total Amount</th>
            <th class="r">Paid Amount</th>
            <th class="r">Due Balance</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customer->propertySales as $i => $s)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $s->property->property_name ?? '—' }}</strong></td>
            <td>{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '—' }}</td>
            <td class="r">₹{{ number_format($s->sale_amount, 2) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">₹{{ number_format($s->booking_amount, 2) }}</td>
            <td class="r" style="color:#B45309; font-weight:700;">₹{{ number_format($s->remaining_amount, 2) }}</td>
            <td class="c"><span class="badge badge-info">{{ ucfirst($s->sale_status) }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if($customer->bookings && $customer->bookings->isNotEmpty())
<div class="section-label"><i class="fa-solid fa-bookmark"></i> Property Bookings &amp; Token Advances</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Property / Unit</th>
            <th>Booking Date</th>
            <th class="r">Final Price</th>
            <th class="r">Token Advance</th>
            <th class="r">Pending Due</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customer->bookings as $i => $b)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td>
                @php $allP = $b->properties->isNotEmpty() ? $b->properties : ($b->property ? collect([$b->property]) : collect([])); @endphp
                @if($allP->count() > 1)
                    @foreach($allP as $p)
                        <div>• <strong>{{ $p->property_name }}</strong> @if($p->unit_no)<span style="color:#64748B;">({{ $p->unit_no }})</span>@endif</div>
                    @endforeach
                @else
                    <strong>{{ $b->property->property_name ?? '—' }}</strong>
                    @if($b->property?->unit_no) <span style="color:#64748B;">({{ $b->property->unit_no }})</span> @endif
                @endif
            </td>
            <td>{{ $b->booking_date ? \Carbon\Carbon::parse($b->booking_date)->format('d M Y') : '—' }}</td>
            <td class="r">₹{{ number_format($b->final_amount, 2) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">₹{{ number_format($b->booking_amount, 2) }}</td>
            <td class="r" style="color:#B45309; font-weight:700;">₹{{ number_format($b->remaining_amount, 2) }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($b->status) }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@include('admin.components.pdf-footer')
</body>
</html>
