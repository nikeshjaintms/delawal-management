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

.header-buttons { display: flex; gap: 10px; flex-wrap: wrap; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px;
    border-radius: 10px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

.btn-emerald {
    background: #10B981 !important; color: #FFFFFF !important; padding: 10px 20px;
    border-radius: 10px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #34D399 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 16px rgba(16, 185, 129, 0.35);
}
.btn-emerald:hover { background: #059669 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(16, 185, 129, 0.50); }

/* Nature Tabs */
.nature-tabs-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 22px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    padding-bottom: 12px;
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
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
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
.nature-tab.active-all {
    background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%) !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.50), inset 0 1px 0 rgba(255, 255, 255, 0.30) !important;
}
.nature-tab.active-taken {
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50), inset 0 1px 0 rgba(255, 255, 255, 0.30) !important;
}
.nature-tab.active-given {
    background: linear-gradient(135deg, #059669 0%, #10B981 100%) !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.50), inset 0 1px 0 rgba(255, 255, 255, 0.30) !important;
}

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
.stat-icon.blue { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.stat-icon.green { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.stat-icon.gold { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.stat-icon.purple { background: rgba(168, 85, 247, 0.18) !important; color: #C084FC !important; border: 1px solid rgba(168, 85, 247, 0.35) !important; }

.stat-body .s-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px; }
.stat-body .s-value { font-size: 20px; font-weight: 800; color: #FFFFFF !important; }
.stat-body .s-sub { font-size: 11.5px; color: #CBD5E1; margin-top: 3px; font-weight: 600; }

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

/* Nature badge in table */
.nature-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap !important;
}
.nb-taken {
    background: rgba(59, 130, 246, 0.18) !important;
    color: #60A5FA !important;
    border: 1px solid rgba(59, 130, 246, 0.35) !important;
}
.nb-given {
    background: rgba(16, 185, 129, 0.18) !important;
    color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.35) !important;
}

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

.action-link.pay {
    padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700;
    text-decoration: none !important; display: inline-flex; align-items: center; gap: 5px;
    border: 1px solid rgba(16, 185, 129, 0.40) !important; color: #34D399 !important;
    background: rgba(16, 185, 129, 0.15) !important; transition: all .2s ease; cursor: pointer;
}
.action-link.pay:hover { background: rgba(16, 185, 129, 0.30) !important; color: #FFFFFF !important; transform: translateY(-1px); }

.action-link.paid {
    padding: 5px 10px; border-radius: 8px; font-size: 11.5px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 4px;
    border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important;
    background: rgba(16, 185, 129, 0.12) !important;
}

/* ── Quick Pay Modal ── */
.modal {
    display: none; position: fixed; inset: 0; width: 100vw; height: 100vh;
    background: rgba(8, 12, 22, 0.75); backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px); z-index: 9999;
    justify-content: center; align-items: center; padding: 20px;
}
.modal.active { display: flex; }
.modal-box {
    background: rgba(20, 27, 41, 0.95) !important;
    backdrop-filter: blur(28px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(28px) saturate(180%) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 20px !important; padding: 28px !important;
    max-width: 480px; width: 100%;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6) !important;
    color: #FFFFFF !important; animation: modalScaleIn 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
@keyframes modalScaleIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
.modal-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.10);
}
.modal-header h3 { font-size: 18px; font-weight: 800; color: #FFFFFF !important; margin: 0; display: flex; align-items: center; gap: 8px; }
.modal-close { background: none; border: none; font-size: 24px; color: #94A3B8; cursor: pointer; line-height: 1; }
.modal-close:hover { color: #FFFFFF; }
.form-group-modal { margin-bottom: 16px; }
.form-label-modal { display: block; font-size: 13px; font-weight: 700; color: #CBD5E1; margin-bottom: 6px; }
.form-label-modal span { color: #F87171; }
.form-control-modal {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.85);
    border: 1.5px solid rgba(255, 255, 255, 0.15); border-radius: 10px;
    font-size: 14px; color: #FFFFFF; outline: none; box-sizing: border-box;
}
.form-control-modal:focus { border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25); }
select.form-control-modal option { background: #101622; color: #FFFFFF; }

.btn-green-modal {
    background: #10B981 !important; color: #FFFFFF !important; padding: 11px 20px;
    border-radius: 10px; font-size: 14px; font-weight: 700; border: 1px solid #34D399 !important;
    cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    transition: all .25s ease; box-shadow: 0 4px 16px rgba(16, 185, 129, 0.35); flex: 1;
}
.btn-green-modal:hover { background: #059669 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.50); }

.btn-outline-modal {
    display: inline-flex; align-items: center; justify-content: center; padding: 10px 18px;
    background: rgba(255, 255, 255, 0.08) !important; color: #FFFFFF !important; font-size: 13.5px;
    font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px;
    cursor: pointer; transition: all .25s ease;
}
.btn-outline-modal:hover { background: rgba(255, 255, 255, 0.15) !important; }

.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Loan Management</h2>
        <p>Track all borrowed loans (Loan Taken) and lent loans (Loan Given).</p>
    </div>
    <div class="header-buttons">
        <a href="{{ route('loans.create') }}?nature=taken" class="btn-gold"><i class="fa-solid fa-hand-holding-dollar"></i> + Loan Taken</a>
        <a href="{{ route('loans.create') }}?nature=given" class="btn-emerald"><i class="fa-solid fa-handshake-angle"></i> + Give Loan</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

{{-- Category Nature Filter Tabs --}}
@php
    $currentNature = request('loan_nature', 'all');
@endphp
<div class="nature-tabs-bar">
    <a href="{{ route('loans.index', array_merge(request()->except('loan_nature', 'page'), ['loan_nature' => 'all'])) }}" class="nature-tab {{ $currentNature === 'all' || empty($currentNature) ? 'active-all' : '' }}">
        <i class="fa-solid fa-layer-group"></i> All Loans
    </a>
    <a href="{{ route('loans.index', array_merge(request()->except('loan_nature', 'page'), ['loan_nature' => 'taken'])) }}" class="nature-tab {{ $currentNature === 'taken' ? 'active-taken' : '' }}">
        <i class="fa-solid fa-hand-holding-dollar"></i> Loan Taken (Liability)
    </a>
    <a href="{{ route('loans.index', array_merge(request()->except('loan_nature', 'page'), ['loan_nature' => 'given'])) }}" class="nature-tab {{ $currentNature === 'given' ? 'active-given' : '' }}">
        <i class="fa-solid fa-handshake-angle"></i> Loan Given (Receivable)
    </a>
</div>

{{-- Stat Cards --}}
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-hand-holding-dollar"></i></div>
        <div class="stat-body">
            <div class="s-label">Loan Taken</div>
            <div class="s-value" style="color:#60A5FA !important;">₹{{ number_format($totalTaken ?? 0, 2) }}</div>
            <div class="s-sub">Pending to Pay: <span style="color:#F87171;">₹{{ number_format($pendingTaken ?? 0, 2) }}</span></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-handshake-angle"></i></div>
        <div class="stat-body">
            <div class="s-label">Loan Given</div>
            <div class="s-value" style="color:#34D399 !important;">₹{{ number_format($totalGiven ?? 0, 2) }}</div>
            <div class="s-sub">Pending to Receive: <span style="color:#FBBF24;">₹{{ number_format($pendingGiven ?? 0, 2) }}</span></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fa-solid fa-money-bill-transfer"></i></div>
        <div class="stat-body">
            <div class="s-label">Total Repaid / Collected</div>
            <div class="s-value" style="color:#FBBF24 !important;">₹{{ number_format($totalPaid ?? 0, 2) }}</div>
            <div class="s-sub">Total Paid Amount Across Loans</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fa-solid fa-vault"></i></div>
        <div class="stat-body">
            <div class="s-label">Total Portfolio Volume</div>
            <div class="s-value" style="color:#C084FC !important;">₹{{ number_format($totalLoan ?? 0, 2) }}</div>
            <div class="s-sub">Combined Active & Completed</div>
        </div>
    </div>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('loans.index') }}" class="filter-bar">
        @if(request('loan_nature') && request('loan_nature') !== 'all')
            <input type="hidden" name="loan_nature" value="{{ request('loan_nature') }}">
        @endif

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
            <input type="text" name="search" value="{{ request('search') }}" class="search-input @error('search') is-invalid @enderror" placeholder="Bank, person, customer, borrower...">
        </div>
        <div class="filter-group">
            <span class="filter-label">Loan Type</span>
            <select name="filter_loan_type" class="filter-control @error('filter_loan_type') is-invalid @enderror">
                <option value="">All Types</option>
                <option value="Business Loan" {{ request('filter_loan_type') == 'Business Loan' ? 'selected' : '' }}>Business Loan</option>
                <option value="Personal Loan" {{ request('filter_loan_type') == 'Personal Loan' ? 'selected' : '' }}>Personal Loan</option>
                <option value="Given to Customer" {{ request('filter_loan_type') == 'Given to Customer' ? 'selected' : '' }}>Given to Customer</option>
                <option value="Given to Person / Party" {{ request('filter_loan_type') == 'Given to Person / Party' ? 'selected' : '' }}>Given to Person / Party</option>
                <option value="Employee Loan" {{ request('filter_loan_type') == 'Employee Loan' ? 'selected' : '' }}>Employee Loan</option>
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
                    <th>Category</th>
                    <th>Firm</th>
                    <th>Lender / Borrower Party</th>
                    <th>Loan Type</th>
                    <th>Property / Project</th>
                    <th style="text-align:right;">Loan Amount</th>
                    <th style="text-align:right;">Pending</th>
                    <th style="text-align:right;">EMI / mo</th>
                    <th>Date</th>
                    <th>Progress</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:200px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $key => $loan)
                @php
                    $isGiven = $loan->isGiven();
                    $pct = $loan->loan_amount > 0 ? round(($loan->paid_amount / $loan->loan_amount) * 100) : 0;
                    $lsCls = 'ls-' . strtolower($loan->loan_status);
                @endphp
                <tr>
                    <td>{{ method_exists($loans, 'firstItem') ? ($loans->firstItem() + $key) : ($key + 1) }}</td>
                    <td>
                        @if($isGiven)
                            <span class="nature-badge nb-given"><i class="fa-solid fa-handshake-angle"></i> Loan Given</span>
                        @else
                            <span class="nature-badge nb-taken"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Taken</span>
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
                                <div style="font-size:11.5px;color:#94A3B8;">{{ $loan->mobile_number ?: ($loan->relationship ?? 'Borrower Party') }}</div>
                            @else
                                <span style="color:#94A3B8;">—</span>
                            @endif
                        @else
                            @if($loan->loan_type === 'Personal Loan' && $loan->person_name)
                                <strong style="color:#FFFFFF !important;">{{ $loan->person_name }}</strong>
                                <div style="font-size:11.5px;color:#94A3B8;">{{ $loan->relationship ?? 'Lender Person' }}</div>
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
                    <td>
                        <span class="type-chip">{{ $loan->loan_type }}</span>
                    </td>
                    <td>
                        @if($loan->property)
                            <span style="color:#CBD5E1;">{{ $loan->property->property_name }}</span>
                            @if($loan->property->project)
                                <div style="font-size:11px;color:#94A3B8;">Proj: {{ $loan->property->project->project_name }}</div>
                            @endif
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td style="text-align:right;font-weight:700;color:#FBBF24 !important;">₹{{ number_format($loan->loan_amount,2) }}</td>
                    <td style="text-align:right;font-weight:700;color:{{ $loan->pending_amount > 0 ? '#F87171' : '#34D399' }} !important;">
                        ₹{{ number_format($loan->pending_amount, 2) }}
                    </td>
                    <td style="text-align:right;color:#60A5FA !important;font-weight:700;">
                        @if($loan->has_emi && $loan->emi_amount)
                            ₹{{ number_format($loan->emi_amount,2) }}
                            <div style="font-size:11px;color:#94A3B8;font-weight:500;">{{ $loan->total_emi_months }} mos</div>
                        @else
                            <span style="color:#94A3B8;font-size:11.5px;font-weight:600;background:rgba(255,255,255,0.06);padding:3px 8px;border-radius:6px;">No EMI</span>
                        @endif
                    </td>
                    <td style="font-size:12.5px;white-space:nowrap;color:#CBD5E1;">{{ \Carbon\Carbon::parse($loan->loan_start_date)->format('d M Y') }}</td>
                    <td>
                        <div class="progress-wrap">
                            <div class="progress-bar" style="width:{{ $pct }}%;"></div>
                        </div>
                        <div style="font-size:11.5px;color:#94A3B8;margin-top:4px;font-weight:600;">{{ $pct }}% {{ $isGiven ? 'recovered' : 'paid' }}</div>
                    </td>
                    <td style="text-align:center;">
                        <span class="loan-status {{ $lsCls }}">{{ $loan->loan_status }}</span>
                    </td>
                    <td>
                        <div class="action-buttons-wrap">
                            <a href="{{ route('loans.show', $loan->id) }}" class="btn-view"><i class="fa fa-eye"></i> View</a>
                            @if($loan->has_emi && $loan->emiSchedules->count() > 0)
                                <a href="{{ route('loans.emi-schedule', $loan->id) }}" class="action-link emi"><i class="fa-solid fa-calendar-days"></i> EMI</a>
                            @else
                                @if($loan->pending_amount > 0)
                                    <button type="button" class="action-link pay" onclick="openIndexPayModal({{ $loan->id }}, '{{ addslashes($loan->party_display_name) }}', {{ (float)$loan->loan_amount }}, {{ (float)$loan->paid_amount }}, {{ (float)$loan->pending_amount }}, '{{ $loan->loan_nature }}')">
                                        <i class="fa-solid {{ $isGiven ? 'fa-hand-holding-dollar' : 'fa-money-bill-wave' }}"></i> {{ $isGiven ? 'Receive' : 'Pay' }}
                                    </button>
                                @else
                                    <span class="action-link paid"><i class="fa-solid fa-circle-check"></i> {{ $isGiven ? 'Collected' : 'Paid' }}</span>
                                @endif
                            @endif
                            <a href="{{ route('loans.edit', $loan->id) }}" class="btn-edit"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('loans.destroy', $loan->id) }}" method="POST" style="display:inline;" id="del-loan-{{ $loan->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" onclick="confirmDelete({{ $loan->id }},'{{ addslashes($loan->party_display_name) }}')">
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

{{-- Quick Payment Modal --}}
<div class="modal" id="indexPayModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-money-bill-wave" style="color:#34D399;"></i> <span id="modalHeaderTitle">Record Payment</span></h3>
            <button type="button" class="modal-close" onclick="closeIndexPayModal()">&times;</button>
        </div>
        <form method="POST" id="indexPayForm" action="">
            @csrf
            <div style="background:rgba(59,130,246,0.12);border:1px solid rgba(59,130,246,0.3);border-radius:12px;padding:14px 16px;margin-bottom:18px;">
                <div style="font-size:14px;font-weight:700;color:#FFFFFF;margin-bottom:8px;" id="modalLoanName">Loan Name</div>
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;color:#CBD5E1;margin-bottom:4px;">
                    <span>Total Loan Amount:</span>
                    <strong style="color:#FFFFFF;" id="modalTotalAmt">₹0.00</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;color:#CBD5E1;margin-bottom:4px;">
                    <span id="modalPaidLabel">Already Paid:</span>
                    <strong style="color:#34D399;" id="modalPaidAmt">₹0.00</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:13.5px;color:#CBD5E1;">
                    <span>Pending Balance:</span>
                    <strong style="color:#F87171;" id="modalPendingAmt">₹0.00</strong>
                </div>
            </div>

            <div class="form-group-modal">
                <label class="form-label-modal" id="modalInputAmountLabel">Payment Amount (₹) <span>*</span></label>
                <input type="number" step="0.01" name="paid_amount" id="modalInputPaidAmount" class="form-control-modal" placeholder="0.00" required>
            </div>

            <div class="form-group-modal">
                <label class="form-label-modal">Payment Date <span>*</span></label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-control-modal" required>
            </div>

            <div class="form-group-modal">
                <label class="form-label-modal">Payment Mode</label>
                <select name="payment_mode_id" class="form-control-modal">
                    <option value="">— Select Payment Mode —</option>
                    @if(isset($paymentModes))
                        @foreach($paymentModes as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="form-group-modal">
                <label class="form-label-modal">Reference / UTR / Cheque No.</label>
                <input type="text" name="reference_no" class="form-control-modal" placeholder="Optional reference number">
            </div>

            <div class="form-group-modal">
                <label class="form-label-modal">Remarks</label>
                <textarea name="remarks" class="form-control-modal" rows="2" placeholder="Optional notes..."></textarea>
            </div>

            <div style="display:flex;gap:12px;margin-top:22px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.1);">
                <button type="submit" class="btn-green-modal" id="modalSubmitBtn"><i class="fa-solid fa-check"></i> Submit Payment</button>
                <button type="button" class="btn-outline-modal" onclick="closeIndexPayModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id,name){
    Swal.fire({title:'Delete Loan?',html:'Delete loan for <strong>'+name+'</strong>?<br><small style="color:#64748B;">All EMI schedules and payment history will also be deleted.</small>',icon:'warning',showCancelButton:true,confirmButtonColor:'#EF4444',cancelButtonColor:'#64748B',confirmButtonText:'Yes, Delete',cancelButtonText:'Cancel',customClass:{popup:'swal-loan-popup'}})
    .then(r=>{if(r.isConfirmed)document.getElementById('del-loan-'+id).submit();});
}

function openIndexPayModal(loanId, loanName, totalAmt, paidAmt, pendingAmt, nature) {
    document.getElementById('indexPayForm').action = "{{ url('loans') }}/" + loanId + "/record-payment";
    document.getElementById('modalLoanName').innerText = loanName;
    document.getElementById('modalTotalAmt').innerText = '₹' + parseFloat(totalAmt).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('modalPaidAmt').innerText = '₹' + parseFloat(paidAmt).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('modalPendingAmt').innerText = '₹' + parseFloat(pendingAmt).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    const isGiven = (nature === 'given');
    document.getElementById('modalHeaderTitle').innerText = isGiven ? 'Record Repayment Received' : 'Record Loan Payment';
    document.getElementById('modalPaidLabel').innerText = isGiven ? 'Already Collected:' : 'Already Paid:';
    document.getElementById('modalInputAmountLabel').innerHTML = (isGiven ? 'Received Amount (₹)' : 'Payment Amount (₹)') + ' <span>*</span>';
    document.getElementById('modalSubmitBtn').innerHTML = isGiven ? '<i class="fa-solid fa-check"></i> Record Received Amount' : '<i class="fa-solid fa-check"></i> Submit Payment';

    const amountInput = document.getElementById('modalInputPaidAmount');
    amountInput.value = '';
    amountInput.max = pendingAmt;
    amountInput.placeholder = 'Max: ₹' + parseFloat(pendingAmt).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    document.getElementById('indexPayModal').classList.add('active');
}

function closeIndexPayModal() {
    document.getElementById('indexPayModal').classList.remove('active');
}

document.getElementById('indexPayModal').addEventListener('click', function(e) {
    if (e.target === this) closeIndexPayModal();
});
</script>
<style>.swal-loan-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection
