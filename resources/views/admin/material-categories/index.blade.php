@extends('admin.layouts.app')
@section('title','Material Categories')
@section('page-title','Inventory Management')
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
    display: flex; gap: 12px; align-items: center; margin-bottom: 24px;
    background: rgba(255, 255, 255, 0.04) !important; padding: 16px 20px !important;
    border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
    width: 100%; flex-wrap: nowrap !important;
}
.search-input {
    flex: 1; max-width: 520px; padding: 11px 16px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
}
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 22px;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    flex-shrink: 0; white-space: nowrap !important;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 14px; flex-shrink: 0; white-space: nowrap !important; transition: color .2s ease; }
.btn-reset:hover { color: #FFFFFF !important; }

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

.badge { display: inline-block; padding: 5px 14px; font-size: 11.5px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.table-action-buttons { display: flex !important; flex-direction: row !important; align-items: center !important; gap: 10px !important; flex-wrap: nowrap !important; white-space: nowrap !important; justify-content: flex-end; }
.table-action-buttons form { display: inline-flex !important; margin: 0 !important; padding: 0 !important; }

.btn-view, a.btn-view {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important;
    border: 1px solid rgba(96, 165, 250, 0.30) !important; border-radius: 10px; text-decoration: none !important;
    font-size: 13px !important; font-weight: 700 !important; transition: all .2s ease; white-space: nowrap !important;
}
.btn-view:hover { background: #2563EB !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(37, 99, 235, 0.40); }

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
</style>

<div class="crud-header">
    <div class="crud-title"><h2>Material Categories</h2><p>Manage inventory material categories firm-wise.</p></div>
    <a href="{{ route('material-categories.create') }}" class="btn-gold"><i class="fa-solid fa-plus"></i> Add Category</a>
</div>

@if(session('success'))<div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>@endif

<div class="card-box">
    <form method="GET" action="{{ route('material-categories.index') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" class="search-input @error('search') is-invalid @enderror" placeholder="Search by category name or description...">
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        @if(request('search'))<a href="{{ route('material-categories.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>@endif
    </form>

    <div class="table-responsive-wrapper">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $key => $cat)
                <tr>
                    <td>{{ $categories->firstItem() + $key }}</td>
                    <td><strong style="color: #FFFFFF !important; font-weight: 700; white-space: nowrap !important;">{{ $cat->category_name }}</strong></td>
                    <td style="color: #CBD5E1 !important; font-size: 13.5px;">{{ $cat->description ? \Illuminate\Support\Str::limit($cat->description, 60) : '-' }}</td>
                    <td><span class="badge badge-{{ $cat->status }}">{{ ucfirst($cat->status) }}</span></td>
                    <td style="text-align: right;">
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
                <tr><td colspan="5" align="center" style="padding:30px;color: #CBD5E1;">No material categories found.</td></tr>
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

