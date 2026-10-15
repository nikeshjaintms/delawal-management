<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Summary Ledger Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Expense Summary Report',
    'orientation' => 'landscape',
    'backUrl' => route('expense-report.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Expenditure Register & Expense Ledger',
    'subtitle' => 'Operational Expenses, Administrative Outlays & Site Costing Statement'
])

{{-- ── KPI Summary Cards ── --}}
<div class="stat-row">
    <div class="stat-box s-red">
        <div class="s-label">Total Expenditure Amount</div>
        <div class="s-value">{{ formatIndianCurrency($totalAmount ?? $expenses->sum('amount')) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Approved &amp; Settled Expenses</div>
        <div class="s-value">{{ formatIndianCurrency($paidAmount ?? $expenses->sum('amount')) }}</div>
    </div>
    <div class="stat-box s-amber">
        <div class="s-label">Pending Approval Outlay</div>
        <div class="s-value">{{ formatIndianCurrency($pendingAmount ?? 0) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Total Vouchers</div>
        <div class="s-value">{{ $expenses->count() }} Entries</div>
    </div>
</div>

{{-- ── 1. CATEGORY-WISE EXPENSE SUB-TOTALS ── --}}
@if(isset($byCategory) && count($byCategory) > 0)
<div class="section-label">
    <i class="fa-solid fa-chart-pie" style="color:#2563EB;"></i> Expenditure Head-Wise Summary
</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Expense Category Head</th>
            <th class="r" style="width:140px;">Subtotal Amount (₹)</th>
            <th class="r" style="width:100px;">Expense Share</th>
        </tr>
    </thead>
    <tbody>
        @php
            $grandCalc = ($totalAmount ?? $expenses->sum('amount')) > 0 ? ($totalAmount ?? $expenses->sum('amount')) : 1;
            $catIdx = 1;
        @endphp
        @foreach($byCategory as $cName => $cAmt)
        <tr>
            <td class="c" style="color:#64748B;">{{ $catIdx++ }}</td>
            <td><strong>{{ $cName ?: 'General Operational' }}</strong></td>
            <td class="r" style="font-weight:700;">{{ formatIndianCurrency($cAmt) }}</td>
            <td class="r" style="color:#64748B;">{{ number_format(($cAmt / $grandCalc) * 100, 1) }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ── 2. DETAILED EXPENSE REGISTER ── --}}
<div class="section-label" style="margin-top:14px;">
    <i class="fa-solid fa-receipt" style="color:#0F172A;"></i> Detailed Expenditure Ledger
</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:65px;">Date</th>
            <th style="width:75px;">Bill / Vch #</th>
            <th>Expense Title &amp; Description</th>
            <th>Category</th>
            <th>Project / Property</th>
            <th>Payee / Vendor</th>
            <th class="c" style="width:65px;">Mode</th>
            <th class="r" style="width:105px;">Amount (₹)</th>
            <th class="c" style="width:65px;">Approval</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $i => $e)
        @php
            $cat = $e->expenseCategory?->name ?? ($e->category?->name ?? ($e->expense_category ?: 'General'));
            $proj = $e->project?->project_name ?: ($e->property?->property_name ?? 'Head Office');
            $payee = $e->paid_to ?: ($e->vendor?->name ?? '—');
            $st = strtolower($e->approval_status ?? 'approved');
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="white-space:nowrap;">{{ $e->expense_date ? \Carbon\Carbon::parse($e->expense_date)->format('d M Y') : '—' }}</td>
            <td style="font-size:8px;">
                <strong>{{ $e->bill_no ?: ('EXP-' . $e->id) }}</strong>
                @if(!empty($e->invoice_no))
                    <div style="font-size:7.5px; color:#64748B;">Inv: {{ $e->invoice_no }}</div>
                @endif
            </td>
            <td>
                <strong>{{ $e->expense_title ?: ($e->title ?: 'Expense Outflow') }}</strong>
                @if(!empty($e->description))
                    <div style="font-size:7.5px; color:#64748B;">{{ $e->description }}</div>
                @endif
            </td>
            <td>{{ $cat }}</td>
            <td>{{ $proj }}</td>
            <td>{{ $payee }}</td>
            <td class="c">
                <span class="badge">{{ ucfirst($e->payment_mode ?: 'Cash') }}</span>
            </td>
            <td class="r" style="font-weight:700; color:#DC2626;">
                {{ formatIndianCurrency($e->amount ?? 0) }}
            </td>
            <td class="c">
                <span class="badge {{ $st === 'approved' ? 'badge-success' : ($st === 'rejected' ? 'badge-danger' : 'badge-warning') }}">
                    {{ ucfirst($e->approval_status ?? 'Approved') }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="10" class="c" style="padding:24px; color:#64748B;">No expense vouchers found for the selected period.</td>
        </tr>
        @endforelse
    </tbody>
    @if($expenses->count() > 0)
    <tfoot>
        <tr class="double-total">
            <td colspan="8" class="r">TOTAL EXPENDITURE:</td>
            <td class="r" style="color:#DC2626; font-size:10px; font-weight:900;">
                {{ formatIndianCurrency($totalAmount ?? $expenses->sum('amount')) }}
            </td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- ── Signatures Block ── --}}
<div class="auth-block" style="margin-top:20px; page-break-inside:avoid;">
    <div class="auth-col">
        <div class="auth-line">Disbursing Cashier / Accountant</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Approving Authority / Delawala Management</div>
    </div>
</div>

@include('admin.components.pdf-footer', ['note' => 'Official Expense Statement • Audited from Approved Accounting Vouchers'])
</body>
</html>