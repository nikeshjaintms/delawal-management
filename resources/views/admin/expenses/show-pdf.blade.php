@php
    $expAmt = (float)($expense->amount ?? 0);
    $payee = $expense->paid_to ?: ($expense->vendor->name ?? '—');
    $cat = $expense->expenseCategory->name ?? ($expense->expense_category ?: ($expense->category->name ?? 'General Expense'));
    $proj = $expense->project->project_name ?? ($expense->project->name ?? ($expense->property->property_name ?? 'Head Office / Corporate'));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Voucher #EXP-{{ str_pad($expense->id ?? 1, 5, '0', STR_PAD_LEFT) }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Expense Voucher #EXP-' . str_pad($expense->id ?? 1, 5, '0', STR_PAD_LEFT),
    'orientation' => 'portrait',
    'backUrl' => isset($expense->id) ? route('expenses.show', $expense->id) : route('expenses.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Official Expense Payment Voucher',
    'subtitle' => 'Debit Voucher, Cost Allocation & Cash/Bank Disbursement Slip',
    'firm' => $expense->firm ?? null,
    'docRef' => 'EXP-' . str_pad($expense->id ?? 1, 5, '0', STR_PAD_LEFT)
])

<div class="stat-row">
    <div class="stat-box s-red">
        <div class="s-label">Disbursed Amount</div>
        <div class="s-value" style="font-size:14px; color:#DC2626;">{{ formatIndianCurrency($expAmt) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Disbursement Mode</div>
        <div class="s-value" style="font-size:13px;">{{ ucfirst(str_replace('_', ' ', $expense->payment_mode ?: 'Cash')) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Voucher Date</div>
        <div class="s-value" style="font-size:13px;">{{ isset($expense->expense_date) ? \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') : now()->format('d M Y') }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Approval Status</div>
        <div class="s-value" style="font-size:13px;">{{ strtoupper($expense->approval_status ?: ($expense->status ?: 'APPROVED')) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-receipt"></i> Expenditure Particulars</div>
        <div class="info-row">
            <span class="info-label">Particulars / Title:</span>
            <span class="info-val"><strong>{{ $expense->expense_title ?: ($expense->title ?: 'Expense Voucher') }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Account Head / Category:</span>
            <span class="info-val">{{ $cat }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Beneficiary / Paid To:</span>
            <span class="info-val">{{ $payee }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Bill / Invoice Ref:</span>
            <span class="info-val">{{ $expense->bill_no ?: ($expense->reference_no ?: 'Direct Voucher') }}</span>
        </div>
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building"></i> Cost Center Allocation</div>
        <div class="info-row">
            <span class="info-label">Site / Project Alloc:</span>
            <span class="info-val"><strong>{{ $proj }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Disbursing Firm:</span>
            <span class="info-val">{{ $expense->firm->firm_name ?? 'Delawala Infrastructure' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Account:</span>
            <span class="info-val">{{ $expense->payment_account ?: 'Petty Cash / Main Bank' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Description / Purpose:</span>
            <span class="info-val">{{ $expense->description ?: '—' }}</span>
        </div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-file-invoice-dollar"></i> Accounting Disbursement Summary</div>
<table>
    <thead>
        <tr>
            <th>Expense Particulars &amp; Narration</th>
            <th class="r" style="width: 160px;">Net Debit Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <strong>{{ $expense->expense_title ?: ($expense->title ?: 'Expense Voucher') }}</strong>
                @if($expense->description)
                    <div style="font-size:8.5px; color:#64748B;">{{ $expense->description }}</div>
                @endif
            </td>
            <td class="r" style="font-weight:700; color:#DC2626;">{{ formatIndianCurrency($expAmt) }}</td>
        </tr>
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td class="r"><strong>TOTAL DISBURSED VOUCHER DEBIT:</strong></td>
            <td class="r" style="color:#DC2626; font-size:11px;"><strong>{{ formatIndianCurrency($expAmt) }}</strong></td>
        </tr>
    </tfoot>
</table>

<div style="margin-top:10px; font-size:8.5px; color:#64748B;">
    <strong>Amount in Words:</strong> {{ numberToWordsIndian($expAmt) }}
</div>

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Receiver / Payee Signature</div>
    </div>
    <div class="auth-col">
        @php
            $expFirm = $expense->firm ?: ($expense->project?->firm);
            $expSig = $expFirm?->digital_signature ?? null;
            $hasExpSig = !empty($expSig) && (
                \Illuminate\Support\Facades\Storage::disk('public')->exists($expSig) ||
                file_exists(public_path('storage/' . $expSig)) ||
                file_exists(public_path($expSig))
            );
            $expSigUrl = $hasExpSig ? (file_exists(public_path($expSig)) ? asset($expSig) : asset('storage/' . $expSig)) : null;
        @endphp
        @if($expSigUrl)
            <div style="min-height: 40px; margin-bottom: 2px;">
                <img src="{{ $expSigUrl }}" style="max-height: 40px; max-width: 140px; object-fit: contain;" alt="Authorized Signature">
            </div>
        @endif
        <div class="auth-line">Authorized Signatory / {{ $expense->firm->firm_name ?? 'Delawala Infrastructure' }}</div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>