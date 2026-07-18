@extends('admin.layouts.app')
@section('title', 'Bookings')
@section('page-title', 'Booking Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:10px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:24px;box-shadow:var(--soft-shadow);}
    .filter-bar{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;align-items:flex-end;}
    .filter-group{display:flex;flex-direction:column;gap:6px;}
    .filter-label{font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:0.6px;}
    .filter-control,.search-input{height:40px;box-sizing:border-box;padding:0 12px;border:1.5px solid var(--border-color);border-radius:8px;font-size:13.5px;font-family:var(--font-primary);color:var(--text-primary);outline:none;background:#FFF;transition:var(--transition);min-width:130px;}
    .filter-control:focus,.search-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-glow);}
    .search-input{min-width:220px;}
    .btn-search{height:40px;box-sizing:border-box;background-color:var(--text-primary);color:#FFF;padding:0 18px;border-radius:8px;border:1.5px solid var(--text-primary);font-size:13.5px;font-weight:600;cursor:pointer;font-family:var(--font-primary);white-space:nowrap;align-self:flex-end;display:inline-flex;align-items:center;justify-content:center;gap:6px;transition:background 0.2s ease,border-color 0.2s ease;}
    .btn-search:hover{background-color:#1E293B;border-color:#1E293B;}
    .btn-reset{height:40px;box-sizing:border-box;display:inline-flex;align-items:center;justify-content:center;padding:0 12px;color:var(--text-secondary);text-decoration:none;font-size:13.5px;font-weight:500;align-self:flex-end;transition:color 0.2s ease;}
    .btn-reset:hover{color:var(--text-primary);}
    .table-container{width:100%;overflow-x:auto;}
    .premium-table{width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;}
    .premium-table th{padding:13px 14px;background:#F9FAFB;color:var(--text-secondary);font-weight:600;border-bottom:1px solid var(--border-color);font-size:11.5px;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;}
    .premium-table td{padding:14px;border-bottom:1px solid #F1F5F9;color:var(--text-primary);vertical-align:middle;}
    .premium-table tr:last-child td{border-bottom:none;}
    .premium-table tbody tr:hover{background-color:#F9FAFB;}
    .badge{display:inline-block;padding:4px 10px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase;}
    .badge-pending{background:rgba(245,158,11,0.1);color:#B45309;}
    .badge-confirmed{background:rgba(34,197,94,0.1);color:#16803D;}
    .badge-cancelled{background:rgba(239,68,68,0.1);color:#B91C1C;}
    .badge-unpaid{background:rgba(239,68,68,0.08);color:#B91C1C;}
    .badge-partial{background:rgba(245,158,11,0.1);color:#B45309;}
    .badge-paid{background:rgba(34,197,94,0.1);color:#16803D;}
    .action-links{display:flex;gap:10px;align-items:center;white-space:nowrap;}
    .action-link{color:var(--text-secondary);text-decoration:none;font-size:13px;transition:var(--transition);display:inline-flex;align-items:center;gap:4px;}
    .action-link.view:hover{color:#0EA5E9;}
    .action-link.edit:hover{color:var(--gold);}
    .action-link.delete-btn{background:none;border:none;cursor:pointer;color:var(--text-secondary);font-family:var(--font-primary);font-size:13px;padding:0;}
    .action-link.delete-btn:hover{color:#EF4444;}
    .alert-success{background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);color:#16803D;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px;}
    .alert-danger{background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);color:#B91C1C;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px;}
    .pagination-wrapper{margin-top:24px;display:flex;justify-content:center;}
    .amount-chip{background:var(--gold-light);color:#92710A;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;display:inline-block;}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Booking Management</h2>
        <p>Manage property bookings, agreements and payment tracking.</p>
    </div>
    <a href="{{ route('bookings.create') }}" class="btn-gold">
        <i class="fa-solid fa-plus"></i> Add Booking
    </a>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif
@if(session('error'))
    <div class="alert-danger"><i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('bookings.index') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input @error('search') is-invalid @enderror" placeholder="Property, customer, status...">
        </div>
        <div class="filter-group">
            <span class="filter-label">Status</span>
            <select name="filter_status" class="filter-control @error('filter_status') is-invalid @enderror">
                <option value="">All Status</option>
                <option value="pending"   {{ request('filter_status')=='pending'   ?'selected':'' }}>Pending</option>
                <option value="confirmed" {{ request('filter_status')=='confirmed' ?'selected':'' }}>Confirmed</option>
                <option value="cancelled" {{ request('filter_status')=='cancelled' ?'selected':'' }}>Cancelled</option>
            </select>
        </div>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','filter_status']))
            <a href="{{ route('bookings.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th><th>Booking Date</th><th>Property</th><th>Customer</th><th>Broker</th>
                    <th>Booking Amount</th><th>Status</th><th>Payment</th><th style="width:160px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $key => $booking)
                <tr>
                    <td>{{ $bookings->firstItem() + $key }}</td>
                    <td>{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') : '-' }}</td>
                    <td><strong>{{ $booking->property->property_name ?? '-' }}</strong></td>
                    <td>{{ $booking->customer->name ?? '-' }}</td>
                    <td>{{ $booking->broker->name ?? '-' }}</td>
                    <td>
                        @if($booking->booking_amount)
                            <span class="amount-chip">₹{{ number_format($booking->booking_amount, 2) }}</span>
                        @else -
                        @endif
                    </td>
                    <td><span class="badge badge-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                    <td><span class="badge badge-{{ $booking->payment_status }}">{{ ucfirst($booking->payment_status) }}</span></td>
                    <td>
                        <div class="table-action-buttons">
                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn-view"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('bookings.edit', $booking->id) }}" class="btn-edit"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" style="display:inline;" id="del-bk-{{ $booking->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" onclick="confirmDelete({{ $booking->id }})">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" align="center" style="padding:30px;color:var(--text-secondary);">No bookings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $bookings->appends(request()->query())->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id) {
    Swal.fire({title:'Delete Booking?',text:'This action cannot be undone.',icon:'warning',showCancelButton:true,confirmButtonColor:'#EF4444',cancelButtonColor:'#64748B',confirmButtonText:'Yes, Delete'})
    .then(r => { if (r.isConfirmed) document.getElementById('del-bk-' + id).submit(); });
}
</script>
@endsection

