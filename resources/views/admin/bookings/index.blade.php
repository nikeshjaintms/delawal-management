@extends('admin.layouts.app')
@section('title', 'Bookings')
@section('page-title', 'Booking Management')
@section('content')

<div class="crud-header">
    <div class="crud-title">
        <h2>Booking Management</h2>
        <p>Manage property bookings, sales, purchases and payment tracking.</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('bookings.pdf', request()->query()) }}" target="_blank" class="btn-gold" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%) !important; border: 1px solid #F87171 !important; color: #FFFFFF !important; box-shadow: 0 4px 16px rgba(239, 68, 68, 0.40);">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('bookings.create') }}" class="btn-gold">
            <i class="fa-solid fa-plus"></i> Add Booking
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif
@if(session('error'))
    <div class="alert-danger"><i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('bookings.index') }}" class="filter-bar">
        @if(auth()->user() && auth()->user()->isAdmin())
        <div class="filter-group">
            <span class="filter-label"><i class="fa-solid fa-building" style="color: #60A5FA;"></i> Firm</span>
            <select name="firm_id" class="filter-control" onchange="this.form.submit()">
                <option value="">All Firms</option>
                @foreach($firms as $f)
                    <option value="{{ $f->id }}" {{ request('firm_id')==$f->id?'selected':'' }}>{{ $f->firm_name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="filter-group search-group">
            <span class="filter-label"><i class="fa-solid fa-magnifying-glass" style="color: #FBBF24;"></i> Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Search property, customer, firm, status...">
        </div>
        <div class="filter-group">
            <span class="filter-label"><i class="fa-solid fa-layer-group" style="color: #A78BFA;"></i> Type</span>
            <select name="filter_booking_type" class="filter-control">
                <option value="">All Types</option>
                <option value="booking" {{ request('filter_booking_type')=='booking' ?'selected':'' }}>Booking</option>
                <option value="selling" {{ request('filter_booking_type')=='selling' ?'selected':'' }}>Selling</option>
                <option value="buying"  {{ request('filter_booking_type')=='buying'  ?'selected':'' }}>Buying</option>
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label"><i class="fa-solid fa-circle-check" style="color: #34D399;"></i> Status</span>
            <select name="filter_status" class="filter-control">
                <option value="">All Status</option>
                <option value="pending"   {{ request('filter_status')=='pending'   ?'selected':'' }}>Pending</option>
                <option value="confirmed" {{ request('filter_status')=='confirmed' ?'selected':'' }}>Confirmed</option>
                <option value="cancelled" {{ request('filter_status')=='cancelled' ?'selected':'' }}>Cancelled</option>
            </select>
        </div>
        <div class="filter-actions-group">
            <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
            @if(request()->hasAny(['search','firm_id','filter_status','filter_booking_type']))
                <a href="{{ route('bookings.index') }}" class="btn-reset" title="Reset all filters"><i class="fa-solid fa-rotate-left"></i> Reset</a>
            @endif
        </div>
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th style="width: 50px; white-space: nowrap;">#</th>
                    <th style="white-space: nowrap;">Type</th>
                    <th style="white-space: nowrap;">Firm</th>
                    <th style="white-space: nowrap;">Booking Date</th>
                    <th style="min-width: 250px; white-space: nowrap;">Property / Units</th>
                    <th style="min-width: 170px; white-space: nowrap;">Customer</th>
                    <th style="white-space: nowrap;">Net Amount</th>
                    <th style="white-space: nowrap;">Paid Amount</th>
                    <th style="white-space: nowrap;">Payment Mode</th>
                    <th style="white-space: nowrap;">Status</th>
                    <th style="white-space: nowrap;">Payment</th>
                    <th style="width: 170px; text-align: right; white-space: nowrap;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $key => $booking)
                @php
                    $allProps = $booking->all_properties;
                    $propCount = $allProps->count();
                @endphp
                <tr>
                    <td style="color: #94A3B8; font-weight: 700; white-space: nowrap;">{{ method_exists($bookings, 'firstItem') ? ($bookings->firstItem() + $key) : ($key + 1) }}</td>
                    <td style="white-space: nowrap;">
                        @php $bType = $booking->booking_type ?? 'booking'; @endphp
                        @if($bType === 'selling')
                            <span class="badge badge-type-selling"><i class="fa-solid fa-arrow-trend-up"></i> Selling</span>
                        @elseif($bType === 'buying')
                            <span class="badge badge-type-buying"><i class="fa-solid fa-cart-shopping"></i> Buying</span>
                        @else
                            <span class="badge badge-type-booking"><i class="fa-solid fa-bookmark"></i> Booking</span>
                        @endif
                    </td>
                    <td style="white-space: nowrap;"><strong style="color: #FFFFFF !important;">{{ $booking->firm->firm_name ?? '-' }}</strong></td>
                    <td style="color: #CBD5E1; white-space: nowrap;">{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') : '-' }}</td>
                    <td>
                        @if($propCount > 1)
                            <div class="booking-plots-compact">
                                <div class="booking-plots-preview">
                                    @foreach($allProps->take(2) as $p)
                                        <a href="{{ route('properties.show', $p->id) }}" class="plot-pill-chip" title="{{ $p->property_name }} {{ $p->unit_no ? '('.$p->unit_no.')' : '' }}">
                                            <i class="fa-solid fa-house" style="font-size: 10.5px; color: #60A5FA;"></i>
                                            <span>{{ $p->property_name }}</span>
                                            @if($p->unit_no)
                                                <span class="u-tag">#{{ $p->unit_no }}</span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                                
                                <div class="booking-plots-actions">
                                    <button type="button" class="btn-plots-modal-open" onclick="openBookingPlotsModal({{ $booking->id }})" title="Click to view all {{ $propCount }} units">
                                        <i class="fa-solid fa-shapes"></i>
                                        <span>+{{ $propCount - 2 }} More</span>
                                        <span class="btn-view-all-pill">View All {{ $propCount }} Units &rarr;</span>
                                    </button>
                                    <button type="button" class="btn-inline-toggle-plots" onclick="toggleInlinePlotsList({{ $booking->id }})" id="toggle-btn-{{ $booking->id }}" title="Expand/Collapse list inline">
                                        <i class="fa-solid fa-chevron-down" id="toggle-icon-{{ $booking->id }}"></i>
                                    </button>
                                </div>

                                {{-- Inline Expandable Drawer --}}
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

                                {{-- Embed JSON data for instant modal popup --}}
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
                                <strong style="color: #FFFFFF !important;">{{ $singleProp->property_name }}</strong>
                                @if($singleProp->unit_no)
                                    <span style="background: rgba(59, 130, 246, 0.20); color: #93C5FD; padding: 1px 5px; border-radius: 4px; font-size: 11px; font-weight: 700;">#{{ $singleProp->unit_no }}</span>
                                @endif
                            </a>
                        @else
                            <span style="color: #94A3B8;">-</span>
                        @endif
                    </td>
                    <td>
                        <strong style="color: #FFFFFF !important; font-size: 13.5px;">{{ $booking->customer->name ?? '-' }}</strong>
                        @if($booking->customer?->phone || $booking->customer?->mobile)
                            <div style="font-size: 11.5px; color: #94A3B8; margin-top: 2px; white-space: nowrap;">
                                <i class="fa-solid fa-phone" style="font-size: 9.5px; color: #60A5FA;"></i> {{ $booking->customer->phone ?: $booking->customer->mobile }}
                            </div>
                        @endif
                    </td>
                    <td style="white-space: nowrap;">
                        @if($booking->final_amount)
                            <strong style="color: #FFFFFF !important; font-size: 15.5px; font-weight: 800; letter-spacing: -0.2px;">₹{{ number_format($booking->final_amount, 2) }}</strong>
                            @if($booking->discount_amount > 0)
                                <div style="font-size: 11px; color: #FBBF24; font-weight: 600;">-₹{{ number_format($booking->discount_amount, 2) }} Off</div>
                            @endif
                        @elseif($booking->total_amount)
                            <strong style="color: #FFFFFF !important; font-size: 15.5px; font-weight: 800;">₹{{ number_format($booking->total_amount, 2) }}</strong>
                        @else
                            <span style="color: #94A3B8;">-</span>
                        @endif
                    </td>
                    <td style="white-space: nowrap;">
                        @if($booking->booking_amount)
                            <span style="color: #FFFFFF !important; font-size: 14.5px; font-weight: 800; background: rgba(245, 158, 11, 0.16); color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35); padding: 4px 10px; border-radius: 8px; display: inline-block;">
                                ₹{{ number_format($booking->booking_amount, 2) }}
                            </span>
                        @else
                            <span style="color: #94A3B8;">-</span>
                        @endif
                    </td>
                    <td style="white-space: nowrap;">
                        @if($booking->paymentMode || $booking->payment_mode)
                            <span style="font-size: 12px; color: #CBD5E1; background: rgba(255, 255, 255, 0.08); padding: 3px 8px; border-radius: 6px; border: 1px solid rgba(255, 255, 255, 0.12); font-weight: 600;">
                                {{ $booking->paymentMode->name ?? $booking->payment_mode }}
                            </span>
                        @else
                            <span style="color: #94A3B8;">-</span>
                        @endif
                    </td>
                    <td style="white-space: nowrap;"><span class="badge badge-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                    <td style="white-space: nowrap;"><span class="badge badge-{{ $booking->payment_status }}">{{ ucfirst($booking->payment_status) }}</span></td>
                    <td style="text-align: right; white-space: nowrap;">
                        <div class="action-buttons-wrap" style="justify-content: flex-end; display: flex; align-items: center; gap: 6px;">
                            <a href="{{ route('bookings.receipt-pdf', $booking->id) }}" target="_blank" class="btn-action-icon btn-action-pdf" title="Print / PDF Slip">
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
                @empty
                <tr><td colspan="12" align="center" style="padding: 36px; color: #CBD5E1;"><i class="fa-solid fa-inbox" style="font-size: 28px; color: #60A5FA; display: block; margin-bottom: 8px;"></i> No bookings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($bookings, 'links'))
        <div class="pagination-wrapper">{{ $bookings->appends(request()->query())->links() }}</div>
    @endif
</div>

{{-- ── Booking Plots Detail Modal ── --}}
<div id="bookingPlotsModal" class="custom-modal-overlay">
    <div class="custom-modal-dialog" style="max-width: 860px;">
        <div class="modal-header-custom">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(59, 130, 246, 0.20); color: #60A5FA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-shapes"></i>
                </div>
                <div>
                    <h3 id="bpm_modal_title" style="margin: 0; font-size: 18px; font-weight: 800; color: #FFFFFF;">Booked Plots &amp; Units</h3>
                    <p id="bpm_modal_subtitle" style="margin: 3px 0 0 0; font-size: 12.5px; color: #94A3B8;"></p>
                </div>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeBookingPlotsModal()">&times;</button>
        </div>
        
        <div style="padding: 16px 24px;">
            <div class="table-container" style="max-height: 420px; overflow-y: auto;">
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
                        {{-- Populated dynamically --}}
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); background: rgba(10, 14, 23, 0.60);">
            <div id="bpm_summary_counts" style="font-size: 13px; font-weight: 700; color: #CBD5E1;"></div>
            <button type="button" class="btn-gold" onclick="closeBookingPlotsModal()" style="padding: 8px 20px; min-height: 36px; font-size: 13px;">Close</button>
        </div>
    </div>
</div>

<style>
/* ── Filter Bar ── */
.filter-bar {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 14px !important;
    align-items: flex-end !important;
    background: rgba(16, 22, 34, 0.65) !important;
    padding: 18px 22px !important;
    border-radius: 16px !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    margin-bottom: 24px !important;
    backdrop-filter: blur(16px) !important;
    -webkit-backdrop-filter: blur(16px) !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25) !important;
}
.filter-group {
    display: flex !important;
    flex-direction: column !important;
    gap: 6px !important;
    min-width: 150px !important;
    flex-shrink: 0 !important;
}
.filter-group.search-group {
    flex: 1 1 240px !important;
    min-width: 220px !important;
}
.filter-label {
    font-size: 11.5px !important;
    font-weight: 800 !important;
    color: #94A3B8 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.6px !important;
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
}
.filter-control, .search-input {
    height: 42px !important;
    padding: 0 14px !important;
    background: rgba(10, 15, 26, 0.85) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important;
    color: #FFFFFF !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    outline: none !important;
    transition: all 0.2s ease !important;
    width: 100% !important;
    box-sizing: border-box !important;
}
.filter-control option {
    background: #111827 !important;
    color: #FFFFFF !important;
    padding: 8px !important;
}
.filter-control:focus, .search-input:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
    background: rgba(16, 23, 38, 0.95) !important;
}
.search-input::placeholder {
    color: #64748B !important;
}
.filter-actions-group {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-shrink: 0 !important;
}
.btn-search {
    height: 42px !important;
    padding: 0 22px !important;
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
    border: 1px solid #3B82F6 !important;
    border-radius: 10px !important;
    color: #FFFFFF !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35) !important;
    transition: all 0.2s ease !important;
    white-space: nowrap !important;
}
.btn-search:hover {
    background: linear-gradient(135deg, #1D4ED8 0%, #1E40AF 100%) !important;
    border-color: #60A5FA !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50) !important;
}
.btn-reset {
    height: 42px !important;
    padding: 0 16px !important;
    background: rgba(255, 255, 255, 0.06) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 10px !important;
    color: #CBD5E1 !important;
    text-decoration: none !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    transition: all 0.2s ease !important;
    white-space: nowrap !important;
}
.btn-reset:hover {
    background: rgba(255, 255, 255, 0.12) !important;
    color: #FFFFFF !important;
    border-color: rgba(255, 255, 255, 0.22) !important;
}

/* ── Table Container ── */
.table-container {
    width: 100% !important;
    overflow-x: auto !important;
    background: rgba(14, 20, 32, 0.70) !important;
    border-radius: 16px !important;
    border: 1px solid rgba(255, 255, 255, 0.10) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
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
}
.premium-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.04) !important;
}

/* ── Multi-Plot / Units Compact Widget ── */
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
    background: rgba(255, 255, 255, 0.12);
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

/* ── Modern Table Action Buttons ── */
.btn-action-icon {
    width: 32px !important;
    height: 32px !important;
    border-radius: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 12.5px !important;
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

/* ── Modal Overlay ── */
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
    background: rgba(17, 24, 39, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 18px;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.70), 0 0 40px rgba(59, 130, 246, 0.15);
    width: 100%;
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
    padding: 16px 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    background: rgba(255, 255, 255, 0.03);
}
.modal-close-btn {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #CBD5E1;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 20px;
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

/* ── Badges ── */
.badge-type-booking {
    background: rgba(59, 130, 246, 0.16) !important;
    color: #60A5FA !important;
    border: 1px solid rgba(59, 130, 246, 0.32) !important;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 11.5px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.badge-type-selling {
    background: rgba(16, 185, 129, 0.16) !important;
    color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.32) !important;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 11.5px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.badge-type-buying {
    background: rgba(139, 92, 246, 0.16) !important;
    color: #A78BFA !important;
    border: 1px solid rgba(139, 92, 246, 0.32) !important;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 11.5px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.badge-pending {
    background: rgba(245, 158, 11, 0.16) !important;
    color: #FBBF24 !important;
    border: 1px solid rgba(245, 158, 11, 0.32) !important;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 11.5px;
}
.badge-confirmed {
    background: rgba(16, 185, 129, 0.16) !important;
    color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.32) !important;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 11.5px;
}
.badge-cancelled {
    background: rgba(239, 68, 68, 0.16) !important;
    color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.32) !important;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 11.5px;
}
.badge-paid {
    background: rgba(16, 185, 129, 0.16) !important;
    color: #34D399 !important;
    border: 1px solid rgba(16, 185, 129, 0.32) !important;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 11.5px;
}
.badge-unpaid {
    background: rgba(239, 68, 68, 0.16) !important;
    color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.32) !important;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 11.5px;
}
.badge-partial {
    background: rgba(245, 158, 11, 0.16) !important;
    color: #FBBF24 !important;
    border: 1px solid rgba(245, 158, 11, 0.32) !important;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 11.5px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openBookingPlotsModal(bookingId) {
    const dataEl = document.getElementById('booking-data-' + bookingId);
    if (!dataEl) return;
    const data = JSON.parse(dataEl.textContent);
    
    document.getElementById('bpm_modal_title').textContent = data.booking_type + ' #' + data.id + ' — ' + data.plots.length + ' Booked Units';
    document.getElementById('bpm_modal_subtitle').innerHTML = '<i class="fa-solid fa-user-check" style="color:#60A5FA;"></i> Customer: <strong>' + data.customer + '</strong> &nbsp;|&nbsp; <i class="fa-solid fa-building" style="color:#FBBF24;"></i> ' + data.firm + ' &nbsp;|&nbsp; <i class="fa-solid fa-calendar" style="color:#34D399;"></i> ' + data.date + ' &nbsp;|&nbsp; Total Net: <strong style="color:#FFFFFF;">₹' + data.net_amount + '</strong>';
    
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
                <a href="${p.url}" target="_blank" class="btn-view" style="font-size: 11.5px; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
                    <i class="fa-regular fa-eye"></i> View Plot
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
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Delete'
    }).then(r => { 
        if (r.isConfirmed) document.getElementById('del-bk-' + id).submit(); 
    });
}
</script>
@endsection

