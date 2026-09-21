@extends('admin.layouts.app')
@section('title', 'Agriculture Farms & Land')
@section('page-title', 'Agriculture Management')
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
    width: 100% !important; flex-wrap: nowrap !important; overflow-x: auto !important;
}
.filter-group { display: flex; flex-direction: column; gap: 6px; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; }
.filter-control, .search-input {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    min-width: 135px;
}
select.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.search-input { min-width: 220px; }
.search-input::placeholder { color: #94A3B8 !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    white-space: nowrap !important; height: 42px;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); }
.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px; white-space: nowrap !important; height: 42px; display: inline-flex; align-items: center; }

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

.status-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.st-active { background: rgba(34, 197, 94, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.st-prep   { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.st-harvest{ background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.st-fallow { background: rgba(148, 163, 184, 0.18) !important; color: #CBD5E1 !important; border: 1px solid rgba(148, 163, 184, 0.35) !important; }
.st-inact  { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.type-chip { background: rgba(255, 255, 255, 0.08) !important; color: #E2E8F0 !important; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.10); white-space: nowrap !important; }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2><i class="fa-solid fa-tractor" style="color: #60A5FA;"></i> Agriculture Farms & Land</h2>
        <p>Manage all agricultural properties, land parcels, survey numbers, and crops.</p>
    </div>
    <a href="{{ route('agriculture.farms.create') }}" class="btn-gold"><i class="fa-solid fa-plus"></i> Add New Farm</a>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('agriculture.farms.index') }}" class="filter-bar">
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
            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Farm name, village, survey no, crop...">
        </div>

        <div class="filter-group">
            <span class="filter-label">Farm Type</span>
            <select name="filter_type" class="filter-control">
                <option value="">All Types</option>
                @foreach(['Owned', 'Leased', 'Contract Farming', 'Shared / Partnership', 'Other'] as $t)
                    <option value="{{ $t }}" {{ request('filter_type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Project</span>
            <select name="filter_project" class="filter-control">
                <option value="">All Projects</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('filter_project') == $p->id ? 'selected' : '' }}>{{ $p->project_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Status</span>
            <select name="filter_status" class="filter-control">
                <option value="">All Status</option>
                @foreach(['Active', 'Under Preparation', 'Harvested', 'Fallow', 'Inactive'] as $st)
                    <option value="{{ $st }}" {{ request('filter_status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search', 'filter_type', 'filter_project', 'filter_status', 'firm_id']))
            <a href="{{ route('agriculture.farms.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Farm Name</th>
                    <th>Location / Village</th>
                    <th>Survey No</th>
                    <th>Land Area</th>
                    <th>Farm Type</th>
                    <th>Current Crop</th>
                    <th>Connected Project / Property</th>
                    <th>Financials</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:160px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($farms as $key => $farm)
                @php
                    $stCls = 'st-active';
                    if ($farm->status === 'Under Preparation') $stCls = 'st-prep';
                    elseif ($farm->status === 'Harvested') $stCls = 'st-harvest';
                    elseif ($farm->status === 'Fallow') $stCls = 'st-fallow';
                    elseif ($farm->status === 'Inactive') $stCls = 'st-inact';
                @endphp
                <tr>
                    <td>{{ method_exists($farms, 'firstItem') ? ($farms->firstItem() + $key) : ($key + 1) }}</td>
                    <td>
                        <strong style="color:#FFFFFF;font-size:14.5px;">{{ $farm->farm_name }}</strong>
                        <div style="font-size:11.5px;color:#94A3B8;">{{ $farm->firm_names }}</div>
                    </td>
                    <td>
                        <div style="color:#FFFFFF;font-weight:600;">{{ $farm->village ?: '—' }}</div>
                        <div style="font-size:11.5px;color:#94A3B8;">{{ $farm->taluka ? $farm->taluka . ', ' : '' }}{{ $farm->district }}</div>
                    </td>
                    <td><span style="color:#CBD5E1;font-weight:600;">{{ $farm->survey_no ?: '—' }}</span></td>
                    <td>
                        <strong style="color:#FBBF24;">{{ number_format($farm->land_area, 2) }}</strong>
                        <span style="color:#94A3B8;font-size:12px;">{{ $farm->area_unit }}</span>
                    </td>
                    <td><span class="type-chip">{{ $farm->farm_type }}</span></td>
                    <td>
                        <span style="color:#34D399;font-weight:700;">{{ $farm->crop_activity ?: '—' }}</span>
                    </td>
                    <td>
                        @if($farm->project)
                            <div style="color:#60A5FA;font-weight:600;font-size:12.5px;"><i class="fa-solid fa-city"></i> {{ $farm->project->project_name }}</div>
                        @endif
                        @if($farm->property)
                            <div style="color:#CBD5E1;font-size:11.5px;"><i class="fa-solid fa-building"></i> {{ $farm->property->property_name }}</div>
                        @endif
                        @if(!$farm->project && !$farm->property)
                            <span style="color:#94A3B8;">Direct Farm</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:11.5px;color:#34D399;font-weight:700;">Inc: ₹{{ number_format($farm->total_income, 2) }}</div>
                        <div style="font-size:11.5px;color:#F87171;font-weight:700;">Exp: ₹{{ number_format($farm->total_expense, 2) }}</div>
                    </td>
                    <td style="text-align:center;">
                        <span class="status-badge {{ $stCls }}">{{ $farm->status }}</span>
                    </td>
                    <td>
                        <div class="action-buttons-wrap">
                            <a href="{{ route('agriculture.farms.show', $farm->id) }}" class="btn-view" title="View Farm Details"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('agriculture.farms.edit', $farm->id) }}" class="btn-edit" title="Edit Farm"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('agriculture.farms.destroy', $farm->id) }}" method="POST" style="display:inline;" id="del-farm-{{ $farm->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" title="Delete Farm" onclick="confirmDeleteFarm({{ $farm->id }}, '{{ addslashes($farm->farm_name) }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="padding:40px;text-align:center;color:#CBD5E1;">
                        <i class="fa-solid fa-tractor" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>
                        No agriculture farm records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($farms, 'links'))
        <div class="pagination-wrapper">{{ $farms->appends(request()->query())->links() }}</div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDeleteFarm(id, name) {
    Swal.fire({
        title: 'Delete Farm?',
        html: 'Are you sure you want to delete <strong>' + name + '</strong>?<br><small style="color:#64748B;">Associated labour, expense, and income links will remain protected.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: { popup: 'swal-loan-popup' }
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('del-farm-' + id).submit();
        }
    });
}
</script>
<style>.swal-loan-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection
