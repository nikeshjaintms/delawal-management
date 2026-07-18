@extends('admin.layouts.app')
@section('title','Loan Management')
@section('page-title','Loan Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:10px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:24px;box-shadow:var(--soft-shadow);}
    .stat-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;}
    .stat-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:20px;box-shadow:var(--soft-shadow);display:flex;align-items:center;gap:14px;}
    .stat-icon{width:46px;height:46px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0;}
    .stat-icon.gold{background:rgba(212,175,55,0.12);color:var(--gold);}
    .stat-icon.green{background:rgba(34,197,94,0.1);color:#16803D;}
    .stat-icon.red{background:rgba(239,68,68,0.1);color:#DC2626;}
    .stat-icon.blue{background:rgba(59,130,246,0.1);color:#1D4ED8;}
    .stat-body .s-label{font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:3px;}
    .stat-body .s-value{font-size:20px;font-weight:800;color:var(--text-primary);}
    .filter-bar{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;align-items:flex-end;}
    .filter-group{display:flex;flex-direction:column;gap:5px;}
    .filter-label{font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.6px;}
    .filter-control{padding:9px 12px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);color:var(--text-primary);outline:none;background:#FFF;transition:var(--transition);min-width:130px;}
    .filter-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .search-input{padding:9px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);outline:none;transition:var(--transition);min-width:210px;}
    .search-input:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .btn-search{background-color:var(--text-primary);color:#FFF;padding:9px 16px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:var(--font-primary);align-self:flex-end;}
    .btn-reset{padding:9px 10px;color:var(--text-secondary);text-decoration:none;font-size:13px;align-self:flex-end;}
    .table-container{width:100%;overflow-x:auto;}
    .premium-table{width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;}
    .premium-table th{padding:13px 14px;background:#F9FAFB;color:var(--text-secondary);font-weight:600;border-bottom:1px solid var(--border-color);font-size:11.5px;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;}
    .premium-table td{padding:14px;border-bottom:1px solid #F1F5F9;color:var(--text-primary);vertical-align:middle;}
    .premium-table tr:last-child td{border-bottom:none;}
    .premium-table tbody tr:hover{background:#F9FAFB;}
    .loan-status{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;}
    .ls-active{background:rgba(34,197,94,0.1);color:#16803D;}
    .ls-completed{background:rgba(59,130,246,0.1);color:#1D4ED8;}
    .ls-closed{background:rgba(100,116,139,0.1);color:#475569;}
    .ls-cancelled{background:rgba(239,68,68,0.1);color:#DC2626;}
    .progress-wrap{width:90px;background:#F1F5F9;border-radius:4px;height:6px;overflow:hidden;}
    .progress-bar{height:100%;border-radius:4px;background:var(--gold);}
    .action-links{display:flex;gap:8px;align-items:center;white-space:nowrap;}
    .action-link{color:var(--text-secondary);text-decoration:none;font-size:13px;transition:var(--transition);display:inline-flex;align-items:center;gap:4px;}
    .action-link.view:hover{color:#0EA5E9;}
    .action-link.edit:hover{color:var(--gold);}
    .action-link.emi:hover{color:#6366F1;}
    .action-link.delete-btn{background:none;border:none;cursor:pointer;color:var(--text-secondary);font-family:var(--font-primary);font-size:13px;padding:0;}
    .action-link.delete-btn:hover{color:#EF4444;}
    .alert-success{background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);color:#16803D;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px;}
    .pagination-wrapper{margin-top:24px;display:flex;justify-content:center;}
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
    $firmId    = Auth::user()->firm_id;
    $allLoans  = \App\Models\Loan::where('firm_id',$firmId);
    $activeCount    = (clone $allLoans)->where('loan_status','Active')->count();
    $completedCount = (clone $allLoans)->where('loan_status','Completed')->count();
    $totalLoanAmt   = (clone $allLoans)->sum('loan_amount');
    $totalPendingAmt= (clone $allLoans)->where('loan_status','Active')->sum('pending_amount');
@endphp
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fa-solid fa-landmark"></i></div>
        <div class="stat-body"><div class="s-label">Total Loan Amount</div><div class="s-value" style="color:var(--gold);">₹{{ number_format($totalLoanAmt,2) }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-circle-play"></i></div>
        <div class="stat-body"><div class="s-label">Active Loans</div><div class="s-value">{{ $activeCount }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="stat-body"><div class="s-label">Completed</div><div class="s-value" style="color:#16803D;">{{ $completedCount }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-hourglass-half"></i></div>
        <div class="stat-body"><div class="s-label">Pending Amount</div><div class="s-value" style="color:#DC2626;">₹{{ number_format($totalPendingAmt,2) }}</div></div>
    </div>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('loans.index') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input @error('search') is-invalid @enderror" placeholder="Bank, type, customer, property...">
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
            <span class="filter-label">Status</span>
            <select name="filter_status" class="filter-control @error('filter_status') is-invalid @enderror">
                <option value="">All Status</option>
                @foreach(['Active','Completed','Closed','Cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('filter_status')==$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','filter_customer','filter_property','filter_status']))
            <a href="{{ route('loans.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bank / Loan</th>
                    <th>Customer</th>
                    <th>Property</th>
                    <th style="text-align:right;">Loan Amount</th>
                    <th style="text-align:right;">EMI / mo</th>
                    <th>EMIs</th>
                    <th>Start</th>
                    <th>Progress</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:180px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $key => $loan)
                @php
                    $pct = $loan->loan_amount > 0 ? round(($loan->paid_amount / $loan->loan_amount) * 100) : 0;
                    $lsCls = 'ls-' . strtolower($loan->loan_status);
                @endphp
                <tr>
                    <td>{{ $loans->firstItem() + $key }}</td>
                    <td>
                        <div style="font-weight:700;">{{ $loan->bank_name }}</div>
                        <div style="font-size:11.5px;color:var(--text-secondary);">{{ $loan->loan_type }}</div>
                    </td>
                    <td>
                        @if($loan->customer)
                            <div style="font-weight:600;font-size:13px;">{{ $loan->customer->name }}</div>
                            <div style="font-size:11px;color:var(--text-secondary);">{{ $loan->customer->mobile }}</div>
                        @else <span style="color:var(--text-secondary);">—</span>
                        @endif
                    </td>
                    <td>{{ $loan->property?->property_name ?? '—' }}</td>
                    <td style="text-align:right;font-weight:700;">₹{{ number_format($loan->loan_amount,2) }}</td>
                    <td style="text-align:right;color:#B91C1C;font-weight:700;">₹{{ number_format($loan->emi_amount,2) }}</td>
                    <td style="font-size:12.5px;">{{ $loan->total_emi_months }} mo</td>
                    <td style="font-size:12.5px;white-space:nowrap;">{{ \Carbon\Carbon::parse($loan->loan_start_date)->format('d M Y') }}</td>
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
                        <div class="table-action-buttons">
                            <a href="{{ route('loans.show', $loan->id) }}" class="btn-view"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('loans.emi-schedule', $loan->id) }}" class="action-link emi"><i class="fa-solid fa-calendar-days"></i> EMI</a>
                            <a href="{{ route('loans.edit', $loan->id) }}" class="btn-edit"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('loans.destroy', $loan->id) }}" method="POST" style="display:inline;" id="del-loan-{{ $loan->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" onclick="confirmDelete({{ $loan->id }},'{{ addslashes($loan->bank_name) }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="padding:40px;text-align:center;color:var(--text-secondary);">
                        <i class="fa-solid fa-landmark" style="font-size:28px;opacity:0.25;display:block;margin-bottom:8px;"></i>
                        No loan records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $loans->appends(request()->query())->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id,name){
    Swal.fire({title:'Delete Loan?',html:'Delete loan from <strong>'+name+'</strong>?<br><small style="color:#64748B;">All EMI schedules will also be deleted.</small>',icon:'warning',showCancelButton:true,confirmButtonColor:'#EF4444',cancelButtonColor:'#64748B',confirmButtonText:'Yes, Delete',cancelButtonText:'Cancel',customClass:{popup:'swal-loan-popup'}})
    .then(r=>{if(r.isConfirmed)document.getElementById('del-loan-'+id).submit();});
}
</script>
<style>.swal-loan-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection

