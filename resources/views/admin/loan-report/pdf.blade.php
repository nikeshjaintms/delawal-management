<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loan Report</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Segoe UI',Arial,sans-serif;font-size:11.5px;color:#0F1F35;background:#fff;padding:26px;}
        .rpt-header{display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:16px;margin-bottom:20px;border-bottom:2.5px solid #D4AF37;}
        .co-name{font-size:22px;font-weight:800;color:#0F1F35;}
        .co-sub{font-size:10px;color:#D4AF37;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-top:3px;}
        .rpt-meta{text-align:right;}
        .rpt-meta .rpt-title{font-size:15px;font-weight:700;color:#0F1F35;margin-bottom:3px;}
        .rpt-meta .rpt-date{font-size:11px;color:#64748B;}
        .stat-row{display:flex;gap:12px;margin-bottom:20px;}
        .stat-box{flex:1;border:1px solid #E5E7EB;border-radius:7px;padding:12px 14px;}
        .stat-box .s-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#64748B;}
        .stat-box .s-value{font-size:18px;font-weight:800;margin-top:3px;color:#0F1F35;}
        .stat-box.s-gold{border-color:rgba(212,175,55,0.3);background:rgba(212,175,55,0.05);}
        .stat-box.s-gold .s-value{color:#92710A;}
        .stat-box.s-green .s-value{color:#16803D;}
        .stat-box.s-red .s-value{color:#DC2626;}
        .section-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#D4AF37;margin-bottom:8px;margin-top:20px;padding-bottom:5px;border-bottom:1px solid #E5E7EB;}
        table{width:100%;border-collapse:collapse;font-size:10.5px;}
        thead tr{background:#0F1F35;}
        thead th{padding:8px 9px;color:#FFF;font-weight:600;text-align:left;font-size:9.5px;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;}
        thead th.r{text-align:right;}
        thead th.c{text-align:center;}
        tbody tr:nth-child(even){background:#F9FAFB;}
        tbody td{padding:7px 9px;border-bottom:1px solid #F1F5F9;vertical-align:middle;}
        tbody td.r{text-align:right;}
        tbody td.c{text-align:center;}
        tfoot tr{background:#F1F5F9;}
        tfoot td{padding:8px 9px;font-weight:800;border-top:2px solid #E5E7EB;}
        tfoot td.r{text-align:right;}
        .ls{display:inline-block;padding:2px 8px;border-radius:10px;font-size:9px;font-weight:700;}
        .ls-approved      {background:rgba(16,185,129,0.12);color:#065F46;}
        .ls-active        {background:rgba(16,185,129,0.12);color:#16803D;}
        .ls-pending       {background:rgba(245,158,11,0.12);color:#92400E;}
        .ls-rejected      {background:rgba(239,68,68,0.12);color:#991B1B;}
        .ls-under-process {background:rgba(99,102,241,0.12);color:#3730A3;}
        .ls-completed     {background:rgba(59,130,246,0.12);color:#1D4ED8;}
        .ls-closed        {background:rgba(100,116,139,0.12);color:#475569;}
        .ls-cancelled     {background:rgba(239,68,68,0.12);color:#DC2626;}
        .sum-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-top:20px;}
        .sum-table{width:100%;border-collapse:collapse;font-size:10.5px;}
        .sum-table th{padding:7px 9px;background:#F9FAFB;color:#64748B;font-weight:600;border-bottom:1px solid #E5E7EB;font-size:9.5px;text-transform:uppercase;}
        .sum-table td{padding:7px 9px;border-bottom:1px solid #F1F5F9;}
        .sum-table td.r{text-align:right;font-weight:700;color:#92710A;}
        .sum-table tfoot td{font-weight:800;border-top:1.5px solid #E5E7EB;}
        .sum-table tfoot td.r{color:#92710A;}
        .rpt-footer{margin-top:22px;padding-top:10px;border-top:1px solid #E5E7EB;display:flex;justify-content:space-between;color:#9CA3AF;font-size:10px;}
        @media print{body{padding:10px;}@page{margin:8mm;}}
    </style>
</head>
<body>

<div class="rpt-header">
    <div><div class="co-name">Delawala</div><div class="co-sub">Properties &amp; Management</div></div>
    <div class="rpt-meta"><div class="rpt-title">Loan Report</div><div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div></div>
</div>

@php
    $totalLoan    = $loans->sum('loan_amount');
    $totalPaid    = $loans->sum('paid_amount');
    $totalPending = $loans->sum('pending_amount');
@endphp

<div class="stat-row">
    <div class="stat-box s-gold"><div class="s-label">Total Loan Amount</div><div class="s-value">₹{{ number_format($totalLoan,2) }}</div></div>
    <div class="stat-box"><div class="s-label">Total Loans</div><div class="s-value">{{ $loans->count() }}</div></div>
    <div class="stat-box s-green"><div class="s-label">Total Paid</div><div class="s-value">₹{{ number_format($totalPaid,2) }}</div></div>
    <div class="stat-box s-red"><div class="s-label">Total Pending</div><div class="s-value">₹{{ number_format($totalPending,2) }}</div></div>
</div>

<div class="section-label">&#9632; Loan Records</div>
<table>
    <thead>
        <tr>
            <th style="width:20px;">#</th>
            <th>Loan Type</th>
            <th>Bank / Person</th>
            <th>Customer</th>
            <th>Property</th>
            <th class="r">Loan Amount</th>
            <th class="r">EMI/mo</th>
            <th>EMIs</th>
            <th>Start</th>
            <th class="r">Paid</th>
            <th class="r">Pending</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($loans as $i => $loan)
        <tr>
            <td style="color:#9CA3AF;">{{ $i+1 }}</td>
            <td>{{ $loan->loan_type }}</td>
            <td style="font-weight:600;">
                @if($loan->loan_type === 'Personal Loan')
                    {{ $loan->person_name }}
                @else
                    {{ $loan->bank_name }}
                @endif
            </td>
            <td>{{ $loan->customer?->name ?? '—' }}</td>
            <td>{{ $loan->property?->property_name ?? '—' }}</td>
            <td class="r" style="color:#92710A;font-weight:700;">₹{{ number_format($loan->loan_amount,2) }}</td>
            <td class="r" style="color:#B91C1C;">₹{{ number_format($loan->emi_amount ?? 0,2) }}</td>
            <td>{{ $loan->total_emi_months ?? '—' }}</td>
            <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($loan->loan_start_date)->format('d M Y') }}</td>
            <td class="r" style="color:#16803D;">₹{{ number_format($loan->paid_amount,2) }}</td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($loan->pending_amount,2) }}</td>
            <td class="c"><span class="ls ls-{{ strtolower(str_replace(' ', '-', $loan->loan_status)) }}">{{ $loan->loan_status }}</span></td>
        </tr>
        @empty
        <tr><td colspan="12" style="text-align:center;padding:20px;color:#64748B;">No records found.</td></tr>
        @endforelse
    </tbody>
    @if($loans->count() > 0)
    <tfoot>
        <tr>
            <td colspan="5">Total ({{ $loans->count() }} records)</td>
            <td class="r">₹{{ number_format($totalLoan,2) }}</td>
            <td colspan="3"></td>
            <td class="r" style="color:#16803D;">₹{{ number_format($totalPaid,2) }}</td>
            <td class="r" style="color:#DC2626;">₹{{ number_format($totalPending,2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

@if($loans->count() > 0)
<div class="sum-grid">
    <div>
        <div class="section-label" style="margin-top:14px;">&#9632; Bank-wise</div>
        <table class="sum-table">
            <thead><tr><th>Bank</th><th class="r">Amount</th></tr></thead>
            <tbody>
                @foreach($byBank as $bank => $amt)
                <tr><td>{{ $bank }}</td><td class="r">₹{{ number_format($amt,2) }}</td></tr>
                @endforeach
            </tbody>
            <tfoot><tr><td><strong>Total</strong></td><td class="r"><strong>₹{{ number_format($totalLoan,2) }}</strong></td></tr></tfoot>
        </table>
    </div>
    <div>
        <div class="section-label" style="margin-top:14px;">&#9632; Customer-wise</div>
        <table class="sum-table">
            <thead><tr><th>Customer</th><th class="r">Amount</th></tr></thead>
            <tbody>
                @foreach($byCustomer as $cust => $amt)
                <tr><td>{{ $cust }}</td><td class="r">₹{{ number_format($amt,2) }}</td></tr>
                @endforeach
            </tbody>
            <tfoot><tr><td><strong>Total</strong></td><td class="r"><strong>₹{{ number_format($totalLoan,2) }}</strong></td></tr></tfoot>
        </table>
    </div>
    <div>
        <div class="section-label" style="margin-top:14px;">&#9632; Type-wise</div>
        <table class="sum-table">
            <thead><tr><th>Type</th><th class="r">Amount</th></tr></thead>
            <tbody>
                @foreach($byType as $type => $amt)
                <tr><td>{{ $type }}</td><td class="r">₹{{ number_format($amt,2) }}</td></tr>
                @endforeach
            </tbody>
            <tfoot><tr><td><strong>Total</strong></td><td class="r"><strong>₹{{ number_format($totalLoan,2) }}</strong></td></tr></tfoot>
        </table>
    </div>
</div>
@endif

<div class="rpt-footer">
    <span>Delawala Management System — Loan Report</span>
    <span>{{ $loans->count() }} records · Total ₹{{ number_format($totalLoan,2) }} · {{ now()->format('d M Y') }}</span>
</div>

<script>window.onload=function(){window.print();}</script>
</body>
</html>
