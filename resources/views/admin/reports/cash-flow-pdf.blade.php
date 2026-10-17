<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Flow Statement & Bank Book - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Cash Flow Statement & Bank Book',
    'orientation' => 'landscape',
    'backUrl' => route('reports.cash-flow')
])

@include('admin.components.pdf-header', [
    'title' => 'Cash Flow Statement & Cash/Bank Book',
    'subtitle' => 'Statement of Cash Receipts, Disbursements & Liquidity Movement'
])

{{-- ── KPI Summary Cards ── --}}
<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Total Inflows / Receipts</div>
        <div class="s-value">{{ formatIndianCurrency($totalInflow ?? 0) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total Outflows / Payments</div>
        <div class="s-value">{{ formatIndianCurrency($totalOutflow ?? 0) }}</div>
    </div>
    <div class="stat-box {{ ($netCashFlow ?? 0) >= 0 ? 's-blue' : 's-red' }}">
        <div class="s-label">Net Cash Flow (Surplus / Deficit)</div>
        <div class="s-value">{{ formatIndianCurrency($netCashFlow ?? 0) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Sales &amp; Booking Receipts</div>
        <div class="s-value">{{ formatIndianCurrency($totalSalesInflow ?? 0) }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Rental Collections</div>
        <div class="s-value">{{ formatIndianCurrency($totalRentalInflow ?? 0) }}</div>
    </div>
    <div class="stat-box">
        <div class="s-label">Total Cash Transactions</div>
        <div class="s-value">{{ isset($allTransactions) ? $allTransactions->count() : 0 }}</div>
    </div>
</div>

{{-- ── 1. MONTHLY CASH FLOW SUMMARY (PROGRESSION) ── --}}
@if(isset($monthlyRows) && count($monthlyRows) > 0)
<div class="section-label">
    <i class="fa-solid fa-chart-line" style="color:#000000;"></i> Monthly Cash Flow Breakdown
</div>
<table>
    <thead>
        <tr>
            <th style="width:30px;" class="c">#</th>
            <th>Accounting Month</th>
            <th class="r" style="width:140px;">Cash Inflows / Receipts (₹)</th>
            <th class="r" style="width:140px;">Cash Outflows / Payments (₹)</th>
            <th class="r" style="width:140px;">Net Surplus / (Deficit) (₹)</th>
            <th class="c" style="width:100px;">Liquidity Status</th>
        </tr>
    </thead>
    <tbody>
        @php $runningCumulative = 0; @endphp
        @foreach($monthlyRows as $mi => $m)
        @php $runningCumulative += ($m['net'] ?? 0); @endphp
        <tr>
            <td class="c" style="color:#000000;">{{ $mi + 1 }}</td>
            <td><strong>{{ \Carbon\Carbon::parse($m['month'] . '-01')->format('F Y') }}</strong></td>
            <td class="r" style="color:#000000; font-weight:700;">+ {{ formatIndianCurrency($m['inflow'] ?? 0) }}</td>
            <td class="r" style="color:#000000; font-weight:700;">− {{ formatIndianCurrency($m['outflow'] ?? 0) }}</td>
            <td class="r" style="font-weight:800; color:#000000;">
                {{ formatIndianCurrency($m['net'] ?? 0) }}
            </td>
            <td class="c">
                @if(($m['net'] ?? 0) >= 0)
                    <span class="badge badge-success">Surplus</span>
                @else
                    <span class="badge badge-danger">Deficit</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="2" class="r">CUMULATIVE PERIOD TOTAL:</td>
            <td class="r" style="color:#000000; font-size:9.5px;">+ {{ formatIndianCurrency($totalInflow ?? 0) }}</td>
            <td class="r" style="color:#000000; font-size:9.5px;">− {{ formatIndianCurrency($totalOutflow ?? 0) }}</td>
            <td class="r" style="color:#000000; font-size:10px;">
                {{ formatIndianCurrency($netCashFlow ?? 0) }}
            </td>
            <td></td>
        </tr>
    </tfoot>
</table>
@endif

{{-- ── 2. DETAILED CASH & BANK REGISTER (CHRONOLOGICAL) ── --}}
<div class="section-label" style="margin-top:14px;">
    <i class="fa-solid fa-book-journal-whills" style="color:#000000;"></i> Detailed Cash &amp; Bank Ledger (All Inflows &amp; Outflows)
</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th style="width:75px;">Date</th>
            <th>Transaction Particulars &amp; Narration</th>
            <th class="c" style="width:110px;">Head / Stream</th>
            <th class="c" style="width:75px;">Mode</th>
            <th class="r" style="width:110px;">Cash Inflow / Cr. (₹)</th>
            <th class="r" style="width:110px;">Cash Outflow / Dr. (₹)</th>
            <th class="r" style="width:110px;">Net Position (₹)</th>
        </tr>
    </thead>
    <tbody>
        @php $runningBalance = 0; @endphp
        @forelse($allTransactions ?? [] as $i => $tx)
        @php
            $isReceipt = ($tx['type'] === 'inflow');
            $amt = $tx['amount'] ?? 0;
            $runningBalance += $isReceipt ? $amt : -$amt;
        @endphp
        <tr>
            <td class="c" style="color:#000000;">{{ $i + 1 }}</td>
            <td style="white-space:nowrap;">{{ $tx['date'] ? \Carbon\Carbon::parse($tx['date'])->format('d M Y') : '—' }}</td>
            <td>
                <strong>{{ $tx['particular'] ?? '—' }}</strong>
            </td>
            <td class="c">
                <span class="badge">{{ $tx['section'] ?? ($isReceipt ? 'Receipt' : 'Payment') }}</span>
            </td>
            <td class="c">{{ ucfirst($tx['payment_mode'] ?: 'Cash') }}</td>
            <td class="r" style="color:#000000; font-weight:{{ $isReceipt ? '700' : 'normal' }};">
                {{ $isReceipt ? formatIndianCurrency($amt) : '—' }}
            </td>
            <td class="r" style="color:#000000; font-weight:{{ !$isReceipt ? '700' : 'normal' }};">
                {{ !$isReceipt ? formatIndianCurrency($amt) : '—' }}
            </td>
            <td class="r" style="font-weight:700; color:#000000;">
                {{ formatIndianCurrency($runningBalance) }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="c" style="padding:24px; color:#000000;">No cash transactions recorded for the selected period.</td>
        </tr>
        @endforelse
    </tbody>
    @if(isset($allTransactions) && $allTransactions->count() > 0)
    <tfoot>
        <tr class="double-total">
            <td colspan="5" class="r">NET FLOW TOTALS:</td>
            <td class="r" style="color:#000000; font-size:10px;">{{ formatIndianCurrency($totalInflow ?? 0) }}</td>
            <td class="r" style="color:#000000; font-size:10px;">{{ formatIndianCurrency($totalOutflow ?? 0) }}</td>
            <td class="r" style="color:#000000; font-size:10.5px;">
                {{ formatIndianCurrency($netCashFlow ?? 0) }}
            </td>
        </tr>
    </tfoot>
    @endif
</table>

@php $hasCustomAuthBlock = true; @endphp
@include('admin.components.pdf-signature', [
    'leftRole' => 'Cashier / Accounts Officer',
    'rightRole' => 'Finance Manager / Authorized Signatory'
])

@include('admin.components.pdf-footer', ['note' => 'Official Cash & Bank Statement • Compiled in Accordance with Accounting Standard AS-3'])
</body>
</html>
