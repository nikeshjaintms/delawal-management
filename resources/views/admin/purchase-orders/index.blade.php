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
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 28px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 22px;
    border-radius: 12px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 18px rgba(37,99,235,0.38);
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37,99,235,0.52); }

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
.filter-group { display: flex !important; flex-direction: column !important; gap: 6px !important; flex: 1 1 0 !important; min-width: 120px !important; }
.filter-group.search-group { flex: 1.4 1 0 !important; min-width: 160px !important; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap !important; }

.filter-control, .search-input {
    width: 100% !important; padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important;
}
.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus, .filter-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(0.8) sepia(1) saturate(5) hue-rotate(185deg);
    cursor: pointer;
}

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 18px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    flex-shrink: 0 !important; white-space: nowrap !important; align-self: flex-end !important;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px; flex-shrink: 0 !important; white-space: nowrap !important; align-self: flex-end !important; transition: color .2s ease; }
.btn-reset:hover { color: #FFFFFF !important; }

.btn-export {
    background: linear-gradient(135deg, #10B981, #059669) !important; color: #FFFFFF !important;
    padding: 10px 16px !important; border-radius: 10px; border: 1px solid #10B981 !important;
    font-size: 13.5px; font-weight: 700; cursor: pointer; text-decoration: none !important;
    display: inline-flex; align-items: center; gap: 8px; flex-shrink: 0 !important; white-space: nowrap !important;
    align-self: flex-end !important; transition: all .2s ease; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
}
.btn-export:hover { background: linear-gradient(135deg, #059669, #047857) !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.55); }

.btn-export-pdf {
    background: linear-gradient(135deg, #EF4444, #DC2626) !important; color: #FFFFFF !important;
    padding: 10px 16px !important; border-radius: 10px; border: 1px solid #EF4444 !important;
    font-size: 13.5px; font-weight: 700; cursor: pointer; text-decoration: none !important;
    display: inline-flex; align-items: center; gap: 8px; flex-shrink: 0 !important; white-space: nowrap !important;
    align-self: flex-end !important; transition: all .2s ease; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
}
.btn-export-pdf:hover { background: linear-gradient(135deg, #DC2626, #B91C1C) !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(239, 68, 68, 0.55); }

.table-responsive-wrapper { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); }

.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
.premium-table th {
    padding: 16px 22px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: 0.9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 18px 22px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 14px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
    white-space: nowrap !important;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11.5px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-Draft { background: rgba(148, 163, 184, 0.18) !important; color: #CBD5E1 !important; border: 1px solid rgba(148, 163, 184, 0.35) !important; }
.badge-Pending { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-Approved { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.badge-Ordered { background: rgba(168, 85, 247, 0.18) !important; color: #C084FC !important; border: 1px solid rgba(168, 85, 247, 0.35) !important; }
.badge-Received { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-Cancelled { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.table-action-buttons { display: flex !important; flex-direction: row !important; align-items: center !important; gap: 10px !important; flex-wrap: nowrap !important; white-space: nowrap !important; justify-content: flex-end; }
.table-action-buttons form { display: inline-flex !important; margin: 0 !important; padding: 0 !important; }

.btn-view, a.btn-view {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important;
    border: 1px solid rgba(96, 165, 250, 0.30) !important; border-radius: 10px; text-decoration: none !important;
    font-size: 13px !important; font-weight: 700 !important; transition: all .2s ease; white-space: nowrap !important;
}
.btn-view:hover { background: #2563EB !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(37, 99, 235, 0.40); }

.btn-print, a.btn-print {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(16, 185, 129, 0.15) !important; color: #34D399 !important;
    border: 1px solid rgba(52, 211, 153, 0.30) !important; border-radius: 10px; text-decoration: none !important;
    font-size: 13px !important; font-weight: 700 !important; transition: all .2s ease; white-space: nowrap !important;
}
.btn-print:hover { background: #059669 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(5, 150, 105, 0.40); }

.btn-edit, a.btn-edit {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(245, 158, 11, 0.15) !important; color: #FBBF24 !important;
    border: 1px solid rgba(245, 158, 11, 0.30) !important; border-radius: 10px; text-decoration: none !important;
    font-size: 13px !important; font-weight: 700 !important; transition: all .2s ease; white-space: nowrap !important;
}
.btn-edit:hover { background: #D97706 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(217, 119, 6, 0.40); }

.btn-delete, button.btn-delete {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(239, 68, 68, 0.15) !important; color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.30) !important; border-radius: 10px; text-decoration: none !important;
    font-size: 13px !important; font-weight: 700 !important; transition: all .2s ease; cursor: pointer; white-space: nowrap !important;
}
.btn-delete:hover { background: #DC2626 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(220, 38, 38, 0.40); }

.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }

.btn-pdf {
    display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px;
    background: rgba(220, 38, 38, 0.15) !important; color: #F87171 !important;
    border: 1px solid rgba(220, 38, 38, 0.35) !important; border-radius: 12px;
    font-size: 14px; font-weight: 700; cursor: pointer; transition: all .25s ease;
    text-decoration: none !important;
}
.btn-pdf:hover { background: #DC2626 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220,38,38,0.45); }

@media print {
    body { background: #FFFFFF !important; color: #000 !important; }
    .sidebar, .topbar, .filter-bar, .pagination-wrapper, .btn-pdf, .btn-gold, .btn-edit, .btn-delete, .btn-view, .btn-print,
    .ambient-glow-wrapper, .sidebar-overlay, .action-col, .breadcrumb-nav { display: none !important; }
    .main-wrapper, .content-area, .content-body { margin: 0 !important; padding: 0 !important; }
    .card-box { background: #fff !important; border: none !important; box-shadow: none !important; padding: 0 !important; }
    .premium-table th { background: #1e293b !important; color: #FFFFFF !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .premium-table td { border-bottom: 1px solid #e2e8f0 !important; color: #1e293b !important; }
    .premium-table tr:nth-child(even) td { background: #f8fafc !important; }
    .print-header { display: block !important; text-align: center; padding: 16px 0 10px; border-bottom: 2px solid #1e293b; margin-bottom: 18px; }
    .print-header h1 { font-size: 22px; font-weight: 800; color: #1e293b; margin: 0; }
    .print-header p { font-size: 13px; color: #64748b; margin: 4px 0 0; }
    .status-badge, span[class*="badge"] { border: 1px solid #aaa !important; background: #f1f5f9 !important; color: #1e293b !important; }
}
</style>

<div class="print-header" style="display:none;">
    <h1><i class="fa-solid fa-file-invoice"></i> Purchase Orders</h1>
    <p>Manage and track procurement contracts and supplier orders &nbsp;|&nbsp; Printed on: {{ date('d M, Y H:i') }}</p>
</div>

<div class="crud-header">
    <div class="crud-title">
        <h2>Purchase Orders</h2>
        <p>Manage and track procurement contracts and supplier orders.</p>
    </div>
    <div style="display:flex; gap:10px; align-items:center;">
        <button type="button" class="btn-pdf" onclick="window.print()" title="Print / Save as PDF">
            <i class="fa-solid fa-file-pdf"></i> PDF
        </button>
        @if($authUser->hasPermission('purchase_order_add'))
        <a href="{{ route('purchase-orders.create') }}" class="btn-gold"><i class="fa-solid fa-plus"></i> Create Purchase Order</a>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert-success">
    <i class="fa-solid fa-circle-check"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('purchase-orders.index') }}" class="filter-bar">
        <div class="filter-group search-group">
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
            <span class="filter-label">Project</span>
            <select name="filter_project" class="filter-control">
                <option value="">All Projects</option>
                @foreach($projects as $p)<option value="{{ $p->id }}" {{ request('filter_project')==$p->id?'selected':'' }}>{{ $p->project_name }} ({{ $p->propertyMaster->property_name ?? 'Property' }})</option>@endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Contractor</span>
            <select name="filter_contractor" class="filter-control">
                <option value="">All Contractors</option>
                @if(isset($contractors))
                    @foreach($contractors as $con)
                        <option value="{{ $con->id }}" {{ request('filter_contractor') == $con->id ? 'selected' : '' }}>
                            {{ $con->contractor_name }}
                        </option>
                    @endforeach
                @endif
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

        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','filter_status','filter_project','filter_contractor','start_date','end_date']))
            <a href="{{ route('purchase-orders.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>
        @endif

        @if($authUser->hasPermission('purchase_order_export'))
        <a href="{{ route('purchase-orders.excel', request()->all()) }}" class="btn-export">
            <i class="fa-solid fa-file-excel"></i> Export CSV
        </a>
        <a href="{{ route('purchase-orders.pdf', request()->all()) }}" target="_blank" class="btn-export-pdf">
            <i class="fa-solid fa-file-pdf"></i> Print PDF Report
        </a>
        @endif
    </form>

    <div class="table-responsive-wrapper">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Firm</th>
                    <th>Project</th>
                    <th>Contractor</th>
                    <th>Supplier</th>
                    <th>PO Date</th>
                    <th>Delivery Date</th>
                    <th>Status</th>
                    <th style="text-align:right;">Grand Total</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseOrders as $po)
                <tr>
                    <td><strong style="color: #FFFFFF !important; font-weight: 700;">{{ $po->po_number }}</strong></td>
                    <td>{{ $po->firm->firm_name ?? '-' }}</td>
                    <td>{{ $po->project->project_name ?? '—' }}</td>
                    <td>
                        @if($po->contractor)
                            <span style="display: inline-flex; align-items: center; gap: 4px; color: #60A5FA; font-weight: 600;">
                                <i class="fa-solid fa-helmet-safety" style="font-size: 11px;"></i> {{ $po->contractor->contractor_name }}
                            </span>
                        @else
                            <span style="color: #94A3B8;">—</span>
                        @endif
                    </td>
                    <td>{{ $po->vendor->name ?? '-' }}</td>
                    <td>{{ $po->po_date ? $po->po_date->format('d M Y') : '-' }}</td>
                    <td>{{ $po->delivery_date ? $po->delivery_date->format('d M Y') : '—' }}</td>
                    <td><span class="badge badge-{{ $po->status }}">{{ $po->status }}</span></td>
                    <td style="text-align:right; font-weight:700; color: #34D399;">₹{{ number_format($po->grand_total, 2) }}</td>
                    <td style="text-align:right;">
                        <div class="table-action-buttons">
                            @if($authUser->hasPermission('purchase_order_view'))
                            <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn-view"><i class="fa-solid fa-eye"></i> View</a>
                            @endif
                            
                            @if($authUser->hasPermission('purchase_order_edit') && in_array($po->status, ['Draft', 'Pending']))
                            <a href="{{ route('purchase-orders.edit', $po->id) }}" class="btn-edit"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                            @endif

                            @if($authUser->hasPermission('purchase_order_print'))
                            <a href="{{ route('purchase-orders.print', $po->id) }}" target="_blank" class="btn-print"><i class="fa-solid fa-print"></i> Print</a>
                            @endif

                            @if($authUser->hasPermission('purchase_order_delete') && $po->status === 'Draft')
                            <form action="{{ route('purchase-orders.destroy', $po->id) }}" method="POST" style="display:inline;" id="del-po-{{ $po->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" onclick="confirmDel({{ $po->id }},'{{ addslashes($po->po_number) }}','del-po-')"><i class="fa-solid fa-trash-can"></i> Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #CBD5E1; padding: 36px;">No purchase orders found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($totalAmount > 0)
    <div style="margin-top:20px; text-align:right; font-size:15px; font-weight:700; color: #E2E8F0;">
        Total Amount: <span style="color:#34D399; font-size:17px; margin-left:6px;">₹{{ number_format($totalAmount, 2) }}</span>
    </div>
    @endif

    <div class="pagination-wrapper">
        {{ $purchaseOrders->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDel(id, name, formPrefix) {
        Swal.fire({
            title:'Delete Purchase Order?',
            html:'Are you sure you want to delete <strong>'+name+'</strong>?',
            icon:'warning',
            showCancelButton:true,
            confirmButtonColor:'#EF4444',
            cancelButtonColor:'#64748B',
            confirmButtonText:'Yes, Delete',
            cancelButtonText:'Cancel',
            customClass:{popup:'swal-inv-popup'}
        })
        .then(r=>{if(r.isConfirmed)document.getElementById(formPrefix+id).submit();});
    }
</script>
<style>.swal-inv-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;background:#101622!important;color:#FFF!important;border:1px solid rgba(255,255,255,0.15)!important;}</style>
@endsection

