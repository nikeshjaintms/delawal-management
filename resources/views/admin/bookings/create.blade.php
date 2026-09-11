@extends('admin.layouts.app')
@section('title', 'Add Booking')
@section('page-title', 'Booking Management')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
    max-width: 960px; margin-left: auto; margin-right: auto;
}

.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
@media(max-width:768px){ .form-row-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .form-row, .form-row-3 { grid-template-columns: 1fr; gap: 0; } }

.form-label { display: block; font-size: 13px; font-weight: 700; color: #CBD5E1 !important; margin-bottom: 8px; }
.form-label span { color: #F87171 !important; }

.form-control {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 14px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important;
}
select.form-control option { background: #101622 !important; color: #FFFFFF !important; }
.form-control::placeholder { color: #94A3B8 !important; }
.form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }
.form-control[readonly] { background: rgba(16, 22, 34, 0.40) !important; color: #94A3B8 !important; border-color: rgba(255, 255, 255, 0.08) !important; cursor: not-allowed; }
textarea.form-control { resize: vertical; min-height: 85px; }

.text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 600; }

/* Calculator Panel Dark Glass */
.calculator-panel {
    background: rgba(16, 22, 34, 0.55) !important;
    border: 1.5px solid rgba(245, 158, 11, 0.35) !important;
    border-radius: 16px !important;
    padding: 24px !important;
    margin: 24px 0 !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
}
.calc-title {
    font-size: 15px;
    font-weight: 800;
    color: #FBBF24 !important;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}
.summary-badge-bar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 14px;
    margin-top: 20px;
    padding-top: 18px;
    border-top: 1px dashed rgba(255, 255, 255, 0.15) !important;
}
.summary-item {
    background: rgba(16, 22, 34, 0.75) !important;
    backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 12px !important;
    padding: 14px 12px !important;
    text-align: center;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25) !important;
}
.summary-label {
    font-size: 11px;
    font-weight: 800;
    color: #94A3B8 !important;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 6px;
}
.summary-val {
    font-size: 17px;
    font-weight: 800;
    letter-spacing: -0.2px;
}
.summary-val.blue { color: #60A5FA !important; }
.summary-val.gold { color: #FBBF24 !important; }
.summary-val.green { color: #34D399 !important; }
.summary-val.red { color: #F87171 !important; }

.form-actions { display: flex; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }
.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 10px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    display: inline-flex; align-items: center; gap: 8px; text-decoration: none !important;
}
.btn-gold:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }
.btn-outline {
    border: 1px solid rgba(255, 255, 255, 0.15) !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #CBD5E1 !important; padding: 11px 24px; border-radius: 10px; text-decoration: none !important;
    font-size: 14px; font-weight: 600; transition: all .2s ease;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.10) !important; color: #FFFFFF !important; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add New Booking</h2>
        <p>Register a property booking with discount calculation, customer and payment details.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('bookings.store') }}" id="bookingForm">
        @csrf
        @include('admin.components.firm-select')

        {{-- 1. Property / Land Master Selection --}}
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="property_master_id">Property / Land Master <span>*</span></label>
                <select name="property_master_id" id="property_master_id" class="form-control @error('property_master_id') is-invalid @enderror" required>
                    <option value="">-- Select Property / Land --</option>
                    @foreach($propertyMasters as $pm)
                        <option value="{{ $pm->id }}"
                                data-price="{{ $pm->purchase_price ?: ($pm->plots->sum('price') ?: 0) }}"
                                data-area="{{ $pm->total_area ? ($pm->total_area . ' ' . ($pm->area_unit ?? 'Sq.Ft')) : '' }}"
                                data-plots-count="{{ $pm->plots->count() }}"
                                data-code="{{ $pm->property_code }}"
                                data-name="{{ $pm->property_name }}"
                                data-firm-id="{{ $pm->firm_id }}"
                                data-projects='@json($pm->all_projects->map(fn($p) => ["id" => $p->id, "name" => $p->project_name]))'
                                {{ old('property_master_id', request('property_master_id')) == $pm->id ? 'selected' : '' }}>
                            {{ $pm->property_name }}
                            @if($pm->property_code) [{{ $pm->property_code }}] @endif
                            @if($pm->total_area) — Area: {{ $pm->total_area }} {{ $pm->area_unit ?? 'Sq.Ft' }} @endif
                            ({{ $pm->plots->count() }} Plots)
                        </option>
                    @endforeach
                </select>
                <div style="font-size:12px;color:#94A3B8;margin-top:4px;">Select the main Property / Land acquisition first.</div>
                @error('property_master_id') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="project_id">Project / Scheme (Optional)</label>
                <select name="project_id" id="project_id" class="form-control">
                    <option value="">— All / Direct —</option>
                    @foreach($projects as $proj)
                        <option value="{{ $proj->id }}" {{ old('project_id', request('project_id')) == $proj->id ? 'selected' : '' }}>
                            {{ $proj->project_name }}
                        </option>
                    @endforeach
                </select>
                <div id="project_hint_msg" style="font-size:12px;color:#94A3B8;margin-top:4px;">Select Property first. If property belongs to a project, it will link automatically.</div>
            </div>
        </div>

        {{-- 2. Booking Scope / Type Switcher --}}
        <div class="form-group" style="margin-bottom: 22px;">
            <label class="form-label">What would you like to book? <span>*</span></label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <label id="scope_entire_card" style="display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-radius: 12px; border: 1.5px solid rgba(59, 130, 246, 0.5); background: rgba(37, 99, 235, 0.12); cursor: pointer; transition: all .2s ease;">
                    <input type="radio" name="booking_scope" id="booking_scope_entire" value="entire" {{ old('booking_scope', 'entire') == 'entire' ? 'checked' : '' }} onchange="onBookingScopeChange()" style="accent-color: #3B82F6; width: 18px; height: 18px;">
                    <div>
                        <div style="font-weight: 800; font-size: 14px; color: #60A5FA;">🏢 Entire Property / Whole Land</div>
                        <div style="font-size: 12px; color: #CBD5E1; margin-top: 2px;">Book the complete property / all plots together in one deal.</div>
                    </div>
                </label>
                <label id="scope_plot_card" style="display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-radius: 12px; border: 1.5px solid rgba(255, 255, 255, 0.15); background: rgba(16, 22, 34, 0.6); cursor: pointer; transition: all .2s ease;">
                    <input type="radio" name="booking_scope" id="booking_scope_plot" value="plot" {{ old('booking_scope') == 'plot' ? 'checked' : '' }} onchange="onBookingScopeChange()" style="accent-color: #3B82F6; width: 18px; height: 18px;">
                    <div>
                        <div style="font-weight: 800; font-size: 14px; color: #FBBF24;">🏷️ Specific Plot / Unit</div>
                        <div style="font-size: 12px; color: #CBD5E1; margin-top: 2px;">Book an individual plot or sub-unit under this property.</div>
                    </div>
                </label>
            </div>
        </div>

        {{-- 3A. Entire Property Summary Banner --}}
        <div id="entire_prop_banner" style="display: none; background: rgba(37, 99, 235, 0.10); border: 1.5px dashed rgba(96, 165, 250, 0.4); border-radius: 14px; padding: 16px 20px; margin-bottom: 22px;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div>
                    <div style="font-weight: 800; color: #93C5FD; font-size: 14.5px;" id="entire_prop_title">
                        🏢 Booking Entire Property Master
                    </div>
                    <div style="font-size: 12.5px; color: #CBD5E1; margin-top: 3px;" id="entire_prop_desc">
                        Please select a Property / Land Master above.
                    </div>
                </div>
                <div id="entire_prop_badge" style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #34D399; font-size: 12.5px; font-weight: 700; padding: 6px 14px; border-radius: 8px;">
                    Whole Land Booking
                </div>
            </div>
        </div>

        {{-- 3B. Specific Plot / Unit Visual Grid Selector (Multi-Select Supported) --}}
        <div id="plot_select_container" class="form-group" style="display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <label class="form-label" style="margin-bottom: 2px;">
                        Select Specific Plot(s) / Unit(s) <span>*</span>
                    </label>
                    <div style="font-size: 12px; color: #94A3B8;">Click any unit card below to select or deselect multiple units easily.</div>
                </div>
                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <input type="text" id="plot_search_input" oninput="filterPlotCardsBySearch()" placeholder="🔍 Search unit / plot / code..." style="background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.15); color: #FFFFFF; font-size: 12px; border-radius: 8px; padding: 5px 12px; outline: none; width: 200px;">
                    <button type="button" onclick="selectAllVisiblePlots()" style="background: rgba(59, 130, 246, 0.18); border: 1px solid rgba(59, 130, 246, 0.4); color: #60A5FA; font-size: 11.5px; font-weight: 700; padding: 5px 10px; border-radius: 6px; cursor: pointer;">
                        <i class="fa-solid fa-check-double"></i> Select All
                    </button>
                    <button type="button" onclick="selectAvailablePlotsOnly()" style="background: rgba(16, 185, 129, 0.18); border: 1px solid rgba(16, 185, 129, 0.4); color: #34D399; font-size: 11.5px; font-weight: 700; padding: 5px 10px; border-radius: 6px; cursor: pointer;">
                        <i class="fa-solid fa-bolt"></i> Available Only
                    </button>
                    <button type="button" onclick="clearSelectedPlots()" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.35); color: #F87171; font-size: 11.5px; font-weight: 700; padding: 5px 10px; border-radius: 6px; cursor: pointer;">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </button>
                </div>
            </div>

            {{-- Hidden select kept in sync for form submit --}}
            <select name="property_ids[]" id="property_id" multiple style="display: none;">
                @foreach($properties as $p)
                    <option value="{{ $p->id }}"
                            data-master-id="{{ $p->property_master_id ?? '' }}"
                            data-project-id="{{ $p->project_id ?? '' }}"
                            data-unit-no="{{ $p->unit_no ?? '' }}"
                            data-price="{{ $p->price ?? '' }}"
                            {{ (is_array(old('property_ids')) && in_array($p->id, old('property_ids'))) || old('property_id', request('property_id'))==$p->id?'selected':'' }}>
                        {{ $p->property_name }}
                    </option>
                @endforeach
            </select>

            {{-- Visual Units Card Grid --}}
            <div id="plot_cards_scrollbox" style="max-height: 320px; overflow-y: auto; padding: 12px; border-radius: 14px; background: rgba(10, 15, 26, 0.75); border: 1.5px solid rgba(255, 255, 255, 0.12); box-shadow: inset 0 2px 8px rgba(0,0,0,0.4);">
                <div id="plot_cards_grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 10px;">
                    @foreach($properties as $p)
                        @php
                            $isSelected = (is_array(old('property_ids')) && in_array($p->id, old('property_ids'))) || old('property_id', request('property_id')) == $p->id;
                            $statusColor = $p->status === 'available' ? '#34D399' : ($p->status === 'booked' ? '#FBBF24' : '#F87171');
                            $statusBg = $p->status === 'available' ? 'rgba(16, 185, 129, 0.15)' : ($p->status === 'booked' ? 'rgba(245, 158, 11, 0.15)' : 'rgba(239, 68, 68, 0.15)');
                        @endphp
                        <div class="plot-card-item {{ $isSelected ? 'is-selected' : '' }}"
                             id="plot_card_{{ $p->id }}"
                             data-id="{{ $p->id }}"
                             data-master-id="{{ $p->property_master_id ?? '' }}"
                             data-project-id="{{ $p->project_id ?? '' }}"
                             data-unit-no="{{ strtolower($p->unit_no ?? '') }}"
                             data-name="{{ strtolower($p->property_name ?? '') }}"
                             data-code="{{ strtolower($p->property_code ?? '') }}"
                             data-price="{{ $p->price ?? 0 }}"
                             data-status="{{ $p->status }}"
                             onclick="togglePlotCardSelection({{ $p->id }})"
                             style="cursor: pointer; user-select: none; padding: 10px 14px; border-radius: 12px; background: {{ $isSelected ? 'rgba(37, 99, 235, 0.22)' : 'rgba(20, 27, 41, 0.65)' }}; border: 1.5px solid {{ $isSelected ? '#3B82F6' : 'rgba(255, 255, 255, 0.10)' }}; transition: all .2s ease; display: flex; align-items: center; gap: 12px; box-shadow: {{ $isSelected ? '0 0 14px rgba(59, 130, 246, 0.35)' : 'none' }};">
                            <div class="plot-checkbox-circle" style="width: 22px; height: 22px; border-radius: 6px; border: 1.5px solid {{ $isSelected ? '#3B82F6' : 'rgba(255, 255, 255, 0.25)' }}; background: {{ $isSelected ? '#2563EB' : 'transparent' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #FFFFFF; font-size: 11px; transition: all .2s ease;">
                                <i class="fa-solid fa-check" style="display: {{ $isSelected ? 'block' : 'none' }};"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; gap: 6px; justify-content: space-between;">
                                    <div style="font-weight: 700; color: #FFFFFF; font-size: 13.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $p->property_name }}
                                    </div>
                                    @if($p->unit_no || $p->property_code)
                                        <span style="font-size: 10.5px; font-weight: 700; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); padding: 1px 6px; border-radius: 4px; color: #93C5FD; flex-shrink: 0;">
                                            {{ $p->property_code ?: 'Unit '.$p->unit_no }}
                                        </span>
                                    @endif
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 4px;">
                                    <div style="font-size: 12px; font-weight: 700; color: #34D399;">
                                        ₹ {{ number_format($p->price ?? 0, 2) }}
                                    </div>
                                    <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 2px 6px; border-radius: 4px; background: {{ $statusBg }}; color: {{ $statusColor }};">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="no_plots_msg" style="display: none; text-align: center; padding: 30px 15px; color: #94A3B8; font-size: 13.5px;">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 24px; margin-bottom: 8px; color: #64748B; display: block;"></i>
                    No plots/units match the selected Property Master or search filter.
                </div>
            </div>

            {{-- Live Selection Summary --}}
            <div id="plots_summary_badge" style="display: none; margin-top: 10px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.35); border-radius: 10px; padding: 10px 16px; font-size: 13px; color: #93C5FD; font-weight: 700; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-layer-group" style="color: #60A5FA; font-size: 16px;"></i>
                    <span id="plots_summary_text"></span>
                </div>
                <div id="plots_summary_pills" style="display: flex; gap: 4px; flex-wrap: wrap; max-width: 450px;"></div>
            </div>
            @error('property_id')<div class="text-error">{{ $message }}</div>@enderror
            @error('property_ids')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Customer <span>*</span></label>
                <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                    <option value="">Select Customer</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}>
                            {{ $c->name }} ({{ $c->mobile }}{{ $c->alternate_mobile ? ' / ' . $c->alternate_mobile : '' }})
                        </option>
                    @endforeach
                </select>
                @error('customer_id')<div class="text-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Booking Date <span>*</span></label>
                <input type="date" name="booking_date" value="{{ old('booking_date', date('Y-m-d')) }}" class="form-control @error('booking_date') is-invalid @enderror" required>
                @error('booking_date')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Broker</label>
                <select name="broker_id" id="broker_id" class="form-control @error('broker_id') is-invalid @enderror">
                    <option value="">No Broker</option>
                    @foreach($brokers as $b)
                        <option value="{{ $b->id }}" {{ old('broker_id')==$b->id?'selected':'' }} data-commission="{{ $b->commission_percentage }}">{{ $b->name }} ({{ $b->commission_percentage ? $b->commission_percentage.'%' : '0%' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Agreement Date</label>
                <input type="date" name="agreement_date" value="{{ old('agreement_date') }}" class="form-control @error('agreement_date') is-invalid @enderror">
            </div>
        </div>

        <!-- PRICING, DISCOUNT & PAYMENT CALCULATOR -->
        <div class="calculator-panel">
            <div class="calc-title">
                <i class="fa-solid fa-calculator"></i> Pricing & Payment Calculator
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="total_amount">Total Property Price (₹)</label>
                    <input type="number" step="0.01" min="0" name="total_amount" id="total_amount" value="{{ old('total_amount') }}" class="form-control @error('total_amount') is-invalid @enderror" placeholder="0.00">
                    @error('total_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Discount Type</label>
                    <select name="discount_type" id="discount_type" class="form-control">
                        <option value="percentage" {{ old('discount_type', 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="discount_value">Discount Value</label>
                    <input type="number" step="0.01" min="0" name="discount_value" id="discount_value" value="{{ old('discount_value', '0') }}" class="form-control @error('discount_value') is-invalid @enderror" placeholder="E.g. 5 or 50000">
                    @error('discount_value')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="discount_amount">Discount Amount (₹)</label>
                    <input type="number" step="0.01" min="0" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', '0.00') }}" class="form-control" readonly placeholder="0.00">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="final_amount">Final / Net Payable Amount (₹)</label>
                    <input type="number" step="0.01" min="0" name="final_amount" id="final_amount" value="{{ old('final_amount') }}" class="form-control @error('final_amount') is-invalid @enderror" readonly placeholder="0.00" style="font-weight:700;color:#60A5FA !important;">
                    @error('final_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="booking_amount">New Payment / Booking Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" min="0" name="booking_amount" id="booking_amount" value="{{ old('booking_amount') }}" class="form-control @error('booking_amount') is-invalid @enderror" placeholder="0.00" required>
                    @error('booking_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="remaining_amount">Remaining / Balance Amount (₹)</label>
                    <input type="number" step="0.01" min="0" name="remaining_amount" id="remaining_amount" value="{{ old('remaining_amount') }}" class="form-control" readonly placeholder="0.00" style="font-weight:700;color:#F87171 !important;">
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_mode_id">Payment Mode <span>*</span></label>
                    <select name="payment_mode_id" id="payment_mode_id" class="form-control @error('payment_mode_id') is-invalid @enderror">
                        <option value="">— Select Payment Mode —</option>
                        @foreach($paymentModes as $pm)
                            <option value="{{ $pm->id }}" {{ old('payment_mode_id') == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                    @error('payment_mode_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="transaction_ref">Payment / Transaction Reference</label>
                    <input type="text" name="transaction_ref" id="transaction_ref" value="{{ old('transaction_ref') }}" class="form-control @error('transaction_ref') is-invalid @enderror" placeholder="Cheque No., UPI Ref, UTR, etc.">
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Status <span>*</span></label>
                    <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror" required>
                        <option value="unpaid"  {{ old('payment_status','unpaid')=='unpaid'  ?'selected':'' }}>Unpaid</option>
                        <option value="partial" {{ old('payment_status')=='partial' ?'selected':'' }}>Partial</option>
                        <option value="paid"    {{ old('payment_status')=='paid'    ?'selected':'' }}>Paid</option>
                    </select>
                    @error('payment_status')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <!-- Visual Live Summary Bar (Dark Glass) -->
            <div class="summary-badge-bar">
                <div class="summary-item">
                    <div class="summary-label">Total Price</div>
                    <div class="summary-val blue" id="sum_total">₹0.00</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Discount (-)</div>
                    <div class="summary-val gold" id="sum_discount">₹0.00</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Net Payable</div>
                    <div class="summary-val blue" id="sum_final">₹0.00</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Paid / Booking</div>
                    <div class="summary-val green" id="sum_paid">₹0.00</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Balance Due</div>
                    <div class="summary-val red" id="sum_balance">₹0.00</div>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Booking Status <span>*</span></label>
                <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="pending"   {{ old('status','pending')=='pending'   ?'selected':'' }}>Pending</option>
                    <option value="confirmed" {{ old('status')=='confirmed' ?'selected':'' }}>Confirmed</option>
                    <option value="cancelled" {{ old('status')=='cancelled' ?'selected':'' }}>Cancelled</option>
                </select>
                @error('status')<div class="text-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="2" placeholder="Additional notes...">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <!-- Broker Commission Details (shows only when a broker is selected) -->
        <div id="commission_section" style="display: none; margin-top: 24px; padding-top: 20px; border-top: 1px dashed rgba(255, 255, 255, 0.15);">
            <h4 style="font-size: 15px; font-weight: 700; color: #FBBF24; margin-bottom: 15px;">
                <i class="fa-solid fa-percent"></i> Broker Commission Details
            </h4>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Commission Type</label>
                    <select name="commission_type" id="commission_type" class="form-control">
                        <option value="percentage" {{ old('commission_type', 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('commission_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Commission Value</label>
                    <input type="number" step="0.01" min="0" name="commission_value" id="commission_value" value="{{ old('commission_value') }}" class="form-control" placeholder="E.g. 2.0 or 5000">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Calculated Commission Amount (₹)</label>
                    <input type="number" step="0.01" min="0" name="commission_amount" id="commission_amount" value="{{ old('commission_amount') }}" class="form-control" placeholder="Auto-calculated">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Save Booking</button>
            <a href="{{ route('bookings.index') }}" class="btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalAmountInput = document.getElementById('total_amount');
        const discountTypeSelect = document.getElementById('discount_type');
        const discountValueInput = document.getElementById('discount_value');
        const discountAmountInput = document.getElementById('discount_amount');
        const finalAmountInput = document.getElementById('final_amount');
        const bookingAmountInput = document.getElementById('booking_amount');
        const remainingAmountInput = document.getElementById('remaining_amount');
        const paymentStatusSelect = document.getElementById('payment_status');

        const sumTotal = document.getElementById('sum_total');
        const sumDiscount = document.getElementById('sum_discount');
        const sumFinal = document.getElementById('sum_final');
        const sumPaid = document.getElementById('sum_paid');
        const sumBalance = document.getElementById('sum_balance');

        function formatINR(val) {
            return '₹' + parseFloat(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function calculateAmounts(userChangedPaymentStatus = false) {
            const total = parseFloat(totalAmountInput.value) || 0;
            const discType = discountTypeSelect.value;
            const discVal = parseFloat(discountValueInput.value) || 0;

            let discountAmt = 0;
            if (discType === 'percentage') {
                discountAmt = (total * discVal) / 100;
            } else {
                discountAmt = discVal;
            }
            if (discountAmt > total) {
                discountAmt = total;
            }

            const finalAmt = Math.max(0, total - discountAmt);
            const bookingAmt = parseFloat(bookingAmountInput.value) || 0;
            const remainingAmt = Math.max(0, finalAmt - bookingAmt);

            discountAmountInput.value = discountAmt > 0 ? discountAmt.toFixed(2) : '0.00';
            finalAmountInput.value = (total > 0 || discountAmt > 0) ? finalAmt.toFixed(2) : (bookingAmt > 0 ? bookingAmt.toFixed(2) : '');
            remainingAmountInput.value = remainingAmt.toFixed(2);

            // Update Summary Bar
            sumTotal.textContent = formatINR(total);
            sumDiscount.textContent = formatINR(discountAmt);
            sumFinal.textContent = formatINR(finalAmt > 0 ? finalAmt : bookingAmt);
            sumPaid.textContent = formatINR(bookingAmt);
            sumBalance.textContent = formatINR(remainingAmt);

            // Auto suggest payment status
            if (!userChangedPaymentStatus) {
                const targetFinal = finalAmt > 0 ? finalAmt : bookingAmt;
                if (targetFinal > 0 && bookingAmt >= targetFinal) {
                    paymentStatusSelect.value = 'paid';
                } else if (bookingAmt > 0) {
                    paymentStatusSelect.value = 'partial';
                } else {
                    paymentStatusSelect.value = 'unpaid';
                }
            }

            calculateCommission();
        }

        totalAmountInput.addEventListener('input', () => calculateAmounts());
        discountTypeSelect.addEventListener('change', () => calculateAmounts());
        discountValueInput.addEventListener('input', () => calculateAmounts());
        bookingAmountInput.addEventListener('input', () => calculateAmounts());

        // Broker commission logic
        const brokerSelect = document.getElementById('broker_id');
        const commissionSection = document.getElementById('commission_section');
        const commissionType = document.getElementById('commission_type');
        const commissionValue = document.getElementById('commission_value');
        const commissionAmount = document.getElementById('commission_amount');

        function toggleCommissionSection() {
            if (brokerSelect && brokerSelect.value) {
                commissionSection.style.display = 'block';
                const option = brokerSelect.options[brokerSelect.selectedIndex];
                const defaultComm = option.getAttribute('data-commission');
                if (!commissionValue.value && defaultComm) {
                    commissionValue.value = parseFloat(defaultComm).toFixed(2);
                }
            } else if (commissionSection) {
                commissionSection.style.display = 'none';
                commissionValue.value = '';
                commissionAmount.value = '';
            }
            calculateCommission();
        }

        function calculateCommission() {
            if (!commissionType || !commissionValue || !commissionAmount) return;
            const type = commissionType.value;
            const val = parseFloat(commissionValue.value) || 0;
            const baseAmt = parseFloat(bookingAmountInput.value) || parseFloat(finalAmountInput.value) || 0;

            let calculated = 0;
            if (type === 'percentage') {
                calculated = (baseAmt * val) / 100;
            } else {
                calculated = val;
            }

            commissionAmount.value = calculated > 0 ? calculated.toFixed(2) : '';
        }

        if (brokerSelect) {
            brokerSelect.addEventListener('change', toggleCommissionSection);
            commissionType.addEventListener('change', calculateCommission);
            commissionValue.addEventListener('input', calculateCommission);
            toggleCommissionSection();
        }

        // Booking scope switcher logic
        window.onBookingScopeChange = function() {
            const isEntire = document.getElementById('booking_scope_entire').checked;
            const entireCard = document.getElementById('scope_entire_card');
            const plotCard = document.getElementById('scope_plot_card');
            const entireBanner = document.getElementById('entire_prop_banner');
            const plotContainer = document.getElementById('plot_select_container');
            const propSelect = document.getElementById('property_id');
            const masterSelect = document.getElementById('property_master_id');

            if (isEntire) {
                entireCard.style.borderColor = 'rgba(59, 130, 246, 0.5)';
                entireCard.style.background = 'rgba(37, 99, 235, 0.15)';
                plotCard.style.borderColor = 'rgba(255, 255, 255, 0.15)';
                plotCard.style.background = 'rgba(16, 22, 34, 0.6)';

                entireBanner.style.display = 'block';
                plotContainer.style.display = 'none';
                propSelect.removeAttribute('required');

                updateEntirePropInfo();
            } else {
                plotCard.style.borderColor = 'rgba(245, 158, 11, 0.5)';
                plotCard.style.background = 'rgba(245, 158, 11, 0.15)';
                entireCard.style.borderColor = 'rgba(255, 255, 255, 0.15)';
                entireCard.style.background = 'rgba(16, 22, 34, 0.6)';

                entireBanner.style.display = 'none';
                plotContainer.style.display = 'block';
                propSelect.setAttribute('required', 'required');

                filterPlotsByMaster();
            }
        };

        function updateEntirePropInfo() {
            const masterSelect = document.getElementById('property_master_id');
            const selectedOpt = masterSelect.selectedOptions[0];
            const titleEl = document.getElementById('entire_prop_title');
            const descEl = document.getElementById('entire_prop_desc');

            if (selectedOpt && selectedOpt.value) {
                const name = selectedOpt.dataset.name || selectedOpt.text;
                const code = selectedOpt.dataset.code || '';
                const area = selectedOpt.dataset.area || '';
                const plotsCount = selectedOpt.dataset.plotsCount || '0';
                const price = parseFloat(selectedOpt.dataset.price) || 0;

                titleEl.innerHTML = `🏢 Booking Entire Property: <strong>${name}</strong> ${code ? `(${code})` : ''}`;
                descEl.innerHTML = `Total Area: <strong>${area || 'N/A'}</strong> | Includes <strong>${plotsCount} Plots</strong>. All sub-plots will be marked as Booked upon confirmation.`;

                if (price > 0 && (!totalAmountInput.value || totalAmountInput.value === '0')) {
                    totalAmountInput.value = price.toFixed(2);
                    calculateAmounts();
                }
            } else {
                titleEl.innerHTML = `🏢 Booking Entire Property Master`;
                descEl.innerHTML = `Please select a Property / Land Master above.`;
            }
        }

        window.togglePlotCardSelection = function(id) {
            const propSelect = document.getElementById('property_id');
            const card = document.getElementById('plot_card_' + id);
            if (!propSelect || !card) return;

            const opt = Array.from(propSelect.options).find(o => o.value == id);
            if (!opt) return;

            opt.selected = !opt.selected;
            syncSingleCardUI(id, opt.selected);
            window.updatePlotsCalculation();
        };

        function syncSingleCardUI(id, isSelected) {
            const card = document.getElementById('plot_card_' + id);
            if (!card) return;
            const checkCircle = card.querySelector('.plot-checkbox-circle');
            const checkIcon = card.querySelector('.plot-checkbox-circle i');

            if (isSelected) {
                card.classList.add('is-selected');
                card.style.background = 'rgba(37, 99, 235, 0.22)';
                card.style.borderColor = '#3B82F6';
                card.style.boxShadow = '0 0 14px rgba(59, 130, 246, 0.35)';
                if (checkCircle) {
                    checkCircle.style.borderColor = '#3B82F6';
                    checkCircle.style.background = '#2563EB';
                }
                if (checkIcon) checkIcon.style.display = 'block';
            } else {
                card.classList.remove('is-selected');
                card.style.background = 'rgba(20, 27, 41, 0.65)';
                card.style.borderColor = 'rgba(255, 255, 255, 0.10)';
                card.style.boxShadow = 'none';
                if (checkCircle) {
                    checkCircle.style.borderColor = 'rgba(255, 255, 255, 0.25)';
                    checkCircle.style.background = 'transparent';
                }
                if (checkIcon) checkIcon.style.display = 'none';
            }
        }

        window.selectAllVisiblePlots = function() {
            const propSelect = document.getElementById('property_id');
            const cards = document.querySelectorAll('.plot-card-item');
            if (!propSelect) return;

            cards.forEach(card => {
                if (card.style.display !== 'none') {
                    const id = card.dataset.id;
                    const opt = Array.from(propSelect.options).find(o => o.value == id);
                    if (opt) {
                        opt.selected = true;
                        syncSingleCardUI(id, true);
                    }
                }
            });
            window.updatePlotsCalculation();
        };

        window.selectAvailablePlotsOnly = function() {
            const propSelect = document.getElementById('property_id');
            const cards = document.querySelectorAll('.plot-card-item');
            if (!propSelect) return;

            cards.forEach(card => {
                const id = card.dataset.id;
                const opt = Array.from(propSelect.options).find(o => o.value == id);
                if (card.style.display !== 'none' && card.dataset.status === 'available') {
                    if (opt) {
                        opt.selected = true;
                        syncSingleCardUI(id, true);
                    }
                } else {
                    if (opt) {
                        opt.selected = false;
                        syncSingleCardUI(id, false);
                    }
                }
            });
            window.updatePlotsCalculation();
        };

        window.clearSelectedPlots = function() {
            const propSelect = document.getElementById('property_id');
            if (!propSelect) return;
            Array.from(propSelect.options).forEach(opt => {
                opt.selected = false;
                if (opt.value) syncSingleCardUI(opt.value, false);
            });
            window.updatePlotsCalculation();
        };

        window.filterPlotCardsBySearch = function() {
            const searchInput = document.getElementById('plot_search_input');
            const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
            const masterSelect = document.getElementById('property_master_id');
            const selectedMasterId = masterSelect ? masterSelect.value : '';
            const cards = document.querySelectorAll('.plot-card-item');
            const noPlotsMsg = document.getElementById('no_plots_msg');

            let visibleCount = 0;
            cards.forEach(card => {
                const masterId = card.dataset.masterId || '';
                const name = card.dataset.name || '';
                const unitNo = card.dataset.unitNo || '';
                const code = card.dataset.code || '';

                const matchesMaster = (!selectedMasterId || masterId === selectedMasterId);
                const matchesQuery = (!query || name.includes(query) || unitNo.includes(query) || code.includes(query));

                if (matchesMaster && matchesQuery) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (noPlotsMsg) {
                noPlotsMsg.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        };

        function filterPlotsByMaster() {
            const masterSelect = document.getElementById('property_master_id');
            const propSelect = document.getElementById('property_id');
            if (!masterSelect || !propSelect) return;

            const selectedMasterId = masterSelect.value;
            const cards = document.querySelectorAll('.plot-card-item');
            const noPlotsMsg = document.getElementById('no_plots_msg');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardMasterId = card.dataset.masterId || '';
                const id = card.dataset.id;
                const opt = Array.from(propSelect.options).find(o => o.value == id);

                if (!selectedMasterId || cardMasterId === selectedMasterId) {
                    card.style.display = 'flex';
                    if (opt) {
                        opt.hidden = false;
                        opt.disabled = false;
                    }
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                    if (opt) {
                        opt.hidden = true;
                        opt.disabled = true;
                        opt.selected = false;
                        syncSingleCardUI(id, false);
                    }
                }
            });

            if (noPlotsMsg) {
                noPlotsMsg.style.display = (visibleCount === 0) ? 'block' : 'none';
            }

            window.filterPlotCardsBySearch();
            window.updatePlotsCalculation();
        }

        window.updatePlotsCalculation = function() {
            const propSelect = document.getElementById('property_id');
            const totalAmountInput = document.getElementById('total_amount');
            const badge = document.getElementById('plots_summary_badge');
            const badgeText = document.getElementById('plots_summary_text');
            const badgePills = document.getElementById('plots_summary_pills');
            if (!propSelect || !totalAmountInput) return;

            let totalSum = 0;
            let selectedCount = 0;
            let pillsHtml = '';

            Array.from(propSelect.selectedOptions).forEach(opt => {
                if (opt.value) {
                    const card = document.getElementById('plot_card_' + opt.value);
                    const p = card ? (parseFloat(card.dataset.price) || 0) : (parseFloat(opt.dataset.price) || 0);
                    totalSum += p;
                    selectedCount++;
                    const name = card ? card.querySelector('[style*="font-weight: 700; color: #FFFFFF"]').innerText : opt.text.trim();
                    pillsHtml += `<span style="background: rgba(59, 130, 246, 0.25); border: 1px solid rgba(59, 130, 246, 0.45); padding: 2px 8px; border-radius: 6px; font-size: 11px; color: #E0F2FE;">${name}</span>`;
                }
            });

            if (selectedCount > 0) {
                totalAmountInput.value = totalSum.toFixed(2);
                calculateAmounts();
                if (badge && badgeText) {
                    badge.style.display = 'flex';
                    badgeText.innerHTML = `<strong>${selectedCount} Unit(s) Selected</strong> (Total: ₹ ${totalSum.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})})`;
                    if (badgePills) badgePills.innerHTML = pillsHtml;
                }
            } else {
                if (badge) badge.style.display = 'none';
            }
        };

        const allProjectsList = @json($projects->map(fn($p) => ['id' => $p->id, 'name' => $p->project_name]));

        function syncProjectsForSelectedMaster(preferredProjectId = null) {
            const masterSelect = document.getElementById('property_master_id');
            const projectSelect = document.getElementById('project_id');
            const projectHint = document.getElementById('project_hint_msg');
            if (!masterSelect || !projectSelect) return;

            const selectedOpt = masterSelect.selectedOptions[0];
            const currentVal = preferredProjectId !== null ? preferredProjectId : projectSelect.value;

            if (!selectedOpt || !selectedOpt.value) {
                let html = `<option value="">— All / Direct —</option>`;
                allProjectsList.forEach(p => {
                    html += `<option value="${p.id}" ${currentVal == p.id ? 'selected' : ''}>${p.name}</option>`;
                });
                projectSelect.innerHTML = html;
                if (projectHint) {
                    projectHint.innerHTML = `<span style="color:#94A3B8;">Select Property first. If property belongs to a project, it will link automatically.</span>`;
                }
                return;
            }

            let linkedProjects = [];
            try {
                linkedProjects = JSON.parse(selectedOpt.dataset.projects || '[]');
            } catch(e) {
                linkedProjects = [];
            }

            if (linkedProjects && linkedProjects.length > 0) {
                let html = '';
                if (linkedProjects.length > 1) {
                    html += `<option value="">-- Select Linked Project (or Direct) --</option>`;
                }

                let hasMatchedCurrent = false;
                linkedProjects.forEach(p => {
                    const isSel = (currentVal == p.id) || (linkedProjects.length === 1 && !currentVal);
                    if (isSel) hasMatchedCurrent = true;
                    html += `<option value="${p.id}" ${isSel ? 'selected' : ''}>${p.name}</option>`;
                });

                html += `<option value="" ${(!hasMatchedCurrent && currentVal === '') ? 'selected' : ''}>— Direct / Standalone (No Project) —</option>`;
                projectSelect.innerHTML = html;

                if (projectHint) {
                    const names = linkedProjects.map(p => p.name).join(', ');
                    projectHint.innerHTML = `<span style="color:#60A5FA;font-weight:600;"><i class="fa-solid fa-city"></i> Linked to Project: <strong>${names}</strong></span>`;
                }
            } else {
                let html = `<option value="" selected>— Direct Property / No Project —</option>`;
                projectSelect.innerHTML = html;
                if (projectHint) {
                    projectHint.innerHTML = `<span style="color:#34D399;font-weight:600;"><i class="fa-solid fa-circle-check"></i> Direct Standalone Property (No Project needed)</span>`;
                }
            }
        }

        const handleMasterChange = function() {
            syncProjectsForSelectedMaster();
            const isEntire = document.getElementById('booking_scope_entire').checked;
            if (isEntire) {
                updateEntirePropInfo();
                const opt = masterSelect.selectedOptions[0];
                if (opt && opt.dataset.price) {
                    const price = parseFloat(opt.dataset.price);
                    if (price > 0) {
                        totalAmountInput.value = price.toFixed(2);
                        calculateAmounts();
                    }
                }
            } else {
                filterPlotsByMaster();
            }
        };

        const handlePropChange = function() {
            window.updatePlotsCalculation();
        };

        const masterSelect = document.getElementById('property_master_id');
        if (masterSelect) {
            masterSelect.addEventListener('change', handleMasterChange);
        }

        const propSelect = document.getElementById('property_id');
        if (propSelect) {
            propSelect.addEventListener('change', handlePropChange);
        }

        if (window.jQuery) {
            jQuery('#property_master_id').on('change select2:select select2:unselect select2:clear', handleMasterChange);
            jQuery('#property_id').on('change select2:select select2:unselect select2:clear', handlePropChange);
        }

        syncProjectsForSelectedMaster("{{ old('project_id', request('project_id')) }}");
        window.onBookingScopeChange();
        calculateAmounts();
    });
</script>
@endsection
