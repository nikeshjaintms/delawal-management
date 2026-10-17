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
    'title' => 'Broker Commissions Statement & Payout Ledger',
    'subtitle' => 'Real Estate Agent Brokerage Accruals, Payouts & Outstanding Liabilities'
])

{{-- ── KPI Summary Cards ── --}}
<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Commission Accrued</div>
        <div class="s-value">{{ formatIndianCurrency($totalCommission ?? 0) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Commission Paid / Disbursed</div>
        <div class="s-value">{{ formatIndianCurrency($paidCommission ?? 0) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Pending Brokerage Liability</div>
        <div class="s-value">{{ formatIndianCurrency($pendingCommission ?? 0) }}</div>
    </div>
    <div class="stat-box">
        <div class="s-label">Total Broker Deals</div>
        <div class="s-value">{{ $commissions->count() }} Entries</div>
    </div>
</div>

<div class="section-label">
    <i class="fa-solid fa-handshake" style="color:#059669;"></i> Brokerage Accruals &amp; Commission Disbursal Register
</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:75px;">Deal Date</th>
            <th>Broker Name &amp; Contact</th>
            <th>Property / Deal Unit</th>
            <th>Customer / Buyer</th>
            <th class="c" style="width:90px;">Commission Rate</th>
            <th class="r" style="width:115px;">Commission (₹)</th>
            <th class="c" style="width:80px;">Payment Status</th>
            <th style="width:80px;">Settlement Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($commissions as $i => $c)
        @php
            $st = strtolower($c->payment_status ?? 'paid');
            $brokerName = $c->broker->name ?? '—';
            $brokerMob = $c->broker->mobile ?? '';
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="white-space:nowrap;">{{ $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('d M Y') : '—' }}</td>
            <td>
                <strong>{{ $brokerName }}</strong>
                @if(!empty($brokerMob))
                    <div style="font-size:7.5px; color:#64748B;">Mob: {{ $brokerMob }}</div>
                @endif
            </td>
            <td>{{ $c->property->property_name ?? '—' }}</td>
            <td>{{ $c->customer->name ?? '—' }}</td>
            <td class="c">
                <span class="badge" style="font-size:7.5px;">
                    {{ ucfirst($c->commission_type ?? 'fixed') }}:
                    <strong>{{ $c->commission_value ?? '—' }}{{ ($c->commission_type ?? '') === 'percentage' ? '%' : '' }}</strong>
                </span>
            </td>
            <td class="r" style="font-weight:700; color:{{ $st === 'paid' ? '#059669' : '#DC2626' }};">
                {{ formatIndianCurrency($c->commission_amount ?? 0) }}
            </td>
            <td class="c">
                <span class="badge {{ $st === 'paid' ? 'badge-success' : 'badge-danger' }}">
                    {{ ucfirst($c->payment_status ?? 'Paid') }}
                </span>
            </td>
            <td>{{ $c->payment_date ? \Carbon\Carbon::parse($c->payment_date)->format('d M Y') : '—' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="c" style="padding:24px; color:#64748B;">No broker commission records found for the selected period.</td>
        </tr>
        @endforelse
    </tbody>
    @if($commissions->count() > 0)
    <tfoot>
        <tr class="double-total">
            <td colspan="6" class="r">TOTAL ACCRUED COMMISSION:</td>
            <td class="r" style="color:#059669; font-size:10px; font-weight:800;">
                {{ formatIndianCurrency($totalCommission ?? 0) }}
            </td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
    @endif
</table>

@php $hasCustomAuthBlock = true; @endphp
@include('admin.components.pdf-signature', [
    'leftRole' => 'Sales & Channel Partner Manager',
    'rightRole' => 'Managing Director / Partner Approval'
])

@include('admin.components.pdf-footer', ['note' => 'Broker Commissions Statement • Verified Against Executed Booking Agreements'])
</body>
</html>