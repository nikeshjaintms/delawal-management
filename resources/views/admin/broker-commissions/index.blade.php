@extends('admin.layouts.app')

@section('title', 'Broker Commissions')
@section('page-title', 'Broker Commissions')

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

.kpi-section {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;
}
@media(max-width: 992px) { .kpi-section { grid-template-columns: repeat(2, 1fr); } }
@media(max-width: 480px) { .kpi-section { grid-template-columns: 1fr; } }

.kpi-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 20px !important;
    display: flex; justify-content: space-between; align-items: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important; transition: transform 0.2s ease;
}
.kpi-card:hover { transform: translateY(-3px); }

.kpi-info h4 {
    font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px;
}
.kpi-info p {
    font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin: 0;
}
.kpi-icon-box {
    width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px;
}

.bg-light-blue { background: rgba(59, 130, 246, 0.15); color: #60A5FA; }
.bg-light-green { background: rgba(16, 185, 129, 0.15); color: #34D399; }
.bg-light-orange { background: rgba(249, 115, 22, 0.15); color: #FB923C; }
.bg-light-red { background: rgba(239, 68, 68, 0.15); color: #F87171; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
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
.filter-group { display: flex !important; flex-direction: column !important; gap: 6px !important; flex: 1 1 0 !important; min-width: 110px !important; }
.filter-group.search-group { flex: 1.4 1 0 !important; min-width: 150px !important; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap !important; }

.filter-control, .search-input {
    width: 100% !important; padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important;
}
.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus, .filter-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(0.8) sepia(1) saturate(5) hue-rotate(185deg);
    cursor: pointer;
}

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 18px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    flex-shrink: 0 !important; white-space: nowrap !important; align-self: flex-end !important;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px; flex-shrink: 0 !important; white-space: nowrap !important; align-self: flex-end !important; transition: color .2s ease; }
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
    font-size: 13.5px; color: #FFFFFF !important; font-weight: 500; vertical-align: middle;
    white-space: nowrap !important;
}
.premium-table td strong { color: #FFFFFF !important; font-weight: 700 !important; }
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-pending { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-partial { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.badge-paid { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.commission-chip {
    display: inline-flex; align-items: center; gap: 4px; background: rgba(245, 158, 11, 0.15);
    color: #FBBF24; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 20px;
    border: 1px solid rgba(245, 158, 11, 0.30);
}

.action-buttons-wrap { display: flex; gap: 8px; align-items: center; white-space: nowrap !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
.export-btn-group { display: flex; gap: 10px; align-items: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Broker Commissions</h2>
        <p>Record, manage, and track payouts for broker commissions.</p>
    </div>
    <div class="export-btn-group">
        <a href="{{ route('broker-commissions.pdf', request()->query()) }}" target="_blank" class="btn-export-pdf">
            <i class="fa-solid fa-file-pdf"></i> Print PDF
        </a>
        <a href="{{ route('broker-commissions.excel', request()->query()) }}" class="btn-excel">
            <i class="fa-solid fa-file-csv"></i> Export CSV
        </a>
        @if($authUser && $authUser->hasPermission('broker_commission_add'))
        <a href="{{ route('broker-commissions.create') }}" class="btn-gold">
            <i class="fa-solid fa-plus"></i> Add Commission
        </a>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- KPI Summary Widgets --}}
<div class="kpi-section">
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Total Commission</h4>
            <p>₹{{ number_format($totalCommission, 2) }}</p>
        </div>
        <div class="kpi-icon-box bg-light-blue"><i class="fa-solid fa-indian-rupee-sign"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Paid Commission</h4>
            <p>₹{{ number_format($paidCommission, 2) }}</p>
        </div>
        <div class="kpi-icon-box bg-light-green"><i class="fa-solid fa-check"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Pending Commission</h4>
            <p>₹{{ number_format($pendingCommission, 2) }}</p>
        </div>
        <div class="kpi-icon-box bg-light-red"><i class="fa-solid fa-clock"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>This Month</h4>
            <p>₹{{ number_format($thisMonthCommission, 2) }}</p>
        </div>
        <div class="kpi-icon-box bg-light-orange"><i class="fa-solid fa-calendar-days"></i></div>
    </div>
</div>

<div class="card-box">
    {{-- Search and Filter Form --}}
    <form method="GET" action="{{ route('broker-commissions.index') }}" class="filter-bar">
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
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Broker, property, or customer name..." class="search-input @error('search') is-invalid @enderror">
        </div>

        <div class="filter-group">
            <span class="filter-label">Broker</span>
            <select name="filter_broker" class="filter-control @error('filter_broker') is-invalid @enderror">
                <option value="">All Brokers</option>
                @foreach($brokers as $b)
                    <option value="{{ $b->id }}" {{ request('filter_broker') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Property</span>
            <select name="filter_property" class="filter-control @error('filter_property') is-invalid @enderror">
                <option value="">All Properties</option>
                @if(isset($propertyMasters))
                    @foreach($propertyMasters as $pm)
                        <option value="{{ $pm->id }}" {{ request('filter_property') == $pm->id ? 'selected' : '' }}>{{ $pm->property_name }} ({{ $pm->property_code }})</option>
                    @endforeach
                @elseif(isset($properties))
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}" {{ request('filter_property') == $p->id ? 'selected' : '' }}>{{ $p->property_name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Payment Status</span>
            <select name="filter_payment_status" class="filter-control @error('filter_payment_status') is-invalid @enderror">
                <option value="">All Status</option>
                <option value="pending" {{ request('filter_payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="partial" {{ request('filter_payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="paid"    {{ request('filter_payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
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

        <div class="filter-actions" style="display: flex; gap: 8px;">
            <button type="submit" class="btn-search">Filter</button>
            @if(request()->hasAny(['search', 'filter_broker', 'filter_property', 'filter_payment_status', 'from_date', 'to_date', 'firm_id']))
                <a href="{{ route('broker-commissions.index') }}" class="btn-reset">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Firm</th>
                    <th>Broker</th>
                    <th>Property</th>
                    <th>Customer</th>
                    <th>Commission</th>
                    <th>Calculated Amount</th>
                    <th>Payment Status</th>
                    <th>Payment Date</th>
                    <th>Status</th>
                    <th style="width: 200px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions as $key => $c)
                    <tr>
                        <td>{{ method_exists($commissions, 'firstItem') ? ($commissions->firstItem() + $key) : ($key + 1) }}</td>
                        <td><strong style="color: #FFFFFF !important;">{{ $c->firm_names }}</strong></td>
                        <td><strong>{{ $c->broker->name ?? '-' }}</strong></td>
                        <td>
                            <strong style="color: #FFFFFF;">
                                {{ $c->property->propertyMaster->property_name ?? ($c->property->project->propertyMaster->property_name ?? ($c->property->project->project_name ?? ($c->property->property_name ?? '-'))) }}
                            </strong>
                            @if($c->property && $c->property->property_code)
                                <div style="font-size: 11.5px; color: #94A3B8; margin-top: 2px;">
                                    {{ $c->property->property_name }} ({{ $c->property->property_code }})
                                </div>
                            @endif
                        </td>
                        <td>{{ $c->customer->name ?? '-' }}</td>
                        <td>
                            <span class="commission-chip">
                                @if($c->commission_type == 'percentage')
                                    {{ number_format($c->commission_value, 2) }}%
                                @else
                                    ₹{{ number_format($c->commission_value, 2) }}
                                @endif
                            </span>
                        </td>
                        <td><strong>₹{{ number_format($c->commission_amount, 2) }}</strong></td>
                        <td>
                            <span class="badge badge-{{ $c->payment_status }}">
                                {{ ucfirst($c->payment_status) }}
                            </span>
                        </td>
                        <td>{{ $c->payment_date ? \Carbon\Carbon::parse($c->payment_date)->format('d M Y') : '-' }}</td>
                        <td>
                            @if($authUser && $authUser->hasPermission('broker_commission_edit'))
                            <form action="{{ route('broker-commissions.toggle-status', $c->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                                    <span class="badge badge-{{ $c->status }}">
                                        {{ ucfirst($c->status) }}
                                    </span>
                                </button>
                            </form>
                            @else
                            <span class="badge badge-{{ $c->status }}">
                                {{ ucfirst($c->status) }}
                            </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons-wrap">
                                @if($authUser && $authUser->hasPermission('broker_commission_view'))
                                <a href="{{ route('broker-commissions.show', $c->id) }}" class="btn-view">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                @endif
                                @if($authUser && $authUser->hasPermission('broker_commission_edit'))
                                <a href="{{ route('broker-commissions.edit', $c->id) }}" class="btn-edit">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                @endif
                                @if($authUser && $authUser->hasPermission('broker_commission_delete'))
                                <form action="{{ route('broker-commissions.destroy', $c->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this record?')" class="btn-delete">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" align="center" style="padding: 30px; color: var(--text-secondary);">No commissions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($commissions, 'links'))
        <div class="pagination-wrapper">
            {{ $commissions->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
