<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GST Sales Report (GSTR-1) - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'GST Sales Report (GSTR-1)',
    'orientation' => 'landscape',
    'backUrl' => route('reports.gst-sales')
])

@include('admin.components.pdf-header', [
    'title' => 'GST Outward Supplies Statement (GSTR-1 Register)',
    'subtitle' => 'Taxable Sales, Central/State GST Liability & Tax Invoices Breakdown'
])

{{-- ── KPI Summary Cards ── --}}
<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Invoices</div>
        <div class="s-value">{{ $sales->count() }} Invoices</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Total Taxable Value</div>
        <div class="s-value">{{ formatIndianCurrency($totalTaxable ?? 0) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">CGST Output Tax</div>
        <div class="s-value">{{ formatIndianCurrency($totalCgst ?? 0) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">SGST Output Tax</div>
        <div class="s-value">{{ formatIndianCurrency($totalSgst ?? 0) }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">IGST Output Tax</div>
        <div class="s-value">{{ formatIndianCurrency($totalIgst ?? 0) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total GST Liability</div>
        <div class="s-value">{{ formatIndianCurrency($totalGst ?? 0) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Invoice Gross Realized</div>
        <div class="s-value">{{ formatIndianCurrency($grandTotal ?? 0) }}</div>
    </div>
</div>

<div class="section-label">
    <i class="fa-solid fa-file-invoice" style="color:#059669;"></i> Outward Taxable Supplies &amp; B2B / B2C Invoices Ledger
</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:75px;">Invoice #</th>
            <th style="width:65px;">Date</th>
            <th>Customer / Buyer Name</th>
            <th style="width:95px;">Customer GSTIN</th>
            <th class="r" style="width:95px;">Taxable (₹)</th>
            <th class="r" style="width:75px;">CGST (₹)</th>
            <th class="r" style="width:75px;">SGST (₹)</th>
            <th class="r" style="width:75px;">IGST (₹)</th>
            <th class="r" style="width:85px;">Total GST (₹)</th>
            <th class="r" style="width:105px;">Invoice Total (₹)</th>
            <th class="c" style="width:65px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $i => $s)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="font-size:8px;">
                <strong>{{ $s->invoice_no ?? ($s->agreement_no ?? ('INV-' . str_pad($s->id, 5, '0', STR_PAD_LEFT))) }}</strong>
            </td>
            <td style="white-space:nowrap;">
                {{ $s->invoice_date ? \Carbon\Carbon::parse($s->invoice_date)->format('d M Y') : ($s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '—') }}
            </td>
            <td><strong>{{ $s->customer->name ?? ($s->recipient_name ?? '—') }}</strong></td>
            <td style="font-size:8px;">{{ $s->customer->gst_no ?? ($s->recipient_gstin ?? 'Unregistered') }}</td>
            <td class="r">{{ formatIndianCurrency($s->taxable_amount ?? ($s->subtotal ?? ($s->sale_amount ?? 0))) }}</td>
            <td class="r" style="color:#2563EB;">{{ formatIndianCurrency($s->cgst_amount ?? 0) }}</td>
            <td class="r" style="color:#2563EB;">{{ formatIndianCurrency($s->sgst_amount ?? 0) }}</td>
            <td class="r" style="color:#7C3AED;">{{ formatIndianCurrency($s->igst_amount ?? 0) }}</td>
            <td class="r" style="color:#DC2626; font-weight:700;">{{ formatIndianCurrency($s->tax_amount ?? 0) }}</td>
            <td class="r" style="color:#059669; font-weight:800;">{{ formatIndianCurrency($s->total_amount ?? ($s->sale_amount ?? 0)) }}</td>
            <td class="c"><span class="badge badge-success">Completed</span></td>
        </tr>
        @empty
        <tr>
            <td colspan="12" class="c" style="padding:24px; color:#64748B;">No GST sales records found for the selected period.</td>
        </tr>
        @endforelse
    </tbody>
    @if($sales->count() > 0)
    <tfoot>
        <tr class="double-total">
            <td colspan="5" class="r">GSTR-1 OUTWARD SUPPLIES TOTAL:</td>
            <td class="r" style="font-weight:800;">{{ formatIndianCurrency($totalTaxable ?? 0) }}</td>
            <td class="r" style="color:#2563EB; font-weight:800;">{{ formatIndianCurrency($totalCgst ?? 0) }}</td>
            <td class="r" style="color:#2563EB; font-weight:800;">{{ formatIndianCurrency($totalSgst ?? 0) }}</td>
            <td class="r" style="color:#7C3AED; font-weight:800;">{{ formatIndianCurrency($totalIgst ?? 0) }}</td>
            <td class="r" style="color:#DC2626; font-weight:800;">{{ formatIndianCurrency($totalGst ?? 0) }}</td>
            <td class="r" style="color:#059669; font-weight:900; font-size:10px;">{{ formatIndianCurrency($grandTotal ?? 0) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- ── Signatures Block ── --}}
<div class="auth-block" style="margin-top:20px; page-break-inside:avoid;">
    <div class="auth-col">
        <div class="auth-line">GST Tax Accountant</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Authorized Signatory / Delawala Management</div>
    </div>
</div>

@include('admin.components.pdf-footer', ['note' => 'Statutory GST Sales Statement • GSTR-1 Outward Supplies Ledger'])
</body>
</html>