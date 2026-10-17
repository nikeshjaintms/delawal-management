<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GST Purchase Report (GSTR-2/3B) - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'GST Purchase Report (GSTR-2/3B)',
    'orientation' => 'landscape',
    'backUrl' => route('reports.gst-purchase')
])

@include('admin.components.pdf-header', [
    'title' => 'GST Inward Supplies Statement (GSTR-2 & Input Tax Credit)',
    'subtitle' => 'Vendor Invoices, Taxable Purchases & Eligible Input Tax Credit (ITC) Register'
])

{{-- ── KPI Summary Cards ── --}}
<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Purchase Bills</div>
        <div class="s-value">{{ $expenses->count() }} Invoices</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Total Taxable Amount</div>
        <div class="s-value">{{ formatIndianCurrency($totalTaxable ?? 0) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">CGST Input Tax (ITC)</div>
        <div class="s-value">{{ formatIndianCurrency($totalCgst ?? 0) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">SGST Input Tax (ITC)</div>
        <div class="s-value">{{ formatIndianCurrency($totalSgst ?? 0) }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">IGST Input Tax (ITC)</div>
        <div class="s-value">{{ formatIndianCurrency($totalIgst ?? 0) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total Input Tax Credit</div>
        <div class="s-value">{{ formatIndianCurrency($totalGst ?? 0) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Grand Total Purchases</div>
        <div class="s-value">{{ formatIndianCurrency($grandTotal ?? 0) }}</div>
    </div>
</div>

<div class="section-label">
    <i class="fa-solid fa-receipt" style="color:#059669;"></i> Inward Supplies &amp; Eligible Input Tax Credit (ITC) Ledger
</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:75px;">Bill #</th>
            <th style="width:65px;">Date</th>
            <th>Vendor / Supplier</th>
            <th>Expense / Material Title</th>
            <th class="c" style="width:65px;">HSN/SAC</th>
            <th class="r" style="width:95px;">Taxable (₹)</th>
            <th class="r" style="width:75px;">CGST (₹)</th>
            <th class="r" style="width:75px;">SGST (₹)</th>
            <th class="r" style="width:75px;">IGST (₹)</th>
            <th class="r" style="width:85px;">Total GST (₹)</th>
            <th class="r" style="width:105px;">Bill Total (₹)</th>
            <th class="c" style="width:65px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $i => $exp)
        @php
            $status = strtolower($exp->approval_status ?? 'approved');
            $supplierName = $exp->vendor?->name ?? ($exp->paid_to ?? '—');
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="font-size:8px;">
                <strong>{{ $exp->bill_no ?: ('EXP-' . $exp->id) }}</strong>
                @if(!empty($exp->invoice_no))
                    <div style="font-size:7.5px; color:#64748B;">Inv: {{ $exp->invoice_no }}</div>
                @endif
            </td>
            <td style="white-space:nowrap;">
                {{ $exp->expense_date ? \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') : '—' }}
            </td>
            <td><strong>{{ $supplierName }}</strong></td>
            <td>{{ $exp->expense_title ?: ($exp->title ?: '—') }}</td>
            <td class="c">{{ $exp->hsn_code ?? '—' }}</td>
            <td class="r">{{ formatIndianCurrency($exp->computed_taxable ?? $exp->amount) }}</td>
            <td class="r" style="color:#2563EB;">{{ formatIndianCurrency($exp->computed_cgst ?? 0) }}</td>
            <td class="r" style="color:#2563EB;">{{ formatIndianCurrency($exp->computed_sgst ?? 0) }}</td>
            <td class="r" style="color:#7C3AED;">{{ formatIndianCurrency($exp->computed_igst ?? 0) }}</td>
            <td class="r" style="color:#DC2626; font-weight:700;">{{ formatIndianCurrency($exp->computed_total_gst ?? 0) }}</td>
            <td class="r" style="color:#059669; font-weight:800;">{{ formatIndianCurrency($exp->computed_total ?? $exp->amount) }}</td>
            <td class="c">
                <span class="badge {{ $status === 'approved' ? 'badge-success' : 'badge-warning' }}">
                    {{ ucfirst($exp->approval_status ?? 'Approved') }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="13" class="c" style="padding:24px; color:#64748B;">No GST purchase bills found for the selected period.</td>
        </tr>
        @endforelse
    </tbody>
    @if($expenses->count() > 0)
    <tfoot>
        <tr class="double-total">
            <td colspan="6" class="r">GSTR-2 INWARD SUPPLIES TOTAL:</td>
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

@php $hasCustomAuthBlock = true; @endphp
@include('admin.components.pdf-signature', [
    'leftRole' => 'Purchase & Tax Officer',
    'rightRole' => 'Authorized Signatory / Delawala Management'
])

@include('admin.components.pdf-footer', ['note' => 'Statutory GST Purchase Statement • Input Tax Credit (ITC) Reconciled with GSTR-2B'])
</body>
</html>