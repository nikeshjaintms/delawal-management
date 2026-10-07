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

<div id="delawala-printable-area" class="delawala-printable-area">
@include('admin.components.pdf-header', [
    'title' => 'GST Sales Report (GSTR-1)',
    'subtitle' => 'Outward Supplies & Tax Liability Register',
    'isBw' => true
])

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Invoices</div>
        <div class="s-value">{{ $sales->count() }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Taxable Value</div>
        <div class="s-value">₹{{ number_format($totalTaxable, 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">CGST Amount</div>
        <div class="s-value">₹{{ number_format($totalCgst, 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">SGST Amount</div>
        <div class="s-value">₹{{ number_format($totalSgst, 2) }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">IGST Amount</div>
        <div class="s-value">₹{{ number_format($totalIgst, 2) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total GST Liability</div>
        <div class="s-value">₹{{ number_format($totalGst, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Grand Total Realized</div>
        <div class="s-value">₹{{ number_format($grandTotal, 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-file-invoice"></i> Outward Supplies Register</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Invoice No</th>
            <th>Invoice Date</th>
            <th>Customer Name</th>
            <th>Customer GSTIN</th>
            <th class="r">Taxable (₹)</th>
            <th class="r">CGST (₹)</th>
            <th class="r">SGST (₹)</th>
            <th class="r">IGST (₹)</th>
            <th class="r">Total GST (₹)</th>
            <th class="r">Invoice Total (₹)</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $i => $s)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $s->invoice_no ?? ($s->agreement_no ?? 'INV-'.str_pad($s->id, 5, '0', STR_PAD_LEFT)) }}</strong></td>
            <td>{{ $s->invoice_date ? \Carbon\Carbon::parse($s->invoice_date)->format('d M Y') : ($s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '—') }}</td>
            <td><strong>{{ $s->customer->name ?? ($s->recipient_name ?? '—') }}</strong></td>
            <td>{{ $s->customer->gst_no ?? ($s->recipient_gstin ?? 'Unregistered') }}</td>
            <td class="r">₹{{ number_format($s->taxable_amount ?? ($s->subtotal ?? ($s->sale_amount ?? 0)), 2) }}</td>
            <td class="r" style="color:#2563EB;">₹{{ number_format($s->cgst_amount ?? 0, 2) }}</td>
            <td class="r" style="color:#2563EB;">₹{{ number_format($s->sgst_amount ?? 0, 2) }}</td>
            <td class="r" style="color:#7C3AED;">₹{{ number_format($s->igst_amount ?? 0, 2) }}</td>
            <td class="r" style="color:#DC2626; font-weight:700;">₹{{ number_format($s->tax_amount ?? 0, 2) }}</td>
            <td class="r" style="color:#059669; font-weight:800;">₹{{ number_format($s->total_amount ?? ($s->sale_amount ?? 0), 2) }}</td>
            <td class="c"><span class="badge badge-success">Completed</span></td>
        </tr>
        @empty
        <tr><td colspan="12" class="c" style="padding:20px;color:#64748B;">No GST sales records found.</td></tr>
        @endforelse
    </tbody>
    @if($sales->count() > 0)
    <tfoot>
        <tr>
            <td colspan="5" class="r">Grand Total</td>
            <td class="r" style="color:#D97706;">₹{{ number_format($totalTaxable, 2) }}</td>
            <td class="r" style="color:#2563EB;">₹{{ number_format($totalCgst, 2) }}</td>
            <td class="r" style="color:#2563EB;">₹{{ number_format($totalSgst, 2) }}</td>
            <td class="r" style="color:#7C3AED;">₹{{ number_format($totalIgst, 2) }}</td>
            <td class="r" style="color:#DC2626; font-weight:800;">₹{{ number_format($totalGst, 2) }}</td>
            <td class="r" style="color:#059669; font-weight:800; font-size:12px;">₹{{ number_format($grandTotal, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

@include('admin.components.pdf-footer')
</div>
</body>
</html>