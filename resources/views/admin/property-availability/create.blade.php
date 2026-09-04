@extends('admin.layouts.app')
@section('title','Update Land Property Status')
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
    padding: 10px 20px; min-height: 42px; background: #1E293B !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #475569 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-sc:hover, .btn-secondary-custom:hover {
    background: #334155 !important; color: #FFFFFF !important; transform: translateY(-2px); border-color: #64748B !important;
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
    background: rgba(59, 130, 246, 0.10) !important;
    border: 1px solid rgba(59, 130, 246, 0.25) !important;
    border-radius: 14px !important;
    padding: 14px 18px !important;
    margin-top: 6px !important;
    margin-bottom: 8px !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.20) !important;
}
.prop-info-box .pi-row { display: flex; gap: 20px; flex-wrap: wrap; align-items: center; }
.prop-info-box .pi-item { font-size: 13.5px; color: #94A3B8 !important; font-weight: 600; }
.prop-info-box .pi-item strong { color: #FFFFFF !important; font-weight: 800; margin-left: 4px; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Update Land Property Status</h2>
        <p>Set or update the availability and development status of acquired land property.</p>
    </div>
    <a href="{{ route('property-availability.index') }}" class="btn-sc"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('property-availability.store') }}">
@csrf

<div class="form-card">
    @include('admin.components.firm-select')

    <div class="section-heading"><i class="fa-solid fa-circle-check"></i> Status Information</div>

    <div class="form-grid" style="margin-bottom:18px">
        <div class="form-group" style="grid-column:1/-1;">
            <label class="form-label" for="property_master_id">Land Property (Property Master) <span>*</span></label>
            <select name="property_master_id" id="property_master_id" class="form-control @error('property_master_id') is-invalid @enderror" required onchange="showPropInfo(this)">
                <option value="">— Select Acquired Land Property —</option>
                @foreach($propertyMasters as $pm)
                @php
                    $pmFirmIds = $pm->firms->pluck('id')->push($pm->firm_id)->filter()->unique()->values()->all();
                    $pmTitle = $pm->property_name . ($pm->property_code ? ' (Code: '.$pm->property_code.')' : '') . ($pm->location ? ' — '.$pm->location : '');
                @endphp
                <option value="{{ $pm->id }}"
                        data-firm-ids="{{ implode(',', $pmFirmIds) }}"
                        data-code="{{ $pm->property_code ?? '—' }}"
                        data-location="{{ $pm->location ?? ($pm->city ?? '—') }}"
                        data-status="{{ ucfirst($pm->status ?? 'available') }}"
                        {{ old('property_master_id') == $pm->id ? 'selected' : '' }}>
                    {{ $pmTitle }} [Current: {{ ucfirst($pm->status ?? 'Available') }}]
                </option>
                @endforeach
            </select>
            @error('property_master_id')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="grid-column:1/-1; margin-top:-6px; margin-bottom:0;">
            {{-- Live property info preview --}}
            <div class="prop-info-box" id="propInfoBox" style="display:none;">
                <div class="pi-row">
                    <div class="pi-item">Property Code: <strong id="piCode">—</strong></div>
                    <div class="pi-item">Location / City: <strong id="piLocation">—</strong></div>
                    <div class="pi-item">Current Status: <strong id="piStatus">—</strong></div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">New Status <span>*</span></label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="">— Select Status —</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('status')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Status Date <span>*</span></label>
            <input type="date" name="status_date" value="{{ old('status_date', date('Y-m-d')) }}"
                   class="form-control @error('status_date') is-invalid @enderror" required>
            @error('status_date')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror"
                  placeholder="Reason for status change, acquisition notes, booking ref, etc.">{{ old('remarks') }}</textarea>
        @error('remarks')<div class="text-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-action-buttons">
        <button type="submit" class="btn-pc"><i class="fa fa-save"></i> Save Status</button>
        <a href="{{ route('property-availability.index') }}" class="btn-sc">Cancel</a>
    </div>
</div>
</form>

<script>
function showPropInfo(select) {
    const box = document.getElementById('propInfoBox');
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) {
        if (box) box.style.display = 'none';
        return;
    }
    document.getElementById('piCode').textContent = opt.dataset.code || '—';
    document.getElementById('piLocation').textContent = opt.dataset.location || '—';
    document.getElementById('piStatus').textContent = opt.dataset.status || '—';
    if (box) box.style.display = 'block';
}

function filterPropertiesByFirm(selectedFirmIds) {
    const propSelect = document.getElementById('property_master_id');
    if (!propSelect) return;
    
    Array.from(propSelect.options).forEach(opt => {
        if (!opt.value) return;
        const fStr = opt.getAttribute('data-firm-ids') || '';
        const propFirms = fStr ? fStr.split(',').map(Number) : [];
        if (!selectedFirmIds || selectedFirmIds.length === 0) {
            opt.style.display = '';
            opt.disabled = false;
        } else {
            const match = propFirms.some(id => selectedFirmIds.includes(id));
            opt.style.display = match ? '' : 'none';
            opt.disabled = !match;
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const propSelect = document.getElementById('property_master_id');
    if (propSelect && propSelect.value) {
        showPropInfo(propSelect);
    }
    
    // Listen for firm-select changes if present
    const firmMulti = document.getElementById('firm_ids');
    if (firmMulti) {
        $(firmMulti).on('change', function() {
            const selected = $(this).val() ? $(this).val().map(Number) : [];
            filterPropertiesByFirm(selected);
        });
        const initial = $(firmMulti).val() ? $(firmMulti).val().map(Number) : [];
        if (initial.length > 0) {
            filterPropertiesByFirm(initial);
        }
    }
});
</script>
@endsection
