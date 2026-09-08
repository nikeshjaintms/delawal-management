@extends('admin.layouts.app')
@section('title', 'Property Buy')
@section('page-title', 'Property Buy')
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
    width: 100% !important; flex-wrap: wrap !important;
}

.filter-group { display: flex; flex-direction: column; gap: 6px; flex-shrink: 0; }
.filter-label { font-size: 11px; font-weight: 800; color: #CBD5E1 !important; text-transform: uppercase; letter-spacing: 0.8px; }

.filter-control {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important; min-width: 140px;
}
select.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.filter-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.search-input {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important; min-width: 240px;
}
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    flex-shrink: 0 !important; white-space: nowrap !important; align-self: flex-end;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px; flex-shrink: 0 !important; white-space: nowrap !important; transition: color .2s ease; align-self: flex-end; }
.btn-reset:hover { color: #FFFFFF !important; }

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

.type-chip { background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; padding: 3px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 700; border: 1px solid rgba(59, 130, 246, 0.30); display: inline-block; }

.btn-view {
    display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: rgba(59, 130, 246, 0.15) !important;
    color: #60A5FA !important; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(59, 130, 246, 0.30) !important;
    border-radius: 8px; text-decoration: none !important; transition: all .2s ease; cursor: pointer;
}
.btn-view:hover { background: #2563EB !important; color: #FFFFFF !important; transform: translateY(-2px); }

.btn-edit {
    display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: rgba(245, 158, 11, 0.15) !important;
    color: #FBBF24 !important; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.30) !important;
    border-radius: 8px; text-decoration: none !important; transition: all .2s ease; cursor: pointer;
}
.btn-edit:hover { background: #D97706 !important; color: #FFFFFF !important; transform: translateY(-2px); }

.btn-delete {
    display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: rgba(239, 68, 68, 0.15) !important;
    color: #F87171 !important; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(239, 68, 68, 0.30) !important;
    border-radius: 8px; cursor: pointer; transition: all .2s ease;
}
.btn-delete:hover { background: #DC2626 !important; color: #FFFFFF !important; transform: translateY(-2px); }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Property Buy</h2>
        <p>Track and manage all property / plot records, area, and legal survey numbers.</p>
    </div>
    <a href="{{ route('purchases.create') }}" class="btn-gold">
        <i class="fa-solid fa-plus"></i> Add Property Buy
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
    <form method="GET" action="{{ route('purchases.index') }}" class="filter-bar">
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
            <span class="filter-label">Property Type</span>
            <select name="property_type" class="filter-control" onchange="this.form.submit()">
                <option value="">All Types</option>
                @foreach($propertyTypes as $pt)
                    <option value="{{ $pt }}" {{ request('property_type') == $pt ? 'selected' : '' }}>{{ $pt }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group" style="flex: 1; min-width: 240px;">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="search-input" placeholder="Property name, survey no, tp no, location...">
        </div>

        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','property_type','firm_id']))
            <a href="{{ route('purchases.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Firm</th>
                    <th>Property / Plot Name</th>
                    <th>Type</th>
                    <th>Property Code</th>
                    <th>Location</th>
                    <th>Area</th>
                    <th>Survey / TP / FP</th>
                    <th style="width:160px;text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $key => $purchase)
                <tr>
                    <td>{{ method_exists($purchases, 'firstItem') ? ($purchases->firstItem() + $key) : ($key + 1) }}</td>
                    <td><strong style="color:#FFFFFF !important;">{{ $purchase->firm_names }}</strong></td>
                    <td>
                        <div style="font-weight:700;color:#FFFFFF;font-size:14px;">{{ $purchase->display_name }}</div>
                        @if($purchase->address)
                            <div style="font-size:11.5px;color:#94A3B8;margin-top:2px;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                <i class="fa-solid fa-location-dot" style="font-size:10px;"></i> {{ $purchase->address }}
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($purchase->property_type)
                            <span class="type-chip">{{ $purchase->property_type }}</span>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($purchase->property_code)
                            <span style="font-family:monospace;font-size:12.5px;color:#CBD5E1;">{{ $purchase->property_code }}</span>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($purchase->location)
                            <span style="color:#60A5FA;font-weight:600;"><i class="fa-solid fa-map-pin" style="font-size:11px;"></i> {{ $purchase->location }}</span>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($purchase->area)
                            <strong style="color:#34D399;font-size:14px;">{{ number_format($purchase->area, 2) }}</strong>
                            <small style="color:#94A3B8;">{{ $purchase->area_unit ?? 'Sq.Ft' }}</small>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($purchase->survey_no || $purchase->tp_no || $purchase->fp_no)
                            <div style="font-size:12px;color:#CBD5E1;">
                                @if($purchase->survey_no)<span>S: <strong>{{ $purchase->survey_no }}</strong></span> @endif
                                @if($purchase->tp_no)<span style="margin-left:4px;">TP: <strong>{{ $purchase->tp_no }}</strong></span> @endif
                                @if($purchase->fp_no)<span style="margin-left:4px;">FP: <strong>{{ $purchase->fp_no }}</strong></span> @endif
                            </div>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div class="action-buttons-wrap" style="justify-content:flex-end;">
                            <a href="{{ route('purchases.show', $purchase->id) }}" class="btn-view" title="View Details">
                                <i class="fa fa-eye"></i> View
                            </a>
                            <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn-edit" title="Edit">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST"
                                  style="display:inline;" id="del-pur-{{ $purchase->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" title="Delete"
                                    onclick="confirmDelete({{ $purchase->id }}, '{{ addslashes($purchase->display_name) }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" align="center" style="padding:40px;color:#CBD5E1;">
                        <i class="fa-solid fa-building" style="font-size:28px;opacity:0.3;margin-bottom:8px;display:block;"></i>
                        No Property Buy records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($purchases, 'links'))
        <div class="pagination-wrapper">
            {{ $purchases->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Delete Property Buy?',
        html: 'Are you sure you want to delete <strong>' + name + '</strong>?<br><small style="color:#94A3B8;">This action cannot be undone.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa fa-trash"></i> Yes, Delete',
        cancelButtonText: 'Cancel',
        background: 'rgba(18, 25, 38, 0.96)',
        color: '#FFFFFF'
    }).then(r => { if (r.isConfirmed) document.getElementById('del-pur-' + id).submit(); });
}
</script>
@endsection
