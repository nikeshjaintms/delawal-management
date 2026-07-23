@extends('admin.layouts.app')

@section('title', 'Rental Management')
@section('page-title', 'Rental Management')

@section('content')
<style>
    .crud-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:15px; }
    .crud-title h2 { font-size:22px; font-weight:700; color:var(--text-primary); margin-bottom:4px; }
    .crud-title p  { font-size:13.5px; color:var(--text-secondary); }
    .btn-gold { background-color:var(--gold); color:#FFF; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; display:inline-flex; align-items:center; gap:8px; border:none; cursor:pointer; transition:var(--transition); box-shadow:0 4px 10px rgba(212,175,55,0.2); }
    .btn-gold:hover { background-color:#B58D1B; transform:translateY(-1px); }
    .card-box { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:24px; box-shadow:var(--soft-shadow); }
    .filter-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:15px; }
    .search-form { display:flex; gap:10px; flex:1; max-width:520px; }
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
    .tenant-name { font-weight:700; color:var(--text-primary); }
    .tenant-mobile { font-size:12px; color:var(--text-secondary); }
    .amount-col { font-weight:700; }
    .badge { display:inline-block; padding:4px 10px; font-size:11px; font-weight:600; border-radius:20px; text-transform:uppercase; }
    .badge-pending   { background:rgba(234,179,8,0.12); color:#92710A; }
    .badge-partial   { background:rgba(59,130,246,0.1);  color:#1D4ED8; }
    .badge-paid      { background:rgba(34,197,94,0.1);   color:#16803D; }
    .badge-active    { background:rgba(34,197,94,0.1);   color:#16803D; }
    .badge-completed { background:rgba(100,116,139,0.1); color:#475569; }
    .badge-cancelled { background:rgba(239,68,68,0.1);   color:#B91C1C; }
    .action-links { display:flex; gap:10px; align-items:center; }
    .action-link { color:var(--text-secondary); text-decoration:none; font-size:13px; transition:var(--transition); display:inline-flex; align-items:center; gap:4px; }
    .action-link.view:hover { color:#0EA5E9; }
    .action-link.edit:hover { color:var(--gold); }
    .action-link.delete-btn { background:none; border:none; cursor:pointer; color:var(--text-secondary); font-family:var(--font-primary); font-size:13px; padding:0; }
    .action-link.delete-btn:hover { color:#EF4444; }
    .action-link.payments:hover { color:#8B5CF6; }
    .action-link.payments { white-space:nowrap; }
    .alert-success { background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.2); color:#16803D; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13.5px; display:flex; align-items:center; gap:8px; }
    .pagination-wrapper { margin-top:24px; display:flex; justify-content:center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Rental Management</h2>
        <p>Manage all property rentals and tenant agreements firm-wise.</p>
    </div>
    <a href="{{ route('rentals.create') }}" class="btn-gold">
        <i class="fa-solid fa-plus"></i>
        <span>Add Rental</span>
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
        <form method="GET" action="{{ route('rentals.index') }}" class="search-form">
            @if(auth()->user() && auth()->user()->isAdmin())
                <select name="firm_id" class="search-input" onchange="this.form.submit()" style="max-width: 180px;">
                    <option value="">All Firms</option>
                    @foreach($firms as $firm)
                        <option value="{{ $firm->id }}" {{ request('firm_id') == $firm->id ? 'selected' : '' }}>
                            {{ $firm->firm_name }}
                        </option>
                    @endforeach
                </select>
            @endif
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by tenant, property, firm, status..." class="search-input @error('search') is-invalid @enderror">
            <button type="submit" class="btn-search">Search</button>
            @if(request('search') || request('firm_id'))
                <a href="{{ route('rentals.index') }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>No</th>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <th>Firm</th>
                    @endif
                    <th>Property</th>
                    <th>Tenant</th>
                    <th>Rent Amount</th>
                    <th>Security Deposit</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Due Date</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th style="width:260px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rentals as $key => $rental)
                    <tr>
                        <td>{{ $rentals->firstItem() + $key }}</td>
                        @if(auth()->user() && auth()->user()->isAdmin())
                            <td><strong>{{ $rental->firm->firm_name ?? '-' }}</strong></td>
                        @endif
                        <td>
                            <div style="font-weight:600;">{{ $rental->property->property_name ?? '-' }}</div>
                            @if($rental->property?->property_code)
                                <div style="font-size:11.5px;color:var(--text-secondary);">{{ $rental->property->property_code }}</div>
                            @endif
                            @if($rental->property?->unit_no)
                                <div style="font-size:11.5px;color:var(--gold);font-weight:600;">Unit: {{ $rental->property->unit_no }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="tenant-name">{{ $rental->tenant_name }}</div>
                            <div class="tenant-mobile">{{ $rental->tenant_mobile }}</div>
                        </td>
                        <td class="amount-col">₹{{ number_format($rental->rent_amount, 0) }}</td>
                        <td>
                            @if($rental->security_deposit)
                                ₹{{ number_format($rental->security_deposit, 0) }}
                            @else
                                <span style="color:var(--text-secondary);">-</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($rental->rent_start_date)->format('d M Y') }}</td>
                        <td>
                            {{ $rental->rent_end_date ? \Carbon\Carbon::parse($rental->rent_end_date)->format('d M Y') : '-' }}
                        </td>
                        <td>
                            @if($rental->rent_due_date)
                                <span style="background:var(--gold-light);color:#92710A;padding:3px 8px;border-radius:5px;font-size:12px;font-weight:700;">
                                    Day {{ $rental->rent_due_date }}
                                </span>
                            @else -
                            @endif
                        </td>
                        <td><span class="badge badge-{{ $rental->payment_status }}">{{ ucfirst($rental->payment_status) }}</span></td>
                        <td><span class="badge badge-{{ $rental->rental_status }}">{{ ucfirst($rental->rental_status) }}</span></td>
                        <td>
                            <div class="table-action-buttons">
                                <a href="{{ route('rental-payments.index', $rental->id) }}" class="action-link payments"
                                   title="Payment History">
                                    <i class="fa-solid fa-money-bill-wave"></i> Payments
                                </a>
                                <a href="{{ route('rentals.show', $rental->id) }}" class="btn-view"
                                   title="View Rental">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a href="{{ route('rentals.edit', $rental->id) }}" class="btn-edit"
                                   title="Edit Rental">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('rentals.destroy', $rental->id) }}" method="POST"
                                      style="display:inline;" id="delete-form-{{ $rental->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="btn-delete"
                                        title="Delete Rental"
                                        onclick="confirmDelete({{ $rental->id }}, '{{ addslashes($rental->tenant_name) }}')">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" align="center" style="padding:30px;color:var(--text-secondary);">
                            No rental records found for this firm.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $rentals->appends(request()->query())->links() }}
    </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id, tenantName) {
    Swal.fire({
        title: 'Delete Rental?',
        html: 'Are you sure you want to delete the rental record for <strong>' + tenantName + '</strong>?<br><small style="color:#64748B;">This action cannot be undone.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa fa-trash"></i> Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: {
            popup:      'swal-popup-custom',
            title:      'swal-title-custom',
            confirmButton: 'swal-confirm-custom',
            cancelButton:  'swal-cancel-custom',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
<style>
    .swal-popup-custom  { font-family: 'Outfit', sans-serif !important; border-radius: 14px !important; }
    .swal-title-custom  { font-size: 18px !important; font-weight: 700 !important; color: #0F1F35 !important; }
    .swal-confirm-custom, .swal-cancel-custom { font-family: 'Outfit', sans-serif !important; font-weight: 600 !important; border-radius: 8px !important; padding: 10px 22px !important; font-size: 14px !important; }
</style>
@endsection

