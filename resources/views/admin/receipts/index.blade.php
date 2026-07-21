@extends('admin.layouts.app')
@section('title', 'Receipts')
@section('page-title', 'Receipt Management')
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
    .search-input{padding:9px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:var(--transition);min-width:220px;}
    .search-input:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .btn-search{background-color:var(--text-primary);color:#FFF;padding:9px 16px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:var(--font-primary);white-space:nowrap;align-self:flex-end;}
    .btn-search:hover{background-color:#1E293B;}
    .btn-reset{padding:9px 12px;color:var(--text-secondary);text-decoration:none;font-size:13px;font-weight:500;align-self:flex-end;}
    .btn-reset:hover{color:var(--text-primary);}
    .total-bar{background:linear-gradient(135deg,rgba(59,130,246,0.07) 0%,rgba(59,130,246,0.02) 100%);border:1px solid rgba(59,130,246,0.2);border-radius:10px;padding:14px 20px;margin-bottom:18px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;}
    .total-bar .total-label{font-size:12.5px;color:var(--text-secondary);}
    .total-bar .total-amount{font-size:20px;font-weight:800;color:#1D4ED8;}
    .total-bar .rec-count{font-size:12.5px;color:var(--text-secondary);margin-left:auto;}
    .table-container{width:100%;overflow-x:auto;}
    .premium-table{width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;}
    .premium-table th{padding:13px 14px;background:#F9FAFB;color:var(--text-secondary);font-weight:600;border-bottom:1px solid var(--border-color);font-size:11.5px;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;}
    .premium-table td{padding:14px;border-bottom:1px solid #F1F5F9;color:var(--text-primary);vertical-align:middle;}
    .premium-table tr:last-child td{border-bottom:none;}
    .premium-table tbody tr:hover{background-color:#F9FAFB;}
    .receipt-no-chip{background:rgba(59,130,246,0.08);color:#1D4ED8;padding:3px 10px;border-radius:6px;font-size:12px;font-weight:700;border:1px solid rgba(59,130,246,0.2);display:inline-block;white-space:nowrap;}
    .amount-col{font-weight:800;color:#1D4ED8;}
    .mode-chip{background:#F1F5F9;color:#475569;padding:3px 8px;border-radius:5px;font-size:12px;font-weight:600;display:inline-block;}
    .ref-chip{background:#F8FAFC;color:#64748B;padding:3px 8px;border-radius:5px;font-size:11.5px;font-weight:600;display:inline-block;border:1px solid var(--border-color);}
    .status-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.3px;}
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
        <i class="fa-solid fa-file-invoice-dollar" style="color:#1D4ED8;font-size:18px;"></i>
        <div>
            <div class="total-label">Total Receipts</div>
            <div class="total-amount">₹{{ number_format($totalAmount, 2) }}</div>
        </div>
        <div class="rec-count">
            <i class="fa-solid fa-list-ul" style="color:var(--text-secondary);"></i>
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
                    <th style="width:160px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $key => $receipt)
                <tr>
                    <td>{{ $receipts->firstItem() + $key }}</td>
                    <td><strong style="color:#0F172A;">{{ $receipt->firm->firm_name ?? '—' }}</strong></td>
                    <td>
                        <span class="receipt-no-chip">{{ $receipt->receipt_no ?? '—' }}</span>
                    </td>
                    <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d M Y') }}</td>
                    <td style="font-weight:600;">{{ $receipt->received_from ?? '—' }}</td>
                    <td class="amount-col">₹{{ number_format($receipt->amount, 2) }}</td>
                    <td>
                        @if($receipt->payment_mode)
                            <span class="mode-chip">{{ $receipt->payment_mode }}</span>
                        @else
                            <span style="color:var(--text-secondary);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($receipt->reference_no)
                            <span class="ref-chip">{{ $receipt->reference_no }}</span>
                        @else
                            <span style="color:var(--text-secondary);">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="status-badge badge-{{ $receipt->status ?? 'active' }}">
                            {{ ucfirst($receipt->status ?? 'active') }}
                        </span>
                    </td>
                    <td>
                        <div class="table-action-buttons">
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
                    <td colspan="9" align="center" style="padding:40px;color:var(--text-secondary);">
                        <i class="fa-solid fa-file-invoice-dollar" style="font-size:28px;opacity:0.3;margin-bottom:8px;display:block;"></i>
                        No receipt records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $receipts->appends(request()->query())->links() }}
    </div>
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

