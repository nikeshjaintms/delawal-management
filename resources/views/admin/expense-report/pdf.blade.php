<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Summary Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Expense Summary Report',
    'orientation' => 'landscape',
    'backUrl' => route('expense-report.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Expense Summary Report',
    'subtitle' => 'Financial Expense Analytics & GST Breakdown'
])

<div class="stat-row">
    <div class="stat-box s-red">
        <div class="s-label">Total Expenses</div>
        <div class="s-value">₹{{ number_format($totalAmount ?? $expenses->sum('amount'), 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Settled / Paid</div>
        <div class="s-value">₹{{ number_format($paidAmount ?? ($totalAmount ?? $expenses->sum('amount')), 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-chart-pie"></i> Expense Breakdown</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Title</th>
            <th>Category</th>
            <th>Project</th>
            <th>Date</th>
            <th class="r">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $i => $e)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $e->title }}</strong></td>
            <td>{{ $e->category->name ?? 'General' }}</td>
            <td>{{ $e->project->project_name ?? 'General' }}</td>
            <td>{{ $e->expense_date ? \Carbon\Carbon::parse($e->expense_date)->format('d M Y') : '—' }}</td>
            <td class="r" style="font-weight:700; color:#DC2626;">₹{{ number_format($e->amount, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="c" style="padding:20px;color:#64748B;">No expense records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>