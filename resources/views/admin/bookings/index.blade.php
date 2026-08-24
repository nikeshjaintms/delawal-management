@extends('admin.layouts.app')
@section('title', 'Bookings')
@section('page-title', 'Booking Management')
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

.filter-group { display: flex !important; flex-direction: column !important; gap: 6px !important; flex: 1 1 0 !important; min-width: 130px !important; }
.filter-group.search-group { flex: 1.4 1 0 !important; min-width: 180px !important; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap !important; }

.filter-control, .search-input {
    width: 100% !important; padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important; height: 42px !important;
}
select.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus, .filter-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    flex-shrink: 0 !important; white-space: nowrap !important; align-self: flex-end !important; height: 42px !important;
    display: inline-flex; align-items: center; gap: 6px;
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
    font-size: 13.5px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
    white-space: nowrap !important;
}
.premium-table td strong { color: #FFFFFF !important; font-weight: 700 !important; }
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-pending { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-confirmed { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-cancelled { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.badge-unpaid { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-partial { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-paid { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }
.amount-chip { background: rgba(245, 158, 11, 0.15); color: #FBBF24; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.30); display: inline-block; }

.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.alert-danger { background: rgba(239, 68, 68, 0.15) !important; border: 1px solid rgba(239, 68, 68, 0.30) !important; color: #F87171 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
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
        @if(auth()->user() && auth()->user()->isAdmin())
        <div class="filter-group">
            <span class="filter-label">Firm</span>
            <select name="firm_id" class="filter-control" onchange="this.form.submit()">
                <option value="">All Firms</option>
                @foreach($firms as $f)
                    <option value="{{ $f->id }}" {{ request('firm_id')==$f->id?'selected':'' }}>{{ $f->firm_name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="filter-group search-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input @error('search') is-invalid @enderror" placeholder="Property, customer, firm, status...">
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
        @if(request()->hasAny(['search','firm_id','filter_status']))
            <a href="{{ route('bookings.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th><th>Firm</th><th>Booking Date</th><th>Property</th><th>Customer</th><th>Broker</th>
                    <th>Booking Amount</th><th>Status</th><th>Payment</th><th style="width:200px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $key => $booking)
                <tr>
                    <td>{{ method_exists($bookings, 'firstItem') ? ($bookings->firstItem() + $key) : ($key + 1) }}</td>
                    <td><strong style="color: #FFFFFF !important;">{{ $booking->firm->firm_name ?? '-' }}</strong></td>
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
                        <div class="action-buttons-wrap">
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
                <tr><td colspan="10" align="center" style="padding:30px;color:#CBD5E1;">No bookings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($bookings, 'links'))
        <div class="pagination-wrapper">{{ $bookings->appends(request()->query())->links() }}</div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id) {
    Swal.fire({title:'Delete Booking?',text:'This action cannot be undone.',icon:'warning',showCancelButton:true,confirmButtonColor:'#EF4444',cancelButtonColor:'#64748B',confirmButtonText:'Yes, Delete'})
    .then(r => { if (r.isConfirmed) document.getElementById('del-bk-' + id).submit(); });
}
</script>
@endsection

