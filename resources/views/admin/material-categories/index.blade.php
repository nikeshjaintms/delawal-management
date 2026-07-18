@extends('admin.layouts.app')
@section('title','Material Categories')
@section('page-title','Inventory Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:10px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:24px;box-shadow:var(--soft-shadow);}
    .filter-bar{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
    .search-input{padding:10px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:13.5px;font-family:var(--font-primary);outline:none;transition:var(--transition);min-width:260px;}
    .search-input:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .btn-search{background-color:var(--text-primary);color:#FFF;padding:10px 16px;border-radius:8px;border:none;font-size:13.5px;font-weight:600;cursor:pointer;font-family:var(--font-primary);}
    .btn-reset{padding:10px 12px;color:var(--text-secondary);text-decoration:none;font-size:13.5px;font-weight:500;}
    .btn-reset:hover{color:var(--text-primary);}
    .table-container{width:100%;overflow-x:auto;}
    .premium-table{width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;}
    .premium-table th{padding:13px 14px;background:#F9FAFB;color:var(--text-secondary);font-weight:600;border-bottom:1px solid var(--border-color);font-size:11.5px;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;}
    .premium-table td{padding:14px;border-bottom:1px solid #F1F5F9;color:var(--text-primary);vertical-align:middle;}
    .premium-table tr:last-child td{border-bottom:none;}
    .premium-table tbody tr:hover{background-color:#F9FAFB;}
    .badge{display:inline-block;padding:4px 10px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase;}
    .badge-active{background:rgba(34,197,94,0.1);color:#16803D;}
    .badge-inactive{background:rgba(239,68,68,0.1);color:#B91C1C;}
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
    <div class="crud-title"><h2>Material Categories</h2><p>Manage inventory material categories firm-wise.</p></div>
    <a href="{{ route('material-categories.create') }}" class="btn-gold"><i class="fa-solid fa-plus"></i> Add Category</a>
</div>
@if(session('success'))<div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>@endif
<div class="card-box">
    <form method="GET" action="{{ route('material-categories.index') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" class="search-input @error('search') is-invalid @enderror" placeholder="Search by category name or description...">
        <button type="submit" class="btn-search">Search</button>
        @if(request('search'))<a href="{{ route('material-categories.index') }}" class="btn-reset">Reset</a>@endif
    </form>
    <div class="table-container">
        <table class="premium-table">
            <thead><tr><th>No</th><th>Category Name</th><th>Description</th><th>Status</th><th style="width:180px;">Action</th></tr></thead>
            <tbody>
                @forelse($categories as $key => $cat)
                <tr>
                    <td>{{ $categories->firstItem() + $key }}</td>
                    <td><strong>{{ $cat->category_name }}</strong></td>
                    <td style="color:var(--text-secondary);font-size:13px;">{{ $cat->description ? \Illuminate\Support\Str::limit($cat->description, 60) : '-' }}</td>
                    <td><span class="badge badge-{{ $cat->status }}">{{ ucfirst($cat->status) }}</span></td>
                    <td>
                        <div class="table-action-buttons">
                            <a href="{{ route('material-categories.show', $cat->id) }}" class="btn-view"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('material-categories.edit', $cat->id) }}" class="btn-edit"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('material-categories.destroy', $cat->id) }}" method="POST" style="display:inline;" id="del-cat-{{ $cat->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" onclick="confirmDel({{ $cat->id }},'{{ addslashes($cat->category_name) }}','del-cat-')"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" align="center" style="padding:30px;color:var(--text-secondary);">No material categories found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $categories->appends(request()->query())->links() }}</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDel(id, name, prefix) {
    Swal.fire({ title:'Delete?', html:'Delete <strong>'+name+'</strong>?<br><small style="color:#64748B;">This cannot be undone.</small>', icon:'warning', showCancelButton:true, confirmButtonColor:'#EF4444', cancelButtonColor:'#64748B', confirmButtonText:'<i class="fa fa-trash"></i> Yes, Delete', cancelButtonText:'Cancel', customClass:{popup:'swal-inv-popup'} })
    .then(r => { if(r.isConfirmed) document.getElementById(prefix+id).submit(); });
}
</script>
<style>.swal-inv-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection

