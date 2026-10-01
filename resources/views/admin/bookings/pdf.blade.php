<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Directory Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Bookings Directory Report',
    'orientation' => 'landscape',
    'backUrl' => route('bookings.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Bookings Directory Report',
    'subtitle' => 'Advance Tokens & Property Bookings Registry'
])

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Bookings</div>
        <div class="s-value">{{ $totalCount ?? $bookings->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Token Advance Received</div>
        <div class="s-value">₹{{ number_format($totalAdvance ?? $bookings->sum('booking_amount'), 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Total Booking Value</div>
        <div class="s-value">₹{{ number_format($totalFinalAmount ?? ($totalValue ?? $bookings->sum('final_amount')), 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-bookmark"></i> Property Bookings Registry</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Booking Code</th>
            <th>Customer Name</th>
            <th>Property / Unit</th>
            <th>Booking Date</th>
            <th class="r">Final Amount</th>
            <th class="r">Token Advance</th>
            <th class="r">Pending Due</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bookings as $i => $b)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $b->booking_code ?: ('BK-' . $b->id) }}</strong></td>
            <td>{{ $b->customer->name ?? '—' }}</td>
            <td>{{ $b->property->property_name ?? '—' }}</td>
            <td>{{ $b->booking_date ? \Carbon\Carbon::parse($b->booking_date)->format('d M Y') : '—' }}</td>
            <td class="r">₹{{ number_format($b->final_amount, 2) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">₹{{ number_format($b->booking_amount, 2) }}</td>
            <td class="r" style="color:#B45309; font-weight:700;">₹{{ number_format($b->remaining_amount, 2) }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($b->status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:20px;color:#64748B;">No booking records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>