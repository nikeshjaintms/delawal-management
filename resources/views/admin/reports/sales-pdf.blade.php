<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Accounting Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Sales Accounting Report',
    'orientation' => 'landscape',
    'backUrl' => route('reports.sales')
])

@include('admin.components.pdf-header', [
    'title' => 'Sales Accounting Report',
    'subtitle' => 'Turnover, Realization & Profit Analysis'
])

<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Total Turnover / Sales</div>
        <div class="s-value">₹{{ number_format($totalSale ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Amount Realized</div>
        <div class="s-value">₹{{ number_format($totalReceived ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Pending Recovery</div>
        <div class="s-value">₹{{ number_format($totalPending ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Net Sales Profit</div>
        <div class="s-value">₹{{ number_format($totalNetProfit ?? 0, 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-chart-line"></i> Property Sales &amp; Financial Realization</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Agreement No</th>
            <th>Customer Name</th>
            <th>Property / Unit</th>
            <th>Sale Date</th>
            <th class="r">Sale Amount</th>
            <th class="r">Realized Amount</th>
            <th class="r">Pending Balance</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $i => $s)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $s->agreement_no ?: ('SALE-' . $s->id) }}</strong></td>
            <td>{{ $s->customer->name ?? '—' }}</td>
            <td>{{ $s->property->property_name ?? '—' }}</td>
            <td>{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '—' }}</td>
            <td class="r">₹{{ number_format($s->sale_amount, 2) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">₹{{ number_format($s->booking_amount, 2) }}</td>
            <td class="r" style="color:#B45309; font-weight:700;">₹{{ number_format($s->remaining_amount, 2) }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($s->sale_status ?? 'Completed') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:20px;color:#64748B;">No property sales records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>