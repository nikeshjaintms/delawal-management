@extends('admin.layouts.app')

@section('title', 'Add Project')
@section('page-title', 'Project Master')

@section('content')
<style>
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .crud-title h2 {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 4px;
    }
    .crud-title p {
        font-size: 13.5px;
        color: var(--text-secondary);
    }
    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 30px;
        box-shadow: var(--soft-shadow);
        max-width: 960px;
    }
    .form-group { margin-bottom: 20px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; gap: 0; } }

    .form-label {
        display: block; font-size: 13.5px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px;
    }
    .form-label span { color: #EF4444; }
    .form-control {
        width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 8px;
        font-size: 14px; color: var(--text-primary); background-color: var(--input-bg, #FFFFFF); outline: none; transition: var(--transition);
    }
    .form-control:focus { border-color: var(--gold); box-shadow: 0 0 0 3px var(--gold-light); }
    textarea.form-control { resize: vertical; min-height: 90px; }
    .text-error { color: #EF4444; font-size: 12.5px; margin-top: 6px; font-weight: 500; }

    .form-actions {
        display: flex; align-items: center; gap: 15px; margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border-color);
    }
    .btn-gold {
        background-color: var(--gold); color: #FFFFFF; padding: 11px 24px; border-radius: 8px;
        text-decoration: none; font-size: 14px; font-weight: 700; border: none; cursor: pointer;
        display: inline-flex; align-items: center; gap: 8px; transition: var(--transition); box-shadow: 0 4px 10px rgba(212, 175, 55, 0.2);
    }
    .btn-gold:hover { background-color: #B58D1B; transform: translateY(-1px); }

    .btn-outline {
        border: 1px solid var(--border-color); background: transparent; color: var(--text-secondary);
        padding: 11px 24px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;
        transition: var(--transition); display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-outline:hover { background: rgba(255, 255, 255, 0.08); color: var(--text-primary); }

    /* ── Property & Plot Selection Box ── */
    .plots-selector-box {
        background: rgba(37, 99, 235, 0.05);
        border: 1px solid rgba(59, 130, 246, 0.25);
        border-radius: 14px;
        padding: 22px;
        margin-top: 24px;
        margin-bottom: 24px;
    }
    .selection-summary-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(59, 130, 246, 0.15);
        border: 1px solid rgba(59, 130, 246, 0.35);
        border-radius: 10px;
        padding: 10px 16px;
        margin-bottom: 16px;
        color: #FFFFFF;
        font-size: 13.5px;
        font-weight: 700;
        flex-wrap: wrap;
        gap: 8px;
    }
    .property-selection-card {
        background: rgba(20, 27, 41, 0.60);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 14px;
    }
    .property-selection-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding-bottom: 10px;
        margin-bottom: 12px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .plots-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
    }
    .plot-check-label {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.10);
        border-radius: 8px;
        padding: 8px 12px;
        cursor: pointer;
        transition: all .15s ease;
        user-select: none;
    }
    .plot-check-label:hover {
        background: rgba(59, 130, 246, 0.12);
        border-color: rgba(59, 130, 246, 0.40);
    }
    .plot-check-label.selected {
        background: rgba(59, 130, 246, 0.20);
        border-color: #3B82F6;
    }
    .plot-check-input {
        margin-top: 3px;
        cursor: pointer;
    }
    .btn-toggle-all {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: #CBD5E1;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
    }
    .btn-toggle-all:hover { background: rgba(59, 130, 246, 0.25); color: #FFFFFF; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Project</h2>
        <p>Create a new project and select plots across one or multiple Property Masters.</p>
    </div>
    <a href="{{ route('projects.index') }}" class="btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Back to Projects
    </a>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" id="projectCreateForm">
        @csrf
        @include('admin.components.firm-select')

        @php
            $preSelectedIds = old('property_ids', (array)($selectedPropertyIds ?? []));
        @endphp

        <div class="form-group">
            <label class="form-label" for="property_ids">Select Property Master(s) <span>*</span></label>
            <select name="property_ids[]" id="property_ids" class="form-control select2-multi @error('property_ids') is-invalid @enderror" multiple required data-placeholder="Choose one or more Property Masters..." onchange="onPropertyMastersChange()">
                @if(isset($properties))
                    @foreach($properties as $prop)
                        <option value="{{ $prop->id }}" {{ in_array($prop->id, $preSelectedIds) ? 'selected' : '' }}>
                            {{ $prop->property_name }} ({{ $prop->property_code }}) - {{ $prop->city ?: 'No city' }}
                        </option>
                    @endforeach
                @endif
            </select>
            <small style="color: #94A3B8; font-size: 12px; margin-top: 4px; display: block;">
                <i class="fa-solid fa-circle-info"></i> You can select multiple properties to combine into this single project.
            </small>
            @error('property_ids') <div class="text-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="project_name">Project Name <span>*</span></label>
                <input type="text" name="project_name" id="project_name" value="{{ old('project_name') }}" class="form-control @error('project_name') is-invalid @enderror" placeholder="e.g. Galaxy Heights" required>
                @error('project_name') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="project_code">Project Code</label>
                <input type="text" name="project_code" id="project_code" value="{{ old('project_code') }}" class="form-control @error('project_code') is-invalid @enderror" placeholder="Auto-generated if empty">
                @error('project_code') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <!-- ── Interactive Multi-Property Plot Selection Section ── -->
        <div class="plots-selector-box" id="plotsSelectorSection" style="display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <strong style="font-size: 15px; color: #FFFFFF; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-layer-group" style="color: #60A5FA;"></i>
                        Available Plots from Selected Property Masters
                    </strong>
                    <span style="font-size: 12.5px; color: #94A3B8; display: block; margin-top: 2px;">
                        Choose which plots to assign into this Project.
                    </span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <button type="button" class="btn-toggle-all" onclick="toggleAllPlotsAcrossProperties()" style="padding: 6px 14px; font-size: 12.5px; background: rgba(59, 130, 246, 0.20); border-color: #3B82F6; color: #93C5FD;">
                        <i class="fa-solid fa-check-double"></i> Select All Available Plots
                    </button>
                    <div class="selection-summary-bar" style="margin-bottom: 0;">
                        <span><i class="fa-solid fa-circle-check" style="color: #34D399; margin-right: 4px;"></i> Selected: <span id="selectedCountBadge" style="color: #FBBF24;">0</span> plots</span>
                    </div>
                </div>
            </div>

            <div id="propertiesPlotsContainer">
                <!-- Dynamically populated properties and plots -->
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="project_type">Project Type <span>*</span></label>
                <input type="text" name="project_type" id="project_type" value="{{ old('project_type', 'Plotted Development') }}" class="form-control @error('project_type') is-invalid @enderror" placeholder="e.g. Plotted Development, Residential" required>
                @error('project_type') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status <span>*</span></label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', 'inactive') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <!-- ── Auto-Fetched Property Master Address Preview Box ── -->
        <div class="property-address-box" id="propertyAddressBox" style="display: none; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.28); border-radius: 14px; padding: 16px 20px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
                <label class="form-label" style="margin-bottom: 0; color: #60A5FA; font-size: 12px; text-transform: uppercase; letter-spacing: 0.6px; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-location-dot"></i> Property Master Address Summary
                </label>
                <span style="font-size: 11.5px; color: #34D399; font-weight: 700;">● Auto-Fetched</span>
            </div>
            <div id="propertyAddressDisplay" style="color: #FFFFFF; font-weight: 700; font-size: 14px; line-height: 1.4;">
                <!-- Filled automatically -->
            </div>
        </div>

        <div class="form-group" id="manualAddressGroup">
            <label class="form-label" for="address">Site Address / Remarks</label>
            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Site location notes or specific address">{{ old('address') }}</textarea>
            @error('address') <div class="text-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="city">City</label>
                <input type="text" name="city" id="city" value="{{ old('city') }}" class="form-control @error('city') is-invalid @enderror" placeholder="Enter city">
                @error('city') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="state">State</label>
                <input type="text" name="state" id="state" value="{{ old('state') }}" class="form-control @error('state') is-invalid @enderror" placeholder="Enter state">
                @error('state') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="country">Country</label>
                <input type="text" name="country" id="country" value="{{ old('country', 'India') }}" class="form-control @error('country') is-invalid @enderror" placeholder="Enter country">
                @error('country') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="pincode">Pincode</label>
                <input type="text" name="pincode" id="pincode" value="{{ old('pincode') }}" class="form-control @error('pincode') is-invalid @enderror" placeholder="Enter pincode">
                @error('pincode') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" placeholder="Enter project description">{{ old('description') }}</textarea>
            @error('description') <div class="text-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="project_image">Project Image</label>
            <input type="file" name="project_image" id="project_image" class="form-control @error('project_image') is-invalid @enderror" accept="image/*">
            @error('project_image') <div class="text-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Save Project &amp; Assign Selected Plots
            </button>
            <a href="{{ route('projects.index') }}" class="btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    onPropertyMastersChange();
});

function getSelectedPropertyIds() {
    const select = document.getElementById('property_ids');
    if (!select) return [];
    return Array.from(select.selectedOptions).map(opt => opt.value).filter(Boolean);
}

function onPropertyMastersChange() {
    const propertyIds = getSelectedPropertyIds();
    loadPropertiesAndPlots(propertyIds);
}

function loadPropertiesAndPlots(propertyIds) {
    const section = document.getElementById('plotsSelectorSection');
    const container = document.getElementById('propertiesPlotsContainer');
    const addrBox = document.getElementById('propertyAddressBox');
    const addrDisplay = document.getElementById('propertyAddressDisplay');

    if (!propertyIds || propertyIds.length === 0) {
        section.style.display = 'none';
        if (addrBox) addrBox.style.display = 'none';
        container.innerHTML = '';
        return;
    }

    container.innerHTML = '<div style="color: #94A3B8; padding: 16px; text-align: center;"><i class="fa-solid fa-spinner fa-spin"></i> Loading available plots from selected Property Masters...</div>';
    section.style.display = 'block';

    fetch('/projects/properties-and-plots?property_ids=' + propertyIds.join(','))
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.properties || data.properties.length === 0) {
                container.innerHTML = '<div style="color: #94A3B8; padding: 14px; font-size: 13.5px; text-align: center;">No plots found for selected Property Masters.</div>';
                updateSelectedCount();
                return;
            }

            // Address display summary
            if (addrBox && addrDisplay) {
                const addrs = data.properties.map(p => `<strong>${p.property_name}:</strong> ${p.full_address || p.address || p.city || '—'}`).join('<br>');
                addrDisplay.innerHTML = addrs;
                addrBox.style.display = 'block';

                const firstProp = data.properties[0];
                const cityInput = document.getElementById('city');
                const stateInput = document.getElementById('state');
                const pinInput = document.getElementById('pincode');
                if (cityInput && !cityInput.value) cityInput.value = firstProp.city || '';
                if (stateInput && !stateInput.value) stateInput.value = firstProp.state || '';
                if (pinInput && !pinInput.value) pinInput.value = firstProp.pincode || '';
            }

            let html = '';
            data.properties.forEach(pm => {
                const plots = pm.plots || [];
                html += `
                <div class="property-selection-card">
                    <div class="property-selection-header">
                        <div>
                            <strong style="color: #FFFFFF; font-size: 14.5px;">${pm.property_name}</strong>
                            <code style="background: rgba(59, 130, 246, 0.18); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.35); padding: 2px 6px; border-radius: 4px; font-size: 11.5px; margin-left: 6px;">${pm.property_code}</code>
                            <span style="color: #94A3B8; font-size: 12px; margin-left: 8px;">(${plots.length} available plots)</span>
                        </div>
                        <div>
                            <button type="button" class="btn-toggle-all" onclick="togglePropertyPlotsSelection(${pm.id})">
                                Select All in Property (${plots.length})
                            </button>
                        </div>
                    </div>
                    <div class="plots-grid" id="pm_plots_${pm.id}">
                `;

                if (plots.length === 0) {
                    html += '<div style="grid-column: 1/-1; color: #94A3B8; font-size: 12.5px; padding: 6px;">No available plots found under this Property Master.</div>';
                } else {
                    plots.forEach(plot => {
                        const sizeStr = plot.size ? ` • ${plot.size} ${plot.size_unit || ''}` : '';
                        const facingStr = plot.facing ? ` • ${plot.facing}` : '';
                        html += `
                        <label class="plot-check-label" id="label_plot_${plot.id}">
                            <input type="checkbox" name="selected_plot_ids[]" value="${plot.id}" class="plot-check-input pm-chk-${pm.id} all-plots-chk" onchange="onPlotCheckChange(this, ${plot.id})">
                            <div style="font-size: 12.5px; line-height: 1.3;">
                                <strong style="color: #FFFFFF; display: block; font-size: 13px;">${plot.property_name}</strong>
                                <code style="font-size: 11px; color: #60A5FA; display: block; margin: 1px 0;">${plot.property_code}</code>
                                <span style="color: #94A3B8; font-size: 11px;">₹${parseFloat(plot.purchase_rate || pm.purchase_rate || 0).toLocaleString('en-IN')}${sizeStr}${facingStr}</span>
                            </div>
                        </label>
                        `;
                    });
                }

                html += `</div></div>`;
            });

            container.innerHTML = html;
            updateSelectedCount();
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = '<div style="color: #EF4444; padding: 10px;">Error loading plots.</div>';
        });
}

function onPlotCheckChange(chk, plotId) {
    const label = document.getElementById('label_plot_' + plotId);
    if (chk.checked) {
        label.classList.add('selected');
    } else {
        label.classList.remove('selected');
    }
    updateSelectedCount();
}

function togglePropertyPlotsSelection(pmId) {
    const checkboxes = document.querySelectorAll('.pm-chk-' + pmId);
    const allChecked = Array.from(checkboxes).every(c => c.checked);

    checkboxes.forEach(c => {
        c.checked = !allChecked;
        const label = document.getElementById('label_plot_' + c.value);
        if (label) {
            if (!allChecked) label.classList.add('selected');
            else label.classList.remove('selected');
        }
    });

    updateSelectedCount();
}

function toggleAllPlotsAcrossProperties() {
    const checkboxes = document.querySelectorAll('.all-plots-chk');
    const allChecked = Array.from(checkboxes).every(c => c.checked);

    checkboxes.forEach(c => {
        c.checked = !allChecked;
        const label = document.getElementById('label_plot_' + c.value);
        if (label) {
            if (!allChecked) label.classList.add('selected');
            else label.classList.remove('selected');
        }
    });

    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('input[name="selected_plot_ids[]"]:checked');
    const badge = document.getElementById('selectedCountBadge');
    if (badge) {
        badge.textContent = checked.length;
    }
}
</script>
@endsection
