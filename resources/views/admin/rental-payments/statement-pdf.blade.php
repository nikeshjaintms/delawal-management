<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Collection Statement - {{ $rental->tenant_name ?? 'Tenant' }} - Delawala Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11.5px; color: #0F172A; background: #F1F5F9; padding: 24px; line-height: 1.45; }

        .sheet {
            max-width: 900px;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #E2E8F0;
        }

        /* ── Action Toolbar (Hidden in print) ── */
        .no-print-bar {
            max-width: 900px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
        }
        .btn-print { background: #2563EB; color: #FFFFFF; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); }
        .btn-print:hover { background: #1D4ED8; }
        .btn-back  { background: #FFFFFF; color: #475569; border: 1px solid #CBD5E1; }
        .btn-back:hover { background: #F8FAFC; color: #0F172A; }

        /* ── Header ── */
        .rpt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 18px;
            margin-bottom: 20px;
            border-bottom: 2.5px solid #2563EB;
        }
        .co-name { font-size: 22px; font-weight: 900; color: #0F172A; letter-spacing: 0.3px; }
        .co-sub { font-size: 10px; color: #2563EB; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 3px; }
        .co-firm { font-size: 11px; color: #64748B; margin-top: 4px; font-weight: 600; }
        .rpt-meta { text-align: right; }
        .rpt-title { font-size: 16px; font-weight: 900; color: #0F172A; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 0.5px; }
        .rpt-doc-no { font-size: 11px; color: #2563EB; font-weight: 800; }
        .rpt-date { font-size: 10.5px; color: #64748B; margin-top: 3px; }

        /* ── Summary Card ── */
        .summary-banner {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            color: #FFFFFF;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .banner-left h2 { font-size: 16px; font-weight: 800; color: #F8FAFC; margin-bottom: 4px; }
        .banner-left p { font-size: 11.5px; color: #94A3B8; }
        .badge-status {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .badge-paid { background: rgba(16, 185, 129, 0.2); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.4); }
        .badge-partial { background: rgba(245, 158, 11, 0.2); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.4); }
        .badge-pending { background: rgba(239, 68, 68, 0.2); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.4); }

        /* ── 2-Col Info Grids ── */
        .grid-2 { display: flex; gap: 16px; margin-bottom: 20px; }
        .grid-col { flex: 1; border: 1px solid #E2E8F0; border-radius: 8px; padding: 14px; background: #F8FAFC; }
        .col-heading {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #2563EB;
            margin-bottom: 10px;
            border-bottom: 1px solid #E2E8F0;
            padding-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .info-row { display: flex; justify-content: space-between; padding: 4.5px 0; border-bottom: 1px dashed #E2E8F0; font-size: 11px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #64748B; font-weight: 600; }
        .info-val { color: #0F172A; font-weight: 700; text-align: right; }

        /* ── KPI Strip ── */
        .kpi-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 22px;
        }
        .kpi-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 10px 14px;
            text-align: center;
        }
        .kpi-card.highlight {
            background: rgba(37, 99, 235, 0.05);
            border-color: #93C5FD;
        }
        .kpi-card.paid {
            background: rgba(16, 185, 129, 0.06);
            border-color: #6EE7B7;
        }
        .kpi-card.pending {
            background: rgba(239, 68, 68, 0.06);
            border-color: #FCA5A5;
        }
        .kpi-title { font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 4px; }
        .kpi-amount { font-size: 14.5px; font-weight: 900; }

        /* ── Table ── */
        .table-title {
            font-size: 12px;
            font-weight: 800;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .statement-table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 22px; }
        .statement-table th {
            background: #1E293B;
            color: #F8FAFC;
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 8px 10px;
            border: 1px solid #334155;
            text-align: left;
        }
        .statement-table th.tar, .statement-table td.tar { text-align: right; }
        .statement-table th.tac, .statement-table td.tac { text-align: center; }
        .statement-table td {
            padding: 8px 10px;
            border: 1px solid #E2E8F0;
            color: #1E293B;
            vertical-align: middle;
        }
        .statement-table tbody tr:nth-child(even) { background: #F8FAFC; }
        .statement-table tfoot td {
            background: #F1F5F9;
            font-weight: 800;
            font-size: 11.5px;
            border-top: 2px solid #CBD5E1;
            padding: 9px 10px;
        }

        .pill-status {
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            display: inline-block;
        }
        .pill-paid { background: #DCFCE7; color: #15803D; }
        .pill-partial { background: #FEF3C7; color: #B45309; }
        .pill-pending { background: #FEE2E2; color: #B91C1C; }

        /* ── Signatures ── */
        .auth-block {
            margin-top: 36px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .auth-col { width: 220px; text-align: center; }
        .auth-line {
            border-top: 1.5px solid #0F172A;
            margin-top: 48px;
            padding-top: 6px;
            font-size: 10.5px;
            font-weight: 800;
            color: #0F172A;
        }

        /* ── Footer ── */
        .rpt-footer {
            margin-top: 26px;
            padding-top: 12px;
            border-top: 1px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            color: #94A3B8;
            font-size: 9.5px;
        }

        @media print {
            body { background: #FFFFFF; padding: 0; }
            .no-print-bar { display: none !important; }
            .sheet { box-shadow: none; border: none; padding: 10px 0; }
            @page { margin: 12mm 10mm; size: portrait; }
        }
    </style>
</head>
<body>

<div class="no-print-bar">
    <a href="{{ route('rental-payments.index', $rental->id) }}" class="btn-action btn-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Collections
    </a>
    <button onclick="window.print()" class="btn-action btn-print">
        <i class="fa-solid fa-print"></i> Print Statement
    </button>
</div>

<div class="sheet">
    <!-- Header -->
    <div class="rpt-header">
        <div>
            <div class="co-name">Delawala</div>
            <div class="co-sub">Properties &amp; Management</div>
            <div class="co-firm"><i class="fa-solid fa-building"></i> {{ $rental->firm->firm_name ?? 'Delawala Group' }}</div>
        </div>
        <div class="rpt-meta">
            <div class="rpt-title">Tenant Collection Statement</div>
            <div class="rpt-doc-no">REF: STMT-RENT-{{ str_pad($rental->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
        </div>
    </div>

    <!-- Banner -->
    @php
        $allProps = $rental->all_properties;
        $firstProp = $allProps->first();
    @endphp
    <div class="summary-banner">
        <div class="banner-left">
            <h2>{{ $rental->tenant_name ?? ($rental->tenant->name ?? 'Tenant Statement') }}</h2>
            <p>
                Agreement #{{ $rental->agreement_no ?: ('RA-' . str_pad($rental->id, 5, '0', STR_PAD_LEFT)) }} &nbsp;|&nbsp;
                @if($allProps->count() > 1)
                    <strong>{{ $allProps->count() }} Units:</strong>
                    @foreach($allProps as $p)
                        {{ $p->property_name ?: ($p->unit_no ? 'Unit #' . $p->unit_no : 'Property #' . $p->id) }}@if(!$loop->last), @endif
                    @endforeach
                @else
                    Property: {{ $firstProp->property_name ?? ($rental->property->property_name ?? '—') }}
                    @if($firstProp && $firstProp->unit_no) (Unit #{{ $firstProp->unit_no }}) @endif
                @endif
            </p>
        </div>
        <div>
            @php
                $overallStatus = $totalPendingAmount <= 0 && $payments->isNotEmpty() ? 'paid' : ($totalPaidAmount > 0 ? 'partial' : 'pending');
            @endphp
            <span class="badge-status badge-{{ $overallStatus }}">
                {{ ucfirst($overallStatus) }}
            </span>
        </div>
    </div>

    <!-- 2 Column Details -->
    <div class="grid-2">
        <!-- Tenant Info -->
        <div class="grid-col">
            <div class="col-heading"><i class="fa-solid fa-user"></i> Tenant Details</div>
            <div class="info-row">
                <span class="info-label">Tenant Name:</span>
                <span class="info-val">{{ $rental->tenant_name ?? ($rental->tenant->name ?? '—') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Mobile Number:</span>
                <span class="info-val">{{ $rental->tenant_mobile ?? ($rental->tenant->mobile ?? '—') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-val">{{ $rental->tenant_email ?: ($rental->tenant->email ?? '—') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Agreement Period:</span>
                <span class="info-val">
                    {{ $rental->rent_start_date ? \Carbon\Carbon::parse($rental->rent_start_date)->format('d M Y') : '—' }}
                    @if($rental->rent_end_date)
                        to {{ \Carbon\Carbon::parse($rental->rent_end_date)->format('d M Y') }}
                    @endif
                </span>
            </div>
        </div>

        <!-- Property Info -->
        <div class="grid-col">
            <div class="col-heading"><i class="fa-solid fa-building"></i> Property &amp; Commercials</div>
            @if($allProps->count() > 1)
                <div class="info-row">
                    <span class="info-label">Rented Units ({{ $allProps->count() }}):</span>
                    <span class="info-val" style="color: #2563EB;">
                        @foreach($allProps as $p)
                            {{ $p->property_name ?: ($p->unit_no ? 'Unit #' . $p->unit_no : 'Property #' . $p->id) }}@if(!$loop->last), @endif
                        @endforeach
                    </span>
                </div>
            @else
                <div class="info-row">
                    <span class="info-label">Property:</span>
                    <span class="info-val">{{ $firstProp->property_name ?? ($rental->property->property_name ?? '—') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Unit / Code:</span>
                    <span class="info-val">{{ $firstProp->property_code ?? ($firstProp && $firstProp->unit_no ? 'Unit #'.$firstProp->unit_no : '—') }}</span>
                </div>
            @endif
            @if($firstProp && $firstProp->project)
            <div class="info-row">
                <span class="info-label">Project:</span>
                <span class="info-val">{{ $firstProp->project->project_name }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Base Monthly Rent:</span>
                <span class="info-val" style="color: #2563EB;">₹{{ number_format($rental->rent_amount, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Security Deposit:</span>
                <span class="info-val">{{ $rental->security_deposit ? '₹' . number_format($rental->security_deposit, 2) : '—' }}</span>
            </div>
        </div>
    </div>

    <!-- KPI Summary Strip -->
    <div class="kpi-strip">
        <div class="kpi-card highlight">
            <div class="kpi-title">Total Rent Billed</div>
            <div class="kpi-amount" style="color: #2563EB;">₹{{ number_format($totalRentCharged, 2) }}</div>
        </div>
        <div class="kpi-card highlight">
            <div class="kpi-title">Total Maintenance</div>
            <div class="kpi-amount" style="color: #D97706;">₹{{ number_format($totalMaintenanceCharged, 2) }}</div>
        </div>
        <div class="kpi-card paid">
            <div class="kpi-title">Total Paid / Collected</div>
            <div class="kpi-amount" style="color: #059669;">₹{{ number_format($totalPaidAmount, 2) }}</div>
        </div>
        <div class="kpi-card {{ $totalPendingAmount > 0 ? 'pending' : 'paid' }}">
            <div class="kpi-title">Total Pending Balance</div>
            <div class="kpi-amount" style="color: {{ $totalPendingAmount > 0 ? '#DC2626' : '#059669' }};">
                ₹{{ number_format($totalPendingAmount, 2) }}
            </div>
        </div>
    </div>

    <!-- Payment Ledger Table -->
    <div class="table-title"><i class="fa-solid fa-list-check"></i> Itemized Payment History / Collections</div>
    <table class="statement-table">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Month / Year</th>
                <th>Payment Date</th>
                <th class="tar">Rent (₹)</th>
                <th class="tar">Maint. (₹)</th>
                <th class="tar">Total Due (₹)</th>
                <th class="tar">Paid (₹)</th>
                <th class="tar">Pending (₹)</th>
                <th>Mode</th>
                <th class="tac">Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $idx => $p)
                @php
                    $maint = (float) ($p->maintenance_amount ?? 0);
                    $total = (float) ($p->total_amount ?: ($p->rent_amount + $maint));
                @endphp
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td><strong>{{ $p->payment_month }} {{ $p->payment_year }}</strong></td>
                    <td>{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d M Y') : '—' }}</td>
                    <td class="tar">₹{{ number_format($p->rent_amount, 2) }}</td>
                    <td class="tar" style="color: {{ $maint > 0 ? '#D97706' : '#94A3B8' }};">
                        {{ $maint > 0 ? '₹' . number_format($maint, 2) : '—' }}
                    </td>
                    <td class="tar" style="font-weight: 700; color: #0F172A;">₹{{ number_format($total, 2) }}</td>
                    <td class="tar" style="color: #059669; font-weight: 700;">₹{{ number_format($p->paid_amount, 2) }}</td>
                    <td class="tar" style="color: {{ $p->pending_amount > 0 ? '#DC2626' : '#059669' }}; font-weight: 700;">
                        ₹{{ number_format($p->pending_amount, 2) }}
                    </td>
                    <td>{{ $p->payment_mode ?: '—' }}</td>
                    <td class="tac"><span class="pill-status pill-{{ $p->payment_status }}">{{ ucfirst($p->payment_status) }}</span></td>
                    <td style="font-size: 10px; color: #64748B;">{{ $p->remarks ?: '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; color: #94A3B8; padding: 24px;">
                        No collection / payment records recorded yet.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($payments->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; text-transform: uppercase;">Total Collections Summary:</td>
                    <td class="tar">₹{{ number_format($totalRentCharged, 2) }}</td>
                    <td class="tar">₹{{ number_format($totalMaintenanceCharged, 2) }}</td>
                    <td class="tar" style="color: #0F172A;">₹{{ number_format($totalDueAmount, 2) }}</td>
                    <td class="tar" style="color: #059669;">₹{{ number_format($totalPaidAmount, 2) }}</td>
                    <td class="tar" style="color: {{ $totalPendingAmount > 0 ? '#DC2626' : '#059669' }};">₹{{ number_format($totalPendingAmount, 2) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Signature Block -->
    <div class="auth-block">
        <div class="auth-col">
            <div class="auth-line">Tenant Signature / Acknowledged</div>
        </div>
        <div class="auth-col">
            <div class="auth-line">Authorized Signatory &amp; Stamp</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="rpt-footer">
        <span>This is a computer-generated rent collection statement issued by Delawala Management System.</span>
        <span>Page 1 of 1</span>
    </div>
</div>

</body>
</html>
