<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Sales Registry Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Property Sales Registry Report',
    'orientation' => 'landscape',
    'backUrl' => route('property-sales.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Property Sales Registry Report',
    'subtitle' => 'Deeds, Agreements & Payment Status'
])

@php
    $allSales = $sales ?? ($propertySales ?? collect([]));
@endphp

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Deals</div>
        <div class="s-value">{{ $totalSalesCount ?? ($totalSales ?? $allSales->count()) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Total Sale Value</div>
        <div class="s-value">₹{{ number_format($totalSaleAmount ?? $allSales->sum('sale_amount'), 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Amount Received</div>
        <div class="s-value">₹{{ number_format($totalBookingAmount ?? ($totalReceived ?? $allSales->sum('booking_amount')), 2) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Outstanding Balance</div>
        <div class="s-value">₹{{ number_format($totalRemaining ?? $allSales->sum('remaining_amount'), 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-file-contract"></i> Conveyance Deeds &amp; Sales Ledger</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Agreement No</th>
            <th>Customer Name</th>
            <th>Property / Unit</th>
            <th>Sale Date</th>
            <th class="r">Sale Amount</th>
            <th class="r">Paid Amount</th>
            <th class="r">Due Amount</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($allSales as $i => $s)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $s->agreement_no ?: ('SALE-' . $s->id) }}</strong></td>
            <td>{{ $s->customer->name ?? '—' }}</td>
            <td>{{ $s->property->property_name ?? '—' }}</td>
            <td>{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '—' }}</td>
            <td class="r">₹{{ number_format($s->sale_amount, 2) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">₹{{ number_format($s->booking_amount, 2) }}</td>
            <td class="r" style="color:#B45309; font-weight:700;">₹{{ number_format($s->remaining_amount, 2) }}</td>
            <td class="c"><span class="badge badge-info">{{ ucfirst($s->sale_status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:20px;color:#64748B;">No property sales found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>