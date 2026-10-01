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
    'title' => 'GST Purchase Report (GSTR-2/3B)',
    'subtitle' => 'Input Tax Credit (ITC) & Vendor Purchases'
])

@if(request()->hasAny(['from_date','to_date','filter_vendor','filter_category','filter_status']))
<div class="filter-row">
    <span><strong>Filters Applied:</strong></span>
    @if(request('from_date'))<span>From: <strong>{{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}</strong></span>@endif
    @if(request('to_date'))<span>To: <strong>{{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}</strong></span>@endif
    @if(request('filter_status'))<span>Status: <strong>{{ ucfirst(request('filter_status')) }}</strong></span>@endif
</div>
@endif

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Bills</div>
        <div class="s-value">{{ $expenses->count() }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Taxable Amount</div>
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
        <div class="s-label">Total GST Tax</div>
        <div class="s-value">₹{{ number_format($totalGst, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Grand Total</div>
        <div class="s-value">₹{{ number_format($grandTotal, 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-receipt"></i> Inward Supplies &amp; Vendor Purchases Ledger</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Bill No</th>
            <th>Invoice No</th>
            <th>Date</th>
            <th>Vendor / Supplier</th>
            <th>Expense Title</th>
            <th class="c">HSN/SAC</th>
            <th class="r">Taxable (₹)</th>
            <th class="r">CGST (₹)</th>
            <th class="r">SGST (₹)</th>
            <th class="r">IGST (₹)</th>
            <th class="r">Total GST (₹)</th>
            <th class="r">Grand Total (₹)</th>
            <th class="c">Status</th>
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
            <td><strong>{{ $exp->bill_no ?? '—' }}</strong></td>
            <td>{{ $exp->invoice_no ?? '—' }}</td>
            <td>{{ $exp->expense_date ? \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') : '—' }}</td>
            <td><strong>{{ $supplierName }}</strong></td>
            <td style="font-size:10px;">{{ $exp->expense_title ?: ($exp->title ?: '—') }}</td>
            <td class="c">{{ $exp->hsn_code ?? '—' }}</td>
            <td class="r">₹{{ number_format($exp->computed_taxable ?? $exp->amount, 2) }}</td>
            <td class="r" style="color:#2563EB;">₹{{ number_format($exp->computed_cgst ?? 0, 2) }}</td>
            <td class="r" style="color:#2563EB;">₹{{ number_format($exp->computed_sgst ?? 0, 2) }}</td>
            <td class="r" style="color:#7C3AED;">₹{{ number_format($exp->computed_igst ?? 0, 2) }}</td>
            <td class="r" style="color:#DC2626; font-weight:700;">₹{{ number_format($exp->computed_total_gst ?? 0, 2) }}</td>
            <td class="r" style="color:#059669; font-weight:800;">₹{{ number_format($exp->computed_grand_total ?? $exp->amount, 2) }}</td>
            <td class="c">
                @if($status === 'approved')
                    <span class="badge badge-success">Approved</span>
                @elseif($status === 'rejected')
                    <span class="badge badge-danger">Rejected</span>
                @else
                    <span class="badge badge-warning">Pending</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="14" class="c" style="padding:20px;color:#64748B;">No GST purchase records found.</td></tr>
        @endforelse
    </tbody>
    @if($expenses->count() > 0)
    <tfoot>
        <tr>
            <td colspan="7" class="r">Total ({{ $expenses->count() }} records)</td>
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
</body>
</html>