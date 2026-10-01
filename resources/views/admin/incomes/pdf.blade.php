<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income Register Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Income Register Report',
    'orientation' => 'landscape',
    'backUrl' => route('incomes.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Income Register Report',
    'subtitle' => 'Revenue & Inflows Accounting Report'
])

@php
    $incList = $allRecords ?? ($incomes ?? collect([]));
@endphp

<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Total Realized Revenue</div>
        <div class="s-value">₹{{ number_format($totalAmount ?? $incList->sum('amount'), 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Total Income Records</div>
        <div class="s-value">{{ $totalIncomes ?? $incList->count() }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-wallet"></i> Income &amp; Revenue Transactions</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Income Source / Title</th>
            <th>Category</th>
            <th>Project</th>
            <th>Date</th>
            <th>Payment Mode</th>
            <th class="r">Amount (₹)</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($incList as $i => $inc)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $inc->title }}</strong></td>
            <td>{{ $inc->category->name ?? 'General Income' }}</td>
            <td>{{ $inc->project->project_name ?? 'Head Office' }}</td>
            <td>{{ $inc->income_date ? \Carbon\Carbon::parse($inc->income_date)->format('d M Y') : '—' }}</td>
            <td>{{ ucfirst($inc->payment_mode ?: 'Cash') }}</td>
            <td class="r" style="color:#059669; font-weight:800;">₹{{ number_format($inc->amount, 2) }}</td>
            <td class="c"><span class="badge badge-success">Received</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:20px;color:#64748B;">No income records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>