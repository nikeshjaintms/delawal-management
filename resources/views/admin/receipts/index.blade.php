@extends('admin.layouts.app')
@section('title', 'Receipts')
@section('page-title', 'Receipt Management')
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

.filter-group { display: flex; flex-direction: column; gap: 5px; flex-shrink: 0; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; }

.filter-control, .search-input {
    padding: 9px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important; min-width: 140px;
}
select.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.filter-control::placeholder, .search-input::placeholder { color: #94A3B8 !important; }
.filter-control:focus, .search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 18px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    flex-shrink: 0 !important; white-space: nowrap !important; align-self: flex-end;
    display: inline-flex; align-items: center; gap: 6px;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px; flex-shrink: 0 !important; white-space: nowrap !important; transition: color .2s ease; align-self: flex-end; display: inline-flex; align-items: center; gap: 5px; }
.btn-reset:hover { color: #FFFFFF !important; }

/* Total Bar */
.total-bar {
    background: rgba(16, 22, 34, 0.75) !important;
    border: 1px solid rgba(59, 130, 246, 0.30) !important;
    border-radius: 16px; padding: 16px 22px; margin-bottom: 20px;
    display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
}
.total-bar .total-label { font-size: 12.5px; color: #CBD5E1 !important; font-weight: 600; }
.total-bar .total-amount { font-size: 21px; font-weight: 800; color: #60A5FA !important; }
.total-bar .rec-count { font-size: 12.5px; color: #94A3B8 !important; margin-left: auto; font-weight: 600; }

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

.receipt-no-chip { background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid rgba(59, 130, 246, 0.30) !important; display: inline-block; white-space: nowrap; }
.amount-col { font-weight: 800; color: #34D399 !important; }
.mode-chip { background: rgba(255, 255, 255, 0.08) !important; color: #CBD5E1 !important; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; border: 1px solid rgba(255, 255, 255, 0.12) !important; }
.ref-chip { background: rgba(255, 255, 255, 0.06) !important; color: #94A3B8 !important; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block; border: 1px solid rgba(255, 255, 255, 0.10) !important; }

.status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Receipt Management</h2>
        <p>Manage all payment receipts.</p>
    </div>
    <a href="{{ route('receipts.create') }}" class="btn-gold">
        <i class="fa-solid fa-plus"></i> Add Receipt
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
    <form method="GET" action="{{ route('receipts.index') }}" class="filter-bar">
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
        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="search-input @error('search') is-invalid @enderror" placeholder="Receipt no, received from...">
        </div>
        <div class="filter-group">
            <span class="filter-label">Status</span>
            <select name="filter_status" class="filter-control @error('filter_status') is-invalid @enderror">
                <option value="">All Status</option>
                <option value="active"   {{ request('filter_status') == 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('filter_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','filter_status','firm_id']))
            <a href="{{ route('receipts.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    {{-- Total Amount Bar --}}
    <div class="total-bar">
        <i class="fa-solid fa-file-invoice-dollar" style="color:#60A5FA;font-size:22px;"></i>
        <div>
            <div class="total-label">Total Receipts</div>
            <div class="total-amount">₹{{ number_format($totalAmount, 2) }}</div>
        </div>
        <div class="rec-count">
            <i class="fa-solid fa-list-ul"></i>
            {{ $receipts->total() }} record{{ $receipts->total() != 1 ? 's' : '' }} found
        </div>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Firm</th>
                    <th>Receipt No</th>
                    <th>Date</th>
                    <th>Received From</th>
                    <th>Amount</th>
                    <th>Payment Mode</th>
                    <th>Reference No</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:200px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $key => $receipt)
                <tr>
                    <td>{{ method_exists($receipts, 'firstItem') ? ($receipts->firstItem() + $key) : ($key + 1) }}</td>
                    <td><strong style="color:#FFFFFF !important;">{{ $receipt->firm_names }}</strong></td>
                    <td>
                        <span class="receipt-no-chip">{{ $receipt->receipt_no ?? '—' }}</span>
                    </td>
                    <td style="white-space:nowrap;color:#E2E8F0;">{{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d M Y') }}</td>
                    <td><strong style="color:#FFFFFF !important;">{{ $receipt->received_from ?? '—' }}</strong></td>
                    <td class="amount-col">₹{{ number_format($receipt->amount, 2) }}</td>
                    <td>
                        @if($receipt->payment_mode)
                            <span class="mode-chip">{{ $receipt->payment_mode }}</span>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($receipt->reference_no)
                            <span class="ref-chip">{{ $receipt->reference_no }}</span>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="status-badge badge-{{ $receipt->status ?? 'active' }}">
                            {{ ucfirst($receipt->status ?? 'active') }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons-wrap">
                            <a href="{{ route('receipts.show', $receipt->id) }}" class="btn-view">
                                <i class="fa fa-eye"></i> View
                            </a>
                            <a href="{{ route('receipts.edit', $receipt->id) }}" class="btn-edit">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('receipts.destroy', $receipt->id) }}" method="POST"
                                  style="display:inline;" id="del-rec-{{ $receipt->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete"
                                    onclick="confirmDelete({{ $receipt->id }}, '{{ addslashes($receipt->receipt_no ?? 'Receipt') }}')">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" align="center" style="padding:40px;color:#CBD5E1;">
                        <i class="fa-solid fa-file-invoice-dollar" style="font-size:28px;opacity:0.3;margin-bottom:8px;display:block;"></i>
                        No receipt records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($receipts, 'links'))
        <div class="pagination-wrapper">
            {{ $receipts->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Delete Receipt?',
        html: 'Delete receipt <strong>' + name + '</strong>?<br><small style="color:#64748B;">This action cannot be undone.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa fa-trash"></i> Yes, Delete',
        cancelButtonText: 'Cancel',
    }).then(r => { if (r.isConfirmed) document.getElementById('del-rec-' + id).submit(); });
}
</script>
@endsection

