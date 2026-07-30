@extends('admin.layouts.app')
@section('title', 'Purchase Orders')
@section('page-title', 'Inventory & Purchasing')
@php
    $user = Auth::user();
    if (!$user && session('login_type') === 'firm' && session('firm_id')) {
        $authUser = new class {
            public function isAdmin()        { return true; }
            public function hasPermission($p){ return true; }
            public $role = null;
            public $name = '';
            public $firm_id = null;
        };
        $authUser->name = session('firm_name', 'Firm');
        $authUser->firm_id = session('firm_id');
    } else {
        $authUser = $user;
    }
@endphp
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:10px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);color:#FFF;}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:24px;box-shadow:var(--soft-shadow);}
    .filter-bar{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;align-items:flex-end;}
    .filter-group{display:flex;flex-direction:column;gap:5px;}
    .filter-label{font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.6px;}
    .filter-control{padding:9px 12px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);outline:none;background:#FFF;transition:var(--transition);min-width:160px;}
    .filter-control:focus{border-color:var(--gold);}
    .search-input{padding:9px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);outline:none;transition:var(--transition);min-width:220px;}
    .search-input:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .btn-search{background-color:var(--text-primary);color:#FFF;padding:9px 16px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:var(--font-primary);align-self:flex-end;height:38px;}
    .btn-reset{padding:9px 12px;color:var(--text-secondary);text-decoration:none;font-size:13px;align-self:flex-end;}
    .btn-export{background-color:#059669;color:#FFF;padding:9px 16px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:var(--font-primary);align-self:flex-end;height:38px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
    .btn-export:hover{background-color:#047857;color:#FFF;}
    .table-container{width:100%;overflow-x:auto;}
    .premium-table{width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;}
    .premium-table th{padding:13px 14px;background:#F9FAFB;color:var(--text-secondary);font-weight:600;border-bottom:1px solid var(--border-color);font-size:11.5px;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;}
    .premium-table td{padding:14px;border-bottom:1px solid #F1F5F9;color:var(--text-primary);vertical-align:middle;}
    .premium-table tr:last-child td{border-bottom:none;}
    .premium-table tbody tr:hover{background-color:#F9FAFB;}
    .badge{display:inline-block;padding:4px 10px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase;}
    
    .badge-Draft{background:rgba(100,116,139,0.1);color:#64748B;}
    .badge-Pending{background:rgba(245,158,11,0.1);color:#D97706;}
    .badge-Approved{background:rgba(59,130,246,0.1);color:#2563EB;}
    .badge-Ordered{background:rgba(139,92,246,0.1);color:#7C3AED;}
    .badge-Received{background:rgba(16,185,129,0.1);color:#059669;}
    .badge-Cancelled{background:rgba(239,68,68,0.1);color:#DC2626;}

    .alert-success{background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);color:#16803D;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px;}
    .pagination-wrapper{margin-top:24px;display:flex;justify-content:center;}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Purchase Orders</h2>
        <p>Manage and track procurement contracts and supplier orders.</p>
    </div>
    @if($authUser->hasPermission('purchase_order_add'))
    <a href="{{ route('purchase-orders.create') }}" class="btn-gold"><i class="fa-solid fa-plus"></i> Create Purchase Order</a>
    @endif
</div>

@if(session('success'))
<div class="alert-success">
    <i class="fa-solid fa-circle-check"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('purchase-orders.index') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="PO number, supplier name...">
        </div>
        
        <div class="filter-group">
            <span class="filter-label">Status</span>
            <select name="filter_status" class="filter-control">
                <option value="">All Statuses</option>
                <option value="Draft" {{ request('filter_status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                <option value="Pending" {{ request('filter_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Approved" {{ request('filter_status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="Ordered" {{ request('filter_status') == 'Ordered' ? 'selected' : '' }}>Ordered</option>
                <option value="Received" {{ request('filter_status') == 'Received' ? 'selected' : '' }}>Received</option>
                <option value="Cancelled" {{ request('filter_status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Start Date</span>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="filter-control">
        </div>

        <div class="filter-group">
            <span class="filter-label">End Date</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="filter-control">
        </div>

        <button type="submit" class="btn-search">Filter</button>
        @if(request()->hasAny(['search','filter_status','start_date','end_date']))
            <a href="{{ route('purchase-orders.index') }}" class="btn-reset">Reset</a>
        @endif

        @if($authUser->hasPermission('purchase_order_export'))
        <a href="{{ route('purchase-orders.excel', request()->all()) }}" class="btn-export">
            <i class="fa-solid fa-file-excel"></i> Export CSV
        </a>
        <a href="{{ route('purchase-orders.pdf', request()->all()) }}" target="_blank" class="btn-export" style="background-color:#EF4444;">
            <i class="fa-solid fa-file-pdf"></i> Print PDF Report
        </a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Firm</th>
                    <th>Supplier</th>
                    <th>PO Date</th>
                    <th>Delivery Date</th>
                    <th>Status</th>
                    <th style="text-align:right;">Grand Total</th>
                    <th style="width:220px; text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseOrders as $po)
                <tr>
                    <td><strong>{{ $po->po_number }}</strong></td>
                    <td>{{ $po->firm->firm_name ?? '-' }}</td>
                    <td>{{ $po->vendor->name ?? '-' }}</td>
                    <td>{{ $po->po_date ? $po->po_date->format('d M Y') : '-' }}</td>
                    <td>{{ $po->delivery_date ? $po->delivery_date->format('d M Y') : '—' }}</td>
                    <td><span class="badge badge-{{ $po->status }}">{{ $po->status }}</span></td>
                    <td style="text-align:right; font-weight:700;">₹{{ number_format($po->grand_total, 2) }}</td>
                    <td>
                        <div class="table-action-buttons" style="justify-content: center;">
                            @if($authUser->hasPermission('purchase_order_view'))
                            <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn-view"><i class="fa fa-eye"></i> View</a>
                            @endif
                            
                            @if($authUser->hasPermission('purchase_order_edit') && in_array($po->status, ['Draft', 'Pending']))
                            <a href="{{ route('purchase-orders.edit', $po->id) }}" class="btn-edit"><i class="fa fa-edit"></i> Edit</a>
                            @endif

                            @if($authUser->hasPermission('purchase_order_print'))
                            <a href="{{ route('purchase-orders.print', $po->id) }}" target="_blank" class="btn-view" style="color:#059669;"><i class="fa fa-print"></i> Print</a>
                            @endif

                            @if($authUser->hasPermission('purchase_order_delete') && $po->status === 'Draft')
                            <form action="{{ route('purchase-orders.destroy', $po->id) }}" method="POST" style="display:inline;" id="del-po-{{ $po->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" onclick="confirmDel({{ $po->id }},'{{ $po->po_number }}','del-po-')"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 24px;">No purchase orders found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($totalAmount > 0)
    <div style="margin-top:20px; text-align:right; font-size:15px; font-weight:700;">
        Total Amount: <span style="color:#059669;">₹{{ number_format($totalAmount, 2) }}</span>
    </div>
    @endif

    <div class="pagination-wrapper">
        {{ $purchaseOrders->links() }}
    </div>
</div>

<script>
    function confirmDel(id, name, formPrefix) {
        if (confirm('Are you sure you want to delete ' + name + '?')) {
            document.getElementById(formPrefix + id).submit();
        }
    }
</script>
@endsection
