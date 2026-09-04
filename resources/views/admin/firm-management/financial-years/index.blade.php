@extends('admin.layouts.app')
@section('title','Financial Year Setup')
@section('page-title','Firm Management')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 13.5px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }

.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-primary-custom:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 24px;
}

.filter-bar {
    display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; align-items: center;
    background: rgba(255, 255, 255, 0.04) !important; padding: 14px 18px !important;
    border-radius: 14px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
}
.search-input {
    padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; min-width: 240px;
    transition: border-color .18s, box-shadow .18s;
}
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.filter-select {
    padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none;
}
.filter-select option { background: #101622 !important; color: #FFFFFF !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    transition: all .2s ease;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); }

.btn-reset { padding: 10px 14px; color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
.btn-reset:hover { color: #FFFFFF !important; }

.table-container {
    width: 100%; overflow-x: auto; background: rgba(16, 22, 34, 0.70) !important;
    border-radius: 18px; border: 1px solid rgba(255, 255, 255, 0.10);
}
.premium-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.premium-table th {
    padding: 14px 16px; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap;
}
.premium-table td {
    padding: 14px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    color: #E2E8F0 !important; font-weight: 500; vertical-align: middle; white-space: nowrap;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

/* ── Badges ── */
.active-indicator {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(59, 130, 246, 0.20); color: #60A5FA;
    font-size: 11px; font-weight: 700; border-radius: 20px;
    padding: 2px 8px; border: 1px solid rgba(59, 130, 246, 0.35);
}
.badge-active-pill {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.35) !important;
    padding: 3px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 700;
}
.badge-inactive-pill {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(148, 163, 184, 0.12) !important; color: #94A3B8 !important;
    border: 1px solid rgba(148, 163, 184, 0.25) !important;
    padding: 3px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 600;
}

.badge-status {
    display: inline-block; padding: 4px 12px; font-size: 11px; font-weight: 700;
    border-radius: 20px; text-transform: uppercase;
}
.badge-status-active {
    background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.35) !important;
}
.badge-status-inactive {
    background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.35) !important;
}

/* ── Action Buttons ── */
.table-action-buttons {
    display: inline-flex; align-items: center; gap: 6px; flex-wrap: nowrap;
}
.btn-act-view {
    display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; min-height: 32px;
    background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important;
    border: 1px solid rgba(59, 130, 246, 0.30) !important; border-radius: 8px;
    font-size: 12.5px; font-weight: 700; text-decoration: none !important;
    transition: all .2s ease; cursor: pointer;
}
.btn-act-view:hover { background: #2563EB !important; color: #FFFFFF !important; transform: translateY(-1px); }

.btn-act-edit {
    display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; min-height: 32px;
    background: rgba(245, 158, 11, 0.15) !important; color: #FBBF24 !important;
    border: 1px solid rgba(245, 158, 11, 0.30) !important; border-radius: 8px;
    font-size: 12.5px; font-weight: 700; text-decoration: none !important;
    transition: all .2s ease; cursor: pointer;
}
.btn-act-edit:hover { background: #D97706 !important; color: #FFFFFF !important; transform: translateY(-1px); }

.btn-act-activate {
    display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; min-height: 32px;
    background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.35) !important; border-radius: 8px;
    font-size: 12.5px; font-weight: 700; text-decoration: none !important;
    transition: all .2s ease; cursor: pointer;
}
.btn-act-activate:hover { background: #059669 !important; color: #FFFFFF !important; transform: translateY(-1px); }

.btn-act-delete {
    display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; min-height: 32px;
    background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.35) !important; border-radius: 8px;
    font-size: 12.5px; font-weight: 700; text-decoration: none !important;
    transition: all .2s ease; cursor: pointer;
}
.btn-act-delete:hover { background: #DC2626 !important; color: #FFFFFF !important; transform: translateY(-1px); }

.pagination-wrap { margin-top: 20px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Financial Year Setup</h2>
        <p>Manage financial years. Only one year can be active at a time.</p>
    </div>
    <a href="{{ route('financial-years.create') }}" class="btn-primary-custom"><i class="fa fa-plus"></i> Add Year</a>
</div>

@if(session('success'))
<div class="alert-success" style="background: rgba(16,185,129,.12); border: 1px solid rgba(16,185,129,.3); color: #34D399; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; font-weight: 600;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert-danger-box" style="background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.3); color: #F87171; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; font-weight: 600;">
    <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
</div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('financial-years.index') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search year name…" class="search-input @error('search') is-invalid @enderror">
        <select name="status" class="filter-select @error('status') is-invalid @enderror">
            <option value="">All Status</option>
            <option value="active"   {{ request('status')=='active'   ? 'selected':'' }}>Active</option>
            <option value="inactive" {{ request('status')=='inactive' ? 'selected':'' }}>Inactive</option>
        </select>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        @if(request('search') || request('status'))
            <a href="{{ route('financial-years.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Year Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Active</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($years as $i => $year)
                <tr>
                    <td>{{ method_exists($years, 'firstItem') ? ($years->firstItem() + $i) : ($i + 1) }}</td>
                    <td>
                        <strong style="color: #FFFFFF; font-size: 14.5px;">{{ $year->year_name }}</strong>
                        @if($year->is_active)
                            <span class="active-indicator" style="margin-left:6px"><i class="fa-solid fa-circle-dot"></i> Current</span>
                        @endif
                    </td>
                    <td>{{ $year->start_date ? $year->start_date->format('d M Y') : '—' }}</td>
                    <td>{{ $year->end_date ? $year->end_date->format('d M Y') : '—' }}</td>
                    <td>
                        @if($year->is_active)
                            <span class="badge-active-pill"><i class="fa-solid fa-check"></i> Yes</span>
                        @else
                            <span class="badge-inactive-pill"><i class="fa-solid fa-xmark"></i> No</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge-status {{ $year->status === 'active' ? 'badge-status-active' : 'badge-status-inactive' }}">
                            {{ ucfirst($year->status ?? ($year->is_active ? 'active' : 'inactive')) }}
                        </span>
                    </td>
                    <td>
                        <div class="table-action-buttons">
                            <a href="{{ route('financial-years.show', $year) }}" class="btn-act-view"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('financial-years.edit', $year) }}" class="btn-act-edit"><i class="fa fa-edit"></i> Edit</a>
                            @if(!$year->is_active)
                            <form action="{{ route('financial-years.set-active', $year) }}" method="POST" style="display:inline; margin:0;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-act-activate"><i class="fa-solid fa-bolt"></i> Set Active</button>
                            </form>
                            <form action="{{ route('financial-years.destroy', $year) }}" method="POST" onsubmit="return confirm('Delete {{ addslashes($year->year_name) }}?')" style="display:inline; margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-act-delete"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:32px; color:#94A3B8;">
                        No financial years found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($years, 'links'))
        <div class="pagination-wrap">{{ $years->links() }}</div>
    @endif
</div>
@endsection
