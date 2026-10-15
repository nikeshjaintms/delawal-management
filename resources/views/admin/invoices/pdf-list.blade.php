<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoices Master Register - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Tax Invoices Master Register',
    'orientation' => 'landscape',
    'backUrl' => route('invoices.index')
])

@php
    $totTaxable = 0;
    $totTax = 0;
    $totGross = 0;
    $totPaid = 0;
    $totDue = 0;

    foreach($invoices as $inv) {
        $totTaxable += (float)($inv->subtotal ?? 0);
        $totTax     += (float)($inv->tax_amount ?? 0);
        $totGross   += (float)($inv->total_amount ?? 0);
        $totPaid    += (float)($inv->paid_amount ?? 0);
        $totDue     += (float)($inv->balance_amount ?? max(0, ($inv->total_amount ?? 0) - ($inv->paid_amount ?? 0)));
    }

    $filters = [];
    if(request('from_date') || request('to_date')) {
        $filters['Period'] = (request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'Start') . ' to ' . (request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'Present');
    }
    if(request('payment_status')) {
        $filters['Payment Status'] = ucfirst(request('payment_status'));
    }
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Tax Invoices Master Register & Outward Billing Ledger',
    'subtitle' => 'Sales Invoices, Realized Collections, Debtor Balances & GST Accruals',
    'firm' => $selectedFirm ?? null,
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Invoices</div>
        <div class="s-value">{{ $totalInvoices ?? $invoices->count() }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Gross Billed Value</div>
        <div class="s-value">{{ formatIndianCurrency($totGross) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Total Realized Collections</div>
        <div class="s-value">{{ formatIndianCurrency($totPaid) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Outstanding Trade Debtors</div>
        <div class="s-value">{{ formatIndianCurrency($totDue) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-file-invoice-dollar"></i> Tax Invoices &amp; Revenue Billing Register</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:90px;">Invoice No</th>
            <th>Recipient / Billed Party</th>
            <th style="width:75px;">Date</th>
            <th class="r" style="width:105px;">Taxable (₹)</th>
            <th class="r" style="width:95px;">GST (₹)</th>
            <th class="r" style="width:110px;">Total Amount (₹)</th>
            <th class="r" style="width:110px;">Paid to Date (₹)</th>
            <th class="r" style="width:110px;">Balance Due (₹)</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($invoices as $i => $inv)
        @php
            $txb = (float)($inv->subtotal ?? 0);
            $gst = (float)($inv->tax_amount ?? 0);
            $tot = (float)($inv->total_amount ?? 0);
            $pd  = (float)($inv->paid_amount ?? 0);
            $bal = (float)($inv->balance_amount ?? max(0, $tot - $pd));
            $st  = strtolower($inv->payment_status ?? ($bal <= 0 ? 'paid' : ($pd > 0 ? 'partial' : 'unpaid')));
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="font-weight:700; color:#1E3A8A;">{{ $inv->invoice_no }}</td>
            <td><strong>{{ $inv->recipient_name }}</strong></td>
            <td style="white-space:nowrap; color:#475569;">{{ $inv->invoice_date ? $inv->invoice_date->format('d-M-Y') : '—' }}</td>
            <td class="r" style="color:#475569;">{{ formatIndianCurrency($txb, 2, false) }}</td>
            <td class="r" style="color:#1E3A8A;">{{ formatIndianCurrency($gst, 2, false) }}</td>
            <td class="r" style="font-weight:700; color:#0F172A;">{{ formatIndianCurrency($tot, 2, false) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($pd, 2, false) }}</td>
            <td class="r" style="color:{{ $bal > 0 ? '#DC2626' : '#10B981' }}; font-weight:700;">{{ formatIndianCurrency($bal, 2, false) }}</td>
            <td class="c">
                @if($st === 'paid')
                    <span class="badge badge-success">Paid</span>
                @elseif($st === 'partial')
                    <span class="badge badge-warning">Partial</span>
                @else
                    <span class="badge badge-danger">Unpaid</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="10" class="c" style="padding:24px; color:#64748B;"><em>No tax invoice records found matching the specified parameters.</em></td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="4" class="r"><strong>CONSOLIDATED TAX INVOICE TOTALS:</strong></td>
            <td class="r" style="font-size:10px;"><strong>{{ formatIndianCurrency($totTaxable, 2, false) }}</strong></td>
            <td class="r" style="color:#1E3A8A; font-size:10px;"><strong>{{ formatIndianCurrency($totTax, 2, false) }}</strong></td>
            <td class="r" style="color:#0F172A; font-size:11px;"><strong>{{ formatIndianCurrency($totGross) }}</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($totPaid) }}</strong></td>
            <td class="r" style="color:#DC2626; font-size:11px;"><strong>{{ formatIndianCurrency($totDue) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>

<div style="margin-top:14px; font-size:9px; color:#64748B; line-height:1.4;">
    <strong>Accounting Ledger Note:</strong> Outward taxable supplies generate Sundry Debtors balances under Current Assets and Output GST liability under Current Liabilities until settled.
</div>

@include('admin.components.pdf-footer')
</body>
</html>