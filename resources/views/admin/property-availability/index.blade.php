@extends('admin.layouts.app')
@section('title','Property Availability / Status')
@section('page-title','Property Availability')

@section('content')
<style>
/* ── page-level common ── */
.btn-pc{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:linear-gradient(135deg,#1E5AA8,#2F6FE4);color:#fff!important;font-size:14px;font-weight:600;line-height:1;border:none;border-radius:10px;text-decoration:none!important;box-shadow:0 8px 20px rgba(47,111,228,.25);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-pc:hover{color:#fff!important;text-decoration:none!important;transform:translateY(-2px);box-shadow:0 12px 28px rgba(47,111,228,.35)}
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px}
.crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px}
.crud-title p{font-size:13.5px;color:var(--text-secondary)}
    .filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .search-form {
        display: flex;
        gap: 10px;
        flex: 1;
        max-width: 500px;
    }

    .search-input {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 13.5px;
        font-family: var(--font-primary);
        color: var(--text-primary);
        outline: none;
        transition: var(--transition);
    }

    .search-input:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px var(--gold-light);
    }

    .btn-search {
        background-color: var(--text-primary);
        color: #FFFFFF;
        padding: 10px 18px;
        border-radius: 8px;
        border: none;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        font-family: var(--font-primary);
        transition: var(--transition);
        white-space: nowrap;
    }

    .btn-search:hover {
        background-color: #1E293B;
    }

    .btn-reset {
        padding: 10px 14px;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 500;
    }
/* table */
.table-container{width:100%;overflow-x:auto}
.premium-table{width:100%;border-collapse:collapse;font-size:13.5px;min-width:820px}
.premium-table th{padding:12px 16px;background:#F8FAFC;color:#475569;font-weight:700;border-bottom:2px solid var(--border-color);font-size:11px;text-transform:uppercase;letter-spacing:.8px;white-space:nowrap}
.premium-table td{padding:13px 16px;border-bottom:1px solid #F1F5F9;vertical-align:middle;color:var(--text-primary)}
.premium-table tbody tr:hover{background:#F0F7FF}
/* status badges */
.badge{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;font-size:11.5px;font-weight:700;border-radius:20px;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap}
.badge i{font-size:8px}
.badge-available   {background:rgba(16,185,129,.12);color:#065F46;border:1px solid rgba(16,185,129,.25)}
.badge-booked      {background:rgba(59,130,246,.12);color:#1D4ED8;border:1px solid rgba(59,130,246,.25)}
.badge-sold        {background:rgba(239,68,68,.10);color:#991B1B;border:1px solid rgba(239,68,68,.22)}
.badge-rented      {background:rgba(249,115,22,.12);color:#9A3412;border:1px solid rgba(249,115,22,.25)}
.badge-reserved    {background:rgba(139,92,246,.12);color:#5B21B6;border:1px solid rgba(139,92,246,.25)}
.badge-under_maintenance{background:rgba(100,116,139,.10);color:#334155;border:1px solid rgba(100,116,139,.22)}
/* misc */
.prop-pill{display:inline-flex;align-items:center;gap:5px;background:var(--blue-light);color:var(--blue);font-size:11.5px;font-weight:600;border-radius:6px;padding:3px 9px;border:1px solid rgba(59,130,246,.15)}
.unit-txt{font-size:12px;color:var(--text-secondary);margin-top:2px}
.alert-success{background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.2);color:#065F46;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px}
.alert-err{background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);color:#991B1B;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px}
.pagination-wrap{margin-top:20px;display:flex;justify-content:center}
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
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by property name, unit no, status..." class="search-input @error('search') is-invalid @enderror">
            <button type="submit" class="btn-search">Search</button>
            @if(request('search'))
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
                    <th>Property Name</th>
                    <th>Property Type</th>
                    <th>Unit / Plot / Flat No</th>
                    <th>Current Status</th>
                    <th>Status Date</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($records as $i => $rec)
                <tr>
                    <td>{{ $records->firstItem() + $i }}</td>
                    <td>
                        <strong>{{ $rec->property->property_name ?? '—' }}</strong>
                        @if($rec->property->property_code)
                            <div class="unit-txt">{{ $rec->property->property_code }}</div>
                        @endif
                    </td>
                    <td>
                        @if($rec->property?->propertyType)
                            <span class="prop-pill">{{ $rec->property->propertyType->name }}</span>
                        @else
                            <span style="color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td>{{ $rec->property->unit_no ?? '—' }}</td>
                    <td>
                        @php $sc = \App\Models\PropertyStatus::statusColor($rec->status); @endphp
                        <span class="badge badge-{{ $rec->status }}">
                            <i class="fa-solid fa-circle"></i>
                            {{ $rec->status_label }}
                        </span>
                    </td>
                    <td>{{ $rec->status_date->format('d M Y') }}</td>
                    <td>
                        @if($rec->remarks)
                            <span style="font-size:13px;color:var(--text-secondary)">
                                {{ \Str::limit($rec->remarks, 40) }}
                            </span>
                        @else
                            <span style="color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-action-buttons">
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
                    <td colspan="8" style="text-align:center;padding:32px;color:var(--text-secondary)">
                        No status records found.
                        <a href="{{ route('property-availability.create') }}" style="color:var(--blue);font-weight:600">Add one</a>.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">{{ $records->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDel(id, name) {
    Swal.fire({
        title: 'Delete Status Record?',
        text: 'This record for "' + name + '" will be permanently removed.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC3545',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then(r => { if (r.isConfirmed) document.getElementById('del-pa-' + id).submit(); });
}
</script>
@endsection
