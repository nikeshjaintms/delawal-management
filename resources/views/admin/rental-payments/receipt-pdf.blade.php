<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Receipt #RCP-{{ str_pad($rentalPayment->id, 5, '0', STR_PAD_LEFT) }} - Delawala Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11.5px; color: #0F172A; background: #F1F5F9; padding: 24px; line-height: 1.45; }

        .receipt-card {
            max-width: 680px;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #E2E8F0;
            position: relative;
        }

        /* ── Action Toolbar ── */
        .no-print-bar {
            max-width: 680px;
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
            padding-bottom: 16px;
            margin-bottom: 18px;
            border-bottom: 2.5px solid #2563EB;
        }
        .co-name { font-size: 22px; font-weight: 900; color: #0F172A; letter-spacing: 0.3px; }
        .co-sub { font-size: 10px; color: #2563EB; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 3px; }
        .co-firm { font-size: 11px; color: #64748B; margin-top: 4px; font-weight: 600; }
        .rpt-meta { text-align: right; }
        .rpt-title { font-size: 15px; font-weight: 900; color: #0F172A; text-transform: uppercase; margin-bottom: 3px; }
        .rpt-doc-no { font-size: 11.5px; color: #2563EB; font-weight: 800; }
        .rpt-date { font-size: 10.5px; color: #64748B; margin-top: 2px; }

        /* ── Badge / Banner ── */
        .receipt-banner {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            color: #FFFFFF;
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .banner-info h3 { font-size: 15px; font-weight: 800; color: #F8FAFC; }
        .banner-info p { font-size: 11px; color: #94A3B8; margin-top: 2px; }
        .badge-pill {
            background: rgba(16, 185, 129, 0.2);
            color: #34D399;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 6px;
            border: 1px solid rgba(16, 185, 129, 0.4);
            text-transform: uppercase;
        }

        /* ── Info Grid ── */
        .grid-2 { display: flex; gap: 14px; margin-bottom: 18px; }
        .grid-col { flex: 1; border: 1px solid #E2E8F0; border-radius: 8px; padding: 12px; background: #F8FAFC; }
        .col-heading {
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #2563EB;
            margin-bottom: 8px;
            border-bottom: 1px solid #E2E8F0;
            padding-bottom: 3px;
        }
        .info-row { display: flex; justify-content: space-between; padding: 3.5px 0; border-bottom: 1px dashed #E2E8F0; font-size: 11px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #64748B; font-weight: 600; }
        .info-val { color: #0F172A; font-weight: 700; text-align: right; }

        /* ── Financial Breakdown Table ── */
        .receipt-table { width: 100%; border-collapse: collapse; font-size: 11.5px; margin-bottom: 18px; border: 1px solid #E2E8F0; border-radius: 8px; overflow: hidden; }
        .receipt-table th { background: #1E293B; color: #F8FAFC; padding: 8px 12px; font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.6px; font-weight: 800; text-align: left; }
        .receipt-table th.tar, .receipt-table td.tar { text-align: right; }
        .receipt-table td { padding: 9px 12px; border-bottom: 1px solid #E2E8F0; }
        .receipt-table tr:last-child td { border-bottom: none; }
        .receipt-table tr.total-row td { background: #F8FAFC; font-weight: 800; font-size: 12px; }
        .receipt-table tr.paid-row td { background: rgba(16, 185, 129, 0.08); font-weight: 900; font-size: 13.5px; color: #059669; }
        .receipt-table tr.pending-row td { background: #FFF; font-weight: 800; color: #DC2626; }

        /* ── Payment Meta Box ── */
        .meta-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 11px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
        }

        /* ── Signatures ── */
        .auth-block {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .auth-col { width: 180px; text-align: center; }
        .auth-line {
            border-top: 1.5px solid #0F172A;
            margin-top: 42px;
            padding-top: 5px;
            font-size: 10px;
            font-weight: 800;
            color: #0F172A;
        }

        /* ── Footer ── */
        .rpt-footer {
            margin-top: 22px;
            padding-top: 10px;
            border-top: 1px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            color: #94A3B8;
            font-size: 9.5px;
        }

        @media print {
            body { background: #FFFFFF; padding: 0; }
            .no-print-bar { display: none !important; }
            .receipt-card { box-shadow: none; border: 1px solid #CBD5E1; padding: 16px; margin: 0; }
            @page { margin: 10mm; size: portrait; }
        }
    </style>
</head>
<body>

<div class="no-print-bar">
    <a href="{{ route('rental-payments.index', $rental->id) }}" class="btn-action btn-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Collections
    </a>
    <button onclick="window.print()" class="btn-action btn-print">
        <i class="fa-solid fa-print"></i> Print Receipt
    </button>
</div>

<div class="receipt-card">
    <!-- Header -->
    <div class="rpt-header">
        <div>
            <div class="co-name">Delawala</div>
            <div class="co-sub">Properties &amp; Management</div>
            <div class="co-firm"><i class="fa-solid fa-building"></i> {{ $rental->firm->firm_name ?? 'Delawala Group' }}</div>
        </div>
        <div class="rpt-meta">
            <div class="rpt-title">Official Rent Receipt</div>
            <div class="rpt-doc-no">Receipt #RCP-{{ str_pad($rentalPayment->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div class="rpt-date">Date: {{ $rentalPayment->payment_date ? \Carbon\Carbon::parse($rentalPayment->payment_date)->format('d M Y') : now()->format('d M Y') }}</div>
        </div>
    </div>

    <!-- Banner -->
    <div class="receipt-banner">
        <div class="banner-info">
            <h3>Period: {{ $rentalPayment->payment_month }} {{ $rentalPayment->payment_year }}</h3>
            <p>Received with thanks from <strong>{{ $rental->tenant_name ?? ($rental->tenant->name ?? 'Tenant') }}</strong></p>
        </div>
        <div>
            <span class="badge-pill">{{ ucfirst($rentalPayment->payment_status) }}</span>
        </div>
    </div>

    <!-- 2 Column Details -->
    <div class="grid-2">
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
                <span class="info-label">Agreement No:</span>
                <span class="info-val">{{ $rental->agreement_no ?: 'RA-'.str_pad($rental->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <div class="grid-col">
            <div class="col-heading"><i class="fa-solid fa-building"></i> Property Details</div>
            @php
                $activeProp = $rentalPayment->property ?? $rental->all_properties->first();
                $allProps = $rental->all_properties;
            @endphp
            @if($allProps->count() > 1)
                <div class="info-row">
                    <span class="info-label">Rented Units:</span>
                    <span class="info-val" style="color: #2563EB;">
                        @foreach($allProps as $p)
                            {{ $p->property_name ?: ($p->unit_no ? 'Unit #' . $p->unit_no : 'Property #' . $p->id) }}@if(!$loop->last), @endif
                        @endforeach
                    </span>
                </div>
            @else
                <div class="info-row">
                    <span class="info-label">Property Name:</span>
                    <span class="info-val">{{ $activeProp->property_name ?? ($rental->property->property_name ?? '—') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Unit / Code:</span>
                    <span class="info-val">{{ $activeProp->property_code ?? ($activeProp && $activeProp->unit_no ? 'Unit #'.$activeProp->unit_no : '—') }}</span>
                </div>
            @endif
            <div class="info-row">
                <span class="info-label">Project:</span>
                <span class="info-val">{{ $activeProp?->project?->project_name ?? ($rental->property?->project?->project_name ?? 'Direct Property') }}</span>
            </div>
        </div>
    </div>

    <!-- Breakdown Table -->
    @php
        $maint = (float) ($rentalPayment->maintenance_amount ?? 0);
        $totalDue = (float) ($rentalPayment->total_amount ?: ($rentalPayment->rent_amount + $maint));
    @endphp
    <table class="receipt-table">
        <thead>
            <tr>
                <th>Item Description</th>
                <th class="tar">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Monthly Base Rent Amount ({{ $rentalPayment->payment_month }} {{ $rentalPayment->payment_year }})</td>
                <td class="tar">₹{{ number_format($rentalPayment->rent_amount, 2) }}</td>
            </tr>
            @if($maint > 0)
            <tr>
                <td>Society / Monthly Maintenance Charges</td>
                <td class="tar">₹{{ number_format($maint, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total Billed / Due Amount</td>
                <td class="tar">₹{{ number_format($totalDue, 2) }}</td>
            </tr>
            <tr class="paid-row">
                <td>Amount Received / Paid</td>
                <td class="tar">₹{{ number_format($rentalPayment->paid_amount, 2) }}</td>
            </tr>
            @if($rentalPayment->pending_amount > 0)
            <tr class="pending-row">
                <td>Balance Pending for this Month</td>
                <td class="tar">₹{{ number_format($rentalPayment->pending_amount, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Payment Meta Box -->
    <div class="meta-box">
        <div>
            <strong>Payment Mode:</strong> {{ $rentalPayment->payment_mode ?: 'Cash' }}
        </div>
        @if($rentalPayment->remarks)
        <div>
            <strong>Note:</strong> {{ $rentalPayment->remarks }}
        </div>
        @endif
    </div>

    <!-- Signature Block -->
    <div class="auth-block">
        <div class="auth-col">
            <div class="auth-line">Tenant Signature</div>
        </div>
        <div class="auth-col">
            <div class="auth-line">Authorized Signatory &amp; Stamp</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="rpt-footer">
        <span>This is a valid official receipt issued by Delawala Properties &amp; Management.</span>
        <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
    </div>
</div>

</body>
</html>
