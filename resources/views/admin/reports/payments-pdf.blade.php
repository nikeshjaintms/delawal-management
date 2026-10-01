<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments & Collections Register - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Payments & Collections Register',
    'orientation' => 'landscape',
    'backUrl' => route('reports.payments')
])

@include('admin.components.pdf-header', [
    'title' => 'Payments & Collections Register',
    'subtitle' => 'Receipts, Inflows & Cash Flow Statement'
])

<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Total Realized / Received</div>
        <div class="s-value">₹{{ number_format($totalReceived ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total Pending Due</div>
        <div class="s-value">₹{{ number_format($totalPending ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Total Transactions</div>
        <div class="s-value">{{ $totalTransactions ?? $records->count() }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-money-bill-transfer"></i> Inflow Transactions Ledger</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Transaction / Receipt</th>
            <th>Date</th>
            <th>Customer / Payer</th>
            <th>Property / Unit</th>
            <th>Payment Mode</th>
            <th class="r">Amount (₹)</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $i => $r)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $r->receipt_no ?? ($r->payment_code ?? ('TXN-'.$r->id)) }}</strong></td>
            <td>{{ $r->payment_date ? \Carbon\Carbon::parse($r->payment_date)->format('d M Y') : '—' }}</td>
            <td>{{ $r->customer->name ?? ($r->tenant_name ?? '—') }}</td>
            <td>{{ $r->property->property_name ?? '—' }}</td>
            <td>{{ ucfirst($r->payment_mode ?: 'Cash') }}</td>
            <td class="r" style="color:#059669; font-weight:800;">₹{{ number_format($r->amount ?? ($r->paid_amount ?? 0), 2) }}</td>
            <td class="c"><span class="badge badge-success">Received</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:20px;color:#64748B;">No payment transactions recorded.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>