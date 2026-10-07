<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoices Register Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Tax Invoices Register Report',
    'orientation' => 'landscape',
    'backUrl' => route('invoices.index')
])

<div id="delawala-printable-area" class="delawala-printable-area">
@include('admin.components.pdf-header', [
    'title' => 'Tax Invoices Register Report',
    'subtitle' => 'Billing Register & GST Breakdown',
    'isBw' => true
])

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Invoices</div>
        <div class="s-value">{{ $totalInvoices ?? $invoices->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Total Invoiced</div>
        <div class="s-value">₹{{ number_format($totalInvoiced ?? $invoices->sum('total_amount'), 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Total Collected</div>
        <div class="s-value">₹{{ number_format($totalPaid ?? $invoices->sum('paid_amount'), 2) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total Balance Due</div>
        <div class="s-value">₹{{ number_format($totalBalance ?? ($totalDue ?? $invoices->sum('balance_amount')), 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-file-invoice-dollar"></i> Tax Invoices Register</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Invoice No</th>
            <th>Recipient Name</th>
            <th>Date</th>
            <th class="r">Taxable</th>
            <th class="r">GST</th>
            <th class="r">Total Amount</th>
            <th class="r">Paid</th>
            <th class="r">Balance</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($invoices as $i => $inv)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $inv->invoice_no }}</strong></td>
            <td>{{ $inv->recipient_name }}</td>
            <td>{{ $inv->invoice_date ? $inv->invoice_date->format('d/m/Y') : '—' }}</td>
            <td class="r">₹{{ number_format($inv->subtotal ?? 0, 2) }}</td>
            <td class="r">₹{{ number_format($inv->tax_amount ?? 0, 2) }}</td>
            <td class="r" style="font-weight:700;">₹{{ number_format($inv->total_amount ?? 0, 2) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">₹{{ number_format($inv->paid_amount ?? 0, 2) }}</td>
            <td class="r" style="color:#DC2626; font-weight:700;">₹{{ number_format($inv->balance_amount ?? 0, 2) }}</td>
            <td class="c">
                @if($inv->payment_status === 'paid')
                    <span class="badge badge-success">Paid</span>
                @elseif($inv->payment_status === 'partial')
                    <span class="badge badge-warning">Partial</span>
                @else
                    <span class="badge badge-danger">Unpaid</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="10" class="c" style="padding:20px;color:#64748B;">No invoice records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</div>
</body>
</html>