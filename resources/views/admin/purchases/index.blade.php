@extends('admin.layouts.app')
@section('title', 'Purchases')
@section('page-title', 'Purchase Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:10px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:24px;box-shadow:var(--soft-shadow);}
    .filter-bar{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;align-items:flex-end;}
    .filter-group{display:flex;flex-direction:column;gap:5px;}
    .filter-label{font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.6px;}
    .filter-control{padding:9px 12px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);color:var(--text-primary);outline:none;background:#FFF;transition:var(--transition);min-width:130px;}
    .filter-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .search-input{padding:9px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:var(--transition);min-width:200px;}
    .search-input:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .btn-search{background-color:var(--text-primary);color:#FFF;padding:9px 16px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:var(--font-primary);white-space:nowrap;align-self:flex-end;}
    .btn-search:hover{background-color:#1E293B;}
    .btn-reset{padding:9px 12px;color:var(--text-secondary);text-decoration:none;font-size:13px;font-weight:500;align-self:flex-end;}
    .btn-reset:hover{color:var(--text-primary);}
    .total-bar{background:linear-gradient(135deg,rgba(212,175,55,0.06) 0%,rgba(212,175,55,0.02) 100%);border:1px solid rgba(212,175,55,0.2);border-radius:10px;padding:14px 20px;margin-bottom:18px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;}
    .total-bar .total-label{font-size:12.5px;color:var(--text-secondary);}
    .total-bar .total-amount{font-size:20px;font-weight:800;color:var(--gold);}
    .total-bar .rec-count{font-size:12.5px;color:var(--text-secondary);margin-left:auto;}
    .table-container{width:100%;overflow-x:auto;}
    .premium-table{width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;}
    .premium-table th{padding:13px 14px;background:#F9FAFB;color:var(--text-secondary);font-weight:600;border-bottom:1px solid var(--border-color);font-size:11.5px;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;}
    .premium-table td{padding:14px;border-bottom:1px solid #F1F5F9;color:var(--text-primary);vertical-align:middle;}
    .premium-table tr:last-child td{border-bottom:none;}
    .premium-table tbody tr:hover{background-color:#F9FAFB;}
    .amount-chip{background:linear-gradient(135deg,rgba(212,175,55,0.15) 0%,rgba(212,175,55,0.07) 100%);color:#92710A;padding:4px 10px;border-radius:6px;font-size:12.5px;font-weight:800;border:1px solid rgba(212,175,55,0.25);display:inline-block;white-space:nowrap;}
    .mode-chip{background:#F1F5F9;color:#475569;padding:3px 8px;border-radius:5px;font-size:12px;font-weight:600;display:inline-block;}
    .status-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.3px;}
    .badge-unpaid{background:rgba(239,68,68,0.1);color:#DC2626;}
    .badge-partial{background:rgba(245,158,11,0.1);color:#B45309;}
    .badge-paid{background:rgba(16,185,129,0.1);color:#059669;}
    .badge-active{background:rgba(16,185,129,0.1);color:#059669;}
    .badge-inactive{background:rgba(239,68,68,0.1);color:#DC2626;}
    .action-links{display:flex;gap:10px;align-items:center;white-space:nowrap;}
    .action-link{color:var(--text-secondary);text-decoration:none;font-size:13px;transition:var(--transition);display:inline-flex;align-items:center;gap:4px;}
    .action-link.view:hover{color:#0EA5E9;}
    .action-link.edit:hover{color:var(--gold);}
    .action-link.delete-btn{background:none;border:none;cursor:pointer;color:var(--text-secondary);font-family:var(--font-primary);font-size:13px;padding:0;}
    .action-link.delete-btn:hover{color:#EF4444;}
    .alert-success{background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);color:#16803D;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px;}
    .pagination-wrapper{margin-top:24px;display:flex;justify-content:center;}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Purchase Management</h2>
        <p>Track and manage all purchase records.</p>
    </div>
    <a href="{{ route('purchases.create') }}" class="btn-gold">
        <i class="fa-solid fa-plus"></i> Add Purchase
    </a>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="card-box">
    {{-- Filters --}}
    <form method="GET" action="{{ route('purchases.index') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="search-input @error('search') is-invalid @enderror" placeholder="Item name, vendor, ref no...">
        </div>
        <div class="filter-group">
            <span class="filter-label">Payment Status</span>
            <select name="filter_payment_status" class="filter-control @error('filter_payment_status') is-invalid @enderror">
                <option value="">All Status</option>
                @foreach(['unpaid','partial','paid'] as $ps)
                    <option value="{{ $ps }}" {{ request('filter_payment_status') == $ps ? 'selected' : '' }}>
                        {{ ucfirst($ps) }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','filter_payment_status']))
            <a href="{{ route('purchases.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    {{-- Total Amount Bar --}}
    <div class="total-bar">
        <i class="fa-solid fa-cart-plus" style="color:var(--gold);font-size:18px;"></i>
        <div>
            <div class="total-label">Total Purchase</div>
            <div class="total-amount">₹{{ number_format($totalAmount, 2) }}</div>
        </div>
        <div class="rec-count">
            <i class="fa-solid fa-list-ul" style="color:var(--text-secondary);"></i>
            {{ $purchases->total() }} record{{ $purchases->total() != 1 ? 's' : '' }} found
        </div>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Item Name</th>
                    <th>Vendor</th>
                    <th>Qty</th>
                    <th>Amount</th>
                    <th>Payment Mode</th>
                    <th style="text-align:center;">Payment Status</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:160px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $key => $purchase)
                <tr>
                    <td>{{ $purchases->firstItem() + $key }}</td>
                    <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</td>
                    <td style="font-weight:700;">{{ $purchase->item_name }}</td>
                    <td>{{ $purchase->vendor ? $purchase->vendor->name : '—' }}</td>
                    <td>{{ $purchase->quantity ?? '—' }}</td>
                    <td>
                        <span class="amount-chip">₹{{ number_format($purchase->purchase_amount, 2) }}</span>
                    </td>
                    <td>
                        @if($purchase->payment_mode)
                            <span class="mode-chip">{{ $purchase->payment_mode }}</span>
                        @else
                            <span style="color:var(--text-secondary);">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="status-badge badge-{{ $purchase->payment_status ?? 'unpaid' }}">
                            {{ ucfirst($purchase->payment_status ?? 'unpaid') }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <span class="status-badge badge-{{ $purchase->status ?? 'active' }}">
                            {{ ucfirst($purchase->status ?? 'active') }}
                        </span>
                    </td>
                    <td>
                        <div class="table-action-buttons">
                            <a href="{{ route('purchases.show', $purchase->id) }}" class="btn-view">
                                <i class="fa fa-eye"></i> View
                            </a>
                            <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn-edit">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST"
                                  style="display:inline;" id="del-pur-{{ $purchase->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete"
                                    onclick="confirmDelete({{ $purchase->id }}, '{{ addslashes($purchase->item_name) }}')">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" align="center" style="padding:40px;color:var(--text-secondary);">
                        <i class="fa-solid fa-cart-plus" style="font-size:28px;opacity:0.3;margin-bottom:8px;display:block;"></i>
                        No purchase records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $purchases->appends(request()->query())->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Delete Purchase?',
        html: 'Delete <strong>' + name + '</strong>?<br><small style="color:#64748B;">This action cannot be undone.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa fa-trash"></i> Yes, Delete',
        cancelButtonText: 'Cancel',
    }).then(r => { if (r.isConfirmed) document.getElementById('del-pur-' + id).submit(); });
}
</script>
@endsection

