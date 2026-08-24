@extends('admin.layouts.app')

@section('title', 'Tenants')
@section('page-title', 'Tenant Master')

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
    display: flex !important; gap: 12px !important; align-items: center !important; margin-bottom: 24px !important;
    background: rgba(255, 255, 255, 0.04) !important; padding: 16px 20px !important;
    border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
    width: 100% !important; flex-wrap: nowrap !important; overflow-x: auto !important;
}

.search-form { display: flex !important; gap: 12px !important; flex: 1 !important; width: 100% !important; align-items: center !important; flex-wrap: nowrap !important; }

.search-input {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important; flex: 1 !important;
}
select.search-input option { background: #101622 !important; color: #FFFFFF !important; }
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    flex-shrink: 0 !important; white-space: nowrap !important;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px; flex-shrink: 0 !important; white-space: nowrap !important; transition: color .2s ease; }
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

.identity-chip { display: inline-flex; align-items: center; gap: 4px; background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 20px; border: 1px solid rgba(59, 130, 246, 0.30) !important; white-space: nowrap !important; }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Tenant Master</h2>
        <p>Add and manage tenant identity and rental contact details.</p>
    </div>
    <a href="{{ route('tenants.create') }}" class="btn-gold">
        <i class="fa-solid fa-plus"></i>
        <span>Add Tenant</span>
    </a>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="card-box">
    <div class="filter-bar">
        <form method="GET" action="{{ route('tenants.index') }}" class="search-form">
            @if(auth()->user() && auth()->user()->isAdmin())
                <select name="firm_id" class="search-input" onchange="this.form.submit()" style="max-width: 180px;">
                    <option value="">All Firms</option>
                    @foreach($firms as $firm)
                        <option value="{{ $firm->id }}" {{ request('firm_id') == $firm->id ? 'selected' : '' }}>
                            {{ $firm->firm_name }}
                        </option>
                    @endforeach
                </select>
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, mobile, email, city, firm, identity no." class="search-input @error('search') is-invalid @enderror">
            <button type="submit" class="btn-search">Search</button>
            @if(request('search') || request('firm_id'))
                <a href="{{ route('tenants.index') }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>No</th>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <th>Firm</th>
                    @endif
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>City</th>
                    <th>Identity Type</th>
                    <th>Identity Number</th>
                    <th>Status</th>
                    <th style="width: 200px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $key => $tenant)
                    <tr>
                        <td>{{ method_exists($tenants, 'firstItem') ? ($tenants->firstItem() + $key) : ($key + 1) }}</td>
                        @if(auth()->user() && auth()->user()->isAdmin())
                            <td><strong style="color:#FFFFFF !important;">{{ $tenant->firm->firm_name ?? '-' }}</strong></td>
                        @endif
                        <td><strong style="color:#FFFFFF !important;">{{ $tenant->name }}</strong></td>
                        <td style="color:#E2E8F0;">{{ $tenant->mobile }}</td>
                        <td style="color:#CBD5E1;">{{ $tenant->email ?? '-' }}</td>
                        <td style="color:#CBD5E1;">{{ $tenant->city ?? '-' }}</td>
                        <td>
                            @if($tenant->identity_type)
                                <span class="identity-chip">
                                    <i class="fa-solid fa-id-card" style="font-size:10px;"></i>
                                    {{ $tenant->identity_type }}
                                </span>
                            @else
                                <span style="color:#94A3B8;">-</span>
                            @endif
                        </td>
                        <td style="color:#CBD5E1;">{{ $tenant->identity_number ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $tenant->status }}">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons-wrap">
                                <a href="{{ route('tenants.show', $tenant->id) }}" class="btn-view">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a href="{{ route('tenants.edit', $tenant->id) }}" class="btn-edit">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('tenants.destroy', $tenant->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this tenant?')" class="btn-delete">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" align="center" style="padding: 30px; color: #CBD5E1;">No tenants found in this firm.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($tenants, 'links'))
        <div class="pagination-wrapper">
            {{ $tenants->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection

