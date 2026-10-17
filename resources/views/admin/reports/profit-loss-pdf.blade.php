<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading & Profit and Loss Statement - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Profit & Loss Statement',
    'orientation' => 'portrait',
    'backUrl' => route('reports.profit-loss')
])

@include('admin.components.pdf-header', [
    'title' => 'Trading & Profit & Loss Statement',
    'subtitle' => 'Financial Performance Statement (Accrual & Cash Basis)'
])

{{-- ── KPI Financial Summary ── --}}
<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Total Revenue (A)</div>
        <div class="s-value">{{ formatIndianCurrency($totalRevenue ?? 0) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Cost of Sales (B)</div>
        <div class="s-value">{{ formatIndianCurrency($totalCostOfSales ?? 0) }}</div>
    </div>
    <div class="stat-box {{ ($grossProfit ?? 0) >= 0 ? 's-blue' : 's-red' }}">
        <div class="s-label">Gross Profit (A - B)</div>
        <div class="s-value">{{ formatIndianCurrency($grossProfit ?? 0) }}</div>
    </div>
    <div class="stat-box s-amber">
        <div class="s-label">Indirect Expenses (C)</div>
        <div class="s-value">{{ formatIndianCurrency($totalOperatingExpenses ?? 0) }}</div>
    </div>
    <div class="stat-box {{ ($netProfitLoss ?? 0) >= 0 ? 's-green' : 's-red' }}">
        <div class="s-label">Net Profit / (Loss)</div>
        <div class="s-value">{{ formatIndianCurrency($netProfitLoss ?? 0) }}</div>
    </div>
</div>

{{-- ── 1. TRADING ACCOUNT: REVENUE FROM OPERATIONS ── --}}
<div class="section-label">
    <i class="fa-solid fa-arrow-trend-up" style="color:#059669;"></i> Part I: Operating Revenue (Inflows &amp; Turnover)
</div>
<table>
    <thead>
        <tr>
            <th style="width:30px;" class="c">#</th>
            <th>Account / Revenue Particulars</th>
            <th class="c" style="width:110px;">Classification</th>
            <th class="r" style="width:140px;">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="c">1</td>
            <td><strong>Property Sales Turnover</strong> (Completed &amp; Active Conveyances)</td>
            <td class="c"><span class="badge">Operating Inflow</span></td>
            <td class="r" style="font-weight:700;">{{ formatIndianCurrency($propertySalesRevenue ?? 0) }}</td>
        </tr>
        <tr>
            <td class="c">2</td>
            <td><strong>Rental Income Collections</strong> (Tenancy Portfolio Receipts)</td>
            <td class="c"><span class="badge">Operating Inflow</span></td>
            <td class="r" style="font-weight:700;">{{ formatIndianCurrency($rentalRevenue ?? 0) }}</td>
        </tr>
        <tr>
            <td class="c">3</td>
            <td><strong>Other Business Receipts &amp; Incomes</strong> (Agricultural &amp; Misc Receipts)</td>
            <td class="c"><span class="badge">Ancillary Inflow</span></td>
            <td class="r" style="font-weight:700;">{{ formatIndianCurrency($otherIncome ?? 0) }}</td>
        </tr>
    </tbody>
    <tfoot>
        <tr style="background:#F1F5F9; font-weight:800;">
            <td colspan="3" class="r">TOTAL OPERATING REVENUE (A):</td>
            <td class="r" style="color:#059669; font-size:10px;">{{ formatIndianCurrency($totalRevenue ?? 0) }}</td>
        </tr>
    </tfoot>
</table>

{{-- ── 2. TRADING ACCOUNT: COST OF SALES (DIRECT COSTS) ── --}}
<div class="section-label">
    <i class="fa-solid fa-boxes-packing" style="color:#DC2626;"></i> Part II: Cost of Sales / Direct Project Costs (COGS)
</div>
<table>
    <thead>
        <tr>
            <th style="width:30px;" class="c">#</th>
            <th>Direct Cost Component</th>
            <th class="c" style="width:110px;">Classification</th>
            <th class="r" style="width:140px;">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="c">1</td>
            <td><strong>Property Acquisition Cost</strong> (Original Purchase / Land Cost of Sold Units)</td>
            <td class="c"><span class="badge">Direct Land Cost</span></td>
            <td class="r" style="font-weight:700;">{{ formatIndianCurrency($propertyPurchaseCost ?? 0) }}</td>
        </tr>
        <tr>
            <td class="c">2</td>
            <td><strong>Material Purchases &amp; Construction Consumption</strong> (Stock Inward &amp; Site Materials)</td>
            <td class="c"><span class="badge">Direct Material</span></td>
            <td class="r" style="font-weight:700;">{{ formatIndianCurrency($materialPurchaseCost ?? 0) }}</td>
        </tr>
    </tbody>
    <tfoot>
        <tr style="background:#F1F5F9; font-weight:800;">
            <td colspan="3" class="r">TOTAL COST OF SALES (B):</td>
            <td class="r" style="color:#DC2626; font-size:10px;">{{ formatIndianCurrency($totalCostOfSales ?? 0) }}</td>
        </tr>
        <tr class="double-total">
            <td colspan="3" class="r" style="font-size:9.5px;">GROSS PROFIT / (LOSS) (A − B): &nbsp;<span style="font-weight:600; font-size:8px; color:#475569;">(Gross Margin: {{ number_format($grossProfitMargin ?? 0, 2) }}%)</span></td>
            <td class="r" style="font-size:10.5px; color:{{ ($grossProfit ?? 0) >= 0 ? '#059669' : '#DC2626' }};">
                {{ formatIndianCurrency($grossProfit ?? 0) }}
            </td>
        </tr>
    </tfoot>
</table>

{{-- ── 3. PROFIT & LOSS: OPERATING & INDIRECT EXPENSES ── --}}
<div class="section-label">
    <i class="fa-solid fa-calculator" style="color:#D97706;"></i> Part III: Operating &amp; Indirect Expenses
</div>
<table>
    <thead>
        <tr>
            <th style="width:30px;" class="c">#</th>
            <th>Expense Particulars</th>
            <th class="c" style="width:110px;">Expense Type</th>
            <th class="r" style="width:140px;">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="c">1</td>
            <td><strong>Brokerage &amp; Commission Disbursals</strong> (Sales &amp; Deal Incentives)</td>
            <td class="c"><span class="badge">Selling Expense</span></td>
            <td class="r" style="font-weight:700;">{{ formatIndianCurrency($brokerCommissions ?? 0) }}</td>
        </tr>
        <tr>
            <td class="c">2</td>
            <td><strong>Administrative, Office &amp; Site Operations</strong> (Approved Vouchers)</td>
            <td class="c"><span class="badge">Administrative</span></td>
            <td class="r" style="font-weight:700;">{{ formatIndianCurrency($operatingExpenses ?? 0) }}</td>
        </tr>
        <tr>
            <td class="c">3</td>
            <td><strong>Finance Charges &amp; Loan EMI Outflows</strong> (Debt Service)</td>
            <td class="c"><span class="badge">Finance Charge</span></td>
            <td class="r" style="font-weight:700;">{{ formatIndianCurrency($loanEmiPaid ?? 0) }}</td>
        </tr>
    </tbody>
    <tfoot>
        <tr style="background:#F1F5F9; font-weight:800;">
            <td colspan="3" class="r">TOTAL OPERATING &amp; INDIRECT EXPENSES (C):</td>
            <td class="r" style="color:#DC2626; font-size:10px;">{{ formatIndianCurrency($totalOperatingExpenses ?? 0) }}</td>
        </tr>
    </tfoot>
</table>

{{-- ── 4. CATEGORY-WISE EXPENSE ANALYSIS ── --}}
@if(isset($expenseByCategory) && count($expenseByCategory) > 0)
<div class="section-label" style="margin-top:10px;">
    <i class="fa-solid fa-chart-pie" style="color:#475569;"></i> Operating Expense Ledger by Category
</div>
<table>
    <thead>
        <tr>
            <th style="width:30px;" class="c">#</th>
            <th>Category Name</th>
            <th class="r" style="width:140px;">Subtotal Amount (₹)</th>
            <th class="r" style="width:90px;">% of Total Expenses</th>
        </tr>
    </thead>
    <tbody>
        @php $totExp = $totalOperatingExpenses > 0 ? $totalOperatingExpenses : 1; @endphp
        @foreach($expenseByCategory as $ci => $cat)
        <tr>
            <td class="c">{{ $ci + 1 }}</td>
            <td><strong>{{ $cat->category ?: 'General Operational' }}</strong></td>
            <td class="r" style="font-weight:600;">{{ formatIndianCurrency($cat->total ?? 0) }}</td>
            <td class="r" style="color:#64748B;">{{ number_format((($cat->total ?? 0) / $totExp) * 100, 1) }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ── 5. NET PROFIT / (LOSS) SUMMARY ── --}}
<table style="margin-top:14px;">
    <tbody>
        <tr class="double-total">
            <td style="font-size:10.5px; padding:9px 12px;">
                <strong>NET PROFIT / (LOSS) FOR THE PERIOD (Gross Profit − Indirect Expenses):</strong>
                <div style="font-size:8px; font-weight:600; color:#475569; margin-top:2px;">
                    Net Margin: <strong>{{ number_format($netProfitMargin ?? 0, 2) }}%</strong> &nbsp;&bull;&nbsp;
                    Amount in Words: <em>{{ \App\Helpers\AccountingHelper::numberToWordsIndian($netProfitLoss ?? 0) }}</em>
                </div>
            </td>
            <td class="r" style="font-size:12px; font-weight:900; vertical-align:middle; width:160px; color:{{ ($netProfitLoss ?? 0) >= 0 ? '#059669' : '#DC2626' }};">
                {{ formatIndianCurrency($netProfitLoss ?? 0) }}
            </td>
        </tr>
    </tbody>
</table>

@php $hasCustomAuthBlock = true; @endphp
@include('admin.components.pdf-signature', [
    'leftRole' => 'Prepared By (Senior Accountant)',
    'rightRole' => 'Verified & Certified (Managing Partner / Director)'
])

@include('admin.components.pdf-footer', ['note' => 'Statutory Profit & Loss Account • Compliant with Indian Real Estate Accounting Standards'])
</body>
</html>