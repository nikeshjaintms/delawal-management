@extends('admin.layouts.app')

@section('title', 'Broker Commissions')
@section('page-title', 'Broker Commissions')

@section('content')
<style>
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .crud-title h2 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .crud-title p {
        font-size: 13.5px;
        color: var(--text-secondary);
    }

    .btn-gold {
        background-color: #fc6900ff; /* Orange/Gold theme */
        color: #FFFFFF;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 4px 10px rgba(252, 105, 0, 0.2);
    }

    .btn-gold:hover {
        background-color: #e05c00;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(252, 105, 0, 0.3);
    }

    .btn-secondary {
        background-color: #64748B;
        color: #FFFFFF;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-secondary:hover {
        background-color: #475569;
        transform: translateY(-1px);
    }

    .kpi-section {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    @media(max-width: 992px) {
        .kpi-section {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(max-width: 480px) {
        .kpi-section {
            grid-template-columns: 1fr;
        }
    }

    .kpi-card {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--soft-shadow);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--card-hover);
    }

    .kpi-info h4 {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 8px;
    }

    .kpi-info p {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-primary);
    }

    .kpi-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .bg-light-blue { background: rgba(59, 130, 246, 0.1); color: #3B82F6; }
    .bg-light-green { background: rgba(16, 185, 129, 0.1); color: #10B981; }
    .bg-light-orange { background: rgba(249, 115, 22, 0.1); color: #F97316; }
    .bg-light-red { background: rgba(239, 68, 68, 0.1); color: #EF4444; }

    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        box-shadow: var(--soft-shadow);
    }

    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1;
        min-width: 150px;
    }

    .filter-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .filter-control, .search-input {
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 13.5px;
        font-family: var(--font-primary);
        color: var(--text-primary);
        outline: none;
        background: #FFF;
        transition: var(--transition);
        width: 100%;
    }

    .filter-control:focus, .search-input:focus {
        border-color: #fc6900ff;
        box-shadow: 0 0 0 3px rgba(252, 105, 0, 0.15);
    }

    .btn-search {
        background-color: var(--text-primary);
        color: #FFFFFF;
        padding: 11px 20px;
        border-radius: 8px;
        border: none;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-search:hover {
        background-color: #1E293B;
    }

    .btn-reset {
        padding: 11px 16px;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 500;
        transition: var(--transition);
    }

    .btn-reset:hover {
        color: var(--text-primary);
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .premium-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 13.5px;
    }

    .premium-table th {
        padding: 14px 16px;
        background: #F9FAFB;
        color: var(--text-secondary);
        font-weight: 600;
        border-bottom: 1px solid var(--border-color);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .premium-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #F1F5F9;
        color: var(--text-primary);
        vertical-align: middle;
    }

    .premium-table tr:last-child td {
        border-bottom: none;
    }

    .premium-table tbody tr:hover {
        background-color: #F9FAFB;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 10.5px;
        font-weight: 700;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .badge-pending { background: rgba(245, 158, 11, 0.1); color: #B45309; }
    .badge-partial { background: rgba(59, 130, 246, 0.1); color: #1D4ED8; }
    .badge-paid { background: rgba(34, 197, 94, 0.1); color: #16803D; }

    .badge-active { background: rgba(34, 197, 94, 0.1); color: #16803D; }
    .badge-inactive { background: rgba(239, 68, 68, 0.1); color: #B91C1C; }

    .commission-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: rgba(252, 105, 0, 0.08);
        color: #e05c00;
        font-size: 12px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
        border: 1px solid rgba(252, 105, 0, 0.2);
    }

    .action-buttons-wrap {
        display: flex;
        gap: 6px;
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.08);
        border: 1px solid rgba(34, 197, 94, 0.2);
        color: #16803D;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }

    .export-btn-group {
        display: flex;
        gap: 10px;
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Broker Commissions</h2>
        <p>Record, manage, and track payouts for broker commissions.</p>
    </div>
    <div class="export-btn-group">
        <a href="{{ route('broker-commissions.pdf', request()->query()) }}" target="_blank" class="btn-secondary">
            <i class="fa-solid fa-print"></i> Print PDF
        </a>
        <a href="{{ route('broker-commissions.excel', request()->query()) }}" class="btn-secondary">
            <i class="fa-solid fa-file-excel"></i> Export CSV
        </a>
        @if(Auth::user()->hasPermission('broker_commission_add'))
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

        <div class="filter-group" style="flex: 1.5; min-width: 200px;">
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
                @foreach($properties as $p)
                    <option value="{{ $p->id }}" {{ request('filter_property') == $p->id ? 'selected' : '' }}>{{ $p->property_name }}</option>
                @endforeach
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

        <button type="submit" class="btn-search">Filter</button>
        @if(request()->hasAny(['search', 'filter_broker', 'filter_property', 'filter_payment_status', 'from_date', 'to_date', 'firm_id']))
            <a href="{{ route('broker-commissions.index') }}" class="btn-reset">Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>No</th>
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
                        <td>{{ $commissions->firstItem() + $key }}</td>
                        <td><strong style="color:#0F172A;">{{ $c->firm_names }}</strong></td>
                        <td><strong>{{ $c->broker->name ?? '-' }}</strong></td>
                        <td>{{ $c->property->property_name ?? '-' }}</td>
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
                            @if(Auth::user()->hasPermission('broker_commission_edit'))
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
                                @if(Auth::user()->hasPermission('broker_commission_view'))
                                <a href="{{ route('broker-commissions.show', $c->id) }}" class="btn-view">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                @endif
                                @if(Auth::user()->hasPermission('broker_commission_edit'))
                                <a href="{{ route('broker-commissions.edit', $c->id) }}" class="btn-edit">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                @endif
                                @if(Auth::user()->hasPermission('broker_commission_delete'))
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

    <div class="pagination-wrapper">
        {{ $commissions->appends(request()->query())->links() }}
    </div>
</div>
@endsection
