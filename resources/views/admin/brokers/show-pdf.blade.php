<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Broker Account Statement & Commission Ledger - {{ $broker->name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Broker Statement: ' . $broker->name,
    'orientation' => 'landscape',
    'backUrl' => isset($broker->id) ? route('brokers.show', $broker->id) : route('brokers.index')
])

@php
    $totDealVal = 0;
    $totCommAccrued = 0;
    $totCommPaid = 0;
    $totCommDue = 0;

    foreach($broker->commissions ?? [] as $comm) {
        $deal = (float)($comm->deal_amount ?? 0);
        $acc = (float)($comm->commission_amount ?? 0);
        $paid = (float)($comm->paid_amount ?? 0);
        $due = max(0, $acc - $paid);

        $totDealVal += $deal;
        $totCommAccrued += $acc;
        $totCommPaid += $paid;
        $totCommDue += $due;
    }
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Broker Account Statement & Commission Ledger',
    'subtitle' => 'Sourced Deals, Accrued Brokerages, Disbursed Payouts & Outstanding Liabilities',
    'firm' => $broker->firm ?? null,
    'docRef' => 'BRK-' . str_pad($broker->id ?? 1, 5, '0', STR_PAD_LEFT)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Channel Partner</div>
        <div class="s-value" style="font-size:13px;">{{ $broker->name }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Sourced Deal Value</div>
        <div class="s-value">{{ formatIndianCurrency($totDealVal) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Gross Commission Accrued</div>
        <div class="s-value">{{ formatIndianCurrency($totCommAccrued) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Disbursed Payouts</div>
        <div class="s-value">{{ formatIndianCurrency($totCommPaid) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Outstanding Payable Balance</div>
        <div class="s-value">{{ formatIndianCurrency($totCommDue) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user-tie"></i> Channel Partner Particulars</div>
        <div class="info-row"><span class="info-label">Broker Name:</span><span class="info-val"><strong>{{ $broker->name }}</strong></span></div>
        <div class="info-row"><span class="info-label">Mobile Number:</span><span class="info-val">{{ $broker->mobile ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Email:</span><span class="info-val">{{ $broker->email ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Commission Rate:</span><span class="info-val">{{ $broker->commission_rate ? ($broker->commission_rate . '%') : 'Standard Agreement' }}</span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Address & Registration</div>
        <div class="info-row"><span class="info-label">Address:</span><span class="info-val">{{ $broker->address ?: 'Dahegam, Bharuch' }}</span></div>
        <div class="info-row"><span class="info-label">City:</span><span class="info-val">{{ $broker->city ?: 'Dahegam' }}</span></div>
        <div class="info-row"><span class="info-label">Firm Link:</span><span class="info-val">{{ $broker->firm->firm_name ?? 'Delawala Infrastructure' }}</span></div>
        <div class="info-row"><span class="info-label">Status:</span><span class="info-val"><span class="badge badge-success">{{ strtoupper($broker->status ?: 'ACTIVE') }}</span></span></div>
    </div>
</div>

@if(isset($broker->commissions) && $broker->commissions->isNotEmpty())
<div class="section-label"><i class="fa-solid fa-handshake"></i> Sourced Property Transactions &amp; Commission Accruals</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Property / Unit Particulars</th>
            <th>Customer / Buyer</th>
            <th class="r" style="width:110px;">Deal Value (₹)</th>
            <th class="r" style="width:90px;">Comm. Rate</th>
            <th class="r" style="width:115px;">Accrued (₹)</th>
            <th class="r" style="width:115px;">Paid to Date (₹)</th>
            <th class="r" style="width:115px;">Balance Due (₹)</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($broker->commissions as $i => $c)
        @php
            $deal = (float)($c->deal_amount ?? 0);
            $acc = (float)($c->commission_amount ?? 0);
            $paid = (float)($c->paid_amount ?? 0);
            $due = max(0, $acc - $paid);
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $c->property->property_name ?? 'Property Deal' }}</strong></td>
            <td>{{ $c->customer->name ?? '—' }}</td>
            <td class="r" style="font-weight:600; color:#1E3A8A;">{{ formatIndianCurrency($deal, 2, false) }}</td>
            <td class="r">{{ $c->commission_rate ? ($c->commission_rate . '%') : '—' }}</td>
            <td class="r" style="font-weight:700; color:#0F172A;">{{ formatIndianCurrency($acc, 2, false) }}</td>
            <td class="r" style="font-weight:700; color:#059669;">{{ formatIndianCurrency($paid, 2, false) }}</td>
            <td class="r" style="font-weight:700; color:{{ $due > 0 ? '#DC2626' : '#10B981' }};">{{ formatIndianCurrency($due, 2, false) }}</td>
            <td class="c">
                @if(strtolower($c->status ?? '') === 'paid' || ($due <= 0 && $acc > 0))
                    <span class="badge badge-success">Settled</span>
                @elseif($paid > 0)
                    <span class="badge badge-warning">Partial</span>
                @else
                    <span class="badge badge-danger">Unpaid</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="3" class="r"><strong>TOTAL BROKERAGE ACCRUALS:</strong></td>
            <td class="r" style="color:#1E3A8A; font-size:11px;"><strong>{{ formatIndianCurrency($totDealVal) }}</strong></td>
            <td class="r">-</td>
            <td class="r" style="color:#0F172A; font-size:11px;"><strong>{{ formatIndianCurrency($totCommAccrued) }}</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($totCommPaid) }}</strong></td>
            <td class="r" style="color:#DC2626; font-size:11px;"><strong>{{ formatIndianCurrency($totCommDue) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>
@endif

<div style="margin-top:14px; font-size:9px; color:#64748B; line-height:1.4;">
    <strong>Accounting Ledger Note:</strong> Outstanding balance due to brokers represents Sundry Creditors (Brokerage Payable) under Current Liabilities in the Balance Sheet.
</div>

@include('admin.components.pdf-footer')
</body>
</html>