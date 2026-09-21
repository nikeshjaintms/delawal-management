@extends('admin.layouts.app')
@section('title', 'Seller Master')
@section('page-title', 'Seller Master')
@section('content')

{{-- ── Page Header ── --}}
<div class="crud-header">
    <div class="crud-title">
        <div class="title-with-badge">
            <h2>Seller Master</h2>
            @if(isset($totalSellers))
                <span class="count-pill">{{ number_format($totalSellers) }} {{ \Illuminate\Support\Str::plural('Seller', $totalSellers) }}</span>
            @endif
        </div>
        <p>Manage landowners, land acquisition profiles, bank accounts &amp; deed details.</p>
    </div>
    <div class="crud-actions">
        <a href="{{ route('sellers.pdf', request()->query()) }}" target="_blank" class="btn-pdf-red" title="Export Sellers List to PDF">
            <i class="fa-solid fa-file-pdf"></i>
            <span>Export PDF</span>
        </a>
        <a href="{{ route('sellers.create') }}" class="btn-gold" title="Add New Landowner / Seller">
            <i class="fa-solid fa-plus"></i>
            <span>Add New Seller</span>
        </a>
    </div>
</div>

{{-- ── KPI Summary Cards ── --}}
<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon amber">
            <i class="fa-solid fa-user-tag"></i>
        </div>
        <div class="kpi-data">
            <h3>{{ number_format($totalSellers ?? 0) }}</h3>
            <p>Total Sellers</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon emerald">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="kpi-data">
            <h3>{{ number_format($activeSellers ?? 0) }}</h3>
            <p>Active Landowners</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon blue">
            <i class="fa-solid fa-map-location-dot"></i>
        </div>
        <div class="kpi-data">
            <h3>{{ \App\Models\PropertyMaster::whereNotNull('seller_id')->orWhereNotNull('seller_name')->count() }}</h3>
            <p>Linked Land Parcels</p>
        </div>
    </div>
</div>

{{-- ── Main Content Card ── --}}
<div class="card-box">
    {{-- ── Modern Glass Filter Bar ── --}}
    <form method="GET" action="{{ route('sellers.index') }}" class="filter-bar">
        @if(isset($firms) && $firms->isNotEmpty())
            <div class="filter-field filter-field-select">
                <label class="filter-label"><i class="fa-solid fa-building" style="color: #60A5FA;"></i> Firm</label>
                <select name="firm_id" class="filter-control" onchange="this.form.submit()">
                    <option value="">All Firms</option>
                    @foreach($firms as $f)
                        <option value="{{ $f->id }}" {{ request('firm_id') == $f->id ? 'selected' : '' }}>{{ $f->firm_name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="filter-field filter-field-select">
            <label class="filter-label"><i class="fa-solid fa-circle-check" style="color: #34D399;"></i> Status</label>
            <select name="status" class="filter-control" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="filter-field filter-field-search">
            <label class="filter-label"><i class="fa-solid fa-magnifying-glass" style="color: #FBBF24;"></i> Search</label>
            <div class="search-input-wrapper">
                <i class="fa-solid fa-magnifying-glass search-inner-icon"></i>
                <input type="text" name="search" value="{{ request('search') }}" class="search-control" placeholder="Search seller by name, phone, PAN, city...">
                @if(request('search'))
                    <a href="{{ route('sellers.index', request()->except('search')) }}" class="search-clear-btn" title="Clear search">&times;</a>
                @endif
            </div>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn-filter" title="Apply Filters">
                <i class="fa-solid fa-sliders"></i>
                <span>Filter</span>
            </button>
            @if(request()->hasAny(['search', 'status', 'firm_id']))
                <a href="{{ route('sellers.index') }}" class="btn-reset" title="Reset all filters">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset</span>
                </a>
            @endif
        </div>
    </form>

    {{-- ── Table or Empty State ── --}}
    @if($sellers->isEmpty())
        <div class="empty-state-container">
            <div class="empty-state-icon">
                <i class="fa-solid fa-user-xmark"></i>
            </div>
            <h3 class="empty-state-title">No Sellers Found</h3>
            <p class="empty-state-desc">
                @if(request()->hasAny(['search', 'status', 'firm_id']))
                    No sellers / landowners match your current filter parameters. Try clearing your filters or changing your search term.
                @else
                    There are no sellers or landowners registered in the system yet. Click below to add your first seller profile.
                @endif
            </p>
            <div class="empty-state-actions">
                @if(request()->hasAny(['search', 'status', 'firm_id']))
                    <a href="{{ route('sellers.index') }}" class="btn-reset-empty">
                        <i class="fa-solid fa-rotate-left"></i> Clear All Filters
                    </a>
                @endif
                <a href="{{ route('sellers.create') }}" class="btn-gold">
                    <i class="fa-solid fa-plus"></i> Add New Seller
                </a>
            </div>
        </div>
    @else
        <div class="table-responsive-wrapper">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="min-width: 200px;">Seller Name</th>
                        <th style="min-width: 170px;">Contact Info</th>
                        <th style="min-width: 120px;">Type</th>
                        <th style="min-width: 170px;">Identity (PAN / Aadhaar)</th>
                        <th style="min-width: 170px;">Bank Details</th>
                        <th style="min-width: 150px;">Linked Properties</th>
                        <th style="min-width: 100px;">Status</th>
                        <th style="width: 130px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sellers as $key => $seller)
                    <tr>
                        {{-- # Index --}}
                        <td class="td-index">
                            {{ method_exists($sellers, 'firstItem') ? ($sellers->firstItem() + $key) : ($key + 1) }}
                        </td>

                        {{-- Seller Name --}}
                        <td>
                            <div class="seller-name-row">
                                <i class="fa-solid fa-user-tag seller-avatar-icon"></i>
                                <div class="seller-name-info">
                                    <strong class="seller-name-text">{{ $seller->name }}</strong>
                                    @if($seller->city || $seller->state)
                                        <div class="seller-location-sub">
                                            <i class="fa-solid fa-location-dot"></i>
                                            <span>{{ $seller->city }}{{ $seller->state ? ', ' . $seller->state : '' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Contact Info --}}
                        <td>
                            @if($seller->mobile || $seller->phone || $seller->email)
                                <div class="contact-info-wrap">
                                    @if($seller->mobile || $seller->phone)
                                        <div class="phone-chip">
                                            <i class="fa-solid fa-phone"></i>
                                            <span>{{ $seller->mobile ?: $seller->phone }}</span>
                                        </div>
                                    @endif
                                    @if($seller->email)
                                        <div class="email-sub" title="{{ $seller->email }}">
                                            <i class="fa-regular fa-envelope"></i>
                                            <span>{{ $seller->email }}</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted-dash">—</span>
                            @endif
                        </td>

                        {{-- Type --}}
                        <td>
                            <span class="seller-type-pill">
                                {{ ucfirst(str_replace('_', ' ', $seller->seller_type ?? 'Individual')) }}
                            </span>
                        </td>

                        {{-- Identity (PAN / Aadhaar) --}}
                        <td>
                            @if($seller->pan_no || $seller->aadhaar_no)
                                <div class="identity-wrap">
                                    @if($seller->pan_no)
                                        <div class="identity-badge pan-badge">
                                            <span class="id-tag">PAN</span>
                                            <span class="id-val">{{ $seller->pan_no }}</span>
                                        </div>
                                    @endif
                                    @if($seller->aadhaar_no)
                                        <div class="identity-badge aadhaar-badge">
                                            <span class="id-tag">UID</span>
                                            <span class="id-val">{{ $seller->aadhaar_no }}</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted-dash">—</span>
                            @endif
                        </td>

                        {{-- Bank Details --}}
                        <td>
                            @if($seller->bank_name || $seller->account_number)
                                <div class="bank-wrap">
                                    @if($seller->bank_name)
                                        <div class="bank-name-text">
                                            <i class="fa-solid fa-building-columns"></i>
                                            <span>{{ $seller->bank_name }}</span>
                                        </div>
                                    @endif
                                    @if($seller->account_number)
                                        <div class="bank-ac-text">
                                            <span>A/C:</span> <code>{{ $seller->account_number }}</code>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted-dash">—</span>
                            @endif
                        </td>

                        {{-- Linked Properties --}}
                        <td>
                            @php $pCount = $seller->propertyMasters ? $seller->propertyMasters->count() : 0; @endphp
                            @if($pCount > 0)
                                <span class="linked-properties-badge">
                                    <i class="fa-solid fa-shapes"></i>
                                    <span>{{ $pCount }} {{ \Illuminate\Support\Str::plural('Property', $pCount) }}</span>
                                </span>
                            @else
                                <span class="text-muted-dash">None</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="badge {{ $seller->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                {{ ucfirst($seller->status) }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td style="text-align: right;">
                            <div class="action-buttons-wrap">
                                <a href="{{ route('sellers.show', $seller->id) }}" class="btn-action-icon btn-action-view" title="View Seller Profile">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                <a href="{{ route('sellers.edit', $seller->id) }}" class="btn-action-icon btn-action-edit" title="Edit Seller">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                <form method="POST" action="{{ route('sellers.destroy', $seller->id) }}" style="display:inline;" id="del-seller-{{ $seller->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-action-icon btn-action-delete" onclick="confirmDeleteSeller({{ $seller->id }})" title="Delete Seller">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ── Pagination ── --}}
        @if(method_exists($sellers, 'links') && $sellers->hasPages())
            <div class="pagination-wrapper">
                {{ $sellers->appends(request()->query())->links() }}
            </div>
        @endif
    @endif
</div>

<style>
/* ================================================================
   1. CRUD HEADER & ACTION BUTTONS
================================================================ */
.crud-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 4px;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.crud-title {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.title-with-badge {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.crud-title h2 {
    font-size: 26px;
    font-weight: 800;
    color: #FFFFFF !important;
    margin: 0;
    letter-spacing: -0.3px;
    line-height: 1.2;
}
.count-pill {
    background: rgba(245, 158, 11, 0.16);
    color: #FDE68A;
    border: 1px solid rgba(245, 158, 11, 0.30);
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.2px;
}
.crud-title p {
    font-size: 13.5px;
    color: #94A3B8 !important;
    font-weight: 500;
    margin: 0;
}
.crud-actions {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

/* Header Buttons */
.btn-gold {
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
    color: #FFFFFF !important;
    padding: 10px 20px;
    border-radius: 12px;
    text-decoration: none !important;
    font-size: 13.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #3B82F6 !important;
    cursor: pointer;
    transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35);
    white-space: nowrap;
}
.btn-gold:hover {
    background: linear-gradient(135deg, #1D4ED8 0%, #1E40AF 100%) !important;
    color: #FFFFFF !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 22px rgba(37, 99, 235, 0.50);
}

.btn-pdf-red {
    background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%) !important;
    color: #FFFFFF !important;
    padding: 10px 20px;
    border-radius: 12px;
    text-decoration: none !important;
    font-size: 13.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #F87171 !important;
    cursor: pointer;
    transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 16px rgba(239, 68, 68, 0.35);
    white-space: nowrap;
}
.btn-pdf-red:hover {
    background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%) !important;
    color: #FFFFFF !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 22px rgba(239, 68, 68, 0.55);
}

/* ================================================================
   2. KPI METRIC CARDS
================================================================ */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 18px;
    margin-bottom: 24px;
}
.kpi-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 18px !important;
    padding: 18px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    transition: all 0.2s ease;
}
.kpi-card:hover {
    border-color: rgba(255, 255, 255, 0.22) !important;
    transform: translateY(-2px);
    box-shadow: 0 14px 36px rgba(0, 0, 0, 0.35);
}
.kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.kpi-icon.amber {
    background: rgba(245, 158, 11, 0.18);
    color: #FBBF24;
    border: 1px solid rgba(245, 158, 11, 0.35);
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.20);
}
.kpi-icon.emerald {
    background: rgba(16, 185, 129, 0.18);
    color: #34D399;
    border: 1px solid rgba(16, 185, 129, 0.35);
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.20);
}
.kpi-icon.blue {
    background: rgba(59, 130, 246, 0.18);
    color: #60A5FA;
    border: 1px solid rgba(59, 130, 246, 0.35);
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.20);
}
.kpi-data h3 {
    font-size: 24px;
    font-weight: 800;
    color: #FFFFFF !important;
    margin: 0 0 2px 0;
    line-height: 1.2;
}
.kpi-data p {
    font-size: 11.5px;
    color: #94A3B8 !important;
    font-weight: 700;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.6px;
}

/* ================================================================
   3. MAIN CARD BOX
================================================================ */
.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important;
    padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 28px;
}

/* ================================================================
   4. MODERN FILTER BAR
================================================================ */
.filter-bar {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 14px !important;
    align-items: flex-end !important;
    background: rgba(255, 255, 255, 0.03) !important;
    padding: 16px 18px !important;
    border-radius: 16px !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    margin-bottom: 22px !important;
}
.filter-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.filter-field-select {
    min-width: 150px;
    flex-shrink: 0;
}
.filter-field-search {
    flex: 1 1 240px;
    min-width: 220px;
}
.filter-label {
    font-size: 11px;
    font-weight: 800;
    color: #94A3B8;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 0;
}
.filter-control {
    height: 42px;
    padding: 0 14px;
    background: rgba(10, 15, 26, 0.85) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 10px !important;
    color: #FFFFFF !important;
    font-size: 13.5px !important;
    font-weight: 500;
    outline: none;
    transition: all 0.2s ease;
    width: 100%;
    box-sizing: border-box;
    cursor: pointer;
}
.filter-control option {
    background: #111827 !important;
    color: #FFFFFF !important;
    padding: 8px !important;
}
.filter-control:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}

/* Search input wrapper */
.search-input-wrapper {
    position: relative;
    width: 100%;
}
.search-inner-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748B;
    font-size: 13px;
    pointer-events: none;
}
.search-control {
    height: 42px;
    width: 100%;
    padding: 0 36px 0 38px !important;
    background: rgba(10, 15, 26, 0.85) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 10px !important;
    color: #FFFFFF !important;
    font-size: 13.5px !important;
    outline: none;
    transition: all 0.2s ease;
    box-sizing: border-box;
}
.search-control::placeholder {
    color: #64748B !important;
}
.search-control:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
    background: rgba(16, 23, 38, 0.95) !important;
}
.search-clear-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94A3B8;
    text-decoration: none;
    font-size: 18px;
    line-height: 1;
    transition: color 0.15s ease;
}
.search-clear-btn:hover {
    color: #F87171;
}

/* Filter Actions */
.filter-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}
.btn-filter {
    height: 42px;
    padding: 0 20px;
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
    border: 1px solid #3B82F6 !important;
    border-radius: 10px !important;
    color: #FFFFFF !important;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    transition: all 0.2s ease;
    white-space: nowrap;
}
.btn-filter:hover {
    background: linear-gradient(135deg, #1D4ED8 0%, #1E40AF 100%) !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50);
}
.btn-reset {
    height: 42px;
    padding: 0 16px;
    background: rgba(255, 255, 255, 0.06) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 10px !important;
    color: #CBD5E1 !important;
    text-decoration: none !important;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.btn-reset:hover {
    background: rgba(255, 255, 255, 0.12) !important;
    color: #FFFFFF !important;
    border-color: rgba(255, 255, 255, 0.22) !important;
}

/* ================================================================
   5. DEDICATED EMPTY STATE
================================================================ */
.empty-state-container {
    text-align: center;
    padding: 56px 24px;
    background: rgba(14, 20, 32, 0.40);
    border-radius: 16px;
    border: 1px dashed rgba(255, 255, 255, 0.14);
}
.empty-state-icon {
    width: 68px;
    height: 68px;
    margin: 0 auto 18px;
    background: rgba(245, 158, 11, 0.14);
    border: 1px solid rgba(245, 158, 11, 0.28);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: #FBBF24;
    box-shadow: 0 8px 24px rgba(245, 158, 11, 0.15);
}
.empty-state-title {
    font-size: 20px;
    font-weight: 800;
    color: #FFFFFF;
    margin: 0 0 8px 0;
}
.empty-state-desc {
    font-size: 14px;
    color: #94A3B8;
    max-width: 480px;
    margin: 0 auto 24px;
    line-height: 1.5;
}
.empty-state-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}
.btn-reset-empty {
    padding: 10px 20px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 12px;
    color: #CBD5E1;
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}
.btn-reset-empty:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #FFFFFF;
}

/* ================================================================
   6. PREMIUM TABLE & CELLS
================================================================ */
.table-responsive-wrapper {
    width: 100% !important;
    overflow-x: auto !important;
    border-radius: 16px !important;
    border: 1px solid rgba(255, 255, 255, 0.10) !important;
    background: rgba(14, 20, 32, 0.45) !important;
    box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.20);
}
.premium-table {
    width: 100% !important;
    border-collapse: collapse !important;
    text-align: left !important;
    font-size: 13.5px !important;
}
.premium-table th {
    padding: 14px 18px !important;
    background: rgba(255, 255, 255, 0.04) !important;
    color: #94A3B8 !important;
    font-weight: 800 !important;
    font-size: 11px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.8px !important;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 14px 18px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    color: #E2E8F0 !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    vertical-align: middle !important;
    white-space: nowrap;
}
.premium-table tbody tr {
    transition: background 0.15s ease;
}
.premium-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.04) !important;
}
.premium-table tbody tr:last-child td {
    border-bottom: none !important;
}

.td-index {
    color: #64748B !important;
    font-weight: 800;
}
.seller-name-row {
    display: flex;
    align-items: center;
    gap: 10px;
}
.seller-avatar-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(245, 158, 11, 0.16);
    border: 1px solid rgba(245, 158, 11, 0.30);
    color: #FBBF24;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.seller-name-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.seller-name-text {
    color: #FFFFFF !important;
    font-size: 14px;
    font-weight: 700;
}
.seller-location-sub {
    font-size: 11.5px;
    color: #94A3B8;
    display: flex;
    align-items: center;
    gap: 4px;
}
.seller-location-sub i {
    font-size: 9.5px;
    color: #60A5FA;
}

/* Contact Info */
.contact-info-wrap {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.phone-chip {
    color: #60A5FA;
    font-weight: 600;
    font-size: 12.5px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.phone-chip i {
    font-size: 10.5px;
}
.email-sub {
    font-size: 11px;
    color: #94A3B8;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Type Pill */
.seller-type-pill {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #CBD5E1;
    padding: 3px 9px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

/* Identity Badges */
.identity-wrap {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.identity-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    padding: 2px 7px;
    border-radius: 5px;
}
.pan-badge {
    background: rgba(245, 158, 11, 0.12);
    border: 1px solid rgba(245, 158, 11, 0.25);
    color: #FCD34D;
}
.aadhaar-badge {
    background: rgba(59, 130, 246, 0.12);
    border: 1px solid rgba(59, 130, 246, 0.25);
    color: #93C5FD;
}
.identity-badge .id-tag {
    font-size: 9.5px;
    font-weight: 800;
    text-transform: uppercase;
    opacity: 0.85;
}
.identity-badge .id-val {
    font-family: monospace;
    font-weight: 600;
}

/* Bank Details */
.bank-wrap {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.bank-name-text {
    color: #34D399;
    font-weight: 700;
    font-size: 12.5px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.bank-name-text i {
    font-size: 10.5px;
}
.bank-ac-text {
    font-size: 11px;
    color: #94A3B8;
}
.bank-ac-text code {
    color: #CBD5E1;
    font-family: monospace;
}

/* Linked Properties Badge */
.linked-properties-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(59, 130, 246, 0.15);
    border: 1px solid rgba(59, 130, 246, 0.30);
    color: #93C5FD;
    padding: 3.5px 10px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 700;
}

/* Status Badges */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 11.5px;
    text-transform: capitalize;
}
.badge-active {
    background: rgba(16, 185, 129, 0.16) !important;
    color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.32) !important;
}
.badge-inactive {
    background: rgba(239, 68, 68, 0.16) !important;
    color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.32) !important;
}
.text-muted-dash {
    color: #64748B;
}

/* Action Buttons */
.action-buttons-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
    white-space: nowrap;
}
.btn-action-icon {
    width: 34px !important;
    height: 34px !important;
    border-radius: 9px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 13px !important;
    text-decoration: none !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    border: 1px solid transparent !important;
    box-sizing: border-box !important;
}
.btn-action-icon:hover {
    transform: translateY(-2px) !important;
}
.btn-action-view {
    background: rgba(16, 185, 129, 0.15) !important;
    border-color: rgba(16, 185, 129, 0.35) !important;
    color: #34D399 !important;
}
.btn-action-view:hover {
    background: rgba(16, 185, 129, 0.28) !important;
    border-color: #10B981 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35) !important;
}
.btn-action-edit {
    background: rgba(59, 130, 246, 0.15) !important;
    border-color: rgba(59, 130, 246, 0.35) !important;
    color: #60A5FA !important;
}
.btn-action-edit:hover {
    background: rgba(59, 130, 246, 0.28) !important;
    border-color: #3B82F6 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35) !important;
}
.btn-action-delete {
    background: rgba(239, 68, 68, 0.15) !important;
    border-color: rgba(239, 68, 68, 0.35) !important;
    color: #F87171 !important;
    cursor: pointer !important;
}
.btn-action-delete:hover {
    background: rgba(239, 68, 68, 0.28) !important;
    border-color: #EF4444 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35) !important;
}

/* ================================================================
   7. PAGINATION WRAPPER
================================================================ */
.pagination-wrapper {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDeleteSeller(id) {
    Swal.fire({
        title: 'Delete Seller Profile?',
        text: 'Are you sure you want to delete this seller record? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Delete',
        background: '#111827',
        color: '#FFFFFF'
    }).then(r => { 
        if (r.isConfirmed) document.getElementById('del-seller-' + id).submit(); 
    });
}
</script>
@endsection
