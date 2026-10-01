<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentals & Tenancies Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Rentals & Tenancies Report',
    'orientation' => 'landscape',
    'backUrl' => route('rentals.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Rentals & Tenancies Report',
    'subtitle' => 'Lease Agreements & Monthly Rent Tracker'
])

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Tenancies</div>
        <div class="s-value">{{ $totalRentalsCount ?? ($totalRentals ?? $rentals->count()) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Leases</div>
        <div class="s-value">{{ $activeRentalsCount ?? ($activeCount ?? $rentals->count()) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Monthly Rent Total</div>
        <div class="s-value">₹{{ number_format($totalMonthlyRent ?? $rentals->sum('monthly_rent'), 2) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Security Deposits</div>
        <div class="s-value">₹{{ number_format($totalDeposit ?? $rentals->sum('security_deposit'), 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-key"></i> Tenancy Agreements Register</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Agreement No</th>
            <th>Tenant Name</th>
            <th>Property / Unit</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th class="r">Monthly Rent</th>
            <th class="r">Security Deposit</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rentals as $i => $r)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $r->agreement_no ?: ('RA-' . $r->id) }}</strong></td>
            <td>{{ $r->tenant_name ?? ($r->tenant->name ?? '—') }}</td>
            <td>{{ $r->property->property_name ?? '—' }}</td>
            <td>{{ $r->start_date ? \Carbon\Carbon::parse($r->start_date)->format('d M Y') : '—' }}</td>
            <td>{{ $r->end_date ? \Carbon\Carbon::parse($r->end_date)->format('d M Y') : '—' }}</td>
            <td class="r" style="color:#2563EB; font-weight:700;">₹{{ number_format($r->monthly_rent, 2) }}</td>
            <td class="r">₹{{ number_format($r->security_deposit, 2) }}</td>
            <td class="c"><span class="badge badge-success">Active</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:20px;color:#64748B;">No rental agreements found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>