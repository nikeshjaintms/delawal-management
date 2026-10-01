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
    'title' => 'Credit Notes Statement Report',
    'subtitle' => 'Sales Returns & Customer Credit Adjustments'
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Credit Notes</div>
        <div class="s-value">{{ $notes->count() }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Taxable Value</div>
        <div class="s-value">₹{{ number_format($totalTaxable ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total GST Reversal</div>
        <div class="s-value">₹{{ number_format($totalGst ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Total Credit Amount</div>
        <div class="s-value">₹{{ number_format($totalCredit ?? 0, 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-file-invoice-dollar"></i> Issued Credit Notes Ledger</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Note No</th>
            <th>Date</th>
            <th>Customer / Party</th>
            <th>Original Invoice</th>
            <th class="r">Taxable</th>
            <th class="r">GST</th>
            <th class="r">Credit Total</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($notes as $i => $n)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $n->credit_note_no ?? 'CN-'.$n->id }}</strong></td>
            <td>{{ $n->note_date ? \Carbon\Carbon::parse($n->note_date)->format('d M Y') : '—' }}</td>
            <td>{{ $n->customer->name ?? '—' }}</td>
            <td>{{ $n->invoice->invoice_no ?? '—' }}</td>
            <td class="r">₹{{ number_format($n->taxable_amount ?? 0, 2) }}</td>
            <td class="r">₹{{ number_format($n->tax_amount ?? 0, 2) }}</td>
            <td class="r" style="color:#DC2626; font-weight:700;">₹{{ number_format($n->total_amount ?? 0, 2) }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($n->status ?? 'Active') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:20px;color:#64748B;">No credit note records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>