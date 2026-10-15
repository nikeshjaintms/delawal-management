<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Statement of Account - {{ $vendor->name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Vendor Statement: ' . $vendor->name,
    'orientation' => 'landscape',
    'backUrl' => isset($vendor->id) ? route('vendors.show', $vendor->id) : route('vendors.index')
])

@php
    $totPO = $purchaseOrders ? $purchaseOrders->sum('grand_total') : 0;
    $totExp = $expenses ? $expenses->sum('amount') : 0;
    $totDN = $debitNotes ? $debitNotes->sum('total_amount') : 0;
    $netBalance = max(0, $totPO - $totExp - $totDN);
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Vendor Statement of Account & Supplier Ledger',
    'subtitle' => 'Procurement Orders, Disbursed Payments, Debit Notes & Outstanding Payables',
    'firm' => $vendor->firm ?? null,
    'docRef' => 'VEN-' . str_pad($vendor->id ?? 1, 5, '0', STR_PAD_LEFT)
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Supplier / Vendor</div>
        <div class="s-value" style="font-size:13px;">{{ $vendor->name }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Total Procurement (Cr.)</div>
        <div class="s-value">{{ formatIndianCurrency($totPO) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Disbursed Payments (Dr.)</div>
        <div class="s-value">{{ formatIndianCurrency($totExp) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Net Balance Payable</div>
        <div class="s-value">{{ formatIndianCurrency($netBalance) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-truck"></i> Vendor Particulars</div>
        <div class="info-row"><span class="info-label">Vendor Name:</span><span class="info-val"><strong>{{ $vendor->name }}</strong></span></div>
        <div class="info-row"><span class="info-label">Contact / Mobile:</span><span class="info-val">{{ $vendor->mobile ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Email:</span><span class="info-val">{{ $vendor->email ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">GSTIN:</span><span class="info-val"><strong style="color:#1E3A8A;">{{ $vendor->gst_no ?: 'Unregistered' }}</strong></span></div>
    </div>
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Billing Address & Terms</div>
        <div class="info-row"><span class="info-label">Address:</span><span class="info-val">{{ $vendor->address ?: 'Dahegam Road, Bharuch' }}</span></div>
        <div class="info-row"><span class="info-label">City / State:</span><span class="info-val">{{ $vendor->city ?: 'Dahegam' }}, {{ $vendor->state ?: 'Gujarat' }}</span></div>
        <div class="info-row"><span class="info-label">Payment Terms:</span><span class="info-val">{{ $vendor->payment_terms ?: 'Standard Net 30' }}</span></div>
        <div class="info-row"><span class="info-label">Account Status:</span><span class="info-val"><span class="badge badge-success">{{ strtoupper($vendor->status ?: 'ACTIVE') }}</span></span></div>
    </div>
</div>

@if(isset($purchaseOrders) && $purchaseOrders->isNotEmpty())
<div class="section-label"><i class="fa-solid fa-file-invoice"></i> Material Purchase Orders (Supplier Credits)</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:90px;">PO Number</th>
            <th style="width:75px;">PO Date</th>
            <th>Project / Target Site</th>
            <th>Items Specification</th>
            <th class="r" style="width:115px;">Order Value (₹)</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($purchaseOrders as $i => $po)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="font-weight:700; color:#1E3A8A;">{{ $po->po_number }}</td>
            <td style="white-space:nowrap; color:#475569;">{{ $po->po_date ? $po->po_date->format('d-M-Y') : '—' }}</td>
            <td>{{ $po->project->project_name ?? 'Head Office / Store' }}</td>
            <td style="font-size:8.5px; color:#475569;">
                @foreach($po->items as $it)
                    <span>• {{ $it->material->material_name ?? 'Material' }} ({{ number_format($it->qty ?? 0) }} {{ $it->material->unit ?? '' }}) </span>
                @endforeach
            </td>
            <td class="r" style="font-weight:700; color:#0F172A;">{{ formatIndianCurrency($po->grand_total ?? 0, 2, false) }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($po->status) }}</span></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="5" class="r"><strong>TOTAL PURCHASE BILLINGS:</strong></td>
            <td class="r" style="color:#1E3A8A; font-size:11px;"><strong>{{ formatIndianCurrency($totPO) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>
@endif

@if(isset($expenses) && $expenses->isNotEmpty())
<div class="section-label"><i class="fa-solid fa-receipt"></i> Payment Disbursements &amp; Settlements (Supplier Debits)</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:90px;">Voucher No</th>
            <th style="width:75px;">Payment Date</th>
            <th>Expense Particulars</th>
            <th>Project / Site</th>
            <th style="width:80px;">Payment Mode</th>
            <th class="r" style="width:115px;">Paid Amount (₹)</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses as $i => $e)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="font-weight:700; color:#1E3A8A;">EXP-{{ str_pad($e->id, 5, '0', STR_PAD_LEFT) }}</td>
            <td style="white-space:nowrap; color:#475569;">{{ $e->expense_date ? \Carbon\Carbon::parse($e->expense_date)->format('d-M-Y') : '—' }}</td>
            <td><strong>{{ $e->expense_title ?: ($e->title ?: 'Payment') }}</strong></td>
            <td>{{ $e->project->project_name ?? 'Head Office' }}</td>
            <td style="text-transform:capitalize; color:#475569;">{{ str_replace('_', ' ', $e->payment_mode ?: 'Cash') }}</td>
            <td class="r" style="font-weight:700; color:#059669;">{{ formatIndianCurrency($e->amount ?? 0, 2, false) }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($e->approval_status ?: 'Approved') }}</span></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="6" class="r"><strong>TOTAL DISBURSED PAYMENTS:</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($totExp) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>
@endif

<div style="margin-top:14px; font-size:9px; color:#64748B; line-height:1.4;">
    <strong>Accounting Ledger Note:</strong> Net balance payable to supplier is classified under Sundry Creditors (Trade Payables) under Current Liabilities in the Balance Sheet.
</div>

@include('admin.components.pdf-footer')
</body>
</html>