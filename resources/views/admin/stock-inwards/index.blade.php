@extends('admin.layouts.app')
@section('title','Stock Inward')
@section('page-title','Inventory Management')
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
    .filter-control{padding:9px 12px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);outline:none;background:#FFF;transition:var(--transition);min-width:150px;}
    .filter-control:focus{border-color:var(--gold);}
    .search-input{padding:9px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);outline:none;transition:var(--transition);min-width:200px;}
    .search-input:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .btn-search{background-color:var(--text-primary);color:#FFF;padding:9px 16px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:var(--font-primary);align-self:flex-end;height:38px;}
    .btn-reset{padding:9px 12px;color:var(--text-secondary);text-decoration:none;font-size:13px;align-self:flex-end;}
    .table-container{width:100%;overflow-x:auto;}
    .premium-table{width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;}
    .premium-table th{padding:13px 14px;background:#F9FAFB;color:var(--text-secondary);font-weight:600;border-bottom:1px solid var(--border-color);font-size:11.5px;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;}
    .premium-table td{padding:14px;border-bottom:1px solid #F1F5F9;color:var(--text-primary);vertical-align:middle;}
    .premium-table tr:last-child td{border-bottom:none;}
    .premium-table tbody tr:hover{background-color:#F9FAFB;}
    .qty-chip{background:rgba(34,197,94,0.1);color:#16803D;padding:3px 9px;border-radius:6px;font-size:12px;font-weight:700;display:inline-block;}
    .amount-col{font-weight:700;}
    .alert-success{background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);color:#16803D;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px;}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Stock Inward</h2>
        <p>Record material purchases, PO receipts, and stock inward transactions.</p>
    </div>
    <a href="{{ route('stock-inwards.create') }}" class="btn-gold"><i class="fa-solid fa-plus"></i> Add Inward</a>
</div>

@if(session('success'))
<div class="alert-success">
    <i class="fa-solid fa-circle-check"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('stock-inwards.index') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Inward No, supplier, bill no...">
        </div>
        <div class="filter-group">
            <span class="filter-label">Material</span>
            <select name="filter_material" class="filter-control">
                <option value="">All Materials</option>
                @foreach($materials as $m)<option value="{{ $m->id }}" {{ request('filter_material')==$m->id?'selected':'' }}>{{ $m->material_name }}</option>@endforeach
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
            <span class="filter-label">Date</span>
            <input type="date" name="filter_date" value="{{ request('filter_date') }}" class="filter-control">
        </div>
        <button type="submit" class="btn-search">Filter</button>
        @if(request()->hasAny(['search','filter_material','filter_project','filter_date']))<a href="{{ route('stock-inwards.index') }}" class="btn-reset">Reset</a>@endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>IMIR Number</th>
                    <th>Date</th>
                    <th>Ref. PO</th>
                    <th>Supplier</th>
                    <th>Bill / Invoice No</th>
                    <th>Project</th>
                    <th>Material (Sample)</th>
                    <th>Total items</th>
                    <th style="width:220px; text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inwards as $inw)
                @php
                    // Count items that share the same inward number
                    $itemCount = 1;
                    $sampleMaterial = $inw->material->material_name ?? '—';
                    if ($inw->inward_number) {
                        $itemCount = \App\Models\StockInward::where('inward_number', $inw->inward_number)->count();
                    }
                @endphp
                <tr>
                    <td><strong>{{ $inw->inward_number ?: 'IMIR-'.$inw->id }}</strong></td>
                    <td style="white-space:nowrap;">{{ $inw->inward_date->format('d M Y') }}</td>
                    <td>{{ $inw->purchaseOrder ? $inw->purchaseOrder->po_number : 'Manual' }}</td>
                    <td>{{ $inw->supplier_name ?: '—' }}</td>
                    <td>{{ $inw->bill_no ?: '—' }}</td>
                    <td>{{ $inw->project->project_name ?? ($inw->property->property_name ?? 'General') }}</td>
                    <td>{{ $sampleMaterial }}</td>
                    <td style="font-weight:700; text-align:center;">{{ $itemCount }}</td>
                    <td>
                        <div class="table-action-buttons" style="justify-content: center;">
                            <a href="{{ route('stock-inwards.show', $inw->id) }}" class="btn-view"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('stock-inwards.print', $inw->id) }}" target="_blank" class="btn-view" style="color:#059669;"><i class="fa fa-print"></i> Print</a>
                            @if(!$inw->purchase_order_id)
                            <a href="{{ route('stock-inwards.edit', $inw->id) }}" class="btn-edit"><i class="fa fa-edit"></i> Edit</a>
                            @endif
                            <form action="{{ route('stock-inwards.destroy', $inw->id) }}" method="POST" style="display:inline;" id="del-in-{{ $inw->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" onclick="confirmDel({{ $inw->id }},'{{ $inw->inward_number ?: $inw->id }}','del-in-')"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" align="center" style="padding:30px;color:var(--text-secondary);">No stock inward records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">{{ $inwards->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDel(id,name,prefix){
    Swal.fire({
        title:'Delete Inward Transaction?',
        html:'Are you sure you want to delete transaction <strong>'+name+'</strong>?<br><small style="color:#64748B;">All related items will be deleted and stock will be reversed.</small>',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#EF4444',
        cancelButtonColor:'#64748B',
        confirmButtonText:'Yes, Delete',
        cancelButtonText:'Cancel',
        customClass:{popup:'swal-inv-popup'}
    })
    .then(r=>{if(r.isConfirmed)document.getElementById(prefix+id).submit();});
}
</script>
<style>.swal-inv-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection
