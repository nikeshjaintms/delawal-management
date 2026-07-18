@extends('admin.layouts.app')

@section('title', 'Payments')
@section('page-title', 'Payment Management')

@section('content')
<style>
    .crud-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:15px; }
    .crud-title h2 { font-size:22px; font-weight:700; color:var(--text-primary); margin-bottom:4px; }
    .crud-title p  { font-size:13.5px; color:var(--text-secondary); }
    .btn-gold { background-color:var(--gold); color:#FFF; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; display:inline-flex; align-items:center; gap:8px; border:none; cursor:pointer; transition:var(--transition); box-shadow:0 4px 10px rgba(212,175,55,0.2); }
    .btn-gold:hover { background-color:#B58D1B; transform:translateY(-1px); }
    .card-box { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:24px; box-shadow:var(--soft-shadow); }
    .filter-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:15px; }
    .search-form { display:flex; gap:10px; flex:1; max-width:540px; }
    .search-input { flex:1; padding:10px 14px; border:1px solid var(--border-color); border-radius:8px; font-size:13.5px; font-family:var(--font-primary); color:var(--text-primary); outline:none; transition:var(--transition); }
    .search-input:focus { border-color:var(--gold); box-shadow:0 0 0 3px var(--gold-light); }
    .btn-search { background-color:var(--text-primary); color:#FFF; padding:10px 18px; border-radius:8px; border:none; font-size:13.5px; font-weight:600; cursor:pointer; font-family:var(--font-primary); }
    .btn-search:hover { background-color:#1E293B; }
    .btn-reset { padding:10px 14px; color:var(--text-secondary); text-decoration:none; font-size:13.5px; font-weight:500; }
    .btn-reset:hover { color:var(--text-primary); }
    .table-container { width:100%; overflow-x:auto; }
    .premium-table { width:100%; border-collapse:collapse; text-align:left; font-size:13.5px; }
    .premium-table th { padding:13px 14px; background:#F9FAFB; color:var(--text-secondary); font-weight:600; border-bottom:1px solid var(--border-color); font-size:11.5px; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap; }
    .premium-table td { padding:14px; border-bottom:1px solid #F1F5F9; color:var(--text-primary); vertical-align:middle; }
    .premium-table tr:last-child td { border-bottom:none; }
    .premium-table tbody tr:hover { background-color:#F9FAFB; }
    .amount-col { font-weight:700; }
    .pending-red { color:#B91C1C; }
    .paid-green  { color:#16803D; }
    .badge { display:inline-block; padding:4px 10px; font-size:11px; font-weight:600; border-radius:20px; text-transform:uppercase; }
    .badge-pending { background:rgba(234,179,8,0.12); color:#92710A; }
    .badge-partial { background:rgba(59,130,246,0.1); color:#1D4ED8; }
    .badge-paid    { background:rgba(34,197,94,0.1);  color:#16803D; }
    .mode-chip { display:inline-flex; align-items:center; gap:4px; background:var(--gold-light); color:#92710A; font-size:11.5px; font-weight:600; padding:3px 9px; border-radius:6px; border:1px solid rgba(212,175,55,0.2); }
    .action-links { display:flex; gap:10px; align-items:center; }
    .action-link { color:var(--text-secondary); text-decoration:none; font-size:13px; transition:var(--transition); display:inline-flex; align-items:center; gap:4px; }
    .action-link.view:hover { color:#0EA5E9; }
    .action-link.edit:hover { color:var(--gold); }
    .action-link.delete-btn { background:none; border:none; cursor:pointer; color:var(--text-secondary); font-family:var(--font-primary); font-size:13px; padding:0; }
    .action-link.delete-btn:hover { color:#EF4444; }
    .alert-success { background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.2); color:#16803D; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13.5px; display:flex; align-items:center; gap:8px; }
    .pagination-wrapper { margin-top:24px; display:flex; justify-content:center; }
    .pay-id { font-size:12px; font-weight:700; color:var(--text-secondary); background:#F1F5F9; padding:3px 8px; border-radius:5px; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Payment Management</h2>
        <p>Track and manage all property sale payments firm-wise.</p>
    </div>
    <a href="{{ route('payments.create') }}" class="btn-gold">
        <i class="fa-solid fa-plus"></i>
        <span>Add Payment</span>
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
        <form method="GET" action="{{ route('payments.index') }}" class="search-form">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by customer, property, mode, status..." class="search-input @error('search') is-invalid @enderror">
            <button type="submit" class="btn-search">Search</button>
            @if(request('search'))
                <a href="{{ route('payments.index') }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Pay ID</th>
                    <th>Customer</th>
                    <th>Property / Unit</th>
                    <th>Booking ID</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Pending Amount</th>
                    <th>Payment Mode</th>
                    <th>Pay Date</th>
                    <th>Status</th>
                    <th style="width:150px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td><span class="pay-id">#{{ $payment->id }}</span></td>
                        <td>
                            <strong>{{ $payment->customer->name ?? '-' }}</strong>
                            @if($payment->customer?->mobile)
                                <div style="font-size:11.5px;color:var(--text-secondary);">{{ $payment->customer->mobile }}</div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600;">{{ $payment->property->property_name ?? '-' }}</div>
                            @if($payment->property?->property_code)
                                <div style="font-size:11.5px;color:var(--text-secondary);">{{ $payment->property->property_code }}</div>
                            @endif
                            @if($payment->property?->unit_no)
                                <div style="font-size:11.5px;color:var(--gold);font-weight:600;">Unit: {{ $payment->property->unit_no }}</div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('property-sales.show', $payment->property_sale_id) }}"
                               style="color:var(--gold);font-weight:600;font-size:12.5px;text-decoration:none;">
                                #{{ $payment->property_sale_id }}
                            </a>
                        </td>
                        <td class="amount-col">₹{{ number_format($payment->total_amount, 0) }}</td>
                        <td class="amount-col paid-green">₹{{ number_format($payment->paid_amount, 0) }}</td>
                        <td class="amount-col {{ $payment->pending_amount > 0 ? 'pending-red' : 'paid-green' }}">
                            ₹{{ number_format($payment->pending_amount, 0) }}
                        </td>
                        <td>
                            @if($payment->payment_mode)
                                <span class="mode-chip">{{ $payment->payment_mode }}</span>
                            @else -
                            @endif
                        </td>
                        <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : '-' }}</td>
                        <td><span class="badge badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td>
                        <td>
                            <div class="table-action-buttons">
                                <a href="{{ route('payments.show', $payment->id) }}" class="btn-view">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a href="{{ route('payments.edit', $payment->id) }}" class="btn-edit">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Delete this payment? This will recalculate booking amounts.')"
                                        class="btn-delete">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" align="center" style="padding:30px;color:var(--text-secondary);">
                            No payment records found for this firm.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $payments->appends(request()->query())->links() }}
    </div>
</div>
@endsection

