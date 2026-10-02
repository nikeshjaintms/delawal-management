@php
    $isGstInvoice = ((float)($invoice->tax_amount ?? 0) > 0 || (float)($invoice->tax_percent ?? 0) > 0);
    $invHeading = $isGstInvoice ? 'TAX INVOICE' : 'INVOICE / BILL OF SUPPLY';
    $invSubtitle = $isGstInvoice ? 'Official GST Tax Invoice (Under Rule 46 of CGST Rules)' : 'Official Commercial Bill / Invoice';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invHeading }} - {{ $invoice->invoice_no ?? 'INV' }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => $invHeading . ' #' . ($invoice->invoice_no ?? 'INV'),
    'orientation' => 'portrait',
    'backUrl' => isset($invoice->id) ? route('invoices.show', $invoice->id) : route('invoices.index')
])

@include('admin.components.pdf-header', [
    'title' => $invHeading,
    'subtitle' => $invSubtitle,
    'firm' => $invoice->firm ?? null,
    'docRef' => $invoice->invoice_no ?? 'INV'
])

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user"></i> Details of Receiver / Billed To</div>
        <div class="info-row">
            <span class="info-label">Name:</span>
            <span class="info-val">{{ $invoice->recipient_name ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Phone:</span>
            <span class="info-val">{{ $invoice->recipient_phone ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Address:</span>
            <span class="info-val">{{ $invoice->recipient_address ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">GSTIN / UIN:</span>
            <span class="info-val" style="color:{{ !empty($invoice->recipient_gstin) ? '#D97706' : '#64748B' }}; font-weight:700;">
                {{ $invoice->recipient_gstin ?: 'Unregistered (B2C)' }}
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">State / Code:</span>
            <span class="info-val">{{ $invoice->recipient_state ?? 'Gujarat' }} (24)</span>
        </div>
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-file-invoice-dollar"></i> Invoice &amp; Supply Details</div>
        <div class="info-row">
            <span class="info-label">Invoice Number:</span>
            <span class="info-val" style="color:#D97706; font-size:12px;">{{ $invoice->invoice_no ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Invoice Date:</span>
            <span class="info-val">{{ isset($invoice->invoice_date) ? $invoice->invoice_date->format('d M Y') : now()->format('d M Y') }}</span>
        </div>
        @if(!empty($invoice->due_date))
        <div class="info-row">
            <span class="info-label">Payment Due Date:</span>
            <span class="info-val" style="color:#DC2626;">{{ $invoice->due_date->format('d M Y') }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Place of Supply:</span>
            <span class="info-val">Gujarat (24)</span>
        </div>
        <div class="info-row">
            <span class="info-label">Project / Site:</span>
            <span class="info-val">{{ $invoice->project->project_name ?? 'General Development' }}</span>
        </div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-list-ol"></i> Particulars of Goods &amp; Services Supplied</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Item Description / Particulars</th>
            <th class="c" style="width:80px;">HSN/SAC</th>
            <th class="r" style="width:70px;">Qty</th>
            <th class="r" style="width:90px;">Rate (₹)</th>
            <th class="r" style="width:100px;">Taxable Value (₹)</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($invoice->items) && count($invoice->items) > 0)
            @foreach($invoice->items as $idx => $item)
                <tr>
                    <td class="c" style="color:#64748B;">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->item_description }}</strong>
                        @if(!empty($item->notes))
                            <br><span style="font-size:9px; color:#64748B;">{{ $item->notes }}</span>
                        @endif
                    </td>
                    <td class="c" style="color:#64748B;">{{ $item->hsn_sac_code ?: '9954' }}</td>
                    <td class="r">{{ number_format($item->quantity, 2) }} {{ $item->unit ?: 'Unit' }}</td>
                    <td class="r">₹{{ number_format($item->unit_price, 2) }}</td>
                    <td class="r" style="font-weight:700;">₹{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="c">1</td>
                <td>
                    <strong>Real Estate Construction &amp; Infrastructure Services</strong>
                    <br><span style="font-size:9px; color:#64748B;">Development &amp; Plotted Land Infrastructure</span>
                </td>
                <td class="c">9954</td>
                <td class="r">1.00 Unit</td>
                <td class="r">₹{{ number_format($invoice->subtotal ?? $invoice->total_amount ?? 0, 2) }}</td>
                <td class="r" style="font-weight:700;">₹{{ number_format($invoice->subtotal ?? $invoice->total_amount ?? 0, 2) }}</td>
            </tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="r">Total Taxable Amount</td>
            <td class="r" style="font-weight:700;">₹{{ number_format($invoice->subtotal ?? $invoice->total_amount ?? 0, 2) }}</td>
        </tr>
        @if(!empty($invoice->tax_amount) && $invoice->tax_amount > 0)
            @if(($invoice->tax_type ?? 'gst_intra') === 'gst_intra')
                <tr>
                    <td colspan="5" class="r" style="color:#2563EB;">CGST ({{ ($invoice->tax_percent ?? 18) / 2 }}%)</td>
                    <td class="r" style="color:#2563EB;">₹{{ number_format(($invoice->cgst_amount ?? ($invoice->tax_amount / 2)), 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="r" style="color:#2563EB;">SGST ({{ ($invoice->tax_percent ?? 18) / 2 }}%)</td>
                    <td class="r" style="color:#2563EB;">₹{{ number_format(($invoice->sgst_amount ?? ($invoice->tax_amount / 2)), 2) }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="5" class="r" style="color:#7C3AED;">IGST ({{ $invoice->tax_percent ?? 18 }}%)</td>
                    <td class="r" style="color:#7C3AED;">₹{{ number_format($invoice->igst_amount ?? $invoice->tax_amount, 2) }}</td>
                </tr>
            @endif
        @endif
        @if(!empty($invoice->discount_amount) && $invoice->discount_amount > 0)
        <tr>
            <td colspan="5" class="r" style="color:#D97706;">Discount Deducted</td>
            <td class="r" style="color:#D97706;">-₹{{ number_format($invoice->discount_amount, 2) }}</td>
        </tr>
        @endif
        @if(!empty($invoice->round_off) && $invoice->round_off != 0)
        <tr>
            <td colspan="5" class="r">Round Off Adjustment</td>
            <td class="r">{{ $invoice->round_off > 0 ? '+' : '' }}₹{{ number_format($invoice->round_off, 2) }}</td>
        </tr>
        @endif
        <tr style="background:#0F172A; color:#FFF;">
            <td colspan="5" class="r" style="color:#FFF; font-size:12px; font-weight:800;">Total Invoice Value (in Figures)</td>
            <td class="r" style="color:#FFF; font-size:13px; font-weight:800;">₹{{ number_format($invoice->total_amount ?? 0, 2) }}</td>
        </tr>
        @if(!empty($invoice->paid_amount) && $invoice->paid_amount > 0)
        <tr style="background:#ECFDF5; color:#059669; font-weight:700;">
            <td colspan="5" class="r" style="color:#059669;">Amount Received / Paid</td>
            <td class="r" style="color:#059669;">₹{{ number_format($invoice->paid_amount, 2) }}</td>
        </tr>
        @endif
        @if(($invoice->balance_amount ?? 0) > 0)
        <tr style="color:#DC2626; font-weight:800;">
            <td colspan="5" class="r" style="color:#DC2626;">Net Balance Payable</td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($invoice->balance_amount, 2) }}</td>
        </tr>
        @endif
    </tfoot>
</table>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building-columns"></i> Bank Account Remittance Details</div>
        <div class="info-row"><span class="info-label">Account Name:</span><span class="info-val"><strong>DELAWALA INFRA CO.</strong></span></div>
        <div class="info-row"><span class="info-label">Bank Name:</span><span class="info-val">{{ $invoice->bank_name ?: ($invoice->firm->bank_name ?? 'Bank of Baroda') }}</span></div>
        <div class="info-row"><span class="info-label">Account No:</span><span class="info-val" style="color:#D97706;">{{ $invoice->bank_account_no ?: ($invoice->firm->bank_account_no ?? '—') }}</span></div>
        <div class="info-row"><span class="info-label">IFSC Code:</span><span class="info-val">{{ $invoice->bank_ifsc ?: ($invoice->firm->bank_ifsc ?? '—') }}</span></div>
        <div class="info-row"><span class="info-label">Branch:</span><span class="info-val">Dahegam Branch</span></div>
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-clipboard-check"></i> Terms, Declarations &amp; Jurisdiction</div>
        <div style="font-size:9.5px; color:#475569; line-height:1.45;">
            <div>1. We declare that this invoice shows the actual price of the goods/services described and that all particulars are true and correct.</div>
            <div style="margin-top:2px;">2. Payment due within specified period. Delay in payment may attract interest @ 18% p.a.</div>
            <div style="margin-top:2px;">3. Subject to <strong>Dahegam / Bharuch Jurisdiction</strong> only.</div>
        </div>
    </div>
</div>

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Customer / Receiver Signature</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">For <strong>DELAWALA INFRA CO.</strong><br><small style="color:#64748B; font-weight:600;">(Authorized Signatory)</small></div>
    </div>
</div>

@include('admin.components.pdf-footer', [
    'note' => 'Official GST Tax Invoice • Delawala Infra Co. • GSTIN: 24CUBPD0770R1ZI'
])
</body>
</html>