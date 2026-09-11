@extends('admin.layouts.app')

@section('title', 'Edit Property Sell')
@section('page-title', 'Property Sell')

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

.section-title {
    font-size: 13px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 18px; padding-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.form-section { margin-bottom: 24px; }
.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
@media(max-width:768px){ .form-row-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .form-row, .form-row-3 { grid-template-columns: 1fr; gap: 0; } }

.form-label { display: block; font-size: 13px; font-weight: 700; color: #CBD5E1 !important; margin-bottom: 8px; }
.form-label span { color: #F87171 !important; }
.form-hint { font-size: 12px; color: #94A3B8 !important; margin-top: 5px; }

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
.calc-hint { font-size: 11.5px; color: #FBBF24 !important; margin-top: 5px; font-weight: 600; }

.form-actions { display: flex; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }
.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 10px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    display: inline-flex; align-items: center; gap: 8px; text-decoration: none !important;
}
.btn-gold:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }
.btn-outline {
    background: transparent; color: #94A3B8 !important; border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    padding: 11px 24px; border-radius: 10px; text-decoration: none !important;
    font-size: 14px; font-weight: 600; transition: all .2s ease;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.10) !important; color: #FFFFFF !important; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Property Sell</h2>
        <p>Update property sale agreement and payment status.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('property-sales.update', $propertySale->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Parties --}}
        @php
            $isEntireSale = ($propertySale->property && $propertySale->property->property_master_id && $propertySale->property->unit_no === null) || old('sale_scope') === 'entire';
            $currentMasterId = old('property_master_id', $propertySale->property?->property_master_id ?? ($propertySale->property && !$propertySale->property->property_master_id ? 'direct' : ''));
        @endphp

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-handshake"></i> Sale Parties & Property</div>
            @include('admin.components.firm-select', ['model' => $propertySale])

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
                                    {{ $currentMasterId == $pm->id ? 'selected' : '' }}>
                                {{ $pm->property_name }}
                                @if($pm->property_code) [{{ $pm->property_code }}] @endif
                                @if($pm->total_area) — Area: {{ $pm->total_area }} {{ $pm->area_unit ?? 'Sq.Ft' }} @endif
                                ({{ $pm->plots->count() }} Plots)
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">Select the main Property / Land acquisition.</div>
                    @error('property_master_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="project_id">Project / Scheme (Optional)</label>
                    <select name="project_id" id="project_id" class="form-control">
                        <option value="">— All / Direct —</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}" {{ old('project_id', $propertySale->property?->project_id) == $proj->id ? 'selected' : '' }}>
                                {{ $proj->project_name }}
                            </option>
                        @endforeach
                    </select>
                    <div id="project_hint_msg" class="form-hint">Select Property first. If property belongs to a project, it will link automatically.</div>
                </div>
            </div>

            {{-- 2. Sale Scope / Type Switcher --}}
            <div class="form-group" style="margin-bottom: 22px;">
                <label class="form-label">What would you like to sell? <span>*</span></label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <label id="scope_entire_card" style="display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-radius: 12px; border: 1.5px solid rgba(59, 130, 246, 0.5); background: rgba(37, 99, 235, 0.12); cursor: pointer; transition: all .2s ease;">
                        <input type="radio" name="sale_scope" id="sale_scope_entire" value="entire" {{ $isEntireSale ? 'checked' : '' }} onchange="onSaleScopeChange()" style="accent-color: #3B82F6; width: 18px; height: 18px;">
                        <div>
                            <div style="font-weight: 800; font-size: 14px; color: #60A5FA;">🏢 Entire Property / Whole Land</div>
                            <div style="font-size: 12px; color: #CBD5E1; margin-top: 2px;">Sell the complete property / all plots together in one deal.</div>
                        </div>
                    </label>
                    <label id="scope_plot_card" style="display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-radius: 12px; border: 1.5px solid rgba(255, 255, 255, 0.15); background: rgba(16, 22, 34, 0.6); cursor: pointer; transition: all .2s ease;">
                        <input type="radio" name="sale_scope" id="sale_scope_plot" value="plot" {{ !$isEntireSale ? 'checked' : '' }} onchange="onSaleScopeChange()" style="accent-color: #3B82F6; width: 18px; height: 18px;">
                        <div>
                            <div style="font-weight: 800; font-size: 14px; color: #FBBF24;">🏷️ Specific Plot / Unit</div>
                            <div style="font-size: 12px; color: #CBD5E1; margin-top: 2px;">Sell an individual plot or sub-unit under this property.</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- 3A. Entire Property Summary Banner --}}
            <div id="entire_prop_banner" style="display: none; background: rgba(37, 99, 235, 0.10); border: 1.5px dashed rgba(96, 165, 250, 0.4); border-radius: 14px; padding: 16px 20px; margin-bottom: 22px;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <div style="font-weight: 800; color: #93C5FD; font-size: 14.5px;" id="entire_prop_title">
                            🏢 Selling Entire Property Master
                        </div>
                        <div style="font-size: 12.5px; color: #CBD5E1; margin-top: 3px;" id="entire_prop_desc">
                            Please select a Property / Land Master above.
                        </div>
                    </div>
                    <div id="entire_prop_badge" style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #34D399; font-size: 12.5px; font-weight: 700; padding: 6px 14px; border-radius: 8px;">
                        Whole Land Sale
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
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}"
                                data-master-id="{{ $property->property_master_id ?? '' }}"
                                data-project-id="{{ $property->project_id ?? '' }}"
                                data-unit-no="{{ $property->unit_no ?? '' }}"
                                data-price="{{ $property->price ?? 0 }}"
                                {{ (is_array(old('property_ids')) && in_array($property->id, old('property_ids'))) || old('property_id', $propertySale->property_id) == $property->id ? 'selected' : '' }}>
                            {{ $property->property_name }}
                        </option>
                    @endforeach
                </select>

                {{-- Visual Units Card Grid --}}
                <div id="plot_cards_scrollbox" style="max-height: 320px; overflow-y: auto; padding: 12px; border-radius: 14px; background: rgba(10, 15, 26, 0.75); border: 1.5px solid rgba(255, 255, 255, 0.12); box-shadow: inset 0 2px 8px rgba(0,0,0,0.4);">
                    <div id="plot_cards_grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 10px;">
                        @foreach($properties as $property)
                            @php
                                $isSelected = (is_array(old('property_ids')) && in_array($property->id, old('property_ids'))) || old('property_id', $propertySale->property_id) == $property->id;
                                $statusColor = $property->status === 'available' ? '#34D399' : ($property->status === 'booked' ? '#FBBF24' : '#F87171');
                                $statusBg = $property->status === 'available' ? 'rgba(16, 185, 129, 0.15)' : ($property->status === 'booked' ? 'rgba(245, 158, 11, 0.15)' : 'rgba(239, 68, 68, 0.15)');
                            @endphp
                            <div class="plot-card-item {{ $isSelected ? 'is-selected' : '' }}"
                                 id="plot_card_{{ $property->id }}"
                                 data-id="{{ $property->id }}"
                                 data-master-id="{{ $property->property_master_id ?? '' }}"
                                 data-project-id="{{ $property->project_id ?? '' }}"
                                 data-unit-no="{{ strtolower($property->unit_no ?? '') }}"
                                 data-name="{{ strtolower($property->property_name ?? '') }}"
                                 data-code="{{ strtolower($property->property_code ?? '') }}"
                                 data-price="{{ $property->price ?? 0 }}"
                                 data-status="{{ $property->status }}"
                                 onclick="togglePlotCardSelection({{ $property->id }})"
                                 style="cursor: pointer; user-select: none; padding: 10px 14px; border-radius: 12px; background: {{ $isSelected ? 'rgba(37, 99, 235, 0.22)' : 'rgba(20, 27, 41, 0.65)' }}; border: 1.5px solid {{ $isSelected ? '#3B82F6' : 'rgba(255, 255, 255, 0.10)' }}; transition: all .2s ease; display: flex; align-items: center; gap: 12px; box-shadow: {{ $isSelected ? '0 0 14px rgba(59, 130, 246, 0.35)' : 'none' }};">
                                <div class="plot-checkbox-circle" style="width: 22px; height: 22px; border-radius: 6px; border: 1.5px solid {{ $isSelected ? '#3B82F6' : 'rgba(255, 255, 255, 0.25)' }}; background: {{ $isSelected ? '#2563EB' : 'transparent' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #FFFFFF; font-size: 11px; transition: all .2s ease;">
                                    <i class="fa-solid fa-check" style="display: {{ $isSelected ? 'block' : 'none' }};"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: center; gap: 6px; justify-content: space-between;">
                                        <div style="font-weight: 700; color: #FFFFFF; font-size: 13.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $property->property_name }}
                                        </div>
                                        @if($property->unit_no || $property->property_code)
                                            <span style="font-size: 10.5px; font-weight: 700; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); padding: 1px 6px; border-radius: 4px; color: #93C5FD; flex-shrink: 0;">
                                                {{ $property->property_code ?: 'Unit '.$property->unit_no }}
                                            </span>
                                        @endif
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 4px;">
                                        <div style="font-size: 12px; font-weight: 700; color: #34D399;">
                                            ₹ {{ number_format($property->price ?? 0, 2) }}
                                        </div>
                                        <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 2px 6px; border-radius: 4px; background: {{ $statusBg }}; color: {{ $statusColor }};">
                                            {{ ucfirst($property->status) }}
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
                @error('property_id') <div class="text-error">{{ $message }}</div> @enderror
                @error('property_ids') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="customer_id">Customer <span>*</span></label>
                    <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $propertySale->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} — {{ $customer->mobile }}{{ $customer->alternate_mobile ? ' / ' . $customer->alternate_mobile : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="sale_date">Sale Date</label>
                    <input type="date" name="sale_date" id="sale_date"
                           value="{{ old('sale_date', is_string($propertySale->sale_date) ? $propertySale->sale_date : ($propertySale->sale_date ? \Carbon\Carbon::parse($propertySale->sale_date)->format('Y-m-d') : '')) }}" class="form-control @error('sale_date') is-invalid @enderror">
                    @error('sale_date') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="broker_id">Broker / Agent (Optional)</label>
                    <select name="broker_id" id="broker_id" class="form-control @error('broker_id') is-invalid @enderror" onchange="handleSaleBrokerChange(this)">
                        <option value="">-- Select Broker (Optional) --</option>
                        @foreach($brokers as $broker)
                            <option value="{{ $broker->id }}" 
                                    data-commission="{{ $broker->commission_percentage ?? 0 }}"
                                    {{ old('broker_id', $propertySale->broker_id) == $broker->id ? 'selected' : '' }}>
                                {{ $broker->name }} — {{ $broker->mobile }} {{ $broker->commission_percentage ? '(' . $broker->commission_percentage . '%)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('broker_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Broker Commission Section -->
            <div id="sale_broker_commission_box" style="{{ ($propertySale->broker_id || old('broker_id')) ? 'display:block;' : 'display:none;' }} background: rgba(167, 139, 250, 0.08); border: 1.5px solid rgba(167, 139, 250, 0.3); border-radius: 12px; padding: 16px; margin-top: 10px;">
                <div style="font-size: 13px; font-weight: 700; color: #A78BFA; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-percent"></i> Broker Commission Details (દલાલી વિગતો)
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #DDD6FE;">Commission Type</label>
                        <select name="broker_commission_type" id="sale_broker_comm_type" class="form-control" onchange="recalcSaleBrokerage()">
                            <option value="percentage" {{ old('broker_commission_type', $propertySale->broker_commission_type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ old('broker_commission_type', $propertySale->broker_commission_type) === 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #DDD6FE;">Commission Rate / Value</label>
                        <input type="number" step="0.01" name="broker_commission_rate" id="sale_broker_comm_rate" value="{{ old('broker_commission_rate', $propertySale->broker_commission_rate) }}" class="form-control" placeholder="e.g. 2.0" oninput="recalcSaleBrokerage()">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #A78BFA; font-weight: 700;">Total Commission (₹)</label>
                        <input type="number" step="0.01" name="broker_commission_amount" id="sale_broker_comm_amount" value="{{ old('broker_commission_amount', $propertySale->broker_commission_amount) }}" class="form-control" placeholder="0.00" oninput="recalcSaleBrokerageDue()">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #34D399; font-weight: 700;">Commission Paid (₹)</label>
                        <input type="number" step="0.01" name="broker_commission_paid" id="sale_broker_comm_paid" value="{{ old('broker_commission_paid', $propertySale->broker_commission_paid ?? 0) }}" class="form-control" placeholder="0.00" oninput="recalcSaleBrokerageDue()">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #FBBF24; font-weight: 700;">Commission Due (₹)</label>
                        <input type="number" step="0.01" name="broker_commission_due" id="sale_broker_comm_due" value="{{ old('broker_commission_due', $propertySale->broker_commission_due ?? 0) }}" class="form-control" placeholder="0.00" readonly style="background: rgba(0,0,0,0.15);">
                    </div>
                </div>
            </div>
        </div>

        {{-- Amounts --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Amount & Financial Details</div>
            <div style="background: rgba(15, 23, 42, 0.65); border: 1.5px solid rgba(255, 255, 255, 0.12); border-radius: 16px; padding: 22px; margin-bottom: 22px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 16px;">
                    <!-- 1. Total Sale Amount -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="sale_amount" style="color: #60A5FA !important; font-size: 13.5px;">
                            <i class="fa-solid fa-money-bill-wave"></i> Total Sale Amount (₹) <span>*</span>
                        </label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 14px; top: 11px; color: #60A5FA; font-weight: 700; font-size: 15px;">₹</span>
                            <input type="number" step="0.01" name="sale_amount" id="sale_amount"
                                   value="{{ old('sale_amount', $propertySale->sale_amount) }}" class="form-control @error('sale_amount') is-invalid @enderror"
                                   placeholder="0.00" oninput="calcRemaining()" required
                                   style="padding-left: 32px; font-weight: 800; font-size: 16px; color: #60A5FA !important; border-color: rgba(96, 165, 250, 0.4) !important;">
                        </div>
                        <div class="form-hint" style="color: #93C5FD;">Total agreed sale/deal price.</div>
                        @error('sale_amount') <div class="text-error">{{ $message }}</div> @enderror
                    </div>

                    <!-- 2. Paid / Booking Amount -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="booking_amount" style="color: #34D399 !important; font-size: 13.5px;">
                            <i class="fa-solid fa-circle-check"></i> Paid / Booking Amount (₹)
                        </label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 14px; top: 11px; color: #34D399; font-weight: 700; font-size: 15px;">₹</span>
                            <input type="number" step="0.01" name="booking_amount" id="booking_amount"
                                   value="{{ old('booking_amount', $propertySale->booking_amount ?? '0.00') }}" class="form-control @error('booking_amount') is-invalid @enderror"
                                   placeholder="0.00" min="0" oninput="calcRemaining()"
                                   style="padding-left: 32px; font-weight: 800; font-size: 16px; color: #34D399 !important; border-color: rgba(52, 211, 153, 0.4) !important;">
                        </div>
                        <div class="form-hint" style="color: #A7F3D0;">Received advance or paid amount.</div>
                        @error('booking_amount') <div class="text-error">{{ $message }}</div> @enderror
                    </div>

                    <!-- 3. Remaining Due Balance -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="remaining_amount" style="color: #F87171 !important; font-size: 13.5px;">
                            <i class="fa-solid fa-clock-rotate-left"></i> Remaining Due (₹)
                        </label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 14px; top: 11px; color: #F87171; font-weight: 700; font-size: 15px;">₹</span>
                            <input type="number" step="0.01" name="remaining_amount" id="remaining_amount"
                                   value="{{ old('remaining_amount', $propertySale->remaining_amount ?? '0.00') }}" class="form-control @error('remaining_amount') is-invalid @enderror"
                                   placeholder="0.00" readonly
                                   style="padding-left: 32px; font-weight: 800; font-size: 16px; color: #F87171 !important; background: rgba(239, 68, 68, 0.08) !important; border-color: rgba(248, 113, 113, 0.4) !important; cursor: not-allowed;">
                        </div>
                        <div class="form-hint" style="color: #FCA5A5;">Auto = Total Sale − Paid Amount</div>
                        @error('remaining_amount') <div class="text-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Quick Buttons -->
                <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center; padding-top: 10px; border-top: 1px dashed rgba(255, 255, 255, 0.1);">
                    <span style="font-size: 12px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Quick Set:</span>
                    <button type="button" onclick="setSaleQuickPayment('full')" style="background: rgba(16, 185, 129, 0.18); border: 1px solid rgba(16, 185, 129, 0.4); color: #34D399; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 8px; cursor: pointer;">
                        <i class="fa-solid fa-check-double"></i> Full Paid (100%)
                    </button>
                    <button type="button" onclick="setSaleQuickPayment('half')" style="background: rgba(245, 158, 11, 0.18); border: 1px solid rgba(245, 158, 11, 0.4); color: #FBBF24; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 8px; cursor: pointer;">
                        <i class="fa-solid fa-percent"></i> 50% Advance
                    </button>
                    <button type="button" onclick="setSaleQuickPayment('unpaid')" style="background: rgba(239, 68, 68, 0.18); border: 1px solid rgba(239, 68, 68, 0.4); color: #F87171; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 8px; cursor: pointer;">
                        <i class="fa-solid fa-xmark"></i> Unpaid (0%)
                    </button>
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-circle-dot"></i> Status & Documents</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_status">Payment Status <span>*</span></label>
                    <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror">
                        @foreach(['pending' => 'Pending / Unpaid', 'partial' => 'Partial Paid', 'paid' => 'Full Paid'] as $val => $label)
                            <option value="{{ $val }}" {{ old('payment_status', $propertySale->payment_status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('payment_status') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="sale_status">Sale Status <span>*</span></label>
                    <select name="sale_status" id="sale_status" class="form-control @error('sale_status') is-invalid @enderror">
                        @foreach(['booked' => 'Booked', 'sold' => 'Sold', 'cancelled' => 'Cancelled'] as $val => $label)
                            <option value="{{ $val }}" {{ old('sale_status', $propertySale->sale_status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="form-hint">Changing this will also update the property status.</div>
                    @error('sale_status') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="agreement_file">Agreement / Document</label>
                    <input type="file" name="agreement_file" id="agreement_file" class="form-control @error('agreement_file') is-invalid @enderror">
                    @if($propertySale->agreement_file)
                        <div class="form-hint">Current file: <a href="{{ asset('storage/' . $propertySale->agreement_file) }}" target="_blank" style="color: #60A5FA;">View Document</a></div>
                    @endif
                    @error('agreement_file') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="note">Note</label>
                <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror"
                          placeholder="Add any additional notes about this sale or booking...">{{ old('note', $propertySale->note) }}</textarea>
                @error('note') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Update Property Sale
            </button>
            <a href="{{ route('property-sales.index') }}" class="btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
function calcRemaining() {
    const sale = parseFloat(document.getElementById('sale_amount').value) || 0;
    const bookingInput = document.getElementById('booking_amount');
    let booking = parseFloat(bookingInput.value) || 0;
    if (booking < 0) {
        booking = 0;
        bookingInput.value = '0.00';
    }

    const remaining = Math.max(0, sale - booking);
    document.getElementById('remaining_amount').value = remaining.toFixed(2);

    const paymentStatus = document.getElementById('payment_status');
    if (sale > 0 && booking >= sale) {
        paymentStatus.value = 'paid';
    } else if (booking > 0) {
        paymentStatus.value = 'partial';
    } else {
        paymentStatus.value = 'pending';
    }
}

function setSaleQuickPayment(type) {
    const sale = parseFloat(document.getElementById('sale_amount').value) || 0;
    const bookingInput = document.getElementById('booking_amount');
    if (type === 'full') {
        bookingInput.value = sale.toFixed(2);
    } else if (type === 'half') {
        bookingInput.value = (sale / 2).toFixed(2);
    } else if (type === 'unpaid') {
        bookingInput.value = '0.00';
    }
    calcRemaining();
}

function onSaleScopeChange() {
    const isEntire = document.getElementById('sale_scope_entire').checked;
    const entireCard = document.getElementById('scope_entire_card');
    const plotCard = document.getElementById('scope_plot_card');
    const entireBanner = document.getElementById('entire_prop_banner');
    const plotContainer = document.getElementById('plot_select_container');
    const propSelect = document.getElementById('property_id');

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
}

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

        titleEl.innerHTML = `🏢 Selling Entire Property: <strong>${name}</strong> ${code ? `(${code})` : ''}`;
        descEl.innerHTML = `Total Area: <strong>${area || 'N/A'}</strong> | Includes <strong>${plotsCount} Plots</strong>. All sub-plots will be marked as Sold upon confirmation.`;
    } else {
        titleEl.innerHTML = `🏢 Selling Entire Property Master`;
        descEl.innerHTML = `Please select a Property / Land Master above.`;
    }
}

function togglePlotCardSelection(id) {
    const propSelect = document.getElementById('property_id');
    const card = document.getElementById('plot_card_' + id);
    if (!propSelect || !card) return;

    const opt = Array.from(propSelect.options).find(o => o.value == id);
    if (!opt) return;

    // Toggle
    opt.selected = !opt.selected;
    syncSingleCardUI(id, opt.selected);
    updatePlotsCalculation();
}

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

function selectAllVisiblePlots() {
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
    updatePlotsCalculation();
}

function selectAvailablePlotsOnly() {
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
    updatePlotsCalculation();
}

function clearSelectedPlots() {
    const propSelect = document.getElementById('property_id');
    if (!propSelect) return;
    Array.from(propSelect.options).forEach(opt => {
        opt.selected = false;
        if (opt.value) syncSingleCardUI(opt.value, false);
    });
    updatePlotsCalculation();
}

function filterPlotCardsBySearch() {
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
}

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

    filterPlotCardsBySearch();
    updatePlotsCalculation();
}

function updatePlotsCalculation() {
    const propSelect = document.getElementById('property_id');
    const saleAmountInput = document.getElementById('sale_amount');
    const badge = document.getElementById('plots_summary_badge');
    const badgeText = document.getElementById('plots_summary_text');
    const badgePills = document.getElementById('plots_summary_pills');
    if (!propSelect || !saleAmountInput) return;

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
        saleAmountInput.value = totalSum.toFixed(2);
        calcRemaining();
        if (badge && badgeText) {
            badge.style.display = 'flex';
            badgeText.innerHTML = `<strong>${selectedCount} Unit(s) Selected</strong> (Total: ₹ ${totalSum.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})})`;
            if (badgePills) badgePills.innerHTML = pillsHtml;
        }
    } else {
        if (badge) badge.style.display = 'none';
    }
}

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

function handleSaleBrokerChange(select) {
    const box = document.getElementById('sale_broker_commission_box');
    const rateInput = document.getElementById('sale_broker_comm_rate');
    const typeSelect = document.getElementById('sale_broker_comm_type');

    if (select && select.value) {
        if (box) box.style.display = 'block';
        const opt = select.options[select.selectedIndex];
        const comm = opt ? opt.getAttribute('data-commission') : null;
        if (comm && parseFloat(comm) > 0 && rateInput && (!rateInput.value || parseFloat(rateInput.value) === 0)) {
            rateInput.value = comm;
            if (typeSelect) typeSelect.value = 'percentage';
        }
    } else {
        if (box) box.style.display = 'none';
    }
    recalcSaleBrokerage();
}

function recalcSaleBrokerage() {
    const saleAmountInput = document.getElementById('sale_amount');
    const typeSelect = document.getElementById('sale_broker_comm_type');
    const rateInput = document.getElementById('sale_broker_comm_rate');
    const amountInput = document.getElementById('sale_broker_comm_amount');

    if (!typeSelect || !rateInput || !amountInput) return;

    const saleAmount = parseFloat(saleAmountInput ? saleAmountInput.value : 0) || 0;
    const type = typeSelect.value;
    const rate = parseFloat(rateInput.value) || 0;

    if (type === 'percentage') {
        if (saleAmount > 0 && rate > 0) {
            const calculated = (saleAmount * rate) / 100;
            amountInput.value = calculated.toFixed(2);
        }
    } else if (type === 'fixed') {
        if (rate > 0) {
            amountInput.value = rate.toFixed(2);
        }
    }

    recalcSaleBrokerageDue();
}

function recalcSaleBrokerageDue() {
    const amountInput = document.getElementById('sale_broker_comm_amount');
    const paidInput   = document.getElementById('sale_broker_comm_paid');
    const dueInput    = document.getElementById('sale_broker_comm_due');

    if (!amountInput || !paidInput || !dueInput) return;

    const amount = parseFloat(amountInput.value) || 0;
    const paid   = parseFloat(paidInput.value) || 0;
    const due    = Math.max(0, amount - paid);

    dueInput.value = due.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    const masterSelect = document.getElementById('property_master_id');
    const propSelect = document.getElementById('property_id');
    const saleAmountInput = document.getElementById('sale_amount');

    const handleMasterChange = function() {
        syncProjectsForSelectedMaster();
        const isEntire = document.getElementById('sale_scope_entire').checked;
        if (isEntire) {
            updateEntirePropInfo();
            const opt = masterSelect.selectedOptions[0];
            if (opt && opt.dataset.price) {
                const price = parseFloat(opt.dataset.price);
                if (price > 0 && (!saleAmountInput.value || saleAmountInput.value === '0')) {
                    saleAmountInput.value = price.toFixed(2);
                    calcRemaining();
                    recalcSaleBrokerage();
                }
            }
        } else {
            filterPlotsByMaster();
        }
    };

    const handlePropChange = function() {
        updatePlotsCalculation();
        recalcSaleBrokerage();
    };

    if (masterSelect) {
        masterSelect.addEventListener('change', handleMasterChange);
    }

    if (propSelect) {
        propSelect.addEventListener('change', handlePropChange);
    }

    if (window.jQuery) {
        jQuery('#property_master_id').on('change select2:select select2:unselect select2:clear', handleMasterChange);
        jQuery('#property_id').on('change select2:select select2:unselect select2:clear', handlePropChange);
    }

    syncProjectsForSelectedMaster("{{ old('project_id', $propertySale->property?->project_id) }}");
    onSaleScopeChange();
    updatePlotsCalculation();
    recalcSaleBrokerageDue();
});
</script>
@endsection
