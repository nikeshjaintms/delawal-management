@extends('admin.layouts.app')
@section('title','EMI Schedules')
@section('page-title','EMI Schedules')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:24px;box-shadow:var(--soft-shadow);}
    .filter-bar{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;align-items:flex-end;}
    .filter-group{display:flex;flex-direction:column;gap:5px;}
    .filter-label{font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.6px;}
    .filter-control{padding:9px 12px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);color:var(--text-primary);outline:none;background:#FFF;transition:var(--transition);min-width:130px;}
    .filter-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .search-input{padding:9px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:var(--transition);min-width:200px;}
    .search-input:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .btn-search{background-color:var(--text-primary);color:#FFF;padding:9px 16px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:var(--font-primary);white-space:nowrap;align-self:flex-end;}
    .btn-search:hover{background-color:#1E293B;}
    .btn-reset{padding:9px 12px;color:var(--text-secondary);text-decoration:none;font-size:13px;font-weight:500;align-self:flex-end;}
    .btn-reset:hover{color:var(--text-primary);}
    .table-container{width:100%;overflow-x:auto;}
    .premium-table{width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;}
    .premium-table th{padding:13px 14px;background:#F9FAFB;color:var(--text-secondary);font-weight:600;border-bottom:1px solid var(--border-color);font-size:11.5px;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;}
    .premium-table td{padding:14px;border-bottom:1px solid #F1F5F9;color:var(--text-primary);vertical-align:middle;}
    .premium-table tr:last-child td{border-bottom:none;}
    .premium-table tbody tr:hover{background-color:#F9FAFB;}
    .progress-wrap{width:70px;background:#E2E8F0;border-radius:4px;height:6px;overflow:hidden;}
    .progress-bar{height:100%;background:var(--gold);border-radius:4px;}
    .action-link.emi{padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border-color);color:#4F46E5;background:#FFF;transition:var(--transition);}
    .action-link.emi:hover{background:rgba(79,70,229,0.04);border-color:rgba(79,70,229,0.3);}
    .loan-status{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.3px;}
    .ls-active{background:rgba(34,197,94,0.1);color:#16803D;}
    .ls-completed{background:rgba(59,130,246,0.1);color:#1D4ED8;}
    .ls-closed{background:rgba(100,116,139,0.1);color:#475569;}
    .ls-cancelled{background:rgba(239,68,68,0.1);color:#DC2626;}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>EMI Schedules</h2>
        <p>View month-wise EMI status and record payments for Business Loans.</p>
    </div>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('emi-schedules.index') }}" class="filter-bar">
        @if(auth()->user() && auth()->user()->isAdmin())
        <div class="filter-group">
            <span class="filter-label">Firm</span>
            <select name="firm_id" class="filter-control" onchange="this.form.submit()">
                <option value="">All Firms</option>
                @foreach($firms as $f)
                    <option value="{{ $f->id }}" {{ request('firm_id') == $f->id ? 'selected' : '' }}>{{ $f->firm_name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Bank, customer, property...">
        </div>
        <div class="filter-group">
            <span class="filter-label">Status</span>
            <select name="filter_status" class="filter-control">
                <option value="">All Status</option>
                @foreach(['Active','Completed','Closed','Cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('filter_status')==$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','filter_status','firm_id']))
            <a href="{{ route('emi-schedules.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Firm</th>
                    <th>Bank / Lender</th>
                    <th>Customer</th>
                    <th>Property</th>
                    <th style="text-align:right;">Loan Amount</th>
                    <th style="text-align:right;">EMI / Month</th>
                    <th>Duration</th>
                    <th>Paid Amount</th>
                    <th>Pending Amount</th>
                    <th>Progress</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:140px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $key => $loan)
                @php
                    $pct = $loan->loan_amount > 0 ? round(($loan->paid_amount / $loan->loan_amount) * 100) : 0;
                    $lsCls = 'ls-' . strtolower($loan->loan_status);
                @endphp
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td><strong style="color:#0F172A;">{{ $loan->firm_names }}</strong></td>
                    <td>
                        <div style="font-weight:700;">{{ $loan->bank_name }}</div>
                    </td>
                    <td>
                        @if($loan->customer)
                            <div style="font-weight:600;font-size:13px;">{{ $loan->customer->name }}</div>
                            <div style="font-size:11px;color:var(--text-secondary);">{{ $loan->customer->mobile }}</div>
                        @else
                            <span style="color:var(--text-secondary);">—</span>
                        @endif
                    </td>
                    <td>{{ $loan->property?->property_name ?? '—' }}</td>
                    <td style="text-align:right;font-weight:700;">₹{{ number_format($loan->loan_amount,2) }}</td>
                    <td style="text-align:right;color:#B91C1C;font-weight:700;">₹{{ number_format($loan->emi_amount,2) }}</td>
                    <td>{{ $loan->total_emi_months }} mo</td>
                    <td style="color:#16803D;font-weight:600;">₹{{ number_format($loan->paid_amount,2) }}</td>
                    <td style="color:#DC2626;font-weight:600;">₹{{ number_format($loan->pending_amount,2) }}</td>
                    <td>
                        <div class="progress-wrap">
                            <div class="progress-bar" style="width:{{ $pct }}%;"></div>
                        </div>
                        <div style="font-size:11px;color:var(--text-secondary);margin-top:2px;">{{ $pct }}% paid</div>
                    </td>
                    <td style="text-align:center;">
                        <span class="loan-status {{ $lsCls }}">{{ $loan->loan_status }}</span>
                    </td>
                    <td>
                        <a href="{{ route('loans.emi-schedule', $loan->id) }}" class="action-link emi"><i class="fa-solid fa-calendar-days"></i> EMI Schedule</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" style="padding:40px;text-align:center;color:var(--text-secondary);">
                        <i class="fa-solid fa-calendar-days" style="font-size:28px;opacity:0.25;display:block;margin-bottom:8px;"></i>
                        No Business Loans found for EMI Schedules.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
