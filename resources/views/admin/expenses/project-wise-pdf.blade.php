<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project-Wise Cost Accounting Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Project-Wise Cost Report',
    'orientation' => 'landscape',
    'backUrl' => route('expenses.project-wise')
])

@include('admin.components.pdf-header', [
    'title' => 'Project-Wise Cost Accounting & Site Expenditure Report',
    'subtitle' => 'Comprehensive Capital Outlay, Contractor Works, Material Inward & Land Acquisition Costing'
])

{{-- ── KPI Summary Cards ── --}}
<div class="stat-row">
    <div class="stat-box s-red">
        <div class="s-label">Total Portfolio Cost</div>
        <div class="s-value">{{ formatIndianCurrency($grandTotal ?? 0) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Direct Site Expenses</div>
        <div class="s-value">{{ formatIndianCurrency($grandDirectTotal ?? 0) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Material &amp; PO Purchases</div>
        <div class="s-value">{{ formatIndianCurrency($grandPoTotal ?? 0) }}</div>
    </div>
    <div class="stat-box s-amber">
        <div class="s-label">Contractor Works</div>
        <div class="s-value">{{ formatIndianCurrency($grandContractorTotal ?? 0) }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Land Acquisition Costs</div>
        <div class="s-value">{{ formatIndianCurrency($grandLandTotal ?? 0) }}</div>
    </div>
    <div class="stat-box">
        <div class="s-label">Brokerage &amp; Commissions</div>
        <div class="s-value">{{ formatIndianCurrency($grandBrokerTotal ?? 0) }}</div>
    </div>
</div>

<div class="section-label">
    <i class="fa-solid fa-city" style="color:#2563EB;"></i> Project Cost Allocation &amp; Head-Wise Expenditure Matrix
</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Project / Development Site</th>
            <th class="r" style="width:105px;">Direct Exp (₹)</th>
            <th class="r" style="width:105px;">Materials / PO (₹)</th>
            <th class="r" style="width:105px;">Contractors (₹)</th>
            <th class="r" style="width:100px;">Brokerage (₹)</th>
            <th class="r" style="width:110px;">Land Purchase (₹)</th>
            <th class="r" style="width:125px;">Total Project Cost (₹)</th>
            <th class="r" style="width:65px;">Cost %</th>
        </tr>
    </thead>
    <tbody>
        @php
            $portfolioGrand = ($grandTotal ?? 0) > 0 ? $grandTotal : 1;
        @endphp
        @forelse($projectsData ?? [] as $i => $item)
        @php
            $projName = $item['project']->project_name ?? ($item['project']->name ?? ('Project #' . ($item['project']->id ?? ($i+1))));
            $projCost = $item['totalCost'] ?? 0;
            $pct = round(($projCost / $portfolioGrand) * 100, 1);
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i + 1 }}</td>
            <td>
                <strong>{{ $projName }}</strong>
                @if(!empty($item['project']->location))
                    <div style="font-size:7.5px; color:#64748B;">Loc: {{ $item['project']->location }}</div>
                @endif
            </td>
            <td class="r">{{ formatIndianCurrency($item['directExpenses'] ?? 0) }}</td>
            <td class="r" style="color:#059669;">{{ formatIndianCurrency($item['poTotal'] ?? 0) }}</td>
            <td class="r" style="color:#B45309;">{{ formatIndianCurrency($item['contractorTotal'] ?? 0) }}</td>
            <td class="r">{{ formatIndianCurrency($item['brokerTotal'] ?? 0) }}</td>
            <td class="r" style="color:#7C3AED;">{{ formatIndianCurrency($item['landTotal'] ?? 0) }}</td>
            <td class="r" style="font-weight:800; color:#DC2626;">{{ formatIndianCurrency($projCost) }}</td>
            <td class="r" style="color:#64748B; font-weight:700;">{{ $pct }}%</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="c" style="padding:24px; color:#64748B;">No project-wise cost records found.</td>
        </tr>
        @endforelse

        {{-- Unallocated / Head Office General Costs --}}
        @if(($unallocatedTotal ?? 0) > 0)
        @php $unallocPct = round((($unallocatedTotal ?? 0) / $portfolioGrand) * 100, 1); @endphp
        <tr style="background:#FFFBEB;">
            <td class="c" style="color:#B45309;">•</td>
            <td>
                <strong>Unallocated / Corporate &amp; General Overheads</strong>
                <div style="font-size:7.5px; color:#64748B;">Administrative &amp; Unassigned Project Vouchers</div>
            </td>
            <td class="r">{{ formatIndianCurrency($unallocatedExpenses->sum('amount') ?? 0) }}</td>
            <td class="r">—</td>
            <td class="r">{{ formatIndianCurrency($unallocatedContractorTotal ?? 0) }}</td>
            <td class="r">{{ formatIndianCurrency($unallocatedBrokerTotal ?? 0) }}</td>
            <td class="r">{{ formatIndianCurrency($unallocatedLandTotal ?? 0) }}</td>
            <td class="r" style="font-weight:800; color:#B45309;">{{ formatIndianCurrency($unallocatedTotal ?? 0) }}</td>
            <td class="r" style="color:#64748B; font-weight:700;">{{ $unallocPct }}%</td>
        </tr>
        @endif
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="2" class="r">PORTFOLIO GRAND TOTALS:</td>
            <td class="r" style="font-weight:800;">{{ formatIndianCurrency($grandDirectTotal ?? 0) }}</td>
            <td class="r" style="color:#059669; font-weight:800;">{{ formatIndianCurrency($grandPoTotal ?? 0) }}</td>
            <td class="r" style="color:#B45309; font-weight:800;">{{ formatIndianCurrency($grandContractorTotal ?? 0) }}</td>
            <td class="r" style="font-weight:800;">{{ formatIndianCurrency($grandBrokerTotal ?? 0) }}</td>
            <td class="r" style="color:#7C3AED; font-weight:800;">{{ formatIndianCurrency($grandLandTotal ?? 0) }}</td>
            <td class="r" style="color:#DC2626; font-size:10px; font-weight:900;">{{ formatIndianCurrency($grandTotal ?? 0) }}</td>
            <td class="r" style="font-weight:800;">100.0%</td>
        </tr>
    </tfoot>
</table>

{{-- ── Signatures Block ── --}}
<div class="auth-block" style="margin-top:20px; page-break-inside:avoid;">
    <div class="auth-col">
        <div class="auth-line">Project Cost Accountant</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Chief Financial Officer / Managing Director</div>
    </div>
</div>

@include('admin.components.pdf-footer', ['note' => 'Project Cost Audit Report • Certified According to Project Accounting Systems'])
</body>
</html>