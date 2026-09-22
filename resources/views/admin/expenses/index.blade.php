@extends('admin.layouts.app')
@section('title', 'Expenses')
@section('page-title', 'Expense Management')
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
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px;
    border-radius: 10px; text-decoration: none !important; font-size: 13.5px; font-weight: 700;
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

.filter-bar {
    display: flex !important; gap: 10px !important; align-items: flex-end !important; margin-bottom: 24px !important;
    background: rgba(255, 255, 255, 0.04) !important; padding: 16px 18px !important;
    border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
    width: 100% !important; flex-wrap: wrap !important;
}

.filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1 1 140px; min-width: 130px; }
.filter-group.search-group { flex: 2 1 200px; min-width: 180px; }
.filter-label { font-size: 11px; font-weight: 800; color: #CBD5E1 !important; text-transform: uppercase; letter-spacing: 0.8px; }

.filter-control {
    width: 100% !important; padding: 9px 12px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important;
}
select.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.filter-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 9px 18px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    white-space: nowrap !important; align-self: flex-end; height: 38px;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset {
    color: #CBD5E1 !important; text-decoration: none; font-size: 13px; font-weight: 600;
    padding: 9px 12px; white-space: nowrap !important; transition: color .2s ease;
    align-self: flex-end; height: 38px; display: inline-flex; align-items: center; gap: 4px;
}
.btn-reset:hover { color: #FFFFFF !important; }

/* KPI Grid */
.kpi-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 14px; margin-bottom: 22px;
}
.kpi-card {
    border-radius: 16px; padding: 13px 16px; display: flex; align-items: center; gap: 12px;
    border: 1px solid rgba(255, 255, 255, 0.10);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25) !important;
    transition: all .25s ease;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: 0 10px 26px rgba(0, 0, 0, 0.35) !important; }
.kpi-icon {
    width: 38px; height: 38px; border-radius: 11px; display: flex; align-items: center;
    justify-content: center; font-size: 16px; flex-shrink: 0;
}
.kpi-label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #CBD5E1; margin-bottom: 2px; }
.kpi-value { font-size: 17px !important; font-weight: 800 !important; line-height: 1.25; letter-spacing: -0.2px; margin: 1px 0; color: #FFFFFF !important; text-shadow: 0 1px 6px rgba(0,0,0,0.45) !important; }

.kpi-total { background: linear-gradient(135deg, rgba(245, 158, 11, 0.08) 0%, rgba(16, 22, 34, 0.75) 100%) !important; border-color: rgba(245, 158, 11, 0.30) !important; }
.kpi-total .kpi-icon { background: rgba(245, 158, 11, 0.18); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); }

.kpi-direct { background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(16, 22, 34, 0.75) 100%) !important; border-color: rgba(59, 130, 246, 0.30) !important; }
.kpi-direct .kpi-icon { background: rgba(59, 130, 246, 0.18); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.35); }

.kpi-approved { background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 22, 34, 0.75) 100%) !important; border-color: rgba(16, 185, 129, 0.30) !important; }
.kpi-approved .kpi-icon { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); }

.kpi-pending { background: linear-gradient(135deg, rgba(239, 68, 68, 0.08) 0%, rgba(16, 22, 34, 0.75) 100%) !important; border-color: rgba(239, 68, 68, 0.30) !important; }
.kpi-pending .kpi-icon { background: rgba(239, 68, 68, 0.18); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.35); }

.table-container { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); }

.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 14px 16px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 14px 16px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
    white-space: nowrap !important;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.expense-title { font-weight: 700; color: #FFFFFF !important; }
.amount-col { font-weight: 800; color: #F87171 !important; font-size: 14.5px; }

.cat-chip { background: rgba(245, 158, 11, 0.15) !important; color: #FBBF24 !important; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.30) !important; display: inline-block; white-space: nowrap; }
.type-chip { background: rgba(99, 102, 241, 0.15) !important; color: #A5B4FC !important; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; border: 1px solid rgba(99, 102, 241, 0.30) !important; display: inline-block; white-space: nowrap; }
.mode-chip { background: rgba(255, 255, 255, 0.08) !important; color: #E2E8F0 !important; padding: 3px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 600; display: inline-block; border: 1px solid rgba(255, 255, 255, 0.10); white-space: nowrap !important; }
.ref-chip { background: rgba(59, 130, 246, 0.12) !important; color: #93C5FD !important; padding: 3px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 600; display: inline-block; border: 1px solid rgba(59, 130, 246, 0.25); white-space: nowrap !important; }

.status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.status-pending  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.status-approved { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.status-rejected { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.action-buttons-wrap { display: flex !important; gap: 6px !important; align-items: center !important; white-space: nowrap !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }

/* ── Expense Type Nav Tabs ── */
.expense-nav-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.expense-nav-tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 12px;
    background: rgba(16, 22, 34, 0.65);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #94A3B8;
    text-decoration: none !important;
    font-size: 13.5px;
    font-weight: 700;
    transition: all .25s ease;
}
.expense-nav-tab:hover {
    color: #FFFFFF;
    background: rgba(255, 255, 255, 0.10);
    transform: translateY(-1px);
}
.expense-nav-tab.active {
    background: linear-gradient(135deg, #2563EB, #1D4ED8) !important;
    border-color: #3B82F6 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 18px rgba(37, 99, 235, 0.40);
}
</style>

@php
    $moduleTitle = 'Expense Management';
    $moduleSubtitle = 'Track, filter and manage expenses.';
    if (isset($activeType)) {
        if ($activeType === 'Property') {
            $moduleTitle = 'Property Expenses';
            $moduleSubtitle = 'Track and manage property-related maintenance, documentation & development costs.';
        } elseif ($activeType === 'Project') {
            $moduleTitle = 'Project Expenses';
            $moduleSubtitle = 'Track and manage project-wise expenses, materials, contractors & development costs.';
        } elseif ($activeType === 'General') {
            $moduleTitle = 'General Expenses';
            $moduleSubtitle = 'Track office, electricity, stationery, staff & administrative expenses.';
        } elseif ($activeType === 'Rental') {
            $moduleTitle = 'Rental Expenses';
            $moduleSubtitle = 'Track rental property maintenance, repairs & tenant-related expenses.';
        } elseif ($activeType === 'Personal') {
            $moduleTitle = 'Personal Expenses';
            $moduleSubtitle = 'Track personal drawings and private expenses.';
        }
    }
@endphp

<div class="crud-header">
    <div class="crud-title">
        <h2>{{ $moduleTitle }}</h2>
        <p>{{ $moduleSubtitle }}</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('expenses.pdf', request()->query()) }}" target="_blank" class="btn-gold" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%) !important; border: 1px solid #F87171 !important; color: #FFFFFF !important; box-shadow: 0 4px 16px rgba(239, 68, 68, 0.40);">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('expenses.create', isset($activeType) ? ['type' => $activeType] : []) }}" class="btn-gold">
            <i class="fa-solid fa-plus"></i> Add {{ isset($activeType) ? $activeType : '' }} Expense
        </a>
    </div>
</div>

{{-- 5 Type Navigation Tabs --}}
<div class="expense-nav-tabs">
    <a href="{{ route('expenses.property') }}" class="expense-nav-tab {{ ($activeType ?? '') === 'Property' ? 'active' : '' }}">
        <i class="fa-solid fa-building"></i> Property Expenses
    </a>
    <a href="{{ route('expenses.project-wise') }}" class="expense-nav-tab {{ ($activeType ?? '') === 'Project' ? 'active' : '' }}">
        <i class="fa-solid fa-city"></i> Project Expenses
    </a>
    <a href="{{ route('expenses.general') }}" class="expense-nav-tab {{ ($activeType ?? '') === 'General' ? 'active' : '' }}">
        <i class="fa-solid fa-briefcase"></i> General Expenses
    </a>
    <a href="{{ route('expenses.rental') }}" class="expense-nav-tab {{ ($activeType ?? '') === 'Rental' ? 'active' : '' }}">
        <i class="fa-solid fa-house-user"></i> Rental Expenses
    </a>
    <a href="{{ route('expenses.personal') }}" class="expense-nav-tab {{ ($activeType ?? '') === 'Personal' ? 'active' : '' }}">
        <i class="fa-solid fa-user"></i> Personal Expenses
    </a>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="card-box">
    {{-- Dynamic Filter Bar --}}
    <form method="GET" action="{{ url()->current() }}" class="filter-bar">
        @if(isset($activeType))
            <input type="hidden" name="type" value="{{ $activeType }}">
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

        <div class="filter-group search-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="filter-control" placeholder="Title, payee, ref, account, notes...">
        </div>

        <div class="filter-group">
            <span class="filter-label">Category</span>
            <select name="filter_category" class="filter-control">
                <option value="">All Categories</option>
                @if(isset($activeType) && $activeType === 'Rental' && isset($rentalCategories))
                    @foreach($rentalCategories as $rc)
                        <option value="{{ $rc }}" {{ request('filter_category') == $rc ? 'selected' : '' }}>{{ $rc }}</option>
                    @endforeach
                @elseif(isset($categories))
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}" {{ request('filter_category') == $cat->name || request('filter_category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        @if(($activeType ?? '') !== 'Rental')
        <div class="filter-group">
            <span class="filter-label">Project</span>
            <select name="filter_project" class="filter-control">
                <option value="">All Projects</option>
                @foreach($projects as $proj)
                    <option value="{{ $proj->id }}" {{ request('filter_project') == $proj->id ? 'selected' : '' }}>
                        {{ $proj->project_name }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="filter-group">
            <span class="filter-label">{{ ($activeType ?? '') === 'Rental' ? 'Rental Property' : 'Property' }}</span>
            <select name="filter_property" class="filter-control">
                <option value="">All Properties</option>
                @foreach($properties as $prop)
                    <option value="{{ $prop->id }}" {{ request('filter_property') == $prop->id ? 'selected' : '' }}>
                        {{ $prop->property_name }}{{ $prop->unit_no ? ' (Unit '.$prop->unit_no.')' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(($activeType ?? '') === 'Rental')
        <div class="filter-group">
            <span class="filter-label">Tenant</span>
            <select name="filter_tenant" class="filter-control">
                <option value="">All Tenants</option>
                @if(isset($tenants))
                    @foreach($tenants as $t)
                        <option value="{{ $t->id }}" {{ request('filter_tenant') == $t->id ? 'selected' : '' }}>
                            {{ $t->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Recovery Status</span>
            <select name="filter_recovery_status" class="filter-control">
                <option value="">All Recovery</option>
                @foreach(['Pending', 'Recovered', 'Partially Recovered', 'Waived', 'Deducted from Deposit'] as $rs)
                    <option value="{{ $rs }}" {{ request('filter_recovery_status') == $rs ? 'selected' : '' }}>{{ $rs }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="filter-group">
            <span class="filter-label">Payment Mode</span>
            <select name="filter_mode" class="filter-control">
                <option value="">All Modes</option>
                @foreach($paymentModes as $pm)
                    @php $pName = is_object($pm) ? $pm->name : $pm; @endphp
                    <option value="{{ $pName }}" {{ request('filter_mode') == $pName ? 'selected' : '' }}>{{ $pName }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Approval Status</span>
            <select name="filter_status" class="filter-control">
                <option value="">All Status</option>
                @foreach(['Pending','Approved','Rejected'] as $s)
                    <option value="{{ $s }}" {{ request('filter_status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Expense Date</span>
            <input type="date" name="filter_date" value="{{ request('filter_date') }}" class="filter-control">
        </div>

        <div style="display: flex; gap: 6px; align-items: flex-end;">
            <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
            @if(request()->hasAny(['search','filter_project','filter_property','filter_rental','filter_tenant','filter_recovery_status','filter_recoverable','filter_category','filter_vendor','filter_mode','filter_status','filter_date','date_from','date_to','firm_id']))
                <a href="{{ url()->current() . (isset($activeType) ? '?type=' . $activeType : '') }}" class="btn-reset" title="Clear Filters"><i class="fa-solid fa-rotate-left"></i> Reset</a>
            @endif
        </div>
    </form>

    {{-- Total Filtered Amount KPI Summary --}}
    <div class="kpi-grid">
        <div class="kpi-card kpi-total">
            <div class="kpi-icon"><i class="fa-solid fa-indian-rupee-sign"></i></div>
            <div>
                <div class="kpi-label">Total {{ ($activeType ?? '') === 'Rental' ? 'Rental Expenses' : 'Expense' }}</div>
                <div class="kpi-value">₹{{ number_format($totalAmount, 2) }}</div>
            </div>
        </div>

        @if(($activeType ?? '') === 'Rental')
        <div class="kpi-card kpi-direct" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 22, 34, 0.75) 100%) !important; border-color: rgba(16, 185, 129, 0.30) !important;">
            <div class="kpi-icon" style="background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35);">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <div class="kpi-label">Tenant Recoverable</div>
                <div class="kpi-value">₹{{ number_format($recoverableTotal ?? 0, 2) }}</div>
            </div>
        </div>

        <div class="kpi-card kpi-approved">
            <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
            <div>
                <div class="kpi-label">Recovered Amount</div>
                <div class="kpi-value">₹{{ number_format($recoveredTotal ?? 0, 2) }}</div>
            </div>
        </div>

        <div class="kpi-card kpi-pending">
            <div class="kpi-icon"><i class="fa-solid fa-hourglass-half"></i></div>
            <div>
                <div class="kpi-label">Pending Recovery</div>
                <div class="kpi-value">₹{{ number_format($pendingRecoveryTotal ?? 0, 2) }}</div>
            </div>
        </div>
        @else
        <div class="kpi-card kpi-direct">
            <div class="kpi-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <div>
                <div class="kpi-label">Direct Expenses</div>
                <div class="kpi-value">₹{{ number_format($directExpensesTotal ?? 0, 2) }}</div>
            </div>
        </div>

        <div class="kpi-card kpi-approved">
            <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
            <div>
                <div class="kpi-label">Approved ({{ $expenses->total() }} Rec)</div>
                <div class="kpi-value">₹{{ number_format($approvedAmount ?? 0, 2) }}</div>
            </div>
        </div>

        <div class="kpi-card kpi-pending">
            <div class="kpi-icon"><i class="fa-solid fa-clock"></i></div>
            <div>
                <div class="kpi-label">Pending Approval</div>
                <div class="kpi-value">₹{{ number_format($pendingAmount ?? 0, 2) }}</div>
            </div>
        </div>
        @endif
    </div>

    {{-- Expenses Listing Table --}}
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Firm</th>
                    @if(($activeType ?? '') === 'Rental')
                        <th>Rental Property</th>
                        <th>Tenant / Agreement</th>
                        <th>Paid To / Payee</th>
                        <th>Amount (₹)</th>
                        <th>Tenant Recovery</th>
                    @else
                        <th>Project</th>
                        <th>Property</th>
                        <th>Paid To / Vendor</th>
                        <th>Amount (₹)</th>
                    @endif
                    <th>Payment Mode</th>
                    <th>Ref No</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center; width:180px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $key => $expense)
                <tr>
                    <td>{{ method_exists($expenses, 'firstItem') ? ($expenses->firstItem() + $key) : ($key + 1) }}</td>
                    <td style="color:#CBD5E1; font-weight:600;">
                        {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                    </td>
                    <td>
                        @if($expense->expense_category)
                            <span class="cat-chip">{{ $expense->expense_category }}</span>
                            @if($expense->expense_subcategory && $expense->expense_subcategory !== $expense->expense_category)
                                <div style="font-size:11px; color:#94A3B8; margin-top:2px;">{{ $expense->expense_subcategory }}</div>
                            @endif
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td><strong style="color:#FFFFFF !important;">{{ $expense->firm_names }}</strong></td>

                    @if(($activeType ?? '') === 'Rental')
                        {{-- Rental Property --}}
                        <td>
                            @php
                                $propObj = $expense->property ?? ($expense->rental?->property ?? ($expense->properties->first() ?? null));
                            @endphp
                            @if($propObj)
                                <div style="font-weight:700;font-size:13px;color:#FFFFFF;">{{ $propObj->property_name }}</div>
                                @if($propObj->unit_no)
                                    <div style="font-size:11px;color:#93C5FD;">Unit: {{ $propObj->unit_no }}</div>
                                @endif
                            @else
                                <span style="color:#94A3B8;">—</span>
                            @endif
                        </td>

                        {{-- Tenant / Agreement --}}
                        <td>
                            @php
                                $tObj = $expense->tenant ?? ($expense->rental?->tenant ?? null);
                                $rObj = $expense->rental;
                            @endphp
                            @if($tObj || $rObj)
                                @if($tObj)
                                    <div style="font-weight:700;font-size:13px;color:#60A5FA;">
                                        <i class="fa-solid fa-user" style="font-size:11px;margin-right:2px;"></i> {{ $tObj->name }}
                                    </div>
                                @elseif($rObj && $rObj->tenant_name)
                                    <div style="font-weight:700;font-size:13px;color:#60A5FA;">{{ $rObj->tenant_name }}</div>
                                @endif
                                @if($rObj)
                                    <a href="{{ route('rentals.show', $rObj->id) }}" style="font-size:11px;color:#A5B4FC;text-decoration:none;font-weight:600;" title="View Agreement">
                                        <i class="fa-solid fa-file-contract"></i> {{ $rObj->agreement_no ?: 'AGR-'.$rObj->id }}
                                    </a>
                                @endif
                            @else
                                <span style="color:#94A3B8;">Direct Property</span>
                            @endif
                        </td>

                        {{-- Paid To --}}
                        <td style="color:#CBD5E1;">
                            @if($expense->vendor)
                                <a href="{{ route('vendors.show', $expense->vendor_id) }}" style="color:#60A5FA; text-decoration:none; font-weight:700; display:inline-flex; align-items:center; gap:4px;" title="View Vendor Profile">
                                    <i class="fa-solid fa-building-user" style="font-size:11px;"></i> {{ $expense->vendor->name }}
                                </a>
                                @if($expense->paid_to && $expense->paid_to !== $expense->vendor->name)
                                    <div style="font-size:11px; color:#94A3B8;">({{ $expense->paid_to }})</div>
                                @endif
                            @else
                                {{ $expense->paid_to ?? '—' }}
                            @endif
                        </td>

                        <td class="amount-col">₹{{ number_format($expense->amount, 2) }}</td>

                        {{-- Tenant Recovery --}}
                        <td>
                            @if($expense->is_tenant_recoverable)
                                <div style="display:flex; flex-direction:column; gap:3px;">
                                    <span style="font-size:12px; font-weight:800; color:#34D399;">
                                        ₹{{ number_format($expense->recovery_amount ?: $expense->amount, 2) }}
                                    </span>
                                    @php
                                        $recSt = $expense->recovery_status ?: 'Pending';
                                        $recBadgeColor = match($recSt) {
                                            'Recovered' => 'rgba(16, 185, 129, 0.2); color:#34D399; border: 1px solid rgba(16, 185, 129, 0.35);',
                                            'Partially Recovered' => 'rgba(59, 130, 246, 0.2); color:#60A5FA; border: 1px solid rgba(59, 130, 246, 0.35);',
                                            'Deducted from Deposit' => 'rgba(168, 85, 247, 0.2); color:#C084FC; border: 1px solid rgba(168, 85, 247, 0.35);',
                                            'Waived' => 'rgba(148, 163, 184, 0.2); color:#94A3B8; border: 1px solid rgba(148, 163, 184, 0.35);',
                                            default => 'rgba(245, 158, 11, 0.2); color:#FBBF24; border: 1px solid rgba(245, 158, 11, 0.35);',
                                        };
                                    @endphp
                                    <span style="font-size:10px; font-weight:700; padding:2px 6px; border-radius:10px; display:inline-block; width:fit-content; background:{{ $recBadgeColor }}">
                                        {{ $recSt }}
                                    </span>
                                </div>
                            @else
                                <span style="color:#64748B; font-size:11.5px;">Not Recoverable</span>
                            @endif
                        </td>
                    @else
                        <td>
                            @if($expense->project ?? $expense->property?->project)
                                @php $pObj = $expense->project ?? $expense->property->project; @endphp
                                <div style="font-weight:700;font-size:13px;color:#60A5FA;">{{ $pObj->project_name }}</div>
                            @else
                                <span style="color:#94A3B8;">General / None</span>
                            @endif
                        </td>
                        <td>
                            @if($expense->relationLoaded('properties') && $expense->properties->isNotEmpty())
                                @foreach($expense->properties as $prop)
                                    <div style="font-weight:700;font-size:12.5px;color:#FFFFFF; margin-bottom: 2px;">
                                        {{ $prop->property_name }}
                                        @if($prop->unit_no)
                                            <span style="font-size:11px;color:#93C5FD;">(Unit {{ $prop->unit_no }})</span>
                                        @endif
                                    </div>
                                @endforeach
                            @elseif($expense->property)
                                <div style="font-weight:700;font-size:13px;color:#FFFFFF;">{{ $expense->property->property_name }}</div>
                                @if($expense->property->unit_no)
                                    <div style="font-size:11px;color:#93C5FD;">Unit: {{ $expense->property->unit_no }}</div>
                                @endif
                            @else
                                <span style="color:#94A3B8;">—</span>
                            @endif
                        </td>
                        <td style="color:#CBD5E1;">
                            @if($expense->vendor)
                                <a href="{{ route('vendors.show', $expense->vendor_id) }}" style="color:#60A5FA; text-decoration:none; font-weight:700; display:inline-flex; align-items:center; gap:4px;" title="View Vendor Profile">
                                    <i class="fa-solid fa-building-user" style="font-size:11px;"></i> {{ $expense->vendor->name }}
                                </a>
                                @if($expense->paid_to && $expense->paid_to !== $expense->vendor->name)
                                    <div style="font-size:11px; color:#94A3B8;">({{ $expense->paid_to }})</div>
                                @endif
                            @else
                                {{ $expense->paid_to ?? '—' }}
                            @endif
                        </td>
                        <td class="amount-col">₹{{ number_format($expense->amount, 2) }}</td>
                    @endif
                    <td>
                        @if($expense->payment_mode)
                            <span class="mode-chip">{{ $expense->payment_mode }}</span>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($expense->reference_no)
                            <span class="ref-chip">{{ $expense->reference_no }}</span>
                        @elseif($expense->bill_no)
                            <span class="ref-chip">Bill: {{ $expense->bill_no }}</span>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @php
                            $st = $expense->approval_status ?? 'Pending';
                            $stClass = strtolower($st);
                        @endphp
                        <span class="status-badge status-{{ $stClass }}">{{ $st }}</span>
                    </td>
                    <td style="text-align:center;">
                        <div class="action-buttons-wrap" style="justify-content:center;">
                            <a href="{{ route('expenses.detail-pdf', $expense->id) }}" target="_blank" class="btn-view" style="background: rgba(252,105,0,0.15) !important; color: #FF8A3D !important; border: 1px solid rgba(252,105,0,0.30) !important;" title="Print / PDF Voucher">
                                <i class="fa fa-file-pdf"></i> PDF
                            </a>
                            <a href="{{ route('expenses.show', $expense->id) }}" class="btn-view" title="View Details">
                                <i class="fa fa-eye"></i> View
                            </a>
                            <a href="{{ route('expenses.edit', $expense->id) }}" class="btn-edit" title="Edit Expense">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST"
                                  style="display:inline;" id="del-exp-{{ $expense->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete"
                                    onclick="confirmDelete({{ $expense->id }}, '{{ addslashes($expense->expense_title ?? $expense->expense_category) }}')" title="Delete Expense">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" align="center" style="padding:40px;color:#CBD5E1;">
                        <i class="fa-solid fa-receipt" style="font-size:28px;opacity:0.3;margin-bottom:8px;display:block;"></i>
                        No expense records found matching current filters.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($expenses, 'links'))
        <div class="pagination-wrapper">
            {{ $expenses->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id, title) {
    Swal.fire({
        title: 'Delete Expense?',
        html: 'Are you sure you want to delete <strong>' + title + '</strong>?<br><small style="color:#64748B;">This action cannot be undone.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa fa-trash"></i> Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: { popup: 'swal-exp-popup' }
    }).then(r => { if (r.isConfirmed) document.getElementById('del-exp-' + id).submit(); });
}
</script>
<style>.swal-exp-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection
