@extends('admin.layouts.app')
@section('title', 'Edit Contractor')
@section('page-title', 'Contractor Master')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.btn-pc, .btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 22px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-pc:hover, .btn-primary-custom:hover {
    background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50);
}

.btn-sc, .btn-secondary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: rgba(255, 255, 255, 0.06) !important;
    color: #CBD5E1 !important; font-size: 14px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-sc:hover, .btn-secondary-custom:hover {
    background: rgba(255, 255, 255, 0.12) !important; color: #FFFFFF !important; transform: translateY(-2px);
}

.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 600 !important; margin: 0; }

.form-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 28px 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 24px;
    max-width: 820px;
}

.section-heading {
    font-size: 12.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #60A5FA !important;
    margin-top: 18px;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
@media(max-width:640px){.form-grid{grid-template-columns:1fr}}
.form-group { margin-bottom: 16px; }
.form-group:last-child { margin-bottom: 0; }

.form-label {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: #CBD5E1 !important;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.form-label span { color: #F87171; }

.form-control, select.form-control, input[type="text"].form-control, textarea.form-control {
    width: 100%;
    padding: 11px 16px;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important;
    font-size: 14px;
    font-family: var(--font-primary);
    color: #FFFFFF !important;
    outline: none;
    transition: all .2s ease;
    background: rgba(16, 22, 34, 0.65) !important;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}

select.form-control option {
    background: #111827 !important;
    color: #FFFFFF !important;
}

textarea.form-control { resize: vertical; min-height: 80px; }
.text-error { color: #F87171; font-size: 12.5px; margin-top: 5px; font-weight: 600; }
.form-hint { font-size: 12px; color: #94A3B8; margin-top: 5px; }
.form-action-buttons { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Contractor</h2>
        <p>Updating contractor: <strong>{{ $contractor->contractor_name }}</strong></p>
    </div>
    <a href="{{ route('contractors.index') }}" class="btn-sc"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('contractors.update', $contractor) }}" autocomplete="off">
@csrf
@method('PUT')

<div class="form-card">
    {{-- Firm Selection --}}
    @include('admin.components.firm-select', ['model' => $contractor])

    <div class="section-heading"><i class="fa-solid fa-city"></i> Project Assignment (Single or Multiple Projects)</div>
    <div class="form-grid">
        <div class="form-group" style="grid-column: 1 / -1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                <label class="form-label" for="project_ids" style="margin-bottom: 0;">
                    Assigned Project(s) <span>*</span>
                    <small style="color: #94A3B8; font-weight: normal; margin-left: 6px;">(Select 1, 2, or multiple projects)</small>
                </label>
                <div style="display: flex; gap: 8px;">
                    <button type="button" id="btnSelectAllProjects" style="background: rgba(59, 130, 246, 0.18); border: 1px solid rgba(59, 130, 246, 0.4); color: #60A5FA; border-radius: 6px; padding: 4px 10px; font-size: 11.5px; font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-check-double"></i> Select All
                    </button>
                    <button type="button" id="btnClearProjects" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.35); color: #F87171; border-radius: 6px; padding: 4px 10px; font-size: 11.5px; font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </button>
                </div>
            </div>

            @php
                $contractorProjectIds = $contractor->relationLoaded('projects') && $contractor->projects->isNotEmpty()
                    ? $contractor->projects->pluck('id')->toArray()
                    : ($contractor->project_id ? [$contractor->project_id] : []);
                $oldProjectIds = (array) old('project_ids', old('project_id', $contractorProjectIds));
                if (!is_array($oldProjectIds)) {
                    $oldProjectIds = $oldProjectIds ? [$oldProjectIds] : [];
                }
            @endphp
            <select name="project_ids[]" id="project_ids" class="form-control select2-multi @error('project_ids') is-invalid @enderror" multiple required data-placeholder="Search and select project(s)...">
                @foreach($projects as $proj)
                    @php
                        $pFirmIds = $proj->firms->pluck('id')->push($proj->firm_id)->filter()->unique()->values()->all();
                        $isSelected = in_array($proj->id, $oldProjectIds);
                    @endphp
                    <option value="{{ $proj->id }}"
                        data-firm-ids="{{ implode(',', $pFirmIds) }}"
                        {{ $isSelected ? 'selected' : '' }}>
                        {{ $proj->project_name }} {{ $proj->propertyMaster ? '('.$proj->propertyMaster->property_name.')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('project_ids')<div class="text-error">{{ $message }}</div>@enderror
            @error('project_ids.*')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        {{-- Specific Plots / Units Selection Grid --}}
        <div class="form-group" style="grid-column: 1 / -1; margin-top: 10px;" id="plotsSectionWrapper">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                <label class="form-label" style="margin-bottom: 0;">
                    <i class="fa-solid fa-shapes" style="color: #60A5FA;"></i> Specific Plot(s) / Unit(s) Assigned
                    <small style="color: #94A3B8; font-weight: normal; margin-left: 6px;">(Optional — select specific units or leave empty for full project)</small>
                </label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <button type="button" id="btnSelectAllPlots" style="background: rgba(59, 130, 246, 0.18); border: 1px solid rgba(59, 130, 246, 0.4); color: #60A5FA; border-radius: 6px; padding: 4px 10px; font-size: 11.5px; font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-check-double"></i> Select All Visible
                    </button>
                    <button type="button" id="btnClearPlots" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.35); color: #F87171; border-radius: 6px; padding: 4px 10px; font-size: 11.5px; font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </button>
                </div>
            </div>

            <div style="margin-bottom: 12px;">
                <input type="text" id="plotSearchInput" class="form-control" placeholder="🔍 Search plots / units by name, number, or code..." style="padding: 8px 14px; font-size: 13px;">
            </div>

            @php
                $contractorPropertyIds = $contractor->relationLoaded('properties')
                    ? $contractor->properties->pluck('id')->toArray()
                    : [];
                $oldPropertyIds = (array) old('property_ids', $contractorPropertyIds);
            @endphp

            <!-- Hidden multi-select for form submission -->
            <select name="property_ids[]" id="property_ids" multiple style="display: none;">
                @foreach($projects as $proj)
                    @foreach($proj->properties as $property)
                        <option value="{{ $property->id }}" {{ in_array($property->id, $oldPropertyIds) ? 'selected' : '' }}>
                            {{ $property->property_name }}
                        </option>
                    @endforeach
                @endforeach
            </select>

            <!-- Visual cards grid -->
            <div id="plotsCardsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; max-height: 340px; overflow-y: auto; padding: 10px; background: rgba(10, 15, 26, 0.75); border: 1.5px solid rgba(255,255,255,0.12); border-radius: 14px; box-shadow: inset 0 2px 8px rgba(0,0,0,0.4);">
                @php $hasAnyPlots = false; @endphp
                @foreach($projects as $proj)
                    @foreach($proj->properties as $property)
                        @php
                            $hasAnyPlots = true;
                            $isSelected = in_array($property->id, $oldPropertyIds);
                            $statusColor = $property->status === 'available' ? '#34D399' : ($property->status === 'booked' ? '#FBBF24' : '#F87171');
                            $statusBg = $property->status === 'available' ? 'rgba(16, 185, 129, 0.15)' : ($property->status === 'booked' ? 'rgba(245, 158, 11, 0.15)' : 'rgba(239, 68, 68, 0.15)');
                            $isShortUnit = $property->unit_no && strlen(trim($property->unit_no)) <= 10 && !str_contains(strtolower($property->unit_no), 'properties');
                        @endphp
                        <div class="plot-card-item {{ $isSelected ? 'is-selected' : '' }}"
                             data-property-id="{{ $property->id }}"
                             data-project-id="{{ $proj->id }}"
                             data-search="{{ strtolower($property->property_name . ' ' . $property->property_code . ' ' . $property->unit_no . ' ' . $proj->project_name) }}"
                             onclick="togglePlotCardSelection(this)"
                             style="cursor: pointer; user-select: none; padding: 12px 14px; border-radius: 12px; background: {{ $isSelected ? 'rgba(37, 99, 235, 0.22)' : 'rgba(20, 27, 41, 0.65)' }}; border: 1.5px solid {{ $isSelected ? '#3B82F6' : 'rgba(255, 255, 255, 0.10)' }}; transition: all .2s ease; display: flex; align-items: center; gap: 12px; box-shadow: {{ $isSelected ? '0 0 14px rgba(59, 130, 246, 0.35)' : 'none' }};">
                            
                            <!-- Checkbox -->
                            <div class="plot-check-box" style="width: 22px; height: 22px; border-radius: 6px; border: 1.5px solid {{ $isSelected ? '#3B82F6' : 'rgba(255, 255, 255, 0.25)' }}; background: {{ $isSelected ? '#2563EB' : 'transparent' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #FFFFFF; font-size: 11px; transition: all .2s ease;">
                                <i class="fa-solid fa-check" style="display: {{ $isSelected ? 'block' : 'none' }};"></i>
                            </div>

                            <!-- Content -->
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                                    <div style="font-weight: 700; color: #FFFFFF; font-size: 14px; line-height: 1.3;">
                                        {{ $property->property_name }}
                                    </div>
                                    @if($property->property_code || $isShortUnit)
                                        <span style="font-size: 10.5px; font-weight: 700; background: rgba(59, 130, 246, 0.18); border: 1px solid rgba(59, 130, 246, 0.35); padding: 1px 7px; border-radius: 4px; color: #93C5FD; flex-shrink: 0; white-space: nowrap;">
                                            {{ $property->property_code ?: '#'.$property->unit_no }}
                                        </span>
                                    @endif
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 6px; gap: 8px; flex-wrap: wrap;">
                                    <div style="font-size: 11.5px; font-weight: 600; color: #60A5FA; display: flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-city" style="font-size: 10px; color: #93C5FD;"></i>
                                        <span>{{ $proj->project_name }}</span>
                                    </div>
                                    <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 2px 7px; border-radius: 4px; background: {{ $statusBg }}; color: {{ $statusColor }}; flex-shrink: 0;">
                                        {{ ucfirst($property->status ?? 'available') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach

                @if(!$hasAnyPlots)
                    <div style="grid-column: 1 / -1; padding: 20px; text-align: center; color: #94A3B8; font-size: 13px;">
                        No specific plots/units found for available projects.
                    </div>
                @endif
            </div>
            <div id="noPlotsFoundMsg" style="display: none; padding: 16px; text-align: center; color: #94A3B8; font-size: 13px;">
                No plots/units match the selected project(s) or search filter.
            </div>
            <div id="plotsSelectedSummary" style="font-size: 12px; color: #94A3B8; margin-top: 6px; font-weight: 600;">
                Selected: <span id="plotsSelectedCount" style="color: #60A5FA;">{{ count($oldPropertyIds) }}</span> plot(s)/unit(s)
            </div>
        </div>
    </div>

    <div class="section-heading"><i class="fa-solid fa-user-shield"></i> Contractor & ID Details</div>
    <div class="form-grid">
        <div class="form-group">
            <label class="form-label" for="contractor_name">Contractor Name <span>*</span></label>
            <input type="text" name="contractor_name" id="contractor_name" value="{{ old('contractor_name', $contractor->contractor_name) }}"
                   class="form-control @error('contractor_name') is-invalid @enderror" placeholder="Enter contractor name" required>
            @error('contractor_name')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="mobile">Mobile Number</label>
            <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $contractor->mobile) }}"
                   class="form-control @error('mobile') is-invalid @enderror" placeholder="10-digit mobile number" maxlength="15">
            @error('mobile')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="aadhar_no">Aadhar Card Number</label>
            <input type="text" name="aadhar_no" id="aadhar_no" value="{{ old('aadhar_no', $contractor->aadhar_no) }}"
                   class="form-control @error('aadhar_no') is-invalid @enderror" placeholder="12-digit Aadhar number" maxlength="20">
            @error('aadhar_no')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="pan_no">PAN Card Number</label>
            <input type="text" name="pan_no" id="pan_no" value="{{ old('pan_no', $contractor->pan_no) }}"
                   class="form-control @error('pan_no') is-invalid @enderror" placeholder="10-character PAN (e.g. ABCDE1234F)" maxlength="20">
            @error('pan_no')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="section-heading"><i class="fa-solid fa-building-columns"></i> Bank Details</div>
    <div class="form-grid">
        <div class="form-group">
            <label class="form-label" for="bank_name">Bank Name</label>
            <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $contractor->bank_name) }}"
                   class="form-control @error('bank_name') is-invalid @enderror" placeholder="e.g. State Bank of India, HDFC Bank">
            @error('bank_name')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="account_number">Bank Account Number</label>
            <input type="text" name="account_number" id="account_number" value="{{ old('account_number', $contractor->account_number) }}"
                   class="form-control @error('account_number') is-invalid @enderror" placeholder="Enter bank account number">
            @error('account_number')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="ifsc_code">IFSC Code</label>
            <input type="text" name="ifsc_code" id="ifsc_code" value="{{ old('ifsc_code', $contractor->ifsc_code) }}"
                   class="form-control @error('ifsc_code') is-invalid @enderror" placeholder="e.g. SBIN0001234" maxlength="20">
            @error('ifsc_code')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="branch_name">Branch / City</label>
            <input type="text" name="branch_name" id="branch_name" value="{{ old('branch_name', $contractor->branch_name) }}"
                   class="form-control @error('branch_name') is-invalid @enderror" placeholder="e.g. Bharuch, Main Branch">
            @error('branch_name')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="section-heading"><i class="fa-solid fa-location-dot"></i> Address & Status</div>
    <div class="form-grid">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label class="form-label" for="address">Address</label>
            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror"
                      placeholder="Contractor full address...">{{ old('address', $contractor->address) }}</textarea>
            @error('address')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="status">Status <span>*</span></label>
            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="active" {{ old('status', $contractor->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $contractor->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-action-buttons">
        <button type="submit" class="btn-pc"><i class="fa-solid fa-save"></i> Update Contractor</button>
        <a href="{{ route('contractors.index') }}" class="btn-sc">Cancel</a>
    </div>
</div>
</form>

<script>
function getSelectedFirmIds() {
    const firmSelect = document.getElementById('firm_ids');
    if (firmSelect) {
        if (firmSelect.multiple) {
            return Array.from(firmSelect.selectedOptions).map(o => parseInt(o.value)).filter(Boolean);
        } else if (firmSelect.value) {
            return [parseInt(firmSelect.value)];
        }
        return [];
    }
    const hiddenFirms = document.querySelectorAll('input[name="firm_ids[]"], input[name="firm_id"]');
    const ids = [];
    hiddenFirms.forEach(input => {
        if (input.value) ids.push(parseInt(input.value));
    });
    return [...new Set(ids)];
}

function filterProjectsByFirm() {
    const projSelect = document.getElementById('project_ids');
    if (!projSelect) return;

    const selectedFirms = getSelectedFirmIds();
    const options = projSelect.querySelectorAll('option');

    options.forEach(opt => {
        if (!opt.value) return;
        const firmIdsStr = opt.getAttribute('data-firm-ids') || '';
        const firmIds = firmIdsStr.split(',').map(s => parseInt(s.trim())).filter(Boolean);

        let match = false;
        if (selectedFirms.length === 0 || firmIds.length === 0) {
            match = true;
        } else {
            match = firmIds.some(f => selectedFirms.includes(f));
        }

        if (match) {
            opt.disabled = false;
            opt.hidden = false;
        } else {
            opt.disabled = true;
            opt.hidden = true;
            opt.selected = false;
        }
    });

    if (window.jQuery && $(projSelect).data('select2')) {
        $(projSelect).trigger('change');
    }
}

function getSelectedProjectIds() {
    const projSelect = document.getElementById('project_ids');
    if (projSelect) {
        if (projSelect.multiple) {
            return Array.from(projSelect.selectedOptions).map(o => parseInt(o.value)).filter(Boolean);
        } else if (projSelect.value) {
            return [parseInt(projSelect.value)];
        }
    }
    return [];
}

function togglePlotCardSelection(card) {
    const propertyId = card.getAttribute('data-property-id');
    const isSelected = card.classList.contains('is-selected');
    const selectEl = document.getElementById('property_ids');
    if (!selectEl) return;

    let opt = selectEl.querySelector(`option[value="${propertyId}"]`);
    if (!opt) {
        opt = document.createElement('option');
        opt.value = propertyId;
        selectEl.appendChild(opt);
    }

    if (isSelected) {
        card.classList.remove('is-selected');
        card.style.background = 'rgba(20, 27, 41, 0.65)';
        card.style.borderColor = 'rgba(255, 255, 255, 0.10)';
        card.style.boxShadow = 'none';
        const check = card.querySelector('.plot-check-box');
        if (check) {
            check.style.background = 'transparent';
            check.style.borderColor = 'rgba(255, 255, 255, 0.25)';
            const icon = check.querySelector('i');
            if (icon) icon.style.display = 'none';
        }
        opt.selected = false;
    } else {
        card.classList.add('is-selected');
        card.style.background = 'rgba(37, 99, 235, 0.22)';
        card.style.borderColor = '#3B82F6';
        card.style.boxShadow = '0 0 14px rgba(59, 130, 246, 0.35)';
        const check = card.querySelector('.plot-check-box');
        if (check) {
            check.style.background = '#2563EB';
            check.style.borderColor = '#3B82F6';
            const icon = check.querySelector('i');
            if (icon) icon.style.display = 'block';
        }
        opt.selected = true;
    }

    updatePlotsSummary();
}

function updatePlotsSummary() {
    const selectedCards = document.querySelectorAll('.plot-card-item.is-selected');
    const countSpan = document.getElementById('plotsSelectedCount');
    if (countSpan) {
        countSpan.textContent = selectedCards.length;
    }
}

function filterPlots() {
    const selectedProjects = getSelectedProjectIds();
    const searchTerm = (document.getElementById('plotSearchInput')?.value || '').toLowerCase().trim();
    const cards = document.querySelectorAll('.plot-card-item');
    let visibleCount = 0;

    cards.forEach(card => {
        const projId = parseInt(card.getAttribute('data-project-id'));
        const searchData = (card.getAttribute('data-search') || '').toLowerCase();

        let projectMatch = false;
        if (selectedProjects.length === 0) {
            projectMatch = true;
        } else {
            projectMatch = selectedProjects.includes(projId);
        }

        let searchMatch = true;
        if (searchTerm) {
            searchMatch = searchData.includes(searchTerm);
        }

        if (projectMatch && searchMatch) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const noPlotsMsg = document.getElementById('noPlotsFoundMsg');
    const gridEl = document.getElementById('plotsCardsGrid');
    if (noPlotsMsg && gridEl) {
        if (visibleCount === 0 && cards.length > 0) {
            noPlotsMsg.style.display = 'block';
        } else {
            noPlotsMsg.style.display = 'none';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Select All Projects
    const btnSelectAll = document.getElementById('btnSelectAllProjects');
    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', () => {
            const projSelect = document.getElementById('project_ids');
            if (!projSelect) return;
            Array.from(projSelect.options).forEach(opt => {
                if (!opt.disabled && !opt.hidden && opt.value) {
                    opt.selected = true;
                }
            });
            if (window.jQuery && $(projSelect).data('select2')) {
                $(projSelect).trigger('change');
            }
            filterPlots();
        });
    }

    // Clear Projects
    const btnClear = document.getElementById('btnClearProjects');
    if (btnClear) {
        btnClear.addEventListener('click', () => {
            const projSelect = document.getElementById('project_ids');
            if (!projSelect) return;
            Array.from(projSelect.options).forEach(opt => opt.selected = false);
            if (window.jQuery && $(projSelect).data('select2')) {
                $(projSelect).trigger('change');
            }
            filterPlots();
        });
    }

    // Plot Search input
    const plotSearch = document.getElementById('plotSearchInput');
    if (plotSearch) {
        plotSearch.addEventListener('input', filterPlots);
    }

    // Select All Visible Plots
    const btnSelectAllPlots = document.getElementById('btnSelectAllPlots');
    if (btnSelectAllPlots) {
        btnSelectAllPlots.addEventListener('click', () => {
            document.querySelectorAll('.plot-card-item').forEach(card => {
                if (card.style.display !== 'none' && !card.classList.contains('is-selected')) {
                    togglePlotCardSelection(card);
                }
            });
        });
    }

    // Clear All Plots
    const btnClearPlots = document.getElementById('btnClearPlots');
    if (btnClearPlots) {
        btnClearPlots.addEventListener('click', () => {
            document.querySelectorAll('.plot-card-item.is-selected').forEach(card => {
                togglePlotCardSelection(card);
            });
        });
    }

    filterProjectsByFirm();
    filterPlots();

    if (window.jQuery && $('#project_ids').length) {
        $('#project_ids').on('change select2:select select2:unselect', function() {
            filterPlots();
        });
    }

    if (window.jQuery && $('#firm_ids').length) {
        $('#firm_ids').on('change select2:select select2:unselect', function() {
            filterProjectsByFirm();
            filterPlots();
        });
    }
    const firmEl = document.getElementById('firm_ids');
    if (firmEl) {
        firmEl.addEventListener('change', () => {
            filterProjectsByFirm();
            filterPlots();
        });
    }
});
</script>
@endsection
