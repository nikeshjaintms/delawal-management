<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenancy Account Ledger Statement - {{ $rental->tenant_name ?? ($rental->tenant?->name ?? 'Tenant') }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Tenancy Statement: ' . ($rental->tenant_name ?? ($rental->tenant?->name ?? 'Tenant')),
    'orientation' => 'landscape',
    'backUrl' => isset($rental->id) ? route('rental-payments.index', $rental->id) : route('rentals.index')
])

@php
    $tRent = (float)($totalRent ?? 0);
    $tPaid = (float)($totalPaid ?? 0);
    $tPending = (float)($totalPending ?? 0);
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Tenancy Account Ledger & Statement of Rent',
    'subtitle' => 'Monthly Accruals, Realized Collections & Outstanding Lease Receivables',
    'docRef' => $rental->agreement_no ?? ('RA-' . ($rental->id ?? 1))
])

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user"></i> Tenant Particulars</div>
        <div class="info-row"><span class="info-label">Tenant Name:</span><span class="info-val"><strong>{{ $rental->tenant_name ?? ($rental->tenant?->name ?? '—') }}</strong></span></div>
        <div class="info-row"><span class="info-label">Contact / Phone:</span><span class="info-val">{{ $rental->tenant_phone ?? ($rental->tenant?->phone ?? '—') }}</span></div>
        <div class="info-row"><span class="info-label">Email:</span><span class="info-val">{{ $rental->tenant_email ?? ($rental->tenant?->email ?? '—') }}</span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building"></i> Tenancy Particulars & Terms</div>
        <div class="info-row"><span class="info-label">Agreement No:</span><span class="info-val" style="color:#1E3A8A; font-weight:700;">{{ $rental->agreement_no ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Property / Unit:</span><span class="info-val">{{ $rental->property->property_name ?? ($rental->property->property_code ?? '—') }}</span></div>
        <div class="info-row"><span class="info-label">Monthly Rent:</span><span class="info-val" style="font-weight:700; color:#059669;">{{ formatIndianCurrency($rental->rent_amount ?? 0) }} / month</span></div>
        <div class="info-row"><span class="info-label">Security Deposit:</span><span class="info-val">{{ formatIndianCurrency($rental->deposit_amount ?? 0) }}</span></div>
    </div>
</div>

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Billed Accrual</div>
        <div class="s-value">{{ formatIndianCurrency($tRent) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Total Realized Collections</div>
        <div class="s-value">{{ formatIndianCurrency($tPaid) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Outstanding Receivables Due</div>
        <div class="s-value">{{ formatIndianCurrency($tPending) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-list-check"></i> Monthly Rental Ledger &amp; Collections Audit Trail</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th style="width:110px;">Billing Period</th>
            <th style="width:85px;">Payment Date</th>
            <th style="width:85px;">Payment Mode</th>
            <th class="r" style="width:110px;">Billed Rent (₹)</th>
            <th class="r" style="width:110px;">Paid Amount (₹)</th>
            <th class="r" style="width:110px;">Balance Due (₹)</th>
            <th class="c" style="width:80px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payments as $i => $p)
        @php
            $rAmt = (float)($p->rent_amount ?? 0);
            $pdAmt = (float)($p->paid_amount ?? 0);
            $duAmt = (float)($p->pending_amount ?? max(0, $rAmt - $pdAmt));
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $p->payment_month }} {{ $p->payment_year }}</strong></td>
            <td style="white-space:nowrap; color:#475569;">{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d-M-Y') : '—' }}</td>
            <td style="text-transform:capitalize; color:#475569;">{{ str_replace('_', ' ', $p->payment_mode ?: 'Cash') }}</td>
            <td class="r" style="font-weight:600; color:#1E3A8A;">{{ formatIndianCurrency($rAmt, 2, false) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($pdAmt, 2, false) }}</td>
            <td class="r" style="color:{{ $duAmt > 0 ? '#DC2626' : '#10B981' }}; font-weight:700;">{{ formatIndianCurrency($duAmt, 2, false) }}</td>
            <td class="c">
                @if(strtolower($p->payment_status) === 'paid')
                    <span class="badge badge-success">Paid</span>
                @elseif(strtolower($p->payment_status) === 'partial')
                    <span class="badge badge-warning">Partial</span>
                @else
                    <span class="badge badge-danger">Pending</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="c" style="padding:24px; color:#64748B;">
                <em>No payment transactions recorded for this tenancy agreement.</em>
            </td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="4" class="r"><strong>CONSOLIDATED TENANCY TOTALS:</strong></td>
            <td class="r" style="color:#1E3A8A; font-size:11px;"><strong>{{ formatIndianCurrency($tRent) }}</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($tPaid) }}</strong></td>
            <td class="r" style="color:#DC2626; font-size:11px;"><strong>{{ formatIndianCurrency($tPending) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>

@include('admin.components.pdf-footer')
</body>
</html>