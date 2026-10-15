<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentals & Tenancies Master Register - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Rentals & Tenancies Master Register',
    'orientation' => 'landscape',
    'backUrl' => route('rentals.index')
])

@php
    $totRent = 0;
    $totDeposit = 0;
    foreach($rentals as $r) {
        $totRent += (float)($r->rent_amount ?? ($r->monthly_rent ?? 0));
        $totDeposit += (float)($r->security_deposit ?? ($r->deposit_amount ?? 0));
    }

    $filters = [];
    if(request('search')) {
        $filters['Search Keyword'] = request('search');
    }
    if(request('status') || request('rental_status')) {
        $filters['Status'] = ucfirst(request('rental_status') ?: request('status'));
    }
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Rentals & Tenancies Master Register',
    'subtitle' => 'Active Lease Agreements, Monthly Rent Accruals & Security Deposits',
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Tenancy Deeds</div>
        <div class="s-value">{{ $totalRentalsCount ?? ($totalRentals ?? $rentals->count()) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Leases</div>
        <div class="s-value">{{ $activeRentalsCount ?? $rentals->where('rental_status', 'active')->count() }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Monthly Rent Accrual</div>
        <div class="s-value">{{ formatIndianCurrency($totRent) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Held Security Deposits</div>
        <div class="s-value">{{ formatIndianCurrency($totDeposit) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-key"></i> Tenancy Agreements Register</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:90px;">Agreement No</th>
            <th>Tenant Name &amp; Contact</th>
            <th>Leased Property / Unit</th>
            <th style="width:75px;">Lease Start</th>
            <th style="width:75px;">Lease End</th>
            <th class="r" style="width:115px;">Monthly Rent (₹)</th>
            <th class="r" style="width:115px;">Security Deposit (₹)</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rentals as $i => $r)
        @php
            $rAmt = (float)($r->rent_amount ?? ($r->monthly_rent ?? 0));
            $sDep = (float)($r->security_deposit ?? ($r->deposit_amount ?? 0));
            $sDate = $r->rent_start_date ?: ($r->start_date ?: null);
            $eDate = $r->rent_end_date ?: ($r->end_date ?: null);
            $st = $r->rental_status ?: ($r->status ?: 'Active');
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="font-weight:700; color:#1E3A8A;">{{ $r->agreement_no ?: ('RA-' . $r->id) }}</td>
            <td>
                <strong>{{ $r->tenant_name ?? ($r->tenant->name ?? '—') }}</strong>
                @if(!empty($r->tenant_mobile))
                    <div style="font-size:8px; color:#64748B;">{{ $r->tenant_mobile }}</div>
                @endif
            </td>
            <td>{{ $r->property->property_name ?? '—' }}</td>
            <td style="white-space:nowrap; color:#475569;">{{ $sDate ? \Carbon\Carbon::parse($sDate)->format('d-M-Y') : '—' }}</td>
            <td style="white-space:nowrap; color:#475569;">{{ $eDate ? \Carbon\Carbon::parse($eDate)->format('d-M-Y') : '—' }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($rAmt, 2, false) }}</td>
            <td class="r" style="font-weight:600; color:#0F172A;">{{ formatIndianCurrency($sDep, 2, false) }}</td>
            <td class="c">
                @if(strtolower($st) === 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-neutral">{{ ucfirst($st) }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:24px; color:#64748B;"><em>No rental agreements found matching the filter parameters.</em></td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="6" class="r"><strong>CONSOLIDATED TENANCY PORTFOLIO TOTALS:</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($totRent) }}</strong></td>
            <td class="r" style="color:#0F172A; font-size:11px;"><strong>{{ formatIndianCurrency($totDeposit) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>

<div style="margin-top:14px; font-size:9px; color:#64748B; line-height:1.4;">
    <strong>Accounting Ledger Note:</strong> Security deposits held from tenants are classified under Current Liabilities in the Balance Sheet until lease termination or refund.
</div>

@include('admin.components.pdf-footer')
</body>
</html>