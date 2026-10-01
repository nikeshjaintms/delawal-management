<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice - {{ $invoice->invoice_no ?? 'INV' }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Tax Invoice #' . ($invoice->invoice_no ?? 'INV'),
    'orientation' => 'portrait',
    'backUrl' => isset($invoice->id) ? route('invoices.show', $invoice->id) : route('invoices.index')
])

@include('admin.components.pdf-header', [
    'title' => 'TAX INVOICE',
    'subtitle' => 'Official GST Tax Invoice',
    'firm' => $invoice->firm ?? null,
    'docRef' => $invoice->invoice_no ?? 'INV'
])

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user"></i> Bill To (Recipient)</div>
        <div class="info-row">
            <span class="info-label">Name:</span>
            <span class="info-val">{{ $invoice->recipient_name ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mobile:</span>
            <span class="info-val">{{ $invoice->recipient_phone ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Address:</span>
            <span class="info-val">{{ $invoice->recipient_address ?? '—' }}</span>
        </div>
        @if(!empty($invoice->recipient_gstin))
        <div class="info-row">
            <span class="info-label">GSTIN:</span>
            <span class="info-val">{{ $invoice->recipient_gstin }}</span>
        </div>
        @endif
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-file-invoice-dollar"></i> Invoice Metadata</div>
        <div class="info-row">
            <span class="info-label">Invoice No:</span>
            <span class="info-val" style="color:#D97706;">{{ $invoice->invoice_no ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Invoice Date:</span>
            <span class="info-val">{{ isset($invoice->invoice_date) ? $invoice->invoice_date->format('d/m/Y') : now()->format('d/m/Y') }}</span>
        </div>
        @if(!empty($invoice->due_date))
        <div class="info-row">
            <span class="info-label">Due Date:</span>
            <span class="info-val">{{ $invoice->due_date->format('d/m/Y') }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Project:</span>
            <span class="info-val">{{ $invoice->project->project_name ?? 'General / Plotted' }}</span>
        </div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-list-ol"></i> Itemized Goods &amp; Services</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Item Description</th>
            <th class="c" style="width:80px;">HSN/SAC</th>
            <th class="r" style="width:70px;">Qty</th>
            <th class="r" style="width:90px;">Rate (₹)</th>
            <th class="r" style="width:100px;">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($invoice->items) && count($invoice->items) > 0)
            @foreach($invoice->items as $idx => $item)
                <tr>
                    <td class="c" style="color:#64748B;">{{ $idx + 1 }}</td>
                    <td><strong>{{ $item->item_description }}</strong></td>
                    <td class="c" style="color:#64748B;">{{ $item->hsn_sac_code ?: '—' }}</td>
                    <td class="r">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                    <td class="r">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="r" style="font-weight:700;">{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="c">1</td>
                <td><strong>Real Estate Services / Property Development</strong></td>
                <td class="c">9954</td>
                <td class="r">1.00 Unit</td>
                <td class="r">{{ number_format($invoice->subtotal ?? $invoice->total_amount ?? 0, 2) }}</td>
                <td class="r" style="font-weight:700;">{{ number_format($invoice->subtotal ?? $invoice->total_amount ?? 0, 2) }}</td>
            </tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="r">Total Taxable Value</td>
            <td class="r">₹{{ number_format($invoice->subtotal ?? $invoice->total_amount ?? 0, 2) }}</td>
        </tr>
        @if(!empty($invoice->tax_amount) && $invoice->tax_amount > 0)
        <tr>
            <td colspan="5" class="r">GST Tax Amount</td>
            <td class="r">₹{{ number_format($invoice->tax_amount, 2) }}</td>
        </tr>
        @endif
        <tr style="background:#0F172A; color:#FFF;">
            <td colspan="5" class="r" style="color:#FFF; font-size:12px;">Grand Total</td>
            <td class="r" style="color:#FFF; font-size:13px; font-weight:800;">₹{{ number_format($invoice->total_amount ?? 0, 2) }}</td>
        </tr>
    </tfoot>
</table>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building-columns"></i> Bank Account Remittance</div>
        <div class="info-row"><span class="info-label">Bank:</span><span class="info-val">{{ $invoice->bank_name ?: ($invoice->firm->bank_name ?? 'Bank of Baroda') }}</span></div>
        <div class="info-row"><span class="info-label">A/C No:</span><span class="info-val">{{ $invoice->bank_account_no ?: ($invoice->firm->bank_account_no ?? '—') }}</span></div>
        <div class="info-row"><span class="info-label">IFSC Code:</span><span class="info-val">{{ $invoice->bank_ifsc ?: ($invoice->firm->bank_ifsc ?? '—') }}</span></div>
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-clipboard-check"></i> Terms &amp; Settlement</div>
        <div style="font-size:9.5px; color:#64748B; line-height:1.4;">
            {{ $invoice->terms_conditions ?: '1. Payment due within 15 days. 2. Subject to Dahegam/Bharuch jurisdiction.' }}
        </div>
    </div>
</div>

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Receiver Signature</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">For Delawala Infra Co. / Authorized</div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>