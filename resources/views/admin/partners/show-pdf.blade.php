<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Profit Statement - {{ $partner->name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Partner Profit Statement: ' . $partner->name,
    'orientation' => 'landscape',
    'backUrl' => route('partners.show', $partner->id)
])

@include('admin.components.pdf-header', [
    'title' => 'Partner Profit Share & Payout Statement',
    'subtitle' => ($partner->is_silent ? 'Confidential Partner Arrangement (Internal Admin Record)' : 'Partner Profit Distribution & Settlement Ledger'),
    'firm' => $partner->firm ?? null,
    'docRef' => 'PRT-' . str_pad($partner->id, 5, '0', STR_PAD_LEFT),
    'hideState' => true,
    'hideOwner' => true,
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Partner Name</div>
        <div class="s-value" style="font-size:13px;">{{ $partner->name }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Realized Profit Share</div>
        <div class="s-value">{{ formatIndianCurrency($totalRealizedEntitled) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Total Paid to Date</div>
        <div class="s-value">{{ formatIndianCurrency($totalPaid) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Remaining Balance Payable</div>
        <div class="s-value">{{ formatIndianCurrency($totalBalanceDue) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-percent"></i> Profit Share Allocations &amp; Profit Summary</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Firm</th>
            <th>Scope &amp; Target</th>
            <th class="r">Share %</th>
            <th class="r">Realized Profit (₹)</th>
            <th class="r">Entitled Share (₹)</th>
            <th class="r">Booked Profit Ref (₹)</th>
            <th class="r">Paid (₹)</th>
            <th class="r">Payable (₹)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($partner->shares as $idx => $sh)
            @php $m = $shareMetrics[$sh->id] ?? []; @endphp
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $sh->firm?->firm_name ?? '—' }}</td>
                <td><strong>{{ $sh->scope_label }}:</strong> {{ $sh->target_name }}</td>
                <td class="r"><strong>{{ number_format((float)$sh->share_percentage, 2) }}%</strong></td>
                <td class="r">{{ formatIndianCurrency($m['realized_profit'] ?? 0, 2, false) }}</td>
                <td class="r" style="font-weight:700;">{{ formatIndianCurrency($m['realized_entitled'] ?? 0, 2, false) }}</td>
                <td class="r">{{ formatIndianCurrency($m['booked_entitled'] ?? 0, 2, false) }}</td>
                <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($m['paid_amount'] ?? 0, 2, false) }}</td>
                <td class="r" style="color:#DC2626; font-weight:700;">{{ formatIndianCurrency($m['balance_due'] ?? 0, 2, false) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@if($partner->payouts->isNotEmpty())
<div class="section-label"><i class="fa-solid fa-receipt"></i> Profit Payout Disbursement History</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Scope &amp; Target</th>
            <th>Payment Mode</th>
            <th>Reference No</th>
            <th>Remarks</th>
            <th class="r">Amount Paid (₹)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($partner->payouts as $idx => $payout)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $payout->payout_date?->format('d M Y') ?? '—' }}</td>
                <td>{{ $payout->share ? ($payout->share->scope_label . ': ' . $payout->share->target_name) : '—' }}</td>
                <td>{{ $payout->payment_mode ?: ($payout->paymentMode?->name ?? 'Cash') }}</td>
                <td>{{ $payout->reference_no ?: '—' }}</td>
                <td>{{ $payout->remarks ?: '—' }}</td>
                <td class="r" style="font-weight:700; color:#059669;">{{ formatIndianCurrency($payout->amount, 2, false) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="6" class="r"><strong>TOTAL PROFIT DISTRIBUTED:</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($totalPaid) }}</strong></td>
        </tr>
    </tfoot>
</table>
@endif

@include('admin.components.pdf-signature', [
    'leftRole' => 'Partner Acknowledgement (' . $partner->name . ')',
    'rightRole' => 'Authorized Signatory',
    'firm' => $partner->firm ?? null,
])

@include('admin.components.pdf-footer', [
    'hideFooterNote' => true,
    'hideFooterFirm' => true,
])
</body>
</html>
