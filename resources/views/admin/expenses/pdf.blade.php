<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses Report - Delawala Management</title>
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Segoe UI',Arial,sans-serif; font-size:11.5px; color:#0F1F35; background:#fff; padding:24px; }

        /* ── Header ── */
        .rpt-header { display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:16px; margin-bottom:20px; border-bottom:2.5px solid #fc6900ff; }
        .co-name  { font-size:22px; font-weight:800; color:#0F1F35; letter-spacing:0.4px; }
        .co-sub   { font-size:10px; color:#fc6900ff; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-top:3px; }
        .rpt-meta { text-align:right; }
        .rpt-meta .rpt-title { font-size:16px; font-weight:700; color:#0F1F35; margin-bottom:4px; }
        .rpt-meta .rpt-date  { font-size:11px; color:#64748B; }

        /* ── Stat Row ── */
        .stat-row { display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; }
        .stat-box { flex:1; min-width:110px; border:1px solid #E5E7EB; border-radius:8px; padding:10px 12px; background:#F8FAFC; }
        .stat-box .s-label { font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#64748B; }
        .stat-box .s-value { font-size:16px; font-weight:800; margin-top:4px; color:#0F1F35; }
        .stat-box.s-orange { border-color:rgba(252,105,0,0.3); background:rgba(252,105,0,0.03); }
        .stat-box.s-orange .s-value { color:#e05c00; }
        .stat-box.s-green { border-color:rgba(16,185,129,0.3); background:rgba(16,185,129,0.03); }
        .stat-box.s-green .s-value { color:#059669; }
        .stat-box.s-amber { border-color:rgba(245,158,11,0.3); background:rgba(245,158,11,0.03); }
        .stat-box.s-amber .s-value { color:#B45309; }

        /* ── Main table ── */
        .section-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#fc6900ff; margin-bottom:8px; margin-top:16px; padding-bottom:4px; border-bottom:1px solid #E5E7EB; }
        table { width:100%; border-collapse:collapse; font-size:11px; }
        thead tr { background:#0F172A; }
        thead th { padding:8px 10px; color:#FFF; font-weight:600; text-align:left; font-size:9.5px; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap; }
        thead th.r { text-align:right; }
        thead th.c { text-align:center; }
        tbody tr:nth-child(even) { background:#F9FAFB; }
        tbody td { padding:8px 10px; border-bottom:1px solid #F1F5F9; vertical-align:middle; }
        tbody td.r { text-align:right; font-weight:700; color:#0F1F35; }
        tbody td.c { text-align:center; }
        tbody tr:last-child td { border-bottom:none; }
        tfoot tr { background:#F1F5F9; }
        tfoot td { padding:9px 10px; font-weight:800; border-top:2px solid #E5E7EB; }
        tfoot td.r { text-align:right; color:#e05c00; font-size:12px; }

        .badge { display:inline-block; padding:2px 7px; border-radius:10px; font-size:9px; font-weight:700; text-transform:uppercase; }
        .badge-success { background:#ECFDF5; color:#059669; border:1px solid #A7F3D0; }
        .badge-warning { background:#FFFBEB; color:#D97706; border:1px solid #FDE68A; }
        .badge-danger  { background:#FEF2F2; color:#DC2626; border:1px solid #FECACA; }

        /* ── Footer ── */
        .rpt-footer { margin-top:24px; padding-top:10px; border-top:1px solid #E5E7EB; display:flex; justify-content:space-between; color:#9CA3AF; font-size:9.5px; }

        @media print { body { padding:10px; } @page { margin:8mm; size:landscape; } }
    </style>
</head>
<body>

<div class="rpt-header">
    <div>
        <div class="co-name">Delawala</div>
        <div class="co-sub">Properties &amp; Management</div>
    </div>
    <div class="rpt-meta">
        <div class="rpt-title">Expenses Ledger Report</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Expense Vouchers</div>
        <div class="s-value">{{ $totalExpensesCount }}</div>
    </div>
    <div class="stat-box s-orange">
        <div class="s-label">Total Expense Amount</div>
        <div class="s-value">₹{{ number_format($totalExpenseAmount, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Approved Expenses</div>
        <div class="s-value">₹{{ number_format($approvedExpenseAmount, 2) }}</div>
    </div>
    <div class="stat-box s-amber">
        <div class="s-label">Pending Approval</div>
        <div class="s-value">₹{{ number_format($pendingExpenseAmount, 2) }}</div>
    </div>
</div>

<div class="section-label">&#9632; Expense Records</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;">#</th>
            <th>Date</th>
            <th>Title / Description</th>
            <th>Category</th>
            <th>Paid To / Vendor</th>
            <th>Project / Property</th>
            <th>Firm(s)</th>
            <th>Mode</th>
            <th>Bill / Ref</th>
            <th class="r">Amount</th>
            <th class="c">Approval Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $i => $item)
        <tr>
            <td style="color:#9CA3AF;">{{ $i+1 }}</td>
            <td style="white-space:nowrap;">{{ $item->expense_date ? \Carbon\Carbon::parse($item->expense_date)->format('d M Y') : '-' }}</td>
            <td><strong>{{ $item->expense_title }}</strong></td>
            <td>{{ $item->expenseCategory->name ?? ($item->expense_category ?: '-') }}</td>
            <td>{{ $item->paid_to ?: '-' }}</td>
            <td>
                @if($item->project)
                    <span>{{ $item->project->project_name }}</span>
                @elseif($item->property)
                    <span>{{ $item->property->property_name }}</span>
                @else
                    <span style="color:#9CA3AF;">General</span>
                @endif
            </td>
            <td>
                @php $fNames = $item->firms->isNotEmpty() ? $item->firms->pluck('firm_name')->implode(', ') : ($item->firm->firm_name ?? '-'); @endphp
                {{ $fNames }}
            </td>
            <td>{{ $item->payment_mode ?: '-' }}</td>
            <td>{{ $item->bill_no ?: '-' }}</td>
            <td class="r">₹{{ number_format($item->amount, 2) }}</td>
            <td class="c">
                @if($item->approval_status === 'Approved')
                    <span class="badge badge-success">Approved</span>
                @elseif($item->approval_status === 'Pending')
                    <span class="badge badge-warning">Pending</span>
                @else
                    <span class="badge badge-danger">{{ ucfirst($item->approval_status) }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="11" style="text-align:center;padding:20px;color:#64748B;">No expense records found.</td></tr>
        @endforelse
    </tbody>
    @if($expenses->count() > 0)
    <tfoot>
        <tr>
            <td colspan="9" style="font-size:11px;">Total ({{ $expenses->count() }} vouchers)</td>
            <td class="r">₹{{ number_format($totalExpenseAmount, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; Expenses Report</span>
    <span>{{ $expenses->count() }} vouchers &nbsp;|&nbsp; Total: ₹{{ number_format($totalExpenseAmount, 2) }} &nbsp;|&nbsp; {{ now()->format('d M Y') }}</span>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
