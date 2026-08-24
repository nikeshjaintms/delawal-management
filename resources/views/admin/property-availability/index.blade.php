@extends('admin.layouts.app')
@section('title','Property Availability / Status')
@section('page-title','Property Availability')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.btn-pc, .btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-pc:hover, .btn-primary-custom:hover {
    background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50);
}

.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 24px;
}

.filter-bar {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 15px;
    background: rgba(255, 255, 255, 0.04) !important; padding: 14px 18px !important;
    border-radius: 14px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
}
.search-form { display: flex; gap: 10px; flex: 1; max-width: 580px; }
.search-input {
    flex: 1; padding: 10px 16px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
}
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

select.search-input option { background: #111827 !important; color: #FFFFFF !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 700; padding: 10px 14px; transition: color .2s ease; display: inline-flex; align-items: center; }
.btn-reset:hover { color: #FFFFFF !important; }

/* Table */
.table-container { width: 100%; overflow-x: auto; background: rgba(16, 22, 34, 0.70) !important; border-radius: 18px; border: 1px solid rgba(255, 255, 255, 0.10); }
.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 14px 16px; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap;
}
.premium-table td {
    padding: 14px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    color: #FFFFFF !important; font-weight: 600; vertical-align: middle;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.04) !important; }

/* Status Badges (Vibrant glowing dark glass) */
.badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; font-size: 11px; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: .4px; white-space: nowrap; }
.badge i { font-size: 7px; }
.badge-available   { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-booked      { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-sold        { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-rented      { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.badge-reserved    { background: rgba(139, 92, 246, 0.18) !important; color: #A78BFA !important; border: 1px solid rgba(139, 92, 246, 0.35) !important; }
.badge-under_maintenance { background: rgba(148, 163, 184, 0.18) !important; color: #CBD5E1 !important; border: 1px solid rgba(148, 163, 184, 0.35) !important; }

/* Misc chips */
.prop-pill { display: inline-flex; align-items: center; gap: 5px; background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; font-size: 12px; font-weight: 700; border-radius: 6px; padding: 4px 10px; border: 1px solid rgba(59, 130, 246, 0.30); }
.unit-txt { font-size: 12px; color: #94A3B8; margin-top: 3px; font-family: monospace; font-weight: 700; }

/* Alert Messages (Glowing luxury glass) */
.alert-success {
    background: rgba(16, 185, 129, 0.16) !important;
    border: 1px solid rgba(16, 185, 129, 0.38) !important;
    color: #34D399 !important;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 22px;
    font-size: 14px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.20);
}
.alert-err {
    background: rgba(239, 68, 68, 0.16) !important;
    border: 1px solid rgba(239, 68, 68, 0.38) !important;
    color: #F87171 !important;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 22px;
    font-size: 14px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 16px rgba(239, 68, 68, 0.20);
}

.table-action-buttons { display: flex; gap: 8px; align-items: center; white-space: nowrap; }
.btn-view {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 6px 12px; min-height: 32px; background: rgba(59, 130, 246, 0.15) !important;
    color: #60A5FA !important; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(59, 130, 246, 0.30) !important;
    border-radius: 8px; text-decoration: none !important; transition: all .2s ease; cursor: pointer;
}
.btn-view:hover { background: #2563EB !important; color: #FFFFFF !important; transform: translateY(-2px); }

.btn-edit {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 6px 12px; min-height: 32px; background: rgba(245, 158, 11, 0.15) !important;
    color: #FBBF24 !important; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.30) !important;
    border-radius: 8px; text-decoration: none !important; transition: all .2s ease; cursor: pointer;
}
.btn-edit:hover { background: #D97706 !important; color: #FFFFFF !important; transform: translateY(-2px); }

.btn-delete {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 6px 12px; min-height: 32px; background: rgba(239, 68, 68, 0.15) !important;
    color: #F87171 !important; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(239, 68, 68, 0.30) !important;
    border-radius: 8px; text-decoration: none !important; transition: all .2s ease; cursor: pointer;
}
.btn-delete:hover { background: #DC2626 !important; color: #FFFFFF !important; transform: translateY(-2px); }

.pagination-wrap { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Property Availability / Status</h2>
        <p>Track and manage the current availability status of every property.</p>
    </div>
    <a href="{{ route('property-availability.create') }}" class="btn-pc">
        <i class="fa fa-plus"></i> Update Status
    </a>
</div>

@if(session('success'))
<div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-err"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
@endif

<div class="card-box">
    <div class="filter-bar">
        <form method="GET" action="{{ route('property-availability.index') }}" class="search-form">
            @if(auth()->user() && auth()->user()->isAdmin())
                <select name="firm_id" class="search-input" onchange="this.form.submit()" style="max-width: 180px;">
                    <option value="">All Firms</option>
                    @foreach(\App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get() as $firm)
                        <option value="{{ $firm->id }}" {{ request('firm_id') == $firm->id ? 'selected' : '' }}>
                            {{ $firm->firm_name }}
                        </option>
                    @endforeach
                </select>
            @endif
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by property name, unit no, status..." class="search-input @error('search') is-invalid @enderror">
            <button type="submit" class="btn-search">Search</button>
            @if(request('search') || request('firm_id'))
                <a href="{{ route('property-availability.index') }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <th>Firm</th>
                    @endif
                    <th>Property Name</th>
                    <th>Property Type</th>
                    <th>Unit / Plot / Flat No</th>
                    <th>Current Status</th>
                    <th>Status Date</th>
                    <th>Remarks</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($records as $i => $rec)
                <tr>
                    <td>{{ method_exists($records, 'firstItem') ? ($records->firstItem() + $i) : ($i + 1) }}</td>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <td><strong style="color: #FFFFFF;">{{ $rec->firm->firm_name ?? 'N/A' }}</strong></td>
                    @endif
                    <td>
                        <strong style="color: #FFFFFF;">{{ $rec->property->property_name ?? '—' }}</strong>
                        @if($rec->property->property_code)
                            <div class="unit-txt">{{ $rec->property->property_code }}</div>
                        @endif
                    </td>
                    <td>
                        @if($rec->property?->propertyType)
                            <span class="prop-pill">{{ $rec->property->propertyType->name }}</span>
                        @else
                            <span style="color:#94A3B8">—</span>
                        @endif
                    </td>
                    <td><span style="color: #CBD5E1; font-weight: 700;">{{ $rec->property->unit_no ?? '—' }}</span></td>
                    <td>
                        <span class="badge badge-{{ $rec->status }}">
                            <i class="fa-solid fa-circle"></i>
                            {{ $rec->status_label }}
                        </span>
                    </td>
                    <td><span style="color: #CBD5E1; font-weight: 600;">{{ $rec->status_date->format('d M Y') }}</span></td>
                    <td>
                        @if($rec->remarks)
                            <span style="font-size:13px;color:#94A3B8; font-weight: 500;">
                                {{ \Str::limit($rec->remarks, 40) }}
                            </span>
                        @else
                            <span style="color:#64748B">—</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div class="table-action-buttons" style="justify-content: flex-end;">
                            <a href="{{ route('property-availability.show', $rec) }}" class="btn-view"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('property-availability.edit', $rec) }}" class="btn-edit"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('property-availability.destroy', $rec) }}" method="POST"
                                  id="del-pa-{{ $rec->id }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete"
                                    onclick="confirmDel({{ $rec->id }}, '{{ addslashes($rec->property->property_name ?? '') }}')">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:36px;color:#94A3B8; font-weight: 600;">
                        No status records found.
                        <a href="{{ route('property-availability.create') }}" style="color:#60A5FA;font-weight:700; text-decoration: none;">Add one</a>.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($records, 'links'))
        <div class="pagination-wrap">{{ $records->links() }}</div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDel(id, name) {
    Swal.fire({
        title: 'Delete Status Record?',
        text: 'This record for "' + name + '" will be permanently removed.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#475569',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        background: '#1E293B',
        color: '#FFFFFF'
    }).then(r => { if (r.isConfirmed) document.getElementById('del-pa-' + id).submit(); });
}
</script>
@endsection
