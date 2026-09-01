@extends('admin.layouts.app')
@section('title', 'Contractors')
@section('page-title', 'Contractor Master')

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
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 600 !important; margin: 0; }

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
    display: flex; justify-content: flex-start; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
    background: rgba(255, 255, 255, 0.04) !important; padding: 14px 18px !important;
    border-radius: 14px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
}

.search-input, .filter-select {
    padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
}
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus, .filter-select:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }
select.filter-select option { background: #111827 !important; color: #FFFFFF !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 18px;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    display: inline-flex; align-items: center; gap: 6px;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset {
    color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 700;
    padding: 10px 14px; transition: color .2s ease; display: inline-flex; align-items: center; gap: 6px;
}
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

/* Badges */
.badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; font-size: 11px; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: .4px; white-space: nowrap; }
.badge-active   { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.proj-pill { display: inline-flex; align-items: center; gap: 6px; background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; font-size: 12px; font-weight: 700; border-radius: 8px; padding: 4px 10px; border: 1px solid rgba(59, 130, 246, 0.30); }
.id-chip { display: inline-flex; align-items: center; gap: 5px; font-family: monospace; font-size: 12px; background: rgba(255, 255, 255, 0.06); padding: 3px 8px; border-radius: 6px; border: 1px solid rgba(255, 255, 255, 0.12); color: #E2E8F0; }

/* Table action buttons */
.table-action-buttons { display: flex; gap: 8px; align-items: center; white-space: nowrap; justify-content: flex-end; }
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

/* Alert Messages */
.alert-success {
    background: rgba(16, 185, 129, 0.16) !important;
    border: 1px solid rgba(16, 185, 129, 0.38) !important;
    color: #34D399 !important;
    border-radius: 12px; padding: 14px 18px; margin-bottom: 22px;
    font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 10px;
}
.pagination-wrap { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Contractor Master</h2>
        <p>Manage project contractors, identity numbers, and bank account details.</p>
    </div>
    <a href="{{ route('contractors.create') }}" class="btn-pc">
        <i class="fa-solid fa-plus"></i> Add Contractor
    </a>
</div>

@if(session('success'))
<div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('contractors.index') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Search name, mobile, aadhar, pan, bank account..." style="flex: 1; min-width: 240px;">

        @if(auth()->user() && auth()->user()->isAdmin() && isset($firms) && $firms->count() > 0)
        <select name="firm_id" class="filter-select" onchange="this.form.submit()">
            <option value="">All Firms</option>
            @foreach($firms as $firm)
                <option value="{{ $firm->id }}" {{ request('firm_id') == $firm->id ? 'selected' : '' }}>
                    {{ $firm->firm_name }}
                </option>
            @endforeach
        </select>
        @endif

        <select name="project_id" class="filter-select" onchange="this.form.submit()">
            <option value="">All Projects</option>
            @foreach($projects as $proj)
                <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>
                    {{ $proj->project_name }}
                </option>
            @endforeach
        </select>

        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search', 'firm_id', 'project_id', 'status']))
            <a href="{{ route('contractors.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <th>Firm</th>
                    @endif
                    <th>Project</th>
                    <th>Contractor Name</th>
                    <th>Mobile</th>
                    <th>Aadhar Card</th>
                    <th>PAN Card</th>
                    <th>Bank Details</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($contractors as $i => $con)
                <tr>
                    <td>{{ method_exists($contractors, 'firstItem') ? ($contractors->firstItem() + $i) : ($i + 1) }}</td>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <td><strong style="color: #FFFFFF;">{{ $con->firm->firm_name ?? '—' }}</strong></td>
                    @endif
                    <td>
                        <span class="proj-pill">
                            <i class="fa-solid fa-city"></i>
                            {{ $con->project->project_name ?? 'No Project' }}
                        </span>
                    </td>
                    <td>
                        <strong style="color: #FFFFFF; font-size: 14px;">{{ $con->contractor_name }}</strong>
                        @if($con->address)
                            <div style="font-size: 11.5px; color: #94A3B8; margin-top: 3px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <i class="fa-solid fa-location-dot" style="font-size: 10px;"></i> {{ $con->address }}
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($con->mobile)
                            <span style="color: #60A5FA;"><i class="fa-solid fa-phone" style="font-size: 11px; margin-right: 4px;"></i> {{ $con->mobile }}</span>
                        @else
                            <span style="color: #94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($con->aadhar_no)
                            <span class="id-chip"><i class="fa-solid fa-address-card" style="color: #FBBF24;"></i> {{ $con->aadhar_no }}</span>
                        @else
                            <span style="color: #94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($con->pan_no)
                            <span class="id-chip"><i class="fa-solid fa-receipt" style="color: #34D399;"></i> {{ $con->pan_no }}</span>
                        @else
                            <span style="color: #94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($con->bank_name || $con->account_number)
                            <div><strong style="color: #FFFFFF;">{{ $con->bank_name ?: 'Bank' }}</strong></div>
                            @if($con->account_number)
                                <div style="font-size: 12px; color: #94A3B8; font-family: monospace;">A/C: {{ $con->account_number }}</div>
                            @endif
                            @if($con->ifsc_code)
                                <div style="font-size: 11px; color: #60A5FA;">IFSC: {{ $con->ifsc_code }}</div>
                            @endif
                        @else
                            <span style="color: #94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $con->status }}">
                            <i class="fa-solid fa-circle" style="font-size: 7px;"></i> {{ ucfirst($con->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="table-action-buttons">
                            <a href="{{ route('contractors.show', $con) }}" class="btn-view" title="View Details">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                            <a href="{{ route('contractors.edit', $con) }}" class="btn-edit" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('contractors.destroy', $con) }}" onsubmit="return confirm('Are you sure you want to delete contractor \'{{ $con->contractor_name }}\'?')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ (auth()->user() && auth()->user()->isAdmin()) ? 10 : 9 }}" style="text-align: center; padding: 48px 16px;">
                        <div style="font-size: 40px; color: #475569; margin-bottom: 12px;"><i class="fa-solid fa-helmet-safety"></i></div>
                        <div style="font-size: 16px; font-weight: 700; color: #CBD5E1; margin-bottom: 6px;">No Contractors Found</div>
                        <div style="font-size: 13px; color: #94A3B8; margin-bottom: 18px;">Start by adding contractors for each project.</div>
                        <a href="{{ route('contractors.create') }}" class="btn-pc"><i class="fa-solid fa-plus"></i> Add First Contractor</a>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($contractors->hasPages())
    <div class="pagination-wrap">
        {{ $contractors->links() }}
    </div>
    @endif
</div>
@endsection
