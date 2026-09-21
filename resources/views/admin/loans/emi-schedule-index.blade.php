@extends('admin.layouts.app')
@section('title','EMI Schedules')
@section('page-title','EMI Schedules')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

/* ── Nature Tabs Bar (Premium Segmented Glass Control) ── */
.nature-tabs-bar {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(15, 23, 42, 0.75) !important;
    backdrop-filter: blur(24px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(24px) saturate(180%) !important;
    padding: 6px 8px;
    border-radius: 16px;
    border: 1.5px solid rgba(255, 255, 255, 0.14) !important;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.12);
    flex-wrap: wrap;
}
.nature-tab {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 10px 20px;
    border-radius: 12px;
    font-size: 13.5px;
    font-weight: 700;
    color: #CBD5E1 !important;
    text-decoration: none !important;
    background: transparent;
    border: 1px solid transparent;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    letter-spacing: 0.2px;
}
.nature-tab:hover {
    color: #FFFFFF !important;
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-1px);
}
.nature-tab i {
    font-size: 14px;
    transition: transform 0.2s ease;
}
.nature-tab:hover i {
    transform: scale(1.12);
}

/* Active State - All EMIs (Royal Indigo / Purple-Blue Gradient) */
.nature-tab.active-all {
    background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%) !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.50), inset 0 1px 0 rgba(255, 255, 255, 0.30) !important;
}

/* Active State - Loan Taken / Payable (Vibrant Electric Blue) */
.nature-tab.active-taken {
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50), inset 0 1px 0 rgba(255, 255, 255, 0.30) !important;
}

/* Active State - Loan Given / Receivable (Vibrant Emerald Green) */
.nature-tab.active-given {
    background: linear-gradient(135deg, #059669 0%, #10B981 100%) !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.50), inset 0 1px 0 rgba(255, 255, 255, 0.30) !important;
}

.tab-badge {
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 800;
    background: rgba(255, 255, 255, 0.16);
    color: #FFFFFF;
    border: 1px solid rgba(255, 255, 255, 0.15);
}
.nature-tab.active-all .tab-badge,
.nature-tab.active-taken .tab-badge,
.nature-tab.active-given .tab-badge {
    background: rgba(0, 0, 0, 0.25) !important;
    border-color: rgba(255, 255, 255, 0.25) !important;
    color: #FFFFFF !important;
}

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
}

.filter-bar {
    display: flex !important; gap: 12px !important; align-items: flex-end !important; margin-bottom: 24px !important;
    background: rgba(255, 255, 255, 0.04) !important; padding: 16px 20px !important;
    border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
    width: 100% !important; flex-wrap: nowrap !important; overflow-x: auto !important;
}

.filter-group { display: flex; flex-direction: column; gap: 6px; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; }

.filter-control, .search-input {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    min-width: 140px;
}
select.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.search-input { min-width: 220px; }
.search-input::placeholder { color: #94A3B8 !important; }
.filter-control:focus, .search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    white-space: nowrap !important; height: 42px;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px; white-space: nowrap !important; transition: color .2s ease; height: 42px; display: inline-flex; align-items: center; }
.btn-reset:hover { color: #FFFFFF !important; }

.table-container { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); }

.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 16px 18px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: 0.9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 16px 18px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
    white-space: nowrap !important;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.nature-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
    letter-spacing: 0.3px; white-space: nowrap !important;
}
.nature-badge.nb-taken {
    background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important;
    border: 1px solid rgba(59, 130, 246, 0.38) !important;
}
.nature-badge.nb-given {
    background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.38) !important;
}

.progress-wrap { width: 90px; background: rgba(255, 255, 255, 0.10); border-radius: 6px; height: 7px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); }
.progress-bar { height: 100%; background: linear-gradient(90deg, #3B82F6, #10B981); border-radius: 6px; }

.action-link.emi {
    padding: 8px 16px; border-radius: 10px; font-size: 12.5px; font-weight: 700;
    text-decoration: none !important; display: inline-flex; align-items: center; gap: 6px;
    border: 1px solid rgba(99, 102, 241, 0.40) !important; color: #A5B4FC !important;
    background: rgba(99, 102, 241, 0.15) !important; transition: all .2s ease;
    white-space: nowrap !important; box-shadow: 0 2px 10px rgba(99, 102, 241, 0.20);
}
.action-link.emi:hover {
    background: rgba(99, 102, 241, 0.30) !important; border-color: #6366F1 !important; color: #FFFFFF !important; transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.35);
}

.loan-status { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; white-space: nowrap !important; }
.ls-active { background: rgba(34, 197, 94, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.ls-completed { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.ls-closed { background: rgba(148, 163, 184, 0.18) !important; color: #CBD5E1 !important; border: 1px solid rgba(148, 163, 184, 0.35) !important; }
.ls-cancelled { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

@php
    $currentNature = request('loan_nature', 'taken');
@endphp

<div class="crud-header">
    <div class="crud-title">
        <h2>EMI Schedules</h2>
        <p>View month-wise EMI status and record payments for Loans with EMI schedules.</p>
    </div>

    {{-- Nature Switcher Tabs (2 Options: Loan Taken & Loan Given) --}}
    <div class="nature-tabs-bar">
        <a href="{{ route('emi-schedules.index', array_merge(request()->except('loan_nature', 'page'), ['loan_nature' => 'taken'])) }}" class="nature-tab {{ $currentNature === 'taken' ? 'active-taken' : '' }}">
            <i class="fa-solid fa-hand-holding-dollar"></i> Loan Taken (Payable) <span class="tab-badge">{{ $countTaken ?? 0 }}</span>
        </a>
        <a href="{{ route('emi-schedules.index', array_merge(request()->except('loan_nature', 'page'), ['loan_nature' => 'given'])) }}" class="nature-tab {{ $currentNature === 'given' ? 'active-given' : '' }}">
            <i class="fa-solid fa-handshake-angle"></i> Loan Given (Receivable) <span class="tab-badge">{{ $countGiven ?? 0 }}</span>
        </a>
    </div>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('emi-schedules.index') }}" class="filter-bar">
        <input type="hidden" name="loan_nature" value="{{ $currentNature }}">

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
            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Bank, person, customer, property...">
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
            <a href="{{ route('emi-schedules.index', ['loan_nature' => $currentNature]) }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nature</th>
                    <th>Firm</th>
                    <th>Lender / Borrower Party</th>
                    <th>Loan Type</th>
                    <th>Property</th>
                    <th style="text-align:right;">Loan Amount</th>
                    <th style="text-align:right;">EMI / Month</th>
                    <th>Duration</th>
                    <th style="text-align:right;">Paid</th>
                    <th style="text-align:right;">Pending</th>
                    <th>Progress</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:140px;text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $key => $loan)
                @php
                    $isGiven = ($loan->loan_nature === 'given');
                    $pct = $loan->loan_amount > 0 ? round(($loan->paid_amount / $loan->loan_amount) * 100) : 0;
                    $lsCls = 'ls-' . strtolower($loan->loan_status);
                @endphp
                <tr>
                    <td>{{ method_exists($loans, 'firstItem') ? ($loans->firstItem() + $key) : ($key + 1) }}</td>
                    <td>
                        @if($isGiven)
                            <span class="nature-badge nb-given"><i class="fa-solid fa-handshake-angle"></i> Given</span>
                        @else
                            <span class="nature-badge nb-taken"><i class="fa-solid fa-hand-holding-dollar"></i> Taken</span>
                        @endif
                    </td>
                    <td><strong style="color:#FFFFFF !important;">{{ $loan->firm_names }}</strong></td>
                    <td>
                        @if($isGiven)
                            @if($loan->customer)
                                <div style="font-weight:700;font-size:13.5px;color:#FFFFFF !important;">
                                    <i class="fa-solid fa-user-tag" style="color:#34D399;font-size:11px;margin-right:4px;"></i>{{ $loan->customer->name }}
                                </div>
                                <div style="font-size:11.5px;color:#94A3B8;">Customer: {{ $loan->customer->mobile }}</div>
                            @elseif($loan->person_name)
                                <div style="font-weight:700;font-size:13.5px;color:#FFFFFF !important;">{{ $loan->person_name }}</div>
                                <div style="font-size:11.5px;color:#94A3B8;">{{ $loan->mobile_number ?: ($loan->relationship ?? 'Borrower') }}</div>
                            @else
                                <span style="color:#94A3B8;">—</span>
                            @endif
                        @else
                            @if($loan->loan_type === 'Personal Loan' && $loan->person_name)
                                <strong style="color:#FFFFFF !important;">{{ $loan->person_name }}</strong>
                                <div style="font-size:11.5px;color:#94A3B8;">{{ $loan->relationship ?? 'Lender' }}</div>
                            @elseif($loan->bank_name)
                                <strong style="color:#FFFFFF !important;">{{ $loan->bank_name }}</strong>
                                @if($loan->customer)
                                    <div style="font-size:11.5px;color:#94A3B8;">Cust: {{ $loan->customer->name }}</div>
                                @endif
                            @else
                                <span style="color:#94A3B8;">—</span>
                            @endif
                        @endif
                    </td>
                    <td><span style="font-size:12.5px;color:#CBD5E1;">{{ $loan->loan_type }}</span></td>
                    <td style="color:#CBD5E1;">{{ $loan->property?->property_name ?? '—' }}</td>
                    <td style="text-align:right;font-weight:700;color:#FBBF24 !important;">₹{{ number_format($loan->loan_amount,2) }}</td>
                    <td style="text-align:right;color:{{ $isGiven ? '#34D399' : '#F87171' }} !important;font-weight:700;">₹{{ number_format($loan->emi_amount,2) }}</td>
                    <td style="color:#CBD5E1;">{{ $loan->total_emi_months }} mo</td>
                    <td style="text-align:right;color:#34D399 !important;font-weight:700;">₹{{ number_format($loan->paid_amount,2) }}</td>
                    <td style="text-align:right;color:#F87171 !important;font-weight:700;">₹{{ number_format($loan->pending_amount,2) }}</td>
                    <td>
                        <div class="progress-wrap">
                            <div class="progress-bar" style="width:{{ $pct }}%;"></div>
                        </div>
                        <div style="font-size:11.5px;color:#94A3B8;margin-top:4px;font-weight:600;">{{ $pct }}% {{ $isGiven ? 'recvd' : 'paid' }}</div>
                    </td>
                    <td style="text-align:center;">
                        <span class="loan-status {{ $lsCls }}">{{ $loan->loan_status }}</span>
                    </td>
                    <td style="text-align:center;">
                        <a href="{{ route('loans.emi-schedule', $loan->id) }}" class="action-link emi"><i class="fa-solid fa-calendar-days"></i> View Schedule</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14" style="padding:40px;text-align:center;color:#CBD5E1;">
                        <i class="fa-solid fa-calendar-days" style="font-size:28px;opacity:0.3;display:block;margin-bottom:8px;"></i>
                        No Loans with active EMI schedules found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($loans, 'links'))
        <div class="pagination-wrapper">
            {{ $loans->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
