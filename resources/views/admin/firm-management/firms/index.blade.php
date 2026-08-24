@extends('admin.layouts.app')
@section('title','Firm Profile & GST Details')
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
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
}

.filter-bar {
    display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; align-items: center;
    background: rgba(255, 255, 255, 0.04) !important; padding: 14px 18px !important;
    border-radius: 14px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
}
.search-input {
    padding: 10px 16px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; min-width: 280px;
}
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.filter-select {
    padding: 10px 16px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none;
}
.filter-select option { background: #101622 !important; color: #FFFFFF !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; display: inline-flex; align-items: center; gap: 8px;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { padding: 10px 14px; color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; transition: color .2s ease; }
.btn-reset:hover { color: #FFFFFF !important; }

.table-container { width: 100%; overflow-x: auto; background: rgba(16, 22, 34, 0.70) !important; border-radius: 18px; border: 1px solid rgba(255, 255, 255, 0.10); }
.premium-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.premium-table th { padding: 14px 16px; background: rgba(255, 255, 255, 0.05) !important; color: #94A3B8 !important; font-weight: 800; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important; font-size: 11px; text-transform: uppercase; letter-spacing: .8px; }
.premium-table td { padding: 14px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important; vertical-align: middle; color: #E2E8F0 !important; font-weight: 500; }
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.badge { display: inline-block; padding: 4px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.table-action-buttons {
    display: flex !important; flex-direction: row !important; align-items: center !important;
    gap: 8px !important; flex-wrap: nowrap !important; white-space: nowrap !important;
}
.table-action-buttons form { display: inline-flex !important; margin: 0 !important; padding: 0 !important; }

.btn-action-custom, a.btn-action-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 7px 13px;
    min-height: 34px; background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important;
    font-size: 12.5px; font-weight: 700; border: 1px solid rgba(96, 165, 250, 0.30) !important;
    border-radius: 8px; text-decoration: none !important; transition: all .2s ease; cursor: pointer;
}
.btn-action-custom:hover { background: #2563EB !important; color: #FFFFFF !important; border-color: #2563EB !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(37, 99, 235, 0.40); }

.btn-edit-custom, a.btn-edit-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 7px 13px;
    min-height: 34px; background: rgba(245, 158, 11, 0.15) !important; color: #FBBF24 !important;
    font-size: 12.5px; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.30) !important;
    border-radius: 8px; text-decoration: none !important; transition: all .2s ease; cursor: pointer;
}
.btn-edit-custom:hover { background: #D97706 !important; color: #FFFFFF !important; border-color: #D97706 !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(217, 119, 6, 0.40); }

.btn-danger-custom, button.btn-danger-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 7px 13px;
    min-height: 34px; background: rgba(239, 68, 68, 0.15) !important; color: #F87171 !important;
    font-size: 12.5px; font-weight: 700; border: 1px solid rgba(239, 68, 68, 0.30) !important;
    border-radius: 8px; text-decoration: none !important; transition: all .2s ease; cursor: pointer;
}
.btn-danger-custom:hover { background: #DC2626 !important; color: #FFFFFF !important; border-color: #DC2626 !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(220, 38, 38, 0.40); }

.pagination-wrap { margin-top: 20px; display: flex; justify-content: center; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.alert-danger { background: rgba(239, 68, 68, 0.15) !important; border: 1px solid rgba(239, 68, 68, 0.30) !important; color: #F87171 !important; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Firm Profile &amp; GST Details</h2>
        <p>Manage your company profiles, GST details and bank information.</p>
    </div>
    <a href="{{ route('firm-master.create') }}" class="btn-primary-custom">
        <i class="fa fa-plus"></i> Add Firm
    </a>
</div>

@if(session('success'))
<div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-danger"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('firm-master.index') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search firm, owner, email, GST…" class="search-input @error('search') is-invalid @enderror">
        <select name="status" class="filter-select @error('status') is-invalid @enderror">
            <option value="">All Status</option>
            <option value="active"   {{ request('status')=='active'   ? 'selected':'' }}>Active</option>
            <option value="inactive" {{ request('status')=='inactive' ? 'selected':'' }}>Inactive</option>
        </select>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        @if(request('search') || request('status'))
            <a href="{{ route('firm-master.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th><th>Firm Name</th><th>Owner</th>
                    <th>Email</th><th>Mobile</th><th>GST No</th><th>City / State</th>
                    <th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($firms as $i => $firm)
                <tr>
                    <td>{{ method_exists($firms, 'firstItem') ? ($firms->firstItem() + $i) : ($i + 1) }}</td>
                    <td><strong>{{ $firm->firm_name }}</strong></td>
                    <td>{{ $firm->owner_name ?? '—' }}</td>
                    <td>{{ $firm->email ?? '—' }}</td>
                    <td>{{ $firm->mobile ?? '—' }}</td>
                    <td>{{ $firm->gst_no ?? '—' }}</td>
                    <td>{{ implode(', ', array_filter([$firm->city, $firm->state])) ?: '—' }}</td>
                    <td><span class="badge badge-{{ $firm->status }}">{{ ucfirst($firm->status) }}</span></td>
                    <td>
                        <div class="table-action-buttons">
                            <a href="{{ route('firm-master.show', $firm) }}" class="btn-action-custom"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('firm-master.edit', $firm) }}" class="btn-edit-custom"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('firm-master.destroy', $firm) }}" method="POST" onsubmit="return confirm('Delete firm \'{{ addslashes($firm->firm_name) }}\'? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger-custom"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" style="text-align:center;padding:30px;color:var(--text-secondary)">No firms found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($firms, 'links'))
        <div class="pagination-wrap">{{ $firms->links() }}</div>
    @endif
</div>
@endsection

