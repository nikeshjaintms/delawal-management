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
            <label class="form-label">Property <span>*</span></label>
            <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror" required>
                <option value="">— Select Property —</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}"
                        data-project="{{ $p->project->project_name ?? ($p->project->propertyMaster->property_name ?? 'No Project Assigned') }}"
                        {{ old('property_id', $record->property_id) == $p->id ? 'selected' : '' }}>
                        {{ $p->property_name }}{{ $p->property_code ? ' ('.$p->property_code.')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('property_id')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="project_display">Project</label>
            <input type="text" id="project_display" class="form-control" readonly placeholder="Auto-determined" style="background: rgba(255,255,255,0.04) !important; color: #60A5FA !important; font-weight: 700; cursor: not-allowed;">
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
function updateProjectMapping() {
    const select = document.getElementById('property_id');
    if (!select) return;
    const selectedOption = select.options[select.selectedIndex];
    const projectDisplay = document.getElementById('project_display');
    if (projectDisplay) {
        if (!select.value || !selectedOption) {
            projectDisplay.value = 'Auto-determined';
        } else {
            const projName = selectedOption.getAttribute('data-project');
            projectDisplay.value = projName || 'No Project Assigned';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const propSelect = document.getElementById('property_id');
    if (propSelect) {
        propSelect.addEventListener('change', updateProjectMapping);
        if (window.jQuery) {
            jQuery('#property_id').on('change select2:select select2:unselect', updateProjectMapping);
        }
        updateProjectMapping();
    }
});
</script>
@endsection
