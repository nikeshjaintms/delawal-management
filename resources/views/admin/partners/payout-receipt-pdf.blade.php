<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Profit Payout Voucher - #{{ $payout->id }}</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Profit Payout Voucher #' . $payout->id,
    'orientation' => 'portrait',
    'backUrl' => route('partners.show', $partner->id)
])

@include('admin.components.pdf-header', [
    'title' => 'Partner Profit Payout Voucher',
    'subtitle' => 'Profit Distribution Payment Acknowledgement',
    'firm' => $payout->firm ?? $partner->firm ?? null,
    'docRef' => 'PPV-' . str_pad($payout->id, 5, '0', STR_PAD_LEFT),
    'hideState' => true,
    'hideOwner' => true,
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Partner</div>
        <div class="s-value" style="font-size:13px;">{{ $partner->name }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Share Percentage</div>
        <div class="s-value">{{ $payout->share ? number_format((float)$payout->share->share_percentage, 2) . '%' : '—' }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Amount Paid</div>
        <div class="s-value">{{ formatIndianCurrency($payout->amount) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Balance Payable</div>
        <div class="s-value">{{ formatIndianCurrency($metrics['balance_due'] ?? 0) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user"></i> Partner &amp; Profit Source</div>
        <div class="info-row"><span class="info-label">Partner Name:</span><span class="info-val"><strong>{{ $partner->name }}</strong></span></div>
        <div class="info-row"><span class="info-label">Firm:</span><span class="info-val">{{ $payout->firm?->firm_name ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Profit Scope:</span><span class="info-val">{{ $payout->share ? ($payout->share->scope_label . ': ' . $payout->share->target_name) : '—' }}</span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-money-check-dollar"></i> Payment Particulars</div>
        <div class="info-row"><span class="info-label">Payout Date:</span><span class="info-val">{{ $payout->payout_date?->format('d M Y') ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Payment Mode:</span><span class="info-val">{{ $payout->payment_mode ?: ($payout->paymentMode?->name ?? 'Cash') }}</span></div>
        <div class="info-row"><span class="info-label">Reference No:</span><span class="info-val">{{ $payout->reference_no ?: '—' }}</span></div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Description</th>
            <th class="r" style="width:160px;">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                Profit Share Payout ({{ $payout->share ? number_format((float)$payout->share->share_percentage, 2) . '%' : '' }}
                — {{ $payout->share?->target_name ?? 'Firm' }})
                @if($payout->remarks)
                    <div style="font-size:9px; color:#64748B;">Remarks: {{ $payout->remarks }}</div>
                @endif
            </td>
            <td class="r" style="font-weight:700; color:#059669;">{{ formatIndianCurrency($payout->amount) }}</td>
        </tr>
        <tr class="double-total">
            <td><strong>Amount in Words:</strong> {{ numberToWordsIndian($payout->amount) }}</td>
            <td class="r"><strong>{{ formatIndianCurrency($payout->amount) }}</strong></td>
        </tr>
    </tbody>
</table>

@include('admin.components.pdf-signature', [
    'leftRole' => 'Received By (' . $partner->name . ')',
    'rightRole' => 'Authorized Signatory',
    'firm' => $payout->firm ?? $partner->firm ?? null,
])

@include('admin.components.pdf-footer', [
    'hideFooterNote' => true,
    'hideFooterFirm' => true,
])
</body>
</html>
