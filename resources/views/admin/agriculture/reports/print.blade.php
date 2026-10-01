<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agriculture Crops & Harvest Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Agriculture Report',
    'orientation' => 'landscape',
    'backUrl' => route('agriculture.reports.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Agriculture Crops & Harvest Report',
    'subtitle' => 'Agricultural Land & Produce Ledger'
])

<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Total Crop Income</div>
        <div class="s-value">₹{{ number_format($totalIncome ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total Farming Expense</div>
        <div class="s-value">₹{{ number_format($totalExpense ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Net Agri Profit</div>
        <div class="s-value">₹{{ number_format($netProfit ?? (($totalIncome ?? 0) - ($totalExpense ?? 0)), 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-wheat-awn"></i> Agricultural Operations Register</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Type</th>
            <th>Description</th>
            <th>Date</th>
            <th class="r">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records ?? [] as $i => $rec)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ ucfirst($rec->type ?? ($reportType ?? 'Harvest')) }}</strong></td>
            <td>{{ $rec->description ?? $rec->title ?? '—' }}</td>
            <td>{{ $rec->date ? \Carbon\Carbon::parse($rec->date)->format('d M Y') : '—' }}</td>
            <td class="r" style="font-weight:700;">₹{{ number_format($rec->amount ?? 0, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="c" style="padding:20px;color:#64748B;">No agriculture records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>