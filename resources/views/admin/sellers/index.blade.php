@extends('admin.layouts.app')
@section('title', 'Seller Master')
@section('page-title', 'Seller Master')
@section('content')
<style>
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.kpi-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 26px; }
.kpi-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 18px !important; padding: 20px 22px;
    display: flex; align-items: center; gap: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
}
.kpi-icon {
    width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; flex-shrink: 0;
}
.kpi-icon.amber { background: rgba(245, 158, 11, 0.18); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); }
.kpi-icon.emerald { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); }
.kpi-icon.blue { background: rgba(59, 130, 246, 0.18); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.35); }
.kpi-data h3 { font-size: 22px; font-weight: 800; color: #FFFFFF !important; margin: 0 0 3px 0; }
.kpi-data p { font-size: 12.5px; color: #94A3B8 !important; font-weight: 600; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 26px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
}

.filter-bar { display: flex; gap: 12px; margin-bottom: 22px; flex-wrap: wrap; align-items: center; }
.filter-group { display: flex; align-items: center; gap: 8px; }
.filter-label { font-size: 12.5px; font-weight: 700; color: #94A3B8; text-transform: uppercase; }
.filter-control, .search-input {
    background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important; color: #FFFFFF !important;
    padding: 8px 14px; font-size: 13.5px; outline: none; transition: all .2s ease;
}
.filter-control:focus, .search-input:focus { border-color: #F59E0B !important; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25) !important; }
.search-input { min-width: 260px; flex: 1; }

.btn-primary-custom {
    background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%) !important;
    color: #FFFFFF !important; font-weight: 700; padding: 10px 20px;
    border-radius: 12px; font-size: 13.5px; text-decoration: none !important;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255, 255, 255, 0.2) !important;
    box-shadow: 0 4px 18px rgba(245, 158, 11, 0.38); cursor: pointer; transition: all .25s ease;
}
.btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(245, 158, 11, 0.52); }

.table-responsive { overflow-x: auto; }
.premium-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.premium-table th {
    background: rgba(15, 23, 42, 0.85); color: #94A3B8;
    font-size: 12px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.8px; padding: 14px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}
.premium-table td {
    padding: 14px 16px; font-size: 13.5px; color: #E2E8F0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.07); vertical-align: middle;
}
.premium-table tr:hover td { background: rgba(255, 255, 255, 0.03); }

.badge-active { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); padding: 4px 10px; border-radius: 8px; font-size: 11.5px; font-weight: 700; }
.badge-inactive { background: rgba(239, 68, 68, 0.18); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.35); padding: 4px 10px; border-radius: 8px; font-size: 11.5px; font-weight: 700; }

.action-btns { display: flex; gap: 6px; align-items: center; }
.btn-action {
    width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;
    font-size: 13px; text-decoration: none !important; border: 1px solid transparent; transition: all .2s ease; cursor: pointer;
}
.btn-action.view { background: rgba(59, 130, 246, 0.15); color: #60A5FA; border-color: rgba(59, 130, 246, 0.3); }
.btn-action.view:hover { background: #2563EB; color: #FFF; }
.btn-action.edit { background: rgba(245, 158, 11, 0.15); color: #FBBF24; border-color: rgba(245, 158, 11, 0.3); }
.btn-action.edit:hover { background: #D97706; color: #FFF; }
.btn-action.delete { background: rgba(239, 68, 68, 0.15); color: #F87171; border-color: rgba(239, 68, 68, 0.3); }
.btn-action.delete:hover { background: #DC2626; color: #FFF; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Seller Master</h2>
        <p>Manage landowners, land acquisition profiles, bank accounts &amp; deed details.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('sellers.pdf') }}" target="_blank" class="btn-primary-custom" style="background: rgba(255,255,255,0.08) !important; color: #FFF !important; border-color: rgba(255,255,255,0.15) !important;">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('sellers.create') }}" class="btn-primary-custom">
            <i class="fa-solid fa-plus"></i> Add New Seller
        </a>
    </div>
</div>

<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon amber"><i class="fa-solid fa-user-tag"></i></div>
        <div class="kpi-data">
            <h3>{{ number_format($totalSellers) }}</h3>
            <p>Total Sellers</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon emerald"><i class="fa-solid fa-circle-check"></i></div>
        <div class="kpi-data">
            <h3>{{ number_format($activeSellers) }}</h3>
            <p>Active Landowners</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon blue"><i class="fa-solid fa-map-location-dot"></i></div>
        <div class="kpi-data">
            <h3>{{ \App\Models\PropertyMaster::whereNotNull('seller_id')->orWhereNotNull('seller_name')->count() }}</h3>
            <p>Linked Land Parcels</p>
        </div>
    </div>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('sellers.index') }}" class="filter-bar">
        @if(isset($firms) && $firms->isNotEmpty())
            <div class="filter-group">
                <span class="filter-label">Firm:</span>
                <select name="firm_id" class="filter-control" onchange="this.form.submit()">
                    <option value="">All Firms</option>
                    @foreach($firms as $f)
                        <option value="{{ $f->id }}" {{ request('firm_id') == $f->id ? 'selected' : '' }}>{{ $f->firm_name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="filter-group">
            <span class="filter-label">Status:</span>
            <select name="status" class="filter-control" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Search seller by name, phone, PAN, city...">
        <button type="submit" class="btn-primary-custom" style="padding: 8px 16px;"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search', 'status', 'firm_id']))
            <a href="{{ route('sellers.index') }}" class="btn-primary-custom" style="background: rgba(255,255,255,0.08) !important; color:#FFF; padding:8px 14px;"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-responsive">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Seller Name</th>
                    <th>Contact Info</th>
                    <th>Type</th>
                    <th>Identity (PAN / Aadhaar)</th>
                    <th>Bank Details</th>
                    <th>Linked Properties</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sellers as $key => $seller)
                    <tr>
                        <td>{{ method_exists($sellers, 'firstItem') ? ($sellers->firstItem() + $key) : ($key + 1) }}</td>
                        <td>
                            <div style="font-weight: 700; color: #FFFFFF; font-size: 14px;">
                                <i class="fa-solid fa-user-tag" style="color: #FBBF24; font-size: 12px; margin-right: 4px;"></i>
                                {{ $seller->name }}
                            </div>
                            @if($seller->city)
                                <small style="color: #94A3B8;"><i class="fa-solid fa-location-dot" style="font-size: 10px;"></i> {{ $seller->city }}{{ $seller->state ? ', ' . $seller->state : '' }}</small>
                            @endif
                        </td>
                        <td>
                            @if($seller->mobile || $seller->phone)
                                <div style="color: #60A5FA; font-weight: 600;"><i class="fa-solid fa-phone" style="font-size: 11px;"></i> {{ $seller->mobile ?: $seller->phone }}</div>
                            @endif
                            @if($seller->email)
                                <small style="color: #94A3B8;">{{ $seller->email }}</small>
                            @endif
                        </td>
                        <td>
                            <span style="text-transform: capitalize; font-size: 12.5px; color: #CBD5E1;">{{ str_replace('_', ' ', $seller->seller_type ?? 'Individual') }}</span>
                        </td>
                        <td>
                            @if($seller->pan_no)
                                <div><small style="color: #94A3B8;">PAN:</small> <span style="font-family: monospace; color: #FCD34D;">{{ $seller->pan_no }}</span></div>
                            @endif
                            @if($seller->aadhaar_no)
                                <div><small style="color: #94A3B8;">Aadhaar:</small> <span style="font-family: monospace; color: #CBD5E1;">{{ $seller->aadhaar_no }}</span></div>
                            @endif
                            @if(!$seller->pan_no && !$seller->aadhaar_no)
                                <span style="color: #64748B;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($seller->bank_name)
                                <div style="font-weight: 600; color: #34D399; font-size: 12.5px;">{{ $seller->bank_name }}</div>
                                @if($seller->account_number)
                                    <small style="color: #94A3B8; font-family: monospace;">A/C: {{ $seller->account_number }}</small>
                                @endif
                            @else
                                <span style="color: #64748B;">—</span>
                            @endif
                        </td>
                        <td>
                            @php $pCount = $seller->propertyMasters->count(); @endphp
                            @if($pCount > 0)
                                <span class="badge-active" style="background: rgba(59, 130, 246, 0.18); color: #60A5FA; border-color: rgba(59, 130, 246, 0.35);">{{ $pCount }} Land Master(s)</span>
                            @else
                                <span style="color: #64748B;">None</span>
                            @endif
                        </td>
                        <td>
                            <span class="{{ $seller->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                {{ ucfirst($seller->status) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div class="action-btns" style="justify-content: flex-end;">
                                <a href="{{ route('sellers.show', $seller->id) }}" class="btn-action view" title="View Profile"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('sellers.edit', $seller->id) }}" class="btn-action edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                <form method="POST" action="{{ route('sellers.destroy', $seller->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this seller?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action delete" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 36px; color: #94A3B8;">
                            <i class="fa-solid fa-user-tag" style="font-size: 32px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                            No Sellers / Landowners found. Click "Add New Seller" to register one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($sellers, 'links'))
        <div style="margin-top: 22px; display: flex; justify-content: center;">
            {{ $sellers->links() }}
        </div>
    @endif
</div>
@endsection
