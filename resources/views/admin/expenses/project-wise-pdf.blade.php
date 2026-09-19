<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project-wise Expenses & Contractor Ledger Report - Delawala Management</title>
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Segoe UI',Arial,sans-serif; font-size:11px; color:#0F1F35; background:#fff; padding:20px; }

        /* ── Header ── */
        .rpt-header { display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:14px; margin-bottom:16px; border-bottom:2.5px solid #2563EB; }
        .co-name  { font-size:20px; font-weight:800; color:#0F1F35; letter-spacing:0.4px; }
        .co-sub   { font-size:9.5px; color:#2563EB; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-top:2px; }
        .rpt-meta { text-align:right; }
        .rpt-meta .rpt-title { font-size:15px; font-weight:700; color:#0F1F35; margin-bottom:3px; }
        .rpt-meta .rpt-date  { font-size:10.5px; color:#64748B; }

        /* ── Stat Row ── */
        .stat-row { display:flex; gap:10px; margin-bottom:18px; }
        .stat-box { flex:1; border:1px solid #E2E8F0; border-radius:8px; padding:10px 12px; background:#F8FAFC; }
        .stat-box .s-label { font-size:8.5px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#64748B; }
        .stat-box .s-value { font-size:14px; font-weight:800; margin-top:3px; color:#0F1F35; }
        .stat-box.s-amber { border-color:rgba(245,158,11,0.4); background:rgba(245,158,11,0.05); }
        .stat-box.s-amber .s-value { color:#B45309; }
        .stat-box.s-purple { border-color:rgba(139,92,246,0.4); background:rgba(139,92,246,0.05); }
        .stat-box.s-purple .s-value { color:#6D28D9; }
        .stat-box.s-blue { border-color:rgba(59,130,246,0.4); background:rgba(59,130,246,0.05); }
        .stat-box.s-blue .s-value { color:#1D4ED8; }
        .stat-box.s-rose { border-color:rgba(244,63,94,0.4); background:rgba(244,63,94,0.05); }
        .stat-box.s-rose .s-value { color:#BE123C; }
        .stat-box.s-violet { border-color:rgba(168,85,247,0.4); background:rgba(168,85,247,0.05); }
        .stat-box.s-violet .s-value { color:#7E22CE; }
        .stat-box.s-teal { border-color:rgba(20,184,166,0.4); background:rgba(20,184,166,0.05); }
        .stat-box.s-teal .s-value { color:#0F766E; }
        .stat-box.s-green { border-color:rgba(16,185,129,0.4); background:rgba(16,185,129,0.05); }
        .stat-box.s-green .s-value { color:#047857; }

        /* ── Project Section ── */
        .project-section { margin-bottom:20px; page-break-inside:avoid; border:1px solid #E2E8F0; border-radius:8px; overflow:hidden; }
        .project-head { background:#0F172A; color:#FFF; padding:10px 14px; display:flex; justify-content:space-between; align-items:center; }
        .project-head-title { font-size:13px; font-weight:800; }
        .project-head-sub { font-size:10px; color:#94A3B8; margin-top:2px; }
        .project-head-cost { font-size:14px; font-weight:800; color:#FBBF24; text-align:right; }

        .project-subribbon { background:#F1F5F9; padding:6px 14px; display:flex; gap:16px; font-size:10px; font-weight:700; border-bottom:1px solid #E2E8F0; color:#475569; flex-wrap:wrap; }

        .table-title { padding:6px 14px; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; background:#F8FAFC; border-bottom:1px solid #E2E8F0; color:#1E293B; }
        table { width:100%; border-collapse:collapse; font-size:10px; }
        thead tr { background:#F8FAFC; border-bottom:1.5px solid #CBD5E1; }
        thead th { padding:6px 10px; color:#475569; font-weight:700; text-align:left; font-size:9px; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap; }
        thead th.r { text-align:right; }
        thead th.c { text-align:center; }
        tbody tr:nth-child(even) { background:#FAFAFA; }
        tbody td { padding:6px 10px; border-bottom:1px solid #F1F5F9; vertical-align:middle; }
        tbody td.r { text-align:right; font-weight:700; }
        tbody td.c { text-align:center; }
        tbody tr:last-child td { border-bottom:none; }

        .badge { display:inline-block; padding:1px 6px; border-radius:8px; font-size:8.5px; font-weight:700; text-transform:uppercase; }
        .badge-approved { background:#ECFDF5; color:#059669; border:1px solid #A7F3D0; }
        .badge-pending  { background:#FFFBEB; color:#D97706; border:1px solid #FDE68A; }
        .badge-rejected { background:#FEF2F2; color:#DC2626; border:1px solid #FECACA; }

        .rpt-footer { margin-top:20px; padding-top:10px; border-top:1px solid #E5E7EB; display:flex; justify-content:space-between; color:#9CA3AF; font-size:9px; }

        @media print { body { padding:8px; } @page { margin:6mm; size:landscape; } }
    </style>
</head>
<body>

<div class="rpt-header">
    <div>
        <div class="co-name">Delawala</div>
        <div class="co-sub">Properties &amp; Management</div>
    </div>
    <div class="rpt-meta">
        <div class="rpt-title">Project-wise Expenses &amp; Outgoing Ledger</div>
        <div class="rpt-date">Printed on: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

<div class="stat-row">
    <div class="stat-box s-amber">
        <div class="s-label">Total Grand Expenses</div>
        <div class="s-value">₹{{ number_format($grandTotal, 2) }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">PO &amp; Procurement</div>
        <div class="s-value">₹{{ number_format($grandPoTotal, 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Direct Site Expenses</div>
        <div class="s-value">₹{{ number_format($grandDirectTotal, 2) }}</div>
    </div>
    <div class="stat-box s-rose">
        <div class="s-label">Contractor Payments</div>
        <div class="s-value">₹{{ number_format($grandContractorTotal, 2) }}</div>
    </div>
    <div class="stat-box s-violet">
        <div class="s-label">Broker Commission</div>
        <div class="s-value">₹{{ number_format($grandBrokerTotal ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-teal">
        <div class="s-label">Land Acquisition</div>
        <div class="s-value">₹{{ number_format($grandLandTotal ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Approved &amp; Paid</div>
        <div class="s-value">₹{{ number_format($grandApprovedTotal, 2) }}</div>
    </div>
</div>

@foreach($projectsData as $pData)
    @php
        $p = $pData['project'];
        $expList = $pData['expenses'];
        $cpList = $pData['contractorPayments'] ?? collect();
        $bcList = $pData['brokerCommissions'] ?? collect();
        $lpList = $pData['landPayments'] ?? collect();
    @endphp
    @if($expList->isNotEmpty() || $cpList->isNotEmpty() || $bcList->isNotEmpty() || $lpList->isNotEmpty())
    <div class="project-section">
        <div class="project-head">
            <div>
                <div class="project-head-title">{{ $p->project_name }}</div>
                <div class="project-head-sub">
                    {{ $p->firm->firm_name ?? '' }} 
                    @if($p->propertyMaster) &bull; {{ $p->propertyMaster->property_name }} @endif
                    &bull; {{ $pData['expensesCount'] }} Direct/PO Expenses
                    &bull; {{ $pData['contractorsCount'] }} Contractor Payments
                    &bull; {{ $pData['brokersCount'] }} Broker Commissions
                    &bull; {{ $pData['landCount'] }} Land Payments
                </div>
            </div>
            <div class="project-head-cost">
                <div style="font-size:9px; text-transform:uppercase; color:#CBD5E1;">Project Total</div>
                ₹{{ number_format($pData['totalCost'], 2) }}
            </div>
        </div>

        <div class="project-subribbon">
            <span>PO &amp; Materials: <strong style="color:#6D28D9;">₹{{ number_format($pData['poCost'], 2) }}</strong></span>
            <span>Direct Site: <strong style="color:#1D4ED8;">₹{{ number_format($pData['directCost'], 2) }}</strong></span>
            <span>Contractors: <strong style="color:#BE123C;">₹{{ number_format($pData['contractorCost'], 2) }}</strong></span>
            <span>Brokers: <strong style="color:#7E22CE;">₹{{ number_format($pData['brokerCost'], 2) }}</strong></span>
            <span>Land: <strong style="color:#0F766E;">₹{{ number_format($pData['landCost'], 2) }}</strong></span>
            <span>Approved: <strong style="color:#047857;">₹{{ number_format($pData['approvedCost'], 2) }}</strong></span>
            <span>Pending: <strong style="color:#B45309;">₹{{ number_format($pData['pendingCost'], 2) }}</strong></span>
        </div>

        {{-- Direct / PO Expenses Table --}}
        @if($expList->isNotEmpty())
        <div class="table-title">Direct Site &amp; Purchase Order Expenses ({{ $expList->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th style="width:20px;">#</th>
                    <th>Date</th>
                    <th>Expense Title / Details</th>
                    <th>Source / Type</th>
                    <th>Category</th>
                    <th>Paid To / Vendor</th>
                    <th class="r">Amount</th>
                    <th>Mode</th>
                    <th>Ref No</th>
                    <th class="c">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expList as $eIdx => $exp)
                <tr>
                    <td style="color:#64748B;">{{ $eIdx + 1 }}</td>
                    <td style="white-space:nowrap;">{{ $exp->expense_date ? \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') : '-' }}</td>
                    <td>
                        <strong>{{ $exp->expense_title ?? $exp->description }}</strong>
                        @if($exp->relationLoaded('properties') && $exp->properties->isNotEmpty())
                            <span style="color:#64748B; font-size:9.5px;">({{ $exp->properties->pluck('property_name')->implode(', ') }})</span>
                        @elseif($exp->property)
                            <span style="color:#64748B; font-size:9.5px;">({{ $exp->property->property_name }})</span>
                        @endif
                    </td>
                    <td>
                        {{ $exp->purchase_order_id ? 'PO #'.$exp->purchaseOrder?->po_number : 'Direct Expense' }}
                    </td>
                    <td>{{ $exp->expense_category ?: ($exp->expenseCategory->name ?? '-') }}</td>
                    <td>{{ $exp->paid_to ?: ($exp->vendor->name ?? '-') }}</td>
                    <td class="r" style="color:#B91C1C;">₹{{ number_format($exp->amount, 2) }}</td>
                    <td>{{ $exp->payment_mode ?: '-' }}</td>
                    <td>{{ $exp->reference_no ?: '-' }}</td>
                    <td class="c">
                        @php $st = strtolower($exp->approval_status ?? 'pending'); @endphp
                        <span class="badge badge-{{ $st }}">{{ ucfirst($exp->approval_status ?? 'Pending') }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Contractor Payments Table --}}
        @if($cpList->isNotEmpty())
        <div class="table-title" style="background:rgba(244,63,94,0.06); color:#9F1239; border-top:1px solid #E2E8F0;">
            Contractor Payments &amp; Installments ({{ $cpList->count() }})
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:20px;">#</th>
                    <th>Payment Date</th>
                    <th>Contractor Name</th>
                    <th>Work Type</th>
                    <th>Assigned Plot / Unit</th>
                    <th class="r">Amount Paid</th>
                    <th>Payment Mode</th>
                    <th>Ref / Bill No</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cpList as $cIdx => $cp)
                <tr>
                    <td style="color:#64748B;">{{ $cIdx + 1 }}</td>
                    <td style="white-space:nowrap;">{{ $cp->payment_date ? \Carbon\Carbon::parse($cp->payment_date)->format('d M Y') : '-' }}</td>
                    <td><strong>{{ $cp->contractor->contractor_name ?? '-' }}</strong></td>
                    <td>{{ $cp->contractor->work_type ?? 'Contractor Work' }}</td>
                    <td>
                        @if($cp->property)
                            {{ $cp->property->property_name }}
                        @elseif($cp->contractor && $cp->contractor->relationLoaded('properties') && $cp->contractor->properties->isNotEmpty())
                            {{ $cp->contractor->properties->pluck('property_name')->first() }}
                        @else
                            Project-Wide
                        @endif
                    </td>
                    <td class="r" style="color:#B91C1C;">₹{{ number_format($cp->amount, 2) }}</td>
                    <td>{{ $cp->payment_mode ?: '-' }} {{ $cp->bank_name ? '('.$cp->bank_name.')' : '' }}</td>
                    <td>{{ $cp->reference_no ?: ($cp->bill_no ?: '-') }}</td>
                    <td>{{ $cp->remarks ?: ($cp->payment_type ?: 'Paid') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Broker Commissions Table --}}
        @if($bcList->isNotEmpty())
        <div class="table-title" style="background:rgba(168,85,247,0.06); color:#6B21A8; border-top:1px solid #E2E8F0;">
            Broker Commissions &amp; Referral Payouts ({{ $bcList->count() }})
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:20px;">#</th>
                    <th>Date</th>
                    <th>Broker Name</th>
                    <th>Property / Booking</th>
                    <th>Commission Type</th>
                    <th class="r">Amount</th>
                    <th class="c">Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bcList as $bIdx => $bc)
                <tr>
                    <td style="color:#64748B;">{{ $bIdx + 1 }}</td>
                    <td style="white-space:nowrap;">{{ $bc->payment_date ? \Carbon\Carbon::parse($bc->payment_date)->format('d M Y') : '-' }}</td>
                    <td><strong>{{ $bc->broker->name ?? '-' }}</strong></td>
                    <td>{{ $bc->property->property_name ?? ($bc->booking->property->property_name ?? 'Project Sale') }}</td>
                    <td>{{ $bc->commission_type ?: 'Percentage' }} {{ $bc->commission_value ? '('.$bc->commission_value.'%)' : '' }}</td>
                    <td class="r" style="color:#B91C1C;">₹{{ number_format($bc->commission_amount, 2) }}</td>
                    <td class="c">
                        <span class="badge badge-approved">{{ ucfirst($bc->payment_status ?: 'Paid') }}</span>
                    </td>
                    <td>{{ $bc->remarks ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Land Acquisition Table --}}
        @if($lpList->isNotEmpty())
        <div class="table-title" style="background:rgba(20,184,166,0.06); color:#115E59; border-top:1px solid #E2E8F0;">
            Land Acquisition &amp; Seller Payments ({{ $lpList->count() }})
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:20px;">#</th>
                    <th>Date</th>
                    <th>Land / Property Master</th>
                    <th>Seller / Landowner</th>
                    <th class="r">Amount Paid</th>
                    <th>Payment Mode</th>
                    <th>Ref No</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lpList as $lIdx => $lp)
                <tr>
                    <td style="color:#64748B;">{{ $lIdx + 1 }}</td>
                    <td style="white-space:nowrap;">{{ $lp->payment_date ? \Carbon\Carbon::parse($lp->payment_date)->format('d M Y') : '-' }}</td>
                    <td><strong>{{ $lp->propertyMaster->property_name ?? '-' }}</strong></td>
                    <td>{{ $lp->propertyMaster?->seller_name ?? ($lp->propertyMaster?->seller?->name ?? '-') }}</td>
                    <td class="r" style="color:#B91C1C;">₹{{ number_format($lp->amount, 2) }}</td>
                    <td>{{ $lp->payment_mode ?: '-' }} {{ $lp->bank_name ? '('.$lp->bank_name.')' : '' }}</td>
                    <td>{{ $lp->reference_no ?: '-' }}</td>
                    <td>{{ $lp->remarks ?: 'Land Payment' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif
@endforeach

@if((isset($unallocatedExpenses) && $unallocatedExpenses->isNotEmpty()) || (isset($unallocatedContractorPayments) && $unallocatedContractorPayments->isNotEmpty()) || (isset($unallocatedBrokerCommissions) && $unallocatedBrokerCommissions->isNotEmpty()) || (isset($unallocatedLandPayments) && $unallocatedLandPayments->isNotEmpty()))
    @php
        $uExp = $unallocatedExpenses ?? collect();
        $uCp = $unallocatedContractorPayments ?? collect();
        $uBc = $unallocatedBrokerCommissions ?? collect();
        $uLp = $unallocatedLandPayments ?? collect();
    @endphp
    <div class="project-section">
        <div class="project-head" style="background:#334155;">
            <div>
                <div class="project-head-title">General &amp; Unallocated Expenses</div>
                <div class="project-head-sub">Unassigned expenses and payments across company operations</div>
            </div>
            <div class="project-head-cost">
                <div style="font-size:9px; text-transform:uppercase; color:#CBD5E1;">General Total</div>
                ₹{{ number_format($unallocatedTotal, 2) }}
            </div>
        </div>

        @if($uExp->isNotEmpty())
        <div class="table-title">General Site Expenses ({{ $uExp->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th style="width:20px;">#</th>
                    <th>Date</th>
                    <th>Expense Title / Details</th>
                    <th>Category</th>
                    <th>Paid To / Vendor</th>
                    <th class="r">Amount</th>
                    <th>Mode</th>
                    <th>Ref No</th>
                    <th class="c">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($uExp as $eIdx => $exp)
                <tr>
                    <td style="color:#64748B;">{{ $eIdx + 1 }}</td>
                    <td style="white-space:nowrap;">{{ $exp->expense_date ? \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') : '-' }}</td>
                    <td><strong>{{ $exp->expense_title ?? $exp->description }}</strong></td>
                    <td>{{ $exp->expense_category ?: ($exp->expenseCategory->name ?? '-') }}</td>
                    <td>{{ $exp->paid_to ?: ($exp->vendor->name ?? '-') }}</td>
                    <td class="r" style="color:#B91C1C;">₹{{ number_format($exp->amount, 2) }}</td>
                    <td>{{ $exp->payment_mode ?: '-' }}</td>
                    <td>{{ $exp->reference_no ?: '-' }}</td>
                    <td class="c">
                        @php $st = strtolower($exp->approval_status ?? 'pending'); @endphp
                        <span class="badge badge-{{ $st }}">{{ ucfirst($exp->approval_status ?? 'Pending') }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if($uCp->isNotEmpty())
        <div class="table-title" style="background:rgba(244,63,94,0.06); color:#9F1239; border-top:1px solid #E2E8F0;">
            General Contractor Payments ({{ $uCp->count() }})
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:20px;">#</th>
                    <th>Date</th>
                    <th>Contractor Name</th>
                    <th>Work Type</th>
                    <th class="r">Amount Paid</th>
                    <th>Payment Mode</th>
                    <th>Ref / Bill No</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($uCp as $cIdx => $cp)
                <tr>
                    <td style="color:#64748B;">{{ $cIdx + 1 }}</td>
                    <td style="white-space:nowrap;">{{ $cp->payment_date ? \Carbon\Carbon::parse($cp->payment_date)->format('d M Y') : '-' }}</td>
                    <td><strong>{{ $cp->contractor->contractor_name ?? '-' }}</strong></td>
                    <td>{{ $cp->contractor->work_type ?? 'Contractor Work' }}</td>
                    <td class="r" style="color:#B91C1C;">₹{{ number_format($cp->amount, 2) }}</td>
                    <td>{{ $cp->payment_mode ?: '-' }}</td>
                    <td>{{ $cp->reference_no ?: ($cp->bill_no ?: '-') }}</td>
                    <td>{{ $cp->remarks ?: ($cp->payment_type ?: 'Paid') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if($uBc->isNotEmpty())
        <div class="table-title" style="background:rgba(168,85,247,0.06); color:#6B21A8; border-top:1px solid #E2E8F0;">
            General Broker Commissions ({{ $uBc->count() }})
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:20px;">#</th>
                    <th>Date</th>
                    <th>Broker Name</th>
                    <th class="r">Amount</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($uBc as $bIdx => $bc)
                <tr>
                    <td style="color:#64748B;">{{ $bIdx + 1 }}</td>
                    <td style="white-space:nowrap;">{{ $bc->payment_date ? \Carbon\Carbon::parse($bc->payment_date)->format('d M Y') : '-' }}</td>
                    <td><strong>{{ $bc->broker->name ?? '-' }}</strong></td>
                    <td class="r" style="color:#B91C1C;">₹{{ number_format($bc->commission_amount, 2) }}</td>
                    <td>{{ ucfirst($bc->payment_status ?: 'Paid') }}</td>
                    <td>{{ $bc->remarks ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if($uLp->isNotEmpty())
        <div class="table-title" style="background:rgba(20,184,166,0.06); color:#115E59; border-top:1px solid #E2E8F0;">
            General Land Payments ({{ $uLp->count() }})
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:20px;">#</th>
                    <th>Date</th>
                    <th>Land / Property Master</th>
                    <th class="r">Amount</th>
                    <th>Mode</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($uLp as $lIdx => $lp)
                <tr>
                    <td style="color:#64748B;">{{ $lIdx + 1 }}</td>
                    <td style="white-space:nowrap;">{{ $lp->payment_date ? \Carbon\Carbon::parse($lp->payment_date)->format('d M Y') : '-' }}</td>
                    <td><strong>{{ $lp->propertyMaster->property_name ?? '-' }}</strong></td>
                    <td class="r" style="color:#B91C1C;">₹{{ number_format($lp->amount, 2) }}</td>
                    <td>{{ $lp->payment_mode ?: '-' }}</td>
                    <td>{{ $lp->remarks ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
@endif

<div class="rpt-footer">
    <span>Delawala ERP &bull; Financial Management System</span>
    <span>Page generated automatically from Expense &amp; Outgoing Ledgers</span>
</div>

</body>
</html>

