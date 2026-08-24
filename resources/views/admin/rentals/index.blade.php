@extends('admin.layouts.app')

@section('title', 'Rental Management')
@section('page-title', 'Rental Management')

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
    display: flex !important; gap: 12px !important; align-items: center !important; margin-bottom: 24px !important;
    background: rgba(255, 255, 255, 0.04) !important; padding: 16px 20px !important;
    border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
    width: 100% !important; flex-wrap: nowrap !important; overflow-x: auto !important;
}

.search-form { display: flex !important; gap: 12px !important; flex: 1 !important; width: 100% !important; align-items: center !important; flex-wrap: nowrap !important; }

.search-input {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important; flex: 1 !important;
}
select.search-input option { background: #101622 !important; color: #FFFFFF !important; }
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    flex-shrink: 0 !important; white-space: nowrap !important;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px; flex-shrink: 0 !important; white-space: nowrap !important; transition: color .2s ease; }
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
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.tenant-name { font-weight: 700; color: #FFFFFF !important; }
.tenant-mobile { font-size: 12px; color: #94A3B8 !important; margin-top: 2px; }
.amount-col { font-weight: 800; color: #60A5FA !important; }

.due-date-chip {
    background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important;
    border: 1px solid rgba(245, 158, 11, 0.35) !important; padding: 4px 10px;
    border-radius: 6px; font-size: 12px; font-weight: 700; display: inline-block; white-space: nowrap;
}

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-pending   { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-partial   { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.badge-paid      { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-active    { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-completed { background: rgba(148, 163, 184, 0.18) !important; color: #CBD5E1 !important; border: 1px solid rgba(148, 163, 184, 0.35) !important; }
.badge-cancelled { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }

.btn-collect {
    background: #2563EB !important; color: #FFFFFF !important; padding: 7px 14px;
    border-radius: 10px; font-size: 12.5px; font-weight: 700; border: 1px solid #3B82F6 !important;
    text-decoration: none !important; display: inline-flex; align-items: center; gap: 6px;
    transition: all .2s ease; box-shadow: 0 4px 12px rgba(37,99,235,0.30);
}
.btn-collect:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-1px); }

.btn-history-link {
    display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px;
    background: rgba(255, 255, 255, 0.08) !important; color: #FFFFFF !important;
    font-size: 12.5px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .2s ease;
}
.btn-history-link:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-1px); }

.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        @if(request()->has('collect'))
            <h2>Rent Collection</h2>
            <p>Track rental payments and collect outstanding dues for active agreements.</p>
        @else
            <h2>Rental Management</h2>
            <p>Manage all property rentals and tenant agreements firm-wise.</p>
        @endif
    </div>
    @if(!request()->has('collect'))
    <a href="{{ route('rentals.create') }}" class="btn-gold">
        <i class="fa-solid fa-plus"></i>
        <span>Add Rental</span>
    </a>
    @endif
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
            @if(request()->has('collect'))
                <input type="hidden" name="collect" value="1">
            @endif
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
                <a href="{{ route('rentals.index', request()->has('collect') ? ['collect' => 1] : []) }}" class="btn-reset">Reset</a>
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
                    <th style="width:230px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rentals as $key => $rental)
                    <tr>
                        <td>{{ method_exists($rentals, 'firstItem') ? ($rentals->firstItem() + $key) : ($key + 1) }}</td>
                        @if(auth()->user() && auth()->user()->isAdmin())
                            <td><strong style="color:#FFFFFF !important;">{{ $rental->firm->firm_name ?? '-' }}</strong></td>
                        @endif
                        <td>
                            <div style="font-weight:700;color:#FFFFFF !important;">{{ $rental->property->property_name ?? '-' }}</div>
                            @if($rental->property?->property_code)
                                <div style="font-size:11.5px;color:#94A3B8;">{{ $rental->property->property_code }}</div>
                            @endif
                            @if($rental->property?->unit_no)
                                <div style="font-size:11.5px;color:#60A5FA;font-weight:700;">Unit: {{ $rental->property->unit_no }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="tenant-name">{{ $rental->tenant_name }}</div>
                            <div class="tenant-mobile">{{ $rental->tenant_mobile }}</div>
                        </td>
                        <td class="amount-col">₹{{ number_format($rental->rent_amount, 0) }}</td>
                        <td style="color:#CBD5E1;">
                            @if($rental->security_deposit)
                                ₹{{ number_format($rental->security_deposit, 0) }}
                            @else
                                <span style="color:#94A3B8;">-</span>
                            @endif
                        </td>
                        <td style="color:#CBD5E1;">{{ \Carbon\Carbon::parse($rental->rent_start_date)->format('d M Y') }}</td>
                        <td style="color:#CBD5E1;">
                            {{ $rental->rent_end_date ? \Carbon\Carbon::parse($rental->rent_end_date)->format('d M Y') : '-' }}
                        </td>
                        <td>
                            @if($rental->rent_due_date)
                                <span class="due-date-chip">
                                    Day {{ $rental->rent_due_date }}
                                </span>
                            @else -
                            @endif
                        </td>
                        <td><span class="badge badge-{{ $rental->payment_status }}">{{ ucfirst($rental->payment_status) }}</span></td>
                        <td><span class="badge badge-{{ $rental->rental_status }}">{{ ucfirst($rental->rental_status) }}</span></td>
                        <td>
                            <div class="action-buttons-wrap">
                                @if(request()->has('collect'))
                                    <a href="{{ route('rental-payments.create', $rental->id) }}" class="btn-collect"
                                       title="Collect Rent">
                                        <i class="fa-solid fa-hand-holding-dollar"></i> Collect Rent
                                    </a>
                                    <a href="{{ route('rental-payments.index', $rental->id) }}" class="btn-history-link"
                                       title="Payment History">
                                        <i class="fa-solid fa-clock-rotate-left"></i> History
                                    </a>
                                @else
                                    <a href="{{ route('rental-payments.index', $rental->id) }}" class="btn-history-link"
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
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" align="center" style="padding:30px;color:#CBD5E1;">
                            No rental records found for this firm.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($rentals, 'links'))
        <div class="pagination-wrapper">
            {{ $rentals->appends(request()->query())->links() }}
        </div>
    @endif
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

