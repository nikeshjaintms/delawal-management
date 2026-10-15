<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income & Revenue Inflows Register - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Income & Revenue Inflows Register',
    'orientation' => 'landscape',
    'backUrl' => route('incomes.index')
])

@php
    $incList = $allRecords ?? ($incomes ?? collect([]));
    $totAmount = $totalAmount ?? $incList->sum('amount');
    $filters = [];
    if(request('date_from') || request('date_to') || request('from_date') || request('to_date')) {
        $f = request('date_from') ?: request('from_date');
        $t = request('date_to') ?: request('to_date');
        $filters['Period'] = ($f ? \Carbon\Carbon::parse($f)->format('d M Y') : 'Start') . ' to ' . ($t ? \Carbon\Carbon::parse($t)->format('d M Y') : 'Current');
    }
    if(request('category_id')) {
        $c = \App\Models\IncomeCategory::find(request('category_id'));
        if($c) $filters['Category'] = $c->name;
    }
    if(request('project_id')) {
        $p = \App\Models\Project::find(request('project_id'));
        if($p) $filters['Project'] = $p->project_name ?? $p->name;
    }
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Income & Revenue Inflows Register',
    'subtitle' => 'Audit Ledger of Direct Revenue, Lease Receipts & Sundry Operating Inflows',
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Total Realized Inflows</div>
        <div class="s-value">{{ formatIndianCurrency($totAmount) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Total Income Vouchers</div>
        <div class="s-value">{{ $totalIncomes ?? $incList->count() }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-wallet"></i> Income &amp; Revenue Ledger</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th style="width:75px;">Date</th>
            <th>Income Source & Title</th>
            <th>Account Category</th>
            <th>Project / Site Allocation</th>
            <th style="width:90px;">Payment Mode</th>
            <th class="r" style="width:120px;">Amount (₹)</th>
            <th class="c" style="width:80px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($incList as $i => $inc)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="white-space:nowrap; color:#475569;">{{ $inc->income_date ? \Carbon\Carbon::parse($inc->income_date)->format('d-M-Y') : '—' }}</td>
            <td><strong>{{ $inc->title }}</strong></td>
            <td>{{ $inc->category->name ?? 'General Income' }}</td>
            <td>{{ $inc->project->project_name ?? 'Head Office / General' }}</td>
            <td style="text-transform:capitalize; color:#475569;">{{ str_replace('_', ' ', $inc->payment_mode ?: 'Cash') }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($inc->amount, 2, false) }}</td>
            <td class="c"><span class="badge badge-success">Received</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:24px;color:#64748B;"><em>No income records found matching the criteria.</em></td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="6" class="r"><strong>GRAND TOTAL REALIZED INFLOWS:</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($totAmount) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>

@include('admin.components.pdf-footer')
</body>
</html>