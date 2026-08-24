@extends('admin.layouts.app')
@section('title','Material Master')
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
    display: flex; gap: 14px; align-items: flex-end; margin-bottom: 24px;
    background: rgba(255, 255, 255, 0.04) !important; padding: 16px 20px !important;
    border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
    width: 100%; flex-wrap: nowrap !important;
}
.filter-group { display: flex; flex-direction: column; gap: 6px; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; }

.filter-control, .search-input {
    padding: 11px 16px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
}
.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.search-input { min-width: 220px; }
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus, .filter-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 22px;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    flex-shrink: 0; white-space: nowrap !important; align-self: flex-end;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 11px 14px; flex-shrink: 0; white-space: nowrap !important; align-self: flex-end; transition: color .2s ease; }
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

.stock-ok { color: #34D399 !important; font-weight: 700; }
.stock-low { color: #F87171 !important; font-weight: 700; }
.low-warn { display: inline-flex; align-items: center; gap: 4px; background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; padding: 3px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 700; margin-top: 4px; }

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
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','filter_category']))<a href="{{ route('materials.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>@endif
    </form>

    <div class="table-responsive-wrapper">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>No</th><th>Material Name</th><th>Category</th><th>Unit</th>
                    <th>Opening Stock</th><th>Current Stock</th><th>Min Stock</th>
                    <th>Status</th><th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $key => $mat)
                <tr>
                    <td>{{ method_exists($materials, 'firstItem') ? ($materials->firstItem() + $key) : ($key + 1) }}</td>
                    <td>
                        <strong style="color: #FFFFFF !important; font-weight: 700; white-space: nowrap !important;">{{ $mat->material_name }}</strong>
                        @if($mat->current_stock <= $mat->minimum_stock && $mat->minimum_stock > 0)
                            <br><span class="low-warn"><i class="fa-solid fa-triangle-exclamation" style="font-size:10px;"></i> Low Stock</span>
                        @endif
                    </td>
                    <td style="white-space: nowrap !important;">{{ $mat->materialCategory->category_name ?? '-' }}</td>
                    <td style="white-space: nowrap !important;">{{ $mat->unit ?? '-' }}</td>
                    <td style="white-space: nowrap !important;">{{ number_format($mat->opening_stock, 2) }}</td>
                    <td class="{{ $mat->current_stock <= $mat->minimum_stock && $mat->minimum_stock > 0 ? 'stock-low' : 'stock-ok' }}" style="white-space: nowrap !important;">
                        {{ number_format($mat->current_stock, 2) }} {{ $mat->unit }}
                    </td>
                    <td style="white-space: nowrap !important;">{{ number_format($mat->minimum_stock, 2) }}</td>
                    <td><span class="badge badge-{{ $mat->status }}">{{ ucfirst($mat->status) }}</span></td>
                    <td style="text-align: right;">
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
                <tr><td colspan="9" align="center" style="padding:30px;color: #CBD5E1;">No materials found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($materials, 'links'))
        <div class="pagination-wrapper">{{ $materials->appends(request()->query())->links() }}</div>
    @endif
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

