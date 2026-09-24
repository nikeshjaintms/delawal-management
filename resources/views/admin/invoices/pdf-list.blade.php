<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoices Export Report - Delawala Management</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; }
        body { padding: 25px; color: #1E293B; background: #FFFFFF; font-size: 12px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 16px; border-bottom: 2px solid #0F172A; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: 800; color: #0F172A; }
        .meta { font-size: 11px; color: #64748B; margin-top: 4px; }
        .kpi-row { display: flex; gap: 15px; margin-bottom: 20px; }
        .kpi-box { flex: 1; padding: 12px 14px; border: 1px solid #CBD5E1; border-radius: 6px; background: #F8FAFC; }
        .kpi-lbl { font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase; }
        .kpi-val { font-size: 16px; font-weight: 800; color: #0F172A; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #0F172A; color: #FFFFFF; font-size: 11px; text-transform: uppercase; padding: 8px 10px; border: 1px solid #0F172A; text-align: left; }
        td { padding: 8px 10px; border: 1px solid #CBD5E1; font-size: 11.5px; }
        tr:nth-child(even) { background: #F8FAFC; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .badge-paid { background: #DCFCE7; color: #166534; }
        .badge-partial { background: #FEF3C7; color: #92400E; }
        .badge-unpaid { background: #FEE2E2; color: #991B1B; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 15px; display: flex; justify-content: flex-end; gap: 10px;">
    <button onclick="window.print()" style="padding: 8px 16px; background: #2563EB; color: #FFF; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">
        Print / Save as PDF
    </button>
</div>

<div class="header">
    <div>
        <div class="title">Invoices Summary Statement</div>
        <div class="meta">Delawala Management &bull; Generated on {{ date('d M, Y h:i A') }}</div>
    </div>
    <div style="text-align: right;">
        <div style="font-weight: 800; font-size: 14px; color: #2563EB;">Total Records: {{ $invoices->count() }}</div>
    </div>
</div>

<div class="kpi-row">
    <div class="kpi-box">
        <div class="kpi-lbl">Total Invoiced</div>
        <div class="kpi-val">₹{{ number_format($totalInvoiced, 2) }}</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-lbl">Total Collected</div>
        <div class="kpi-val" style="color: #166534;">₹{{ number_format($totalPaid, 2) }}</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-lbl">Total Outstanding Balance</div>
        <div class="kpi-val" style="color: #991B1B;">₹{{ number_format($totalBalance, 2) }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Inv No</th>
            <th>Date</th>
            <th>Recipient / Client</th>
            <th>Project</th>
            <th>Type</th>
            <th class="text-right">Total (₹)</th>
            <th class="text-right">Paid (₹)</th>
            <th class="text-right">Balance (₹)</th>
            <th class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($invoices as $inv)
            <tr>
                <td style="font-weight: 700;">{{ $inv->invoice_no }}</td>
                <td>{{ $inv->invoice_date->format('d/m/Y') }}</td>
                <td>{{ $inv->recipient_name }}</td>
                <td>{{ $inv->project->project_name ?? '—' }}</td>
                <td>{{ $inv->type_label }}</td>
                <td class="text-right" style="font-weight: 700;">{{ number_format($inv->total_amount, 2) }}</td>
                <td class="text-right" style="color: #166534;">{{ number_format($inv->paid_amount, 2) }}</td>
                <td class="text-right" style="color: {{ $inv->balance_amount > 0 ? '#991B1B' : '#64748B' }};">{{ number_format($inv->balance_amount, 2) }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ $inv->payment_status }}">
                        {{ str_replace('_', ' ', $inv->payment_status) }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px;">No invoices found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
