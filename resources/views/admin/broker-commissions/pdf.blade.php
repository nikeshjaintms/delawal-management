<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Broker Commissions Statement - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Broker Commissions Statement',
    'orientation' => 'landscape',
    'backUrl' => route('broker-commissions.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Broker Commissions Statement',
    'subtitle' => 'Deal Brokerages & Payout Ledger'
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Commission Accrued</div>
        <div class="s-value">₹{{ number_format($totalCommission ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Commission Paid</div>
        <div class="s-value">₹{{ number_format($paidCommission ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Pending Commission</div>
        <div class="s-value">₹{{ number_format($pendingCommission ?? 0, 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-handshake"></i> Commission Settlements Register</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Broker Name</th>
            <th>Property / Deal</th>
            <th>Customer</th>
            <th>Deal Date</th>
            <th class="r">Deal Value</th>
            <th class="r">Commission (₹)</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($commissions as $i => $c)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $c->broker->name ?? '—' }}</strong></td>
            <td>{{ $c->property->property_name ?? '—' }}</td>
            <td>{{ $c->customer->name ?? '—' }}</td>
            <td>{{ $c->deal_date ? \Carbon\Carbon::parse($c->deal_date)->format('d M Y') : '—' }}</td>
            <td class="r">₹{{ number_format($c->deal_amount ?? 0, 2) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">₹{{ number_format($c->commission_amount ?? 0, 2) }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($c->status ?? 'Paid') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:20px;color:#64748B;">No commission records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>