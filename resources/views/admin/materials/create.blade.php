@extends('admin.layouts.app')
@section('title','Add Material')
@section('page-title','Inventory Management')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 4px; letter-spacing: -0.3px; }
.crud-title p { font-size: 13.5px; color: #CBD5E1 !important; font-weight: 500; }

.btn-gold {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 22px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-2px); }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 30px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; max-width: 860px; margin: 0 auto 28px;
}

.section-title {
    font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
    color: #60A5FA !important; margin-bottom: 20px; padding-bottom: 10px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important; display: flex; align-items: center; gap: 8px;
}
.form-section { margin-bottom: 24px; }
.form-group { margin-bottom: 18px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; }
.form-row-4 { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 18px; }
@media(max-width:900px) { .form-row-4 { grid-template-columns: 1fr 1fr; } }
@media(max-width:768px) { .form-row-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px) { .form-row, .form-row-3, .form-row-4 { grid-template-columns: 1fr; gap: 0; } }

.form-label { display: block; font-size: 13px; font-weight: 700; color: #FFFFFF !important; margin-bottom: 7px; }
.form-label span { color: #F87171; }

.form-control {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: border-color .18s, box-shadow .18s;
}
.form-control option { background: #101622 !important; color: #FFFFFF !important; }
.form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.text-error { color: #F87171; font-size: 12px; margin-top: 5px; font-weight: 500; }
.form-hint { font-size: 11.5px; color: #CBD5E1 !important; margin-top: 5px; }
.form-actions { display: flex; align-items: center; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Material</h2>
        <p>Register a new construction material item in inventory.</p>
    </div>
</div>

<div class="card-box">  
    <form method="POST" action="{{ route('materials.store') }}" autocomplete="off">
        @csrf
        @include('admin.components.firm-select')
        
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-boxes-stacked"></i> 1. Material Classification</div>
            
            <div class="form-row">
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 7px;">
                        <label class="form-label" for="material_category_id" style="margin-bottom: 0;">Material Category <span>*</span></label>
                        <button type="button" id="toggle_custom_cat_btn" style="background: none; border: none; color: #60A5FA; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: underline; padding: 0;">
                            <i class="fa-solid fa-pen-to-square"></i> <span id="toggle_cat_text">Or Enter Manually</span>
                        </button>
                    </div>
                    <select name="material_category_id" id="material_category_id" class="form-control @error('material_category_id') is-invalid @enderror">
                        <option value="">— Select Category (e.g. Steel, Cement, Sand) —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" data-name="{{ $cat->category_name }}" {{ old('material_category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->category_name }}
                            </option>
                        @endforeach
                        <option value="__custom__" {{ old('custom_category') ? 'selected' : '' }}>➕ + Enter Custom Category Manually</option>
                    </select>

                    <div id="custom_cat_box" style="margin-top: 10px; display: {{ old('custom_category') ? 'block' : 'none' }};">
                        <input type="text" name="custom_category" id="custom_category" value="{{ old('custom_category') }}" class="form-control" placeholder="Type custom category name (e.g. Glass, Wood, Chemical)...">
                        <div class="form-hint" style="color: #60A5FA !important;"><i class="fa-solid fa-check"></i> Custom category will be created and saved automatically.</div>
                    </div>
                    <div class="form-hint" id="cat_select_hint">Choose pre-defined category or enter custom manually.</div>
                    @error('material_category_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 7px;">
                        <label class="form-label" for="preset_item" style="margin-bottom: 0;">Size Specification</label>
                        <button type="button" id="toggle_custom_spec_btn" style="background: none; border: none; color: #60A5FA; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: underline; padding: 0;">
                            <i class="fa-solid fa-pen-to-square"></i> <span id="toggle_spec_text">Or Enter Manually</span>
                        </button>
                    </div>
                    <select id="preset_item" class="form-control">
                        <option value="">— Select Category to view sizes —</option>
                    </select>

                    <div id="custom_spec_box" style="margin-top: 10px; display: none;">
                        <input type="text" id="custom_spec_input" class="form-control" placeholder="Type custom size (e.g. 14mm, 5 Inch, Special Grade)...">
                        <div class="form-hint" style="color: #60A5FA !important;"><i class="fa-solid fa-check"></i> Type custom size to auto-set specification.</div>
                    </div>
                    <div class="form-hint" id="spec_select_hint">Selecting or typing a size auto-fills name, specification & unit.</div>
                </div>
            </div>

            <input type="hidden" name="material_name" id="material_name" value="{{ old('material_name') }}">
            <input type="hidden" name="specification" id="specification" value="{{ old('specification') }}">
        </div>

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-building-user"></i> 2. Project &amp; Contractor Assignment</div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="project_id">Project <span style="font-size: 11px; font-weight: normal; color: #94A3B8;">(Optional)</span></label>
                    <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror">
                        <option value="">— Select Project (Optional) —</option>
                        @if(isset($projects))
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}" {{ old('project_id') == $proj->id ? 'selected' : '' }}>
                                    {{ $proj->project_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <div class="form-hint">Select a project to auto-fetch assigned contractors.</div>
                    @error('project_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="contractor_id">Contractor / Agency <span style="font-size: 11px; font-weight: normal; color: #94A3B8;">(Optional)</span></label>
                    <select name="contractor_id" id="contractor_id" class="form-control @error('contractor_id') is-invalid @enderror">
                        <option value="">— Select Contractor (Optional) —</option>
                        @foreach($contractors as $con)
                            <option value="{{ $con->id }}"
                                    data-project-id="{{ $con->project_id }}"
                                    data-firm-id="{{ $con->firm_id }}"
                                    {{ old('contractor_id') == $con->id ? 'selected' : '' }}>
                                {{ $con->contractor_name }} {{ $con->project ? '('.$con->project->project_name.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">Assign this material to a specific contractor / project.</div>
                    @error('contractor_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-calculator"></i> 3. Quantity &amp; Price Details</div>

            <div class="form-row-4">
                <div class="form-group">
                    <label class="form-label" for="unit">Unit of Measure <span>*</span></label>
                    <input type="text" name="unit" id="unit" value="{{ old('unit') }}" class="form-control @error('unit') is-invalid @enderror" autocomplete="off" placeholder="e.g. Kg, Ton, Bags, Brass" list="unit-list" required>
                    <datalist id="unit-list">
                        <option value="Kg">
                        <option value="Ton">
                        <option value="Bags">
                        <option value="Nos">
                        <option value="Brass">
                        <option value="CBM">
                        <option value="Sq.Ft">
                        <option value="Box">
                        <option value="Metres">
                        <option value="Litres">
                        <option value="Coils">
                        <option value="Sheets">
                    </datalist>
                    @error('unit')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="opening_stock">Quantity Needed <span>*</span></label>
                    <input type="number" step="0.001" min="0" name="opening_stock" id="opening_stock" value="{{ old('opening_stock', 0) }}" class="form-control @error('opening_stock') is-invalid @enderror" placeholder="e.g. 2000" required>
                    <div class="form-hint">Required quantity for project.</div>
                    @error('opening_stock')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="unit_price">Unit Price (₹)</label>
                    <input type="number" step="0.01" min="0" name="unit_price" id="unit_price" value="{{ old('unit_price', 0) }}" class="form-control @error('unit_price') is-invalid @enderror" placeholder="0.00">
                    <div class="form-hint">Rate per unit.</div>
                    @error('unit_price')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="total_price">Total Price (₹)</label>
                    <input type="number" step="0.01" min="0" name="total_price" id="total_price" value="{{ old('total_price', 0) }}" class="form-control @error('total_price') is-invalid @enderror" placeholder="0.00" readonly style="background: rgba(16, 22, 34, 0.90) !important; color: #60A5FA !important; font-weight: 700;">
                    <div class="form-hint">Qty × Unit Price.</div>
                    @error('total_price')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <input type="hidden" name="minimum_stock" id="minimum_stock" value="0">

            <div class="form-row">
                <div class="form-group" style="max-width: 400px;">
                    <label class="form-label" for="status">Status <span>*</span></label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status','active')=='active'?'selected':'' }}>Active</option>
                        <option value="inactive" {{ old('status')=='inactive'?'selected':'' }}>Inactive</option>
                    </select>
                    @error('status')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Save Material</button>
            <a href="{{ route('materials.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── Construction Materials Catalog ──
    const catalog = @json($catalog ?? []);
    const categorySelect = document.getElementById('material_category_id');
    const presetSelect = document.getElementById('preset_item');
    const nameInput = document.getElementById('material_name');
    const specInput = document.getElementById('specification');
    const unitInput = document.getElementById('unit');

    function updatePresetDropdown() {
        if (!categorySelect || !presetSelect) return;
        const selectedOpt = categorySelect.options[categorySelect.selectedIndex];
        const catName = selectedOpt ? (selectedOpt.getAttribute('data-name') || selectedOpt.textContent.trim()) : '';

        presetSelect.innerHTML = '';
        const defaultOpt = document.createElement('option');

        if (!catName || !catalog[catName]) {
            defaultOpt.value = '';
            defaultOpt.textContent = '— Select Category to view sizes —';
            presetSelect.appendChild(defaultOpt);
            return;
        }

        const items = catalog[catName].items || [];
        defaultOpt.value = '';
        defaultOpt.textContent = `— Select Size Specification (${items.length} available) —`;
        presetSelect.appendChild(defaultOpt);

        items.forEach((item, index) => {
            const opt = document.createElement('option');
            opt.value = index;
            opt.textContent = item.label || item.spec || item.name;
            opt.setAttribute('data-name', item.name);
            opt.setAttribute('data-spec', item.spec || '');
            opt.setAttribute('data-unit', item.unit || '');
            presetSelect.appendChild(opt);
        });

        const customOpt = document.createElement('option');
        customOpt.value = '__custom__';
        customOpt.textContent = '➕ + Enter Custom Size Manually';
        presetSelect.appendChild(customOpt);
    }

    // ── Custom Category Handling ──
    const customCatBox = document.getElementById('custom_cat_box');
    const customCatInput = document.getElementById('custom_category');
    const toggleCustomBtn = document.getElementById('toggle_custom_cat_btn');
    const toggleCatText = document.getElementById('toggle_cat_text');

    function showCustomCategory() {
        if (customCatBox) customCatBox.style.display = 'block';
        if (customCatInput) customCatInput.focus();
        if (categorySelect) categorySelect.value = '__custom__';
        if (toggleCatText) toggleCatText.textContent = 'Or Choose From List';
        updatePresetDropdown();
    }

    function hideCustomCategory() {
        if (customCatBox) customCatBox.style.display = 'none';
        if (customCatInput) customCatInput.value = '';
        if (toggleCatText) toggleCatText.textContent = 'Or Enter Manually';
    }

    if (toggleCustomBtn) {
        toggleCustomBtn.addEventListener('click', function() {
            if (!customCatBox || customCatBox.style.display === 'none') {
                showCustomCategory();
            } else {
                hideCustomCategory();
                if (categorySelect && categorySelect.value === '__custom__') categorySelect.value = '';
                updatePresetDropdown();
            }
        });
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            if (categorySelect.value === '__custom__') {
                showCustomCategory();
            } else {
                hideCustomCategory();
            }
            updatePresetDropdown();
        });
    }

    // ── Custom Size Specification Handling ──
    const customSpecBox = document.getElementById('custom_spec_box');
    const customSpecInput = document.getElementById('custom_spec_input');
    const toggleCustomSpecBtn = document.getElementById('toggle_custom_spec_btn');
    const toggleSpecText = document.getElementById('toggle_spec_text');

    function showCustomSpec() {
        if (customSpecBox) customSpecBox.style.display = 'block';
        if (customSpecInput) {
            customSpecInput.focus();
            if (specInput && specInput.value) customSpecInput.value = specInput.value;
        }
        if (presetSelect) presetSelect.value = '__custom__';
        if (toggleSpecText) toggleSpecText.textContent = 'Or Choose From List';
    }

    function hideCustomSpec() {
        if (customSpecBox) customSpecBox.style.display = 'none';
        if (customSpecInput) customSpecInput.value = '';
        if (toggleSpecText) toggleSpecText.textContent = 'Or Enter Manually';
    }

    if (toggleCustomSpecBtn) {
        toggleCustomSpecBtn.addEventListener('click', function() {
            if (!customSpecBox || customSpecBox.style.display === 'none') {
                showCustomSpec();
            } else {
                hideCustomSpec();
                if (presetSelect && presetSelect.value === '__custom__') presetSelect.value = '';
            }
        });
    }

    if (customSpecInput) {
        customSpecInput.addEventListener('input', function() {
            if (specInput) specInput.value = customSpecInput.value;
        });
    }

    if (specInput) {
        specInput.addEventListener('input', function() {
            if (customSpecInput && customSpecBox && customSpecBox.style.display !== 'none') {
                customSpecInput.value = specInput.value;
            }
        });
    }

    if (presetSelect) {
        presetSelect.addEventListener('change', function() {
            if (presetSelect.value === '__custom__') {
                showCustomSpec();
                return;
            }
            hideCustomSpec();
            const selectedOpt = presetSelect.options[presetSelect.selectedIndex];
            if (selectedOpt && selectedOpt.value !== '') {
                nameInput.value = selectedOpt.getAttribute('data-name') || '';
                specInput.value = selectedOpt.getAttribute('data-spec') || '';
                unitInput.value = selectedOpt.getAttribute('data-unit') || '';
            }
        });
    }

    // ── Project & Contractor Auto-fetch & Select ──
    const projectSelect = document.getElementById('project_id');
    const contractorSelect = document.getElementById('contractor_id');
    const allContractors = @json($contractors ?? []);

    function populateAndAutoSelectContractors(contractorList, preserveId = null) {
        if (!contractorSelect) return;
        contractorSelect.innerHTML = '';

        if (contractorList.length === 0) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = (projectSelect && projectSelect.value) ? '— No contractor assigned to this project —' : '— Select Contractor (Optional) —';
            contractorSelect.appendChild(opt);
            return;
        }

        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = `— Select Contractor (${contractorList.length} available) —`;
        contractorSelect.appendChild(defaultOpt);

        let selectedValue = preserveId || '';

        contractorList.forEach((c) => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.setAttribute('data-project-id', c.project_id || '');
            opt.setAttribute('data-firm-id', c.firm_id || '');
            opt.textContent = c.contractor_name + (c.mobile ? ` (${c.mobile})` : '');
            contractorSelect.appendChild(opt);

            if (preserveId && String(c.id) === String(preserveId)) {
                selectedValue = c.id;
            } else if (!preserveId && contractorList.length === 1) {
                selectedValue = c.id;
            }
        });

        if (selectedValue) {
            contractorSelect.value = selectedValue;
        }
    }

    async function onProjectChange() {
        if (!projectSelect || !contractorSelect) return;
        const selectedProjId = projectSelect.value;
        const previousContractorId = contractorSelect.value;

        if (!selectedProjId) {
            populateAndAutoSelectContractors(allContractors);
            return;
        }

        const localFiltered = allContractors.filter(c => String(c.project_id) === String(selectedProjId));
        populateAndAutoSelectContractors(localFiltered, previousContractorId);

        try {
            const res = await fetch(`/projects/${selectedProjId}/contractors`);
            if (res.ok) {
                const freshData = await res.json();
                populateAndAutoSelectContractors(freshData, contractorSelect.value);
            }
        } catch (err) {
            console.log('Contractor fetch error:', err);
        }
    }

    if (projectSelect) {
        projectSelect.addEventListener('change', onProjectChange);
    }

    if (contractorSelect) {
        contractorSelect.addEventListener('change', function() {
            const opt = contractorSelect.options[contractorSelect.selectedIndex];
            if (opt && opt.value) {
                const pId = opt.getAttribute('data-project-id');
                if (pId && (!projectSelect.value || projectSelect.value !== pId)) {
                    projectSelect.value = pId;
                }
            }
        });
    }

    // Initialize on load
    updatePresetDropdown();
    const initialContractorId = "{{ old('contractor_id') }}";
    if (projectSelect && projectSelect.value) {
        const initialFiltered = allContractors.filter(c => String(c.project_id) === String(projectSelect.value));
        populateAndAutoSelectContractors(initialFiltered, initialContractorId);
    } else if (initialContractorId) {
        contractorSelect.value = initialContractorId;
    }

    // ── Live Calculation: Quantity × Unit Price = Total Price ──
    const qtyInput = document.getElementById('opening_stock');
    const priceInput = document.getElementById('unit_price');
    const totalInput = document.getElementById('total_price');

    function calcTotal() {
        if (!qtyInput || !priceInput || !totalInput) return;
        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        totalInput.value = (qty * price).toFixed(2);
    }

    if (qtyInput) qtyInput.addEventListener('input', calcTotal);
    if (priceInput) priceInput.addEventListener('input', calcTotal);
    calcTotal();
});
</script>
@endsection
