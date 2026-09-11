@extends('admin.layouts.app')

@section('title', 'Add Property Master')
@section('page-title', 'Property Management')

@section('content')
<style>
    .breadcrumb-nav {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: rgba(20, 27, 41, 0.60);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.10);
        border-radius: 12px;
        font-size: 13px;
        color: #94A3B8;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .breadcrumb-nav a {
        color: #60A5FA;
        text-decoration: none;
        font-weight: 700;
        transition: color 0.15s;
    }
    .breadcrumb-nav a:hover { color: #93C5FD; }
    .breadcrumb-nav .separator { font-size: 10px; color: #64748B; }
    .breadcrumb-nav .active { color: #FFFFFF; font-weight: 700; }

    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .crud-title h2 {
        font-size: 26px;
        font-weight: 800;
        color: #FFFFFF;
        margin-bottom: 4px;
        letter-spacing: -0.3px;
    }

    .crud-title p {
        font-size: 13.5px;
        color: #94A3B8;
        margin: 0;
    }

    .form-card {
        background: rgba(20, 27, 41, 0.70) !important;
        backdrop-filter: blur(20px) saturate(160%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 20px !important;
        padding: 28px !important;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
        margin-bottom: 30px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    @media (max-width: 850px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 13px;
        font-weight: 700;
        color: #CBD5E1;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid rgba(255, 255, 255, 0.14) !important;
        border-radius: 10px !important;
        font-size: 13.5px !important;
        outline: none !important;
        transition: all 0.2s ease !important;
        background: rgba(15, 23, 42, 0.70) !important;
        color: #FFFFFF !important;
    }

    .form-control:focus {
        border-color: #60A5FA !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.20) !important;
    }

    .form-control::placeholder {
        color: #64748B !important;
    }

    .form-control option {
        background: #0F172A !important;
        color: #FFFFFF !important;
    }

    /* ── Luxury Input Groups ── */
    .input-luxury-group {
        display: flex;
        align-items: stretch;
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.2s ease;
        width: 100%;
    }
    .input-luxury-group:focus-within {
        border-color: #60A5FA !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.25) !important;
    }
    .input-luxury-group.border-emerald { border-color: rgba(16, 185, 129, 0.40); }
    .input-luxury-group.border-emerald:focus-within { border-color: #10B981 !important; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25) !important; }
    .input-luxury-group.border-blue { border-color: rgba(59, 130, 246, 0.40); }
    .input-luxury-group.border-blue:focus-within { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }
    .input-luxury-group.border-amber { border-color: rgba(245, 158, 11, 0.40); }
    .input-luxury-group.border-amber:focus-within { border-color: #F59E0B !important; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25) !important; }
    .input-luxury-group.border-purple { border-color: rgba(167, 139, 250, 0.40); }
    .input-luxury-group.border-purple:focus-within { border-color: #8B5CF6 !important; box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.25) !important; }

    .input-luxury-group.bg-readonly {
        background: rgba(10, 15, 29, 0.85);
    }

    .input-luxury-addon {
        padding: 0 13px;
        font-weight: 800;
        font-size: 15px;
        background: rgba(255, 255, 255, 0.05);
        border-right: 1px solid rgba(255, 255, 255, 0.10);
        display: flex;
        align-items: center;
        justify-content: center;
        user-select: none;
        flex-shrink: 0;
    }
    .input-luxury-addon.text-emerald { color: #34D399; background: rgba(16, 185, 129, 0.15); }
    .input-luxury-addon.text-blue    { color: #60A5FA; background: rgba(59, 130, 246, 0.15); }
    .input-luxury-addon.text-amber   { color: #FBBF24; background: rgba(245, 158, 11, 0.15); }
    .input-luxury-addon.text-purple  { color: #C4B5FD; background: rgba(167, 139, 250, 0.15); }

    .input-luxury-group input {
        flex: 1;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 10px 14px !important;
        color: #FFFFFF !important;
        font-size: 14.5px !important;
        font-weight: 700 !important;
        outline: none !important;
        width: 100%;
    }
    .input-luxury-group input::placeholder {
        color: #64748B !important;
        font-weight: 500;
    }

    .btn-gold {
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
        color: #FFFFFF !important;
        padding: 10px 24px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid #60A5FA !important;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    }
    .btn-gold:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50);
    }

    .btn-outline {
        border: 1px solid rgba(255, 255, 255, 0.15);
        background: rgba(255, 255, 255, 0.06);
        color: #CBD5E1;
        padding: 9px 18px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 700;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-outline:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #FFFFFF;
        border-color: rgba(255, 255, 255, 0.25);
    }

    .invalid-feedback {
        color: #F87171;
        font-size: 12px;
        margin-top: 3px;
        font-weight: 600;
    }
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-nav">
    <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house"></i></a>
    <span class="separator">/</span>
    <a href="{{ route('property-masters.index') }}">Property Masters</a>
    <span class="separator">/</span>
    <span class="active">Add New</span>
</div>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Property Master</h2>
        <p>Register a top-level property acquisition, pricing, units &amp; vendor records.</p>
    </div>
    <a href="{{ route('property-masters.index') }}" class="btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Back to Properties
    </a>
</div>

<div class="form-card">
    <form method="POST" action="{{ route('property-masters.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-grid">
            @if(Auth::user() && Auth::user()->isAdmin())
                <div class="form-group">
                    <label class="form-label">Firm <span style="color:#EF4444;">*</span></label>
                    <select name="firm_id" class="form-control" required>
                        <option value="">Select Firm</option>
                        @foreach($firms as $firm)
                            <option value="{{ $firm->id }}" {{ old('firm_id') == $firm->id ? 'selected' : '' }}>
                                {{ $firm->firm_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('firm_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            @endif

            <div class="form-group">
                <label class="form-label">Property Name <span style="color:#EF4444;">*</span></label>
                <input type="text" name="property_name" value="{{ old('property_name') }}" class="form-control" placeholder="e.g. Delawala Heights" required>
                @error('property_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Property Code</label>
                <input type="text" name="property_code" value="{{ old('property_code') }}" class="form-control" placeholder="Auto-generated if left blank">
                @error('property_code') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fa-solid fa-layer-group" style="color: #60A5FA;"></i> Property Type
                </label>
                <select name="property_type" id="property_type_select" class="form-control" onchange="handlePropertyTypeChange(this.value)">
                    <option value="">-- Select Property Type --</option>
                    @foreach($propertyTypes as $key => $label)
                        <option value="{{ $key }}" {{ old('property_type') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('property_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Status <span style="color:#EF4444;">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <!-- ================================================================
                 SECTION 2: PROPERTY SIZE, AREA & RATE (MOVED UP)
            ================================================================ -->
            <div class="form-group full-width" style="margin-top: 10px; margin-bottom: 5px;">
                <div style="font-size: 13px; font-weight: 800; color: #60A5FA; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1px solid rgba(255, 255, 255, 0.10); padding-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-ruler-combined"></i>
                    <span id="measurementSectionTitle">Property Size / Area &amp; Rate Details</span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" id="areaLabel">
                    <i class="fa-solid fa-mountain-sun" style="color: #34D399;"></i> Total Land / Property Area <span style="color:#94A3B8; font-weight: normal;">(optional)</span>
                </label>
                <div style="display: flex; gap: 8px;">
                    <input type="number" step="0.01" name="total_area" id="total_area" value="{{ old('total_area') }}" class="form-control" placeholder="e.g. 150000" oninput="calculateLandTotalPrice()">
                    <select name="area_unit" id="area_unit" class="form-control" style="width: 125px; flex-shrink: 0;" onchange="calculateLandTotalPrice()">
                        @foreach(['Sq.Ft', 'Sq.Yd', 'Sq.Mtr', 'Acre', 'Vigha', 'Guntha'] as $u)
                            <option value="{{ $u }}" {{ old('area_unit', 'Sq.Ft') == $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                @error('total_area') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" id="rateLabel">
                    <i class="fa-solid fa-tag" style="color: #60A5FA;"></i> Purchase Rate per Unit (₹) <span style="color:#94A3B8; font-weight: normal;">(optional)</span>
                </label>
                <input type="number" step="0.01" name="purchase_rate" id="purchase_rate" value="{{ old('purchase_rate') }}" class="form-control" placeholder="e.g. 1500.00" oninput="calculateLandTotalPrice()">
                @error('purchase_rate') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <!-- Total Price Live Calculation Box -->
            <div class="form-group full-width" id="calculatedPriceCard" style="display: none; background: rgba(59, 130, 246, 0.10); border: 1.5px solid rgba(59, 130, 246, 0.35); border-radius: 14px; padding: 14px 18px; margin-top: -5px; margin-bottom: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(59, 130, 246, 0.20); color: #60A5FA; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                        <div>
                            <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; color: #93C5FD; font-weight: 700; display: block;">Calculated Total Price</span>
                            <div style="display: flex; align-items: baseline; gap: 8px; flex-wrap: wrap;">
                                <span id="calculatedPriceText" style="font-size: 20px; font-weight: 800; color: #FFFFFF;">₹ 0.00</span>
                                <span id="calculatedPriceFormula" style="font-size: 12.5px; color: #94A3B8; font-weight: 600;"></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-outline" onclick="applyCalculatedToPurchasePrice()" style="padding: 7px 16px; font-size: 13px; border-radius: 8px; color: #93C5FD; border-color: rgba(59, 130, 246, 0.50); background: rgba(59, 130, 246, 0.20); font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-arrow-down"></i> Auto-Apply to Purchase Price
                    </button>
                </div>
            </div>

            <!-- Unit / Plot Numbers and Quantity Box -->
            <div class="form-group full-width" style="background: rgba(15, 23, 42, 0.65); border: 1.5px solid rgba(245, 158, 11, 0.30); border-radius: 14px; padding: 18px; margin-top: 4px; margin-bottom: 10px;">
                <div style="font-size: 13px; font-weight: 800; color: #F59E0B; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                    <span id="unitSectionHeader"><i class="fa-solid fa-list-ol"></i> Units / Plots Purchased &amp; Numbering</span>
                    <span id="unitSectionSub" style="font-size: 11px; color: #FDE68A; text-transform: none; font-weight: 500;">e.g. 1 to 10, 30, 35 or 1-10, 30, 35</span>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px;">
                    <!-- Purchased Unit Numbers -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" id="unitListLabel" style="color: #FDE68A; font-weight: 700;">
                            <i class="fa-solid fa-hashtag"></i> Unit / Plot Numbers List
                        </label>
                        <input type="text" name="unit_numbers_list" id="unit_numbers_list" value="{{ old('unit_numbers_list') }}" class="form-control" placeholder="e.g. 1-10, 30, 35 or 1 to 10, 30, 35" style="border-color: rgba(245, 158, 11, 0.4);" oninput="handleUnitNumbersInput()">
                        <small style="color: #CBD5E1; font-size: 11.5px; margin-top: 4px; display: block;">Supports ranges like <code>1-10, 30, 35</code> or <code>1 to 10, 30, 35</code></small>
                        @error('unit_numbers_list') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <!-- Total Units Count -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" id="unitCountLabel" style="color: #FDE68A; font-weight: 700;">
                            <i class="fa-solid fa-cubes"></i> Total Units Count
                        </label>
                        <input type="number" min="0" name="total_units_count" id="total_units_count" value="{{ old('total_units_count') }}" class="form-control" placeholder="e.g. 12" style="font-weight: 800; color: #F59E0B; border-color: rgba(245, 158, 11, 0.4);">
                        <small style="color: #CBD5E1; font-size: 11.5px; margin-top: 4px; display: block;">Auto-calculated from list</small>
                        @error('total_units_count') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <!-- Unit Prefix -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #CBD5E1;">Prefix / Label</label>
                        <input type="text" name="unit_prefix" id="unit_prefix" value="{{ old('unit_prefix', 'Plot ') }}" class="form-control" placeholder="e.g. Plot, Flat, Shop">
                        <small style="color: #94A3B8; font-size: 11.5px; margin-top: 4px; display: block;">e.g. Plot 1, Flat 101</small>
                        @error('unit_prefix') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Live Unit Number Preview Tag -->
                <div id="unit_preview_container" style="display: none; margin-top: 14px; padding-top: 12px; border-top: 1px dashed rgba(245, 158, 11, 0.25);">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                        <div id="unit_preview_badges" style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center;"></div>
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #34D399; font-weight: 700; cursor: pointer; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.35); padding: 5px 12px; border-radius: 8px;">
                            <input type="checkbox" name="auto_generate_units" id="auto_generate_units" value="1" checked style="accent-color: #10B981; width: 16px; height: 16px; cursor: pointer;">
                            <span id="autoGenText"><i class="fa-solid fa-wand-magic-sparkles"></i> Auto-create individual plots/units on save</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- ================================================================
                 SECTION 3: PURCHASE PRICE & PAYMENT BREAKDOWN
            ================================================================ -->
            <div class="form-group full-width" style="margin-top: 10px; margin-bottom: 5px;">
                <div style="font-size: 13px; font-weight: 800; color: #34D399; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1px solid rgba(255, 255, 255, 0.10); padding-bottom: 8px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                    <span><i class="fa-solid fa-indian-rupee-sign"></i> Purchase Price &amp; Payment Breakdown</span>
                    <div style="display: flex; gap: 6px; font-size: 11.5px; text-transform: none;">
                        <button type="button" class="btn-outline" onclick="setQuickPayment('full')" style="padding: 4px 10px; font-size: 11.5px; border-radius: 6px; color: #34D399; border-color: rgba(16, 185, 129, 0.4);">
                            <i class="fa-solid fa-check-double"></i> Full Paid (100%)
                        </button>
                        <button type="button" class="btn-outline" onclick="setQuickPayment('half')" style="padding: 4px 10px; font-size: 11.5px; border-radius: 6px; color: #FBBF24; border-color: rgba(245, 158, 11, 0.4);">
                            <i class="fa-solid fa-percent"></i> 50% Advance
                        </button>
                        <button type="button" class="btn-outline" onclick="setQuickPayment('unpaid')" style="padding: 4px 10px; font-size: 11.5px; border-radius: 6px; color: #F87171; border-color: rgba(239, 68, 68, 0.4);">
                            <i class="fa-solid fa-clock"></i> Unpaid (0%)
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group full-width" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(5, 150, 105, 0.03) 100%); border: 1.5px solid rgba(16, 185, 129, 0.30); border-radius: 14px; padding: 18px; margin-bottom: 10px;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                    <!-- 1. Total Purchase Price -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #34D399; font-weight: 700;">
                            <i class="fa-solid fa-money-bill-wave"></i> Total Purchase Price (₹)
                        </label>
                        <div class="input-luxury-group border-emerald">
                            <span class="input-luxury-addon text-emerald">₹</span>
                            <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" placeholder="0.00" oninput="recalculatePayment()">
                        </div>
                        <small style="color: #A7F3D0; font-size: 11.5px; margin-top: 4px; display: block;">Total purchase amount agreed</small>
                        @error('purchase_price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <!-- 2. Paid Amount -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #60A5FA; font-weight: 700;">
                            <i class="fa-solid fa-circle-check"></i> Paid Amount (₹)
                        </label>
                        <div class="input-luxury-group border-blue">
                            <span class="input-luxury-addon text-blue">₹</span>
                            <input type="number" step="0.01" name="paid_amount" id="paid_amount" value="{{ old('paid_amount') }}" placeholder="0.00" oninput="recalculatePayment()">
                        </div>
                        <small style="color: #BFDBFE; font-size: 11.5px; margin-top: 4px; display: block;">Amount paid to vendor/seller so far</small>
                        @error('paid_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <!-- 3. Due Balance -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #FBBF24; font-weight: 700;">
                            <i class="fa-solid fa-hand-holding-dollar"></i> Due Balance (₹)
                        </label>
                        <div class="input-luxury-group border-amber bg-readonly">
                            <span class="input-luxury-addon text-amber">₹</span>
                            <input type="number" step="0.01" name="due_amount" id="due_amount" value="{{ old('due_amount', 0) }}" placeholder="0.00" readonly>
                        </div>
                        <small style="color: #FDE68A; font-size: 11.5px; margin-top: 4px; display: block;">Auto-calculated (Total - Paid)</small>
                        @error('due_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-top: 14px; padding-top: 14px; border-top: 1px dashed rgba(16, 185, 129, 0.25);">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label"><i class="fa-solid fa-calendar-day"></i> Purchase Date</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" class="form-control">
                        @error('purchase_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label"><i class="fa-solid fa-wallet"></i> Payment Status</label>
                        <select name="payment_status" id="payment_status" class="form-control" style="font-weight: 700;">
                            <option value="paid" {{ old('payment_status', 'paid') === 'paid' ? 'selected' : '' }}>Paid (100% Full)</option>
                            <option value="partial" {{ old('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="unpaid" {{ old('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid (0%)</option>
                        </select>
                        @error('payment_status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label"><i class="fa-solid fa-user-tie"></i> Vendor</label>
                        <select name="vendor_id" id="vendor_id" class="form-control">
                            <option value="">— Select Vendor —</option>
                            @if(isset($vendors))
                                @foreach($vendors as $v)
                                    <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>
                                        {{ $v->name }} {{ ($v->mobile ?: $v->phone) ? '(' . ($v->mobile ?: $v->phone) . ')' : '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('vendor_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-top: 14px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label"><i class="fa-solid fa-credit-card"></i> Payment Mode</label>
                        <select name="payment_mode" id="payment_mode" class="form-control">
                            <option value="Cash" {{ old('payment_mode', 'Cash') === 'Cash' ? 'selected' : '' }}>💵 Cash</option>
                            <option value="Cheque" {{ old('payment_mode', 'Cheque') === 'Cheque' ? 'selected' : '' }}>📝 Cheque / Check</option>
                            <option value="Bank Transfer / RTGS / NEFT" {{ old('payment_mode') === 'Bank Transfer / RTGS / NEFT' ? 'selected' : '' }}>🏦 Bank Transfer / RTGS / NEFT</option>
                            <option value="UPI" {{ old('payment_mode') === 'UPI' ? 'selected' : '' }}>📱 UPI (GPay/PhonePe)</option>
                            <option value="Demand Draft" {{ old('payment_mode') === 'Demand Draft' ? 'selected' : '' }}>📜 Demand Draft (DD)</option>
                            <option value="Other" {{ old('payment_mode') === 'Other' ? 'selected' : '' }}>✨ Other</option>
                        </select>
                        @error('payment_mode') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Cheque No. / Ref ID <span style="font-size:11px; opacity:0.7;">(opt)</span></label>
                        <input type="text" name="reference_no" value="{{ old('reference_no') }}" class="form-control" placeholder="e.g. CHQ-481920">
                        @error('reference_no') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Bank Name / Branch <span style="font-size:11px; opacity:0.7;">(opt)</span></label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="form-control" placeholder="e.g. HDFC Bank, Surat">
                        @error('bank_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- ================================================================
                 SECTION 4: BROKER & BROKERAGE / COMMISSION DETAILS
            ================================================================ -->
            <div class="form-group full-width" style="margin-top: 10px; margin-bottom: 5px;">
                <div style="font-size: 13px; font-weight: 800; color: #A78BFA; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1px solid rgba(255, 255, 255, 0.10); padding-bottom: 8px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                    <span><i class="fa-solid fa-user-tie"></i> Broker &amp; Brokerage Details</span>
                    <span style="font-size: 11px; color: #C4B5FD; text-transform: none; font-weight: 500;">Optional — Fill if a Broker was involved in this purchase</span>
                </div>
            </div>

            <div class="form-group full-width" style="background: linear-gradient(135deg, rgba(167, 139, 250, 0.08) 0%, rgba(139, 92, 246, 0.03) 100%); border: 1.5px solid rgba(167, 139, 250, 0.30); border-radius: 14px; padding: 18px; margin-bottom: 10px;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                    <!-- Broker Select -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #DDD6FE; font-weight: 700;">
                            <i class="fa-solid fa-address-book"></i> Select Registered Broker
                        </label>
                        <select name="broker_id" id="pm_broker_id" class="form-control" onchange="handleBrokerSelection(this)">
                            <option value="">— Direct / No Registered Broker —</option>
                            @if(isset($brokers))
                                @foreach($brokers as $b)
                                    <option value="{{ $b->id }}" 
                                            data-name="{{ $b->name }}"
                                            data-phone="{{ $b->phone ?: $b->mobile }}"
                                            data-commission="{{ $b->commission_percentage ?? 0 }}"
                                            {{ old('broker_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->name }} {{ ($b->phone ?: $b->mobile) ? '(' . ($b->phone ?: $b->mobile) . ')' : '' }} {{ $b->commission_percentage ? '— ' . $b->commission_percentage . '%' : '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('broker_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <!-- Manual Broker Name -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #DDD6FE;">Broker / Agent Name</label>
                        <input type="text" name="broker_name" id="pm_broker_name" value="{{ old('broker_name') }}" class="form-control" placeholder="e.g. Ramesh Patel">
                        @error('broker_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <!-- Broker Notes / Contact -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #DDD6FE;">Broker Notes / Terms</label>
                        <input type="text" name="broker_notes" value="{{ old('broker_notes') }}" class="form-control" placeholder="e.g. 1% commission on registry">
                        @error('broker_notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Brokerage Amount & Calculation -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 14px; padding-top: 14px; border-top: 1px dashed rgba(167, 139, 250, 0.25);">
                    <!-- Commission Type & Rate -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #DDD6FE; font-weight: 700;">
                            <i class="fa-solid fa-percent"></i> Commission Type &amp; Rate
                        </label>
                        <div style="display: grid; grid-template-columns: 105px 1fr; gap: 8px;">
                            <select name="broker_commission_type" id="pm_broker_commission_type" class="form-control" onchange="recalculateBrokerage()">
                                <option value="percentage" {{ old('broker_commission_type', 'percentage') === 'percentage' ? 'selected' : '' }}>% Rate</option>
                                <option value="fixed" {{ old('broker_commission_type') === 'fixed' ? 'selected' : '' }}>Fixed ₹</option>
                            </select>
                            <div class="input-luxury-group border-purple">
                                <input type="number" step="0.01" name="broker_commission_rate" id="pm_broker_commission_rate" value="{{ old('broker_commission_rate') }}" placeholder="Rate" oninput="recalculateBrokerage()">
                                <span class="input-luxury-addon text-purple" id="commRateSuffix" style="border-right: none; border-left: 1px solid rgba(255,255,255,0.10);">%</span>
                            </div>
                        </div>
                        <small style="color: #C4B5FD; font-size: 11.5px; margin-top: 4px; display: block;">e.g. 1% or 2% of purchase price</small>
                        @error('broker_commission_rate') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <!-- Total Brokerage Amount -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #A78BFA; font-weight: 700;">
                            <i class="fa-solid fa-coins"></i> Total Brokerage Amount (₹)
                        </label>
                        <div class="input-luxury-group border-purple">
                            <span class="input-luxury-addon text-purple">₹</span>
                            <input type="number" step="0.01" name="broker_commission_amount" id="pm_broker_commission_amount" value="{{ old('broker_commission_amount') }}" placeholder="0.00" oninput="recalculateBrokerageFromAmount()">
                        </div>
                        <small style="color: #C4B5FD; font-size: 11.5px; margin-top: 4px; display: block;">Total commission to be paid</small>
                        @error('broker_commission_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <!-- Brokerage Paid Amount -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #34D399; font-weight: 700;">
                            <i class="fa-solid fa-circle-check"></i> Brokerage Paid (₹)
                        </label>
                        <div class="input-luxury-group border-emerald">
                            <span class="input-luxury-addon text-emerald">₹</span>
                            <input type="number" step="0.01" name="broker_commission_paid" id="pm_broker_commission_paid" value="{{ old('broker_commission_paid', 0) }}" placeholder="0.00" oninput="recalculateBrokerageDue()">
                        </div>
                        <small style="color: #A7F3D0; font-size: 11.5px; margin-top: 4px; display: block;">Amount given to broker</small>
                        @error('broker_commission_paid') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Brokerage Balance & Mode -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 14px;">
                    <!-- Brokerage Due Balance -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #FBBF24; font-weight: 700;">
                            <i class="fa-solid fa-hand-holding-dollar"></i> Brokerage Due (₹)
                        </label>
                        <div class="input-luxury-group border-amber bg-readonly">
                            <span class="input-luxury-addon text-amber">₹</span>
                            <input type="number" step="0.01" name="broker_commission_due" id="pm_broker_commission_due" value="{{ old('broker_commission_due', 0) }}" placeholder="0.00" readonly>
                        </div>
                        <small style="color: #FDE68A; font-size: 11.5px; margin-top: 4px; display: block;">Auto-calculated (Total - Paid)</small>
                        @error('broker_commission_due') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <!-- Brokerage Payment Mode -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #DDD6FE;">Broker Payment Mode</label>
                        <select name="broker_commission_payment_mode" class="form-control">
                            <option value="">— Select Mode —</option>
                            <option value="Cash" {{ old('broker_commission_payment_mode') === 'Cash' ? 'selected' : '' }}>💵 Cash</option>
                            <option value="Cheque" {{ old('broker_commission_payment_mode') === 'Cheque' ? 'selected' : '' }}>📝 Cheque</option>
                            <option value="Bank Transfer / RTGS" {{ old('broker_commission_payment_mode') === 'Bank Transfer / RTGS' ? 'selected' : '' }}>🏦 Bank Transfer / RTGS</option>
                            <option value="UPI" {{ old('broker_commission_payment_mode') === 'UPI' ? 'selected' : '' }}>📱 UPI (GPay/PhonePe)</option>
                            <option value="Other" {{ old('broker_commission_payment_mode') === 'Other' ? 'selected' : '' }}>✨ Other</option>
                        </select>
                        @error('broker_commission_payment_mode') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <!-- Broker Payment Status -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #DDD6FE;">Broker Commission Status</label>
                        <select name="broker_commission_status" id="pm_broker_commission_status" class="form-control" style="font-weight: 700;">
                            <option value="unpaid" {{ old('broker_commission_status', 'unpaid') === 'unpaid' ? 'selected' : '' }}>Unpaid (0%)</option>
                            <option value="partial" {{ old('broker_commission_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="paid" {{ old('broker_commission_status') === 'paid' ? 'selected' : '' }}>Paid (100% Full)</option>
                        </select>
                        @error('broker_commission_status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- ================================================================
                 SECTION 5: LOCATION & ADDRESS DETAILS
            ================================================================ -->
            <div class="form-group full-width" style="margin-top: 10px; margin-bottom: 5px;">
                <div style="font-size: 13px; font-weight: 800; color: #60A5FA; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1px solid rgba(255, 255, 255, 0.10); padding-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-map-location-dot"></i> Location &amp; Address Details
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Location / Landmark</label>
                <input type="text" name="location" value="{{ old('location') }}" class="form-control" placeholder="e.g. Zadeshwar Road">
                @error('location') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">City</label>
                <input type="text" name="city" value="{{ old('city') }}" class="form-control" placeholder="e.g. Bharuch">
                @error('city') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">State</label>
                <input type="text" name="state" value="{{ old('state', 'Gujarat') }}" class="form-control" placeholder="e.g. Gujarat">
                @error('state') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Country</label>
                <input type="text" name="country" value="{{ old('country', 'India') }}" class="form-control">
                @error('country') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Pincode</label>
                <input type="text" name="pincode" value="{{ old('pincode') }}" class="form-control">
                @error('pincode') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Full property address">{{ old('address') }}</textarea>
                @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Description / Notes</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Property details or notes">{{ old('description') }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Main Image</label>
                <input type="file" name="main_image" class="form-control" accept="image/*">
                @error('main_image') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Document File</label>
                <input type="file" name="document_file" class="form-control">
                @error('document_file') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: flex-end;">
            <a href="{{ route('property-masters.index') }}" class="btn-outline" style="padding: 10px 20px;">Cancel</a>
            <button type="submit" class="btn-gold">Create Property Master</button>
        </div>
    </form>
</div>

<script>
function handlePropertyTypeChange(type) {
    const areaLabel = document.getElementById('areaLabel');
    const rateLabel = document.getElementById('rateLabel');
    const areaInput = document.getElementById('total_area');
    const rateInput = document.getElementById('purchase_rate');
    const unitSelect = document.getElementById('area_unit');
    const title = document.getElementById('measurementSectionTitle');

    // Section 2 Dynamic Elements
    const unitSecHeader = document.getElementById('unitSectionHeader');
    const unitSecSub = document.getElementById('unitSectionSub');
    const unitListLabel = document.getElementById('unitListLabel');
    const unitCountLabel = document.getElementById('unitCountLabel');
    const unitPrefixInput = document.getElementById('unit_prefix');
    const unitListInput = document.getElementById('unit_numbers_list');
    const autoGenText = document.getElementById('autoGenText');

    const t = (type || '').toLowerCase();

    if (t === 'land') {
        if (title) title.innerText = 'Land Area & Purchase Rate Details';
        if (areaLabel) areaLabel.innerHTML = '<i class="fa-solid fa-mountain-sun" style="color: #34D399;"></i> Total Land Area <span style="color:#94A3B8; font-weight:normal;">(optional)</span>';
        if (rateLabel) rateLabel.innerHTML = '<i class="fa-solid fa-tag" style="color: #60A5FA;"></i> Purchase Rate per Unit (₹) <span style="color:#94A3B8; font-weight:normal;">(optional)</span>';
        if (areaInput) areaInput.placeholder = 'e.g. 50000 (Sq.Ft / Vigha / Acre)';
        if (rateInput) rateInput.placeholder = 'e.g. 1500.00';

        if (unitSecHeader) unitSecHeader.innerHTML = '<i class="fa-solid fa-list-ol"></i> Land Survey / Sub-Plot Numbers';
        if (unitSecSub) unitSecSub.innerText = 'e.g. 101/1 to 101/10, 105, 110';
        if (unitListLabel) unitListLabel.innerHTML = '<i class="fa-solid fa-hashtag"></i> Survey / Sub-Plot Numbers List';
        if (unitCountLabel) unitCountLabel.innerHTML = '<i class="fa-solid fa-cubes"></i> Total Plots / Parcels Count';
        if (unitListInput && !unitListInput.value) unitListInput.placeholder = 'e.g. 1-10, 30, 35 or 1 to 10, 30, 35';
        if (unitPrefixInput && (!unitPrefixInput.value || ['Plot ', 'Flat ', 'Shop ', 'House '].includes(unitPrefixInput.value))) unitPrefixInput.value = 'Plot ';
        if (autoGenText) autoGenText.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Auto-create individual land plots on save';

    } else if (t === 'plot') {
        if (title) title.innerText = 'Plot Size & Purchase Rate Details';
        if (areaLabel) areaLabel.innerHTML = '<i class="fa-solid fa-map" style="color: #34D399;"></i> Total Plot Area / Size <span style="color:#94A3B8; font-weight:normal;">(optional)</span>';
        if (rateLabel) rateLabel.innerHTML = '<i class="fa-solid fa-tag" style="color: #60A5FA;"></i> Purchase Rate per Sq.Ft / Unit (₹) <span style="color:#94A3B8; font-weight:normal;">(optional)</span>';
        if (areaInput) areaInput.placeholder = 'e.g. 2500';
        if (rateInput) rateInput.placeholder = 'e.g. 2200.00';

        if (unitSecHeader) unitSecHeader.innerHTML = '<i class="fa-solid fa-list-ol"></i> Plots Purchased &amp; Numbering';
        if (unitSecSub) unitSecSub.innerText = 'e.g. 1 to 10, 30, 35 or 1-10, 30, 35';
        if (unitListLabel) unitListLabel.innerHTML = '<i class="fa-solid fa-hashtag"></i> Plot Numbers List';
        if (unitCountLabel) unitCountLabel.innerHTML = '<i class="fa-solid fa-cubes"></i> Total Plots Count';
        if (unitListInput && !unitListInput.value) unitListInput.placeholder = 'e.g. 1-10, 30, 35 or 1 to 10, 30, 35';
        if (unitPrefixInput && (!unitPrefixInput.value || ['Plot ', 'Flat ', 'Shop ', 'House '].includes(unitPrefixInput.value))) unitPrefixInput.value = 'Plot ';
        if (autoGenText) autoGenText.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Auto-create individual plots on save';

    } else if (t === 'flat' || t === 'apartment') {
        if (title) title.innerText = 'Flat Built-up Area & Purchase Rate';
        if (areaLabel) areaLabel.innerHTML = '<i class="fa-solid fa-building" style="color: #34D399;"></i> Total Flat / Built-up Area <span style="color:#94A3B8; font-weight:normal;">(optional)</span>';
        if (rateLabel) rateLabel.innerHTML = '<i class="fa-solid fa-tag" style="color: #60A5FA;"></i> Rate per Sq.Ft (₹) <span style="color:#94A3B8; font-weight:normal;">(optional)</span>';
        if (areaInput) areaInput.placeholder = 'e.g. 1450';
        if (rateInput) rateInput.placeholder = 'e.g. 4500.00';

        if (unitSecHeader) unitSecHeader.innerHTML = '<i class="fa-solid fa-building"></i> Flats / Apartments Purchased &amp; Numbering';
        if (unitSecSub) unitSecSub.innerText = 'e.g. 101 to 108, 201-208, 305';
        if (unitListLabel) unitListLabel.innerHTML = '<i class="fa-solid fa-hashtag"></i> Flat / Unit Numbers List';
        if (unitCountLabel) unitCountLabel.innerHTML = '<i class="fa-solid fa-cubes"></i> Total Flats Count';
        if (unitListInput && !unitListInput.value) unitListInput.placeholder = 'e.g. 101-108, 201-208, 305';
        if (unitPrefixInput && (!unitPrefixInput.value || ['Plot ', 'Flat ', 'Shop ', 'House '].includes(unitPrefixInput.value))) unitPrefixInput.value = 'Flat ';
        if (autoGenText) autoGenText.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Auto-create individual flats/units on save';

    } else if (t === 'house' || t === 'villa' || t.includes('row') || t.includes('tenement') || t.includes('bungalow')) {
        if (title) title.innerText = 'House / Construction Area & Purchase Rate';
        if (areaLabel) areaLabel.innerHTML = '<i class="fa-solid fa-house-chimney" style="color: #34D399;"></i> Total Construction / Plot Area <span style="color:#94A3B8; font-weight:normal;">(optional)</span>';
        if (rateLabel) rateLabel.innerHTML = '<i class="fa-solid fa-tag" style="color: #60A5FA;"></i> Purchase Rate / Price per Unit (₹) <span style="color:#94A3B8; font-weight:normal;">(optional)</span>';
        if (areaInput) areaInput.placeholder = 'e.g. 1800';
        if (rateInput) rateInput.placeholder = 'e.g. 3500.00';

        const labelName = t.includes('villa') ? 'Villa' : (t.includes('bungalow') ? 'Bungalow' : (t.includes('row') ? 'Row House' : 'House'));
        if (unitSecHeader) unitSecHeader.innerHTML = `<i class="fa-solid fa-house-chimney"></i> ${labelName}s Purchased &amp; Numbering`;
        if (unitSecSub) unitSecSub.innerText = 'e.g. 1 to 12, 15, 20';
        if (unitListLabel) unitListLabel.innerHTML = `<i class="fa-solid fa-hashtag"></i> ${labelName} Numbers List`;
        if (unitCountLabel) unitCountLabel.innerHTML = `<i class="fa-solid fa-cubes"></i> Total ${labelName}s Count`;
        if (unitListInput && !unitListInput.value) unitListInput.placeholder = 'e.g. 1-12, 15, 20';
        if (unitPrefixInput && (!unitPrefixInput.value || ['Plot ', 'Flat ', 'Shop ', 'House ', 'Villa ', 'Row House '].includes(unitPrefixInput.value))) unitPrefixInput.value = labelName + ' ';
        if (autoGenText) autoGenText.innerHTML = `<i class="fa-solid fa-wand-magic-sparkles"></i> Auto-create individual ${labelName.toLowerCase()} units on save`;

    } else if (t === 'commercial' || t === 'industrial' || t === 'shop' || t === 'office') {
        if (title) title.innerText = 'Commercial / Shed Area & Rate Details';
        if (areaLabel) areaLabel.innerHTML = '<i class="fa-solid fa-store" style="color: #34D399;"></i> Total Carpet / Built-up Area <span style="color:#94A3B8; font-weight:normal;">(optional)</span>';
        if (rateLabel) rateLabel.innerHTML = '<i class="fa-solid fa-tag" style="color: #60A5FA;"></i> Purchase Rate per Sq.Ft (₹) <span style="color:#94A3B8; font-weight:normal;">(optional)</span>';
        if (areaInput) areaInput.placeholder = 'e.g. 850';
        if (rateInput) rateInput.placeholder = 'e.g. 6500.00';

        const labelName = t.includes('shop') ? 'Shop' : (t.includes('office') ? 'Office' : 'Commercial Unit');
        if (unitSecHeader) unitSecHeader.innerHTML = `<i class="fa-solid fa-store"></i> ${labelName}s Purchased &amp; Numbering`;
        if (unitSecSub) unitSecSub.innerText = 'e.g. 1 to 15, 101-105';
        if (unitListLabel) unitListLabel.innerHTML = `<i class="fa-solid fa-hashtag"></i> ${labelName} Numbers List`;
        if (unitCountLabel) unitCountLabel.innerHTML = `<i class="fa-solid fa-cubes"></i> Total ${labelName}s Count`;
        if (unitListInput && !unitListInput.value) unitListInput.placeholder = 'e.g. 1-15, 101-105';
        if (unitPrefixInput && (!unitPrefixInput.value || ['Plot ', 'Flat ', 'Shop ', 'House ', 'Office ', 'Commercial Unit '].includes(unitPrefixInput.value))) unitPrefixInput.value = labelName + ' ';
        if (autoGenText) autoGenText.innerHTML = `<i class="fa-solid fa-wand-magic-sparkles"></i> Auto-create individual ${labelName.toLowerCase()}s on save`;

    } else if (t === 'farmhouse') {
        if (title) title.innerText = 'Farmhouse Land & Rate Details';
        if (areaLabel) areaLabel.innerHTML = '<i class="fa-solid fa-wheat-awn" style="color: #34D399;"></i> Total Farm Land Area <span style="color:#94A3B8; font-weight:normal;">(optional)</span>';
        if (rateLabel) rateLabel.innerHTML = '<i class="fa-solid fa-tag" style="color: #60A5FA;"></i> Rate per Unit / Acre (₹) <span style="color:#94A3B8; font-weight:normal;">(optional)</span>';
        if (areaInput) areaInput.placeholder = 'e.g. 2 (Acre / Vigha)';
        if (rateInput) rateInput.placeholder = 'e.g. 2500000.00';

        if (unitSecHeader) unitSecHeader.innerHTML = '<i class="fa-solid fa-wheat-awn"></i> Farmhouse Plots / Parcels Purchased &amp; Numbering';
        if (unitSecSub) unitSecSub.innerText = 'e.g. 1 to 5, 8, 12';
        if (unitListLabel) unitListLabel.innerHTML = '<i class="fa-solid fa-hashtag"></i> Farmhouse Plot Numbers List';
        if (unitCountLabel) unitCountLabel.innerHTML = '<i class="fa-solid fa-cubes"></i> Total Farmhouse Plots Count';
        if (unitListInput && !unitListInput.value) unitListInput.placeholder = 'e.g. 1-5, 8, 12';
        if (unitPrefixInput && (!unitPrefixInput.value || ['Plot ', 'Flat ', 'Shop ', 'House ', 'Farm '].includes(unitPrefixInput.value))) unitPrefixInput.value = 'Plot ';
        if (autoGenText) autoGenText.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Auto-create individual farmhouse plots on save';

    } else {
        if (title) title.innerText = 'Property Size / Area & Rate Details';
        if (areaLabel) areaLabel.innerHTML = '<i class="fa-solid fa-ruler-combined" style="color: #34D399;"></i> Total Area / Size <span style="color:#94A3B8; font-weight:normal;">(optional)</span>';
        if (rateLabel) rateLabel.innerHTML = '<i class="fa-solid fa-tag" style="color: #60A5FA;"></i> Purchase Rate per Unit (₹) <span style="color:#94A3B8; font-weight:normal;">(optional)</span>';
        if (areaInput) areaInput.placeholder = 'e.g. 150000';
        if (rateInput) rateInput.placeholder = 'e.g. 1500.00';

        if (unitSecHeader) unitSecHeader.innerHTML = '<i class="fa-solid fa-list-ol"></i> Units / Plots Purchased &amp; Numbering';
        if (unitSecSub) unitSecSub.innerText = 'e.g. 1 to 10, 30, 35 or 1-10, 30, 35';
        if (unitListLabel) unitListLabel.innerHTML = '<i class="fa-solid fa-hashtag"></i> Unit / Plot Numbers List';
        if (unitCountLabel) unitCountLabel.innerHTML = '<i class="fa-solid fa-cubes"></i> Total Units Count';
        if (unitListInput && !unitListInput.value) unitListInput.placeholder = 'e.g. 1-10, 30, 35 or 1 to 10, 30, 35';
        if (unitPrefixInput && (!unitPrefixInput.value || ['Plot ', 'Flat ', 'Shop ', 'House '].includes(unitPrefixInput.value))) unitPrefixInput.value = 'Plot ';
        if (autoGenText) autoGenText.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Auto-create individual units on save';
    }

    handleUnitNumbersInput();
}

function calculateLandTotalPrice(autoFillIfEmpty = true) {
    const areaInput = document.getElementById('total_area');
    const rateInput = document.getElementById('purchase_rate');
    const unitSelect = document.getElementById('area_unit');
    const card = document.getElementById('calculatedPriceCard');
    const textSpan = document.getElementById('calculatedPriceText');
    const formulaSpan = document.getElementById('calculatedPriceFormula');
    const priceInput = document.getElementById('purchase_price');

    if (!areaInput || !rateInput) return;

    const area = parseFloat(areaInput.value) || 0;
    const rate = parseFloat(rateInput.value) || 0;
    const unit = unitSelect ? unitSelect.value : 'Sq.Ft';

    if (area > 0 && rate > 0) {
        const total = area * rate;
        if (card) card.style.display = 'block';
        if (textSpan) textSpan.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (formulaSpan) formulaSpan.textContent = `(${area.toLocaleString('en-IN')} ${unit} × ₹${rate.toLocaleString('en-IN')})`;

        // Auto-fill purchase price
        if (autoFillIfEmpty && priceInput) {
            priceInput.value = total.toFixed(2);
            recalculatePayment();
        }
    } else {
        if (card) card.style.display = 'none';
    }
}

function applyCalculatedToPurchasePrice() {
    const areaInput = document.getElementById('total_area');
    const rateInput = document.getElementById('purchase_rate');
    const priceInput = document.getElementById('purchase_price');

    const area = parseFloat(areaInput.value) || 0;
    const rate = parseFloat(rateInput.value) || 0;

    if (area > 0 && rate > 0 && priceInput) {
        const total = area * rate;
        priceInput.value = total.toFixed(2);
        recalculatePayment();
    }
}

function recalculatePayment() {
    const priceInput = document.getElementById('purchase_price');
    const paidInput  = document.getElementById('paid_amount');
    const dueInput   = document.getElementById('due_amount');
    const statusSelect = document.getElementById('payment_status');

    if (!priceInput || !paidInput || !dueInput) return;

    const price = parseFloat(priceInput.value) || 0;
    const paid  = parseFloat(paidInput.value) || 0;
    const due   = Math.max(0, price - paid);

    dueInput.value = due.toFixed(2);

    if (statusSelect) {
        if (price > 0) {
            if (paid >= price) {
                statusSelect.value = 'paid';
            } else if (paid > 0) {
                statusSelect.value = 'partial';
            } else {
                statusSelect.value = 'unpaid';
            }
        }
    }

    // Also update brokerage if based on %
    recalculateBrokerage();
}

function handleBrokerSelection(select) {
    if (!select) return;
    const opt = select.options[select.selectedIndex];
    const nameInput = document.getElementById('pm_broker_name');
    const rateInput = document.getElementById('pm_broker_commission_rate');
    const typeSelect = document.getElementById('pm_broker_commission_type');

    if (select.value && opt) {
        const name = opt.getAttribute('data-name');
        const comm = opt.getAttribute('data-commission');
        if (name && nameInput && !nameInput.value) {
            nameInput.value = name;
        }
        if (comm && parseFloat(comm) > 0 && rateInput) {
            rateInput.value = comm;
            if (typeSelect) typeSelect.value = 'percentage';
        }
    }
    recalculateBrokerage();
}

function recalculateBrokerage() {
    const priceInput = document.getElementById('purchase_price');
    const typeSelect = document.getElementById('pm_broker_commission_type');
    const rateInput = document.getElementById('pm_broker_commission_rate');
    const amountInput = document.getElementById('pm_broker_commission_amount');
    const suffix = document.getElementById('commRateSuffix');

    if (!typeSelect || !rateInput || !amountInput) return;

    const price = parseFloat(priceInput ? priceInput.value : 0) || 0;
    const type = typeSelect.value;
    const rate = parseFloat(rateInput.value) || 0;

    if (suffix) {
        suffix.innerText = (type === 'percentage') ? '%' : '₹';
    }

    if (type === 'percentage') {
        if (price > 0 && rate > 0) {
            const calculated = (price * rate) / 100;
            amountInput.value = calculated.toFixed(2);
        }
    } else if (type === 'fixed') {
        if (rate > 0) {
            amountInput.value = rate.toFixed(2);
        }
    }

    recalculateBrokerageDue();
}

function recalculateBrokerageFromAmount() {
    recalculateBrokerageDue();
}

function recalculateBrokerageDue() {
    const amountInput = document.getElementById('pm_broker_commission_amount');
    const paidInput   = document.getElementById('pm_broker_commission_paid');
    const dueInput    = document.getElementById('pm_broker_commission_due');
    const statusSelect = document.getElementById('pm_broker_commission_status');

    if (!amountInput || !paidInput || !dueInput) return;

    const amount = parseFloat(amountInput.value) || 0;
    const paid   = parseFloat(paidInput.value) || 0;
    const due    = Math.max(0, amount - paid);

    dueInput.value = due.toFixed(2);

    if (statusSelect) {
        if (amount > 0) {
            if (paid >= amount) {
                statusSelect.value = 'paid';
            } else if (paid > 0) {
                statusSelect.value = 'partial';
            } else {
                statusSelect.value = 'unpaid';
            }
        }
    }
}

function setQuickPayment(type) {
    const priceInput = document.getElementById('purchase_price');
    const paidInput  = document.getElementById('paid_amount');
    const price = parseFloat(priceInput.value) || 0;

    if (!paidInput) return;

    if (type === 'full') {
        paidInput.value = price > 0 ? price.toFixed(2) : '';
    } else if (type === 'half') {
        paidInput.value = price > 0 ? (price / 2).toFixed(2) : '';
    } else if (type === 'unpaid') {
        paidInput.value = (0).toFixed(2);
    }
    recalculatePayment();
}

function parseRangeString(input) {
    if (!input || !input.trim()) return [];
    const segments = input.split(/[,;\n\r]+/);
    const units = [];

    segments.forEach(segment => {
        segment = segment.trim();
        if (!segment) return;

        // Matches: "1 to 10", "1-10", "1..10"
        const m = segment.match(/^(.*?)\s*(\d+)\s*(?:-|to|\.\.)\s*(?:.*?)(\d+)$/i);
        if (m) {
            const prefix = m[1] ? m[1].trim() + ' ' : '';
            const start = parseInt(m[2], 10);
            const end = parseInt(m[3], 10);
            if (!isNaN(start) && !isNaN(end) && Math.abs(end - start) <= 500) {
                const step = start <= end ? 1 : -1;
                for (let i = start; start <= end ? i <= end : i >= end; i += step) {
                    units.push(prefix + i);
                }
            } else {
                units.push(segment);
            }
        } else {
            units.push(segment);
        }
    });

    return [...new Set(units.filter(u => u.trim() !== ''))];
}

function handleUnitNumbersInput() {
    const input = document.getElementById('unit_numbers_list');
    const countInput = document.getElementById('total_units_count');
    const prefixInput = document.getElementById('unit_prefix');
    const container = document.getElementById('unit_preview_container');
    const badgeBox = document.getElementById('unit_preview_badges');

    if (!input) return;

    const parsed = parseRangeString(input.value);
    const prefix = prefixInput ? prefixInput.value.trim() : 'Plot';

    if (countInput && parsed.length > 0) {
        countInput.value = parsed.length;
    }

    if (parsed.length > 0) {
        if (container) container.style.display = 'block';
        if (badgeBox) {
            let html = `<span style="font-size: 12px; color: #F59E0B; font-weight: 800; margin-right: 6px;"><i class="fa-solid fa-layer-group"></i> ${parsed.length} Unit(s):</span>`;
            const previewLimit = 15;
            parsed.slice(0, previewLimit).forEach(u => {
                html += `<span style="background: rgba(245, 158, 11, 0.18); color: #FDE68A; border: 1px solid rgba(245, 158, 11, 0.35); padding: 2px 7px; border-radius: 6px; font-size: 11.5px; font-weight: 700;">${prefix} ${u}</span>`;
            });
            if (parsed.length > previewLimit) {
                html += `<span style="color: #94A3B8; font-size: 11px; font-weight: 600;">+${parsed.length - previewLimit} more</span>`;
            }
            badgeBox.innerHTML = html;
        }
    } else {
        if (container) container.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('property_type_select');
    if (sel && sel.value) {
        handlePropertyTypeChange(sel.value);
    }
    recalculatePayment();
    recalculateBrokerageDue();
    handleUnitNumbersInput();
    calculateLandTotalPrice(false);
});
</script>
@endsection
