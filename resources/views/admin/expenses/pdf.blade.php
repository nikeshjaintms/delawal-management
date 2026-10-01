<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses Accounting Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Expenses Accounting Report',
    'orientation' => 'landscape',
    'backUrl' => route('expenses.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Expenses Accounting Report',
    'subtitle' => 'Expenditure Register & Category Summary'
])

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Expense Vouchers</div>
        <div class="s-value">{{ $totalExpensesCount ?? ($totalExpenses ?? $expenses->count()) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total Expenditure Amount</div>
        <div class="s-value">₹{{ number_format($totalExpenseAmount ?? ($totalAmount ?? $expenses->sum('amount')), 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Approved Expenses</div>
        <div class="s-value">₹{{ number_format($approvedAmount ?? $expenses->sum('amount'), 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-receipt"></i> Expenses Register</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Voucher #</th>
            <th>Expense Title</th>
            <th>Category</th>
            <th>Project / Site</th>
            <th>Date</th>
            <th>Mode</th>
            <th class="r">Amount (₹)</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $i => $e)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>EXP-{{ str_pad($e->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
            <td>{{ $e->title }}</td>
            <td>{{ $e->category->name ?? 'General' }}</td>
            <td>{{ $e->project->project_name ?? 'Head Office' }}</td>
            <td>{{ $e->expense_date ? \Carbon\Carbon::parse($e->expense_date)->format('d M Y') : '—' }}</td>
            <td>{{ ucfirst($e->payment_mode ?: 'Cash') }}</td>
            <td class="r" style="font-weight:700; color:#DC2626;">₹{{ number_format($e->amount, 2) }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($e->status ?: 'Approved') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:20px;color:#64748B;">No expense vouchers found.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" class="r">Grand Total</td>
            <td class="r" style="color:#DC2626; font-size:12px;">₹{{ number_format($totalExpenseAmount ?? ($totalAmount ?? $expenses->sum('amount')), 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

@include('admin.components.pdf-footer')
</body>
</html>