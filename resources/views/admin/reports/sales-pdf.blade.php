<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Sales & Revenue Register - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Property Sales Register',
    'orientation' => 'landscape',
    'backUrl' => route('reports.sales')
])

@include('admin.components.pdf-header', [
    'title' => 'Property Sales & Conveyance Register',
    'subtitle' => 'Turnover, Cost of Acquisition, Collections Realized & Profit Margins'
])

{{-- ── KPI Summary Cards ── --}}
<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Total Sales Turnover</div>
        <div class="s-value">{{ formatIndianCurrency($totalSale ?? 0) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Amount Realized (Inflow)</div>
        <div class="s-value">{{ formatIndianCurrency($totalReceived ?? 0) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Outstanding Debtors (Due)</div>
        <div class="s-value">{{ formatIndianCurrency($totalPending ?? 0) }}</div>
    </div>
    <div class="stat-box s-amber">
        <div class="s-label">Total Land/Purchase Cost</div>
        <div class="s-value">{{ formatIndianCurrency($totalPurchaseCost ?? 0) }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Net Sales Profit (Margin: {{ number_format($profitMargin ?? 0, 1) }}%)</div>
        <div class="s-value">{{ formatIndianCurrency($totalNetProfit ?? 0) }}</div>
    </div>
</div>

<div class="section-label">
    <i class="fa-solid fa-file-contract" style="color:#059669;"></i> Property Conveyance Deeds &amp; Realization Ledger
</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:65px;">Date</th>
            <th style="width:80px;">Agreement #</th>
            <th>Customer Name</th>
            <th>Property / Unit</th>
            <th class="r" style="width:90px;">Sale Value (₹)</th>
            <th class="r" style="width:85px;">Cost Price (₹)</th>
            <th class="r" style="width:90px;">Realized (₹)</th>
            <th class="r" style="width:90px;">Balance Due (₹)</th>
            <th class="r" style="width:85px;">Net Profit (₹)</th>
            <th class="c" style="width:65px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $i => $s)
        @php
            $propName = $s->property?->property_name ?: ($s->properties?->pluck('property_name')->join(', ') ?: '—');
            $custName = $s->customer?->name ?? '—';
            $custMob = $s->customer?->mobile ?? '';
            $st = strtolower($s->sale_status ?? 'completed');
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="white-space:nowrap;">{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '—' }}</td>
            <td style="font-size:8px;">
                <strong>{{ $s->agreement_no ?: ('SALE-' . $s->id) }}</strong>
                @if(!empty($s->invoice_no))
                    <div style="font-size:7.5px; color:#64748B;">Inv: {{ $s->invoice_no }}</div>
                @endif
            </td>
            <td>
                <strong>{{ $custName }}</strong>
                @if(!empty($custMob))
                    <div style="font-size:7.5px; color:#64748B;">Mob: {{ $custMob }}</div>
                @endif
            </td>
            <td>{{ $propName }}</td>
            <td class="r" style="font-weight:700;">{{ formatIndianCurrency($s->sale_amount ?? 0) }}</td>
            <td class="r" style="color:#64748B;">{{ formatIndianCurrency($s->total_purchase_cost ?? 0) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($s->received_amount ?? ($s->booking_amount ?? 0)) }}</td>
            <td class="r" style="color:{{ ($s->remaining_amount ?? 0) > 0 ? '#DC2626' : '#94A3B8' }}; font-weight:{{ ($s->remaining_amount ?? 0) > 0 ? '700' : 'normal' }};">
                {{ ($s->remaining_amount ?? 0) > 0 ? formatIndianCurrency($s->remaining_amount) : '—' }}
            </td>
            <td class="r" style="color:{{ ($s->net_profit ?? 0) >= 0 ? '#059669' : '#DC2626' }}; font-weight:700;">
                {{ formatIndianCurrency($s->net_profit ?? 0) }}
            </td>
            <td class="c">
                <span class="badge {{ $st === 'completed' ? 'badge-success' : ($st === 'partial' ? 'badge-warning' : 'badge-danger') }}">
                    {{ ucfirst($s->sale_status ?? 'Completed') }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="11" class="c" style="padding:24px; color:#64748B;">No property sales conveyance records found for the selected filters.</td>
        </tr>
        @endforelse
    </tbody>
    @if($records->count() > 0)
    <tfoot>
        <tr class="double-total">
            <td colspan="5" class="r">PORTFOLIO GRAND TOTALS:</td>
            <td class="r" style="font-size:9.5px; font-weight:800;">{{ formatIndianCurrency($totalSale ?? 0) }}</td>
            <td class="r" style="color:#64748B; font-size:9.5px;">{{ formatIndianCurrency($totalPurchaseCost ?? 0) }}</td>
            <td class="r" style="color:#059669; font-size:10px; font-weight:800;">{{ formatIndianCurrency($totalReceived ?? 0) }}</td>
            <td class="r" style="color:#DC2626; font-size:9.5px; font-weight:800;">{{ formatIndianCurrency($totalPending ?? 0) }}</td>
            <td class="r" style="color:{{ ($totalNetProfit ?? 0) >= 0 ? '#059669' : '#DC2626' }}; font-size:10px; font-weight:800;">
                {{ formatIndianCurrency($totalNetProfit ?? 0) }}
            </td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

@php $hasCustomAuthBlock = true; @endphp
@include('admin.components.pdf-signature', [
    'leftRole' => 'Legal & Conveyance Officer',
    'rightRole' => 'Managing Director / Partner Signature'
])

@include('admin.components.pdf-footer', ['note' => 'Official Sales Register • Audited from Executed Sale Deeds & Registry Records'])
</body>
</html>