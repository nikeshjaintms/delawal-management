<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Collections & Tenancy Register - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Rental Collections Register',
    'orientation' => 'landscape',
    'backUrl' => route('reports.rentals')
])

@include('admin.components.pdf-header', [
    'title' => 'Rental Collections & Tenancy Ledger',
    'subtitle' => 'Tenancy Agreements, Monthly Rent Collections & Arrears Statement'
])

@php
    $collectionEff = ($totalRentAmt ?? 0) > 0 ? round((($totalReceived ?? 0) / ($totalRentAmt ?? 0)) * 100, 1) : 0;
@endphp

{{-- ── KPI Summary Cards ── --}}
<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Rent Demanded</div>
        <div class="s-value">{{ formatIndianCurrency($totalRentAmt ?? 0) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Rent Collected / Realized</div>
        <div class="s-value">{{ formatIndianCurrency($totalReceived ?? 0) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Outstanding Arrears / Due</div>
        <div class="s-value">{{ formatIndianCurrency($totalPending ?? 0) }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Collection Efficiency</div>
        <div class="s-value">{{ $collectionEff }}%</div>
    </div>
    <div class="stat-box">
        <div class="s-label">Total Rent Receipts</div>
        <div class="s-value">{{ $records->count() }} Entries</div>
    </div>
</div>

<div class="section-label">
    <i class="fa-solid fa-house-user" style="color:#2563EB;"></i> Rental Collections &amp; Receivables Register
</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:70px;">Date</th>
            <th style="width:75px;">Receipt #</th>
            <th>Tenant Name &amp; Contact</th>
            <th>Property / Unit</th>
            <th class="c" style="width:85px;">Rent Month</th>
            <th class="c" style="width:70px;">Mode</th>
            <th class="r" style="width:95px;">Rent Billed (₹)</th>
            <th class="r" style="width:95px;">Collected (₹)</th>
            <th class="r" style="width:95px;">Balance Due (₹)</th>
            <th class="c" style="width:65px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $i => $r)
        @php
            $tenant = $r->rental?->tenant_name ?? 'Tenant';
            $mobile = $r->rental?->tenant_mobile ?? '';
            $prop = $r->property?->property_name ?: ($r->rental?->property?->property_name ?? '—');
            $st = strtolower($r->status ?? 'paid');
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="white-space:nowrap;">{{ $r->payment_date ? \Carbon\Carbon::parse($r->payment_date)->format('d M Y') : '—' }}</td>
            <td style="font-size:8px;">
                <strong>{{ $r->receipt_no ?: ('RP-' . $r->id) }}</strong>
                @if(!empty($r->transaction_id))
                    <div style="font-size:7.5px; color:#64748B;">Txn: {{ $r->transaction_id }}</div>
                @endif
            </td>
            <td>
                <strong>{{ $tenant }}</strong>
                @if(!empty($mobile))
                    <div style="font-size:7.5px; color:#64748B;">Mob: {{ $mobile }}</div>
                @endif
            </td>
            <td>{{ $prop }}</td>
            <td class="c">
                <span class="badge" style="font-size:7.5px;">{{ $r->payment_month }} {{ $r->payment_year }}</span>
            </td>
            <td class="c">{{ ucfirst($r->payment_mode ?: 'Cash') }}</td>
            <td class="r" style="font-weight:600;">{{ formatIndianCurrency($r->rent_amount ?? 0) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($r->paid_amount ?? 0) }}</td>
            <td class="r" style="color:{{ ($r->pending_amount ?? 0) > 0 ? '#DC2626' : '#94A3B8' }}; font-weight:{{ ($r->pending_amount ?? 0) > 0 ? '700' : 'normal' }};">
                {{ ($r->pending_amount ?? 0) > 0 ? formatIndianCurrency($r->pending_amount) : '—' }}
            </td>
            <td class="c">
                <span class="badge {{ $st === 'paid' ? 'badge-success' : ($st === 'partial' ? 'badge-warning' : 'badge-danger') }}">
                    {{ ucfirst($r->status ?? 'Paid') }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="11" class="c" style="padding:24px; color:#64748B;">No rental collection records found for the selected period.</td>
        </tr>
        @endforelse
    </tbody>
    @if($records->count() > 0)
    <tfoot>
        <tr class="double-total">
            <td colspan="7" class="r">PORTFOLIO GRAND TOTALS:</td>
            <td class="r" style="font-size:9.5px; font-weight:800;">{{ formatIndianCurrency($totalRentAmt ?? 0) }}</td>
            <td class="r" style="color:#059669; font-size:10px; font-weight:800;">{{ formatIndianCurrency($totalReceived ?? 0) }}</td>
            <td class="r" style="color:#DC2626; font-size:9.5px; font-weight:800;">{{ formatIndianCurrency($totalPending ?? 0) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- ── Signatures Block ── --}}
<div class="auth-block" style="margin-top:20px; page-break-inside:avoid;">
    <div class="auth-col">
        <div class="auth-line">Property / Tenancy Manager</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Finance Controller / Authorized Signatory</div>
    </div>
</div>

@include('admin.components.pdf-footer', ['note' => 'Tenancy & Rental Ledger • Verified Against Executed Lease Agreements'])
</body>
</html>