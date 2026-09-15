<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contractor Profile - {{ $contractor->contractor_name }}</title>
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

        /* ── Banner ── */
        .banner { background:#0F172A; color:#fff; border-radius:8px; padding:16px 20px; margin-bottom:22px; display:flex; justify-content:space-between; align-items:center; }
        .banner h2 { font-size:17px; font-weight:700; color:#F8FAFC; margin-bottom:4px; }
        .badge-pill { background:rgba(37,99,235,0.3); color:#60A5FA; font-size:11px; font-weight:700; padding:4px 12px; border-radius:4px; border:1px solid rgba(59,130,246,0.4); display:inline-block; }

        /* ── 2 Column Grid ── */
        .grid-2 { display:flex; gap:18px; margin-bottom:20px; }
        .grid-col { flex:1; border:1px solid #E2E8F0; border-radius:8px; padding:14px; background:#F8FAFC; }
        .col-heading { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#fc6900ff; margin-bottom:10px; border-bottom:1px solid #E2E8F0; padding-bottom:4px; }

        .info-row { display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px dashed #E2E8F0; font-size:11px; }
        .info-row:last-child { border-bottom:none; }
        .info-label { color:#64748B; font-weight:500; }
        .info-value { color:#0F1F35; font-weight:700; text-align:right; }

        /* ── Signature Box ── */
        .sig-row { display:flex; justify-content:space-between; margin-top:40px; padding-top:20px; }
        .sig-box { width:200px; text-align:center; border-top:1px solid #0F172A; padding-top:6px; font-size:10px; font-weight:700; color:#475569; }

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
        <div class="rpt-title">Contractor Dossier &amp; KYC Profile</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

<div class="banner">
    <div>
        <h2>{{ $contractor->contractor_name }}</h2>
        <div style="font-size:11px; color:#94A3B8;">
            Project: {{ $contractor->project->project_name ?? 'General Contractor' }} &nbsp;|&nbsp; Firm: {{ $contractor->firm->firm_name ?? 'Delawala Management' }}
        </div>
    </div>
    <div>
        <span class="badge-pill">STATUS: {{ strtoupper($contractor->status ?: 'ACTIVE') }}</span>
    </div>
</div>

<div class="grid-2">
    <!-- Contact & KYC Info -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Contractor &amp; Identity Details</div>
        <div class="info-row">
            <span class="info-label">Full Name:</span>
            <span class="info-value">{{ $contractor->contractor_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mobile Number:</span>
            <span class="info-value">{{ $contractor->mobile ?: '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Aadhar Card No:</span>
            <span class="info-value">{{ $contractor->aadhar_no ?: '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">PAN Card No:</span>
            <span class="info-value">{{ $contractor->pan_no ?: '—' }}</span>
        </div>
        @php
            $assignedProjs = $contractor->relationLoaded('projects') && $contractor->projects->isNotEmpty()
                ? $contractor->projects
                : ($contractor->project ? collect([$contractor->project]) : collect());
        @endphp
        <div class="info-row">
            <span class="info-label">Assigned Project(s):</span>
            <span class="info-value">{{ $assignedProjs->pluck('project_name')->implode(', ') ?: '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Specific Plot(s)/Unit(s):</span>
            <span class="info-value">
                {{ $contractor->properties && $contractor->properties->isNotEmpty() ? $contractor->properties->pluck('property_name')->implode(', ') : 'All Units (Full Project)' }}
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Property Master(s):</span>
            <span class="info-value">
                {{ $assignedProjs->pluck('propertyMaster.property_name')->filter()->unique()->implode(', ') ?: '—' }}
            </span>
        </div>
    </div>

    <!-- Banking & Address Details -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Banking &amp; Residential Info</div>
        <div class="info-row">
            <span class="info-label">Bank Name:</span>
            <span class="info-value">{{ $contractor->bank_name ?: '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Account Number:</span>
            <span class="info-value">{{ $contractor->account_number ?: '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">IFSC Code:</span>
            <span class="info-value">{{ $contractor->ifsc_code ?: '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Branch Name:</span>
            <span class="info-value">{{ $contractor->branch_name ?: '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Address:</span>
            <span class="info-value">{{ $contractor->address ?: '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Registered On:</span>
            <span class="info-value">{{ $contractor->created_at->format('d M Y, h:i A') }}</span>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Work & Contract Financials -->
    <div class="grid-col" style="border-left: 3px solid #3B82F6;">
        <div class="col-heading">&#9632; Work &amp; Financial Summary</div>
        <div class="info-row">
            <span class="info-label">Work Type / Trade:</span>
            <span class="info-value">{{ $contractor->work_type ?: 'General Construction' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Contract Date:</span>
            <span class="info-value">{{ $contractor->contract_date ? \Carbon\Carbon::parse($contractor->contract_date)->format('d M Y') : '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Contract Amount:</span>
            <span class="info-value" style="color: #2563EB; font-size: 12px;">₹{{ number_format($contractor->contract_amount ?? 0, 2) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Paid Amount:</span>
            <span class="info-value" style="color: #059669; font-size: 12px;">₹{{ number_format($contractor->paid_amount ?? 0, 2) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Balance Due:</span>
            <span class="info-value" style="color: #DC2626; font-size: 12px;">₹{{ number_format($contractor->due_amount ?? 0, 2) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Status:</span>
            <span class="info-value">{{ strtoupper($contractor->payment_status ?? 'UNPAID') }}</span>
        </div>
    </div>

    <!-- Contract Scope / Notes -->
    <div class="grid-col">
        <div class="col-heading">&#9632; Scope of Work &amp; Agreement Terms</div>
        <div style="font-size: 11px; color: #334155; line-height: 1.5; white-space: pre-line;">
            {{ $contractor->contract_notes ?: 'No special notes or terms specified.' }}
        </div>
    </div>
</div>

@if($contractor->payments && $contractor->payments->isNotEmpty())
<div style="margin-bottom: 20px;">
    <div class="col-heading" style="margin-bottom: 8px;">&#9632; Payment Installments Statement ({{ $contractor->payments->count() }} Payments)</div>
    <table style="width: 100%; border-collapse: collapse; font-size: 10.5px; border: 1px solid #E2E8F0;">
        <thead>
            <tr style="background: #F1F5F9;">
                <th style="padding: 6px 8px; border: 1px solid #CBD5E1; text-align: left;">#</th>
                <th style="padding: 6px 8px; border: 1px solid #CBD5E1; text-align: left;">Date</th>
                <th style="padding: 6px 8px; border: 1px solid #CBD5E1; text-align: left;">Type</th>
                <th style="padding: 6px 8px; border: 1px solid #CBD5E1; text-align: right;">Amount (₹)</th>
                <th style="padding: 6px 8px; border: 1px solid #CBD5E1; text-align: left;">Mode</th>
                <th style="padding: 6px 8px; border: 1px solid #CBD5E1; text-align: left;">Reference / Cheque</th>
                <th style="padding: 6px 8px; border: 1px solid #CBD5E1; text-align: left;">Bill No</th>
                <th style="padding: 6px 8px; border: 1px solid #CBD5E1; text-align: left;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contractor->payments as $k => $p)
            <tr>
                <td style="padding: 5px 8px; border: 1px solid #E2E8F0;">{{ $k + 1 }}</td>
                <td style="padding: 5px 8px; border: 1px solid #E2E8F0; font-weight: 700;">{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d M Y') : '—' }}</td>
                <td style="padding: 5px 8px; border: 1px solid #E2E8F0;">{{ $p->payment_type ?: 'Part Payment' }}</td>
                <td style="padding: 5px 8px; border: 1px solid #E2E8F0; text-align: right; font-weight: 700; color: #059669;">₹{{ number_format($p->amount, 2) }}</td>
                <td style="padding: 5px 8px; border: 1px solid #E2E8F0;">{{ $p->payment_mode }}</td>
                <td style="padding: 5px 8px; border: 1px solid #E2E8F0;">{{ $p->reference_no ?: '—' }}</td>
                <td style="padding: 5px 8px; border: 1px solid #E2E8F0;">{{ $p->bill_no ?: '—' }}</td>
                <td style="padding: 5px 8px; border: 1px solid #E2E8F0; color: #64748B;">{{ $p->remarks ?: '—' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #F8FAFC; font-weight: 700;">
                <td colspan="3" style="padding: 6px 8px; border: 1px solid #CBD5E1; text-align: right;">Total Paid to Date:</td>
                <td style="padding: 6px 8px; border: 1px solid #CBD5E1; text-align: right; color: #059669;">₹{{ number_format($contractor->paid_amount ?? 0, 2) }}</td>
                <td colspan="4" style="padding: 6px 8px; border: 1px solid #CBD5E1; color: #DC2626;">Due Balance: ₹{{ number_format($contractor->due_amount ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endif

<div class="sig-row">
    <div class="sig-box">Contractor Signature</div>
    <div class="sig-box">Project Head / Authorized Signatory</div>
</div>

<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; Contractor Profile Dossier</span>
    <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
