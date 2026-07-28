@extends('admin.layouts.app')
@section('title','Stock Outward')
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
    .alert-success{background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);color:#16803D;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px;}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Stock Outward</h2>
        <p>Record material usage, stock issues to construction sites, and dispatch gate passes.</p>
    </div>
    <a href="{{ route('stock-outwards.create') }}" class="btn-gold"><i class="fa-solid fa-plus"></i> Add Outward</a>
</div>

@if(session('success'))
<div class="alert-success">
    <i class="fa-solid fa-circle-check"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('stock-outwards.index') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Outward No, SI reference, used for...">
        </div>
        <div class="filter-group">
            <span class="filter-label">Material</span>
            <select name="filter_material" class="filter-control">
                <option value="">All Materials</option>
                @foreach($materials as $m)<option value="{{ $m->id }}" {{ request('filter_material')==$m->id?'selected':'' }}>{{ $m->material_name }}</option>@endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Property</span>
            <select name="filter_property" class="filter-control">
                <option value="">All Properties</option>
                @foreach($properties as $p)<option value="{{ $p->id }}" {{ request('filter_property')==$p->id?'selected':'' }}>{{ $p->property_name }}</option>@endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Date</span>
            <input type="date" name="filter_date" value="{{ request('filter_date') }}" class="filter-control">
        </div>
        <button type="submit" class="btn-search">Filter</button>
        @if(request()->hasAny(['search','filter_material','filter_property','filter_date']))<a href="{{ route('stock-outwards.index') }}" class="btn-reset">Reset</a>@endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Outward Number</th>
                    <th>Date</th>
                    <th>Ref. IMIR</th>
                    <th>Destination Property</th>
                    <th>Material (Sample)</th>
                    <th>Total Items</th>
                    <th>Vehicle No</th>
                    <th>Driver Name</th>
                    <th style="width:220px; text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($outwards as $out)
                @php
                    $itemCount = 1;
                    $sampleMaterial = $out->material->material_name ?? '—';
                    if ($out->outward_number) {
                        $itemCount = \App\Models\StockOutward::where('outward_number', $out->outward_number)->count();
                    }
                @endphp
                <tr>
                    <td><strong>{{ $out->outward_number ?: 'SO-'.$out->id }}</strong></td>
                    <td style="white-space:nowrap;">{{ $out->outward_date->format('d M Y') }}</td>
                    <td>{{ $out->stock_inward_number ?: 'Manual' }}</td>
                    <td>{{ $out->property->property_name ?? 'General' }}</td>
                    <td>{{ $sampleMaterial }}</td>
                    <td style="font-weight:700; text-align:center;">{{ $itemCount }}</td>
                    <td>{{ $out->vehicle_no ?: '—' }}</td>
                    <td>{{ $out->driver_name ?: '—' }}</td>
                    <td>
                        <div class="table-action-buttons" style="justify-content: center;">
                            <a href="{{ route('stock-outwards.show', $out->id) }}" class="btn-view"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('stock-outwards.print', $out->id) }}" target="_blank" class="btn-view" style="color:#059669;"><i class="fa fa-print"></i> Print</a>
                            @if(!$out->stock_inward_number)
                            <a href="{{ route('stock-outwards.edit', $out->id) }}" class="btn-edit"><i class="fa fa-edit"></i> Edit</a>
                            @endif
                            <form action="{{ route('stock-outwards.destroy', $out->id) }}" method="POST" style="display:inline;" id="del-out-{{ $out->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" onclick="confirmDel({{ $out->id }},'{{ $out->outward_number ?: $out->id }}','del-out-')"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" align="center" style="padding:30px;color:var(--text-secondary);">No stock outward records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">{{ $outwards->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDel(id,name,prefix){
    Swal.fire({
        title:'Delete Outward Transaction?',
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
