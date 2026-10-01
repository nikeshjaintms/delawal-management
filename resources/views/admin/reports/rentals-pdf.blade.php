<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Portfolio Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Rental Portfolio Report',
    'orientation' => 'landscape',
    'backUrl' => route('reports.rentals')
])

@include('admin.components.pdf-header', [
    'title' => 'Rental Portfolio Report',
    'subtitle' => 'Rental Income & Occupancy Ledger'
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Monthly Rent Demand</div>
        <div class="s-value">₹{{ number_format($totalRentAmt ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Total Rent Collected</div>
        <div class="s-value">₹{{ number_format($totalReceived ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total Rent Due</div>
        <div class="s-value">₹{{ number_format($totalPending ?? 0, 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-house-user"></i> Rental Tenancy Agreements</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Agreement No</th>
            <th>Tenant Name</th>
            <th>Property / Unit</th>
            <th>Start Date</th>
            <th class="r">Monthly Rent</th>
            <th class="r">Security Deposit</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $i => $r)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $r->agreement_no ?: ('RA-' . $r->id) }}</strong></td>
            <td>{{ $r->tenant_name ?? ($r->tenant->name ?? '—') }}</td>
            <td>{{ $r->property->property_name ?? '—' }}</td>
            <td>{{ $r->start_date ? \Carbon\Carbon::parse($r->start_date)->format('d M Y') : '—' }}</td>
            <td class="r" style="color:#2563EB; font-weight:700;">₹{{ number_format($r->monthly_rent, 2) }}</td>
            <td class="r">₹{{ number_format($r->security_deposit, 2) }}</td>
            <td class="c"><span class="badge badge-success">Active</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:20px;color:#64748B;">No rental portfolio records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>