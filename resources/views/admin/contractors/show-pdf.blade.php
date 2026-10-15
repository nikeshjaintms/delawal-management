<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contractor Statement & Work Ledger - {{ $contractor->contractor_name ?: ($contractor->name ?? 'Contractor') }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Contractor Statement: ' . ($contractor->contractor_name ?: ($contractor->name ?? 'Contractor')),
    'orientation' => 'landscape',
    'backUrl' => isset($contractor->id) ? route('contractors.show', $contractor->id) : route('contractors.index')
])

@php
    $cVal = (float)($contractor->contract_amount ?? 0);
    $pVal = (float)($contractor->paid_amount ?? ($contractor->payments ? $contractor->payments->sum('amount') : 0));
    $dVal = (float)($contractor->due_amount ?? max(0, $cVal - $pVal));
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Contractor Account Statement & Work Contract Ledger',
    'subtitle' => 'Civil Works, Site Commitments, Disbursed Payments & Balance Payable',
    'firm' => $contractor->firm ?? null,
    'docRef' => 'CONT-' . str_pad($contractor->id ?? 1, 5, '0', STR_PAD_LEFT)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Contractor Name</div>
        <div class="s-value" style="font-size:13px;">{{ $contractor->contractor_name ?: ($contractor->name ?? 'Contractor') }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Total Contract Commitment</div>
        <div class="s-value">{{ formatIndianCurrency($cVal) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Settled / Paid to Date</div>
        <div class="s-value">{{ formatIndianCurrency($pVal) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Outstanding Balance Due</div>
        <div class="s-value">{{ formatIndianCurrency($dVal) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-helmet-safety"></i> Contractor Particulars</div>
        <div class="info-row"><span class="info-label">Full Name:</span><span class="info-val"><strong>{{ $contractor->contractor_name ?: ($contractor->name ?? 'Contractor') }}</strong></span></div>
        <div class="info-row"><span class="info-label">Mobile Number:</span><span class="info-val">{{ $contractor->mobile ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Work Type / Trade:</span><span class="info-val">{{ $contractor->work_type ?: ($contractor->specialization ?: 'Civil Works') }}</span></div>
        <div class="info-row"><span class="info-label">PAN Number:</span><span class="info-val"><strong>{{ $contractor->pan_no ?: '—' }}</strong></span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building-columns"></i> Site & Banking Particulars</div>
        <div class="info-row"><span class="info-label">Assigned Site / Project:</span><span class="info-val">{{ $contractor->project->project_name ?? ($contractor->project->name ?? 'Head Office') }}</span></div>
        <div class="info-row"><span class="info-label">Bank Name:</span><span class="info-val">{{ $contractor->bank_name ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Account Number:</span><span class="info-val">{{ $contractor->account_number ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Contract Status:</span><span class="info-val"><span class="badge badge-success">{{ strtoupper($contractor->status ?: 'ACTIVE') }}</span></span></div>
    </div>
</div>

@if($contractor->payments && $contractor->payments->isNotEmpty())
<div class="section-label"><i class="fa-solid fa-receipt"></i> Payment Settlements &amp; Disbursement Audit Trail</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:85px;">Payment Date</th>
            <th>Voucher Reference / Narration</th>
            <th>Site / Allocation</th>
            <th style="width:90px;">Payment Mode</th>
            <th class="r" style="width:120px;">Amount Paid (₹)</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($contractor->payments as $i => $pmt)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="white-space:nowrap; color:#475569;">{{ $pmt->payment_date ? \Carbon\Carbon::parse($pmt->payment_date)->format('d-M-Y') : '—' }}</td>
            <td>
                <strong>{{ $pmt->notes ?: ($pmt->reference_no ?: 'Contractor Payment') }}</strong>
                @if(!empty($pmt->voucher_no))
                    <div style="font-size:8px; color:#64748B;">Voucher: {{ $pmt->voucher_no }}</div>
                @endif
            </td>
            <td>{{ $pmt->property->property_name ?? ($contractor->project->project_name ?? 'General') }}</td>
            <td style="text-transform:capitalize; color:#475569;">{{ str_replace('_', ' ', $pmt->payment_mode ?: 'Bank') }}</td>
            <td class="r" style="font-weight:700; color:#059669;">{{ formatIndianCurrency($pmt->amount ?? 0, 2, false) }}</td>
            <td class="c"><span class="badge badge-success">Disbursed</span></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="5" class="r"><strong>TOTAL DISBURSED SETTLEMENTS:</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($pVal) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>
@endif

<div style="margin-top:14px; font-size:9px; color:#64748B; line-height:1.4;">
    <strong>Accounting Ledger Note:</strong> Outstanding balance payable to contractor constitutes Sundry Creditors (Trade Payables) under Current Liabilities in the Balance Sheet.
</div>

@include('admin.components.pdf-footer')
</body>
</html>