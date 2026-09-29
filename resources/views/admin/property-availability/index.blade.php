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
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; text-shadow: 0 2px 10px rgba(0,0,0,0.5); }
.crud-title p { font-size: 14px; color: #E2E8F0 !important; font-weight: 600; margin: 0; text-shadow: 0 1px 6px rgba(0,0,0,0.5); }

/* ── 6-Card KPI Grid (Clean 1-Row Grid with High-Contrast Dark Glass) ── */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
@media (max-width: 1200px) {
    .kpi-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 680px) {
    .kpi-grid { grid-template-columns: repeat(2, 1fr); }
}

.kpi-pill-card {
    background: rgba(13, 19, 33, 0.92) !important;
    backdrop-filter: blur(24px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(24px) saturate(180%) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 16px !important;
    padding: 16px 18px !important;
    text-decoration: none !important;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 95px;
    transition: all 0.25s ease;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45);
    position: relative;
    overflow: hidden;
}
.kpi-pill-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 36px rgba(0, 0, 0, 0.55);
}

.kpi-pill-label {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    white-space: nowrap;
}
.kpi-pill-val {
    font-size: 26px;
    font-weight: 900;
    letter-spacing: -0.5px;
    line-height: 1;
}

/* Individual Card Theming */
.kpi-card-all {
    border-color: rgba(148, 163, 184, 0.35) !important;
}
.kpi-card-all .kpi-pill-label { color: #CBD5E1; }
.kpi-card-all .kpi-pill-val { color: #FFFFFF; }
.kpi-card-all.active, .kpi-card-all:hover {
    border-color: #94A3B8 !important;
    background: rgba(30, 41, 59, 0.95) !important;
}

.kpi-card-available {
    background: rgba(6, 44, 30, 0.88) !important;
    border-color: rgba(52, 211, 153, 0.45) !important;
}
.kpi-card-available .kpi-pill-label { color: #34D399; }
.kpi-card-available .kpi-pill-val { color: #34D399; }
.kpi-card-available.active, .kpi-card-available:hover {
    border-color: #34D399 !important;
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.35), 0 10px 30px rgba(0,0,0,0.5) !important;
}

.kpi-card-booked {
    background: rgba(45, 30, 5, 0.88) !important;
    border-color: rgba(251, 191, 36, 0.45) !important;
}
.kpi-card-booked .kpi-pill-label { color: #FBBF24; }
.kpi-card-booked .kpi-pill-val { color: #FBBF24; }
.kpi-card-booked.active, .kpi-card-booked:hover {
    border-color: #FBBF24 !important;
    box-shadow: 0 0 20px rgba(245, 158, 11, 0.35), 0 10px 30px rgba(0,0,0,0.5) !important;
}

.kpi-card-sold {
    background: rgba(48, 13, 13, 0.88) !important;
    border-color: rgba(248, 113, 113, 0.45) !important;
}
.kpi-card-sold .kpi-pill-label { color: #F87171; }
.kpi-card-sold .kpi-pill-val { color: #F87171; }
.kpi-card-sold.active, .kpi-card-sold:hover {
    border-color: #F87171 !important;
    box-shadow: 0 0 20px rgba(239, 68, 68, 0.35), 0 10px 30px rgba(0,0,0,0.5) !important;
}

.kpi-card-rented {
    background: rgba(10, 32, 64, 0.88) !important;
    border-color: rgba(96, 165, 250, 0.45) !important;
}
.kpi-card-rented .kpi-pill-label { color: #60A5FA; }
.kpi-card-rented .kpi-pill-val { color: #60A5FA; }
.kpi-card-rented.active, .kpi-card-rented:hover {
    border-color: #60A5FA !important;
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.35), 0 10px 30px rgba(0,0,0,0.5) !important;
}

.kpi-card-reserved {
    background: rgba(36, 16, 56, 0.88) !important;
    border-color: rgba(192, 132, 252, 0.45) !important;
}
.kpi-card-reserved .kpi-pill-label { color: #C084FC; }
.kpi-card-reserved .kpi-pill-val { color: #C084FC; }
.kpi-card-reserved.active, .kpi-card-reserved:hover {
    border-color: #C084FC !important;
    box-shadow: 0 0 20px rgba(139, 92, 246, 0.35), 0 10px 30px rgba(0,0,0,0.5) !important;
}

.card-box {
    background: rgba(13, 19, 33, 0.90) !important;
    backdrop-filter: blur(24px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(24px) saturate(180%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 22px !important;
    padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45) !important;
    margin-bottom: 24px;
}

.filter-bar {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;
    background: rgba(255, 255, 255, 0.04) !important; padding: 14px 18px !important;
    border-radius: 14px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
}
.search-form { display: flex; gap: 10px; flex: 1; flex-wrap: wrap; }
.search-input {
    padding: 10px 14px; background: rgba(16, 22, 34, 0.85) !important;
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
.table-container { width: 100%; overflow-x: auto; background: rgba(16, 22, 34, 0.75) !important; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); }
.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 13px 16px; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap;
}
.premium-table td {
    padding: 13px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    color: #FFFFFF !important; font-weight: 600; vertical-align: middle;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.04) !important; }

/* Status Badges */
.badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; font-size: 11px; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: .4px; white-space: nowrap; }
.badge i { font-size: 8px; }
.badge-available         { background: rgba(16, 185, 129, 0.22) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.45) !important; }
.badge-booked            { background: rgba(245, 158, 11, 0.22) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.45) !important; }
.badge-sold              { background: rgba(239, 68, 68, 0.22) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.45) !important; }
.badge-rented            { background: rgba(59, 130, 246, 0.22) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.45) !important; }
.badge-reserved          { background: rgba(139, 92, 246, 0.22) !important; color: #A78BFA !important; border: 1px solid rgba(139, 92, 246, 0.45) !important; }
.badge-under_maintenance { background: rgba(148, 163, 184, 0.22) !important; color: #CBD5E1 !important; border: 1px solid rgba(148, 163, 184, 0.45) !important; }

.prop-code-badge { font-size: 11px; font-weight: 800; color: #60A5FA; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.35); padding: 2px 8px; border-radius: 6px; display: inline-block; }
.type-badge { font-size: 11px; font-weight: 700; color: #CBD5E1; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); padding: 2px 8px; border-radius: 6px; display: inline-block; }

/* Action buttons */
.btn-action-status {
    background: rgba(59, 130, 246, 0.18); border: 1px solid rgba(59, 130, 246, 0.40); color: #60A5FA;
    padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all .2s;
    display: inline-flex; align-items: center; gap: 6px; text-decoration: none !important;
}
.btn-action-status:hover { background: #2563EB; color: #FFFFFF; border-color: #3B82F6; transform: translateY(-1px); }

.btn-view {
    background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.18); color: #FFFFFF;
    padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .2s;
    display: inline-flex; align-items: center; gap: 5px; text-decoration: none !important;
}
.btn-view:hover { background: rgba(255, 255, 255, 0.18); transform: translateY(-1px); }

/* Quick Status Modal */
.modal-overlay {
    position: fixed; inset: 0; background: rgba(5, 10, 20, 0.85); backdrop-filter: blur(8px);
    display: none; align-items: center; justify-content: center; z-index: 9999; padding: 16px;
}
.modal-overlay.active { display: flex; }
.modal-card {
    background: #131B2E; border: 1.5px solid rgba(255, 255, 255, 0.15); border-radius: 20px;
    width: 100%; max-width: 480px; padding: 26px; box-shadow: 0 24px 60px rgba(0,0,0,0.6);
    position: relative; animation: modalIn 0.2s ease-out;
}
@keyframes modalIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.modal-header h3 { font-size: 18px; font-weight: 800; color: #FFFFFF; margin: 0; display: flex; align-items: center; gap: 8px; }
.modal-close-btn { background: transparent; border: none; color: #94A3B8; font-size: 20px; cursor: pointer; }
.modal-close-btn:hover { color: #F87171; }
.m-group { margin-bottom: 16px; }
.m-label { display: block; font-size: 12px; font-weight: 800; color: #CBD5E1; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
.m-control {
    width: 100%; padding: 10px 14px; background: rgba(10, 15, 26, 0.85);
    border: 1.5px solid rgba(255, 255, 255, 0.15); border-radius: 10px;
    color: #FFFFFF; font-size: 13.5px; outline: none; transition: border-color .2s; box-sizing: border-box;
}
.m-control:focus { border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25); }
select.m-control option { background: #111827; color: #FFF; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Property Availability / Status</h2>
        <p>Live, auto-synchronized availability status of every property, plot, and unit.</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <button type="button" onclick="openQuickStatusModal('', '', 'available')" class="btn-pc">
            <i class="fa-solid fa-arrows-rotate"></i> Update Status
        </button>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.16); border: 1px solid rgba(16, 185, 129, 0.38); color: #34D399; border-radius: 12px; padding: 14px 18px; margin-bottom: 22px; font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-circle-check"></i> <span>{{ session('success') }}</span>
    </div>
@endif

{{-- ── 6 KPI Status Overview Cards (Clean English with Solid Glowing Glass) ── --}}
<div class="kpi-grid">
    {{-- ALL --}}
    <a href="{{ route('property-availability.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
       class="kpi-pill-card kpi-card-all {{ !request('status') || request('status') === 'all' ? 'active' : '' }}">
        <div class="kpi-pill-label">
            <span><i class="fa-solid fa-cubes"></i> Total Properties</span>
            <i class="fa-solid fa-chevron-right" style="font-size: 9px;"></i>
        </div>
        <div class="kpi-pill-val">{{ number_format($totalUnitsCount) }}</div>
    </a>

    {{-- AVAILABLE --}}
    <a href="{{ route('property-availability.index', array_merge(request()->except('status', 'page'), ['status' => 'available'])) }}"
       class="kpi-pill-card kpi-card-available {{ request('status') === 'available' ? 'active' : '' }}">
        <div class="kpi-pill-label">
            <span><i class="fa-solid fa-circle-check"></i> Available</span>
            <i class="fa-solid fa-chevron-right" style="font-size: 9px;"></i>
        </div>
        <div class="kpi-pill-val">{{ number_format($availableCount) }}</div>
    </a>

    {{-- BOOKED --}}
    <a href="{{ route('property-availability.index', array_merge(request()->except('status', 'page'), ['status' => 'booked'])) }}"
       class="kpi-pill-card kpi-card-booked {{ request('status') === 'booked' ? 'active' : '' }}">
        <div class="kpi-pill-label">
            <span><i class="fa-solid fa-bookmark"></i> Booked</span>
            <i class="fa-solid fa-chevron-right" style="font-size: 9px;"></i>
        </div>
        <div class="kpi-pill-val">{{ number_format($bookedCount) }}</div>
    </a>

    {{-- SOLD --}}
    <a href="{{ route('property-availability.index', array_merge(request()->except('status', 'page'), ['status' => 'sold'])) }}"
       class="kpi-pill-card kpi-card-sold {{ request('status') === 'sold' ? 'active' : '' }}">
        <div class="kpi-pill-label">
            <span><i class="fa-solid fa-circle-xmark"></i> Sold</span>
            <i class="fa-solid fa-chevron-right" style="font-size: 9px;"></i>
        </div>
        <div class="kpi-pill-val">{{ number_format($soldCount) }}</div>
    </a>

    {{-- RENTED --}}
    <a href="{{ route('property-availability.index', array_merge(request()->except('status', 'page'), ['status' => 'rented'])) }}"
       class="kpi-pill-card kpi-card-rented {{ request('status') === 'rented' ? 'active' : '' }}">
        <div class="kpi-pill-label">
            <span><i class="fa-solid fa-key"></i> Rented</span>
            <i class="fa-solid fa-chevron-right" style="font-size: 9px;"></i>
        </div>
        <div class="kpi-pill-val">{{ number_format($rentedCount) }}</div>
    </a>

    {{-- RESERVED / MAINT --}}
    <a href="{{ route('property-availability.index', array_merge(request()->except('status', 'page'), ['status' => 'reserved'])) }}"
       class="kpi-pill-card kpi-card-reserved {{ request('status') === 'reserved' || request('status') === 'under_maintenance' ? 'active' : '' }}">
        <div class="kpi-pill-label">
            <span><i class="fa-solid fa-shield-halved"></i> Reserved / Maint</span>
            <i class="fa-solid fa-chevron-right" style="font-size: 9px;"></i>
        </div>
        <div class="kpi-pill-val">{{ number_format($reservedCount) }}</div>
    </a>
</div>

<div class="card-box">
    {{-- ── Filter & Search Bar ── --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('property-availability.index') }}" class="search-form">
            @if(auth()->user() && auth()->user()->isAdmin())
                <select name="firm_id" class="search-input" onchange="this.form.submit()" style="max-width: 170px;">
                    <option value="">All Firms</option>
                    @foreach($firms as $firm)
                        <option value="{{ $firm->id }}" {{ request('firm_id') == $firm->id ? 'selected' : '' }}>
                            {{ $firm->firm_name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <select name="property_master_id" class="search-input" onchange="this.form.submit()" style="max-width: 200px;">
                <option value="">All Property Masters</option>
                @foreach($propertyMasters as $pm)
                    <option value="{{ $pm->id }}" {{ request('property_master_id') == $pm->id ? 'selected' : '' }}>
                        {{ $pm->property_name }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="search-input" onchange="this.form.submit()" style="max-width: 150px;">
                <option value="all">All Statuses</option>
                @foreach($statuses as $k => $v)
                    <option value="{{ $k }}" {{ request('status') == $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>

            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name, plot no, code, location..." class="search-input" style="flex: 1; min-width: 180px;">
            <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
            @if(request('search') || request('firm_id') || request('property_master_id') || (request('status') && request('status') !== 'all'))
                <a href="{{ route('property-availability.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
            @endif
        </form>
    </div>

    {{-- ── Properties Table ── --}}
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th style="width: 35px;">#</th>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <th>Firm</th>
                    @endif
                    <th>Property / Project</th>
                    <th>Plot / Unit No</th>
                    <th>Type</th>
                    <th>Size / Area</th>
                    <th>Valuation (₹)</th>
                    <th>Current Live Status</th>
                    <th>Assigned To / Buyer</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($properties as $i => $prop)
                @php
                    $buyerName = '—';
                    if ($prop->status === 'sold') {
                        $sale = $prop->sales->first();
                        $buyerName = $sale && $sale->customer ? $sale->customer->name : 'Sold';
                    } elseif ($prop->status === 'booked') {
                        $booking = $prop->bookings->where('status', '!=', 'cancelled')->first();
                        $buyerName = $booking && $booking->customer ? $booking->customer->name : 'Booked';
                    } elseif ($prop->status === 'rented') {
                        $rental = $prop->rentals->where('rental_status', 'active')->first();
                        $buyerName = $rental && $rental->tenant ? $rental->tenant->name : 'Tenant';
                    }
                @endphp
                <tr>
                    <td style="color: #94A3B8;">{{ $properties->firstItem() + $i }}</td>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <td><strong style="color: #FFFFFF;">{{ $prop->firm->firm_name ?? 'N/A' }}</strong></td>
                    @endif
                    <td>
                        <strong style="color: #FFFFFF; font-size: 14px;">{{ $prop->property_name }}</strong>
                        @if($prop->propertyMaster)
                            <div style="font-size: 11.5px; color: #94A3B8; margin-top: 2px;">
                                <i class="fa-solid fa-layer-group" style="font-size: 10px; color: #60A5FA;"></i> {{ $prop->propertyMaster->property_name }}
                            </div>
                        @elseif($prop->project)
                            <div style="font-size: 11.5px; color: #94A3B8; margin-top: 2px;">
                                <i class="fa-solid fa-city" style="font-size: 10px; color: #F59E0B;"></i> {{ $prop->project->project_name }}
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($prop->property_code || $prop->unit_no)
                            <span class="prop-code-badge">{{ $prop->property_code ?: 'Unit #'.$prop->unit_no }}</span>
                        @else
                            <span style="color: #64748B;">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="type-badge">{{ $prop->propertyType->name ?? 'Plot' }}</span>
                    </td>
                    <td>
                        @if($prop->size)
                            <span style="color: #CBD5E1; font-weight: 700;">{{ number_format((float)$prop->size, 2) }} {{ $prop->size_unit ?? 'Sq.Ft' }}</span>
                        @else
                            <span style="color: #64748B;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($prop->price > 0)
                            <strong style="color: #34D399; font-size: 13.5px;">₹{{ number_format($prop->price, 2) }}</strong>
                        @else
                            <span style="color: #64748B;">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $prop->status }}">
                            <i class="fa-solid fa-circle"></i>
                            {{ ucfirst(str_replace('_', ' ', $prop->status)) }}
                        </span>
                    </td>
                    <td>
                        @if($buyerName !== '—')
                            <span style="color: #93C5FD; font-weight: 700;"><i class="fa-solid fa-user-check" style="font-size: 11px;"></i> {{ $buyerName }}</span>
                        @else
                            <span style="color: #64748B;">—</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; gap: 6px; align-items: center;">
                            <button type="button" class="btn-action-status"
                                    onclick="openQuickStatusModal('{{ $prop->id }}', '{{ addslashes($prop->property_name) }}', '{{ $prop->status }}')">
                                <i class="fa-solid fa-arrows-rotate"></i> Change Status
                            </button>
                            <a href="{{ route('properties.show', $prop->id) }}" class="btn-view" title="View Property Details">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" align="center" style="padding: 40px; color: #94A3B8;">
                        <div style="font-size: 32px; color: #64748B; margin-bottom: 10px;"><i class="fa-solid fa-folder-open"></i></div>
                        <div style="font-size: 15px; font-weight: 700; color: #FFFFFF;">No properties found matching current filter.</div>
                        <div style="font-size: 13px; color: #94A3B8; margin-top: 4px;">Try changing the filter or search keyword.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($properties->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
            {{ $properties->links() }}
        </div>
    @endif
</div>

{{-- ── Quick Status Update Modal ── --}}
<div class="modal-overlay" id="quickStatusModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3><i class="fa-solid fa-arrows-rotate" style="color: #3B82F6;"></i> Update Property Status</h3>
            <button type="button" class="modal-close-btn" onclick="closeQuickStatusModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('property-availability.store') }}">
            @csrf
            <div class="m-group" id="propertySelectGroup">
                <label class="m-label">Select Property / Plot <span style="color:#F87171;">*</span></label>
                <select name="property_id" id="modalPropertyId" class="m-control" required>
                    <option value="">-- Choose Property / Plot --</option>
                    @foreach(\App\Models\Property::orderBy('property_name')->get() as $p)
                        <option value="{{ $p->id }}">{{ $p->property_name }} ({{ $p->property_code ?: 'Unit '.$p->unit_no }}) - Status: {{ ucfirst($p->status) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="m-group">
                <label class="m-label">New Status <span style="color:#F87171;">*</span></label>
                <select name="status" id="modalStatusSelect" class="m-control" required>
                    @foreach($statuses as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
            </div>

            <div class="m-group">
                <label class="m-label">Status Effective Date</label>
                <input type="date" name="status_date" value="{{ date('Y-m-d') }}" class="m-control">
            </div>

            <div class="m-group">
                <label class="m-label">Remarks / Note</label>
                <textarea name="remarks" class="m-control" rows="2" placeholder="e.g. Customer booked with token advance or under site visit..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.1);">
                <button type="button" class="btn-reset" onclick="closeQuickStatusModal()">Cancel</button>
                <button type="submit" class="btn-pc"><i class="fa-solid fa-check"></i> Save Status</button>
            </div>
        </form>
    </div>
</div>

<script>
function openQuickStatusModal(propId, propName, currentStatus) {
    var modal = document.getElementById('quickStatusModal');
    var select = document.getElementById('modalPropertyId');
    var statusSelect = document.getElementById('modalStatusSelect');

    if (propId) {
        select.value = propId;
    }
    if (currentStatus) {
        statusSelect.value = currentStatus;
    }
    modal.classList.add('active');
}

function closeQuickStatusModal() {
    document.getElementById('quickStatusModal').classList.remove('active');
}

window.onclick = function(event) {
    var modal = document.getElementById('quickStatusModal');
    if (event.target === modal) {
        closeQuickStatusModal();
    }
}
</script>
@endsection
