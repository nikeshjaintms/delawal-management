<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Voucher #EXP-{{ str_pad($expense->id, 5, '0', STR_PAD_LEFT) }} - Delawala Management</title>
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Segoe UI',Arial,sans-serif; font-size:11.5px; color:#0F1F35; background:#fff; padding:28px; line-height:1.45; }

        /* ── Header ── */
        .rpt-header { display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:16px; margin-bottom:20px; border-bottom:2.5px solid #fc6900ff; }
        .co-name  { font-size:24px; font-weight:800; color:#0F1F35; letter-spacing:0.4px; }
        .co-sub   { font-size:10px; color:#fc6900ff; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-top:3px; }
        .rpt-meta { text-align:right; }
        .rpt-meta .rpt-title { font-size:17px; font-weight:800; color:#0F1F35; margin-bottom:4px; }
        .rpt-meta .rpt-date  { font-size:11px; color:#64748B; }

        /* ── Voucher Banner ── */
        .banner { background:#0F172A; color:#fff; border-radius:8px; padding:16px 20px; margin-bottom:22px; display:flex; justify-content:space-between; align-items:center; }
        .banner h2 { font-size:17px; font-weight:700; color:#F8FAFC; margin-bottom:4px; }
        .voucher-badge { background:rgba(252,105,0,0.25); color:#FF8A3D; font-size:11px; font-weight:700; padding:4px 12px; border-radius:4px; border:1px solid rgba(252,105,0,0.4); display:inline-block; }

        /* ── 2 Column Grid ── */
        .grid-2 { display:flex; gap:18px; margin-bottom:20px; }
        .grid-col { flex:1; border:1px solid #E2E8F0; border-radius:8px; padding:14px; background:#F8FAFC; }
        .col-heading { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#fc6900ff; margin-bottom:10px; border-bottom:1px solid #E2E8F0; padding-bottom:4px; }

        .info-row { display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px dashed #E2E8F0; font-size:11px; }
        .info-row:last-child { border-bottom:none; }
        .info-label { color:#64748B; font-weight:500; }
        .info-value { color:#0F1F35; font-weight:700; text-align:right; }

        /* ── Voucher Amount Box ── */
        .amount-card { border:1.5px solid #CBD5E1; border-radius:8px; overflow:hidden; margin-bottom:22px; }
        .amount-header { background:#1E293B; color:#fff; padding:10px 16px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; }
        .amount-body { padding:18px 20px; background:#fff; display:flex; justify-content:space-between; align-items:center; }
        .amount-title { font-size:13px; color:#64748B; font-weight:600; }
        .amount-value { font-size:24px; font-weight:800; color:#e05c00; }

        /* ── Remarks Box ── */
        .terms-box { background:#F8FAFC; border:1px solid #E2E8F0; border-radius:6px; padding:12px 16px; margin-bottom:24px; font-size:10.5px; color:#475569; }
        .terms-box strong { color:#0F1F35; }

        /* ── Signatures ── */
        .auth-block { margin-top:40px; padding-top:14px; display:flex; justify-content:space-between; page-break-inside:avoid; }
        .auth-col { width:200px; text-align:center; }
        .auth-line { border-top:1.5px solid #0F1F35; margin-top:46px; padding-top:5px; font-size:10px; font-weight:700; color:#0F1F35; }

        /* ── Footer ── */
        .rpt-footer { margin-top:26px; padding-top:10px; border-top:1px solid #E5E7EB; display:flex; justify-content:space-between; color:#9CA3AF; font-size:9.5px; }

        @media print { body { padding:14px; } @page { margin:10mm; } }
    </style>
</head>
<body>

<div class="rpt-header">
    <div>
        <div class="co-name">Delawala</div>
        <div class="co-sub">Properties &amp; Management</div>
    </div>
    <div class="rpt-meta">
        <div class="rpt-title">Official Expense / Payment Voucher</div>
        <div class="rpt-date">Date: {{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') : now()->format('d M Y') }}</div>
    </div>
</div>

<div class="banner">
    <div>
        <h2>Voucher #EXP-{{ str_pad($expense->id, 5, '0', STR_PAD_LEFT) }}</h2>
        <div style="font-size:11px; color:#94A3B8;">
            Expense: {{ $expense->expense_title }}
        </div>
    </div>
    <div>
        <span class="voucher-badge">Status: {{ strtoupper($expense->approval_status ?: 'Pending') }}</span>
    </div>
</div>

<div class="amount-card">
    <div class="amount-header">Payment Voucher Consideration</div>
    <div class="amount-body">
        <div class="amount-title">Total Disbursed / Paid Amount:</div>
        <div class="amount-value">₹{{ number_format($expense->amount, 2) }}</div>
    </div>
</div>

<div class="grid-2">
    <!-- Payment & Category Details -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Expense &amp; Classification</div>
        <div class="info-row">
            <span class="info-label">Title:</span>
            <span class="info-value">{{ $expense->expense_title }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Category:</span>
            <span class="info-value">{{ $expense->expenseCategory->name ?? ($expense->expense_category ?: 'General') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Expense Date:</span>
            <span class="info-value">{{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') : '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Mode:</span>
            <span class="info-value">{{ $expense->payment_mode ?: '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Bill / Invoice No:</span>
            <span class="info-value">{{ $expense->bill_no ?: '-' }}</span>
        </div>
    </div>

    <!-- Entity & Recipient Details -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Payee &amp; Cost Center</div>
        <div class="info-row">
            <span class="info-label">Paid To / Beneficiary:</span>
            <span class="info-value">{{ $expense->paid_to ?: '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Associated Firm(s):</span>
            <span class="info-value">
                @php $fNames = $expense->firms->isNotEmpty() ? $expense->firms->pluck('firm_name')->implode(', ') : ($expense->firm->firm_name ?? '-'); @endphp
                {{ $fNames }}
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Project:</span>
            <span class="info-value">{{ $expense->project->project_name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Property / Unit:</span>
            <span class="info-value">{{ $expense->property->property_name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Created At:</span>
            <span class="info-value">{{ $expense->created_at->format('d M Y, h:i A') }}</span>
        </div>
    </div>
</div>

@if($expense->remarks)
<div class="terms-box">
    <strong>Remarks / Audit Notes:</strong><br>
    {{ $expense->remarks }}
</div>
@endif

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Prepared By (Accounts)</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Verified / Passed By</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Authorized Signatory / Payee</div>
    </div>
</div>

<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; Expense Voucher #EXP-{{ str_pad($expense->id, 5, '0', STR_PAD_LEFT) }}</span>
    <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
