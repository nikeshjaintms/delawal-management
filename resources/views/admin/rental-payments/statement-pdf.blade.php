<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenancy Ledger Statement - {{ $rental->tenant_name ?? ($rental->tenant->name ?? 'Tenant') }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Tenancy Statement: ' . ($rental->tenant_name ?? ($rental->tenant->name ?? 'Tenant')),
    'orientation' => 'landscape',
    'backUrl' => isset($rental->id) ? route('rental-payments.index', $rental->id) : route('rentals.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Tenancy Account Statement',
    'subtitle' => 'Rent Accrual & Collection Ledger',
    'docRef' => $rental->agreement_no ?? ('RA-' . ($rental->id ?? 1))
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Billed Rent</div>
        <div class="s-value">₹{{ number_format($totalRent ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Total Paid</div>
        <div class="s-value">₹{{ number_format($totalPaid ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total Pending Due</div>
        <div class="s-value">₹{{ number_format($totalPending ?? 0, 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-list"></i> Payment Transactions Ledger</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Month / Year</th>
            <th>Payment Date</th>
            <th>Payment Mode</th>
            <th class="r">Rent Amount</th>
            <th class="r">Paid Amount</th>
            <th class="r">Pending Due</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payments as $i => $p)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $p->payment_month }} {{ $p->payment_year }}</strong></td>
            <td>{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d M Y') : '—' }}</td>
            <td>{{ ucfirst($p->payment_mode ?: 'Cash') }}</td>
            <td class="r">₹{{ number_format($p->rent_amount, 2) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">₹{{ number_format($p->paid_amount, 2) }}</td>
            <td class="r" style="color:{{ $p->pending_amount > 0 ? '#DC2626' : '#64748B' }}; font-weight:700;">₹{{ number_format($p->pending_amount, 2) }}</td>
            <td class="c">
                @if($p->payment_status === 'paid')
                    <span class="badge badge-success">Paid</span>
                @elseif($p->payment_status === 'partial')
                    <span class="badge badge-warning">Partial</span>
                @else
                    <span class="badge badge-danger">Pending</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:20px;color:#64748B;">No payment transactions found.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="r">Totals</td>
            <td class="r">₹{{ number_format($totalRent ?? 0, 2) }}</td>
            <td class="r" style="color:#059669;">₹{{ number_format($totalPaid ?? 0, 2) }}</td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($totalPending ?? 0, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

@include('admin.components.pdf-footer')
</body>
</html>