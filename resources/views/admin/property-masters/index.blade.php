@extends('admin.layouts.app')

@section('title', 'Property Master')
@section('page-title', 'Property Management')

@section('content')
<style>
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .crud-title h2 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .crud-title p {
        font-size: 13.5px;
        color: var(--text-secondary);
    }

    .btn-gold {
        background-color: var(--gold);
        color: #FFFFFF;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-gold:hover {
        background-color: #B58D1B;
        color: #FFFFFF;
    }

    .table-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--soft-shadow);
    }

    .table-toolbar {
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-color);
        gap: 15px;
        flex-wrap: wrap;
    }

    .search-form {
        display: flex;
        gap: 10px;
        align-items: center;
        flex: 1;
        max-width: 500px;
    }

    .search-input {
        width: 100%;
        padding: 9px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 13.5px;
        outline: none;
        transition: var(--transition);
    }

    .search-input:focus {
        border-color: var(--gold);
    }

    .btn-search {
        background: var(--text-primary);
        color: #FFF;
        padding: 9px 16px;
        border-radius: 8px;
        border: none;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-reset {
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 13.5px;
        padding: 9px 12px;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .custom-table th {
        padding: 14px 20px;
        background: #F8FAFC;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border-color);
    }

    .custom-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #F1F5F9;
        font-size: 14px;
        color: var(--text-primary);
    }

    .custom-table tr:hover {
        background: #F8FAFC;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 11.5px;
        font-weight: 600;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .badge-active {
        background: rgba(34, 197, 94, 0.1);
        color: #16803D;
    }

    .badge-inactive {
        background: rgba(239, 68, 68, 0.1);
        color: #B91C1C;
    }

    .action-link {
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 14px;
        padding: 6px;
        border-radius: 4px;
        transition: var(--transition);
    }

    .action-link:hover {
        color: var(--gold);
        background: #F1F5F9;
    }

    .pagination-wrapper {
        padding: 16px 20px;
        border-top: 1px solid var(--border-color);
    }
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

    <div style="width: 100%; overflow-x: auto;">
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
                            <a href="{{ route('property-masters.show', $property->id) }}" style="font-weight: 600; color: var(--text-primary); text-decoration: none;">
                                {{ $property->property_name }}
                            </a>
                        </td>
                        <td><code>{{ $property->property_code }}</code></td>
                        <td>{{ $property->firm->firm_name ?? '-' }}</td>
                        <td>{{ $property->city ?? $property->location ?? '-' }}</td>
                        <td>
                            <a href="{{ route('projects.index', ['property_id' => $property->id]) }}" style="color: var(--gold); font-weight: 600; text-decoration: none;">
                                <i class="fa-solid fa-city"></i> {{ $property->projects_count }} Projects
                            </a>
                        </td>
                        <td>
                            <span class="badge {{ $property->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                {{ $property->status }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('property-masters.show', $property->id) }}" class="action-link" title="View Property & Projects">
                                <i class="fa-regular fa-eye"></i>
                            </a>
                            @if(Auth::user() && Auth::user()->hasPermission('property_edit'))
                                <a href="{{ route('property-masters.edit', $property->id) }}" class="action-link" title="Edit Property">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                            @endif
                            @if(Auth::user() && Auth::user()->hasPermission('property_delete'))
                                <form action="{{ route('property-masters.destroy', $property->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Property? All associated Projects will be deleted!')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-link" style="border: none; background: transparent; cursor: pointer; color: #EF4444;" title="Delete Property">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            @endif
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
