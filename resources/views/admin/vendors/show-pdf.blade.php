<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Statement - {{ $vendor->name }}</title>
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Segoe UI', Arial, sans-serif; font-size:11px; color:#0F172A; background:#fff; padding:24px; line-height:1.4; }

        /* ── Header ── */
        .rpt-header { display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:14px; margin-bottom:16px; border-bottom:2.5px solid #2563EB; }
        .co-name  { font-size:22px; font-weight:800; color:#0F172A; }
        .co-sub   { font-size:9.5px; color:#2563EB; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; margin-top:2px; }
        .rpt-meta { text-align:right; }
        .rpt-meta .rpt-title { font-size:16px; font-weight:800; color:#0F172A; margin-bottom:3px; }
        .rpt-meta .rpt-date  { font-size:10px; color:#64748B; }

        /* ── Banner ── */
        .banner { background:#0F172A; color:#fff; border-radius:6px; padding:12px 16px; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; }
        .banner h2 { font-size:16px; font-weight:700; color:#F8FAFC; margin-bottom:3px; }
        .badge-pill { background:rgba(37,99,235,0.3); color:#60A5FA; font-size:10.5px; font-weight:700; padding:3px 10px; border-radius:4px; border:1px solid rgba(59,130,246,0.4); display:inline-block; }

        /* ── KPI Summary Cards ── */
        .kpi-row { display:flex; gap:12px; margin-bottom:18px; }
        .kpi-box { flex:1; border:1px solid #E2E8F0; border-radius:6px; padding:10px 12px; background:#F8FAFC; text-align:center; }
        .kpi-box-title { font-size:9px; text-transform:uppercase; font-weight:700; color:#64748B; letter-spacing:0.5px; margin-bottom:4px; }
        .kpi-box-val { font-size:15px; font-weight:800; color:#0F172A; }

        /* ── 2 Column Grid ── */
        .grid-2 { display:flex; gap:14px; margin-bottom:18px; }
        .grid-col { flex:1; border:1px solid #E2E8F0; border-radius:6px; padding:12px; background:#F8FAFC; }
        .col-heading { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#2563EB; margin-bottom:8px; border-bottom:1px solid #E2E8F0; padding-bottom:4px; }

        .info-row { display:flex; justify-content:space-between; padding:4px 0; border-bottom:1px dashed #E2E8F0; font-size:10.5px; }
        .info-row:last-child { border-bottom:none; }
        .info-label { color:#64748B; font-weight:500; }
        .info-value { color:#0F172A; font-weight:700; text-align:right; }

        /* ── Tables ── */
        .section-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#1E293B; margin-bottom:6px; margin-top:16px; padding-bottom:4px; border-bottom:1.5px solid #CBD5E1; }
        table { width:100%; border-collapse:collapse; font-size:10px; margin-bottom:16px; }
        thead tr { background:#0F172A; }
        thead th { padding:6px 8px; color:#FFF; font-weight:600; text-align:left; font-size:9px; text-transform:uppercase; letter-spacing:0.5px; }
        thead th.r { text-align:right; }
        thead th.c { text-align:center; }
        tbody tr:nth-child(even) { background:#F8FAFC; }
        tbody td { padding:5px 8px; border-bottom:1px solid #F1F5F9; vertical-align:middle; }
        tbody td.r { text-align:right; font-weight:700; color:#0F172A; }
        tbody td.c { text-align:center; }

        .badge-status { padding:2px 6px; border-radius:3px; font-size:8.5px; font-weight:700; text-transform:uppercase; }
        .badge-approved { background:#DCFCE7; color:#15803D; }
        .badge-pending  { background:#FEF3C7; color:#B45309; }
        .badge-rejected { background:#FEE2E2; color:#B91C1C; }

        /* ── Footer ── */
        .rpt-footer { margin-top:20px; padding-top:8px; border-top:1px solid #E5E7EB; display:flex; justify-content:space-between; color:#94A3B8; font-size:9px; }

        @media print { body { padding:10px; } @page { margin:8mm; } }
    </style>
</head>
<body>

<div class="rpt-header">
    <div>
        <div class="co-name">{{ $vendor->firm->firm_name ?? 'Delawala Management' }}</div>
        <div class="co-sub">Properties &amp; Management • Vendor Account Statement</div>
    </div>
    <div class="rpt-meta">
        <div class="rpt-title">Vendor Statement &amp; Ledger</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

<div class="banner">
    <div>
        <h2>{{ $vendor->name }}</h2>
        <div style="font-size:10px; color:#94A3B8;">
            Phone: {{ $vendor->mobile ?: '-' }} &nbsp;|&nbsp; City: {{ $vendor->city ?: '-' }} &nbsp;|&nbsp; GST: {{ $vendor->gst_no ?: 'Unregistered' }}
        </div>
    </div>
    <div>
        <span class="badge-pill">STATUS: {{ strtoupper($vendor->status ?: 'ACTIVE') }}</span>
    </div>
</div>

{{-- Financial Summary KPI Cards --}}
<div class="kpi-row">
    <div class="kpi-box">
        <div class="kpi-box-title">Total Purchases / Bills</div>
        <div class="kpi-box-val" style="color:#2563EB;">₹{{ number_format($totalExpenseAmount, 2) }}</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-box-title">Paid / Approved</div>
        <div class="kpi-box-val" style="color:#16A34A;">₹{{ number_format($approvedExpenseAmount, 2) }}</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-box-title">Pending / Due</div>
        <div class="kpi-box-val" style="color:#D97706;">₹{{ number_format($pendingExpenseAmount, 2) }}</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-box-title">Purchase Orders (PO)</div>
        <div class="kpi-box-val" style="color:#7C3AED;">₹{{ number_format($totalPOAmount, 2) }}</div>
    </div>
</div>

<div class="grid-2">
    <!-- Contact Info -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Vendor Details</div>
        <div class="info-row">
            <span class="info-label">Vendor Name:</span>
            <span class="info-value">{{ $vendor->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mobile Number:</span>
            <span class="info-value">{{ $vendor->mobile ?: '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email Address:</span>
            <span class="info-value">{{ $vendor->email ?: '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">GSTIN / Tax No:</span>
            <span class="info-value">{{ $vendor->gst_no ?: 'Unregistered' }}</span>
        </div>
    </div>

    <!-- Address & Commercials -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Commercial &amp; Project Info</div>
        <div class="info-row">
            <span class="info-label">Assigned Project:</span>
            <span class="info-value">{{ $vendor->project->project_name ?? 'All Projects (General)' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">City / Address:</span>
            <span class="info-value">{{ $vendor->city ?: ($vendor->address ?: '-') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Terms:</span>
            <span class="info-value">{{ $vendor->payment_terms ?: 'Standard' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Account Created:</span>
            <span class="info-value">{{ $vendor->created_at->format('d M Y') }}</span>
        </div>
    </div>
</div>

{{-- ── Purchases & Bills Table ── --}}
<div class="section-label">&#9632; Item Purchases &amp; Bills Ledger</div>
@if($expenses && $expenses->isNotEmpty())
<table>
    <thead>
        <tr>
            <th style="width:20px;">#</th>
            <th>Date</th>
            <th>Bill / Inv #</th>
            <th>Items / Description</th>
            <th>Project / Property</th>
            <th>Payment Mode</th>
            <th class="c">Status</th>
            <th class="r">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses as $i => $exp)
        <tr>
            <td style="color:#94A3B8;">{{ $i+1 }}</td>
            <td><strong>{{ $exp->expense_date ? $exp->expense_date->format('d M Y') : $exp->created_at->format('d M Y') }}</strong></td>
            <td>{{ $exp->bill_no ?: ($exp->invoice_no ?: '—') }}</td>
            <td>
                <strong>{{ $exp->expense_title ?: 'Material / Service' }}</strong>
                @if($exp->description)
                    <div style="font-size:9px; color:#64748B;">{{ Str::limit($exp->description, 50) }}</div>
                @endif
            </td>
            <td>{{ $exp->project->project_name ?? ($exp->property->property_name ?? 'General') }}</td>
            <td>{{ $exp->payment_mode ?: 'Cash' }}</td>
            <td class="c">
                @if($exp->approval_status === 'Approved')
                    <span class="badge-status badge-approved">Paid</span>
                @elseif($exp->approval_status === 'Rejected')
                    <span class="badge-status badge-rejected">Rejected</span>
                @else
                    <span class="badge-status badge-pending">Pending</span>
                @endif
            </td>
            <td class="r">₹{{ number_format($exp->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#F1F5F9; font-weight:bold;">
            <td colspan="7" style="text-align:right; font-size:10px; padding:6px 8px;">TOTAL BILLS AMOUNT:</td>
            <td class="r" style="color:#2563EB; font-size:11px; padding:6px 8px;">₹{{ number_format($totalExpenseAmount, 2) }}</td>
        </tr>
    </tfoot>
</table>
@else
<p style="font-size:10.5px; color:#64748B; font-style:italic; margin-bottom:14px;">No bill or expense records found for this vendor.</p>
@endif

{{-- ── Purchase Orders Table ── --}}
@if($purchaseOrders && $purchaseOrders->isNotEmpty())
<div class="section-label">&#9632; Purchase Orders &amp; Material Breakdown</div>
<table>
    <thead>
        <tr>
            <th style="width:20px;">#</th>
            <th>PO Number</th>
            <th>PO Date</th>
            <th>Project</th>
            <th>Material Items &amp; Qty Ordered</th>
            <th class="c">Status</th>
            <th class="r">Grand Total (₹)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($purchaseOrders as $pIdx => $po)
        <tr>
            <td style="color:#94A3B8;">{{ $pIdx+1 }}</td>
            <td><strong>{{ $po->po_number }}</strong></td>
            <td>{{ $po->po_date ? $po->po_date->format('d M Y') : $po->created_at->format('d M Y') }}</td>
            <td>{{ $po->project->project_name ?? '—' }}</td>
            <td>
                @if($po->items && $po->items->isNotEmpty())
                    @foreach($po->items as $itm)
                        <div>• {{ $itm->material ? $itm->material->material_name : ($itm->item_name ?? 'Item') }} ({{ $itm->quantity }} {{ $itm->material->unit ?? '' }}) @ ₹{{ number_format($itm->unit_price ?: $itm->rate, 2) }}</div>
                    @endforeach
                @else
                    —
                @endif
            </td>
            <td class="c">{{ ucfirst($po->status) }}</td>
            <td class="r">₹{{ number_format($po->grand_total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#F1F5F9; font-weight:bold;">
            <td colspan="6" style="text-align:right; font-size:10px; padding:6px 8px;">TOTAL PURCHASE ORDERS AMOUNT:</td>
            <td class="r" style="color:#7C3AED; font-size:11px; padding:6px 8px;">₹{{ number_format($totalPOAmount, 2) }}</td>
        </tr>
    </tfoot>
</table>
@endif

<div class="rpt-footer">
    <span>{{ $vendor->firm->firm_name ?? 'Delawala Management System' }} &nbsp;—&nbsp; Official Vendor Ledger Statement</span>
    <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
