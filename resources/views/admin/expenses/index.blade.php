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

.filter-bar {
    display: flex !important; gap: 12px !important; align-items: flex-end !important; margin-bottom: 24px !important;
    background: rgba(255, 255, 255, 0.04) !important; padding: 16px 20px !important;
    border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
    width: 100% !important; flex-wrap: nowrap !important; overflow-x: auto !important;
}

.filter-group { display: flex; flex-direction: column; gap: 6px; flex-shrink: 0; }
.filter-label { font-size: 11px; font-weight: 800; color: #CBD5E1 !important; text-transform: uppercase; letter-spacing: 0.8px; }

.filter-control {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important; min-width: 140px;
}
select.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.filter-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.search-input {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important; min-width: 200px;
}
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    flex-shrink: 0 !important; white-space: nowrap !important; align-self: flex-end;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px; flex-shrink: 0 !important; white-space: nowrap !important; transition: color .2s ease; align-self: flex-end; }
.btn-reset:hover { color: #FFFFFF !important; }

.total-bar {
    background: rgba(245, 158, 11, 0.12) !important;
    border: 1px solid rgba(245, 158, 11, 0.30) !important;
    border-radius: 16px !important; padding: 16px 22px !important;
    margin-bottom: 24px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
}
.total-bar .total-label { font-size: 13px; color: #CBD5E1 !important; font-weight: 600; }
.total-bar .total-amount { font-size: 22px; font-weight: 800; color: #FBBF24 !important; }
.total-bar .rec-count { font-size: 13px; color: #CBD5E1 !important; margin-left: auto; font-weight: 600; }

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

.expense-title { font-weight: 700; color: #FFFFFF !important; }
.amount-col { font-weight: 800; color: #F87171 !important; }

.cat-chip { background: rgba(245, 158, 11, 0.15) !important; color: #FBBF24 !important; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.30) !important; display: inline-block; white-space: nowrap; }
.mode-chip { background: rgba(255, 255, 255, 0.08) !important; color: #E2E8F0 !important; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block; border: 1px solid rgba(255, 255, 255, 0.10); white-space: nowrap !important; }
.bill-chip { background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; padding: 3px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 600; display: inline-block; border: 1px solid rgba(59, 130, 246, 0.30); white-space: nowrap !important; }

.status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.status-pending  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.status-approved { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.status-rejected { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Expense Management</h2>
        <p>Track and manage all firm-wise property expenses.</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('expenses.pdf', request()->query()) }}" target="_blank" class="btn-gold" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%) !important; border: 1px solid #F87171 !important; color: #FFFFFF !important; box-shadow: 0 4px 16px rgba(239, 68, 68, 0.40);">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('expenses.create') }}" class="btn-gold">
            <i class="fa-solid fa-plus"></i> Add Expense
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="card-box">
    {{-- Filters --}}
    <form method="GET" action="{{ route('expenses.index') }}" class="filter-bar">
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
            <input type="text" name="search" value="{{ request('search') }}"
                   class="search-input @error('search') is-invalid @enderror" placeholder="Title, paid to, bill no...">
        </div>
        <div class="filter-group">
            <span class="filter-label">Project</span>
            <select name="filter_project" class="filter-control @error('filter_project') is-invalid @enderror">
                <option value="">All Projects</option>
                @foreach($projects as $proj)
                    <option value="{{ $proj->id }}" {{ request('filter_project') == $proj->id ? 'selected' : '' }}>
                        {{ $proj->project_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Property</span>
            <select name="filter_property" class="filter-control @error('filter_property') is-invalid @enderror">
                <option value="">All Properties</option>
                @foreach($properties as $prop)
                    <option value="{{ $prop->id }}" {{ request('filter_property') == $prop->id ? 'selected' : '' }}>
                        {{ $prop->property_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Category</span>
            <select name="filter_category" class="filter-control @error('filter_category') is-invalid @enderror">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('filter_category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Vendor</span>
            <select name="filter_vendor" class="filter-control @error('filter_vendor') is-invalid @enderror">
                <option value="">All Vendors</option>
                @if(isset($vendors))
                    @foreach($vendors as $ven)
                        <option value="{{ $ven->id }}" {{ request('filter_vendor') == $ven->id ? 'selected' : '' }}>
                            {{ $ven->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Payment Mode</span>
            <select name="filter_mode" class="filter-control @error('filter_mode') is-invalid @enderror">
                <option value="">All Modes</option>
                @foreach(\App\Models\PaymentMode::where('status', 'active')->orderBy('name')->get() as $pm)
                    <option value="{{ $pm->name }}" {{ request('filter_mode') == $pm->name ? 'selected' : '' }}>{{ $pm->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Approval Status</span>
            <select name="filter_status" class="filter-control @error('filter_status') is-invalid @enderror">
                <option value="">All Status</option>
                @foreach(['Pending','Approved','Rejected'] as $s)
                    <option value="{{ $s }}" {{ request('filter_status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Date</span>
            <input type="date" name="filter_date" value="{{ request('filter_date') }}" class="filter-control @error('filter_date') is-invalid @enderror">
        </div>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','filter_project','filter_property','filter_category','filter_vendor','filter_mode','filter_status','filter_date','firm_id']))
            <a href="{{ route('expenses.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    {{-- Project / Overall Expense KPI Breakdown Strip --}}
    @if(isset($selectedProject) && $selectedProject)
        <div style="background: rgba(37, 99, 235, 0.12); border: 1.5px solid rgba(59, 130, 246, 0.35); border-radius: 16px; padding: 14px 20px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(37, 99, 235, 0.25); display: flex; align-items: center; justify-content: center; color: #60A5FA; font-size: 18px;">
                    <i class="fa-solid fa-city"></i>
                </div>
                <div>
                    <div style="font-size: 11px; font-weight: 800; color: #93C5FD; text-transform: uppercase; letter-spacing: 0.8px;">Viewing Project Expense Ledger</div>
                    <div style="font-size: 18px; font-weight: 800; color: #FFFFFF;">{{ $selectedProject->project_name }} ({{ $selectedProject->project_code }})</div>
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <a href="{{ route('projects.show', $selectedProject->id) }}" class="btn-gold" style="padding: 7px 14px; font-size: 12.5px; background: rgba(255, 255, 255, 0.08) !important; border-color: rgba(255, 255, 255, 0.20) !important;">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Project Details
                </a>
                <a href="{{ route('expenses.create', ['project_id' => $selectedProject->id]) }}" class="btn-gold" style="padding: 7px 14px; font-size: 12.5px;">
                    <i class="fa-solid fa-plus"></i> Add Project Expense
                </a>
            </div>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 14px; margin-bottom: 24px;">
        <div style="background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.30); border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(245, 158, 11, 0.20); color: #FBBF24; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                <i class="fa-solid fa-calculator"></i>
            </div>
            <div>
                <div style="font-size: 11px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Total Expenses</div>
                <div style="font-size: 20px; font-weight: 800; color: #FBBF24; line-height: 1.2; margin-top: 2px;">₹{{ number_format($totalAmount, 2) }}</div>
            </div>
        </div>

        <div style="background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.30); border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(59, 130, 246, 0.20); color: #60A5FA; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <div style="font-size: 11px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Direct Expenses</div>
                <div style="font-size: 20px; font-weight: 800; color: #60A5FA; line-height: 1.2; margin-top: 2px;">₹{{ number_format($directExpensesTotal ?? 0, 2) }}</div>
            </div>
        </div>

        <div style="background: rgba(139, 92, 246, 0.12); border: 1px solid rgba(139, 92, 246, 0.30); border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(139, 92, 246, 0.20); color: #A78BFA; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                <i class="fa-solid fa-cart-flatbed"></i>
            </div>
            <div>
                <div style="font-size: 11px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">PO Expenses</div>
                <div style="font-size: 20px; font-weight: 800; color: #A78BFA; line-height: 1.2; margin-top: 2px;">₹{{ number_format($poExpensesTotal ?? 0, 2) }}</div>
            </div>
        </div>

        <div style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.30); border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(16, 185, 129, 0.20); color: #34D399; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div style="font-size: 11px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Approved ({{ $expenses->total() }} Rec)</div>
                <div style="font-size: 20px; font-weight: 800; color: #34D399; line-height: 1.2; margin-top: 2px;">₹{{ number_format($approvedAmount ?? 0, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Firm</th>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Project / Property</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Vendor</th>
                    <th>Bill No</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:200px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $key => $expense)
                <tr>
                    <td>{{ method_exists($expenses, 'firstItem') ? ($expenses->firstItem() + $key) : ($key + 1) }}</td>
                    <td><strong style="color:#FFFFFF !important;">{{ $expense->firm_names }}</strong></td>
                    <td style="color:#CBD5E1;">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                    <td>
                        <div class="expense-title">{{ $expense->expense_title }}</div>
                        @if($expense->purchase_order_id && $expense->purchaseOrder)
                            <div style="margin-top: 4px;">
                                <a href="{{ route('purchase-orders.show', $expense->purchase_order_id) }}" 
                                   style="background: rgba(139, 92, 246, 0.20); color: #C4B5FD; border: 1px solid rgba(139, 92, 246, 0.40); padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;"
                                   title="View linked Purchase Order">
                                    <i class="fa-solid fa-file-invoice"></i> PO: {{ $expense->purchaseOrder->po_number }}
                                </a>
                            </div>
                        @endif
                        @if($expense->remarks)
                            <div style="font-size:11.5px;color:#94A3B8;margin-top:2px;">
                                {{ \Illuminate\Support\Str::limit($expense->remarks, 40) }}
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($expense->expense_category)
                            <span class="cat-chip">{{ $expense->expense_category }}</span>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($expense->property)
                            <div style="font-weight:700;font-size:13px;color:#FFFFFF;">{{ $expense->property->property_name }}</div>
                            @if($expense->property->unit_no)
                                <div style="font-size:11px;color:#60A5FA;font-weight:700;">Unit: {{ $expense->property->unit_no }}</div>
                            @endif
                            @if($expense->project ?? $expense->property->project)
                                @php $pName = ($expense->project ?? $expense->property->project)->project_name; @endphp
                                <div style="font-size:11px;color:#93C5FD;">{{ $pName }}</div>
                            @else
                                <div style="font-size:11px;color:#94A3B8;">[Direct Property]</div>
                            @endif
                        @elseif($expense->project)
                            <div style="font-weight:700;font-size:13px;color:#60A5FA;">{{ $expense->project->project_name }}</div>
                            <div style="font-size:11px;color:#CBD5E1;">[Project Expense]</div>
                        @else
                            <span style="color:#CBD5E1;">General</span>
                        @endif
                    </td>
                    <td class="amount-col">₹{{ number_format($expense->amount, 2) }}</td>
                    <td>
                        @if($expense->payment_mode)
                            <span class="mode-chip">{{ $expense->payment_mode }}</span>
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
                    <td>
                        @if($expense->bill_no)
                            <span class="bill-chip">{{ $expense->bill_no }}</span>
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
                    <td>
                        <div class="action-buttons-wrap">
                            <a href="{{ route('expenses.detail-pdf', $expense->id) }}" target="_blank" class="btn-view" style="background: rgba(252,105,0,0.15) !important; color: #FF8A3D !important; border: 1px solid rgba(252,105,0,0.30) !important;" title="Print / PDF Voucher">
                                <i class="fa fa-file-pdf"></i> PDF
                            </a>
                            <a href="{{ route('expenses.show', $expense->id) }}" class="btn-view">
                                <i class="fa fa-eye"></i> View
                            </a>
                            <a href="{{ route('expenses.edit', $expense->id) }}" class="btn-edit">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST"
                                  style="display:inline;" id="del-exp-{{ $expense->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete"
                                    onclick="confirmDelete({{ $expense->id }}, '{{ addslashes($expense->expense_title) }}')">
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
                        No expense records found.
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
        html: 'Delete <strong>' + title + '</strong>?<br><small style="color:#64748B;">This action cannot be undone.</small>',
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

