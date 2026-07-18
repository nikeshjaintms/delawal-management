@extends('admin.layouts.app')
@section('title','Material Master')
@section('page-title','Inventory Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:10px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:24px;box-shadow:var(--soft-shadow);}
    .filter-bar{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;align-items:flex-end;}
    .filter-group{display:flex;flex-direction:column;gap:5px;}
    .filter-label{font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.6px;}
    .filter-control{padding:9px 12px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);outline:none;background:#FFF;transition:var(--transition);min-width:160px;}
    .filter-control:focus{border-color:var(--gold);}
    .search-input{padding:9px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);outline:none;transition:var(--transition);min-width:220px;}
    .search-input:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .btn-search{background-color:var(--text-primary);color:#FFF;padding:9px 16px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:var(--font-primary);align-self:flex-end;}
    .btn-reset{padding:9px 12px;color:var(--text-secondary);text-decoration:none;font-size:13px;align-self:flex-end;}
    .table-container{width:100%;overflow-x:auto;}
    .premium-table{width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;}
    .premium-table th{padding:13px 14px;background:#F9FAFB;color:var(--text-secondary);font-weight:600;border-bottom:1px solid var(--border-color);font-size:11.5px;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;}
    .premium-table td{padding:14px;border-bottom:1px solid #F1F5F9;color:var(--text-primary);vertical-align:middle;}
    .premium-table tr:last-child td{border-bottom:none;}
    .premium-table tbody tr:hover{background-color:#F9FAFB;}
    .badge{display:inline-block;padding:4px 10px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase;}
    .badge-active{background:rgba(34,197,94,0.1);color:#16803D;}
    .badge-inactive{background:rgba(239,68,68,0.1);color:#B91C1C;}
    .stock-ok{color:#16803D;font-weight:700;}
    .stock-low{color:#B91C1C;font-weight:700;}
    .low-warn{display:inline-flex;align-items:center;gap:4px;background:rgba(239,68,68,0.08);color:#B91C1C;padding:3px 8px;border-radius:5px;font-size:11.5px;font-weight:700;}
    .action-links{display:flex;gap:10px;align-items:center;}
    .action-link{color:var(--text-secondary);text-decoration:none;font-size:13px;transition:var(--transition);display:inline-flex;align-items:center;gap:4px;}
    .action-link.view:hover{color:#0EA5E9;}
    .action-link.edit:hover{color:var(--gold);}
    .action-link.delete-btn{background:none;border:none;cursor:pointer;color:var(--text-secondary);font-family:var(--font-primary);font-size:13px;padding:0;}
    .action-link.delete-btn:hover{color:#EF4444;}
    .alert-success{background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);color:#16803D;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px;}
    .pagination-wrapper{margin-top:24px;display:flex;justify-content:center;}
</style>
<div class="crud-header">
    <div class="crud-title"><h2>Material Master</h2><p>Manage inventory materials with current stock tracking.</p></div>
    <a href="{{ route('materials.create') }}" class="btn-gold"><i class="fa-solid fa-plus"></i> Add Material</a>
</div>
@if(session('success'))<div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>@endif
<div class="card-box">
    <form method="GET" action="{{ route('materials.index') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input @error('search') is-invalid @enderror" placeholder="Material name, unit...">
        </div>
        <div class="filter-group">
            <span class="filter-label">Category</span>
            <select name="filter_category" class="filter-control @error('filter_category') is-invalid @enderror">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('filter_category')==$cat->id?'selected':'' }}>{{ $cat->category_name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-search">Filter</button>
        @if(request()->hasAny(['search','filter_category']))<a href="{{ route('materials.index') }}" class="btn-reset">Reset</a>@endif
    </form>
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>No</th><th>Material Name</th><th>Category</th><th>Unit</th>
                    <th>Opening Stock</th><th>Current Stock</th><th>Min Stock</th>
                    <th>Status</th><th style="width:180px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $key => $mat)
                <tr>
                    <td>{{ $materials->firstItem() + $key }}</td>
                    <td>
                        <strong>{{ $mat->material_name }}</strong>
                        @if($mat->current_stock <= $mat->minimum_stock && $mat->minimum_stock > 0)
                            <br><span class="low-warn"><i class="fa-solid fa-triangle-exclamation" style="font-size:10px;"></i> Low Stock</span>
                        @endif
                    </td>
                    <td>{{ $mat->materialCategory->category_name ?? '-' }}</td>
                    <td>{{ $mat->unit ?? '-' }}</td>
                    <td>{{ number_format($mat->opening_stock, 2) }}</td>
                    <td class="{{ $mat->current_stock <= $mat->minimum_stock && $mat->minimum_stock > 0 ? 'stock-low' : 'stock-ok' }}">
                        {{ number_format($mat->current_stock, 2) }} {{ $mat->unit }}
                    </td>
                    <td>{{ number_format($mat->minimum_stock, 2) }}</td>
                    <td><span class="badge badge-{{ $mat->status }}">{{ ucfirst($mat->status) }}</span></td>
                    <td>
                        <div class="table-action-buttons">
                            <a href="{{ route('materials.show', $mat->id) }}" class="btn-view"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('materials.edit', $mat->id) }}" class="btn-edit"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('materials.destroy', $mat->id) }}" method="POST" style="display:inline;" id="del-mat-{{ $mat->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" onclick="confirmDel({{ $mat->id }},'{{ addslashes($mat->material_name) }}','del-mat-')"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" align="center" style="padding:30px;color:var(--text-secondary);">No materials found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $materials->appends(request()->query())->links() }}</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDel(id,name,prefix){
    Swal.fire({title:'Delete?',html:'Delete <strong>'+name+'</strong>?',icon:'warning',showCancelButton:true,confirmButtonColor:'#EF4444',cancelButtonColor:'#64748B',confirmButtonText:'Yes, Delete',cancelButtonText:'Cancel',customClass:{popup:'swal-inv-popup'}})
    .then(r=>{if(r.isConfirmed)document.getElementById(prefix+id).submit();});
}
</script>
<style>.swal-inv-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection

