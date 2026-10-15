<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debit Notes Statement Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Debit Notes Statement Report',
    'orientation' => 'landscape',
    'backUrl' => route('reports.debit-note')
])

@include('admin.components.pdf-header', [
    'title' => 'Debit Notes Register & Supplier Claims Statement',
    'subtitle' => 'Purchase Returns, Supplier Debit Notes & Input Tax Credit Adjustments'
])

{{-- ── KPI Summary Cards ── --}}
<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Debit Notes</div>
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
        <div class="s-label">Total Debit Value (Gross)</div>
        <div class="s-value">{{ formatIndianCurrency($totalDebit ?? 0) }}</div>
    </div>
</div>

<div class="section-label">
    <i class="fa-solid fa-file-invoice-dollar" style="color:#059669;"></i> Issued Supplier Debit Notes Register
</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:75px;">Note Date</th>
            <th style="width:90px;">Debit Note #</th>
            <th>Supplier / Vendor Name</th>
            <th>Original Bill / Invoice #</th>
            <th>Reason / Remarks</th>
            <th class="r" style="width:110px;">Taxable (₹)</th>
            <th class="r" style="width:95px;">GST (₹)</th>
            <th class="r" style="width:115px;">Debit Total (₹)</th>
            <th class="c" style="width:70px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($notes as $i => $n)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="white-space:nowrap;">{{ $n->note_date ? \Carbon\Carbon::parse($n->note_date)->format('d M Y') : '—' }}</td>
            <td><strong>{{ $n->debit_note_no ?? ('DN-' . $n->id) }}</strong></td>
            <td><strong>{{ $n->vendor->name ?? '—' }}</strong></td>
            <td>{{ $n->bill_no ?: ($n->invoice_no ?? '—') }}</td>
            <td style="font-size:8px;">{{ $n->reason ?? ($n->remarks ?? 'Purchase Return / Rate Difference') }}</td>
            <td class="r">{{ formatIndianCurrency($n->taxable_amount ?? 0) }}</td>
            <td class="r" style="color:#64748B;">{{ formatIndianCurrency($n->tax_amount ?? 0) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($n->total_amount ?? 0) }}</td>
            <td class="c">
                <span class="badge badge-success">{{ ucfirst($n->status ?? 'Active') }}</span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="10" class="c" style="padding:24px; color:#64748B;">No debit note vouchers found for the selected period.</td>
        </tr>
        @endforelse
    </tbody>
    @if($notes->count() > 0)
    <tfoot>
        <tr class="double-total">
            <td colspan="6" class="r">DEBIT NOTES GRAND TOTAL:</td>
            <td class="r" style="font-weight:800;">{{ formatIndianCurrency($totalTaxable ?? 0) }}</td>
            <td class="r" style="font-weight:800;">{{ formatIndianCurrency($totalGst ?? 0) }}</td>
            <td class="r" style="color:#059669; font-size:10px; font-weight:900;">
                {{ formatIndianCurrency($totalDebit ?? 0) }}
            </td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- ── Signatures Block ── --}}
<div class="auth-block" style="margin-top:20px; page-break-inside:avoid;">
    <div class="auth-col">
        <div class="auth-line">Purchase &amp; Accounts Officer</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Authorised Signatory / Partner Approval</div>
    </div>
</div>

@include('admin.components.pdf-footer', ['note' => 'Statutory Debit Notes Register • Input Tax Credit Adjustment Validated'])
</body>
</html>