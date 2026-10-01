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
    'title' => 'Official Expense Voucher',
    'subtitle' => 'Debit Voucher & Expenditure Slip',
    'docRef' => 'EXP-' . str_pad($expense->id ?? 1, 5, '0', STR_PAD_LEFT)
])

<div class="stat-row">
    <div class="stat-box s-red">
        <div class="s-label">Total Expense Amount</div>
        <div class="s-value">₹{{ number_format($expense->amount ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Payment Mode</div>
        <div class="s-value" style="font-size:13px;">{{ ucfirst($expense->payment_mode ?? 'Cash') }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Voucher Date</div>
        <div class="s-value" style="font-size:13px;">{{ isset($expense->expense_date) ? \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') : now()->format('d M Y') }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-receipt"></i> Expenditure Particulars</div>
        <div class="info-row">
            <span class="info-label">Expense Title:</span>
            <span class="info-val">{{ $expense->title ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Category:</span>
            <span class="info-val">{{ $expense->category->name ?? 'General Expense' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Paid To / Beneficiary:</span>
            <span class="info-val">{{ $expense->paid_to ?? ($expense->vendor->name ?? '—') }}</span>
        </div>
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building"></i> Allocation Details</div>
        <div class="info-row">
            <span class="info-label">Project:</span>
            <span class="info-val">{{ $expense->project->project_name ?? 'General / Head Office' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Firm:</span>
            <span class="info-val">{{ $expense->firm->firm_name ?? 'Delawala Infra Co.' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Description / Remarks:</span>
            <span class="info-val">{{ $expense->description ?: '—' }}</span>
        </div>
    </div>
</div>

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Receiver / Payee Signature</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Authorized Signatory &amp; Stamp</div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>