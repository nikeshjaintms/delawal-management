<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit & Loss Statement - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Profit & Loss Statement',
    'orientation' => 'portrait',
    'backUrl' => route('reports.profit-loss')
])

@include('admin.components.pdf-header', [
    'title' => 'Profit & Loss Statement',
    'subtitle' => 'Comprehensive Financial P&L Statement'
])

<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Total Revenue / Inflows</div>
        <div class="s-value">₹{{ number_format($revenue ?? ($totalRevenue ?? 0), 2) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total Expenditure</div>
        <div class="s-value">₹{{ number_format($expenses ?? ($totalExpenses ?? 0), 2) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Net Profit / Surplus</div>
        <div class="s-value">₹{{ number_format($netProfit ?? (($revenue ?? 0) - ($expenses ?? 0)), 2) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-arrow-trend-up"></i> Inflows &amp; Revenue Breakdown</div>
<table>
    <thead>
        <tr>
            <th>Revenue Head</th>
            <th class="r">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Property Sales Realization</td>
            <td class="r">₹{{ number_format($salesRevenue ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Rental Income Collections</td>
            <td class="r">₹{{ number_format($rentalRevenue ?? 0, 2) }}</td>
        </tr>
        <tr style="background:#F1F5F9; font-weight:800;">
            <td>Total Operating Revenue</td>
            <td class="r" style="color:#059669;">₹{{ number_format($revenue ?? ($totalRevenue ?? 0), 2) }}</td>
        </tr>
    </tbody>
</table>

<div class="section-label"><i class="fa-solid fa-arrow-trend-down"></i> Outflows &amp; Expenditures</div>
<table>
    <thead>
        <tr>
            <th>Expense Head</th>
            <th class="r">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Project Site &amp; Material Costs</td>
            <td class="r">₹{{ number_format($totalCostOfSales ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Operating &amp; Administrative Expenses</td>
            <td class="r">₹{{ number_format($operatingExpenses ?? ($expenses ?? 0), 2) }}</td>
        </tr>
        <tr style="background:#F1F5F9; font-weight:800;">
            <td>Total Operating Outflows</td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($expenses ?? ($totalExpenses ?? 0), 2) }}</td>
        </tr>
    </tbody>
</table>

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Accountant Signature</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Authorized Signatory / Delawala Infra Co.</div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>