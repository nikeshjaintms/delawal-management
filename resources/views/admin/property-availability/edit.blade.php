@extends('admin.layouts.app')
@section('title','Edit Property Status Record')
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
.crud-title p { font-size: 14px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }

.form-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 28px 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 24px;
    max-width: 780px;
}

.section-heading {
    font-size: 13.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #60A5FA !important;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
@media(max-width:640px){.form-grid{grid-template-columns:1fr}}
.form-group { margin-bottom: 18px; }
.form-group:last-child { margin-bottom: 0; }

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #CBD5E1 !important;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.form-label span { color: #F87171; }

.form-control, select.form-control, input[type="text"].form-control, input[type="date"].form-control, textarea.form-control {
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

textarea.form-control { resize: vertical; min-height: 90px; }
.text-error { color: #F87171; font-size: 12.5px; margin-top: 5px; font-weight: 600; }
.form-action-buttons { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }

/* Property info live preview (Luxury Dark Glass) */
.prop-info-box {
    background: rgba(255, 255, 255, 0.05) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 14px !important;
    padding: 14px 18px !important;
    margin-top: 14px !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.20) !important;
    display: none;
}
.prop-info-box .pi-row { display: flex; gap: 20px; flex-wrap: wrap; align-items: center; }
.prop-info-box .pi-item { font-size: 13.5px; color: #94A3B8 !important; font-weight: 600; }
.prop-info-box .pi-item strong { color: #FFFFFF !important; font-weight: 800; margin-left: 4px; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Status Record</h2>
        <p>Updating: <strong>{{ $record->property->property_name ?? '—' }}</strong></p>
    </div>
    <a href="{{ route('property-availability.index') }}" class="btn-sc"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('property-availability.update', $record) }}">
@csrf @method('PUT')

<div class="form-card">
    @include('admin.components.firm-select', ['model' => $record])

    <div class="section-heading"><i class="fa-solid fa-circle-check"></i> Status Information</div>

    <div class="form-grid" style="margin-bottom:18px">
        <div class="form-group">
            <label class="form-label" for="project_id">Project <span>*</span></label>
            <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror" required onchange="onProjectChange(this.value)">
                <option value="">— Select Project —</option>
            </select>
            @error('project_id')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="property_id">Property <span>*</span></label>
            <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror" required onchange="showPropInfo(this)">
                <option value="">— Select Project First —</option>
            </select>
            @error('property_id')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="grid-column:1/-1; margin-top:-6px; margin-bottom:0;">
            {{-- Live property info preview --}}
            <div class="prop-info-box" id="propInfoBox">
                <div class="pi-row">
                    <div class="pi-item">Project: <strong id="piProject">—</strong></div>
                    <div class="pi-item">Type: <strong id="piType">—</strong></div>
                    <div class="pi-item">Unit / Plot No: <strong id="piUnit">—</strong></div>
                    <div class="pi-item">Current Status: <strong id="piStatus">—</strong></div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">New Status <span>*</span></label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="">— Select Status —</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ old('status', $record->status) == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('status')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Status Date <span>*</span></label>
            <input type="date" name="status_date"
                   value="{{ old('status_date', $record->status_date->format('Y-m-d')) }}"
                   class="form-control" required>
            @error('status_date')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks', $record->remarks) }}</textarea>
        @error('remarks')<div class="text-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-action-buttons">
        <button type="submit" class="btn-pc"><i class="fa fa-save"></i> Update Status</button>
        <a href="{{ route('property-availability.index') }}" class="btn-sc">Cancel</a>
    </div>
</div>
</form>

<script>
const allProjects = [
    @foreach($projects as $proj)
    @php
        $projFirmIds = $proj->firms->pluck('id')->push($proj->firm_id)->filter()->unique()->values()->all();
        $pTitle = $proj->project_name . ($proj->propertyMaster ? ' ('.$proj->propertyMaster->property_name.')' : '');
    @endphp
    {
        id: {{ $proj->id }},
        name: @json($pTitle),
        firmIds: [{{ implode(',', $projFirmIds) }}]
    },
    @endforeach
];

const allProperties = [
    @foreach($properties as $p)
    @php
        $propFirmIds = $p->firms->pluck('id')->push($p->firm_id)->filter()->unique()->values()->all();
        $pName = $p->property_name . ($p->unit_no ? ' (Unit: '.$p->unit_no.')' : ($p->property_code ? ' ('.$p->property_code.')' : ''));
        $pProjTitle = $p->project->project_name ?? ($p->project->propertyMaster->property_name ?? 'No Project Assigned');
    @endphp
    {
        id: {{ $p->id }},
        projectId: {{ $p->project_id ?: 'null' }},
        firmIds: [{{ implode(',', $propFirmIds) }}],
        name: @json($pName),
        project: @json($pProjTitle),
        type: @json($p->propertyType->name ?? '—'),
        unit: @json($p->unit_no ?? '—'),
        code: @json($p->property_code ?? ''),
        status: @json(ucfirst(str_replace('_',' ',$p->status)))
    },
    @endforeach
];

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

function filterProjects(keepSelectedProjectId = null, keepSelectedPropertyId = null) {
    const projSelect = document.getElementById('project_id');
    if (!projSelect) return;

    const selectedFirms = getSelectedFirmIds();
    const currentVal = keepSelectedProjectId !== null ? keepSelectedProjectId : projSelect.value;

    let filteredProjects = allProjects;
    if (selectedFirms.length > 0) {
        filteredProjects = allProjects.filter(p => {
            if (!p.firmIds || p.firmIds.length === 0) return true;
            return p.firmIds.some(fId => selectedFirms.includes(fId));
        });
    }

    projSelect.innerHTML = '<option value="">— Select Project —</option>';
    let selectedStillValid = false;

    filteredProjects.forEach(proj => {
        const opt = document.createElement('option');
        opt.value = proj.id;
        opt.textContent = proj.name;
        if (currentVal && String(proj.id) === String(currentVal)) {
            opt.selected = true;
            selectedStillValid = true;
        }
        projSelect.appendChild(opt);
    });

    if (!selectedStillValid && currentVal) {
        projSelect.value = '';
    }

    onProjectChange(projSelect.value, keepSelectedPropertyId);
}

function onProjectChange(projectId, keepSelectedPropertyId = null) {
    const propSelect = document.getElementById('property_id');
    const propInfoBox = document.getElementById('propInfoBox');
    if (!propSelect) return;

    const currentPropVal = keepSelectedPropertyId !== null ? keepSelectedPropertyId : propSelect.value;

    if (!projectId) {
        propSelect.innerHTML = '<option value="">— Select Project First —</option>';
        propSelect.disabled = true;
        if (propInfoBox) propInfoBox.style.display = 'none';
        return;
    }

    propSelect.disabled = false;
    const projectProps = allProperties.filter(p => String(p.projectId) === String(projectId));

    if (projectProps.length === 0) {
        propSelect.innerHTML = '<option value="">— No properties found in this project —</option>';
        if (propInfoBox) propInfoBox.style.display = 'none';
        return;
    }

    propSelect.innerHTML = '<option value="">— Select Property —</option>';
    let selectedStillValid = false;

    projectProps.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = p.name;
        if (currentPropVal && String(p.id) === String(currentPropVal)) {
            opt.selected = true;
            selectedStillValid = true;
        }
        propSelect.appendChild(opt);
    });

    if (selectedStillValid && propSelect.value) {
        showPropInfo(propSelect);
    } else {
        propSelect.value = '';
        if (propInfoBox) propInfoBox.style.display = 'none';
    }
}

function showPropInfo(sel) {
    const box = document.getElementById('propInfoBox');
    const id  = parseInt(sel.value);
    const d = allProperties.find(p => p.id === id);

    if (!id || !d || !box) {
        if (box) box.style.display = 'none';
        return;
    }

    document.getElementById('piProject').textContent = d.project;
    document.getElementById('piType').textContent    = d.type;
    document.getElementById('piUnit').textContent    = d.unit;
    document.getElementById('piStatus').textContent  = d.status;
    box.style.display = 'block';
}

document.addEventListener('DOMContentLoaded', () => {
    let initialPropId = "{{ old('property_id', $record->property_id) }}";
    let initialProjId = "{{ old('project_id', $record->property->project_id ?? '') }}";

    if (!initialProjId && initialPropId) {
        const found = allProperties.find(p => String(p.id) === String(initialPropId));
        if (found && found.projectId) {
            initialProjId = found.projectId;
        }
    }

    filterProjects(initialProjId, initialPropId);

    if (window.jQuery && $('#firm_ids').length) {
        $('#firm_ids').on('change select2:select select2:unselect', function() {
            filterProjects();
        });
    }
    const firmEl = document.getElementById('firm_ids');
    if (firmEl) {
        firmEl.addEventListener('change', () => filterProjects());
    }
});
</script>
@endsection
