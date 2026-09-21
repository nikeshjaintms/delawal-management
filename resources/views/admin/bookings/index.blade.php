@extends('admin.layouts.app')
@section('title', 'Bookings')
@section('page-title', 'Booking Management')
@section('content')

{{-- ── Page Header ── --}}
<div class="crud-header">
    <div class="crud-title">
        <div class="title-with-badge">
            <h2>Booking Management</h2>
            @if(isset($bookings) && method_exists($bookings, 'total'))
                <span class="count-pill">{{ $bookings->total() }} {{ \Illuminate\Support\Str::plural('Booking', $bookings->total()) }}</span>
            @endif
        </div>
        <p>Manage property bookings, sales, purchases, and track payment schedules.</p>
    </div>
    <div class="crud-actions">
        <a href="{{ route('bookings.pdf', request()->query()) }}" target="_blank" class="btn-pdf-red" title="Export Bookings to PDF">
            <i class="fa-solid fa-file-pdf"></i>
            <span>Export PDF</span>
        </a>
        <a href="{{ route('bookings.create') }}" class="btn-gold" title="Create New Booking">
            <i class="fa-solid fa-plus"></i>
            <span>Add Booking</span>
        </a>
    </div>
</div>

{{-- ── Session Alerts ── --}}
@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if(session('error'))
    <div class="alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

{{-- ── Main Content Card ── --}}
<div class="card-box">
    {{-- ── Modern Glass Filter Bar ── --}}
    <form method="GET" action="{{ route('bookings.index') }}" class="filter-bar">
        @if(auth()->user() && auth()->user()->isAdmin())
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

        <div class="filter-field filter-field-search">
            <label class="filter-label"><i class="fa-solid fa-magnifying-glass" style="color: #FBBF24;"></i> Search</label>
            <div class="search-input-wrapper">
                <i class="fa-solid fa-magnifying-glass search-inner-icon"></i>
                <input type="text" name="search" value="{{ request('search') }}" class="search-control" placeholder="Search property, customer, mobile, firm, status...">
                @if(request('search'))
                    <a href="{{ route('bookings.index', request()->except('search')) }}" class="search-clear-btn" title="Clear search">&times;</a>
                @endif
            </div>
        </div>

        <div class="filter-field filter-field-select">
            <label class="filter-label"><i class="fa-solid fa-layer-group" style="color: #A78BFA;"></i> Type</label>
            <select name="filter_booking_type" class="filter-control" onchange="this.form.submit()">
                <option value="">All Types</option>
                <option value="booking" {{ request('filter_booking_type') === 'booking' ? 'selected' : '' }}>Booking</option>
                <option value="selling" {{ request('filter_booking_type') === 'selling' ? 'selected' : '' }}>Selling</option>
                <option value="buying"  {{ request('filter_booking_type') === 'buying'  ? 'selected' : '' }}>Buying</option>
            </select>
        </div>

        <div class="filter-field filter-field-select">
            <label class="filter-label"><i class="fa-solid fa-circle-check" style="color: #34D399;"></i> Status</label>
            <select name="filter_status" class="filter-control" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="pending"   {{ request('filter_status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('filter_status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ request('filter_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn-filter" title="Apply Filters">
                <i class="fa-solid fa-sliders"></i>
                <span>Filter</span>
            </button>
            @if(request()->hasAny(['search', 'firm_id', 'filter_status', 'filter_booking_type']))
                <a href="{{ route('bookings.index') }}" class="btn-reset" title="Reset all filters">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset</span>
                </a>
            @endif
        </div>
    </form>

    {{-- ── Table or Empty State ── --}}
    @if($bookings->isEmpty())
        <div class="empty-state-container">
            <div class="empty-state-icon">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
            <h3 class="empty-state-title">No Bookings Found</h3>
            <p class="empty-state-desc">
                @if(request()->hasAny(['search', 'firm_id', 'filter_status', 'filter_booking_type']))
                    No bookings match your current filter parameters. Try adjusting your search query or clearing active filters.
                @else
                    There are no property bookings created yet. Get started by adding your first booking!
                @endif
            </p>
            <div class="empty-state-actions">
                @if(request()->hasAny(['search', 'firm_id', 'filter_status', 'filter_booking_type']))
                    <a href="{{ route('bookings.index') }}" class="btn-reset-empty">
                        <i class="fa-solid fa-rotate-left"></i> Clear All Filters
                    </a>
                @endif
                <a href="{{ route('bookings.create') }}" class="btn-gold">
                    <i class="fa-solid fa-plus"></i> Add New Booking
                </a>
            </div>
        </div>
    @else
        <div class="table-responsive-wrapper">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Type</th>
                        <th>Firm</th>
                        <th>Booking Date</th>
                        <th style="min-width: 240px;">Property / Units</th>
                        <th style="min-width: 170px;">Customer</th>
                        <th>Net Amount</th>
                        <th>Paid Advance</th>
                        <th>Payment Mode</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th style="width: 160px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $key => $booking)
                    @php
                        $allProps = $booking->all_properties;
                        $propCount = $allProps->count();
                        $bType = $booking->booking_type ?? 'booking';
                    @endphp
                    <tr>
                        {{-- # Index --}}
                        <td class="td-index">
                            {{ method_exists($bookings, 'firstItem') ? ($bookings->firstItem() + $key) : ($key + 1) }}
                        </td>

                        {{-- Type --}}
                        <td>
                            @if($bType === 'selling')
                                <span class="badge badge-type-selling"><i class="fa-solid fa-arrow-trend-up"></i> Selling</span>
                            @elseif($bType === 'buying')
                                <span class="badge badge-type-buying"><i class="fa-solid fa-cart-shopping"></i> Buying</span>
                            @else
                                <span class="badge badge-type-booking"><i class="fa-solid fa-bookmark"></i> Booking</span>
                            @endif
                        </td>

                        {{-- Firm --}}
                        <td>
                            <strong class="firm-name-text">{{ $booking->firm->firm_name ?? '-' }}</strong>
                        </td>

                        {{-- Booking Date --}}
                        <td>
                            @if($booking->booking_date)
                                <div class="date-chip">
                                    <i class="fa-regular fa-calendar" style="color: #60A5FA; font-size: 11px;"></i>
                                    <span>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</span>
                                </div>
                            @else
                                <span class="text-muted-dash">-</span>
                            @endif
                        </td>

                        {{-- Property / Units --}}
                        <td>
                            @if($propCount > 1)
                                <div class="booking-plots-compact">
                                    <div class="booking-plots-preview">
                                        @foreach($allProps->take(2) as $p)
                                            <a href="{{ route('properties.show', $p->id) }}" class="plot-pill-chip" title="{{ $p->property_name }} {{ $p->unit_no ? '(#'.$p->unit_no.')' : '' }}">
                                                <i class="fa-solid fa-house" style="font-size: 10px; color: #60A5FA;"></i>
                                                <span>{{ $p->property_name }}</span>
                                                @if($p->unit_no)
                                                    <span class="u-tag">#{{ $p->unit_no }}</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>

                                    <div class="booking-plots-actions">
                                        <button type="button" class="btn-plots-modal-open" onclick="openBookingPlotsModal({{ $booking->id }})" title="View all {{ $propCount }} units in modal">
                                            <i class="fa-solid fa-shapes"></i>
                                            <span>+{{ $propCount - 2 }} More</span>
                                            <span class="btn-view-all-pill">View All {{ $propCount }} &rarr;</span>
                                        </button>
                                        <button type="button" class="btn-inline-toggle-plots" onclick="toggleInlinePlotsList({{ $booking->id }})" id="toggle-btn-{{ $booking->id }}" title="Toggle drawer inline">
                                            <i class="fa-solid fa-chevron-down" id="toggle-icon-{{ $booking->id }}"></i>
                                        </button>
                                    </div>

                                    {{-- Inline Drawer --}}
                                    <div class="inline-plots-drawer hidden" id="inline-plots-{{ $booking->id }}">
                                        <div class="inline-plots-grid">
                                            @foreach($allProps as $idx => $p)
                                                <a href="{{ route('properties.show', $p->id) }}" class="inline-plot-card" title="View {{ $p->property_name }}">
                                                    <span class="ip-num">{{ $idx + 1 }}.</span>
                                                    <span class="ip-name">{{ $p->property_name }}</span>
                                                    @if($p->unit_no) <span class="ip-unit">#{{ $p->unit_no }}</span> @endif
                                                    @if($p->size) <span class="ip-size">{{ $p->formatted_size }}</span> @endif
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- JSON Data Block for Modal --}}
                                    <script type="application/json" id="booking-data-{{ $booking->id }}">
                                        {!! json_encode([
                                            'id' => $booking->id,
                                            'booking_type' => ucfirst($booking->booking_type ?? 'Booking'),
                                            'customer' => $booking->customer->name ?? 'Customer',
                                            'firm' => $booking->firm->firm_name ?? '',
                                            'date' => $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') : '-',
                                            'net_amount' => number_format($booking->final_amount ?: $booking->total_amount, 2),
                                            'plots' => $allProps->map(function($p, $idx) {
                                                return [
                                                    'index' => $idx + 1,
                                                    'id' => $p->id,
                                                    'name' => $p->property_name,
                                                    'code' => $p->property_code,
                                                    'unit_no' => $p->unit_no ?: '-',
                                                    'project' => $p->project->project_name ?? ($p->propertyMaster->property_name ?? 'Direct Plot'),
                                                    'size' => $p->formatted_size ?: ($p->size ? $p->size . ' ' . ($p->size_unit ?? 'sq.ft') : '-'),
                                                    'facing' => $p->facing ?: '-',
                                                    'price' => $p->price ? '₹' . number_format($p->price, 2) : ($p->purchase_rate ? '₹' . number_format($p->purchase_rate, 2) : '-'),
                                                    'url' => route('properties.show', $p->id)
                                                ];
                                            })->values()
                                        ]) !!}
                                    </script>
                                </div>
                            @elseif($propCount === 1)
                                @php $singleProp = $allProps->first(); @endphp
                                <a href="{{ route('properties.show', $singleProp->id) }}" class="plot-single-chip" title="View Plot Details">
                                    <i class="fa-solid fa-house" style="color: #60A5FA;"></i>
                                    <strong>{{ $singleProp->property_name }}</strong>
                                    @if($singleProp->unit_no)
                                        <span class="single-u-tag">#{{ $singleProp->unit_no }}</span>
                                    @endif
                                </a>
                            @else
                                <span class="text-muted-dash">—</span>
                            @endif
                        </td>

                        {{-- Customer --}}
                        <td>
                            <strong class="customer-name-text">{{ $booking->customer->name ?? '-' }}</strong>
                            @if($booking->customer?->phone || $booking->customer?->mobile)
                                <div class="customer-phone-sub">
                                    <i class="fa-solid fa-phone"></i>
                                    <span>{{ $booking->customer->phone ?: $booking->customer->mobile }}</span>
                                </div>
                            @endif
                        </td>

                        {{-- Net Amount --}}
                        <td>
                            @if($booking->final_amount)
                                <div class="amount-wrap">
                                    <strong class="amount-net">₹{{ number_format($booking->final_amount, 2) }}</strong>
                                    @if($booking->discount_amount > 0)
                                        <span class="discount-pill">-₹{{ number_format($booking->discount_amount, 2) }} Off</span>
                                    @endif
                                </div>
                            @elseif($booking->total_amount)
                                <strong class="amount-net">₹{{ number_format($booking->total_amount, 2) }}</strong>
                            @else
                                <span class="text-muted-dash">—</span>
                            @endif
                        </td>

                        {{-- Paid Advance / Booking Amount --}}
                        <td>
                            @if($booking->booking_amount)
                                <span class="paid-advance-pill">
                                    ₹{{ number_format($booking->booking_amount, 2) }}
                                </span>
                            @else
                                <span class="text-muted-dash">—</span>
                            @endif
                        </td>

                        {{-- Payment Mode --}}
                        <td>
                            @if($booking->paymentMode || $booking->payment_mode)
                                <span class="payment-mode-pill">
                                    {{ $booking->paymentMode->name ?? $booking->payment_mode }}
                                </span>
                            @else
                                <span class="text-muted-dash">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="badge badge-{{ $booking->status }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>

                        {{-- Payment Status --}}
                        <td>
                            <span class="badge badge-{{ $booking->payment_status }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td style="text-align: right;">
                            <div class="action-buttons-wrap">
                                <a href="{{ route('bookings.receipt-pdf', $booking->id) }}" target="_blank" class="btn-action-icon btn-action-pdf" title="Download / Print PDF Receipt">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>
                                <a href="{{ route('bookings.show', $booking->id) }}" class="btn-action-icon btn-action-view" title="View Booking Details">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                <a href="{{ route('bookings.edit', $booking->id) }}" class="btn-action-icon btn-action-edit" title="Edit Booking">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" style="display:inline;" id="del-bk-{{ $booking->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-action-icon btn-action-delete" onclick="confirmDelete({{ $booking->id }})" title="Delete Booking">
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
        @if(method_exists($bookings, 'links') && $bookings->hasPages())
            <div class="pagination-wrapper">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        @endif
    @endif
</div>

{{-- ── Booking Plots Detail Modal ── --}}
<div id="bookingPlotsModal" class="custom-modal-overlay">
    <div class="custom-modal-dialog">
        <div class="modal-header-custom">
            <div class="modal-title-wrap">
                <div class="modal-icon-badge">
                    <i class="fa-solid fa-shapes"></i>
                </div>
                <div>
                    <h3 id="bpm_modal_title" class="modal-title">Booked Plots &amp; Units</h3>
                    <p id="bpm_modal_subtitle" class="modal-subtitle"></p>
                </div>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeBookingPlotsModal()" title="Close">&times;</button>
        </div>
        
        <div class="modal-body-custom">
            <div class="table-responsive-wrapper" style="max-height: 420px; overflow-y: auto;">
                <table class="premium-table" id="bpm_plots_table">
                    <thead>
                        <tr>
                            <th style="width: 45px;">#</th>
                            <th>Plot / Unit Name</th>
                            <th>Unit No</th>
                            <th>Code</th>
                            <th>Project / Master</th>
                            <th>Size / Area</th>
                            <th>Facing</th>
                            <th>Price / Rate</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bpm_plots_tbody">
                        {{-- Populated dynamically via JS --}}
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal-footer-custom">
            <div id="bpm_summary_counts" class="modal-summary-counts"></div>
            <button type="button" class="btn-gold" onclick="closeBookingPlotsModal()" style="padding: 8px 22px; min-height: 38px; font-size: 13px;">Close</button>
        </div>
    </div>
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
    background: rgba(59, 130, 246, 0.16);
    color: #93C5FD;
    border: 1px solid rgba(59, 130, 246, 0.30);
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
   2. MAIN CARD BOX
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
   3. MODERN FILTER BAR
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

/* Search input with inside icon */
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
   4. DEDICATED EMPTY STATE (Fixes awkward table scrollbars)
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
    background: rgba(59, 130, 246, 0.14);
    border: 1px solid rgba(59, 130, 246, 0.28);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: #60A5FA;
    box-shadow: 0 8px 24px rgba(59, 130, 246, 0.15);
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
   5. PREMIUM TABLE & CELLS
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
.firm-name-text {
    color: #FFFFFF !important;
    font-weight: 700;
    font-size: 13.5px;
}
.customer-name-text {
    color: #FFFFFF !important;
    font-size: 13.5px;
    font-weight: 700;
    display: block;
}
.customer-phone-sub {
    font-size: 11.5px;
    color: #94A3B8;
    margin-top: 3px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.customer-phone-sub i {
    font-size: 9.5px;
    color: #60A5FA;
}
.date-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #CBD5E1;
    font-weight: 500;
    font-size: 13px;
}
.amount-wrap {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.amount-net {
    color: #FFFFFF !important;
    font-size: 15px;
    font-weight: 800;
    letter-spacing: -0.2px;
}
.discount-pill {
    font-size: 11px;
    color: #FBBF24;
    font-weight: 600;
}
.paid-advance-pill {
    font-size: 13.5px;
    font-weight: 800;
    background: rgba(245, 158, 11, 0.14);
    color: #FBBF24 !important;
    border: 1px solid rgba(245, 158, 11, 0.32);
    padding: 3.5px 10px;
    border-radius: 8px;
    display: inline-block;
}
.payment-mode-pill {
    font-size: 12px;
    color: #CBD5E1;
    background: rgba(255, 255, 255, 0.08);
    padding: 3px 8px;
    border-radius: 6px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    font-weight: 600;
}
.text-muted-dash {
    color: #64748B;
}

/* ================================================================
   6. MULTI-PLOT COMPACT CHIPS & INLINE DRAWER
================================================================ */
.booking-plots-compact {
    display: flex;
    flex-direction: column;
    gap: 6px;
    max-width: 320px;
}
.booking-plots-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-items: center;
}
.plot-pill-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(59, 130, 246, 0.12);
    border: 1px solid rgba(59, 130, 246, 0.28);
    color: #93C5FD !important;
    padding: 3.5px 9px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.plot-pill-chip:hover {
    background: rgba(59, 130, 246, 0.24);
    border-color: rgba(96, 165, 250, 0.50);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    color: #FFFFFF !important;
}
.plot-pill-chip .u-tag {
    background: rgba(255, 255, 255, 0.14);
    color: #FFFFFF;
    font-size: 10px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 4px;
}
.plot-single-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(59, 130, 246, 0.10);
    border: 1px solid rgba(59, 130, 246, 0.22);
    color: #E2E8F0 !important;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 12.5px;
    text-decoration: none;
    transition: all 0.2s ease;
}
.plot-single-chip:hover {
    background: rgba(59, 130, 246, 0.20);
    border-color: rgba(96, 165, 250, 0.45);
    transform: translateY(-1px);
    color: #FFFFFF !important;
}
.single-u-tag {
    background: rgba(59, 130, 246, 0.20);
    color: #93C5FD;
    padding: 1px 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 800;
}
.booking-plots-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-plots-modal-open {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.28) 0%, rgba(147, 51, 234, 0.28) 100%);
    border: 1px solid rgba(96, 165, 250, 0.40);
    color: #FFFFFF;
    padding: 4px 9px;
    border-radius: 7px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.15);
}
.btn-plots-modal-open:hover {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.50) 0%, rgba(147, 51, 234, 0.50) 100%);
    border-color: #60A5FA;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35);
}
.btn-view-all-pill {
    background: rgba(255, 255, 255, 0.18);
    color: #FFFFFF;
    font-size: 10px;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 4px;
}
.btn-inline-toggle-plots {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #94A3B8;
    width: 24px;
    height: 24px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10.5px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-inline-toggle-plots:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #FFFFFF;
}
.inline-plots-drawer {
    margin-top: 6px;
    padding: 8px;
    background: rgba(10, 14, 23, 0.75);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 8px;
}
.inline-plots-drawer.hidden {
    display: none;
}
.inline-plots-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(125px, 1fr));
    gap: 5px;
}
.inline-plot-card {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    padding: 4px 7px;
    border-radius: 6px;
    font-size: 11px;
    color: #CBD5E1 !important;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: all 0.15s ease;
}
.inline-plot-card:hover {
    background: rgba(59, 130, 246, 0.15);
    border-color: rgba(59, 130, 246, 0.35);
    color: #FFFFFF !important;
}
.inline-plot-card .ip-num { color: #60A5FA; font-weight: 700; font-size: 10px; }
.inline-plot-card .ip-name { font-weight: 600; }
.inline-plot-card .ip-unit { color: #FBBF24; font-weight: 700; }
.inline-plot-card .ip-size { color: #94A3B8; font-size: 9.5px; margin-left: auto; }

/* ================================================================
   7. STATUS BADGES
================================================================ */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 11.5px;
    text-transform: capitalize;
    white-space: nowrap;
}
.badge-type-booking {
    background: rgba(59, 130, 246, 0.16) !important;
    color: #60A5FA !important;
    border: 1px solid rgba(59, 130, 246, 0.32) !important;
}
.badge-type-selling {
    background: rgba(16, 185, 129, 0.16) !important;
    color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.32) !important;
}
.badge-type-buying {
    background: rgba(139, 92, 246, 0.16) !important;
    color: #A78BFA !important;
    border: 1px solid rgba(139, 92, 246, 0.32) !important;
}
.badge-pending {
    background: rgba(245, 158, 11, 0.16) !important;
    color: #FBBF24 !important;
    border: 1px solid rgba(245, 158, 11, 0.32) !important;
}
.badge-confirmed {
    background: rgba(16, 185, 129, 0.16) !important;
    color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.32) !important;
}
.badge-cancelled {
    background: rgba(239, 68, 68, 0.16) !important;
    color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.32) !important;
}
.badge-paid {
    background: rgba(16, 185, 129, 0.16) !important;
    color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.32) !important;
}
.badge-unpaid {
    background: rgba(239, 68, 68, 0.16) !important;
    color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.32) !important;
}
.badge-partial {
    background: rgba(245, 158, 11, 0.16) !important;
    color: #FBBF24 !important;
    border: 1px solid rgba(245, 158, 11, 0.32) !important;
}

/* ================================================================
   8. ACTION BUTTONS
================================================================ */
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
.btn-action-pdf {
    background: rgba(239, 68, 68, 0.15) !important;
    border-color: rgba(239, 68, 68, 0.35) !important;
    color: #F87171 !important;
}
.btn-action-pdf:hover {
    background: rgba(239, 68, 68, 0.28) !important;
    border-color: #EF4444 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35) !important;
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
   9. MODAL POPUP
================================================================ */
.custom-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(6, 9, 18, 0.80);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 99999;
    padding: 20px;
}
.custom-modal-overlay.active {
    display: flex !important;
    animation: modalFadeIn 0.2s ease-out;
}
@keyframes modalFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.custom-modal-dialog {
    background: rgba(17, 24, 39, 0.98);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 20px;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.75), 0 0 40px rgba(59, 130, 246, 0.15);
    width: 100%;
    max-width: 860px;
    overflow: hidden;
    animation: modalScaleUp 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes modalScaleUp {
    from { transform: scale(0.94); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.modal-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    background: rgba(255, 255, 255, 0.03);
}
.modal-title-wrap {
    display: flex;
    align-items: center;
    gap: 14px;
}
.modal-icon-badge {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(59, 130, 246, 0.18);
    border: 1px solid rgba(59, 130, 246, 0.35);
    color: #60A5FA;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
.modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    color: #FFFFFF;
}
.modal-subtitle {
    margin: 4px 0 0 0;
    font-size: 12.5px;
    color: #94A3B8;
}
.modal-close-btn {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #CBD5E1;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    font-size: 22px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
}
.modal-close-btn:hover {
    background: rgba(239, 68, 68, 0.25);
    color: #EF4444;
    border-color: rgba(239, 68, 68, 0.40);
}
.modal-body-custom {
    padding: 20px 24px;
}
.modal-footer-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 24px;
    border-top: 1px solid rgba(255, 255, 255, 0.10);
    background: rgba(10, 14, 23, 0.60);
}
.modal-summary-counts {
    font-size: 13px;
    font-weight: 700;
    color: #CBD5E1;
}

/* ================================================================
   10. PAGINATION WRAPPER
================================================================ */
.pagination-wrapper {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openBookingPlotsModal(bookingId) {
    const dataEl = document.getElementById('booking-data-' + bookingId);
    if (!dataEl) return;
    const data = JSON.parse(dataEl.textContent);
    
    document.getElementById('bpm_modal_title').textContent = data.booking_type + ' #' + data.id + ' — ' + data.plots.length + ' Booked Units';
    document.getElementById('bpm_modal_subtitle').innerHTML = '<i class="fa-solid fa-user-check" style="color:#60A5FA;"></i> Customer: <strong style="color:#FFF;">' + data.customer + '</strong> &nbsp;|&nbsp; <i class="fa-solid fa-building" style="color:#FBBF24;"></i> ' + data.firm + ' &nbsp;|&nbsp; <i class="fa-solid fa-calendar" style="color:#34D399;"></i> ' + data.date + ' &nbsp;|&nbsp; Total Net: <strong style="color:#FFFFFF;">₹' + data.net_amount + '</strong>';
    
    const tbody = document.getElementById('bpm_plots_tbody');
    tbody.innerHTML = '';
    
    data.plots.forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="color: #94A3B8; font-weight: 700; font-size: 12px;">${String(p.index).padStart(2, '0')}</td>
            <td><strong style="color: #FFFFFF; font-size: 13.5px;">${p.name}</strong></td>
            <td>${p.unit_no !== '-' ? '<span style="background: rgba(59,130,246,0.18); color: #93C5FD; padding: 2px 7px; border-radius: 4px; font-weight: 800; font-size: 11.5px;">#' + p.unit_no + '</span>' : '<span style="color:#94A3B8;">—</span>'}</td>
            <td><code style="background: rgba(255,255,255,0.06); color: #93C5FD; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 11.5px;">${p.code}</code></td>
            <td><span style="color: #CBD5E1; font-size: 12px;"><i class="fa-solid fa-city" style="color: #60A5FA; margin-right: 4px;"></i>${p.project}</span></td>
            <td style="color: #E2E8F0; font-weight: 600;">${p.size}</td>
            <td style="color: #94A3B8; font-size: 12px;">${p.facing}</td>
            <td><strong style="color: #FFFFFF !important; font-size: 13.5px;">${p.price}</strong></td>
            <td style="text-align: right;">
                <a href="${p.url}" target="_blank" class="btn-action-icon btn-action-view" style="width: 28px; height: 28px; font-size: 11px;" title="View Plot Details">
                    <i class="fa-regular fa-eye"></i>
                </a>
            </td>
        `;
        tbody.appendChild(tr);
    });
    
    document.getElementById('bpm_summary_counts').innerHTML = `Total <span style="color:#60A5FA; font-weight:800;">${data.plots.length}</span> Units allocated to this booking`;
    document.getElementById('bookingPlotsModal').classList.add('active');
}

function closeBookingPlotsModal() {
    document.getElementById('bookingPlotsModal').classList.remove('active');
}

function toggleInlinePlotsList(bookingId) {
    const el = document.getElementById('inline-plots-' + bookingId);
    const icon = document.getElementById('toggle-icon-' + bookingId);
    if (!el) return;
    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
        if (icon) { icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up'); }
    } else {
        el.classList.add('hidden');
        if (icon) { icon.classList.remove('fa-chevron-up'); icon.classList.add('fa-chevron-down'); }
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeBookingPlotsModal();
    }
});

function confirmDelete(id) {
    Swal.fire({
        title: 'Delete Booking?',
        text: 'This action cannot be undone. All booked units will revert to available status.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Delete',
        background: '#111827',
        color: '#FFFFFF'
    }).then(r => { 
        if (r.isConfirmed) document.getElementById('del-bk-' + id).submit(); 
    });
}
</script>
@endsection
