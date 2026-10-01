<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loans & Liabilities Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Loans & Liabilities Report',
    'orientation' => 'landscape',
    'backUrl' => route('loan-report.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Loans & Liabilities Report',
    'subtitle' => 'Principal, Interest & Repayment Statement'
])

<div class="stat-row">
    <div class="stat-box s-red">
        <div class="s-label">Total Borrowings / Loans</div>
        <div class="s-value">₹{{ number_format($totalLoanAmount ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Principal Repaid</div>
        <div class="s-value">₹{{ number_format($totalPaidAmount ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Outstanding Liability</div>
        <div class="s-value">₹{{ number_format($totalPendingAmount ?? 0, 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-landmark"></i> Loans &amp; Advances Register</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Lender / Bank</th>
            <th>Loan Type</th>
            <th>Sanction Date</th>
            <th class="r">Sanctioned Amount</th>
            <th class="r">Repaid</th>
            <th class="r">Outstanding</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($loans ?? [] as $i => $l)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $l->lender_name ?? $l->bank_name ?? '—' }}</strong></td>
            <td>{{ $l->loan_type ?? 'Term Loan' }}</td>
            <td>{{ $l->sanction_date ? \Carbon\Carbon::parse($l->sanction_date)->format('d M Y') : '—' }}</td>
            <td class="r">₹{{ number_format($l->loan_amount ?? 0, 2) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">₹{{ number_format($l->paid_amount ?? 0, 2) }}</td>
            <td class="r" style="color:#DC2626; font-weight:700;">₹{{ number_format($l->pending_amount ?? 0, 2) }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($l->status ?? 'Active') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:20px;color:#64748B;">No loan records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>