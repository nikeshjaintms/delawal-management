@extends('admin.layouts.app')
@section('title', 'Contractors')
@section('page-title', 'Contractor Master')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.btn-pc, .btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-pc:hover, .btn-primary-custom:hover {
    background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50);
}

.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 600 !important; margin: 0; }

/* ── KPI Summary Cards (All Payments & Totals Bar) ── */
.kpi-section {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;
}
@media(max-width: 1024px) { .kpi-section { grid-template-columns: repeat(2, 1fr); } }
@media(max-width: 540px) { .kpi-section { grid-template-columns: 1fr; } }

.kpi-card {
    background: rgba(20, 27, 41, 0.65) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 18px !important; padding: 18px 20px !important;
    display: flex; justify-content: space-between; align-items: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important; transition: all 0.25s ease;
    position: relative; overflow: hidden;
}
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 14px 34px rgba(0,0,0,0.35) !important; }

.kpi-info h4 {
    font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px;
}
.kpi-info p {
    font-size: 22px; font-weight: 800; color: #FFFFFF !important; margin: 0; line-height: 1.2;
}
.kpi-icon-box {
    width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;
}
.bg-light-green  { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); }
.bg-light-blue   { background: rgba(59, 130, 246, 0.18); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.35); }
.bg-light-red    { background: rgba(239, 68, 68, 0.18); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.35); }
.bg-light-orange { background: rgba(245, 158, 11, 0.18); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 24px;
}

.filter-bar {
    display: flex; justify-content: flex-start; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;
    background: rgba(255, 255, 255, 0.04) !important; padding: 14px 18px !important;
    border-radius: 14px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
}

.search-input, .filter-select {
    padding: 9px 13px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
}
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus, .filter-select:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }
select.filter-select option { background: #111827 !important; color: #FFFFFF !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 9px 16px;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    display: inline-flex; align-items: center; gap: 6px;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset {
    color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 700;
    padding: 9px 12px; transition: color .2s ease; display: inline-flex; align-items: center; gap: 6px;
}
.btn-reset:hover { color: #FFFFFF !important; }

/* Table System: Responsive & Compact to eliminate horizontal scrollbar */
.table-container {
    width: 100%;
    overflow-x: auto;
    background: rgba(16, 22, 34, 0.70) !important;
    border-radius: 18px;
    border: 1px solid rgba(255, 255, 255, 0.10);
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.20) transparent;
}
.table-container::-webkit-scrollbar { height: 6px; }
.table-container::-webkit-scrollbar-track { background: transparent; }
.table-container::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.20); border-radius: 6px; }

.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
.premium-table th {
    padding: 12px 14px; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap;
}
.premium-table td {
    padding: 12px 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    color: #FFFFFF !important; font-weight: 600; vertical-align: middle;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.035) !important; }

/* Badges */
.badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; font-size: 11px; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: .3px; white-space: nowrap; }
.badge-active   { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-paid     { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-partial  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-unpaid   { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-running  { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }

.proj-pill { display: inline-flex; align-items: center; gap: 5px; background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; font-size: 11.5px; font-weight: 700; border-radius: 6px; padding: 3px 8px; border: 1px solid rgba(59, 130, 246, 0.30); }
.id-chip   { display: inline-flex; align-items: center; gap: 4px; font-family: monospace; font-size: 11.5px; background: rgba(255, 255, 255, 0.06); padding: 2px 7px; border-radius: 6px; border: 1px solid rgba(255, 255, 255, 0.12); color: #E2E8F0; }

.btn-history-pill {
    display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700;
    background: rgba(16, 185, 129, 0.14); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.30);
    padding: 2px 8px; border-radius: 12px; cursor: pointer; transition: all .2s ease; margin-top: 4px;
    text-decoration: none;
}
.btn-history-pill:hover { background: rgba(16, 185, 129, 0.25); color: #FFFFFF; transform: translateY(-1px); }

/* Table Action Buttons: Streamlined & Compact */
.table-action-buttons { display: flex; gap: 5px; align-items: center; white-space: nowrap; justify-content: flex-end; }
.btn-pay-compact {
    display: inline-flex; align-items: center; justify-content: center; gap: 4px;
    padding: 5px 9px; min-height: 28px; background: rgba(16, 185, 129, 0.16) !important;
    color: #34D399 !important; font-size: 11.5px; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.35) !important;
    border-radius: 7px; text-decoration: none !important; transition: all .2s ease; cursor: pointer;
}
.btn-pay-compact:hover { background: #059669 !important; color: #FFFFFF !important; transform: translateY(-1px); }

.btn-act {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: 7px; text-decoration: none !important;
    transition: all .2s ease; cursor: pointer; font-size: 12px;
}
.btn-act-pdf   { background: rgba(252, 105, 0, 0.15) !important; color: #FB923C !important; border: 1px solid rgba(252, 105, 0, 0.30) !important; }
.btn-act-pdf:hover { background: #EA580C !important; color: #FFFFFF !important; transform: translateY(-1px); }
.btn-act-view  { background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.30) !important; }
.btn-act-view:hover { background: #2563EB !important; color: #FFFFFF !important; transform: translateY(-1px); }
.btn-act-edit  { background: rgba(245, 158, 11, 0.15) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.30) !important; }
.btn-act-edit:hover { background: #D97706 !important; color: #FFFFFF !important; transform: translateY(-1px); }
.btn-act-del   { background: rgba(239, 68, 68, 0.15) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.30) !important; }
.btn-act-del:hover { background: #DC2626 !important; color: #FFFFFF !important; transform: translateY(-1px); }

/* Alert Messages */
.alert-success {
    background: rgba(16, 185, 129, 0.16) !important;
    border: 1px solid rgba(16, 185, 129, 0.38) !important;
    color: #34D399 !important;
    border-radius: 12px; padding: 14px 18px; margin-bottom: 22px;
    font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 10px;
}
.pagination-wrap { margin-top: 24px; display: flex; justify-content: center; }

/* ── Modal: All Payments Quick View ── */
.payments-modal-backdrop {
    position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px);
    display: none; align-items: center; justify-content: center; z-index: 9999; padding: 16px;
}
.payments-modal-box {
    background: #111827; border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 20px; width: 100%; max-width: 760px; max-height: 85vh;
    display: flex; flex-direction: column; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.60);
    overflow: hidden; animation: popIn .2s ease-out;
}
@keyframes popIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.payments-modal-header {
    padding: 20px 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02);
}
.payments-modal-body {
    padding: 20px 24px; overflow-y: auto; flex: 1;
}
.payments-modal-footer {
    padding: 16px 24px; border-top: 1px solid rgba(255, 255, 255, 0.10);
    display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02);
}
.modal-stat-pill {
    padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700;
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Contractor Master</h2>
        <p>Manage project contractors, financial tracking, identity, and bank details.</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('contractors.pdf', request()->query()) }}" target="_blank" class="btn-pc" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%) !important; border: 1px solid #F87171 !important; color: #FFFFFF !important; box-shadow: 0 4px 16px rgba(239, 68, 68, 0.40);">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('contractors.create') }}" class="btn-pc">
            <i class="fa-solid fa-plus"></i> Add Contractor
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif

{{-- All Payments & Financial KPI Summary Bar --}}
<div class="kpi-section">
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Total Paid (All Payments)</h4>
            <p style="color: #34D399 !important;">₹{{ number_format($totalPaid ?? 0, 2) }}</p>
        </div>
        <div class="kpi-icon-box bg-light-green"><i class="fa-solid fa-money-bill-wave"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Total Contract Value</h4>
            <p style="color: #60A5FA !important;">₹{{ number_format($totalContract ?? 0, 2) }}</p>
        </div>
        <div class="kpi-icon-box bg-light-blue"><i class="fa-solid fa-file-contract"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Total Outstanding Due</h4>
            <p style="color: #F87171 !important;">₹{{ number_format($totalDue ?? 0, 2) }}</p>
        </div>
        <div class="kpi-icon-box bg-light-red"><i class="fa-solid fa-clock"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Active Contractors</h4>
            <p>{{ $totalContractors ?? $contractors->total() }}</p>
        </div>
        <div class="kpi-icon-box bg-light-orange"><i class="fa-solid fa-helmet-safety"></i></div>
    </div>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('contractors.index') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Search contractor name, phone, aadhar, pan, bank account..." style="flex: 1; min-width: 240px;">

        @if(auth()->user() && auth()->user()->isAdmin() && isset($firms) && $firms->count() > 0)
        <select name="firm_id" class="filter-select" onchange="this.form.submit()">
            <option value="">All Firms</option>
            @foreach($firms as $firm)
                <option value="{{ $firm->id }}" {{ request('firm_id') == $firm->id ? 'selected' : '' }}>
                    {{ $firm->firm_name }}
                </option>
            @endforeach
        </select>
        @endif

        <select name="project_id" class="filter-select" onchange="this.form.submit()">
            <option value="">All Projects</option>
            @foreach($projects as $proj)
                <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>
                    {{ $proj->project_name }}
                </option>
            @endforeach
        </select>

        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search', 'firm_id', 'project_id', 'status']))
            <a href="{{ route('contractors.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th style="width: 45px;">#</th>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <th>Firm</th>
                    @endif
                    <th>Project & Units</th>
                    <th>Contractor & Contact</th>
                    <th>Identity & Bank Details</th>
                    <th>Contract & Payments</th>
                    <th>Status</th>
                    <th style="text-align: right; width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($contractors as $i => $con)
                @php
                    $paymentsList = $con->payments ?? collect();
                    $payCount = $paymentsList->count();
                @endphp
                <tr>
                    <td style="color: #94A3B8;">{{ method_exists($contractors, 'firstItem') ? ($contractors->firstItem() + $i) : ($i + 1) }}</td>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <td><strong style="color: #FFFFFF;">{{ $con->firm->firm_name ?? '—' }}</strong></td>
                    @endif
                    <td>
                        @php
                            $assignedProjs = $con->relationLoaded('projects') && $con->projects->isNotEmpty()
                                ? $con->projects
                                : ($con->project ? collect([$con->project]) : collect());
                        @endphp
                        @if($assignedProjs->isNotEmpty())
                            <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 200px;">
                                @foreach($assignedProjs as $p)
                                    <span class="proj-pill">
                                        <i class="fa-solid fa-city" style="font-size: 10px;"></i>
                                        {{ $p->project_name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span style="color: #94A3B8;">—</span>
                        @endif

                        @if($con->properties && $con->properties->isNotEmpty())
                            <div style="margin-top: 4px; display: flex; flex-wrap: wrap; gap: 3px; max-width: 200px;">
                                @foreach($con->properties->take(2) as $pl)
                                    <span style="font-size: 9.5px; font-weight: 700; background: rgba(16, 185, 129, 0.15); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.3); padding: 1px 5px; border-radius: 4px;" title="{{ $pl->property_name }}">
                                        <i class="fa-solid fa-shapes" style="font-size: 8px;"></i> {{ $pl->property_name }}
                                    </span>
                                @endforeach
                                @if($con->properties->count() > 2)
                                    <span style="font-size: 9.5px; font-weight: 700; background: rgba(255,255,255,0.08); color: #CBD5E1; padding: 1px 4px; border-radius: 4px;">
                                        +{{ $con->properties->count() - 2 }} more
                                    </span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong style="color: #FFFFFF; font-size: 14px; display: block;">{{ $con->contractor_name }}</strong>
                        @if($con->mobile)
                            <div style="font-size: 12px; color: #60A5FA; margin-top: 2px;">
                                <i class="fa-solid fa-phone" style="font-size: 10px; margin-right: 3px;"></i> {{ $con->mobile }}
                            </div>
                        @endif
                        @if($con->address)
                            <div style="font-size: 11px; color: #94A3B8; margin-top: 2px; max-width: 190px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $con->address }}">
                                <i class="fa-solid fa-location-dot" style="font-size: 10px;"></i> {{ $con->address }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 3px;">
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                @if($con->pan_no)
                                    <span class="id-chip" title="PAN Card"><i class="fa-solid fa-receipt" style="color: #34D399; font-size: 10px;"></i> {{ $con->pan_no }}</span>
                                @endif
                                @if($con->aadhar_no)
                                    <span class="id-chip" title="Aadhar Card"><i class="fa-solid fa-address-card" style="color: #FBBF24; font-size: 10px;"></i> {{ $con->aadhar_no }}</span>
                                @endif
                            </div>
                            @if($con->bank_name || $con->account_number)
                                <div style="font-size: 11.5px; color: #E2E8F0; margin-top: 2px;">
                                    <strong>{{ $con->bank_name ?: 'Bank' }}</strong>
                                    @if($con->account_number)
                                        <span style="font-size: 11px; color: #94A3B8; font-family: monospace;"> ({{ substr($con->account_number, -4) ? '••••' . substr($con->account_number, -4) : $con->account_number }})</span>
                                    @endif
                                </div>
                            @elseif(!$con->pan_no && !$con->aadhar_no)
                                <span style="color: #94A3B8; font-size: 12px;">—</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($con->contract_amount > 0)
                            <div style="font-size: 12.5px; font-weight: 700; color: #FFFFFF;">
                                Contract: <span style="color: #60A5FA;">₹{{ number_format($con->contract_amount, 2) }}</span>
                                @if($con->due_amount > 0)
                                    <span style="color: #F87171; font-size: 11px; font-weight: 700; margin-left: 4px;">Due: ₹{{ number_format($con->due_amount, 2) }}</span>
                                @endif
                            </div>
                        @endif

                        @if($con->paid_amount > 0 || $payCount > 0)
                            <div style="font-size: 13px; font-weight: 800; color: #34D399; margin-top: 2px;">
                                Total Paid: ₹{{ number_format($con->paid_amount, 2) }}
                            </div>
                        @endif

                        {{-- Direct listing of all payments made (pela 100, pachi 2000, etc.) --}}
                        @if($payCount > 0)
                            <div style="margin-top: 5px; display: flex; flex-direction: column; gap: 3px; max-width: 240px; max-height: 130px; overflow-y: auto; padding-right: 2px;">
                                @foreach($paymentsList->sortBy('payment_date')->values() as $idx => $pay)
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; background: rgba(16, 185, 129, 0.09); border: 1px solid rgba(16, 185, 129, 0.22); border-radius: 6px; padding: 2.5px 7px; font-size: 11px;" title="{{ $pay->payment_type ? $pay->payment_type . ($pay->reference_no ? ' - ' . $pay->reference_no : '') : '' }}">
                                        <div style="display: flex; align-items: center; gap: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <span style="color: #94A3B8; font-size: 9.5px; font-weight: 800;">#{{ $idx + 1 }}</span>
                                            <span style="color: #93C5FD; font-size: 10.5px; font-weight: 600;">
                                                {{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('d M') : '—' }}
                                            </span>
                                            @if($pay->payment_mode)
                                                <span style="color: #E2E8F0; font-size: 9.5px; background: rgba(255,255,255,0.08); padding: 0 4px; border-radius: 3px;">
                                                    {{ $pay->payment_mode }}
                                                </span>
                                            @endif
                                        </div>
                                        <strong style="color: #34D399; font-size: 11.5px; font-weight: 800; white-space: nowrap;">
                                            ₹{{ number_format($pay->amount, 2) }}
                                        </strong>
                                    </div>
                                @endforeach
                            </div>
                            <div style="margin-top: 4px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                <span class="badge badge-{{ $con->contract_amount > 0 ? ($con->payment_status ?? 'paid') : 'running' }}">
                                    {{ $con->contract_amount > 0 ? ucfirst($con->payment_status ?? 'paid') : 'Paid Direct' }}
                                </span>
                                <span style="font-size: 10.5px; color: #94A3B8; font-weight: 600;">({{ $payCount }} {{ Str::plural('payment', $payCount) }})</span>
                            </div>
                        @elseif($con->contract_amount > 0)
                            <div style="margin-top: 4px;">
                                <span class="badge badge-unpaid">Unpaid</span>
                            </div>
                        @else
                            <div style="color: #64748B; font-size: 12px; font-style: italic;">No contract / payments</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $con->status }}">
                            <i class="fa-solid fa-circle" style="font-size: 6px;"></i> {{ ucfirst($con->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="table-action-buttons">
                            <a href="{{ route('contractors.show', $con) }}" class="btn-pay-compact" title="Record / Manage Payments">
                                <i class="fa-solid fa-money-bill-wave"></i> Pay
                            </a>
                            <a href="{{ route('contractors.detail-pdf', $con) }}" target="_blank" class="btn-act btn-act-pdf" title="Print Dossier PDF">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                            <a href="{{ route('contractors.show', $con) }}" class="btn-act btn-act-view" title="View Full Profile">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('contractors.edit', $con) }}" class="btn-act btn-act-edit" title="Edit Contractor">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form method="POST" action="{{ route('contractors.destroy', $con) }}" onsubmit="return confirm('Are you sure you want to delete contractor \'{{ $con->contractor_name }}\'?')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-act btn-act-del" title="Delete Contractor">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ (auth()->user() && auth()->user()->isAdmin()) ? 8 : 7 }}" style="text-align: center; padding: 48px 16px;">
                        <div style="font-size: 40px; color: #475569; margin-bottom: 12px;"><i class="fa-solid fa-helmet-safety"></i></div>
                        <div style="font-size: 16px; font-weight: 700; color: #CBD5E1; margin-bottom: 6px;">No Contractors Found</div>
                        <div style="font-size: 13px; color: #94A3B8; margin-bottom: 18px;">Start by adding contractors for each project.</div>
                        <a href="{{ route('contractors.create') }}" class="btn-pc"><i class="fa-solid fa-plus"></i> Add First Contractor</a>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($contractors->hasPages())
    <div class="pagination-wrap">
        {{ $contractors->links() }}
    </div>
    @endif
</div>

{{-- All Payments Quick View Modal --}}
<div id="paymentsModalBackdrop" class="payments-modal-backdrop" onclick="closePaymentsModalOnBackdrop(event)">
    <div class="payments-modal-box">
        <div class="payments-modal-header">
            <div>
                <h3 id="modalContractorName" style="color: #FFFFFF; font-size: 18px; font-weight: 800; margin: 0 0 4px;">Contractor Payments</h3>
                <div style="font-size: 12.5px; color: #94A3B8; display: flex; gap: 12px; flex-wrap: wrap;" id="modalFinancialSummary">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>
            <button type="button" onclick="closePaymentsModal()" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #CBD5E1; width: 32px; height: 32px; border-radius: 8px; font-size: 14px; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="payments-modal-body">
            <div id="modalPaymentsContainer">
                <!-- Payment table injected by JS -->
            </div>
        </div>

        <div class="payments-modal-footer">
            <button type="button" onclick="closePaymentsModal()" style="background: rgba(255,255,255,0.08); color: #CBD5E1; border: 1px solid rgba(255,255,255,0.15); padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                Close
            </button>
            <a id="modalFullDossierLink" href="#" class="btn-pay-compact" style="padding: 8px 16px; font-size: 13px;">
                <i class="fa-solid fa-money-bill-wave"></i> Add / Manage Payments
            </a>
        </div>
    </div>
</div>

<script>
function formatINR(val) {
    return Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function openPaymentsModal(id, name, contractAmt, paidAmt, dueAmt, payments, dossierUrl) {
    document.getElementById('modalContractorName').innerHTML = '<i class="fa-solid fa-user-shield" style="color: #60A5FA; margin-right: 6px;"></i> ' + name;

    let summaryHtml = '';
    if (contractAmt > 0) {
        summaryHtml += '<span>Contract: <strong style="color: #60A5FA;">₹' + formatINR(contractAmt) + '</strong></span>';
    }
    summaryHtml += '<span>Paid: <strong style="color: #34D399;">₹' + formatINR(paidAmt) + '</strong></span>';
    if (contractAmt > 0) {
        summaryHtml += '<span>Due: <strong style="color: #F87171;">₹' + formatINR(dueAmt) + '</strong></span>';
    }
    document.getElementById('modalFinancialSummary').innerHTML = summaryHtml;
    document.getElementById('modalFullDossierLink').href = dossierUrl;

    let container = document.getElementById('modalPaymentsContainer');
    if (!payments || payments.length === 0) {
        container.innerHTML = '<div style="text-align: center; padding: 30px; color: #94A3B8;"><i class="fa-solid fa-receipt" style="font-size: 32px; margin-bottom: 10px; display: block; color: #475569;"></i>No payment installments recorded yet.</div>';
    } else {
        let tableHtml = '<table style="width: 100%; border-collapse: collapse; font-size: 12.5px;">' +
            '<thead>' +
                '<tr style="background: rgba(255,255,255,0.05); color: #94A3B8; text-transform: uppercase; font-size: 11px; text-align: left;">' +
                    '<th style="padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.10);">#</th>' +
                    '<th style="padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.10);">Date</th>' +
                    '<th style="padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.10);">Amount</th>' +
                    '<th style="padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.10);">Mode</th>' +
                    '<th style="padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.10);">Type / Ref</th>' +
                    '<th style="padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.10);">Remarks</th>' +
                '</tr>' +
            '</thead>' +
            '<tbody>';

        payments.forEach((p, idx) => {
            let pDate = p.payment_date ? p.payment_date.substring(0, 10) : '—';
            let pMode = p.payment_mode || (p.payment_mode ? p.payment_mode.name : '—');
            let pType = p.payment_type || 'Part Payment';
            let pRef = p.reference_no ? ' (' + p.reference_no + ')' : '';
            let pRemarks = p.remarks || '—';

            tableHtml += '<tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">' +
                '<td style="padding: 10px 12px; color: #94A3B8;">' + (idx + 1) + '</td>' +
                '<td style="padding: 10px 12px; color: #CBD5E1;">' + pDate + '</td>' +
                '<td style="padding: 10px 12px; font-weight: 800; color: #34D399;">₹' + formatINR(p.amount) + '</td>' +
                '<td style="padding: 10px 12px; color: #60A5FA;">' + pMode + '</td>' +
                '<td style="padding: 10px 12px; color: #E2E8F0;"><span style="background: rgba(255,255,255,0.07); padding: 2px 6px; border-radius: 4px; font-size: 11px;">' + pType + pRef + '</span></td>' +
                '<td style="padding: 10px 12px; color: #94A3B8; font-size: 11.5px; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' + pRemarks + '">' + pRemarks + '</td>' +
            '</tr>';
        });

        tableHtml += '</tbody></table>';
        container.innerHTML = tableHtml;
    }

    let backdrop = document.getElementById('paymentsModalBackdrop');
    backdrop.style.display = 'flex';
}

function closePaymentsModal() {
    document.getElementById('paymentsModalBackdrop').style.display = 'none';
}

function closePaymentsModalOnBackdrop(e) {
    if (e.target.id === 'paymentsModalBackdrop') {
        closePaymentsModal();
    }
}
</script>
@endsection
