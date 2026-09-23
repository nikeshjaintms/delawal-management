@extends('admin.layouts.app')
@section('title', 'Add Expense')
@section('page-title', 'Expense Management')
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
    max-width: 950px; margin-left: auto; margin-right: auto;
}

.section-title {
    font-size: 12px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 18px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.form-section { margin-bottom: 28px; }
.form-group { margin-bottom: 18px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; }
@media(max-width:768px){ .form-row-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .form-row, .form-row-3 { grid-template-columns: 1fr; gap: 0; } }

.form-label { display: block; font-size: 13px; font-weight: 700; color: #CBD5E1 !important; margin-bottom: 8px; }
.form-label span.req { color: #F87171 !important; }
.form-label .opt { color: #94A3B8 !important; font-weight: 400; font-size: 12px; }

.form-control {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 14px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important;
}
select.form-control option { background: #101622 !important; color: #FFFFFF !important; }
.form-control::placeholder { color: #94A3B8 !important; }
.form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }
textarea.form-control { resize: vertical; min-height: 80px; }

/* File Upload Glass Dropzone */
.file-upload-box {
    border: 2px dashed rgba(255, 255, 255, 0.20) !important;
    border-radius: 16px !important; padding: 24px !important; text-align: center;
    transition: all .25s ease; cursor: pointer;
    background: rgba(16, 22, 34, 0.50) !important;
}
.file-upload-box:hover {
    border-color: #3B82F6 !important;
    background: rgba(37, 99, 235, 0.12) !important;
}
.file-upload-box input[type="file"] { display: none; }
.file-upload-label { display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer; margin: 0; }
.file-upload-label i { font-size: 28px; color: #60A5FA !important; }
.file-upload-label .upload-text { font-size: 14px; font-weight: 700; color: #FFFFFF !important; }
.file-upload-label .upload-hint { font-size: 12px; color: #94A3B8 !important; }
#file-name-display { margin-top: 8px; font-size: 13px; color: #94A3B8 !important; }

.text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 600; }
.form-hint { font-size: 12px; color: #CBD5E1 !important; margin-top: 5px; }

.form-actions { display: flex; align-items: center; gap: 14px; margin-top: 28px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 12px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all .25s ease;
    box-shadow: 0 4px 18px rgba(37,99,235,0.38); font-family: inherit;
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37,99,235,0.52); }

.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 22px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-2px); }


.btn-quick-add-cat {
    background: rgba(37, 99, 235, 0.20) !important;
    border: 1.5px solid rgba(59, 130, 246, 0.50) !important;
    color: #60A5FA !important; padding: 10px 16px; border-radius: 10px;
    font-size: 13.5px; font-weight: 700; cursor: pointer; display: inline-flex;
    align-items: center; gap: 6px; white-space: nowrap; transition: all .2s ease;
    font-family: inherit; flex-shrink: 0;
}
.btn-quick-add-cat:hover {
    background: #2563EB !important; color: #FFFFFF !important;
    border-color: #3B82F6 !important; transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.40);
}

/* ── 4 Distinct Expense Type Selector Bar ── */
.expense-type-bar {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}
@media (max-width: 768px) {
    .expense-type-bar { grid-template-columns: repeat(2, 1fr); }
}
.type-btn {
    padding: 13px 16px;
    border-radius: 14px;
    background: rgba(16, 22, 34, 0.65);
    border: 1.5px solid rgba(255, 255, 255, 0.12);
    color: #94A3B8;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all .25s ease;
    font-family: inherit;
}
.type-btn:hover {
    color: #FFFFFF;
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.25);
}
.type-btn.active {
    background: linear-gradient(135deg, #2563EB, #1D4ED8) !important;
    border-color: #3B82F6 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 18px rgba(37, 99, 235, 0.40);
}

/* Category Quick Modal */
.cat-modal-backdrop {
    position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
    z-index: 99999; display: flex; align-items: center; justify-content: center;
    padding: 20px; box-sizing: border-box;
}
.cat-modal-box {
    background: #141B29 !important;
    border: 1px solid rgba(255, 255, 255, 0.16) !important;
    border-radius: 20px !important;
    width: 100%; max-width: 460px;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.60);
    overflow: hidden; animation: catModalSlideIn .25s ease-out;
}
@keyframes catModalSlideIn {
    from { transform: translateY(20px) scale(0.96); opacity: 0; }
    to { transform: translateY(0) scale(1); opacity: 1; }
}
.cat-modal-header {
    padding: 20px 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex; justify-content: space-between; align-items: center;
}
.cat-modal-icon {
    width: 36px; height: 36px; border-radius: 10px;
    background: rgba(37, 99, 235, 0.20); color: #60A5FA;
    display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;
}
.cat-modal-close {
    background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15);
    color: #94A3B8; width: 32px; height: 32px; border-radius: 8px; cursor: pointer;
    display: flex; align-items: center; justify-content: center; transition: all .2s;
}
.cat-modal-close:hover { background: rgba(239, 68, 68, 0.20); color: #F87171; border-color: rgba(239, 68, 68, 0.40); }
.cat-modal-body { padding: 24px; }
.cat-modal-footer {
    padding: 16px 24px; border-top: 1px solid rgba(255, 255, 255, 0.10);
    display: flex; justify-content: flex-end; gap: 10px; background: rgba(0, 0, 0, 0.20);
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Expense</h2>
        <p>Record a new expense voucher under the selected expense type.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" id="expense-form">
        @csrf

        {{-- Top Selector: 4 Distinct Expense Types --}}
        @php
            $currType = old('expense_type', $selectedType ?? 'Property');
        @endphp
        <input type="hidden" name="expense_type" id="expense_type_input" value="{{ $currType }}">

        <div class="expense-type-bar">
            <button type="button" class="type-btn {{ $currType === 'Property' ? 'active' : '' }}" onclick="selectExpenseType('Property')">
                <i class="fa-solid fa-building"></i> <span>Property Expense</span>
            </button>
            <button type="button" class="type-btn {{ $currType === 'General' ? 'active' : '' }}" onclick="selectExpenseType('General')">
                <i class="fa-solid fa-briefcase"></i> <span>General Expense</span>
            </button>
            <button type="button" class="type-btn {{ $currType === 'Rental' ? 'active' : '' }}" onclick="selectExpenseType('Rental')">
                <i class="fa-solid fa-house-user"></i> <span>Rental Expense</span>
            </button>
            <button type="button" class="type-btn {{ $currType === 'Personal' ? 'active' : '' }}" onclick="selectExpenseType('Personal')">
                <i class="fa-solid fa-user"></i> <span>Personal Expense</span>
            </button>
        </div>

        {{-- Category Quick Chips for Rental --}}
        <div id="rental-category-chips" style="{{ $currType === 'Rental' ? '' : 'display: none;' }} margin-bottom: 22px;">
            <label class="form-label" style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                <i class="fa-solid fa-bolt text-warning" style="color: #F59E0B !important;"></i>
                <span>Quick Rental Expense Categories:</span>
                <span class="opt">(Click to select)</span>
            </label>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @php
                    $rentalChips = [
                        ['cat' => 'Property Maintenance', 'icon' => 'fa-screwdriver-wrench'],
                        ['cat' => 'Repairs & Renovation', 'icon' => 'fa-hammer'],
                        ['cat' => 'Electricity Bill', 'icon' => 'fa-bolt'],
                        ['cat' => 'Water Bill', 'icon' => 'fa-droplet'],
                        ['cat' => 'Society Maintenance', 'icon' => 'fa-building'],
                        ['cat' => 'Property Tax', 'icon' => 'fa-landmark'],
                        ['cat' => 'Plumbing Work', 'icon' => 'fa-faucet-drip'],
                        ['cat' => 'Painting & Whitewash', 'icon' => 'fa-paint-roller'],
                        ['cat' => 'Carpentry Work', 'icon' => 'fa-ruler-combined'],
                        ['cat' => 'Deep Cleaning', 'icon' => 'fa-broom'],
                        ['cat' => 'Agreement & Legal', 'icon' => 'fa-file-signature'],
                        ['cat' => 'Brokerage / Commission', 'icon' => 'fa-handshake'],
                        ['cat' => 'Security & Guard', 'icon' => 'fa-shield-halved'],
                        ['cat' => 'Pest Control', 'icon' => 'fa-bug'],
                        ['cat' => 'Appliance Repair', 'icon' => 'fa-tv'],
                    ];
                @endphp
                @foreach($rentalChips as $chip)
                    <button type="button" class="rental-chip-btn"
                            onclick="setRentalCategory('{{ $chip['cat'] }}')"
                            style="background: rgba(30, 41, 59, 0.70); border: 1px solid rgba(255, 255, 255, 0.12); color: #E2E8F0; padding: 6px 12px; border-radius: 20px; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all .2s ease;">
                        <i class="fa-solid {{ $chip['icon'] }}" style="color: #60A5FA;"></i>
                        <span>{{ $chip['cat'] }}</span>
                    </button>
                @endforeach
            </div>
            <input type="hidden" name="expense_subcategory" id="expense_subcategory" value="{{ old('expense_subcategory') }}">
        </div>

        {{-- Section 1: Classification & Hierarchy --}}
        <div class="form-section" id="section-classification">
            <div class="section-title"><i class="fa-solid fa-sitemap"></i> <span id="section-classification-title">{{ $currType === 'Rental' ? '1. Rental Property & Agreement Allocation' : ($currType === 'Property' ? '1. Property Expense Classification' : '1. Expense Classification & Allocation') }}</span></div>
            
            {{-- Firm selector --}}
            @include('admin.components.firm-select')

            <div class="form-row">
                {{-- Expense Date --}}
                <div class="form-group">
                    <label class="form-label" for="expense_date">Expense Date <span class="req">*</span></label>
                    <input type="date" name="expense_date" id="expense_date"
                           value="{{ old('expense_date', date('Y-m-d')) }}"
                           class="form-control @error('expense_date') is-invalid @enderror" required>
                    @error('expense_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Expense Category with Dark Dropdown & Quick Add Button --}}
                <div class="form-group">
                    <label class="form-label" for="expense_category">Expense Category <span class="req">*</span></label>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <select name="expense_category" id="expense_category" class="form-control @error('expense_category') is-invalid @enderror" onchange="handleCategoryDropdownChange(this)" required>
                            <option value="">— Select Category —</option>
                            @if(isset($categories))
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}" {{ old('expense_category') == $cat->name ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            @endif
                            <option value="__new__" style="color: #60A5FA; font-weight: 700; background: #1E293B;">➕ + Add New Category...</option>
                        </select>
                        <button type="button" class="btn-quick-add-cat" onclick="openCategoryModal()" title="Add New Category">
                            <i class="fa-solid fa-plus"></i> <span>Add</span>
                        </button>
                    </div>
                    @error('expense_category')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row" id="project-property-row" style="{{ in_array($currType, ['General', 'Personal']) ? 'display:none;' : '' }}">
                {{-- Project (Hidden in Rental Mode or shown for filtering) --}}
                <div class="form-group" id="project-field-wrapper" style="{{ $currType === 'Rental' ? 'display:none;' : '' }}">
                    <label class="form-label" for="project_id">Project <span class="opt">(Project-wise Expense / Filter)</span></label>
                    <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror">
                        <option value="">— All Projects / General —</option>
                        @if(isset($projects))
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}"
                                        data-firm-id="{{ $proj->firm_id }}"
                                        {{ old('project_id', $selectedProjectId ?? '') == $proj->id ? 'selected' : '' }}>
                                    {{ $proj->project_name }} {{ $proj->propertyMaster ? '('.$proj->propertyMaster->property_name.')' : '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('project_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Property / Unit Multi-Select or Single Select --}}
                <div class="form-group" id="property-field-wrapper" style="{{ $currType === 'Rental' ? 'grid-column: 1 / -1;' : '' }}">
                    <label class="form-label" for="property_ids" id="property-label">
                        <span id="property-label-text">{{ $currType === 'Rental' ? 'Rental Property / Unit' : 'Property / Unit' }}</span>
                        <span class="opt" id="property-label-opt">{{ $currType === 'Rental' ? '(Selecting a property auto-fetches Tenant & Agreement)' : '(Multi-Select Enabled)' }}</span>
                    </label>
                    @php
                        $selectedPropIds = (array) old('property_ids', request('property_ids') ?: (request('property_id') ? [request('property_id')] : (old('property_id') ? [old('property_id')] : ($selectedPropertyIds ?? ($selectedPropertyId ? [$selectedPropertyId] : [])))));
                    @endphp
                    <select name="property_ids[]" id="property_ids" class="form-control select2-multi @error('property_ids') is-invalid @enderror" multiple data-placeholder="Search and select property / unit...">
                        @if(isset($properties))
                            @foreach($properties as $prop)
                                <option value="{{ $prop->id }}"
                                        data-project-id="{{ $prop->project_id }}"
                                        data-firm-id="{{ $prop->firm_id }}"
                                        data-project="{{ $prop->project->project_name ?? ($prop->project->propertyMaster->property_name ?? '') }}"
                                        {{ in_array($prop->id, $selectedPropIds) ? 'selected' : '' }}>
                                    {{ $prop->property_name }}{{ $prop->unit_no ? ' · Unit '.$prop->unit_no : '' }}
                                    @if(!$prop->project_id) [Direct Property] @elseif($prop->project) ({{ $prop->project->project_name }}) @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('property_ids')<div class="text-error">{{ $message }}</div>@enderror
                    @error('property_ids.*')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Rental Connection & Auto-Fetch Details Card --}}
            <div id="rental-connection-card" style="{{ $currType === 'Rental' ? '' : 'display:none;' }} margin-top: 20px; background: rgba(15, 23, 42, 0.85); border: 1.5px solid rgba(59, 130, 246, 0.35); border-radius: 18px; padding: 22px; box-shadow: 0 10px 30px rgba(0,0,0,0.35);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(37,99,235,0.25); color: #60A5FA; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                            <i class="fa-solid fa-house-user"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0; font-size: 15px; font-weight: 800; color: #FFFFFF;">Rental Management Connection</h4>
                            <p style="margin: 2px 0 0; font-size: 12px; color: #94A3B8;">Auto-synced with active Lease Agreement &amp; Tenant Profile</p>
                        </div>
                    </div>
                    <div id="rental-sync-badge" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.4); color: #93C5FD;">
                        <i class="fa-solid fa-circle-notch fa-spin" id="rental-spinner" style="display: none;"></i>
                        <span id="rental-status-text">Select a Property to Auto-Fetch Details</span>
                    </div>
                </div>

                <div class="form-row" style="margin-bottom: 16px;">
                    {{-- Rental Agreement Selector --}}
                    <div class="form-group">
                        <label class="form-label" for="rental_id">Linked Rental Agreement <span class="opt">(Auto-Selected)</span></label>
                        <select name="rental_id" id="rental_id" class="form-control @error('rental_id') is-invalid @enderror" onchange="handleRentalAgreementChange(this)">
                            <option value="">— No Agreement / Direct Property Expense —</option>
                            @if(isset($rentals))
                                @foreach($rentals as $r)
                                    <option value="{{ $r->id }}"
                                            data-property-id="{{ $r->property_id }}"
                                            data-tenant-id="{{ $r->tenant_id }}"
                                            data-tenant-name="{{ $r->tenant_name ?? $r->tenant?->name }}"
                                            data-tenant-phone="{{ $r->tenant_mobile ?? $r->tenant?->phone }}"
                                            data-rent="{{ $r->rent_amount }}"
                                            data-deposit="{{ $r->security_deposit }}"
                                            data-status="{{ $r->rental_status }}"
                                            data-firm-id="{{ $r->firm_id }}"
                                            data-start="{{ $r->start_date ? (is_string($r->start_date) ? $r->start_date : $r->start_date->format('d M Y')) : '' }}"
                                            data-end="{{ $r->end_date ? (is_string($r->end_date) ? $r->end_date : $r->end_date->format('d M Y')) : '' }}"
                                            {{ old('rental_id', $selectedRentalId ?? '') == $r->id ? 'selected' : '' }}>
                                        {{ $r->agreement_no ?: 'AGR-'.$r->id }} ({{ $r->tenant_name ?? $r->tenant?->name ?? 'Tenant' }}) · {{ $r->property?->property_name }} [{{ ucfirst($r->rental_status) }}]
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('rental_id')<div class="text-error">{{ $message }}</div>@enderror
                    </div>

                    {{-- Tenant Selector --}}
                    <div class="form-group">
                        <label class="form-label" for="tenant_id">Tenant <span class="opt">(Auto-Populated)</span></label>
                        <select name="tenant_id" id="tenant_id" class="form-control @error('tenant_id') is-invalid @enderror">
                            <option value="">— Select Tenant (Optional) —</option>
                            @if(isset($tenants))
                                @foreach($tenants as $t)
                                    <option value="{{ $t->id }}"
                                            data-phone="{{ $t->phone }}"
                                            data-firm-id="{{ $t->firm_id }}"
                                            {{ old('tenant_id', $selectedTenantId ?? '') == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} {{ $t->phone ? '('.$t->phone.')' : '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('tenant_id')<div class="text-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Visual Info Snapshot Box --}}
                <div id="rental-snapshot-box" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; background: rgba(0,0,0,0.30); border-radius: 12px; padding: 14px; border: 1px solid rgba(255,255,255,0.06);">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; color: #94A3B8; font-weight: 700; letter-spacing: 0.5px;">Active Tenant</div>
                        <div id="snap-tenant-name" style="font-size: 14px; font-weight: 800; color: #FFFFFF; margin-top: 2px;">—</div>
                        <div id="snap-tenant-phone" style="font-size: 12px; color: #60A5FA;">—</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; color: #94A3B8; font-weight: 700; letter-spacing: 0.5px;">Agreement No</div>
                        <div id="snap-agreement-no" style="font-size: 14px; font-weight: 800; color: #38BDF8; margin-top: 2px;">—</div>
                        <div id="snap-rental-status" style="font-size: 11.5px; color: #10B981; font-weight: 700;">—</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; color: #94A3B8; font-weight: 700; letter-spacing: 0.5px;">Rent &amp; Deposit</div>
                        <div id="snap-rent-amt" style="font-size: 14px; font-weight: 800; color: #F59E0B; margin-top: 2px;">₹ 0.00 / mo</div>
                        <div id="snap-deposit-amt" style="font-size: 11.5px; color: #94A3B8;">Dep: ₹ 0.00</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; color: #94A3B8; font-weight: 700; letter-spacing: 0.5px;">Lease Period</div>
                        <div id="snap-lease-period" style="font-size: 12.5px; font-weight: 600; color: #CBD5E1; margin-top: 3px;">—</div>
                    </div>
                </div>

                {{-- Tenant Recovery Configuration Box --}}
                <div style="margin-top: 18px; padding-top: 16px; border-top: 1px dashed rgba(255,255,255,0.12);">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                            <input type="checkbox" name="is_tenant_recoverable" id="is_tenant_recoverable" value="1"
                                   {{ old('is_tenant_recoverable') ? 'checked' : '' }}
                                   onchange="toggleRecoveryFields(this.checked)"
                                   style="width: 18px; height: 18px; accent-color: #2563EB; cursor: pointer;">
                            <span style="font-size: 13.5px; font-weight: 700; color: #FFFFFF;">
                                <i class="fa-solid fa-hand-holding-dollar" style="color: #34D399; margin-right: 4px;"></i>
                                Billable / Recoverable from Tenant
                            </span>
                        </label>
                        <span style="font-size: 12px; color: #94A3B8;">(Tracks whether tenant must reimburse this expense or be deducted from deposit)</span>
                    </div>

                    <div id="recovery-fields-row" class="form-row" style="{{ old('is_tenant_recoverable') ? '' : 'display:none;' }} margin-top: 14px;">
                        <div class="form-group">
                            <label class="form-label" for="recovery_amount">Recoverable Amount (₹) <span class="req">*</span></label>
                            <input type="number" step="0.01" min="0" name="recovery_amount" id="recovery_amount"
                                   value="{{ old('recovery_amount') }}" class="form-control"
                                   placeholder="0.00 (Defaults to expense amount)">
                            <span class="form-hint">Amount tenant is expected to pay back.</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="recovery_status">Recovery Status <span class="req">*</span></label>
                            <select name="recovery_status" id="recovery_status" class="form-control">
                                @foreach(['Pending', 'Recovered', 'Partially Recovered', 'Waived', 'Deducted from Deposit'] as $st)
                                    <option value="{{ $st }}" {{ old('recovery_status', 'Pending') == $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                            <span class="form-hint">Current reimbursement stage.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Amount & Payment Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> 2. Amount &amp; Payment Details</div>
            
            <div class="form-row-3">
                {{-- Amount --}}
                <div class="form-group">
                    <label class="form-label" for="amount">Amount (₹) <span class="req">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount" id="amount"
                           value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror"
                           placeholder="0.00" oninput="handleAmountInput(this.value)" required>
                    @error('amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Payment Mode --}}
                <div class="form-group">
                    <label class="form-label" for="payment_mode">Payment Mode</label>
                    <select name="payment_mode" id="payment_mode" class="form-control @error('payment_mode') is-invalid @enderror">
                        <option value="">— Select Mode —</option>
                        @foreach($paymentModes as $pm)
                            @php $pName = is_object($pm) ? $pm->name : $pm; @endphp
                            <option value="{{ $pName }}" {{ old('payment_mode', 'Cash') == $pName ? 'selected' : '' }}>{{ $pName }}</option>
                        @endforeach
                    </select>
                    @error('payment_mode')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Paid To / Vendor --}}
                <div class="form-group">
                    <label class="form-label" for="paid_to">Paid To / Vendor <span class="opt">(Payee Name or Vendor)</span></label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" name="paid_to" id="paid_to" value="{{ old('paid_to', $selectedPaidTo ?? request('paid_to')) }}"
                                class="form-control @error('paid_to') is-invalid @enderror"
                                placeholder="Vendor, contractor, or tenant name"
                                list="vendors-datalist">
                        <datalist id="vendors-datalist">
                            @if(isset($vendors))
                                @foreach($vendors as $ven)
                                    <option value="{{ $ven->name }}" data-id="{{ $ven->id }}">{{ $ven->name }} {{ $ven->mobile ? '('.$ven->mobile.')' : '' }}</option>
                                @endforeach
                            @endif
                        </datalist>
                        <input type="hidden" name="vendor_id" id="vendor_id" value="{{ old('vendor_id', $selectedVendorId ?? request('vendor_id')) }}">
                    </div>
                    @error('paid_to')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row-3">
                {{-- Reference Number --}}
                <div class="form-group">
                    <label class="form-label" for="reference_no">Reference Number <span class="opt">(UPI / Cheque / Bank Ref)</span></label>
                    <input type="text" name="reference_no" id="reference_no" value="{{ old('reference_no') }}"
                           class="form-control @error('reference_no') is-invalid @enderror" placeholder="e.g. UPI-98765432, CHQ-100234">
                    @error('reference_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Payment Account --}}
                <div class="form-group">
                    <label class="form-label" for="payment_account">Payment Account <span class="opt">(Bank / Account Source)</span></label>
                    <input type="text" name="payment_account" id="payment_account" value="{{ old('payment_account') }}"
                           class="form-control @error('payment_account') is-invalid @enderror" placeholder="e.g. HDFC Current A/c, Cash in Hand">
                    @error('payment_account')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Bill / Invoice No --}}
                <div class="form-group">
                    <label class="form-label" for="bill_no">Bill / Invoice No <span class="opt">(Optional)</span></label>
                    <input type="text" name="bill_no" id="bill_no" value="{{ old('bill_no') }}"
                           class="form-control @error('bill_no') is-invalid @enderror" placeholder="e.g. INV-2026-089">
                    @error('bill_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                {{-- Approval Status --}}
                <div class="form-group">
                    <label class="form-label" for="approval_status">Approval Status <span class="req">*</span></label>
                    <select name="approval_status" id="approval_status" class="form-control @error('approval_status') is-invalid @enderror">
                        @foreach(['Pending','Approved','Rejected'] as $s)
                            <option value="{{ $s }}" {{ old('approval_status', 'Pending') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('approval_status')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Expense Title (Optional - auto-generated if blank) --}}
                <div class="form-group">
                    <label class="form-label" for="expense_title">Expense Title <span class="opt">(Optional Short Title)</span></label>
                    <input type="text" name="expense_title" id="expense_title"
                           value="{{ old('expense_title') }}" class="form-control @error('expense_title') is-invalid @enderror"
                           placeholder="e.g. Society Maintenance, AC Repair (Auto-generated if empty)">
                    @error('expense_title')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Section 3: Description & Notes --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-align-left"></i> 3. Description &amp; Notes</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="description">Description <span class="opt">(Explain the expense in detail)</span></label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                              placeholder="Enter details about what this expense was incurred for...">{{ old('description') }}</textarea>
                    @error('description')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="notes">Notes / Remarks <span class="opt">(Internal remarks)</span></label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                              placeholder="Add any additional notes, remarks or audit references...">{{ old('notes') }}</textarea>
                    @error('notes')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Section 4: Attachment Upload --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-paperclip"></i> 4. Attachment (Bill / Invoice / Receipt)</div>
            <div class="form-group">
                <div class="file-upload-box" onclick="document.getElementById('bill_file').click()">
                    <label class="file-upload-label" for="bill_file">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span class="upload-text">Click to upload bill, invoice or receipt</span>
                        <span class="upload-hint">PDF, JPG, JPEG, PNG — max 5 MB</span>
                    </label>
                    <input type="file" name="bill_file" id="bill_file" accept=".pdf,.jpg,.jpeg,.png"
                           onchange="showFileName(this)" class="@error('bill_file') is-invalid @enderror">
                    <div id="file-name-display">No file selected</div>
                </div>
                @error('bill_file')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Save Expense
            </button>
            <a href="{{ route('expenses.index') }}" class="btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>
    </form>
</div>

<script>
function showFileName(input) {
    const display = document.getElementById('file-name-display');
    if (input.files && input.files[0]) {
        display.textContent = '📎 ' + input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(1) + ' KB)';
        display.style.color = '#60A5FA';
        display.style.fontWeight = '600';
    } else {
        display.textContent = 'No file selected';
        display.style.color = '#94A3B8';
    }
}

function handleAmountInput(val) {
    const recCheck = document.getElementById('is_tenant_recoverable');
    const recAmt = document.getElementById('recovery_amount');
    if (recCheck && recCheck.checked && recAmt && (!recAmt.value || recAmt.dataset.userEdited !== 'true')) {
        recAmt.value = val;
    }
}

function toggleRecoveryFields(checked) {
    const row = document.getElementById('recovery-fields-row');
    const recAmt = document.getElementById('recovery_amount');
    const amt = document.getElementById('amount');
    if (row) {
        row.style.display = checked ? 'grid' : 'none';
    }
    if (checked && recAmt && !recAmt.value && amt && amt.value) {
        recAmt.value = amt.value;
    }
}

function setRentalCategory(catName) {
    const catSelect = document.getElementById('expense_category');
    const subInput = document.getElementById('expense_subcategory');
    const titleInput = document.getElementById('expense_title');
    
    if (subInput) subInput.value = catName;

    if (catSelect) {
        let opt = Array.from(catSelect.options).find(o => o.value.toLowerCase() === catName.toLowerCase());
        if (!opt) {
            opt = document.createElement('option');
            opt.value = catName;
            opt.textContent = catName;
            const newOpt = catSelect.querySelector('option[value="__new__"]');
            if (newOpt) catSelect.insertBefore(opt, newOpt);
            else catSelect.appendChild(opt);
        }
        catSelect.value = opt.value;
    }

    if (titleInput && (!titleInput.value || titleInput.dataset.userEdited !== 'true')) {
        const propSelect = document.getElementById('property_ids');
        let propName = '';
        if (propSelect) {
            const selectedOpt = propSelect.options[propSelect.selectedIndex];
            if (selectedOpt && selectedOpt.value) {
                propName = selectedOpt.textContent.trim().split('·')[0].split('[')[0].trim();
            }
        }
        titleInput.value = propName ? `${catName} - ${propName}` : `${catName} Expense`;
    }

    // Highlight selected chip
    document.querySelectorAll('.rental-chip-btn').forEach(btn => {
        if (btn.innerText.includes(catName)) {
            btn.style.background = '#2563EB';
            btn.style.borderColor = '#3B82F6';
            btn.style.color = '#FFFFFF';
        } else {
            btn.style.background = 'rgba(30, 41, 59, 0.70)';
            btn.style.borderColor = 'rgba(255, 255, 255, 0.12)';
            btn.style.color = '#E2E8F0';
        }
    });
}

function handleRentalAgreementChange(select) {
    const selectedOpt = select.options[select.selectedIndex];
    if (!selectedOpt || !selectedOpt.value) {
        updateRentalSnapshot(null);
        return;
    }

    const tId = selectedOpt.getAttribute('data-tenant-id');
    const tName = selectedOpt.getAttribute('data-tenant-name');
    const tPhone = selectedOpt.getAttribute('data-tenant-phone');
    const rent = selectedOpt.getAttribute('data-rent');
    const deposit = selectedOpt.getAttribute('data-deposit');
    const status = selectedOpt.getAttribute('data-status');
    const fId = selectedOpt.getAttribute('data-firm-id');
    const start = selectedOpt.getAttribute('data-start');
    const end = selectedOpt.getAttribute('data-end');
    const agrNo = selectedOpt.textContent.trim().split('(')[0].trim();

    // Auto set tenant
    const tenantSelect = document.getElementById('tenant_id');
    if (tenantSelect && tId) {
        tenantSelect.value = tId;
    }

    // Auto sync firm if available
    const firmSelect = document.getElementById('firm_ids') || document.querySelector('[name="firm_id"]');
    if (firmSelect && fId) {
        if (firmSelect.multiple) {
            $(firmSelect).val([fId]).trigger('change');
        } else {
            firmSelect.value = fId;
        }
    }

    updateRentalSnapshot({
        tenant_name: tName,
        tenant_phone: tPhone,
        agreement_no: agrNo,
        rental_status: status,
        rent_amount: rent,
        security_deposit: deposit,
        lease_period: (start && end) ? `${start} → ${end}` : (start ? `From ${start}` : '—')
    });
}

function updateRentalSnapshot(info) {
    const nameEl = document.getElementById('snap-tenant-name');
    const phoneEl = document.getElementById('snap-tenant-phone');
    const agrEl = document.getElementById('snap-agreement-no');
    const stEl = document.getElementById('snap-rental-status');
    const rentEl = document.getElementById('snap-rent-amt');
    const depEl = document.getElementById('snap-deposit-amt');
    const leaseEl = document.getElementById('snap-lease-period');
    const badgeText = document.getElementById('rental-status-text');

    if (info) {
        if (nameEl) nameEl.textContent = info.tenant_name || '—';
        if (phoneEl) phoneEl.textContent = info.tenant_phone ? `📞 ${info.tenant_phone}` : '';
        if (agrEl) agrEl.textContent = info.agreement_no || '—';
        if (stEl) {
            stEl.textContent = (info.rental_status || 'Active').toUpperCase();
            stEl.style.color = (info.rental_status === 'active' || !info.rental_status) ? '#10B981' : '#F59E0B';
        }
        if (rentEl) rentEl.textContent = info.rent_amount ? `₹ ${parseFloat(info.rent_amount).toLocaleString('en-IN')} / mo` : '₹ 0.00 / mo';
        if (depEl) depEl.textContent = info.security_deposit ? `Dep: ₹ ${parseFloat(info.security_deposit).toLocaleString('en-IN')}` : 'Dep: ₹ 0.00';
        if (leaseEl) leaseEl.textContent = info.lease_period || '—';
        if (badgeText) badgeText.textContent = '✅ Connected to Rental Agreement';
    } else {
        if (nameEl) nameEl.textContent = '—';
        if (phoneEl) phoneEl.textContent = '—';
        if (agrEl) agrEl.textContent = '—';
        if (stEl) stEl.textContent = '—';
        if (rentEl) rentEl.textContent = '₹ 0.00 / mo';
        if (depEl) depEl.textContent = 'Dep: ₹ 0.00';
        if (leaseEl) leaseEl.textContent = '—';
        if (badgeText) badgeText.textContent = 'No Agreement Selected';
    }
}

function fetchRentalInfoForProperty(propertyId) {
    if (!propertyId) {
        updateRentalSnapshot(null);
        return;
    }

    const spinner = document.getElementById('rental-spinner');
    const badgeText = document.getElementById('rental-status-text');
    if (spinner) spinner.style.display = 'inline-block';
    if (badgeText) badgeText.textContent = 'Fetching Rental & Tenant Info...';

    const url = '{{ url("expenses/rental-property-info") }}/' + propertyId;

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (spinner) spinner.style.display = 'none';

        if (data.success) {
            const rentalSelect = document.getElementById('rental_id');
            const tenantSelect = document.getElementById('tenant_id');

            // Populate rental agreements dropdown for this property
            if (rentalSelect && data.rentals && data.rentals.length > 0) {
                rentalSelect.innerHTML = '<option value="">— Select Agreement —</option>';
                data.rentals.forEach(r => {
                    const opt = document.createElement('option');
                    opt.value = r.id;
                    opt.textContent = `${r.agreement_no || 'AGR-' + r.id} (${r.tenant_name || 'Tenant'}) [${r.rental_status}]`;
                    opt.setAttribute('data-tenant-id', r.tenant_id || '');
                    opt.setAttribute('data-tenant-name', r.tenant_name || '');
                    opt.setAttribute('data-tenant-phone', r.tenant_phone || '');
                    opt.setAttribute('data-rent', r.rent_amount || '0');
                    opt.setAttribute('data-deposit', r.security_deposit || '0');
                    opt.setAttribute('data-status', r.rental_status || '');
                    opt.setAttribute('data-firm-id', r.firm_id || '');
                    opt.setAttribute('data-start', r.start_date || '');
                    opt.setAttribute('data-end', r.end_date || '');
                    rentalSelect.appendChild(opt);
                });

                if (data.active_rental) {
                    rentalSelect.value = data.active_rental.id;
                }
            }

            // Populate/select tenant
            if (data.active_rental && data.active_rental.tenant_id && tenantSelect) {
                tenantSelect.value = data.active_rental.tenant_id;
            }

            // Sync Firm
            if (data.property && data.property.firm_id) {
                const firmSelect = document.getElementById('firm_ids') || document.querySelector('[name="firm_id"]');
                if (firmSelect) {
                    if (firmSelect.multiple) {
                        $(firmSelect).val(data.property.firm_ids || [data.property.firm_id]).trigger('change');
                    } else {
                        firmSelect.value = data.property.firm_id;
                    }
                }
            }

            if (data.active_rental) {
                updateRentalSnapshot({
                    tenant_name: data.active_rental.tenant_name,
                    tenant_phone: data.active_rental.tenant_phone,
                    agreement_no: data.active_rental.agreement_no || 'AGR-' + data.active_rental.id,
                    rental_status: data.active_rental.rental_status,
                    rent_amount: data.active_rental.rent_amount,
                    security_deposit: data.active_rental.security_deposit,
                    lease_period: (data.active_rental.start_date && data.active_rental.end_date) ? `${data.active_rental.start_date} → ${data.active_rental.end_date}` : 'Active Agreement'
                });
            } else {
                updateRentalSnapshot(null);
                if (badgeText) badgeText.textContent = 'ℹ️ No active rental on this property';
            }
        }
    })
    .catch(err => {
        if (spinner) spinner.style.display = 'none';
        if (badgeText) badgeText.textContent = 'Error connecting to rental database';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const firmSelect    = document.getElementById('firm_ids') || document.querySelector('[name="firm_id"]');
    const projectSelect = document.getElementById('project_id');
    const propSelect    = document.getElementById('property_ids') || document.getElementById('property_id');
    const paidToInput   = document.getElementById('paid_to');
    const vendorIdInput = document.getElementById('vendor_id');
    const recoveryAmtInput = document.getElementById('recovery_amount');
    const titleInput = document.getElementById('expense_title');

    if (recoveryAmtInput) {
        recoveryAmtInput.addEventListener('input', function() {
            this.dataset.userEdited = 'true';
        });
    }
    if (titleInput) {
        titleInput.addEventListener('input', function() {
            this.dataset.userEdited = 'true';
        });
    }

    const allProjectOptions = projectSelect ? Array.from(projectSelect.querySelectorAll('option')).slice(1) : [];
    const allPropOptions    = propSelect ? Array.from(propSelect.querySelectorAll('option')) : [];

    // Initialize Select2 on property_ids if not already done
    if (propSelect && $.fn.select2) {
        $(propSelect).select2({
            placeholder: "Search and select property / unit...",
            allowClear: true,
            width: '100%'
        });
    }

    // Paid to vendor auto-link
    if (paidToInput) {
        paidToInput.addEventListener('input', function() {
            const val = this.value.trim().toLowerCase();
            const datalist = document.getElementById('vendors-datalist');
            let matchedId = '';
            if (datalist && val) {
                Array.from(datalist.options).forEach(opt => {
                    if (opt.value.trim().toLowerCase() === val) {
                        matchedId = opt.getAttribute('data-id') || '';
                    }
                });
            }
            if (vendorIdInput) vendorIdInput.value = matchedId;
        });
    }

    function getSelectedFirmIds() {
        if (!firmSelect) return [];
        if (firmSelect.multiple) {
            return Array.from(firmSelect.selectedOptions).map(o => String(o.value)).filter(Boolean);
        } else if (firmSelect.value) {
            return [String(firmSelect.value)];
        }
        return [];
    }

    function filterProjectsAndProperties() {
        const selectedFirmIds = getSelectedFirmIds();
        const currentProjectVal = projectSelect ? projectSelect.value : '';

        // Filter Projects
        if (projectSelect) {
            projectSelect.innerHTML = '<option value="">— All Projects / General —</option>';
            let projCount = 0;
            allProjectOptions.forEach(opt => {
                const optFirmId = String(opt.getAttribute('data-firm-id') || '');
                if (selectedFirmIds.length === 0 || selectedFirmIds.includes(optFirmId) || !optFirmId) {
                    projectSelect.appendChild(opt.cloneNode(true));
                    projCount++;
                }
            });
            const projMatch = Array.from(projectSelect.options).some(o => o.value === currentProjectVal);
            projectSelect.value = projMatch ? currentProjectVal : '';
        }

        // Filter Properties
        filterPropertiesByProject();
    }

    function filterPropertiesByProject() {
        if (!propSelect) return;
        const selectedProjectId = projectSelect ? projectSelect.value : '';
        const selectedFirmIds   = getSelectedFirmIds();
        
        let currentPropVals = [];
        if ($.fn.select2 && $(propSelect).hasClass('select2-hidden-accessible')) {
            currentPropVals = $(propSelect).val() || [];
        } else {
            currentPropVals = Array.from(propSelect.selectedOptions).map(o => o.value);
        }
        if (!Array.isArray(currentPropVals)) {
            currentPropVals = currentPropVals ? [currentPropVals] : [];
        }

        propSelect.innerHTML = '';

        let validSelected = [];
        allPropOptions.forEach(opt => {
            const optProjId = String(opt.getAttribute('data-project-id') || '');
            const optFirmId = String(opt.getAttribute('data-firm-id') || '');

            const firmMatch = selectedFirmIds.length === 0 || selectedFirmIds.includes(optFirmId) || !optFirmId;
            const projMatch = !selectedProjectId || optProjId === String(selectedProjectId);

            if (firmMatch && projMatch) {
                const newOpt = opt.cloneNode(true);
                if (currentPropVals.includes(newOpt.value)) {
                    newOpt.selected = true;
                    validSelected.push(newOpt.value);
                }
                propSelect.appendChild(newOpt);
            }
        });

        if ($.fn.select2 && $(propSelect).hasClass('select2-hidden-accessible')) {
            $(propSelect).val(validSelected).trigger('change.select2');
        }
    }

    if (firmSelect) {
        $(firmSelect).on('change', filterProjectsAndProperties);
    }

    if (projectSelect) {
        projectSelect.addEventListener('change', filterPropertiesByProject);
    }

    if (propSelect) {
        $(propSelect).on('change', function() {
            const selectedVals = $(this).val() || [];
            const typeInput = document.getElementById('expense_type_input');
            const isRental = typeInput && typeInput.value === 'Rental';

            if (Array.isArray(selectedVals) && selectedVals.length > 0) {
                const firstVal = selectedVals[0];
                if (isRental) {
                    fetchRentalInfoForProperty(firstVal);
                }

                if (projectSelect && !projectSelect.value) {
                    const matchedOpt = allPropOptions.find(o => o.value === firstVal);
                    if (matchedOpt) {
                        const pId = matchedOpt.getAttribute('data-project-id');
                        if (pId) {
                            projectSelect.value = pId;
                            filterPropertiesByProject();
                        }
                    }
                }
            }
        });
    }

    // Auto load initial rental if provided
    const rentalSelect = document.getElementById('rental_id');
    if (rentalSelect && rentalSelect.value) {
        handleRentalAgreementChange(rentalSelect);
    } else if (propSelect) {
        const selProps = $(propSelect).val() || [];
        const typeInput = document.getElementById('expense_type_input');
        if (typeInput && typeInput.value === 'Rental' && Array.isArray(selProps) && selProps.length > 0) {
            fetchRentalInfoForProperty(selProps[0]);
        }
    }

    // Initialize state
    if (projectSelect && projectSelect.value) {
        filterPropertiesByProject();
    }
});

// Quick Add Category Modal Functions
function selectExpenseType(type) {
    const input = document.getElementById('expense_type_input');
    if (input) input.value = type;

    document.querySelectorAll('.expense-type-bar .type-btn').forEach(btn => {
        if (btn.innerText.includes(type)) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    const row = document.getElementById('project-property-row');
    const projWrapper = document.getElementById('project-field-wrapper');
    const propWrapper = document.getElementById('property-field-wrapper');
    const rentalCard = document.getElementById('rental-connection-card');
    const rentalChips = document.getElementById('rental-category-chips');
    const titleEl = document.getElementById('section-classification-title');
    const propLabel = document.getElementById('property-label-text');
    const propOpt = document.getElementById('property-label-opt');

    if (type === 'Rental') {
        if (row) row.style.display = 'grid';
        if (projWrapper) projWrapper.style.display = 'none';
        if (propWrapper) propWrapper.style.gridColumn = '1 / -1';
        if (rentalCard) rentalCard.style.display = 'block';
        if (rentalChips) rentalChips.style.display = 'block';
        if (titleEl) titleEl.textContent = '1. Rental Property & Agreement Allocation';
        if (propLabel) propLabel.textContent = 'Rental Property / Unit';
        if (propOpt) propOpt.textContent = '(Selecting a property auto-fetches Tenant & Agreement)';

        const propSelect = document.getElementById('property_ids');
        if (propSelect) {
            const selVal = $(propSelect).val();
            if (selVal && (Array.isArray(selVal) ? selVal[0] : selVal)) {
                fetchRentalInfoForProperty(Array.isArray(selVal) ? selVal[0] : selVal);
            }
        }
    } else if (type === 'Property') {
        if (row) row.style.display = 'grid';
        if (projWrapper) projWrapper.style.display = 'block';
        if (propWrapper) propWrapper.style.gridColumn = 'auto';
        if (rentalCard) rentalCard.style.display = 'none';
        if (rentalChips) rentalChips.style.display = 'none';
        if (titleEl) titleEl.textContent = '1. Property Expense Classification';
        if (propLabel) propLabel.textContent = 'Property / Unit';
        if (propOpt) propOpt.textContent = '(Multi-Select Enabled)';
    } else if (type === 'General') {
        if (row) row.style.display = 'none';
        if (rentalCard) rentalCard.style.display = 'none';
        if (rentalChips) rentalChips.style.display = 'none';
        if (titleEl) titleEl.textContent = '1. General Expense Classification';
    } else if (type === 'Personal') {
        if (row) row.style.display = 'none';
        if (rentalCard) rentalCard.style.display = 'none';
        if (rentalChips) rentalChips.style.display = 'none';
        if (titleEl) titleEl.textContent = '1. Personal Expense Classification';
    }
}

function openCategoryModal() {
    const modal = document.getElementById('quickCatModal');
    const nameInput = document.getElementById('quick_cat_name');
    const descInput = document.getElementById('quick_cat_desc');
    const errorEl = document.getElementById('quick_cat_error');
    if (nameInput) nameInput.value = '';
    if (descInput) descInput.value = '';
    if (errorEl) errorEl.style.display = 'none';
    if (modal) modal.style.display = 'flex';
    setTimeout(() => { if (nameInput) nameInput.focus(); }, 150);
}

function closeCategoryModal() {
    const modal = document.getElementById('quickCatModal');
    if (modal) modal.style.display = 'none';
    const select = document.getElementById('expense_category');
    if (select && select.value === '__new__') {
        select.value = '';
    }
}

function closeCategoryModalOnBackdrop(e) {
    if (e.target && e.target.id === 'quickCatModal') {
        closeCategoryModal();
    }
}

function handleCategoryDropdownChange(select) {
    if (select.value === '__new__') {
        openCategoryModal();
    }
}

function handleQuickCatKey(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        submitQuickCategory();
    }
}

function submitQuickCategory() {
    const nameInput = document.getElementById('quick_cat_name');
    const descInput = document.getElementById('quick_cat_desc');
    const errorEl   = document.getElementById('quick_cat_error');
    const saveBtn   = document.getElementById('btn-save-cat');
    const nameVal   = nameInput ? nameInput.value.trim() : '';
    const descVal   = descInput ? descInput.value.trim() : '';

    if (!nameVal) {
        if (errorEl) {
            errorEl.textContent = 'Please enter a category name.';
            errorEl.style.display = 'block';
        }
        if (nameInput) nameInput.focus();
        return;
    }

    if (errorEl) errorEl.style.display = 'none';
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
    }

    const firmSelect = document.getElementById('firm_ids') || document.querySelector('[name="firm_id"]');
    let firmIds = [];
    if (firmSelect) {
        if (firmSelect.multiple) {
            firmIds = Array.from(firmSelect.selectedOptions).map(o => o.value).filter(Boolean);
        } else if (firmSelect.value) {
            firmIds = [firmSelect.value];
        }
    }

    fetch('{{ route("expense-categories.quick-store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            name: nameVal,
            description: descVal,
            firm_ids: firmIds,
        })
    })
    .then(res => res.json())
    .then(data => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fa-solid fa-check"></i> Save Category';
        }
        if (data.success && data.category) {
            const select = document.getElementById('expense_category');
            if (select) {
                let opt = Array.from(select.options).find(o => o.value.toLowerCase() === data.category.name.toLowerCase());
                if (!opt) {
                    opt = document.createElement('option');
                    opt.value = data.category.name;
                    opt.textContent = data.category.name;
                    const newOption = select.querySelector('option[value="__new__"]');
                    if (newOption) {
                        select.insertBefore(opt, newOption);
                    } else {
                        select.appendChild(opt);
                    }
                }
                select.value = opt.value;
            }
            closeCategoryModal();
        } else {
            if (errorEl) {
                errorEl.textContent = data.message || 'Error saving category.';
                errorEl.style.display = 'block';
            }
        }
    })
    .catch(err => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fa-solid fa-check"></i> Save Category';
        }
        if (errorEl) {
            errorEl.textContent = 'Server error. Please try again.';
            errorEl.style.display = 'block';
        }
    });
}
</script>

{{-- Quick Add Category Modal --}}
<div id="quickCatModal" class="cat-modal-backdrop" style="display: none;" onclick="closeCategoryModalOnBackdrop(event)">
    <div class="cat-modal-box">
        <div class="cat-modal-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="cat-modal-icon"><i class="fa-solid fa-tag"></i></div>
                <div>
                    <h3 style="margin: 0; color: #FFFFFF; font-size: 16.5px; font-weight: 800;">Add New Expense Category</h3>
                    <p style="margin: 2px 0 0; font-size: 12px; color: #94A3B8;">Create a custom category for tracking expenses</p>
                </div>
            </div>
            <button type="button" class="cat-modal-close" onclick="closeCategoryModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="cat-modal-body">
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" for="quick_cat_name">Category Name <span class="req">*</span></label>
                <input type="text" id="quick_cat_name" class="form-control" placeholder="e.g. Paint, Hardware, Fuel, Transport" autocomplete="off" onkeydown="handleQuickCatKey(event)">
                <div id="quick_cat_error" class="text-error" style="display: none; margin-top: 5px;"></div>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="quick_cat_desc">Description <span class="opt">(Optional)</span></label>
                <textarea id="quick_cat_desc" class="form-control" rows="2" placeholder="Brief note about what this category covers"></textarea>
            </div>
        </div>
        <div class="cat-modal-footer">
            <button type="button" class="btn-outline" onclick="closeCategoryModal()" style="padding: 8px 18px; font-size: 13px;">Cancel</button>
            <button type="button" id="btn-save-cat" class="btn-gold" onclick="submitQuickCategory()" style="padding: 8px 20px; font-size: 13px;">
                <i class="fa-solid fa-check"></i> Save Category
            </button>
        </div>
    </div>
</div>
@endsection
