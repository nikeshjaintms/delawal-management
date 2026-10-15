<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Voucher Register & Disbursement Ledger - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Expense Voucher Register & Disbursement Ledger',
    'orientation' => 'landscape',
    'backUrl' => route('expenses.index')
])

@php
    $filters = [];
    if(request('date_from') || request('date_to') || request('filter_date')) {
        $fDate = request('filter_date') ?: (request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('d M Y') : 'Earliest');
        $tDate = request('filter_date') ?: (request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('d M Y') : 'Present');
        $filters['Period'] = $fDate . ' to ' . $tDate;
    }
    if(!empty($activeType)) {
        $filters['Expense Stream'] = $activeType;
    }
    if(request('filter_project')) {
        $p = \App\Models\Project::find(request('filter_project'));
        if($p) $filters['Project / Site'] = $p->project_name ?? $p->name;
    }
    if(request('filter_category')) {
        $filters['Category'] = request('filter_category');
    }
    if(request('filter_vendor')) {
        $v = \App\Models\Vendor::find(request('filter_vendor'));
        if($v) $filters['Beneficiary / Vendor'] = $v->name;
    }
    if(request('filter_mode')) {
        $filters['Payment Mode'] = ucfirst(request('filter_mode'));
    }
    if(request('filter_status')) {
        $filters['Status'] = ucfirst(request('filter_status'));
    }

    $fromDate = request('date_from') ?: request('filter_date');
    $toDate   = request('date_to') ?: request('filter_date');
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Expense Voucher Register & Disbursement Ledger',
    'subtitle' => 'Detailed Audit Trail of Site, Project, Operational & Corporate Expenditures',
    'financialYear' => ($fromDate ? \Carbon\Carbon::parse($fromDate)->format('M Y') : 'FY ' . (date('m') >= 4 ? date('Y').'-'.(date('y')+1) : (date('Y')-1).'-'.date('y'))) . ($toDate ? ' - ' . \Carbon\Carbon::parse($toDate)->format('M Y') : ''),
    'period' => ($fromDate ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'All Recorded Expenses') . ' to ' . ($toDate ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'Present'),
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Expense Vouchers</div>
        <div class="s-value">{{ $totalExpensesCount ?? $expenses->count() }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Gross Expenditure</div>
        <div class="s-value">{{ formatIndianCurrency($totalExpenseAmount ?? $expenses->sum('amount')) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Approved & Disbursed</div>
        <div class="s-value">{{ formatIndianCurrency($approvedExpenseAmount ?? $expenses->where('approval_status', 'Approved')->sum('amount')) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Pending Approval</div>
        <div class="s-value">{{ formatIndianCurrency($pendingExpenseAmount ?? $expenses->where('approval_status', 'Pending')->sum('amount')) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-file-invoice-dollar"></i> Expense Vouchers & Payment Audit Trail</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:75px;">Voucher No.</th>
            <th style="width:70px;">Date</th>
            <th>Expense Particulars & Title</th>
            <th>Category / Account Head</th>
            <th>Beneficiary / Paid To</th>
            <th>Project / Property Alloc.</th>
            <th style="width:70px;">Mode</th>
            <th class="r" style="width:95px;">Amount (₹)</th>
            <th class="c" style="width:70px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $i => $e)
        @php
            $alloc = 'Head Office / General';
            if ($e->project) {
                $alloc = $e->project->project_name ?? $e->project->name;
            } elseif ($e->property) {
                $alloc = $e->property->property_name ?? 'Property';
            } elseif ($e->properties && $e->properties->count() > 0) {
                $alloc = $e->properties->first()->property_name;
            }

            $payee = $e->paid_to ?: ($e->vendor->name ?? ($e->tenant->name ?? '—'));
            $status = $e->approval_status ?: ($e->status ?: 'Approved');
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i + 1 }}</td>
            <td style="font-weight:700; color:#1E3A8A;">EXP-{{ str_pad($e->id, 5, '0', STR_PAD_LEFT) }}</td>
            <td style="white-space:nowrap; color:#475569;">{{ $e->expense_date ? \Carbon\Carbon::parse($e->expense_date)->format('d-M-Y') : '—' }}</td>
            <td>
                <strong>{{ $e->expense_title ?: ($e->title ?: 'Expense Voucher') }}</strong>
                @if(!empty($e->bill_no))
                    <div style="font-size:8px; color:#64748B;">Bill/Ref: {{ $e->bill_no }}</div>
                @endif
            </td>
            <td>{{ $e->expenseCategory->name ?? ($e->expense_category ?: ($e->category->name ?? 'General')) }}</td>
            <td style="color:#334155;">{{ $payee }}</td>
            <td style="color:#475569;">{{ $alloc }}</td>
            <td style="text-transform:capitalize; color:#475569;">{{ str_replace('_', ' ', $e->payment_mode ?: 'Cash') }}</td>
            <td class="r" style="font-weight:700; color:#DC2626;">{{ formatIndianCurrency($e->amount, 2, false) }}</td>
            <td class="c">
                @if(strtolower($status) === 'approved')
                    <span class="badge badge-success">Approved</span>
                @elseif(strtolower($status) === 'pending')
                    <span class="badge badge-warning">Pending</span>
                @elseif(strtolower($status) === 'rejected')
                    <span class="badge badge-danger">Rejected</span>
                @else
                    <span class="badge badge-neutral">{{ ucfirst($status) }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="10" class="c" style="padding:24px; color:#64748B;">
                <em>No expense vouchers found matching the specified parameters.</em>
            </td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="8" class="r"><strong>TOTAL EXPENDITURE VOUCHERS AMOUNT:</strong></td>
            <td class="r" style="color:#DC2626; font-size:11px;"><strong>{{ formatIndianCurrency($totalExpenseAmount ?? $expenses->sum('amount')) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>

@include('admin.components.pdf-footer')
</body>
</html>