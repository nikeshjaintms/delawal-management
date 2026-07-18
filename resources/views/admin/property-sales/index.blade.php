@extends('admin.layouts.app')

@section('title', 'Property Sales')
@section('page-title', 'Property Sales')

@section('content')
<style>
    .crud-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:15px; }
    .crud-title h2 { font-size:22px; font-weight:700; color:var(--text-primary); margin-bottom:4px; }
    .crud-title p  { font-size:13.5px; color:var(--text-secondary); }
    .btn-gold { background-color:var(--gold); color:#FFF; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; display:inline-flex; align-items:center; gap:8px; border:none; cursor:pointer; transition:var(--transition); box-shadow:0 4px 10px rgba(212,175,55,0.2); }
    .btn-gold:hover { background-color:#B58D1B; transform:translateY(-1px); box-shadow:0 6px 14px rgba(212,175,55,0.3); }
    .card-box { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:24px; box-shadow:var(--soft-shadow); }
    .filter-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:15px; }
    .search-form { display:flex; gap:10px; flex:1; max-width:560px; }
    .search-input { flex:1; padding:10px 14px; border:1px solid var(--border-color); border-radius:8px; font-size:13.5px; font-family:var(--font-primary); color:var(--text-primary); outline:none; transition:var(--transition); }
    .search-input:focus { border-color:var(--gold); box-shadow:0 0 0 3px var(--gold-light); }
    .btn-search { background-color:var(--text-primary); color:#FFF; padding:10px 18px; border-radius:8px; border:none; font-size:13.5px; font-weight:600; cursor:pointer; font-family:var(--font-primary); transition:var(--transition); }
    .btn-search:hover { background-color:#1E293B; }
    .btn-reset { padding:10px 14px; color:var(--text-secondary); text-decoration:none; font-size:13.5px; font-weight:500; }
    .btn-reset:hover { color:var(--text-primary); }
    .table-container { width:100%; overflow-x:auto; }
    .premium-table { width:100%; border-collapse:collapse; text-align:left; font-size:13.5px; }
    .premium-table th { padding:13px 14px; background:#F9FAFB; color:var(--text-secondary); font-weight:600; border-bottom:1px solid var(--border-color); font-size:11.5px; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap; }
    .premium-table td { padding:14px; border-bottom:1px solid #F1F5F9; color:var(--text-primary); vertical-align:middle; }
    .premium-table tr:last-child td { border-bottom:none; }
    .premium-table tbody tr:hover { background-color:#F9FAFB; }
    .prop-name { font-weight:600; color:var(--text-primary); }
    .prop-code { font-size:11.5px; color:var(--text-secondary); }
    .amount-cell { font-weight:700; color:var(--text-primary); }
    .badge { display:inline-block; padding:4px 10px; font-size:11px; font-weight:600; border-radius:20px; text-transform:uppercase; }
    .badge-pending  { background:rgba(234,179,8,0.12); color:#92710A; }
    .badge-partial  { background:rgba(59,130,246,0.1); color:#1D4ED8; }
    .badge-paid     { background:rgba(34,197,94,0.1); color:#16803D; }
    .badge-booked   { background:rgba(234,179,8,0.12); color:#92710A; }
    .badge-sold     { background:rgba(34,197,94,0.1); color:#16803D; }
    .badge-cancelled{ background:rgba(239,68,68,0.1); color:#B91C1C; }
    .action-links { display:flex; gap:10px; align-items:center; }
    .action-link { color:var(--text-secondary); text-decoration:none; font-size:13px; transition:var(--transition); display:inline-flex; align-items:center; gap:4px; }
    .action-link.view:hover { color:#0EA5E9; }
    .action-link.edit:hover { color:var(--gold); }
    .action-link.delete-btn { background:none; border:none; cursor:pointer; color:var(--text-secondary); font-family:var(--font-primary); font-size:13px; padding:0; }
    .action-link.delete-btn:hover { color:#EF4444; }
    .alert-success { background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.2); color:#16803D; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13.5px; display:flex; align-items:center; gap:8px; }
    .pagination-wrapper { margin-top:24px; display:flex; justify-content:center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Property Sales</h2>
        <p>Manage property bookings and sales firm-wise.</p>
    </div>
    <a href="{{ route('property-sales.create') }}" class="btn-gold">
        <i class="fa-solid fa-plus"></i>
        <span>Add Property Sale</span>
    </a>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="card-box">
    <div class="filter-bar">
        <form method="GET" action="{{ route('property-sales.index') }}" class="search-form">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by property, customer, broker, status..." class="search-input @error('search') is-invalid @enderror">
            <button type="submit" class="btn-search">Search</button>
            @if(request('search'))
                <a href="{{ route('property-sales.index') }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Property</th>
                    <th>Customer</th>
                    <th>Broker</th>
                    <th>Sale Date</th>
                    <th>Sale Amount</th>
                    <th>Booking Amt</th>
                    <th>Remaining Amt</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th style="width:160px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($propertySales as $key => $sale)
                    <tr>
                        <td>{{ $propertySales->firstItem() + $key }}</td>
                        <td>
                            <div class="prop-name">{{ $sale->property->property_name ?? '-' }}</div>
                            @if($sale->property?->property_code)
                                <div class="prop-code">{{ $sale->property->property_code }}</div>
                            @endif
                        </td>
                        <td>{{ $sale->customer->name ?? '-' }}</td>
                        <td>{{ $sale->broker->name ?? '-' }}</td>
                        <td>{{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : '-' }}</td>
                        <td>
                            @if($sale->sale_amount !== null)
                                <span class="amount-cell">₹{{ number_format($sale->sale_amount, 0) }}</span>
                            @else -
                            @endif
                        </td>
                        <td>
                            @if($sale->booking_amount !== null)
                                ₹{{ number_format($sale->booking_amount, 0) }}
                            @else -
                            @endif
                        </td>
                        <td>
                            @if($sale->remaining_amount !== null)
                                ₹{{ number_format($sale->remaining_amount, 0) }}
                            @else -
                            @endif
                        </td>
                        <td><span class="badge badge-{{ $sale->payment_status }}">{{ ucfirst($sale->payment_status) }}</span></td>
                        <td><span class="badge badge-{{ $sale->sale_status }}">{{ ucfirst($sale->sale_status) }}</span></td>
                        <td>
                            <div class="table-action-buttons">
                                <a href="{{ route('property-sales.show', $sale->id) }}" class="btn-view">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a href="{{ route('property-sales.edit', $sale->id) }}" class="btn-edit">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('property-sales.destroy', $sale->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this sale record?')"
                                        class="btn-delete">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" align="center" style="padding:30px; color:var(--text-secondary);">
                            No property sale records found for this firm.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $propertySales->appends(request()->query())->links() }}
    </div>
</div>
@endsection

