<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loans & Liabilities Accounting Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Loans & Liabilities Report',
    'orientation' => 'landscape',
    'backUrl' => route('loan-report.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Loans & Liabilities Accounting Statement',
    'subtitle' => 'Secured Borrowings, Bank Mortgages, EMI Schedules & Outstanding Debt Portfolio'
])

@php
    $totSanctioned = (float)$loans->sum('loan_amount');
    $totRepaid     = (float)$loans->sum('paid_amount');
    $totPending    = (float)$loans->sum('pending_amount');
@endphp

{{-- ── KPI Summary Cards ── --}}
<div class="stat-row">
    <div class="stat-box s-red">
        <div class="s-label">Total Sanctioned Debt</div>
        <div class="s-value">{{ formatIndianCurrency($totSanctioned) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Principal Repaid (Paid Off)</div>
        <div class="s-value">{{ formatIndianCurrency($totRepaid) }}</div>
    </div>
    <div class="stat-box s-amber">
        <div class="s-label">Total Outstanding Liability</div>
        <div class="s-value">{{ formatIndianCurrency($totPending) }}</div>
    </div>
    <div class="stat-box">
        <div class="s-label">Total Loan Accounts</div>
        <div class="s-value">{{ $loans->count() }} Accounts</div>
    </div>
</div>

{{-- ── 1. BANK-WISE DEBT EXPOSURE SUMMARY ── --}}
@if(isset($byBank) && count($byBank) > 0)
<div class="section-label">
    <i class="fa-solid fa-landmark" style="color:#2563EB;"></i> Lender / Bank Debt Exposure Breakdown
</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Lender / Bank Institution</th>
            <th class="r" style="width:140px;">Total Exposure (₹)</th>
            <th class="r" style="width:100px;">Portfolio Share</th>
        </tr>
    </thead>
    <tbody>
        @php $denom = $totSanctioned > 0 ? $totSanctioned : 1; $bIdx = 1; @endphp
        @foreach($byBank as $bName => $bAmt)
        <tr>
            <td class="c" style="color:#64748B;">{{ $bIdx++ }}</td>
            <td><strong>{{ $bName ?: 'Commercial Bank / NBFC' }}</strong></td>
            <td class="r" style="font-weight:700;">{{ formatIndianCurrency($bAmt) }}</td>
            <td class="r" style="color:#64748B;">{{ number_format(($bAmt / $denom) * 100, 1) }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ── 2. DETAILED LOAN ACCOUNT STATEMENT ── --}}
<div class="section-label" style="margin-top:14px;">
    <i class="fa-solid fa-file-invoice-dollar" style="color:#0F172A;"></i> Detailed Loans &amp; Mortgages Ledger
</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Lender / Bank</th>
            <th>Loan Type &amp; A/c</th>
            <th>Borrower / Property</th>
            <th style="width:70px;">Start Date</th>
            <th class="c" style="width:75px;">Interest &amp; Tenure</th>
            <th class="r" style="width:90px;">Monthly EMI (₹)</th>
            <th class="r" style="width:105px;">Sanctioned (₹)</th>
            <th class="r" style="width:100px;">Repaid (₹)</th>
            <th class="r" style="width:105px;">Outstanding (₹)</th>
            <th class="c" style="width:65px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($loans as $i => $l)
        @php
            $prop = $l->property?->property_name ?: ($l->customer?->name ?? '—');
            $st = strtolower($l->loan_status ?? 'active');
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td>
                <strong>{{ $l->bank_name ?? '—' }}</strong>
            </td>
            <td>
                <strong>{{ $l->loan_type ?? 'Term Loan' }}</strong>
                @if(!empty($l->account_no))
                    <div style="font-size:7.5px; color:#64748B;">A/c: {{ $l->account_no }}</div>
                @endif
            </td>
            <td>{{ $prop }}</td>
            <td style="white-space:nowrap;">{{ $l->loan_start_date ? \Carbon\Carbon::parse($l->loan_start_date)->format('d M Y') : '—' }}</td>
            <td class="c" style="font-size:8px;">
                {{ $l->interest_rate ?? '0' }}% p.a.
                @if($l->tenure_months)
                    <div style="color:#64748B;">{{ $l->tenure_months }} Mo</div>
                @endif
            </td>
            <td class="r" style="font-weight:600;">{{ formatIndianCurrency($l->emi_amount ?? 0) }}</td>
            <td class="r" style="font-weight:700;">{{ formatIndianCurrency($l->loan_amount ?? 0) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($l->paid_amount ?? 0) }}</td>
            <td class="r" style="color:#DC2626; font-weight:700;">{{ formatIndianCurrency($l->pending_amount ?? 0) }}</td>
            <td class="c">
                <span class="badge {{ $st === 'active' ? 'badge-danger' : ($st === 'closed' ? 'badge-success' : 'badge-warning') }}">
                    {{ ucfirst($l->loan_status ?? 'Active') }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="11" class="c" style="padding:24px; color:#64748B;">No loan or liability accounts found.</td>
        </tr>
        @endforelse
    </tbody>
    @if($loans->count() > 0)
    <tfoot>
        <tr class="double-total">
            <td colspan="7" class="r">TOTAL DEBT PORTFOLIO:</td>
            <td class="r" style="font-size:9.5px; font-weight:800;">{{ formatIndianCurrency($totSanctioned) }}</td>
            <td class="r" style="color:#059669; font-size:9.5px; font-weight:800;">{{ formatIndianCurrency($totRepaid) }}</td>
            <td class="r" style="color:#DC2626; font-size:10px; font-weight:900;">{{ formatIndianCurrency($totPending) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- ── Signatures Block ── --}}
<div class="auth-block" style="margin-top:20px; page-break-inside:avoid;">
    <div class="auth-col">
        <div class="auth-line">Finance &amp; Banking Officer</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Managing Director / Partner Approval</div>
    </div>
</div>

@include('admin.components.pdf-footer', ['note' => 'Statutory Loan Statement • Certified from Banking Sanction Letters & Amortization Records'])
</body>
</html>