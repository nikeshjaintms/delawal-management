@php
    $typeTitle = 'Expenses Ledger Report';
    $backRoute = route('expenses.index');
    if (isset($activeType) && $activeType) {
        if ($activeType === 'Property') {
            $typeTitle = 'Property Expenses Report';
            $backRoute = route('expenses.property');
        } elseif ($activeType === 'Project') {
            $typeTitle = 'Project Expenses Report';
            $backRoute = route('expenses.project-wise');
        } elseif ($activeType === 'General') {
            $typeTitle = 'General Expenses Report';
            $backRoute = route('expenses.general');
        } elseif ($activeType === 'Rental') {
            $typeTitle = 'Rental Expenses Report';
            $backRoute = route('expenses.rental');
        } elseif ($activeType === 'Personal') {
            $typeTitle = 'Personal Expenses Report';
            $backRoute = route('expenses.personal');
        } else {
            $typeTitle = $activeType . ' Expenses Report';
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $typeTitle }} - Delawala Management</title>
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
@include('admin.components.pdf-action-bar', [
    'title' => $typeTitle,
    'orientation' => 'landscape',
    'backUrl' => $backRoute
])

<div class="rpt-header" style="display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:14px; margin-bottom:18px; border-bottom:2.5px solid #D97706; gap:16px;">
    <div style="display:flex; align-items:center; gap:14px;">
        <img src="{{ asset('images/logo.png') }}" alt="Delawala Properties" style="height:52px; width:auto; object-fit:contain;" onerror="this.style.display='none';">
        <div style="display:flex; flex-direction:column; gap:2px;">
            <div class="co-name" style="font-size:20px; font-weight:800; color:#0F172A; text-transform:uppercase; letter-spacing:0.3px; line-height:1.1;">Delawala Properties</div>
            <div class="co-sub" style="font-size:9.5px; color:#D97706; font-weight:700; letter-spacing:1.2px; text-transform:uppercase;">Delawala Infra Co. &bull; Real Estate &amp; Management</div>
            <div style="font-size:10px; color:#475569; line-height:1.3; margin-top:1px;">
                <i class="fa-solid fa-location-dot" style="color:#D97706; font-size:9px; margin-right:3px;"></i>Ground Floor, F F SH No. 116, Aman Plazza, Dahegam Road, Dahegam, Bharuch - 392012
            </div>
            <div style="display:flex; align-items:center; gap:6px; margin-top:3px; flex-wrap:wrap;">
                <span style="display:inline-flex; align-items:center; background:#FEF3C7; color:#92400E; border:1px solid #FCD34D; padding:1px 6px; border-radius:4px; font-size:9.5px; font-weight:700; letter-spacing:0.3px;"><strong>GSTIN:</strong> 24CUBPD0770R1ZI</span>
                <span style="display:inline-flex; align-items:center; background:#F1F5F9; color:#334155; border:1px solid #CBD5E1; padding:1px 6px; border-radius:4px; font-size:9px;"><strong>Proprietor:</strong> Delawala Zafar</span>
            </div>
        </div>
    </div>
    <div class="rpt-meta" style="text-align:right; flex-shrink:0;">
        <div class="rpt-title" style="font-size:16px; font-weight:800; color:#0F172A; letter-spacing:0.2px; line-height:1.2;">Official Document</div>
        <div class="rpt-date" style="font-size:10px; color:#64748B; margin-top:3px;">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>
    </div>
    <div class="rpt-meta">
        <div class="rpt-title">{{ $typeTitle }}</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total {{ isset($activeType) && $activeType ? $activeType : '' }} Vouchers</div>
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

<div class="section-label">&#9632; {{ isset($activeType) && $activeType ? $activeType : 'All' }} Expense Records</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;">#</th>
            <th>Date</th>
            <th>Category</th>
            <th>Paid To / Payee</th>
            @if(($activeType ?? '') === 'Rental')
                <th>Rental Property</th>
                <th>Tenant / Agreement</th>
            @elseif(($activeType ?? '') === 'Personal')
                <th>Expense Title / Notes</th>
                <th>Firm(s)</th>
            @else
                <th>Project / Property</th>
                <th>Firm(s)</th>
            @endif
            <th>Mode</th>
            <th>Ref / Bill</th>
            <th class="r">Amount</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $i => $item)
        <tr>
            <td style="color:#9CA3AF;">{{ $i+1 }}</td>
            <td style="white-space:nowrap;">{{ $item->expense_date ? \Carbon\Carbon::parse($item->expense_date)->format('d M Y') : '-' }}</td>
            <td><strong>{{ $item->expense_category ?: ($item->expenseCategory->name ?? '-') }}</strong></td>
            <td>{{ $item->paid_to ?: ($item->vendor->name ?? '-') }}</td>

            @if(($activeType ?? '') === 'Rental')
                <td>
                    @php $p = $item->property ?? ($item->rental?->property ?? null); @endphp
                    {{ $p ? $p->property_name : '-' }}
                </td>
                <td>
                    @php $t = $item->tenant ?? ($item->rental?->tenant ?? null); @endphp
                    {{ $t ? $t->name : ($item->rental?->tenant_name ?? 'Direct Property') }}
                </td>
            @elseif(($activeType ?? '') === 'Personal')
                <td>{{ $item->expense_title ?: ($item->description ?: 'Personal Drawing') }}</td>
                <td>
                    @php $fNames = $item->firms->isNotEmpty() ? $item->firms->pluck('firm_name')->implode(', ') : ($item->firm->firm_name ?? '-'); @endphp
                    {{ $fNames }}
                </td>
            @else
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
            @endif

            <td>{{ $item->payment_mode ?: '-' }}</td>
            <td>{{ $item->reference_no ?: ($item->bill_no ?: '-') }}</td>
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
        <tr>
            <td colspan="10" style="text-align:center;padding:26px;color:#64748B;">
                <div style="font-size:13px;font-weight:700;margin-bottom:4px;color:#0F1F35;">No {{ isset($activeType) && $activeType ? strtolower($activeType) . ' ' : '' }}expense records found.</div>
                <div style="font-size:11px;color:#94A3B8;">There are no expenses recorded under this category matching the current filters.</div>
            </td>
        </tr>
        @endforelse
    </tbody>
    @if($expenses->count() > 0)
    <tfoot>
        <tr>
            <td colspan="8" style="font-size:11px;">Total ({{ $expenses->count() }} vouchers)</td>
            <td class="r">₹{{ number_format($totalExpenseAmount, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; {{ $typeTitle }}</span>
    <span>{{ $expenses->count() }} vouchers &nbsp;|&nbsp; Total: ₹{{ number_format($totalExpenseAmount, 2) }} &nbsp;|&nbsp; {{ now()->format('d M Y') }}</span>
</div>

</body>
</html>
