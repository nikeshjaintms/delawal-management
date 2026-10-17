<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Notes Statement Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Credit Notes Statement Report',
    'orientation' => 'landscape',
    'backUrl' => route('reports.credit-note')
])

@include('admin.components.pdf-header', [
    'title' => 'Credit Notes Register & Adjustments Statement',
    'subtitle' => 'Sales Allowances, Cancellation Adjustments & Customer Credit Notes Ledger'
])

{{-- ── KPI Summary Cards ── --}}
<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Credit Notes</div>
        <div class="s-value">{{ $notes->count() }} Notes</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Total Taxable Value</div>
        <div class="s-value">{{ formatIndianCurrency($totalTaxable ?? 0) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total GST Reversal</div>
        <div class="s-value">{{ formatIndianCurrency($totalGst ?? 0) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Total Credit Value (Gross)</div>
        <div class="s-value">{{ formatIndianCurrency($totalCredit ?? 0) }}</div>
    </div>
</div>

<div class="section-label">
    <i class="fa-solid fa-file-invoice-dollar" style="color:#059669;"></i> Issued Customer Credit Notes Register
</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:75px;">Note Date</th>
            <th style="width:90px;">Credit Note #</th>
            <th>Customer / Buyer Name</th>
            <th>Original Invoice / Ref</th>
            <th>Reason / Remarks</th>
            <th class="r" style="width:110px;">Taxable (₹)</th>
            <th class="r" style="width:95px;">GST (₹)</th>
            <th class="r" style="width:115px;">Credit Total (₹)</th>
            <th class="c" style="width:70px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($notes as $i => $n)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="white-space:nowrap;">{{ $n->note_date ? \Carbon\Carbon::parse($n->note_date)->format('d M Y') : '—' }}</td>
            <td><strong>{{ $n->credit_note_no ?? ('CN-' . $n->id) }}</strong></td>
            <td><strong>{{ $n->customer->name ?? '—' }}</strong></td>
            <td>{{ $n->invoice->invoice_no ?? ($n->propertySale?->agreement_no ?? '—') }}</td>
            <td style="font-size:8px;">{{ $n->reason ?? ($n->remarks ?? 'Sales Adjustment') }}</td>
            <td class="r">{{ formatIndianCurrency($n->taxable_amount ?? 0) }}</td>
            <td class="r" style="color:#64748B;">{{ formatIndianCurrency($n->tax_amount ?? 0) }}</td>
            <td class="r" style="color:#DC2626; font-weight:700;">{{ formatIndianCurrency($n->total_amount ?? 0) }}</td>
            <td class="c">
                <span class="badge badge-success">{{ ucfirst($n->status ?? 'Active') }}</span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="10" class="c" style="padding:24px; color:#64748B;">No credit note vouchers found for the selected period.</td>
        </tr>
        @endforelse
    </tbody>
    @if($notes->count() > 0)
    <tfoot>
        <tr class="double-total">
            <td colspan="6" class="r">CREDIT NOTES GRAND TOTAL:</td>
            <td class="r" style="font-weight:800;">{{ formatIndianCurrency($totalTaxable ?? 0) }}</td>
            <td class="r" style="font-weight:800;">{{ formatIndianCurrency($totalGst ?? 0) }}</td>
            <td class="r" style="color:#DC2626; font-size:10px; font-weight:900;">
                {{ formatIndianCurrency($totalCredit ?? 0) }}
            </td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

@php $hasCustomAuthBlock = true; @endphp
@include('admin.components.pdf-signature', [
    'leftRole' => 'Billing & Tax Officer',
    'rightRole' => 'Authorised Signatory / Partner Approval'
])

@include('admin.components.pdf-footer', ['note' => 'Statutory Credit Notes Register • Section 34 of CGST Act Compliant'])
</body>
</html>