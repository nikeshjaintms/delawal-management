<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project-Wise Expense Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Project-Wise Expense Report',
    'orientation' => 'landscape',
    'backUrl' => route('expenses.project-wise')
])

@include('admin.components.pdf-header', [
    'title' => 'Project-Wise Expense Report',
    'subtitle' => 'Site Costing & Material Expense Breakdown'
])

@php
    $expList = $projectExpenses ?? ($expenses ?? collect([]));
    $grand = $grandTotal ?? ($totalAmount ?? $expList->sum('amount'));
@endphp

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Site Cost</div>
        <div class="s-value">₹{{ number_format($grand, 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-list-check"></i> Project Expenditures</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Project</th>
            <th>Category</th>
            <th>Title / Description</th>
            <th>Date</th>
            <th class="r">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expList as $i => $e)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $e->project->project_name ?? 'General' }}</strong></td>
            <td>{{ $e->category->name ?? 'General' }}</td>
            <td>{{ $e->title }}</td>
            <td>{{ $e->expense_date ? \Carbon\Carbon::parse($e->expense_date)->format('d M Y') : '—' }}</td>
            <td class="r" style="font-weight:700; color:#DC2626;">₹{{ number_format($e->amount, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="c" style="padding:20px;color:#64748B;">No expense records found.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="r">Total Amount</td>
            <td class="r" style="color:#DC2626; font-size:12px;">₹{{ number_format($grand, 2) }}</td>
        </tr>
    </tfoot>
</table>

@include('admin.components.pdf-footer')
</body>
</html>