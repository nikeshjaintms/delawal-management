@extends('admin.layouts.app')
@section('title','Loans')
@section('page-title','Loan Management')
@php
    $user = Auth::user();
    if (!$user && session('login_type') === 'firm' && session('firm_id')) {
        $authUser = new class {
            public function isAdmin()        { return true; }
            public function hasPermission($p){ return true; }
            public $role = null;
            public $name = '';
            public $firm_id = null;
        };
        $authUser->name = session('firm_name', 'Firm');
        $authUser->firm_id = session('firm_id');
    } else {
        $authUser = $user;
    }
@endphp
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 22px;
    border-radius: 10px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
}

.stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px; }
.stat-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 20px 22px;
    display: flex; align-items: center; gap: 16px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.30); transition: all .25s ease;
}
.stat-card:hover { transform: translateY(-3px); border-color: rgba(59, 130, 246, 0.40) !important; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45); }

.stat-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
.stat-icon.gold { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.stat-icon.blue { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.stat-icon.green { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.stat-icon.red { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.stat-body .s-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px; }
.stat-body .s-value { font-size: 20px; font-weight: 800; color: #FFFFFF !important; }

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
    min-width: 130px;
}
select.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.search-input { min-width: 200px; }
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

.loan-status { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; white-space: nowrap !important; }
.ls-active { background: rgba(34, 197, 94, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.ls-completed { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.ls-closed { background: rgba(148, 163, 184, 0.18) !important; color: #CBD5E1 !important; border: 1px solid rgba(148, 163, 184, 0.35) !important; }
.ls-cancelled { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.progress-wrap { width: 80px; background: rgba(255, 255, 255, 0.10); border-radius: 6px; height: 7px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); }
.progress-bar { height: 100%; background: linear-gradient(90deg, #3B82F6, #10B981); border-radius: 6px; }

.type-chip { background: rgba(255, 255, 255, 0.08) !important; color: #E2E8F0 !important; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.10); white-space: nowrap !important; }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }

.action-link.emi {
    padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700;
    text-decoration: none !important; display: inline-flex; align-items: center; gap: 5px;
    border: 1px solid rgba(99, 102, 241, 0.40) !important; color: #A5B4FC !important;
    background: rgba(99, 102, 241, 0.15) !important; transition: all .2s ease;
}
.action-link.emi:hover { background: rgba(99, 102, 241, 0.30) !important; color: #FFFFFF !important; }

.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Loan Management</h2>
        <p>Track all loans, EMI schedules, and repayments.</p>
    </div>
    <a href="{{ route('loans.create') }}" class="btn-gold"><i class="fa-solid fa-plus"></i> Add Loan</a>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

{{-- Stat Cards --}}
@php
    $firmId    = $authUser ? $authUser->firm_id : null;
    $allLoans  = \App\Models\Loan::where('firm_id',$firmId);
    $activeCount    = (clone $allLoans)->where('loan_status','Active')->count();
    $completedCount = (clone $allLoans)->where('loan_status','Completed')->count();
    $totalLoanAmt   = (clone $allLoans)->sum('loan_amount');
    $totalPendingAmt= (clone $allLoans)->where('loan_status','Active')->sum('pending_amount');
@endphp
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fa-solid fa-landmark"></i></div>
        <div class="stat-body"><div class="s-label">Total Loan Amount</div><div class="s-value" style="color:#FBBF24 !important;">₹{{ number_format($totalLoanAmt,2) }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-circle-play"></i></div>
        <div class="stat-body"><div class="s-label">Active Loans</div><div class="s-value">{{ $activeCount }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="stat-body"><div class="s-label">Completed</div><div class="s-value" style="color:#34D399 !important;">{{ $completedCount }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-hourglass-half"></i></div>
        <div class="stat-body"><div class="s-label">Pending Amount</div><div class="s-value" style="color:#F87171 !important;">₹{{ number_format($totalPendingAmt,2) }}</div></div>
    </div>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('loans.index') }}" class="filter-bar">
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
            <input type="text" name="search" value="{{ request('search') }}" class="search-input @error('search') is-invalid @enderror" placeholder="Bank, person, customer, property...">
        </div>
        <div class="filter-group">
            <span class="filter-label">Loan Type</span>
            <select name="filter_loan_type" class="filter-control @error('filter_loan_type') is-invalid @enderror">
                <option value="">All Types</option>
                <option value="Business Loan" {{ request('filter_loan_type') == 'Business Loan' ? 'selected' : '' }}>Business Loan</option>
                <option value="Personal Loan" {{ request('filter_loan_type') == 'Personal Loan' ? 'selected' : '' }}>Personal Loan</option>
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Customer</span>
            <select name="filter_customer" class="filter-control @error('filter_customer') is-invalid @enderror">
                <option value="">All Customers</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ request('filter_customer')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Property</span>
            <select name="filter_property" class="filter-control @error('filter_property') is-invalid @enderror">
                <option value="">All Properties</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}" {{ request('filter_property')==$p->id?'selected':'' }}>{{ $p->property_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">From Date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-control @error('from_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">To Date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-control @error('to_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">Status</span>
            <select name="filter_status" class="filter-control @error('filter_status') is-invalid @enderror">
                <option value="">All Status</option>
                @foreach(['Active','Completed','Closed','Cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('filter_status')==$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','filter_customer','filter_property','filter_status','firm_id','filter_loan_type','from_date','to_date']))
            <a href="{{ route('loans.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Firm</th>
                    <th>Loan Type</th>
                    <th>Bank / Person</th>
                    <th>Customer</th>
                    <th>Property</th>
                    <th style="text-align:right;">Loan Amount</th>
                    <th style="text-align:right;">EMI / mo</th>
                    <th>EMIs</th>
                    <th>Date</th>
                    <th>Progress</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:200px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $key => $loan)
                @php
                    $pct = $loan->loan_amount > 0 ? round(($loan->paid_amount / $loan->loan_amount) * 100) : 0;
                    $lsCls = 'ls-' . strtolower($loan->loan_status);
                @endphp
                <tr>
                    <td>{{ method_exists($loans, 'firstItem') ? ($loans->firstItem() + $key) : ($key + 1) }}</td>
                    <td><strong style="color:#FFFFFF !important;">{{ $loan->firm_names }}</strong></td>
                    <td>
                        <span class="type-chip">{{ $loan->loan_type }}</span>
                    </td>
                    <td>
                        @if($loan->loan_type === 'Personal Loan')
                            <strong style="color:#FFFFFF !important;">{{ $loan->person_name }}</strong>
                            <div style="font-size:11.5px;color:#94A3B8;">{{ $loan->relationship ?? 'Personal' }}</div>
                        @else
                            <strong style="color:#FFFFFF !important;">{{ $loan->bank_name }}</strong>
                        @endif
                    </td>
                    <td>
                        @if($loan->loan_type === 'Business Loan' && $loan->customer)
                            <div style="font-weight:700;font-size:13.5px;color:#FFFFFF !important;">{{ $loan->customer->name }}</div>
                            <div style="font-size:11.5px;color:#94A3B8;">{{ $loan->customer->mobile }}</div>
                        @else <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($loan->loan_type === 'Business Loan')
                            <span style="color:#CBD5E1;">{{ $loan->property?->property_name ?? '—' }}</span>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td style="text-align:right;font-weight:700;color:#FBBF24 !important;">₹{{ number_format($loan->loan_amount,2) }}</td>
                    <td style="text-align:right;color:#F87171 !important;font-weight:700;">
                        @if($loan->loan_type === 'Business Loan' && $loan->emi_amount)
                            ₹{{ number_format($loan->emi_amount,2) }}
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td style="font-size:12.5px;color:#CBD5E1;">
                        @if($loan->loan_type === 'Business Loan' && $loan->total_emi_months)
                            {{ $loan->total_emi_months }} mo
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td style="font-size:12.5px;white-space:nowrap;color:#CBD5E1;">{{ \Carbon\Carbon::parse($loan->loan_start_date)->format('d M Y') }}</td>
                    <td>
                        <div class="progress-wrap">
                            <div class="progress-bar" style="width:{{ $pct }}%;"></div>
                        </div>
                        <div style="font-size:11.5px;color:#94A3B8;margin-top:4px;font-weight:600;">{{ $pct }}% paid</div>
                    </td>
                    <td style="text-align:center;">
                        <span class="loan-status {{ $lsCls }}">{{ $loan->loan_status }}</span>
                    </td>
                    <td>
                        <div class="action-buttons-wrap">
                            <a href="{{ route('loans.show', $loan->id) }}" class="btn-view"><i class="fa fa-eye"></i> View</a>
                            @if($loan->loan_type === 'Business Loan')
                                <a href="{{ route('loans.emi-schedule', $loan->id) }}" class="action-link emi"><i class="fa-solid fa-calendar-days"></i> EMI</a>
                            @endif
                            <a href="{{ route('loans.edit', $loan->id) }}" class="btn-edit"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('loans.destroy', $loan->id) }}" method="POST" style="display:inline;" id="del-loan-{{ $loan->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" onclick="confirmDelete({{ $loan->id }},'{{ addslashes($loan->loan_type === 'Personal Loan' ? $loan->person_name : $loan->bank_name) }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" style="padding:40px;text-align:center;color:#CBD5E1;">
                        <i class="fa-solid fa-landmark" style="font-size:28px;opacity:0.3;display:block;margin-bottom:8px;"></i>
                        No loan records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($loans, 'links'))
        <div class="pagination-wrapper">{{ $loans->appends(request()->query())->links() }}</div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id,name){
    Swal.fire({title:'Delete Loan?',html:'Delete loan from <strong>'+name+'</strong>?<br><small style="color:#64748B;">All EMI schedules and history will also be deleted.</small>',icon:'warning',showCancelButton:true,confirmButtonColor:'#EF4444',cancelButtonColor:'#64748B',confirmButtonText:'Yes, Delete',cancelButtonText:'Cancel',customClass:{popup:'swal-loan-popup'}})
    .then(r=>{if(r.isConfirmed)document.getElementById('del-loan-'+id).submit();});
}
</script>
<style>.swal-loan-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection

