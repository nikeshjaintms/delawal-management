@extends('admin.layouts.app')

@section('title', 'Property Master')
@section('page-title', 'Property Management')

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

.table-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
}

.table-toolbar {
    padding: 16px 20px !important; display: flex; justify-content: space-between;
    align-items: center; border: 1px solid rgba(255, 255, 255, 0.10) !important;
    border-radius: 16px !important; margin-bottom: 20px !important;
    gap: 15px; flex-wrap: wrap; background: rgba(255, 255, 255, 0.04) !important;
}

.search-form { display: flex; gap: 12px; align-items: center; flex: 1; max-width: 540px; }
.search-input {
    width: 100%; padding: 11px 18px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 14px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
}
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 22px;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 14px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 14px; transition: color .2s ease; }
.btn-reset:hover { color: #FFFFFF !important; }

.table-responsive-wrapper { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); }

.custom-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
.custom-table th {
    padding: 16px 22px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: 0.9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.custom-table td {
    padding: 18px 22px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 14px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
    white-space: nowrap !important;
}
.custom-table tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.custom-table code {
    background: rgba(255, 255, 255, 0.08) !important; color: #60A5FA !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important; padding: 5px 12px !important;
    border-radius: 8px !important; font-family: monospace; font-size: 13px; font-weight: 700;
    white-space: nowrap !important; display: inline-block !important;
}

.badge { display: inline-block; padding: 5px 14px; font-size: 11.5px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.table-action-cell { display: flex; align-items: center; justify-content: flex-end; gap: 10px; flex-wrap: nowrap !important; white-space: nowrap !important; }
.action-link-view {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important;
    border: 1px solid rgba(96, 165, 250, 0.30) !important; border-radius: 10px; text-decoration: none !important;
    font-size: 13px !important; font-weight: 700 !important; transition: all .2s ease; white-space: nowrap !important;
}
.action-link-view:hover { background: #2563EB !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(37, 99, 235, 0.40); }

.action-link-edit {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(245, 158, 11, 0.15) !important; color: #FBBF24 !important;
    border: 1px solid rgba(245, 158, 11, 0.30) !important; border-radius: 10px; text-decoration: none !important;
    font-size: 13px !important; font-weight: 700 !important; transition: all .2s ease; white-space: nowrap !important;
}
.action-link-edit:hover { background: #D97706 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(217, 119, 6, 0.40); }

.action-link-delete {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(239, 68, 68, 0.15) !important; color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.30) !important; border-radius: 10px; text-decoration: none !important;
    font-size: 13px !important; font-weight: 700 !important; transition: all .2s ease; cursor: pointer; white-space: nowrap !important;
}
.action-link-delete:hover { background: #DC2626 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(220, 38, 38, 0.40); }

.pagination-wrapper { padding: 18px 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Property Master</h2>
        <p>Manage first-level Property entries and their associated Projects.</p>
    </div>
    @if(Auth::user() && Auth::user()->hasPermission('property_add'))
        <a href="{{ route('property-masters.create') }}" class="btn-gold">
            <i class="fa-solid fa-plus"></i> Add Property
        </a>
    @endif
</div>

@if(session('success'))
    <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid #22C55E; color: #16803D; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #EF4444; color: #B91C1C; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('error') }}
    </div>
@endif

<div class="table-card">
    <div class="table-toolbar">
        <form method="GET" action="{{ route('property-masters.index') }}" class="search-form">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Property Name, Code, City..." class="search-input">
            <button type="submit" class="btn-search">Filter</button>
            @if(request()->hasAny(['search', 'status', 'firm_id']))
                <a href="{{ route('property-masters.index') }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-responsive-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Property Name</th>
                    <th>Code</th>
                    <th>Firm</th>
                    <th>City / Location</th>
                    <th>Projects</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($propertyMasters as $property)
                    <tr>
                        <td>
                            <a href="{{ route('property-masters.show', $property->id) }}" style="font-weight: 700; color: #FFFFFF !important; text-decoration: none;">
                                {{ $property->property_name }}
                            </a>
                        </td>
                        <td><code>{{ $property->property_code }}</code></td>
                        <td>{{ $property->firm->firm_name ?? '-' }}</td>
                        <td>{{ $property->city ?? $property->location ?? '-' }}</td>
                        <td>
                            <a href="{{ route('projects.index', ['property_id' => $property->id]) }}" style="color: #60A5FA !important; font-weight: 700; text-decoration: none;">
                                <i class="fa-solid fa-city"></i> {{ $property->projects_count }} Projects
                            </a>
                        </td>
                        <td>
                            <span class="badge {{ $property->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                {{ ucfirst($property->status) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div class="table-action-cell">
                                <a href="{{ route('property-masters.show', $property->id) }}" class="action-link-view" title="View Property & Projects">
                                    <i class="fa-regular fa-eye"></i> View
                                </a>
                                @if(Auth::user() && Auth::user()->hasPermission('property_edit'))
                                    <a href="{{ route('property-masters.edit', $property->id) }}" class="action-link-edit" title="Edit Property">
                                        <i class="fa-regular fa-pen-to-square"></i> Edit
                                    </a>
                                @endif
                                @if(Auth::user() && Auth::user()->hasPermission('property_delete'))
                                    <form action="{{ route('property-masters.destroy', $property->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Property? All associated Projects will be deleted!')" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-link-delete" title="Delete Property">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                            No Property records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($propertyMasters->hasPages())
        <div class="pagination-wrapper">
            {{ $propertyMasters->links() }}
        </div>
    @endif
</div>
@endsection
