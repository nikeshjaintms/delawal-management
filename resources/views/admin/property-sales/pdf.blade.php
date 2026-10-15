<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Sales & Conveyance Register - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Property Sales & Conveyance Register',
    'orientation' => 'landscape',
    'backUrl' => route('property-sales.index')
])

@php
    $allSales = $sales ?? ($propertySales ?? collect([]));
    $totSaleAmount = $totalSaleAmount ?? $allSales->sum('sale_amount');
    $totReceived = $totalBookingAmount ?? ($totalReceived ?? $allSales->sum('booking_amount'));
    $totRemaining = $totalRemaining ?? $allSales->sum('remaining_amount');

    $filters = [];
    if(request('from_date') || request('to_date') || request('sale_date_from') || request('sale_date_to')) {
        $f = request('from_date') ?: request('sale_date_from');
        $t = request('to_date') ?: request('sale_date_to');
        $filters['Period'] = ($f ? \Carbon\Carbon::parse($f)->format('d M Y') : 'Start') . ' to ' . ($t ? \Carbon\Carbon::parse($t)->format('d M Y') : 'Present');
    }
    if(request('project_id')) {
        $p = \App\Models\Project::find(request('project_id'));
        if($p) $filters['Site / Project'] = $p->project_name ?? $p->name;
    }
    if(request('customer_id')) {
        $c = \App\Models\Customer::find(request('customer_id'));
        if($c) $filters['Buyer / Customer'] = $c->name;
    }
    if(request('sale_status')) {
        $filters['Status'] = ucfirst(request('sale_status'));
    }
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Property Sales & Conveyance Register',
    'subtitle' => 'Customer Deeds, Consideration Values, Receipts & Sundry Debtor Balances',
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Sale Contracts</div>
        <div class="s-value">{{ $totalSalesCount ?? ($totalSales ?? $allSales->count()) }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Gross Consideration Value</div>
        <div class="s-value">{{ formatIndianCurrency($totSaleAmount) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Realized Inflows</div>
        <div class="s-value">{{ formatIndianCurrency($totReceived) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Sundry Debtors Receivable</div>
        <div class="s-value">{{ formatIndianCurrency($totRemaining) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-file-contract"></i> Conveyance Deeds &amp; Sales Register</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:90px;">Agreement / Ref</th>
            <th>Purchaser / Customer</th>
            <th>Property / Unit / Scheme</th>
            <th style="width:75px;">Sale Date</th>
            <th class="r" style="width:110px;">Sale Value (₹)</th>
            <th class="r" style="width:110px;">Realized (₹)</th>
            <th class="r" style="width:110px;">Balance Due (₹)</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($allSales as $i => $s)
        @php
            $sAmt = (float)($s->sale_amount ?? 0);
            $rAmt = (float)($s->booking_amount ?? 0);
            $dAmt = (float)($s->remaining_amount ?? max(0, $sAmt - $rAmt));
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="font-weight:700; color:#1E3A8A;">{{ $s->agreement_no ?: ('SALE-' . $s->id) }}</td>
            <td><strong>{{ $s->customer->name ?? '—' }}</strong></td>
            <td>{{ $s->property->property_name ?? ($s->property->property_code ?? '—') }}</td>
            <td style="white-space:nowrap; color:#475569;">{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d-M-Y') : '—' }}</td>
            <td class="r" style="font-weight:600; color:#1E3A8A;">{{ formatIndianCurrency($sAmt, 2, false) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($rAmt, 2, false) }}</td>
            <td class="r" style="color:{{ $dAmt > 0 ? '#DC2626' : '#10B981' }}; font-weight:700;">{{ formatIndianCurrency($dAmt, 2, false) }}</td>
            <td class="c"><span class="badge badge-info">{{ ucfirst($s->sale_status ?? 'Completed') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:24px; color:#64748B;"><em>No property sales found matching the filter criteria.</em></td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="5" class="r"><strong>GRAND TOTAL CONVEYANCE PROCEEDS:</strong></td>
            <td class="r" style="color:#1E3A8A; font-size:11px;"><strong>{{ formatIndianCurrency($totSaleAmount) }}</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($totReceived) }}</strong></td>
            <td class="r" style="color:#DC2626; font-size:11px;"><strong>{{ formatIndianCurrency($totRemaining) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>

<div style="margin-top:12px; font-size:9px; color:#64748B; line-height:1.4;">
    <strong>Accounting Ledger Note:</strong> Outstanding balance due from buyers constitutes Sundry Debtors (Trade Receivables) under Current Assets in the Balance Sheet.
</div>

@include('admin.components.pdf-footer')
</body>
</html>